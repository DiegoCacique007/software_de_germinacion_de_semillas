<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class EvidenceQueryService
{
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='evidencias') return $this->error('recurso_no_soportado','EvidenceQueryService solamente procesa evidencias de lote.');

        $query=$this->query($user);
        $alcance=$this->aplicarEntidades($query,$resolucion);
        $periodo=$this->aplicarPeriodo($query,$plan['periodo']??null);
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$alcance,$periodo),
            'ultimo'=>$this->detalle($query,$alcance,$periodo),
            'listar'=>$this->listar($query,$alcance,$periodo,$plan),
            default=>$this->error('operacion_no_soportada','La operación solicitada todavía no está implementada para evidencias.')
        };
    }

    private function query(User $user): Builder
    {
        $query=DB::table('evidencias_lote as ev')
            ->join('seguimientos_lote as sl','sl.id','=','ev.seguimiento_lote_id')
            ->join('lotes as l','l.id','=','sl.lote_id')
            ->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')
            ->join('incubadoras as i','i.id','=','p.incubadora_id')
            ->join('especies as e','e.id','=','l.especie_id')
            ->join('users as u','u.id','=','sl.user_id');

        if($user->isSuperAdmin()) return $query;
        if(!$user->isEncargado()) return $query->whereRaw('1=0');

        $hoy=Carbon::now($this->timezone)->toDateString();

        return $query->whereExists(function($q) use($user,$hoy){
            $q->selectRaw('1')->from('asignaciones_incubadora as ai')
                ->whereColumn('ai.incubadora_id','i.id')
                ->where('ai.user_id',$user->id)
                ->whereDate('ai.fecha_inicio','<=',$hoy)
                ->where(function($x) use($hoy){$x->whereNull('ai.fecha_fin')->orWhereDate('ai.fecha_fin','>=',$hoy);});
        });
    }

    private function aplicarEntidades(Builder $query,array $resolucion): array
    {
        $entidades=$resolucion['entidades']??[];
        $alcance=['tipo'=>'global_autorizado'];

        if(isset($entidades['incubadora']['id'])){
            $query->where('i.id',(int)$entidades['incubadora']['id']);
            $alcance=['tipo'=>'incubadora','id'=>(int)$entidades['incubadora']['id'],'codigo'=>$entidades['incubadora']['codigo']??null,'nombre'=>$entidades['incubadora']['nombre']??null];
        }

        if(isset($entidades['especie']['id'])){
            $query->where('e.id',(int)$entidades['especie']['id']);
            $alcance=['tipo'=>'especie','id'=>(int)$entidades['especie']['id'],'nombre_comun'=>$entidades['especie']['nombre_comun']??null,'nombre_cientifico'=>$entidades['especie']['nombre_cientifico']??null];
        }

        if(isset($entidades['lote']['id'])){
            $query->where('l.id',(int)$entidades['lote']['id']);
            $alcance=['tipo'=>'lote','id'=>(int)$entidades['lote']['id'],'codigo'=>$entidades['lote']['codigo']??null];
        }

        return $alcance;
    }

    private function aplicarPeriodo(Builder $query,?string $periodo): array
    {
        $ahora=Carbon::now($this->timezone);

        switch($periodo){
            case 'hoy':
                $desde=$hasta=$ahora->toDateString();$descripcion='hoy';break;
            case 'ayer':
                $desde=$hasta=$ahora->copy()->subDay()->toDateString();$descripcion='ayer';break;
            case 'esta_semana':
                $desde=$ahora->copy()->startOfWeek()->toDateString();$hasta=$ahora->copy()->endOfWeek()->toDateString();$descripcion='esta semana';break;
            case 'semana_pasada':
                $base=$ahora->copy()->subWeek();$desde=$base->copy()->startOfWeek()->toDateString();$hasta=$base->copy()->endOfWeek()->toDateString();$descripcion='la semana pasada';break;
            case 'este_mes':
                $desde=$ahora->copy()->startOfMonth()->toDateString();$hasta=$ahora->copy()->endOfMonth()->toDateString();$descripcion='este mes';break;
            case 'mes_pasado':
                $base=$ahora->copy()->subMonthNoOverflow();$desde=$base->copy()->startOfMonth()->toDateString();$hasta=$base->copy()->endOfMonth()->toDateString();$descripcion='el mes pasado';break;
            case 'ultimos_7_dias':
                $desde=$ahora->copy()->subDays(6)->toDateString();$hasta=$ahora->toDateString();$descripcion='los últimos 7 días';break;
            case 'ultimos_30_dias':
                $desde=$ahora->copy()->subDays(29)->toDateString();$hasta=$ahora->toDateString();$descripcion='los últimos 30 días';break;
            case 'ultimas_24_horas':
                $desde=$ahora->copy()->subDay();$hasta=$ahora->copy();
                $query->whereBetween('ev.created_at',[$desde,$hasta]);
                return ['clave'=>$periodo,'descripcion'=>'las últimas 24 horas','desde'=>$desde->toDateTimeString(),'hasta'=>$hasta->toDateTimeString()];
            default:
                return ['clave'=>null,'descripcion'=>'todo el historial disponible'];
        }

        $query->whereDate('ev.created_at','>=',$desde)->whereDate('ev.created_at','<=',$hasta);

        return ['clave'=>$periodo,'descripcion'=>$descripcion,'desde'=>$desde,'hasta'=>$hasta];
    }

    private function contar(Builder $query,array $alcance,array $periodo): array
    {
        return [
            'ok'=>true,
            'tipo'=>'evidencia_conteo',
            'recurso'=>'evidencias',
            'operacion'=>'contar',
            'total'=>(clone $query)->count(),
            'alcance'=>$alcance,
            'periodo'=>$periodo
        ];
    }

    private function listar(Builder $query,array $alcance,array $periodo,array $plan): array
    {
        $limite=max(1,min((int)($plan['limite']??5),50));
        $orden=($plan['orden']??'desc')==='asc'?'asc':'desc';
        $total=(clone $query)->count();

        $items=$this->seleccionar($query)
            ->orderBy('ev.created_at',$orden)
            ->orderBy('ev.id',$orden)
            ->limit($limite)
            ->get();

        if($items->isEmpty()) return $this->sinDatos($alcance,$periodo);

        return [
            'ok'=>true,
            'tipo'=>'evidencia_lista',
            'recurso'=>'evidencias',
            'operacion'=>'listar',
            'total'=>$total,
            'total_devuelto'=>$items->count(),
            'limite'=>$limite,
            'datos'=>$items->map(fn($x)=>$this->mapear($x))->values()->all(),
            'alcance'=>$alcance,
            'periodo'=>$periodo
        ];
    }

    private function detalle(Builder $query,array $alcance,array $periodo): array
    {
        $item=$this->seleccionar($query)
            ->orderByDesc('ev.created_at')
            ->orderByDesc('ev.id')
            ->first();

        if(!$item) return $this->sinDatos($alcance,$periodo);

        return [
            'ok'=>true,
            'tipo'=>'evidencia_detalle',
            'recurso'=>'evidencias',
            'operacion'=>'ultimo',
            'datos'=>$this->mapear($item),
            'alcance'=>$alcance,
            'periodo'=>$periodo
        ];
    }

    private function seleccionar(Builder $query): Builder
    {
        return (clone $query)->select([
            'ev.id',
            'ev.seguimiento_lote_id',
            'ev.archivo',
            'ev.descripcion',
            'ev.created_at',
            'sl.fecha_revision',
            'sl.porcentaje_germinacion',
            'l.id as lote_id',
            'l.codigo_lote',
            'e.id as especie_id',
            'e.nombre_comun',
            'e.nombre_cientifico',
            'i.id as incubadora_id',
            'i.codigo as incubadora_codigo',
            'i.nombre as incubadora_nombre',
            'u.id as usuario_id',
            'u.name as usuario_nombre'
        ]);
    }

    private function mapear(object $r): array
    {
        return [
            'id'=>(int)$r->id,
            'seguimiento_lote_id'=>(int)$r->seguimiento_lote_id,
            'archivo'=>$r->archivo,
            'archivo_disponible'=>!empty($r->archivo),
            'descripcion'=>$r->descripcion,
            'fecha_registro'=>$r->created_at,
            'seguimiento'=>[
                'id'=>(int)$r->seguimiento_lote_id,
                'fecha_revision'=>(string)$r->fecha_revision,
                'porcentaje_germinacion'=>(float)$r->porcentaje_germinacion
            ],
            'lote'=>[
                'id'=>(int)$r->lote_id,
                'codigo'=>$r->codigo_lote
            ],
            'especie'=>[
                'id'=>(int)$r->especie_id,
                'nombre_comun'=>$r->nombre_comun,
                'nombre_cientifico'=>$r->nombre_cientifico
            ],
            'incubadora'=>[
                'id'=>(int)$r->incubadora_id,
                'codigo'=>$r->incubadora_codigo,
                'nombre'=>$r->incubadora_nombre
            ],
            'usuario'=>[
                'id'=>(int)$r->usuario_id,
                'nombre'=>$r->usuario_nombre
            ]
        ];
    }

    private function sinDatos(array $alcance,array $periodo): array
    {
        return [
            'ok'=>false,
            'tipo'=>'sin_datos',
            'recurso'=>'evidencias',
            'mensaje'=>'No encontré evidencias de lote que cumplan con los criterios de tu consulta.',
            'alcance'=>$alcance,
            'periodo'=>$periodo
        ];
    }

    private function error(string $tipo,string $mensaje): array
    {
        return ['ok'=>false,'tipo'=>$tipo,'mensaje'=>$mensaje];
    }
}
