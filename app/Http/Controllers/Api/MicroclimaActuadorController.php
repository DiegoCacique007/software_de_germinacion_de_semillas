<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MicroclimaActuatorService;
use Illuminate\Http\Request;

class MicroclimaActuadorController extends Controller
{
    public function __construct(
        private MicroclimaActuatorService $actuatorService
    ){}

    public function show(Request $request,string $actuador)
    {
        if(!$this->tokenValido($request)){
            return response()->json([
                'ok'=>false,
                'message'=>'Token inválido.',
            ],401);
        }

        if(!$this->actuatorService->actuadorValido($actuador)){
            return response()->json([
                'ok'=>false,
                'message'=>'Actuador no válido.',
            ],404);
        }

        $estado=$this->actuatorService->obtenerActuador($actuador);

        return response()->json([
            'ok'=>true,
            'actuador'=>$actuador,
            'comando'=>$estado['comando'],
            'actualizado_en'=>$estado['actualizado_en'],
        ]);
    }

    public function update(Request $request,string $actuador)
    {
        if(!$this->actuatorService->actuadorValido($actuador)){
            return response()->json([
                'ok'=>false,
                'message'=>'Actuador no válido.',
            ],404);
        }

        $validated=$request->validate([
            'accion'=>['required','in:encender,apagar'],
        ]);

        $estado=$this->actuatorService->actualizarActuador(
            $actuador,
            $validated['accion'],
            auth()->id()
        );

        return response()->json([
            'ok'=>true,
            'message'=>'Orden enviada correctamente.',
            'actuador'=>$actuador,
            'comando'=>$estado['comando'],
        ]);
    }

    public function modo()
    {
        $estado=$this->actuatorService->obtenerModo();

        return response()->json([
            'ok'=>true,
            'modo'=>$estado['valor'],
            'actualizado_en'=>$estado['actualizado_en'],
        ]);
    }

    public function updateModo(Request $request)
    {
        $validated=$request->validate([
            'modo'=>['required','in:automatico,manual'],
        ]);

        $estado=$this->actuatorService->actualizarModo(
            $validated['modo'],
            auth()->id()
        );

        return response()->json([
            'ok'=>true,
            'message'=>'Modo de operación actualizado correctamente.',
            'modo'=>$estado['valor'],
        ]);
    }

    private function tokenValido(Request $request): bool
    {
        $token=(string)$request->header('X-SENSOR-TOKEN');
        $sensorToken=(string)config('services.sensor.token');

        return $token!=='' &&
            $sensorToken!=='' &&
            hash_equals($sensorToken,$token);
    }
}
