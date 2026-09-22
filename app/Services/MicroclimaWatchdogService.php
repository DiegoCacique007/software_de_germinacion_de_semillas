<?php

namespace App\Services;

use App\Models\LecturaMicroclima;
use Illuminate\Support\Facades\Log;

class MicroclimaWatchdogService
{
    private int $minutosMaximosSinLectura=2;

    public function __construct(private MicroclimaActuatorService $actuatorService){}

    public function verificar(): array
    {
        $modo=$this->actuatorService->obtenerModo();

        if(($modo['valor']??'automatico')!=='automatico'){
            return [
                'ok'=>true,
                'accion'=>null,
                'motivo'=>'Modo manual activo.',
            ];
        }

        $estadoNiebla=$this->actuatorService->obtenerActuador('niebla');

        if(($estadoNiebla['comando']??'apagar')!=='encender'){
            return [
                'ok'=>true,
                'accion'=>null,
                'motivo'=>'La niebla ya está apagada.',
            ];
        }

        $ultimaLectura=LecturaMicroclima::orderByDesc('fecha_hora')->first();

        if(!$ultimaLectura){
            return $this->apagarPorSeguridad(
                'No existen lecturas de microclima registradas.'
            );
        }

        $limite=now('America/Mexico_City')->subMinutes($this->minutosMaximosSinLectura);

        if($ultimaLectura->fecha_hora->lt($limite)){
            return $this->apagarPorSeguridad(
                'No se han recibido lecturas recientes del sensor.'
            );
        }

        return [
            'ok'=>true,
            'accion'=>null,
            'motivo'=>'Sensor comunicándose correctamente.',
            'ultima_lectura'=>$ultimaLectura->fecha_hora->format('Y-m-d H:i:s'),
        ];
    }

    private function apagarPorSeguridad(string $motivo): array
    {
        $this->actuatorService->actualizarActuador('niebla','apagar');

        Log::warning('Watchdog de microclima activado',[
            'accion'=>'apagar_niebla',
            'motivo'=>$motivo,
        ]);

        return [
            'ok'=>true,
            'accion'=>'apagar',
            'seguridad'=>true,
            'motivo'=>$motivo,
        ];
    }
}
