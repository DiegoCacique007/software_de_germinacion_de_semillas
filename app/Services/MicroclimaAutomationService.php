<?php

namespace App\Services;

use App\Models\CondicionOptimaEspecie;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use Illuminate\Support\Facades\Log;

class MicroclimaAutomationService
{
    private float $margenHumedadEncendido=1.0;
    private float $margenHumedadApagado=2.0;
    private float $margenTemperatura=1.0;

    public function __construct(private MicroclimaActuatorService $actuatorService){}

    public function procesar(LecturaMicroclima $lectura): array
    {
        $modo=$this->actuatorService->obtenerModo();

        if(($modo['valor']??'automatico')!=='automatico'){
            return [
                'evaluado'=>true,
                'modo'=>'manual',
                'niebla'=>null,
                'calefaccion'=>null,
                'ventilacion'=>null,
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
        $temperaturaMin=(float)$condiciones->max('temperatura_min');
        $temperaturaMax=(float)$condiciones->min('temperatura_max');

        if($humedadMin>$humedadMax||$temperaturaMin>$temperaturaMax){
            Log::warning('Rangos ambientales incompatibles en incubadora',[
                'incubadora_id'=>$lectura->incubadora_id,
                'humedad_min'=>$humedadMin,
                'humedad_max'=>$humedadMax,
                'temperatura_min'=>$temperaturaMin,
                'temperatura_max'=>$temperaturaMax,
            ]);

            return $this->estadoSeguro(
                $lectura,
                'Los lotes tienen rangos ambientales incompatibles.'
            );
        }

        $humedad=(float)$lectura->humedad;
        $temperatura=(float)$lectura->temperatura;

        if($humedad<0||$humedad>100||$temperatura<-20||$temperatura>80){
            return $this->estadoSeguro(
                $lectura,
                'Lectura ambiental inválida.'
            );
        }

        $controlHumedad=$this->controlarHumedad(
            $humedad,
            $humedadMin,
            $humedadMax
        );

        $controlTemperatura=$this->controlarTemperatura(
            $temperatura,
            $temperaturaMin,
            $temperaturaMax
        );

        return [
            'evaluado'=>true,
            'modo'=>'automatico',

            'niebla'=>$controlHumedad['accion'],
            'humedad'=>$humedad,
            'humedad_min'=>$humedadMin,
            'humedad_max'=>$humedadMax,
            'umbral_humedad_encendido'=>$controlHumedad['umbral_encendido'],
            'umbral_humedad_apagado'=>$controlHumedad['umbral_apagado'],
            'motivo_humedad'=>$controlHumedad['motivo'],

            'calefaccion'=>$controlTemperatura['calefaccion'],
            'ventilacion'=>$controlTemperatura['ventilacion'],
            'temperatura'=>$temperatura,
            'temperatura_min'=>$temperaturaMin,
            'temperatura_max'=>$temperaturaMax,
            'umbral_calefaccion_encendido'=>$controlTemperatura['calefaccion_encendido'],
            'umbral_calefaccion_apagado'=>$controlTemperatura['calefaccion_apagado'],
            'umbral_ventilacion_apagado'=>$controlTemperatura['ventilacion_apagado'],
            'umbral_ventilacion_encendido'=>$controlTemperatura['ventilacion_encendido'],
            'motivo_temperatura'=>$controlTemperatura['motivo'],

            'histeresis'=>true,
        ];
    }

    private function controlarHumedad(float $humedad,float $min,float $max): array
    {
        $umbralEncendido=max(0,$min-$this->margenHumedadEncendido);
        $umbralApagado=min($max,$min+$this->margenHumedadApagado);

        $estado=$this->actuatorService->obtenerActuador('niebla');
        $actual=$estado['comando']??'apagar';

        if($humedad<$umbralEncendido){
            $accion='encender';
            $motivo='Humedad por debajo del umbral de encendido.';
        }elseif($humedad>=$umbralApagado){
            $accion='apagar';
            $motivo='Humedad recuperada; se alcanzó el umbral de apagado.';
        }else{
            $accion=$actual;
            $motivo='Humedad dentro de la banda de histéresis; se conserva el estado anterior.';
        }

        $this->aplicarSiCambio('niebla',$accion);

        return [
            'accion'=>$accion,
            'umbral_encendido'=>$umbralEncendido,
            'umbral_apagado'=>$umbralApagado,
            'motivo'=>$motivo,
        ];
    }

    private function controlarTemperatura(float $temperatura,float $min,float $max): array
    {
        $calefaccionEncendido=$min-$this->margenTemperatura;
        $calefaccionApagado=$min+$this->margenTemperatura;
        $ventilacionApagado=$max-$this->margenTemperatura;
        $ventilacionEncendido=$max+$this->margenTemperatura;

        $estadoCalefaccion=$this->actuatorService->obtenerActuador('calefaccion');
        $estadoVentilacion=$this->actuatorService->obtenerActuador('ventilacion');

        $calefaccionActual=$estadoCalefaccion['comando']??'apagar';
        $ventilacionActual=$estadoVentilacion['comando']??'apagar';

        if($temperatura<$calefaccionEncendido){
            $calefaccion='encender';
            $ventilacion='apagar';
            $motivo='Temperatura baja; calefacción activada.';
        }elseif($temperatura<$calefaccionApagado){
            $calefaccion=$calefaccionActual;
            $ventilacion='apagar';
            $motivo='Temperatura en histéresis inferior; se conserva el estado de calefacción.';
        }elseif($temperatura<=$ventilacionApagado){
            $calefaccion='apagar';
            $ventilacion='apagar';
            $motivo='Temperatura dentro del rango estable.';
        }elseif($temperatura<=$ventilacionEncendido){
            $calefaccion='apagar';
            $ventilacion=$ventilacionActual;
            $motivo='Temperatura en histéresis superior; se conserva el estado de ventilación.';
        }else{
            $calefaccion='apagar';
            $ventilacion='encender';
            $motivo='Temperatura alta; ventilación activada.';
        }

        if($calefaccion==='encender'){
            $ventilacion='apagar';
        }

        if($ventilacion==='encender'){
            $calefaccion='apagar';
        }

        $this->aplicarSiCambio('calefaccion',$calefaccion);
        $this->aplicarSiCambio('ventilacion',$ventilacion);

        return [
            'calefaccion'=>$calefaccion,
            'ventilacion'=>$ventilacion,
            'calefaccion_encendido'=>$calefaccionEncendido,
            'calefaccion_apagado'=>$calefaccionApagado,
            'ventilacion_apagado'=>$ventilacionApagado,
            'ventilacion_encendido'=>$ventilacionEncendido,
            'motivo'=>$motivo,
        ];
    }

    private function aplicarSiCambio(string $actuador,string $accion): void
    {
        $estado=$this->actuatorService->obtenerActuador($actuador);
        $actual=$estado['comando']??'apagar';

        if($actual!==$accion){
            $this->actuatorService->actualizarActuador(
                $actuador,
                $accion
            );
        }
    }

    private function estadoSeguro(
        LecturaMicroclima $lectura,
        string $motivo
    ): array{
        $this->aplicarSiCambio('niebla','apagar');
        $this->aplicarSiCambio('calefaccion','apagar');
        $this->aplicarSiCambio('ventilacion','apagar');

        Log::warning('Automatización de microclima en estado seguro',[
            'incubadora_id'=>$lectura->incubadora_id,
            'lectura_microclima_id'=>$lectura->id,
            'motivo'=>$motivo,
        ]);

        return [
            'evaluado'=>false,
            'modo'=>'automatico',
            'niebla'=>'apagar',
            'calefaccion'=>'apagar',
            'ventilacion'=>'apagar',
            'seguridad'=>true,
            'motivo'=>$motivo,
        ];
    }
}
