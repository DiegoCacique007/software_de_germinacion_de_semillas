<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ControlQueryService
{
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='controles') return $this->error('recurso_no_soportado','ControlQueryService solamente procesa controles de incubadora.');

        $query=$this->query($user);
        $alcance=$this->aplicarEntidades($query,$resolucion);
        $filtros=$this->aplicarFiltros($query,$plan['filtros']??[]);
        $periodo=$this->aplicarPeriodo($query,$plan['periodo']??null);
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$alcance,$periodo,$filtros),
            'ultimo','estado'=>$this->detalle($query,$alcance,$periodo,$filtros,$plan),
            'listar'=>$this->listar($query,$alcance,$periodo,$filtros,$plan),
            default=>$this->error('operacion_no_soportada','La operación solicitada todavía no está implementada para controles de incubadora.')
        };
    }

    private function query(User $user): Builder
    {
        $query=DB::table('controles_incubadora as c')
            ->join('incubadoras as i','i.id','=','c.incubadora_id')
            ->join('tipos_control_incubadora as t','t.id','=','c.tipo_control_incubadora_id')
            ->join('modos_control_incubadora as m','m.id','=','c.modo_control_incubadora_id')
            ->leftJoin('users as u','u.id','=','c.user_id');

        if($user->isSuperAdmin()) return $query;
        if(!$user->isEncargado()) return $query->whereRaw('1=0');

        $hoy=Carbon::now($this->timezone)->toDateString();

        return $query->whereExists(function($q) use($user,$hoy){
            $q->selectRaw('1')
                ->from('asignaciones_incubadora as ai')
                ->whereColumn('ai.incubadora_id','i.id')
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
            $query->where('i.id',(int)$e['incubadora']['id']);

            $alcance=[
                'tipo'=>'incubadora',
                'id'=>(int)$e['incubadora']['id'],
                'codigo'=>$e['incubadora']['codigo']??null,
                'nombre'=>$e['incubadora']['nombre']??null
            ];
        }

        if(isset($e['lote']['id'])){
            $lote=DB::table('lotes as l')
                ->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')
                ->where('l.id',(int)$e['lote']['id'])
                ->select('p.incubadora_id')
                ->first();

            if($lote){
                $query->where('i.id',(int)$lote->incubadora_id);

                $alcance=[
                    'tipo'=>'lote',
                    'id'=>(int)$e['lote']['id'],
                    'codigo'=>$e['lote']['codigo']??null,
                    'incubadora_id'=>(int)$lote->incubadora_id
                ];
            }else{
                $query->whereRaw('1=0');
            }
        }

        return $alcance;
    }

    private function aplicarFiltros(Builder $query,array $filtros): array
    {
        $aplicados=[];

        if(!empty($filtros['tipo_control'])){
            $tipo=$this->normalizarClave((string)$filtros['tipo_control']);

            $query->where(function($q) use($tipo){
                $q->whereRaw('LOWER(t.clave)=?',[$tipo])
                    ->orWhereRaw('LOWER(t.nombre)=?',[$tipo]);
            });

            $aplicados['tipo_control']=$tipo;
        }

        if(!empty($filtros['modo_control'])){
            $modo=$this->normalizarClave((string)$filtros['modo_control']);

            $query->where(function($q) use($modo){
                $q->whereRaw('LOWER(m.clave)=?',[$modo])
                    ->orWhereRaw('LOWER(m.nombre)=?',[$modo]);
            });

            $aplicados['modo_control']=$modo;
        }

        return $aplicados;
    }

    private function aplicarPeriodo(Builder $query,?string $periodo): array
    {
        $ahora=Carbon::now($this->timezone);

        switch($periodo){
            case 'hoy':
                $query->whereDate('c.fecha_hora',$ahora->toDateString());
                return ['clave'=>$periodo,'descripcion'=>'hoy'];

            case 'ayer':
                $query->whereDate('c.fecha_hora',$ahora->copy()->subDay()->toDateString());
                return ['clave'=>$periodo,'descripcion'=>'ayer'];

            case 'esta_semana':
                $desde=$ahora->copy()->startOfWeek();
                $hasta=$ahora->copy()->endOfWeek();
                $descripcion='esta semana';
                break;

            case 'semana_pasada':
                $base=$ahora->copy()->subWeek();
                $desde=$base->copy()->startOfWeek();
                $hasta=$base->copy()->endOfWeek();
                $descripcion='la semana pasada';
                break;

            case 'este_mes':
                $desde=$ahora->copy()->startOfMonth();
                $hasta=$ahora->copy()->endOfMonth();
                $descripcion='este mes';
                break;

            case 'mes_pasado':
                $base=$ahora->copy()->subMonthNoOverflow();
                $desde=$base->copy()->startOfMonth();
                $hasta=$base->copy()->endOfMonth();
                $descripcion='el mes pasado';
                break;

            case 'ultimos_7_dias':
                $desde=$ahora->copy()->subDays(7);
                $hasta=$ahora;
                $descripcion='los últimos 7 días';
                break;

            case 'ultimos_30_dias':
                $desde=$ahora->copy()->subDays(30);
                $hasta=$ahora;
                $descripcion='los últimos 30 días';
                break;

            case 'ultimas_24_horas':
                $desde=$ahora->copy()->subDay();
                $hasta=$ahora;
                $descripcion='las últimas 24 horas';
                break;

            default:
                return ['clave'=>null,'descripcion'=>'todo el historial disponible'];
        }

        $query->whereBetween('c.fecha_hora',[$desde,$hasta]);

        return [
            'clave'=>$periodo,
            'descripcion'=>$descripcion,
            'desde'=>$desde->toDateTimeString(),
            'hasta'=>$hasta->toDateTimeString()
        ];
    }

    private function contar(Builder $query,array $alcance,array $periodo,array $filtros): array
    {
        return [
            'ok'=>true,
            'tipo'=>'control_conteo',
            'recurso'=>'controles',
            'operacion'=>'contar',
            'total'=>(clone $query)->count(),
            'alcance'=>$alcance,
            'periodo'=>$periodo,
            'filtros'=>$filtros,
            'historico'=>true
        ];
    }

    private function listar(Builder $query,array $alcance,array $periodo,array $filtros,array $plan): array
    {
        $limite=max(1,min((int)($plan['limite']??5),50));
        $orden=($plan['orden']??'desc')==='asc'?'asc':'desc';
        $total=(clone $query)->count();

        $items=$this->seleccionar($query)
            ->orderBy('c.fecha_hora',$orden)
            ->orderBy('c.id',$orden)
            ->limit($limite)
            ->get();

        if($items->isEmpty()) return $this->sinDatos($alcance,$periodo,$filtros);

        return [
            'ok'=>true,
            'tipo'=>'control_lista',
            'recurso'=>'controles',
            'operacion'=>'listar',
            'variable'=>$plan['variable']??null,
            'total'=>$total,
            'total_devuelto'=>$items->count(),
            'limite'=>$limite,
            'datos'=>$items->map(fn($x)=>$this->mapear($x))->values()->all(),
            'alcance'=>$alcance,
            'periodo'=>$periodo,
            'filtros'=>$filtros,
            'historico'=>true
        ];
    }

    private function detalle(Builder $query,array $alcance,array $periodo,array $filtros,array $plan): array
    {
        $item=$this->seleccionar($query)
            ->orderByDesc('c.fecha_hora')
            ->orderByDesc('c.id')
            ->first();

        if(!$item) return $this->sinDatos($alcance,$periodo,$filtros);

        return [
            'ok'=>true,
            'tipo'=>'control_detalle',
            'recurso'=>'controles',
            'operacion'=>$plan['operacion']??'ultimo',
            'variable'=>$plan['variable']??null,
            'datos'=>$this->mapear($item),
            'alcance'=>$alcance,
            'periodo'=>$periodo,
            'filtros'=>$filtros,
            'historico'=>true
        ];
    }

    private function seleccionar(Builder $query): Builder
    {
        return (clone $query)->select([
            'c.id',
            'c.incubadora_id',
            'c.tipo_control_incubadora_id',
            'c.modo_control_incubadora_id',
            'c.valor_aplicado',
            'c.fecha_hora',
            'c.user_id',
            'c.observaciones',
            'i.codigo as incubadora_codigo',
            'i.nombre as incubadora_nombre',
            't.clave as tipo_clave',
            't.nombre as tipo_nombre',
            't.descripcion as tipo_descripcion',
            'm.clave as modo_clave',
            'm.nombre as modo_nombre',
            'm.descripcion as modo_descripcion',
            'u.name as usuario_nombre'
        ]);
    }

    private function mapear(object $r): array
    {
        return [
            'id'=>(int)$r->id,
            'valor_aplicado'=>$r->valor_aplicado!==null?(float)$r->valor_aplicado:null,
            'fecha_hora'=>(string)$r->fecha_hora,
            'observaciones'=>$r->observaciones,
            'incubadora'=>[
                'id'=>(int)$r->incubadora_id,
                'codigo'=>$r->incubadora_codigo,
                'nombre'=>$r->incubadora_nombre
            ],
            'tipo'=>[
                'id'=>(int)$r->tipo_control_incubadora_id,
                'clave'=>$r->tipo_clave,
                'nombre'=>$r->tipo_nombre,
                'descripcion'=>$r->tipo_descripcion
            ],
            'modo'=>[
                'id'=>(int)$r->modo_control_incubadora_id,
                'clave'=>$r->modo_clave,
                'nombre'=>$r->modo_nombre,
                'descripcion'=>$r->modo_descripcion
            ],
            'usuario'=>$r->user_id!==null
                ?[
                    'id'=>(int)$r->user_id,
                    'nombre'=>$r->usuario_nombre
                ]
                :null,
            'historico'=>true
        ];
    }

    private function normalizarClave(string $valor): string
    {
        $valor=mb_strtolower(trim($valor));

        return strtr($valor,[
            'á'=>'a',
            'é'=>'e',
            'í'=>'i',
            'ó'=>'o',
            'ú'=>'u',
            'ü'=>'u',
            'ñ'=>'n'
        ]);
    }

    private function sinDatos(array $alcance,array $periodo,array $filtros): array
    {
        return [
            'ok'=>false,
            'tipo'=>'sin_datos',
            'recurso'=>'controles',
            'mensaje'=>'No encontré registros de control que cumplan con los criterios de tu consulta.',
            'alcance'=>$alcance,
            'periodo'=>$periodo,
            'filtros'=>$filtros
        ];
    }

    private function error(string $tipo,string $mensaje): array
    {
        return ['ok'=>false,'tipo'=>$tipo,'mensaje'=>$mensaje];
    }
}
