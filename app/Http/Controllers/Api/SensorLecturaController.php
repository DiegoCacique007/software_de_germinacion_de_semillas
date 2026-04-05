<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incubadora;
use App\Models\LecturaMicroclima;
use App\Services\MicroclimaAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SensorLecturaController extends Controller
{
    public function store(Request $request, MicroclimaAlertService $alertService)
    {
        $token = $request->header('X-SENSOR-TOKEN');

        if (!$token || $token !== env('SENSOR_API_TOKEN')) {
            return response()->json([
                'ok' => false,
                'message' => 'Token de sensor inválido.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'incubadora_id' => 'required|exists:incubadoras,id',
            'temperatura'   => 'required|numeric|min:-50|max:100',
            'humedad'       => 'required|numeric|min:0|max:100',
            'fecha_hora'    => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $incubadora = Incubadora::findOrFail($request->incubadora_id);

        $lectura = LecturaMicroclima::create([
            'incubadora_id' => $incubadora->id,
            'fecha_hora'    => $request->fecha_hora ?? now(),
            'temperatura'   => $request->temperatura,
            'humedad'       => $request->humedad,
            'observaciones' => $request->observaciones,
        ]);

        $alertasGeneradas = $alertService->evaluarLectura($lectura);

        return response()->json([
            'ok' => true,
            'message' => 'Lectura registrada correctamente.',
            'lectura_id' => $lectura->id,
            'alertas_generadas' => $alertasGeneradas,
        ], 201);
    }
}
