<?php

namespace App\Services;

use App\Models\CondicionOptimaEspecie;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use Illuminate\Support\Facades\Log;

class MicroclimaAutomationService
{
    private float $margenEncendido=1.0;
    private float $margenApagado=2.0;

    public function __construct(private MicroclimaActuatorService $actuatorService){}

    public function procesar(LecturaMicroclima $lectura): array
    {
        $modo=$this->actuatorService->obtenerModo();

        if(($modo['valor']??'automatico')!=='automatico'){
            return [
                'evaluado'=>true,
                'modo'=>'manual',
                'niebla'=>null,
                'motivo'=>'Control manual activo.',
            ];
        }

        $especies=Lote::whereHas(
            'posicion',
            fn($q)=>$q->where('incubadora_id',$lectura->incubadora_id)
        )->pluck('especie_id')->unique()->values();

        if($especies->isEmpty()){
            return $this->estadoSeguro(
                $lectura,
                'La incubadora no tiene lotes activos.'
            );
        }

        $condiciones=CondicionOptimaEspecie::whereIn(
            'especie_id',
            $especies
        )->get();

        if($condiciones->count()!==$especies->count()){
            return $this->estadoSeguro(
                $lectura,
                'Faltan condiciones óptimas para uno o más lotes.'
            );
        }

        $humedadMin=(float)$condiciones->max('humedad_min');
        $humedadMax=(float)$condiciones->min('humedad_max');

        if($humedadMin>$humedadMax){
            Log::warning('Rangos de humedad incompatibles en incubadora',[
                'incubadora_id'=>$lectura->incubadora_id,
                'humedad_min'=>$humedadMin,
                'humedad_max'=>$humedadMax,
            ]);

            return $this->estadoSeguro(
                $lectura,
                'Los lotes tienen rangos de humedad incompatibles.'
            );
        }

        $humedad=(float)$lectura->humedad;

        if($humedad<0||$humedad>100){
            return $this->estadoSeguro(
                $lectura,
                'Lectura de humedad inválida.'
            );
        }

        $umbralEncendido=max(0,$humedadMin-$this->margenEncendido);
        $umbralApagado=min($humedadMax,$humedadMin+$this->margenApagado);

        $estadoActual=$this->actuatorService->obtenerActuador('niebla');
        $comandoActual=$estadoActual['comando']??'apagar';

        if($humedad<$umbralEncendido){
            $accion='encender';
            $motivo='Humedad por debajo del umbral de encendido.';
        }elseif($humedad>=$umbralApagado){
            $accion='apagar';
            $motivo='Humedad recuperada; se alcanzó el umbral de apagado.';
        }else{
            $accion=$comandoActual;
            $motivo='Humedad dentro de la banda de histéresis; se conserva el estado anterior.';
        }

        $this->aplicarSiCambio($accion);

        return [
            'evaluado'=>true,
            'modo'=>'automatico',
            'niebla'=>$accion,
            'humedad'=>$humedad,
            'humedad_min'=>$humedadMin,
            'humedad_max'=>$humedadMax,
            'umbral_encendido'=>$umbralEncendido,
            'umbral_apagado'=>$umbralApagado,
            'histeresis'=>true,
            'motivo'=>$motivo,
        ];
    }

    private function aplicarSiCambio(string $accion): void
    {
        $estado=$this->actuatorService->obtenerActuador('niebla');
        $actual=$estado['comando']??'apagar';

        if($actual!==$accion){
            $this->actuatorService->actualizarActuador(
                'niebla',
                $accion
            );
        }
    }

    private function estadoSeguro(
        LecturaMicroclima $lectura,
        string $motivo
    ): array{
        $this->aplicarSiCambio('apagar');

        Log::warning('Automatización de microclima en estado seguro',[
            'incubadora_id'=>$lectura->incubadora_id,
            'lectura_microclima_id'=>$lectura->id,
            'motivo'=>$motivo,
        ]);

        return [
            'evaluado'=>false,
            'modo'=>'automatico',
            'niebla'=>'apagar',
            'seguridad'=>true,
            'motivo'=>$motivo,
        ];
    }
}
