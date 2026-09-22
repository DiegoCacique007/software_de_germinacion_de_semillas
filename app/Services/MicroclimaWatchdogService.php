<?php

namespace App\Services;

use App\Models\LecturaMicroclima;
use Illuminate\Support\Facades\Log;

class MicroclimaWatchdogService
{
    private int $minutosMaximosSinLectura=2;
    private array $actuadoresAmbientales=['niebla','calefaccion','ventilacion'];

    public function __construct(private MicroclimaActuatorService $actuatorService){}

    public function verificar(): array
    {
        $activos=$this->actuadoresActivos();

        if(empty($activos)){
            return [
                'ok'=>true,
                'accion'=>null,
                'motivo'=>'Los actuadores ambientales ya están apagados.'
            ];
        }

        $ultimaLectura=LecturaMicroclima::orderByDesc('fecha_hora')->first();

        if(!$ultimaLectura){
            return $this->apagarPorSeguridad(
                'No existen lecturas de microclima registradas.',
                $activos
            );
        }

        $limite=now('America/Mexico_City')->subMinutes($this->minutosMaximosSinLectura);

        if($ultimaLectura->fecha_hora->lt($limite)){
            return $this->apagarPorSeguridad(
                'No se han recibido lecturas recientes del sensor.',
                $activos
            );
        }

        return [
            'ok'=>true,
            'accion'=>null,
            'motivo'=>'Sensor comunicándose correctamente.',
            'actuadores_activos'=>$activos,
            'ultima_lectura'=>$ultimaLectura->fecha_hora->format('Y-m-d H:i:s')
        ];
    }

    private function actuadoresActivos(): array
    {
        $activos=[];

        foreach($this->actuadoresAmbientales as $actuador){
            $estado=$this->actuatorService->obtenerActuador($actuador);

            if(($estado['comando']??'apagar')==='encender'){
                $activos[]=$actuador;
            }
        }

        return $activos;
    }

    private function apagarPorSeguridad(string $motivo,array $activos): array
    {
        foreach($this->actuadoresAmbientales as $actuador){
            $estado=$this->actuatorService->obtenerActuador($actuador);

            if(($estado['comando']??'apagar')!=='apagar'){
                $this->actuatorService->actualizarActuador($actuador,'apagar');
            }
        }

        Log::warning('Watchdog de microclima activado',[
            'accion'=>'estado_seguro',
            'actuadores_apagados'=>$activos,
            'motivo'=>$motivo
        ]);

        return [
            'ok'=>true,
            'accion'=>'apagar',
            'seguridad'=>true,
            'actuadores_apagados'=>$activos,
            'motivo'=>$motivo
        ];
    }
}
