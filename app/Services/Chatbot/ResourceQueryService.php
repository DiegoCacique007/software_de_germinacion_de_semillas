<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ResourceQueryService
{
    private const RECURSOS=['lotes','frascos','especies'];
    private const LIMITE_DEFECTO=5;
    private const LIMITE_MAXIMO=50;
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        $recurso=$plan['recurso']??null;

        if(!in_array($recurso,self::RECURSOS,true)) return ['ok'=>false,'tipo'=>'recurso_no_soportado','mensaje'=>'Este servicio solamente consulta lotes, frascos y especies.'];

        $query=match($recurso){
            'lotes'=>$this->queryLotes($user),
            'frascos'=>$this->queryFrascos($user),
            'especies'=>$this->queryEspecies($user)
        };

        $alcance=$this->aplicarResolucion($query,$recurso,$resolucion);
        $this->aplicarFiltros($query,$recurso,$plan['filtros']??[]);
        $periodo=$this->aplicarPeriodo($query,$recurso,$plan['periodo']??null);
        if(isset($plan['filtros']['sin_seguimiento_dias'])){
            $periodo['clave']='sin_seguimiento_reciente';
            $periodo['descripcion']='sin seguimiento en los últimos '.(int)$plan['filtros']['sin_seguimiento_dias'].' días';
        }
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$recurso,$alcance,$periodo),
            'estado'=>$this->detalle($query,$recurso,$alcance,$periodo),
            'ultimo'=>$this->ultimo($query,$recurso,$alcance,$periodo),
            'listar'=>$this->listar($query,$recurso,$plan,$alcance,$periodo),
            default=>['ok'=>false,'tipo'=>'operacion_no_soportada','mensaje'=>'La operación solicitada todavía no está disponible para este recurso.']
        };
    }

    private function contar(Builder $query,string $recurso,array $alcance,array $periodo): array
    {
        return ['ok'=>true,'tipo'=>'recurso_conteo','recurso'=>$recurso,'operacion'=>'contar','total'=>(clone $query)->count(),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function listar(Builder $query,string $recurso,array $plan,array $alcance,array $periodo): array
    {
        $limite=max(1,min((int)($plan['limite']??self::LIMITE_DEFECTO),self::LIMITE_MAXIMO));
        $total=(clone $query)->count();
        $items=$this->ordenar(clone $query,$recurso,$plan['orden']??'desc')->limit($limite)->get();

        if($items->isEmpty()) return $this->sinDatos($recurso,$alcance,$periodo);

        return ['ok'=>true,'tipo'=>'recurso_lista','recurso'=>$recurso,'operacion'=>'listar','total'=>$total,'total_devuelto'=>$items->count(),'limite'=>$limite,'datos'=>$items->map(fn($item)=>$this->mapear($recurso,$item))->values()->all(),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function detalle(Builder $query,string $recurso,array $alcance,array $periodo): array
    {
        $total=(clone $query)->count();

        if($total===0) return $this->sinDatos($recurso,$alcance,$periodo);

        if($total>1) return ['ok'=>false,'tipo'=>'requiere_aclaracion','mensaje'=>'La consulta coincide con varios registros. Indica cuál deseas revisar.','recurso'=>$recurso,'total'=>$total,'alcance'=>$alcance,'periodo'=>$periodo];

        return ['ok'=>true,'tipo'=>'recurso_detalle','recurso'=>$recurso,'operacion'=>'estado','datos'=>$this->mapear($recurso,$query->first()),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function ultimo(Builder $query,string $recurso,array $alcance,array $periodo): array
    {
        $item=$this->ordenar(clone $query,$recurso,'desc')->first();

        if(!$item) return $this->sinDatos($recurso,$alcance,$periodo);

        return ['ok'=>true,'tipo'=>'recurso_detalle','recurso'=>$recurso,'operacion'=>'ultimo','datos'=>$this->mapear($recurso,$item),'alcance'=>$alcance,'periodo'=>$periodo];
    }

    private function queryLotes(User $user): Builder
    {
        $query=DB::table('lotes as l')
            ->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')
            ->join('incubadoras as i','i.id','=','p.incubadora_id')
            ->join('especies as e','e.id','=','l.especie_id')
            ->leftJoin('estados_lote as el','el.id','=','l.estado_lote_id')
            ->select(['l.id','l.codigo_lote','l.especie_id','l.posicion_incubadora_id','l.fecha_siembra','l.fecha_inicio','l.fecha_fin','l.observaciones','e.nombre_comun','e.nombre_cientifico','p.numero_posicion','i.id as incubadora_id','i.codigo as incubadora_codigo','i.nombre as incubadora_nombre','el.clave as estado_clave','el.nombre as estado_nombre']);

        $this->restringirPorIncubadora($query,$user,'i.id');

        return $query;
    }

    private function queryFrascos(User $user): Builder
    {
        $query=DB::table('frascos as f')
            ->join('lotes as l','l.id','=','f.lote_id')
            ->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')
            ->join('incubadoras as i','i.id','=','p.incubadora_id')
            ->join('especies as e','e.id','=','l.especie_id')
            ->leftJoin('estados_frasco as ef','ef.id','=','f.estado_frasco_id')
            ->select(['f.id','f.lote_id','f.numero_frasco','f.cantidad_semillas','f.observaciones','f.created_at','l.codigo_lote','e.id as especie_id','e.nombre_comun','e.nombre_cientifico','i.id as incubadora_id','i.codigo as incubadora_codigo','i.nombre as incubadora_nombre','ef.clave as estado_clave','ef.nombre as estado_nombre']);

        $this->restringirPorIncubadora($query,$user,'i.id');

        return $query;
    }

    private function queryEspecies(User $user): Builder
    {
        $query=DB::table('especies as e')->select(['e.id','e.nombre_comun','e.nombre_cientifico','e.familia','e.descripcion','e.created_at']);

        if($user->isSuperAdmin()) return $query;
        if(!$user->isEncargado()) return $query->whereRaw('1 = 0');

        $hoy=now($this->timezone)->toDateString();

        return $query->whereExists(function($subquery) use($user,$hoy){
            $subquery->selectRaw('1')->from('lotes as le')->join('posiciones_incubadora as pe','pe.id','=','le.posicion_incubadora_id')->join('asignaciones_incubadora as ae','ae.incubadora_id','=','pe.incubadora_id')->whereColumn('le.especie_id','e.id')->where('ae.user_id',$user->id)->whereDate('ae.fecha_inicio','<=',$hoy)->where(function($q) use($hoy){$q->whereNull('ae.fecha_fin')->orWhereDate('ae.fecha_fin','>=',$hoy);});
        });
    }

    private function aplicarResolucion(Builder $query,string $recurso,array $resolucion): array
    {
        $entidades=$resolucion['entidades']??[];
        $alcance=['tipo'=>'global_autorizado'];

        if($recurso==='lotes'){
            if(isset($entidades['lote']['id'])){
                $dato=['id'=>(int)$entidades['lote']['id'],'codigo'=>$entidades['lote']['codigo']??null];
                $query->where('l.id',$dato['id']);
                $this->agregarAlcance($alcance,'lote',$dato);
            }

            if(isset($entidades['especie']['id'])){
                $dato=['id'=>(int)$entidades['especie']['id'],'nombre_comun'=>$entidades['especie']['nombre_comun']??null];
                $query->where('l.especie_id',$dato['id']);
                $this->agregarAlcance($alcance,'especie',$dato);
            }

            if(isset($entidades['incubadora']['id'])){
                $dato=['id'=>(int)$entidades['incubadora']['id'],'codigo'=>$entidades['incubadora']['codigo']??null,'nombre'=>$entidades['incubadora']['nombre']??null];
                $query->where('i.id',$dato['id']);
                $this->agregarAlcance($alcance,'incubadora',$dato);
            }
        }

        if($recurso==='frascos'){
            if(isset($entidades['frasco']['id'])){
                $dato=['id'=>(int)$entidades['frasco']['id'],'numero'=>$entidades['frasco']['numero']??null];
                $query->where('f.id',$dato['id']);
                $this->agregarAlcance($alcance,'frasco',$dato);
            }

            if(isset($entidades['lote']['id'])){
                $dato=['id'=>(int)$entidades['lote']['id'],'codigo'=>$entidades['lote']['codigo']??null];
                $query->where('f.lote_id',$dato['id']);
                $this->agregarAlcance($alcance,'lote',$dato);
            }

            if(isset($entidades['especie']['id'])){
                $dato=['id'=>(int)$entidades['especie']['id'],'nombre_comun'=>$entidades['especie']['nombre_comun']??null];
                $query->where('l.especie_id',$dato['id']);
                $this->agregarAlcance($alcance,'especie',$dato);
            }

            if(isset($entidades['incubadora']['id'])){
                $dato=['id'=>(int)$entidades['incubadora']['id'],'codigo'=>$entidades['incubadora']['codigo']??null,'nombre'=>$entidades['incubadora']['nombre']??null];
                $query->where('i.id',$dato['id']);
                $this->agregarAlcance($alcance,'incubadora',$dato);
            }
        }

        if($recurso==='especies'){
            if(isset($entidades['especie']['id'])){
                $dato=['id'=>(int)$entidades['especie']['id'],'nombre_comun'=>$entidades['especie']['nombre_comun']??null];
                $query->where('e.id',$dato['id']);
                $this->agregarAlcance($alcance,'especie',$dato);
            }

            if(isset($entidades['lote']['id'])){
                $lote=$entidades['lote'];
                $especieId=$lote['especie']['id']??null;

                if($especieId) $query->where('e.id',(int)$especieId);
                else $query->whereExists(function($subquery) use($lote){$subquery->selectRaw('1')->from('lotes as lr')->whereColumn('lr.especie_id','e.id')->where('lr.id',(int)$lote['id']);});

                $this->agregarAlcance($alcance,'lote',['id'=>(int)$lote['id'],'codigo'=>$lote['codigo']??null]);
            }

            if(isset($entidades['incubadora']['id'])){
                $incubadora=$entidades['incubadora'];
                $incubadoraId=(int)$incubadora['id'];

                $query->whereExists(function($subquery) use($incubadoraId){
                    $subquery->selectRaw('1')->from('lotes as li')->join('posiciones_incubadora as pi','pi.id','=','li.posicion_incubadora_id')->whereColumn('li.especie_id','e.id')->where('pi.incubadora_id',$incubadoraId);
                });

                $this->agregarAlcance($alcance,'incubadora',['id'=>$incubadoraId,'codigo'=>$incubadora['codigo']??null,'nombre'=>$incubadora['nombre']??null]);
            }
        }

        return $alcance;
    }

    private function agregarAlcance(array &$alcance,string $tipo,array $dato): void
    {
        if(($alcance['tipo']??null)==='global_autorizado'){
            $alcance=['tipo'=>$tipo,...$dato];
            return;
        }

        $alcance[$tipo]=$dato;
    }

    private function aplicarFiltros(Builder $query,string $recurso,array $filtros): void
    {
        if(isset($filtros['sin_seguimiento_dias'])&&in_array($recurso,['lotes','frascos'],true)){
            $tabla=$recurso==='lotes'?'seguimientos_lote':'seguimientos_frasco';
            $fk=$recurso==='lotes'?'lote_id':'frasco_id';
            $id=$recurso==='lotes'?'l.id':'f.id';
            $hasta=now($this->timezone)->toDateString();
            $desde=now($this->timezone)->subDays((int)$filtros['sin_seguimiento_dias']-1)->toDateString();
            $query->whereNotExists(function($sub) use($tabla,$fk,$id,$desde,$hasta){
                $sub->selectRaw('1')->from($tabla.' as seguimiento_reciente')
                    ->whereColumn('seguimiento_reciente.'.$fk,$id)
                    ->whereDate('seguimiento_reciente.fecha_revision','>=',$desde)
                    ->whereDate('seguimiento_reciente.fecha_revision','<=',$hasta);
            });
        }
        if(($filtros['estado_general']??null)!=='activo') return;

        if($recurso==='lotes') $query->whereIn('el.clave',['activo','seguimiento']);
        if($recurso==='frascos') $query->whereIn('ef.clave',['preparado','imbibicion','germinacion','germinado']);
    }

    private function aplicarPeriodo(Builder $query,string $recurso,?string $periodo): array
    {
        if(!$periodo) return ['clave'=>null,'desde'=>null,'hasta'=>null,'campo'=>null,'descripcion'=>'Todo el historial disponible'];

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

        $campo=match($recurso){
            'lotes'=>'l.fecha_siembra',
            'frascos'=>'f.created_at',
            'especies'=>'e.created_at'
        };

        if($desde&&$hasta){
            if($recurso==='lotes') $query->whereDate($campo,'>=',$desde->toDateString())->whereDate($campo,'<=',$hasta->toDateString());
            else $query->whereBetween($campo,[$desde->format('Y-m-d H:i:s'),$hasta->format('Y-m-d H:i:s')]);
        }

        return ['clave'=>$periodo,'desde'=>$desde?->format('Y-m-d H:i:s'),'hasta'=>$hasta?->format('Y-m-d H:i:s'),'campo'=>$campo,'descripcion'=>$descripcion];
    }

    private function ordenar(Builder $query,string $recurso,string $orden): Builder
    {
        $direccion=$orden==='asc'?'asc':'desc';

        return match($recurso){
            'lotes'=>$query->orderBy('l.fecha_siembra',$direccion)->orderBy('l.id',$direccion),
            'frascos'=>$query->orderBy('f.created_at',$direccion)->orderBy('f.id',$direccion),
            'especies'=>$query->orderBy('e.created_at',$direccion)->orderBy('e.id',$direccion)
        };
    }

    private function restringirPorIncubadora(Builder $query,User $user,string $columnaIncubadora): void
    {
        if($user->isSuperAdmin()) return;

        if(!$user->isEncargado()){
            $query->whereRaw('1 = 0');
            return;
        }

        $hoy=now($this->timezone)->toDateString();

        $query->whereExists(function($subquery) use($user,$hoy,$columnaIncubadora){
            $subquery->selectRaw('1')->from('asignaciones_incubadora as ai')->whereColumn('ai.incubadora_id',$columnaIncubadora)->where('ai.user_id',$user->id)->whereDate('ai.fecha_inicio','<=',$hoy)->where(function($q) use($hoy){$q->whereNull('ai.fecha_fin')->orWhereDate('ai.fecha_fin','>=',$hoy);});
        });
    }

    private function mapear(string $recurso,object $item): array
    {
        return match($recurso){
            'lotes'=>$this->mapLote($item),
            'frascos'=>$this->mapFrasco($item),
            'especies'=>$this->mapEspecie($item)
        };
    }

    private function mapLote(object $item): array
    {
        return ['id'=>(int)$item->id,'codigo'=>$item->codigo_lote,'especie'=>['id'=>(int)$item->especie_id,'nombre_comun'=>$item->nombre_comun,'nombre_cientifico'=>$item->nombre_cientifico],'incubadora'=>['id'=>(int)$item->incubadora_id,'codigo'=>$item->incubadora_codigo,'nombre'=>$item->incubadora_nombre],'posicion'=>['id'=>(int)$item->posicion_incubadora_id,'numero'=>$item->numero_posicion],'estado'=>['clave'=>$item->estado_clave,'nombre'=>$item->estado_nombre],'fecha_siembra'=>$item->fecha_siembra,'fecha_inicio'=>$item->fecha_inicio,'fecha_fin'=>$item->fecha_fin,'observaciones'=>$item->observaciones];
    }

    private function mapFrasco(object $item): array
    {
        return ['id'=>(int)$item->id,'numero'=>$item->numero_frasco,'cantidad_semillas'=>$item->cantidad_semillas,'lote'=>['id'=>(int)$item->lote_id,'codigo'=>$item->codigo_lote],'especie'=>['id'=>(int)$item->especie_id,'nombre_comun'=>$item->nombre_comun,'nombre_cientifico'=>$item->nombre_cientifico],'incubadora'=>['id'=>(int)$item->incubadora_id,'codigo'=>$item->incubadora_codigo,'nombre'=>$item->incubadora_nombre],'estado'=>['clave'=>$item->estado_clave,'nombre'=>$item->estado_nombre],'created_at'=>$item->created_at,'observaciones'=>$item->observaciones];
    }

    private function mapEspecie(object $item): array
    {
        return ['id'=>(int)$item->id,'nombre_comun'=>$item->nombre_comun,'nombre_cientifico'=>$item->nombre_cientifico,'familia'=>$item->familia,'descripcion'=>$item->descripcion,'created_at'=>$item->created_at];
    }

    private function sinDatos(string $recurso,array $alcance,array $periodo): array
    {
        return ['ok'=>false,'tipo'=>'sin_datos','recurso'=>$recurso,'mensaje'=>'No se encontraron registros que coincidan con la consulta.','alcance'=>$alcance,'periodo'=>$periodo];
    }
}
