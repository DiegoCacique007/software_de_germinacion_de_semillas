<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PositionQueryService
{
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='posiciones') return $this->error('recurso_no_soportado','PositionQueryService solamente procesa posiciones de incubadora.');

        $query=$this->query($user);
        $alcance=$this->aplicarEntidades($query,$resolucion);
        $filtros=$this->aplicarFiltros($query,$plan['filtros']??[]);
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$alcance,$filtros),
            'listar','estado','ultimo'=>$this->listar($query,$alcance,$filtros,$plan),
            default=>$this->error('operacion_no_soportada','La operación solicitada todavía no está implementada para posiciones.')
        };
    }

    private function query(User $user): Builder
    {
        $query=DB::table('posiciones_incubadora as p')
            ->join('incubadoras as i','i.id','=','p.incubadora_id');

        if($user->isSuperAdmin()) return $query;
        if(!$user->isEncargado()) return $query->whereRaw('1=0');

        $hoy=Carbon::now($this->timezone)->toDateString();

        return $query->whereExists(function($q) use($user,$hoy){
            $q->selectRaw('1')
                ->from('asignaciones_incubadora as ai')
                ->whereColumn('ai.incubadora_id','p.incubadora_id')
                ->where('ai.user_id',$user->id)
                ->whereDate('ai.fecha_inicio','<=',$hoy)
                ->where(function($x) use($hoy){
                    $x->whereNull('ai.fecha_fin')->orWhereDate('ai.fecha_fin','>=',$hoy);
                });
        });
    }

    private function aplicarEntidades(Builder $query,array $resolucion): array
    {
        $e=$resolucion['entidades']??[];
        $alcance=['tipo'=>'global_autorizado'];

        if(isset($e['incubadora']['id'])){
            $query->where('p.incubadora_id',(int)$e['incubadora']['id']);

            $alcance=[
                'tipo'=>'incubadora',
                'id'=>(int)$e['incubadora']['id'],
                'codigo'=>$e['incubadora']['codigo']??null,
                'nombre'=>$e['incubadora']['nombre']??null
            ];
        }

        if(isset($e['lote']['id'])){
            $lote=DB::table('lotes as l')
                ->join('posiciones_incubadora as p2','p2.id','=','l.posicion_incubadora_id')
                ->where('l.id',(int)$e['lote']['id'])
                ->select([
                    'l.posicion_incubadora_id',
                    'p2.incubadora_id'
                ])
                ->first();

            if(!$lote){
                $query->whereRaw('1=0');
                return $alcance;
            }

            $query->where('p.id',(int)$lote->posicion_incubadora_id);

            $alcance=[
                'tipo'=>'lote',
                'id'=>(int)$e['lote']['id'],
                'codigo'=>$e['lote']['codigo']??null,
                'posicion_id'=>(int)$lote->posicion_incubadora_id,
                'incubadora_id'=>(int)$lote->incubadora_id
            ];
        }

        return $alcance;
    }

    private function aplicarFiltros(Builder $query,array $filtros): array
    {
        $aplicados=[];

        if(isset($filtros['numero_posicion'])&&is_numeric($filtros['numero_posicion'])){
            $numero=(int)$filtros['numero_posicion'];
            $query->where('p.numero_posicion',$numero);
            $aplicados['numero_posicion']=$numero;
        }

        return $aplicados;
    }

    private function contar(Builder $query,array $alcance,array $filtros): array
    {
        return [
            'ok'=>true,
            'tipo'=>'posicion_conteo',
            'recurso'=>'posiciones',
            'operacion'=>'contar',
            'total'=>(clone $query)->count(),
            'alcance'=>$alcance,
            'filtros'=>$filtros
        ];
    }

    private function listar(Builder $query,array $alcance,array $filtros,array $plan): array
    {
        $limite=max(1,min((int)($plan['limite']??5),50));
        $orden=($plan['orden']??'asc')==='desc'?'desc':'asc';
        $total=(clone $query)->count();

        $items=(clone $query)
            ->select([
                'p.id',
                'p.incubadora_id',
                'p.numero_posicion',
                'p.descripcion',
                'i.codigo as incubadora_codigo',
                'i.nombre as incubadora_nombre'
            ])
            ->orderBy('i.codigo',$orden)
            ->orderBy('p.numero_posicion',$orden)
            ->limit($limite)
            ->get();

        if($items->isEmpty()) return $this->sinDatos($alcance,$filtros);

        $datos=$this->mapearColeccion($items);

        return [
            'ok'=>true,
            'tipo'=>count($datos)===1?'posicion_detalle':'posicion_lista',
            'recurso'=>'posiciones',
            'operacion'=>$plan['operacion']??'listar',
            'total'=>$total,
            'total_devuelto'=>count($datos),
            'limite'=>$limite,
            'datos'=>count($datos)===1?$datos[0]:$datos,
            'alcance'=>$alcance,
            'filtros'=>$filtros
        ];
    }

    private function mapearColeccion(Collection $items): array
    {
        $ids=$items->pluck('id')->map(fn($id)=>(int)$id)->all();

        $lotes=empty($ids)
            ?collect()
            :DB::table('lotes as l')
                ->leftJoin('estados_lote as el','el.id','=','l.estado_lote_id')
                ->whereIn('l.posicion_incubadora_id',$ids)
                ->select([
                    'l.id',
                    'l.posicion_incubadora_id',
                    'l.codigo_lote',
                    'el.clave as estado_clave',
                    'el.nombre as estado_nombre'
                ])
                ->orderBy('l.id')
                ->get()
                ->groupBy('posicion_incubadora_id');

        return $items->map(function($p) use($lotes){
            $asociados=$lotes->get($p->id,collect());

            return [
                'id'=>(int)$p->id,
                'numero'=>(int)$p->numero_posicion,
                'descripcion'=>$p->descripcion,
                'incubadora'=>[
                    'id'=>(int)$p->incubadora_id,
                    'codigo'=>$p->incubadora_codigo,
                    'nombre'=>$p->incubadora_nombre
                ],
                'total_lotes'=>$asociados->count(),
                'lotes'=>$asociados->map(fn($l)=>[
                    'id'=>(int)$l->id,
                    'codigo'=>$l->codigo_lote,
                    'estado'=>[
                        'clave'=>$l->estado_clave,
                        'nombre'=>$l->estado_nombre
                    ]
                ])->values()->all()
            ];
        })->values()->all();
    }

    private function sinDatos(array $alcance,array $filtros): array
    {
        return [
            'ok'=>false,
            'tipo'=>'sin_datos',
            'recurso'=>'posiciones',
            'mensaje'=>'No encontré posiciones de incubadora que cumplan con los criterios de tu consulta.',
            'alcance'=>$alcance,
            'filtros'=>$filtros
        ];
    }

    private function error(string $tipo,string $mensaje): array
    {
        return ['ok'=>false,'tipo'=>$tipo,'mensaje'=>$mensaje];
    }
}
