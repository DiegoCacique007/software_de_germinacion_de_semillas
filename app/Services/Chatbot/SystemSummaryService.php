<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class SystemSummaryService
{
    private string $timezone='America/Mexico_City';

    private const LOTES_ACTIVOS=['activo','seguimiento'];
    private const ALERTAS_ABIERTAS=['pendiente','atendida'];

    public function ejecutar(array $plan,User $user): array
    {
        if(($plan['recurso']??null)!=='resumen'){
            return $this->error(
                'recurso_no_soportado',
                'SystemSummaryService solamente procesa el resumen del sistema.'
            );
        }

        $periodo=$this->resolverPeriodo($plan['periodo']??null);

        $incubadoras=$this->incubadoras($user)->count();
        $lotesActivos=$this->lotes($user)
            ->join('estados_lote as el','el.id','=','l.estado_lote_id')
            ->whereIn('el.clave',self::LOTES_ACTIVOS)
            ->count();

        $frascos=$this->frascos($user)->count();

        $alertasActivas=$this->alertas($user)
            ->join('estados_alerta as ea','ea.id','=','a.estado_alerta_id')
            ->whereIn('ea.clave',self::ALERTAS_ABIERTAS)
            ->count();

        $lecturas=$this->lecturas($user);

        if($periodo['clave']!==null){
            $lecturas=$this->aplicarPeriodo(
                $lecturas,
                'lm.fecha_hora',
                $periodo
            );
        }

        $ultimaLectura=$lecturas
            ->join('incubadoras as i','i.id','=','lm.incubadora_id')
            ->select([
                'lm.id',
                'lm.incubadora_id',
                'lm.temperatura',
                'lm.humedad',
                'lm.fecha_hora',
                'lm.observaciones',
                'i.codigo as incubadora_codigo',
                'i.nombre as incubadora_nombre'
            ])
            ->orderByDesc('lm.fecha_hora')
            ->orderByDesc('lm.id')
            ->first();

        $alertasPeriodo=null;

        if($periodo['clave']!==null){
            $query=$this->alertas($user);
            $query=$this->aplicarPeriodo(
                $query,
                'a.fecha_hora',
                $periodo
            );

            $alertasPeriodo=$query->count();
        }

        return [
            'ok'=>true,
            'tipo'=>'resumen_sistema',
            'recurso'=>'resumen',
            'operacion'=>'resumen',
            'alcance'=>$user->isSuperAdmin()
                ?'global'
                :'asignaciones_vigentes',
            'totales'=>[
                'incubadoras'=>$incubadoras,
                'lotes_activos'=>$lotesActivos,
                'frascos'=>$frascos,
                'alertas_activas'=>$alertasActivas
            ],
            'ultima_lectura'=>$ultimaLectura
                ?$this->mapearLectura($ultimaLectura)
                :null,
            'periodo'=>$periodo,
            'alertas_periodo'=>$alertasPeriodo
        ];
    }

    private function incubadoras(User $user): Builder
    {
        $query=DB::table('incubadoras as i');

        return $this->aplicarAlcanceIncubadora(
            $query,
            $user,
            'i.id'
        );
    }

    private function lotes(User $user): Builder
    {
        $query=DB::table('lotes as l')
            ->join(
                'posiciones_incubadora as p',
                'p.id',
                '=',
                'l.posicion_incubadora_id'
            );

        return $this->aplicarAlcanceIncubadora(
            $query,
            $user,
            'p.incubadora_id'
        );
    }

    private function frascos(User $user): Builder
    {
        $query=DB::table('frascos as f')
            ->join('lotes as l','l.id','=','f.lote_id')
            ->join(
                'posiciones_incubadora as p',
                'p.id',
                '=',
                'l.posicion_incubadora_id'
            );

        return $this->aplicarAlcanceIncubadora(
            $query,
            $user,
            'p.incubadora_id'
        );
    }

    private function alertas(User $user): Builder
    {
        $query=DB::table('alertas as a');

        return $this->aplicarAlcanceIncubadora(
            $query,
            $user,
            'a.incubadora_id'
        );
    }

    private function lecturas(User $user): Builder
    {
        $query=DB::table('lecturas_microclima as lm');

        return $this->aplicarAlcanceIncubadora(
            $query,
            $user,
            'lm.incubadora_id'
        );
    }

    private function aplicarAlcanceIncubadora(
        Builder $query,
        User $user,
        string $columna
    ): Builder {
        if($user->isSuperAdmin()) return $query;

        if(!$user->isEncargado()){
            return $query->whereRaw('1=0');
        }

        $hoy=Carbon::now($this->timezone)->toDateString();

        return $query->whereExists(
            function($q) use($user,$hoy,$columna){
                $q->selectRaw('1')
                    ->from('asignaciones_incubadora as ai')
                    ->whereColumn(
                        'ai.incubadora_id',
                        $columna
                    )
                    ->where('ai.user_id',$user->id)
                    ->whereDate('ai.fecha_inicio','<=',$hoy)
                    ->where(
                        function($x) use($hoy){
                            $x->whereNull('ai.fecha_fin')
                                ->orWhereDate(
                                    'ai.fecha_fin',
                                    '>=',
                                    $hoy
                                );
                        }
                    );
            }
        );
    }

    private function resolverPeriodo(?string $periodo): array
    {
        $ahora=Carbon::now($this->timezone);

        switch($periodo){
            case 'hoy':
                $desde=$ahora->copy()->startOfDay();
                $hasta=$ahora->copy()->endOfDay();
                $descripcion='hoy';
                break;

            case 'ayer':
                $desde=$ahora->copy()->subDay()->startOfDay();
                $hasta=$ahora->copy()->subDay()->endOfDay();
                $descripcion='ayer';
                break;

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

            case 'ultimas_24_horas':
                $desde=$ahora->copy()->subDay();
                $hasta=$ahora;
                $descripcion='las últimas 24 horas';
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

            default:
                return [
                    'clave'=>null,
                    'descripcion'=>'sin filtro temporal',
                    'desde'=>null,
                    'hasta'=>null
                ];
        }

        return [
            'clave'=>$periodo,
            'descripcion'=>$descripcion,
            'desde'=>$desde->toDateTimeString(),
            'hasta'=>$hasta->toDateTimeString()
        ];
    }

    private function aplicarPeriodo(
        Builder $query,
        string $campo,
        array $periodo
    ): Builder {
        if(
            empty($periodo['desde'])
            ||empty($periodo['hasta'])
        ){
            return $query;
        }

        return $query->whereBetween(
            $campo,
            [
                $periodo['desde'],
                $periodo['hasta']
            ]
        );
    }

    private function mapearLectura(object $r): array
    {
        return [
            'id'=>(int)$r->id,
            'temperatura'=>$r->temperatura!==null
                ?(float)$r->temperatura
                :null,
            'humedad'=>$r->humedad!==null
                ?(float)$r->humedad
                :null,
            'fecha_hora'=>(string)$r->fecha_hora,
            'observaciones'=>$r->observaciones,
            'incubadora'=>[
                'id'=>(int)$r->incubadora_id,
                'codigo'=>$r->incubadora_codigo,
                'nombre'=>$r->incubadora_nombre
            ]
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
