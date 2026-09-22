<?php

namespace App\Services;

use App\Models\CondicionOptimaEspecie;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use Illuminate\Support\Facades\Log;

class MicroclimaAutomationService
{
    public function __construct(private MicroclimaActuatorService $actuatorService){}

    public function procesar(LecturaMicroclima $lectura): array
    {
        $modo=$this->actuatorService->obtenerModo();

        if(($modo['valor']??'automatico')!=='automatico'){
            return ['evaluado'=>true,'modo'=>'manual','niebla'=>null,'motivo'=>'Control manual activo.'];
        }

        $especies=Lote::whereHas('posicion',fn($q)=>$q->where('incubadora_id',$lectura->incubadora_id))
            ->pluck('especie_id')
            ->unique()
            ->values();

        if($especies->isEmpty()){
            return ['evaluado'=>false,'modo'=>'automatico','niebla'=>null,'motivo'=>'La incubadora no tiene lotes.'];
        }

        $condiciones=CondicionOptimaEspecie::whereIn('especie_id',$especies)->get();

        if($condiciones->count()!==$especies->count()){
            return ['evaluado'=>false,'modo'=>'automatico','niebla'=>null,'motivo'=>'Faltan condiciones óptimas para uno o más lotes.'];
        }

        $humedadMin=(float)$condiciones->max('humedad_min');
        $humedadMax=(float)$condiciones->min('humedad_max');

        if($humedadMin>$humedadMax){
            Log::warning('Rangos de humedad incompatibles en incubadora',[
                'incubadora_id'=>$lectura->incubadora_id,
                'humedad_min'=>$humedadMin,
                'humedad_max'=>$humedadMax,
            ]);

            return ['evaluado'=>false,'modo'=>'automatico','niebla'=>null,'motivo'=>'Los lotes tienen rangos de humedad incompatibles.'];
        }

        $humedad=(float)$lectura->humedad;
        $accion=$humedad<$humedadMin?'encender':'apagar';

        $this->actuatorService->actualizarActuador('niebla',$accion);

        return [
            'evaluado'=>true,
            'modo'=>'automatico',
            'niebla'=>$accion,
            'humedad'=>$humedad,
            'humedad_min'=>$humedadMin,
            'humedad_max'=>$humedadMax,
        ];
    }
}
