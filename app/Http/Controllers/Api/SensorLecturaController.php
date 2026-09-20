<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LecturaMicroclima;
use App\Services\MicroclimaAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SensorLecturaController extends Controller
{
    public function store(Request $request, MicroclimaAlertService $alertService)
    {
        $token = $request->header('X-SENSOR-TOKEN');
        $sensorToken = config('services.sensor.token');

        if (!is_string($token) || $token === '' || !is_string($sensorToken) || $sensorToken === '' || !hash_equals($sensorToken, $token)) {
            return response()->json([
                'ok' => false,
                'message' => 'Token inválido.',
            ], 401);
        }

        $validated = $request->validate([
            'incubadora_id' => ['required', 'integer', 'exists:incubadoras,id'],
            'temperatura' => ['required', 'numeric', 'between:-20,80'],
            'humedad' => ['required', 'numeric', 'between:0,100'],
        ]);

        try {
            $lectura = LecturaMicroclima::create([
                'incubadora_id' => $validated['incubadora_id'],
                'fecha_hora' => now('America/Mexico_City')->format('Y-m-d H:i:s'),
                'temperatura' => $validated['temperatura'],
                'humedad' => $validated['humedad'],
                'observaciones' => 'Lectura enviada por ESP32-WROOM-32 con sensor DHT22.',
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al registrar lectura de microclima desde ESP32', [
                'error' => $e->getMessage(),
                'incubadora_id' => $validated['incubadora_id'],
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Error interno al registrar la lectura.',
            ], 500);
        }

        $alertas = ['evaluadas' => false, 'generadas' => 0];
        try {
            $alertas['generadas'] = $alertService->evaluarLectura($lectura);
            $alertas['evaluadas'] = true;
        } catch (\Throwable $e) {
            Log::error('Error al evaluar alertas de microclima', [
                'lectura_microclima_id' => $lectura->id,
                'incubadora_id' => $lectura->incubadora_id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Lectura registrada correctamente.',
            'data' => $lectura,
            'alertas' => $alertas,
        ], 201);
    }
}
