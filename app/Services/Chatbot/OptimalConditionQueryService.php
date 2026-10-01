<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class OptimalConditionQueryService
{
    private const LIMITE=5;
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='condiciones_optimas') return ['ok'=>false,'tipo'=>'recurso_no_soportado','mensaje'=>'Este servicio solamente consulta condiciones óptimas de especies.'];

        $entidades=$resolucion['entidades']??[];
        $especie=$entidades['especie']??$entidades['lote']['especie']??null;

        if(!$especie||empty($especie['id'])){
            $opciones=$this->queryEspeciesPermitidas($user)->orderBy('e.nombre_comun')->limit(self::LIMITE)->pluck('e.nombre_comun')->filter()->values()->all();
            $mensaje=empty($opciones)?'No hay especies disponibles para consultar condiciones óptimas.':'Indica la especie que deseas consultar. Disponibles: '.implode(', ',$opciones).'.';

            return ['ok'=>false,'tipo'=>'requiere_aclaracion','mensaje'=>$mensaje,'sugerencias'=>array_map(fn($nombre)=>'Condiciones óptimas de '.$nombre,$opciones)];
        }

        $especieId=(int)$especie['id'];
        $query=$this->queryCondicionesPermitidas($user)->where('c.especie_id',$especieId);
        $total=(clone $query)->count('c.id');

        if($total===0) return ['ok'=>false,'tipo'=>'sin_datos','recurso'=>'condiciones_optimas','mensaje'=>'No hay condiciones óptimas registradas para '.($especie['nombre_comun']??'la especie consultada').'.','alcance'=>['tipo'=>'especie','id'=>$especieId,'nombre_comun'=>$especie['nombre_comun']??null]];

        $operacion=$plan['operacion']??'listar';

        if($operacion==='ultimo') $items=(clone $query)->orderByDesc('c.id')->limit(1)->get();
        else $items=(clone $query)->orderBy('c.id')->limit(self::LIMITE)->get();

        return ['ok'=>true,'tipo'=>'condiciones_optimas','operacion'=>$operacion,'variable'=>$plan['variable']??null,'total'=>$total,'especie'=>['id'=>$especieId,'nombre_comun'=>$especie['nombre_comun']??$items->first()?->nombre_comun,'nombre_cientifico'=>$especie['nombre_cientifico']??$items->first()?->nombre_cientifico],'datos'=>$items->map(fn($item)=>$this->mapear($item))->values()->all(),'multiple'=>$total>1];
    }

    private function queryCondicionesPermitidas(User $user): Builder
    {
        $query=DB::table('condiciones_optimas_especie as c')->join('especies as e','e.id','=','c.especie_id')->select(['c.id','c.especie_id','c.temperatura_min','c.temperatura_max','c.humedad_min','c.humedad_max','c.observaciones','c.created_at','e.nombre_comun','e.nombre_cientifico']);

        $this->restringirPorEspecie($query,$user,'e.id');

        return $query;
    }

    private function queryEspeciesPermitidas(User $user): Builder
    {
        $query=DB::table('especies as e')->select(['e.id','e.nombre_comun','e.nombre_cientifico']);

        $this->restringirPorEspecie($query,$user,'e.id');

        return $query;
    }

    private function restringirPorEspecie(Builder $query,User $user,string $columnaEspecie): void
    {
        if($user->isSuperAdmin()) return;

        if(!$user->isEncargado()){
            $query->whereRaw('1 = 0');
            return;
        }

        $hoy=now($this->timezone)->toDateString();

        $query->whereExists(function($subquery) use($user,$hoy,$columnaEspecie){
            $subquery->selectRaw('1')->from('lotes as ls')->join('posiciones_incubadora as ps','ps.id','=','ls.posicion_incubadora_id')->join('asignaciones_incubadora as ais','ais.incubadora_id','=','ps.incubadora_id')->whereColumn('ls.especie_id',$columnaEspecie)->where('ais.user_id',$user->id)->whereDate('ais.fecha_inicio','<=',$hoy)->where(function($q) use($hoy){$q->whereNull('ais.fecha_fin')->orWhereDate('ais.fecha_fin','>=',$hoy);});
        });
    }

    private function mapear(object $item): array
    {
        return ['id'=>(int)$item->id,'temperatura_min'=>$item->temperatura_min!==null?(float)$item->temperatura_min:null,'temperatura_max'=>$item->temperatura_max!==null?(float)$item->temperatura_max:null,'humedad_min'=>$item->humedad_min!==null?(float)$item->humedad_min:null,'humedad_max'=>$item->humedad_max!==null?(float)$item->humedad_max:null,'observaciones'=>$item->observaciones,'created_at'=>$item->created_at];
    }
}
