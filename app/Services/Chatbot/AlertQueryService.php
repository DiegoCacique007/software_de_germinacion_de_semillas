<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AlertQueryService
{
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='alertas') return ['ok'=>false,'tipo'=>'recurso_no_soportado','mensaje'=>'AlertQueryService solamente procesa consultas relacionadas con alertas.'];

        $query=$this->queryAlertasPermitidas($user);
        $alcance=$this->aplicarEntidades($query,$resolucion);
        $periodo=$this->aplicarPeriodo($query,$plan['periodo']??null);
        $this->aplicarFiltros($query,$plan['filtros']??[]);
        $operacion=$this->resolverOperacion($plan);

        return match($operacion){
            'contar'=>$this->contar($query,$alcance,$periodo,$plan),
            'ultimo'=>$this->ultima($query,$alcance,$periodo),
            'ranking'=>$this->ranking($query,$plan,$alcance,$periodo),
            'listar'=>$this->listar($query,$plan,$alcance,$periodo),
            default=>['ok'=>false,'tipo'=>'operacion_no_soportada','mensaje'=>'No pude determinar qué operación realizar sobre las alertas.']
        };
    }

    private function resolverOperacion(array $plan): string
    {
        $operacion=$plan['operacion']??null;
        if(in_array($operacion,['contar','ultimo','listar','ranking'],true)) return $operacion;
        if(!empty($plan['filtros'])) return 'listar';
        return 'listar';
    }

    private function contar(Builder $query,array $alcance,array $periodo,array $plan): array
    {
        return ['ok'=>true,'tipo'=>'conteo_alertas','operacion'=>'contar','total'=>(clone $query)->count('a.id'),'filtros'=>$plan['filtros']??[],'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function ultima(Builder $query,array $alcance,array $periodo): array
    {
        $alerta=(clone $query)->select($this->columnas())->orderByDesc('a.fecha_hora')->orderByDesc('a.id')->first();

        if(!$alerta) return $this->sinDatos($alcance,$periodo);

        return ['ok'=>true,'tipo'=>'ultima_alerta','operacion'=>'ultimo','datos'=>$this->formatearAlerta($alerta),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function listar(Builder $query,array $plan,array $alcance,array $periodo): array
    {
        $limite=max(1,min((int)($plan['limite']??5),50));
        $total=(clone $query)->count('a.id');

        $alertas=(clone $query)->select($this->columnas())->orderByDesc('a.fecha_hora')->orderByDesc('a.id')->limit($limite)->get();

        if($alertas->isEmpty()) return $this->sinDatos($alcance,$periodo);

        return ['ok'=>true,'tipo'=>'lista_alertas','operacion'=>'listar','total'=>$total,'total_devuelto'=>$alertas->count(),'limite'=>$limite,'datos'=>$alertas->map(fn($alerta)=>$this->formatearAlerta($alerta))->values()->all(),'filtros'=>$plan['filtros']??[],'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function ranking(Builder $query,array $plan,array $alcance,array $periodo): array
    {
        if(($plan['agrupacion']??null)!=='incubadora') return ['ok'=>false,'tipo'=>'agrupacion_no_soportada','mensaje'=>'El ranking de alertas actualmente se puede agrupar por incubadora.'];

        $limite=max(1,min((int)($plan['limite']??5),20));

        $items=(clone $query)->select(['i.id','i.codigo','i.nombre',DB::raw('COUNT(a.id) as total_alertas')])->groupBy('i.id','i.codigo','i.nombre')->orderByDesc('total_alertas')->orderBy('i.codigo')->limit($limite)->get();

        if($items->isEmpty()) return $this->sinDatos($alcance,$periodo);

        return ['ok'=>true,'tipo'=>'ranking_alertas','operacion'=>'ranking','agrupacion'=>'incubadora','datos'=>$items->map(fn($item)=>['incubadora'=>['id'=>(int)$item->id,'codigo'=>$item->codigo,'nombre'=>$item->nombre],'total_alertas'=>(int)$item->total_alertas])->values()->all(),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function queryAlertasPermitidas(User $user): Builder
    {
        $query=DB::table('alertas as a')->join('incubadoras as i','i.id','=','a.incubadora_id')->leftJoin('tipos_alerta as ta','ta.id','=','a.tipo_alerta_id')->leftJoin('niveles_alerta as na','na.id','=','a.nivel_alerta_id')->leftJoin('estados_alerta as ea','ea.id','=','a.estado_alerta_id')->leftJoin('lotes as l','l.id','=','a.lote_id');

        $this->restringirPorUsuario($query,$user);

        return $query;
    }

    private function aplicarFiltros(Builder $query,array $filtros): void
    {
        if(isset($filtros['estado_alerta'])) $query->where('ea.clave',$filtros['estado_alerta']);

        if(($filtros['estado_general']??null)==='activo'&&!isset($filtros['estado_alerta'])) $query->whereIn('ea.clave',['pendiente','atendida']);

        $nivel=$filtros['nivel_alerta']??null;

        $niveles=match($nivel){
            'critica'=>['critica','critico'],
            'alta'=>['alta','alto'],
            'media'=>['media','medio'],
            'baja'=>['baja','bajo'],
            default=>null
        };

        if($niveles) $query->whereIn('na.clave',$niveles);
    }

    private function aplicarEntidades(Builder $query,array $resolucion): array
    {
        $entidades=$resolucion['entidades']??[];
        $alcance=['tipo'=>'global_autorizado'];

        if(isset($entidades['incubadora']['id'])){
            $incubadora=$entidades['incubadora'];

            $query->where('a.incubadora_id',(int)$incubadora['id']);

            $alcance=['tipo'=>'incubadora','id'=>(int)$incubadora['id'],'codigo'=>$incubadora['codigo']??null,'nombre'=>$incubadora['nombre']??null];
        }

        if(isset($entidades['lote']['id'])){
            $lote=$entidades['lote'];

            $query->where('a.lote_id',(int)$lote['id']);

            $dato=['id'=>(int)$lote['id'],'codigo'=>$lote['codigo']??null];

            if(($alcance['tipo']??null)==='global_autorizado') $alcance=['tipo'=>'lote',...$dato];
            else $alcance['lote']=$dato;
        }

        if(isset($entidades['especie']['id'])){
            $especie=$entidades['especie'];
            $especieId=(int)$especie['id'];

            $query->whereExists(function($subquery) use($especieId){
                $subquery->selectRaw('1')->from('lotes as le')->whereColumn('le.id','a.lote_id')->where('le.especie_id',$especieId);
            });

            $dato=['id'=>$especieId,'nombre_comun'=>$especie['nombre_comun']??null,'nombre_cientifico'=>$especie['nombre_cientifico']??null];

            if(($alcance['tipo']??null)==='global_autorizado') $alcance=['tipo'=>'especie',...$dato];
            else $alcance['especie']=$dato;
        }

        return $alcance;
    }

    private function aplicarPeriodo(Builder $query,?string $periodo): array
    {
        if(!$periodo) return ['clave'=>null,'desde'=>null,'hasta'=>null,'descripcion'=>'Todo el historial disponible'];

        $ahora=now($this->timezone);

        [$desde,$hasta,$descripcion]=match($periodo){
            'hoy'=>[$ahora->copy()->startOfDay(),$ahora->copy()->endOfDay(),'Hoy'],
            'ayer'=>[$ahora->copy()->subDay()->startOfDay(),$ahora->copy()->subDay()->endOfDay(),'Ayer'],
            'esta_semana'=>[$ahora->copy()->startOfWeek(),$ahora->copy()->endOfWeek(),'Esta semana'],
            'semana_pasada'=>[$ahora->copy()->subWeek()->startOfWeek(),$ahora->copy()->subWeek()->endOfWeek(),'Semana pasada'],
            'este_mes'=>[$ahora->copy()->startOfMonth(),$ahora->copy()->endOfMonth(),'Este mes'],
            'mes_pasado'=>[$ahora->copy()->subMonthNoOverflow()->startOfMonth(),$ahora->copy()->subMonthNoOverflow()->endOfMonth(),'Mes pasado'],
            'ultimas_24_horas'=>[$ahora->copy()->subHours(24),$ahora->copy(),'Últimas 24 horas'],
            'ultimos_7_dias'=>[$ahora->copy()->subDays(7),$ahora->copy(),'Últimos 7 días'],
            'ultimos_30_dias'=>[$ahora->copy()->subDays(30),$ahora->copy(),'Últimos 30 días'],
            default=>[null,null,'Todo el historial disponible']
        };

        if($desde&&$hasta) $query->whereBetween('a.fecha_hora',[$desde->format('Y-m-d H:i:s'),$hasta->format('Y-m-d H:i:s')]);

        return ['clave'=>$periodo,'desde'=>$desde?->format('Y-m-d H:i:s'),'hasta'=>$hasta?->format('Y-m-d H:i:s'),'descripcion'=>$descripcion];
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
            $subquery->selectRaw('1')->from('asignaciones_incubadora as ai')->whereColumn('ai.incubadora_id','a.incubadora_id')->where('ai.user_id',$user->id)->whereDate('ai.fecha_inicio','<=',$hoy)->where(function($query) use($hoy){$query->whereNull('ai.fecha_fin')->orWhereDate('ai.fecha_fin','>=',$hoy);});
        });
    }

    private function columnas(): array
    {
        return ['a.id','a.incubadora_id','a.lote_id','a.mensaje','a.fecha_hora','a.fecha_atencion','a.fecha_resolucion','a.observaciones','i.codigo as incubadora_codigo','i.nombre as incubadora_nombre','l.codigo_lote','ta.clave as tipo_clave','ta.nombre as tipo_nombre','na.clave as nivel_clave','na.nombre as nivel_nombre','ea.clave as estado_clave','ea.nombre as estado_nombre'];
    }

    private function formatearAlerta(object $alerta): array
    {
        return ['id'=>(int)$alerta->id,'incubadora'=>['id'=>(int)$alerta->incubadora_id,'codigo'=>$alerta->incubadora_codigo,'nombre'=>$alerta->incubadora_nombre],'lote'=>$alerta->lote_id?['id'=>(int)$alerta->lote_id,'codigo'=>$alerta->codigo_lote]:null,'tipo'=>['clave'=>$alerta->tipo_clave,'nombre'=>$alerta->tipo_nombre],'nivel'=>['clave'=>$alerta->nivel_clave,'nombre'=>$alerta->nivel_nombre],'estado'=>['clave'=>$alerta->estado_clave,'nombre'=>$alerta->estado_nombre],'mensaje'=>$alerta->mensaje,'fecha_hora'=>$alerta->fecha_hora,'fecha_atencion'=>$alerta->fecha_atencion,'fecha_resolucion'=>$alerta->fecha_resolucion,'observaciones'=>$alerta->observaciones];
    }

    private function sinDatos(array $alcance,array $periodo): array
    {
        return ['ok'=>false,'tipo'=>'sin_datos','mensaje'=>'No se encontraron alertas que coincidan con los criterios solicitados.','alcance'=>$alcance,'periodo'=>$periodo];
    }
}
