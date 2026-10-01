<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='lecturas') return ['ok'=>false,'tipo'=>'recurso_no_soportado','mensaje'=>'StatisticsService solamente procesa consultas de lecturas de microclima en esta etapa.'];

        $operacion=$plan['operacion']??null;
        $variable=$plan['variable']??null;
        $query=$this->queryLecturasPermitidas($user);
        $alcance=$this->aplicarEntidadesResueltas($query,$resolucion);
        $periodo=$this->aplicarPeriodo($query,$plan['periodo']??null);

        return match($operacion){
            'ultimo'=>$this->ultimaLectura($query,$alcance,$periodo),
            'estado'=>$this->estadoMicroclima($query,$alcance,$periodo),
            'primero'=>$this->primeraLectura($query,$alcance,$periodo),
            'listar'=>$this->listarLecturas($query,$plan,$alcance,$periodo),
            'promedio','maximo','minimo'=>$this->agregacion($query,$operacion,$variable,$alcance,$periodo),
            'contar'=>$this->contarLecturas($query,$alcance,$periodo),
            default=>['ok'=>false,'tipo'=>'operacion_no_soportada','mensaje'=>'La operación solicitada todavía no está implementada para lecturas de microclima.']
        };
    }

    private function estadoMicroclima(Builder $query,array $alcance,array $periodo): array
    {
        $resultado=$this->ultimaLectura($query,$alcance,$periodo);
        if(!$resultado['ok']) return $resultado;

        // La incubadora procede de una lectura ya restringida al ámbito del usuario.
        $incubadoraId=$resultado['datos']['incubadora']['id'];
        $condiciones=DB::table('lotes as l')->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')
            ->join('especies as e','e.id','=','l.especie_id')
            ->leftJoin('condiciones_optimas_especie as c','c.especie_id','=','e.id')
            ->where('p.incubadora_id',$incubadoraId)
            ->select(['e.id as especie_id','e.nombre_comun','c.id','c.temperatura_min','c.temperatura_max','c.humedad_min','c.humedad_max'])
            ->distinct()->get()->groupBy('especie_id');

        $resultado['comparaciones']=[];
        foreach($condiciones as $opciones){
            $c=$opciones->first();
            $comparacion=['especie'=>$c->nombre_comun,'variables'=>[]];
            if($opciones->count()>1) $comparacion['advertencia']='Hay varias configuraciones óptimas registradas; no se elige una automáticamente.';
            elseif(!$c->id) $comparacion['advertencia']='No hay condiciones óptimas registradas.';
            else foreach(['temperatura','humedad'] as $variable){
                $valor=$resultado['datos'][$variable];
                $min=$c->{$variable.'_min'};
                $max=$c->{$variable.'_max'};
                if($valor===null||$min===null||$max===null||$min>$max) continue;
                $comparacion['variables'][$variable]=['min'=>(float)$min,'max'=>(float)$max,
                    'estado'=>$valor<$min?'por debajo':($valor>$max?'por encima':'dentro del rango')];
            }
            $resultado['comparaciones'][]=$comparacion;
        }
        return $resultado;
    }

    private function ultimaLectura(Builder $query,array $alcance,array $periodo): array
    {
        $lectura=(clone $query)->select(['lm.id','lm.incubadora_id','lm.fecha_hora','lm.temperatura','lm.humedad','lm.observaciones','i.codigo as incubadora_codigo','i.nombre as incubadora_nombre'])->orderByDesc('lm.fecha_hora')->orderByDesc('lm.id')->first();

        if(!$lectura) return $this->sinDatos($alcance,$periodo);

        return ['ok'=>true,'tipo'=>'ultima_lectura','operacion'=>'ultimo','datos'=>$this->formatearLectura($lectura),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function primeraLectura(Builder $query,array $alcance,array $periodo): array
    {
        $lectura=(clone $query)->select(['lm.id','lm.incubadora_id','lm.fecha_hora','lm.temperatura','lm.humedad','lm.observaciones','i.codigo as incubadora_codigo','i.nombre as incubadora_nombre'])->orderBy('lm.fecha_hora')->orderBy('lm.id')->first();

        if(!$lectura) return $this->sinDatos($alcance,$periodo);

        return ['ok'=>true,'tipo'=>'primera_lectura','operacion'=>'primero','datos'=>$this->formatearLectura($lectura),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function listarLecturas(Builder $query,array $plan,array $alcance,array $periodo): array
    {
        $limite=max(1,min((int)($plan['limite']??5),50));
        $orden=$plan['orden']??'desc';
        $query=(clone $query)->select(['lm.id','lm.incubadora_id','lm.fecha_hora','lm.temperatura','lm.humedad','lm.observaciones','i.codigo as incubadora_codigo','i.nombre as incubadora_nombre']);

        $orden==='asc'?$query->orderBy('lm.fecha_hora')->orderBy('lm.id'):$query->orderByDesc('lm.fecha_hora')->orderByDesc('lm.id');

        $lecturas=$query->limit($limite)->get();

        if($lecturas->isEmpty()) return $this->sinDatos($alcance,$periodo);

        return ['ok'=>true,'tipo'=>'lista_lecturas','operacion'=>'listar','limite_solicitado'=>$limite,'total_devuelto'=>$lecturas->count(),'datos'=>$lecturas->map(fn($lectura)=>$this->formatearLectura($lectura))->values()->all(),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function agregacion(Builder $query,string $operacion,?string $variable,array $alcance,array $periodo): array
    {
        $columnas=['temperatura'=>'lm.temperatura','humedad'=>'lm.humedad'];

        if(!$variable||!isset($columnas[$variable])) return ['ok'=>false,'tipo'=>'variable_requerida','mensaje'=>'Para calcular '.$operacion.' necesito saber si deseas analizar temperatura o humedad.','alcance'=>$alcance,'periodo'=>$periodo];

        $funciones=['promedio'=>'AVG','maximo'=>'MAX','minimo'=>'MIN'];
        $funcion=$funciones[$operacion];
        $columna=$columnas[$variable];
        $resultado=(clone $query)->selectRaw("{$funcion}({$columna}) as valor, COUNT({$columna}) as total_registros")->first();

        if(!$resultado||$resultado->valor===null) return $this->sinDatos($alcance,$periodo);

        return ['ok'=>true,'tipo'=>'estadistica_microclima','operacion'=>$operacion,'variable'=>$variable,'valor'=>round((float)$resultado->valor,2),'unidad'=>$variable==='temperatura'?'°C':'%','total_registros'=>(int)$resultado->total_registros,'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function contarLecturas(Builder $query,array $alcance,array $periodo): array
    {
        return ['ok'=>true,'tipo'=>'conteo_lecturas','operacion'=>'contar','total'=>(clone $query)->count('lm.id'),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function queryLecturasPermitidas(User $user): Builder
    {
        $query=DB::table('lecturas_microclima as lm')->join('incubadoras as i','i.id','=','lm.incubadora_id');
        $this->restringirPorUsuario($query,$user);

        return $query;
    }

    private function aplicarEntidadesResueltas(Builder $query,array $resolucion): array
    {
        $entidades=$resolucion['entidades']??[];
        $alcance=['tipo'=>'global_autorizado'];

        if(isset($entidades['incubadora']['id'])){
            $id=(int)$entidades['incubadora']['id'];
            $query->where('i.id',$id);
            $alcance=['tipo'=>'incubadora','id'=>$id,'codigo'=>$entidades['incubadora']['codigo']??null,'nombre'=>$entidades['incubadora']['nombre']??null];
        }

        if(isset($entidades['lote']['id'])){
            $lote=$entidades['lote'];
            $incubadora=$lote['incubadora']??null;

            if(isset($incubadora['id'])) $query->where('i.id',(int)$incubadora['id']);

            $alcance=['tipo'=>'lote','lote'=>['id'=>(int)$lote['id'],'codigo'=>$lote['codigo']??null],'incubadora'=>$incubadora];
        }

        if(isset($entidades['especie']['id'])){
            $especieId=(int)$entidades['especie']['id'];

            $query->whereExists(function($subquery) use($especieId){
                $subquery->selectRaw('1')->from('lotes as lscope')->join('posiciones_incubadora as pscope','pscope.id','=','lscope.posicion_incubadora_id')->whereColumn('pscope.incubadora_id','i.id')->where('lscope.especie_id',$especieId);
            });

            if(isset($entidades['lote']['especie']['id'])&&(int)$entidades['lote']['especie']['id']!==$especieId) $query->whereRaw('1 = 0');

            $especie=['id'=>$especieId,'nombre_comun'=>$entidades['especie']['nombre_comun']??null,'nombre_cientifico'=>$entidades['especie']['nombre_cientifico']??null];

            if(($alcance['tipo']??null)==='global_autorizado') $alcance=['tipo'=>'especie',...$especie];
            else $alcance['especie']=$especie;
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

        if($desde&&$hasta) $query->whereBetween('lm.fecha_hora',[$desde->format('Y-m-d H:i:s'),$hasta->format('Y-m-d H:i:s')]);

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
            $subquery->selectRaw('1')->from('asignaciones_incubadora as ai')->whereColumn('ai.incubadora_id','i.id')->where('ai.user_id',$user->id)->whereDate('ai.fecha_inicio','<=',$hoy)->where(function($query) use($hoy){$query->whereNull('ai.fecha_fin')->orWhereDate('ai.fecha_fin','>=',$hoy);});
        });
    }

    private function formatearLectura(object $lectura): array
    {
        $fecha=Carbon::parse($lectura->fecha_hora,$this->timezone);
        $ahora=now($this->timezone);
        $esFutura=$fecha->greaterThan($ahora);
        $minutos=$esFutura?0:(int)$fecha->diffInMinutes($ahora);
        $frescura=$esFutura?'inconsistente':match(true){$minutos<=2=>'actual',$minutos<=5=>'reciente',default=>'desactualizada'};

        return ['id'=>(int)$lectura->id,'incubadora'=>['id'=>(int)$lectura->incubadora_id,'codigo'=>$lectura->incubadora_codigo,'nombre'=>$lectura->incubadora_nombre],'temperatura'=>$lectura->temperatura!==null?(float)$lectura->temperatura:null,'humedad'=>$lectura->humedad!==null?(float)$lectura->humedad:null,'fecha_hora'=>$fecha->format('Y-m-d H:i:s'),'fecha_formateada'=>$fecha->format('d/m/Y H:i:s'),'minutos_desde_lectura'=>$minutos,'frescura'=>$frescura,'fecha_futura'=>$esFutura,'observaciones'=>$lectura->observaciones];
    }

    private function sinDatos(array $alcance,array $periodo): array
    {
        return ['ok'=>false,'tipo'=>'sin_datos','mensaje'=>'No se encontraron lecturas para los criterios solicitados.','alcance'=>$alcance,'periodo'=>$periodo];
    }
}
