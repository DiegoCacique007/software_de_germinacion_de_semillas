<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class FollowUpQueryService
{
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='seguimientos') return $this->error('recurso_no_soportado','FollowUpQueryService solamente procesa seguimientos.');

        $subtipo=$this->detectarSubtipo($plan,$resolucion);
        if($subtipo===null) return $this->error('requiere_aclaracion','Indica si deseas consultar seguimientos de lote o seguimientos de frasco.');

        $query=$subtipo==='frasco'?$this->queryFrascos($user):$this->queryLotes($user);
        $alcance=$this->aplicarEntidades($query,$subtipo,$resolucion);
        $periodo=$this->aplicarPeriodo($query,$subtipo,$plan['periodo']??null);
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$subtipo,$alcance,$periodo),
            'ultimo','estado'=>$this->detalle($query,$subtipo,$alcance,$periodo,$plan),
            'listar'=>$this->listar($query,$subtipo,$alcance,$periodo,$plan),
            default=>$this->error('operacion_no_soportada','La operación solicitada todavía no está implementada para seguimientos.')
        };
    }

    private function detectarSubtipo(array $plan,array $resolucion): ?string
    {
        $entidades=$resolucion['entidades']??[];
        $texto=mb_strtolower((string)($plan['mensaje_normalizado']??$plan['mensaje_original']??''));

        if(isset($entidades['frasco'])) return 'frasco';
        if(preg_match('/\bfrascos?\b/u',$texto)) return 'frasco';
        if(isset($entidades['lote'])) return 'lote';
        if(preg_match('/\blotes?\b/u',$texto)) return 'lote';
        if(($plan['variable']??null)==='germinacion') return 'lote';

        return null;
    }

    private function queryLotes(User $user): Builder
    {
        $query=DB::table('seguimientos_lote as sl')
            ->join('lotes as l','l.id','=','sl.lote_id')
            ->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')
            ->join('incubadoras as i','i.id','=','p.incubadora_id')
            ->join('especies as e','e.id','=','l.especie_id')
            ->join('etapas_desarrollo as ed','ed.id','=','sl.etapa_desarrollo_id')
            ->join('users as u','u.id','=','sl.user_id');

        return $this->aplicarPermiso($query,$user);
    }

    private function queryFrascos(User $user): Builder
    {
        $query=DB::table('seguimientos_frasco as sf')
            ->join('frascos as f','f.id','=','sf.frasco_id')
            ->join('lotes as l','l.id','=','f.lote_id')
            ->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')
            ->join('incubadoras as i','i.id','=','p.incubadora_id')
            ->join('especies as e','e.id','=','l.especie_id')
            ->join('estados_frasco as ef','ef.id','=','sf.estado_frasco_id')
            ->join('users as u','u.id','=','sf.user_id');

        return $this->aplicarPermiso($query,$user);
    }

    private function aplicarPermiso(Builder $query,User $user): Builder
    {
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

    private function aplicarEntidades(Builder $query,string $subtipo,array $resolucion): array
    {
        $entidades=$resolucion['entidades']??[];
        $alcance=['tipo'=>'global_autorizado'];

        if(isset($entidades['incubadora']['id'])){
            $query->where('i.id',(int)$entidades['incubadora']['id']);
            $alcance=[
                'tipo'=>'incubadora',
                'id'=>(int)$entidades['incubadora']['id'],
                'codigo'=>$entidades['incubadora']['codigo']??null,
                'nombre'=>$entidades['incubadora']['nombre']??null
            ];
        }

        if(isset($entidades['especie']['id'])){
            $query->where('e.id',(int)$entidades['especie']['id']);
            $alcance=[
                'tipo'=>'especie',
                'id'=>(int)$entidades['especie']['id'],
                'nombre_comun'=>$entidades['especie']['nombre_comun']??null,
                'nombre_cientifico'=>$entidades['especie']['nombre_cientifico']??null
            ];
        }

        if(isset($entidades['lote']['id'])){
            $query->where('l.id',(int)$entidades['lote']['id']);
            $alcance=[
                'tipo'=>'lote',
                'id'=>(int)$entidades['lote']['id'],
                'codigo'=>$entidades['lote']['codigo']??null
            ];
        }

        if($subtipo==='frasco'&&isset($entidades['frasco']['id'])){
            $query->where('f.id',(int)$entidades['frasco']['id']);
            $alcance=[
                'tipo'=>'frasco',
                'id'=>(int)$entidades['frasco']['id'],
                'numero'=>$entidades['frasco']['numero']??$entidades['frasco']['numero_frasco']??null,
                'lote'=>$entidades['frasco']['lote']??null
            ];
        }

        return $alcance;
    }

    private function aplicarPeriodo(Builder $query,string $subtipo,?string $periodo): array
    {
        $campo=$subtipo==='frasco'?'sf.fecha_revision':'sl.fecha_revision';
        $ahora=Carbon::now($this->timezone);
        $descripcion='todo el historial disponible';

        switch($periodo){
            case 'hoy':
                $desde=$hasta=$ahora->toDateString();
                $descripcion='hoy';
                break;

            case 'ayer':
                $desde=$hasta=$ahora->copy()->subDay()->toDateString();
                $descripcion='ayer';
                break;

            case 'esta_semana':
                $desde=$ahora->copy()->startOfWeek()->toDateString();
                $hasta=$ahora->copy()->endOfWeek()->toDateString();
                $descripcion='esta semana';
                break;

            case 'semana_pasada':
                $base=$ahora->copy()->subWeek();
                $desde=$base->copy()->startOfWeek()->toDateString();
                $hasta=$base->copy()->endOfWeek()->toDateString();
                $descripcion='la semana pasada';
                break;

            case 'este_mes':
                $desde=$ahora->copy()->startOfMonth()->toDateString();
                $hasta=$ahora->copy()->endOfMonth()->toDateString();
                $descripcion='este mes';
                break;

            case 'mes_pasado':
                $base=$ahora->copy()->subMonthNoOverflow();
                $desde=$base->copy()->startOfMonth()->toDateString();
                $hasta=$base->copy()->endOfMonth()->toDateString();
                $descripcion='el mes pasado';
                break;

            case 'ultimos_7_dias':
                $desde=$ahora->copy()->subDays(6)->toDateString();
                $hasta=$ahora->toDateString();
                $descripcion='los últimos 7 días';
                break;

            case 'ultimos_30_dias':
                $desde=$ahora->copy()->subDays(29)->toDateString();
                $hasta=$ahora->toDateString();
                $descripcion='los últimos 30 días';
                break;

            case 'ultimas_24_horas':
                $desde=$ahora->copy()->subDay()->toDateString();
                $hasta=$ahora->toDateString();
                $descripcion='las últimas 24 horas aproximadas por fecha de revisión';
                break;

            default:
                return [
                    'clave'=>null,
                    'descripcion'=>$descripcion
                ];
        }

        $query->whereBetween($campo,[$desde,$hasta]);

        return [
            'clave'=>$periodo,
            'descripcion'=>$descripcion,
            'desde'=>$desde,
            'hasta'=>$hasta
        ];
    }

    private function contar(Builder $query,string $subtipo,array $alcance,array $periodo): array
    {
        return [
            'ok'=>true,
            'tipo'=>'seguimiento_conteo',
            'recurso'=>'seguimientos',
            'subtipo'=>$subtipo,
            'operacion'=>'contar',
            'total'=>(clone $query)->count(),
            'alcance'=>$alcance,
            'periodo'=>$periodo
        ];
    }

    private function listar(Builder $query,string $subtipo,array $alcance,array $periodo,array $plan): array
    {
        $limite=max(1,min((int)($plan['limite']??5),50));
        $orden=($plan['orden']??'desc')==='asc'?'asc':'desc';
        $total=(clone $query)->count();

        $items=$this->seleccionar($query,$subtipo)
            ->orderBy($subtipo==='frasco'?'sf.fecha_revision':'sl.fecha_revision',$orden)
            ->orderBy($subtipo==='frasco'?'sf.id':'sl.id',$orden)
            ->limit($limite)
            ->get();

        if($items->isEmpty()) return $this->sinDatos($subtipo,$alcance,$periodo);

        return [
            'ok'=>true,
            'tipo'=>'seguimiento_lista',
            'recurso'=>'seguimientos',
            'subtipo'=>$subtipo,
            'operacion'=>'listar',
            'variable'=>$plan['variable']??null,
            'total'=>$total,
            'total_devuelto'=>$items->count(),
            'limite'=>$limite,
            'datos'=>$items->map(fn($x)=>$this->mapear($subtipo,$x))->values()->all(),
            'alcance'=>$alcance,
            'periodo'=>$periodo
        ];
    }

    private function detalle(Builder $query,string $subtipo,array $alcance,array $periodo,array $plan): array
    {
        $item=$this->seleccionar($query,$subtipo)
            ->orderByDesc($subtipo==='frasco'?'sf.fecha_revision':'sl.fecha_revision')
            ->orderByDesc($subtipo==='frasco'?'sf.id':'sl.id')
            ->first();

        if(!$item) return $this->sinDatos($subtipo,$alcance,$periodo);

        return [
            'ok'=>true,
            'tipo'=>'seguimiento_detalle',
            'recurso'=>'seguimientos',
            'subtipo'=>$subtipo,
            'operacion'=>$plan['operacion']??'ultimo',
            'variable'=>$plan['variable']??null,
            'datos'=>$this->mapear($subtipo,$item),
            'alcance'=>$alcance,
            'periodo'=>$periodo
        ];
    }

    private function seleccionar(Builder $query,string $subtipo): Builder
    {
        if($subtipo==='frasco'){
            return (clone $query)->select([
                'sf.id',
                'sf.frasco_id',
                'sf.fecha_revision',
                'sf.semillas_germinadas',
                'sf.altura_promedio_cm',
                'sf.observaciones',
                'sf.user_id',
                'f.numero_frasco',
                'f.cantidad_semillas',
                'l.id as lote_id',
                'l.codigo_lote',
                'e.id as especie_id',
                'e.nombre_comun',
                'e.nombre_cientifico',
                'i.id as incubadora_id',
                'i.codigo as incubadora_codigo',
                'i.nombre as incubadora_nombre',
                'ef.id as estado_id',
                'ef.clave as estado_clave',
                'ef.nombre as estado_nombre',
                'u.id as usuario_id',
                'u.name as usuario_nombre'
            ]);
        }

        return (clone $query)->select([
            'sl.id',
            'sl.lote_id',
            'sl.fecha_revision',
            'sl.frascos_activos',
            'sl.semillas_germinadas',
            'sl.porcentaje_germinacion',
            'sl.altura_promedio_cm',
            'sl.observaciones',
            'sl.user_id',
            'l.codigo_lote',
            'e.id as especie_id',
            'e.nombre_comun',
            'e.nombre_cientifico',
            'i.id as incubadora_id',
            'i.codigo as incubadora_codigo',
            'i.nombre as incubadora_nombre',
            'ed.id as etapa_id',
            'ed.clave as etapa_clave',
            'ed.nombre as etapa_nombre',
            'u.id as usuario_id',
            'u.name as usuario_nombre'
        ]);
    }

    private function mapear(string $subtipo,object $r): array
    {
        $base=[
            'id'=>(int)$r->id,
            'fecha_revision'=>(string)$r->fecha_revision,
            'semillas_germinadas'=>(int)$r->semillas_germinadas,
            'altura_promedio_cm'=>$r->altura_promedio_cm!==null?(float)$r->altura_promedio_cm:null,
            'observaciones'=>$r->observaciones,
            'usuario'=>[
                'id'=>(int)$r->usuario_id,
                'nombre'=>$r->usuario_nombre
            ],
            'incubadora'=>[
                'id'=>(int)$r->incubadora_id,
                'codigo'=>$r->incubadora_codigo,
                'nombre'=>$r->incubadora_nombre
            ],
            'especie'=>[
                'id'=>(int)$r->especie_id,
                'nombre_comun'=>$r->nombre_comun,
                'nombre_cientifico'=>$r->nombre_cientifico
            ]
        ];

        if($subtipo==='frasco'){
            $base['frasco']=[
                'id'=>(int)$r->frasco_id,
                'numero'=>(int)$r->numero_frasco,
                'cantidad_semillas'=>(int)$r->cantidad_semillas
            ];

            $base['lote']=[
                'id'=>(int)$r->lote_id,
                'codigo'=>$r->codigo_lote
            ];

            $base['estado']=[
                'id'=>(int)$r->estado_id,
                'clave'=>$r->estado_clave,
                'nombre'=>$r->estado_nombre
            ];

            return $base;
        }

        $base['lote']=[
            'id'=>(int)$r->lote_id,
            'codigo'=>$r->codigo_lote
        ];

        $base['frascos_activos']=(int)$r->frascos_activos;
        $base['porcentaje_germinacion']=(float)$r->porcentaje_germinacion;

        $base['etapa']=[
            'id'=>(int)$r->etapa_id,
            'clave'=>$r->etapa_clave,
            'nombre'=>$r->etapa_nombre
        ];

        return $base;
    }

    private function sinDatos(string $subtipo,array $alcance,array $periodo): array
    {
        return [
            'ok'=>false,
            'tipo'=>'sin_datos',
            'recurso'=>'seguimientos',
            'subtipo'=>$subtipo,
            'mensaje'=>'No encontré seguimientos de '.$subtipo.' que cumplan con los criterios de tu consulta.',
            'alcance'=>$alcance,
            'periodo'=>$periodo
        ];
    }

    private function error(string $tipo,string $mensaje): array
    {
        return [
            'ok'=>false,
            'tipo'=>$tipo,
            'mensaje'=>$mensaje
        ];
    }
}
