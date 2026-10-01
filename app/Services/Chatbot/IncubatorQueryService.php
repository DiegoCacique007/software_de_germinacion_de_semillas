<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class IncubatorQueryService
{
    private const LIMITE_DEFECTO=5;
    private const LIMITE_MAXIMO=50;
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='incubadoras') return ['ok'=>false,'tipo'=>'recurso_no_soportado','mensaje'=>'Este servicio solamente consulta incubadoras.'];

        $query=$this->queryPermitidas($user);
        $alcance=$this->aplicarResolucion($query,$resolucion);
        $this->aplicarFiltros($query,$plan['filtros']??[]);
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$alcance),
            'estado'=>$this->detalle($query,$alcance),
            'ultimo'=>$this->ultimo($query,$alcance),
            'listar'=>$this->listar($query,$plan,$alcance),
            default=>['ok'=>false,'tipo'=>'operacion_no_soportada','mensaje'=>'La operación solicitada no está disponible para incubadoras.']
        };
    }

    private function contar(Builder $query,array $alcance): array
    {
        return ['ok'=>true,'tipo'=>'recurso_conteo','recurso'=>'incubadoras','operacion'=>'contar','total'=>(clone $query)->count('i.id'),'alcance'=>$alcance,'periodo'=>[]];
    }

    private function listar(Builder $query,array $plan,array $alcance): array
    {
        $limite=max(1,min((int)($plan['limite']??self::LIMITE_DEFECTO),self::LIMITE_MAXIMO));
        $total=(clone $query)->count('i.id');
        $direccion=($plan['orden']??'desc')==='asc'?'asc':'desc';
        $items=(clone $query)->orderBy('i.created_at',$direccion)->orderBy('i.id',$direccion)->limit($limite)->get();

        if($items->isEmpty()) return $this->sinDatos($alcance);

        return ['ok'=>true,'tipo'=>'recurso_lista','recurso'=>'incubadoras','operacion'=>'listar','total'=>$total,'total_devuelto'=>$items->count(),'limite'=>$limite,'datos'=>$items->map(fn($item)=>$this->mapear($item))->values()->all(),'alcance'=>$alcance,'periodo'=>[]];
    }

    private function detalle(Builder $query,array $alcance): array
    {
        $total=(clone $query)->count('i.id');

        if($total===0) return $this->sinDatos($alcance);
        if($total>1) return ['ok'=>false,'tipo'=>'requiere_aclaracion','mensaje'=>'La consulta coincide con varias incubadoras. Indica el código, nombre o ID de la incubadora que deseas revisar.','recurso'=>'incubadoras','total'=>$total,'alcance'=>$alcance];

        return ['ok'=>true,'tipo'=>'recurso_detalle','recurso'=>'incubadoras','operacion'=>'estado','datos'=>$this->mapear((clone $query)->first()),'alcance'=>$alcance,'periodo'=>[]];
    }

    private function ultimo(Builder $query,array $alcance): array
    {
        $item=(clone $query)->orderByDesc('i.created_at')->orderByDesc('i.id')->first();

        if(!$item) return $this->sinDatos($alcance);

        return ['ok'=>true,'tipo'=>'recurso_detalle','recurso'=>'incubadoras','operacion'=>'ultimo','datos'=>$this->mapear($item),'alcance'=>$alcance,'periodo'=>[]];
    }

    private function queryPermitidas(User $user): Builder
    {
        $query=DB::table('incubadoras as i')
            ->leftJoin('estados_incubadora as ei','ei.id','=','i.estado_incubadora_id')
            ->select(['i.id','i.codigo','i.nombre','i.ubicacion','i.descripcion','i.created_at','ei.clave as estado_clave','ei.nombre as estado_nombre'])
            ->selectSub(function($q){$q->from('alertas as ax')->join('estados_alerta as eax','eax.id','=','ax.estado_alerta_id')->selectRaw('COUNT(*)')->whereColumn('ax.incubadora_id','i.id')->whereIn('eax.clave',['pendiente','atendida']);},'alertas_activas')
            ->selectSub(function($q){$q->from('lecturas_microclima as lx')->select('lx.temperatura')->whereColumn('lx.incubadora_id','i.id')->orderByDesc('lx.fecha_hora')->orderByDesc('lx.id')->limit(1);},'ultima_temperatura')
            ->selectSub(function($q){$q->from('lecturas_microclima as lx')->select('lx.humedad')->whereColumn('lx.incubadora_id','i.id')->orderByDesc('lx.fecha_hora')->orderByDesc('lx.id')->limit(1);},'ultima_humedad')
            ->selectSub(function($q){$q->from('lecturas_microclima as lx')->select('lx.fecha_hora')->whereColumn('lx.incubadora_id','i.id')->orderByDesc('lx.fecha_hora')->orderByDesc('lx.id')->limit(1);},'ultima_fecha_hora');

        $this->restringirPorUsuario($query,$user);

        return $query;
    }

    private function aplicarResolucion(Builder $query,array $resolucion): array
    {
        $incubadora=$resolucion['entidades']['incubadora']??null;

        if(!$incubadora||empty($incubadora['id'])) return ['tipo'=>'global_autorizado'];

        $query->where('i.id',(int)$incubadora['id']);

        return ['tipo'=>'incubadora','id'=>(int)$incubadora['id'],'codigo'=>$incubadora['codigo']??null,'nombre'=>$incubadora['nombre']??null];
    }

    private function aplicarFiltros(Builder $query,array $filtros): void
    {
        if(!empty($filtros['estado_incubadora'])) $query->where('ei.clave',$filtros['estado_incubadora']);

        if(!empty($filtros['con_alertas'])){
            $query->whereExists(function($subquery){
                $subquery->selectRaw('1')
                    ->from('alertas as af')
                    ->join('estados_alerta as ef','ef.id','=','af.estado_alerta_id')
                    ->whereColumn('af.incubadora_id','i.id')
                    ->whereIn('ef.clave',['pendiente','atendida']);
            });
        }
    }

    private function restringirPorUsuario(Builder $query,User $user): void
    {
        if($user->isSuperAdmin()) return;

        if(!$user->isEncargado()){
            $query->whereRaw('1 = 0');
            return;
        }

        $hoy=now($this->timezone)->toDateString();

        $query->whereExists(function($subquery) use($user,$hoy){
            $subquery->selectRaw('1')
                ->from('asignaciones_incubadora as ai')
                ->whereColumn('ai.incubadora_id','i.id')
                ->where('ai.user_id',$user->id)
                ->whereDate('ai.fecha_inicio','<=',$hoy)
                ->where(function($q) use($hoy){$q->whereNull('ai.fecha_fin')->orWhereDate('ai.fecha_fin','>=',$hoy);});
        });
    }

    private function mapear(object $item): array
    {
        return [
            'id'=>(int)$item->id,
            'codigo'=>$item->codigo,
            'nombre'=>$item->nombre,
            'ubicacion'=>$item->ubicacion,
            'descripcion'=>$item->descripcion,
            'estado'=>['clave'=>$item->estado_clave,'nombre'=>$item->estado_nombre],
            'alertas_activas'=>(int)($item->alertas_activas??0),
            'ultima_lectura'=>$item->ultima_fecha_hora?[
                'temperatura'=>$item->ultima_temperatura!==null?(float)$item->ultima_temperatura:null,
                'humedad'=>$item->ultima_humedad!==null?(float)$item->ultima_humedad:null,
                'fecha_hora'=>$item->ultima_fecha_hora
            ]:null,
            'created_at'=>$item->created_at
        ];
    }

    private function sinDatos(array $alcance): array
    {
        return ['ok'=>false,'tipo'=>'sin_datos','recurso'=>'incubadoras','mensaje'=>'No se encontraron incubadoras que coincidan con la consulta.','alcance'=>$alcance];
    }
}
