<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LecturaMicroclima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SensorLecturaController extends Controller
{
    public function store(Request $request)
    {
        $token = $request->header('X-SENSOR-TOKEN');

        if (!$token || $token !== env('SENSOR_API_TOKEN')) {
            return response()->json([
                'ok' => false,
                'message' => 'Token inválido.',
            ], 401);
        }

        $validated = $request->validate([
            'incubadora_id' => ['nullable', 'integer'],
            'temperatura' => ['required', 'numeric', 'between:-20,80'],
            'humedad' => ['required', 'numeric', 'between:0,100'],
        ]);

        $incubadoraId = $validated['incubadora_id'] ?? 106;

        $existeIncubadora = DB::table('incubadoras')
            ->where('id', $incubadoraId)
            ->exists();

        if (!$existeIncubadora) {
            return response()->json([
                'ok' => false,
                'message' => 'La incubadora indicada no existe.',
                'incubadora_id' => $incubadoraId,
            ], 404);
        }

        try {
            $lectura = LecturaMicroclima::create([
                'incubadora_id' => $incubadoraId,
                'fecha_hora' => now('America/Mexico_City')->format('Y-m-d H:i:s'),
                'temperatura' => $validated['temperatura'],
                'humedad' => $validated['humedad'],
                'observaciones' => 'Lectura enviada por ESP32-WROOM-32 con sensor DHT22.',
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Lectura registrada correctamente.',
                'data' => $lectura,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error al registrar lectura de microclima desde ESP32', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Error interno al registrar la lectura.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
