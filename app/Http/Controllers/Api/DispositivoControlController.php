<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ControlIncubadora;
use App\Models\OrdenControlIncubadora;
use Illuminate\Http\Request;

class DispositivoControlController extends Controller
{
    public function pendientes(Request $request, $incubadora)
    {
        $token = $request->header('X-SENSOR-TOKEN');

        if (!$token || $token !== env('SENSOR_API_TOKEN')) {
            return response()->json([
                'ok' => false,
                'message' => 'Token inválido.',
            ], 401);
        }

        $orden = OrdenControlIncubadora::with(['tipo', 'modo'])
            ->where('incubadora_id', $incubadora)
            ->where('estado_orden', 'pendiente')
            ->orderBy('fecha_solicitud')
            ->first();

        if (!$orden) {
            return response()->json([
                'ok' => true,
                'pendiente' => false,
                'message' => 'No hay órdenes pendientes.',
            ]);
        }

        return response()->json([
            'ok' => true,
            'pendiente' => true,
            'orden' => [
                'id' => $orden->id,
                'incubadora_id' => $orden->incubadora_id,
                'tipo_control' => $orden->tipo->clave ?? $orden->tipo->nombre,
                'modo_control' => $orden->modo->clave ?? $orden->modo->nombre,
                'accion' => $orden->accion,
                'valor_aplicado' => $orden->valor_aplicado,
                'fecha_solicitud' => optional($orden->fecha_solicitud)->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function confirmar(Request $request, $orden)
    {
        $token = $request->header('X-SENSOR-TOKEN');

        if (!$token || $token !== env('SENSOR_API_TOKEN')) {
            return response()->json([
                'ok' => false,
                'message' => 'Token inválido.',
            ], 401);
        }

        $request->validate([
            'resultado' => 'required|in:aplicada,fallida',
            'respuesta_dispositivo' => 'nullable|string',
        ]);

        $orden = OrdenControlIncubadora::findOrFail($orden);

        $orden->update([
            'estado_orden' => $request->resultado,
            'respuesta_dispositivo' => $request->respuesta_dispositivo,
            'fecha_aplicacion' => now(),
        ]);

        if ($request->resultado === 'aplicada') {
            ControlIncubadora::create([
                'incubadora_id' => $orden->incubadora_id,
                'tipo_control_incubadora_id' => $orden->tipo_control_incubadora_id,
                'modo_control_incubadora_id' => $orden->modo_control_incubadora_id,
                'valor_aplicado' => $orden->valor_aplicado,
                'fecha_hora' => now(),
                'user_id' => $orden->solicitada_por,
                'observaciones' => 'Aplicado por dispositivo. Acción: '.$orden->accion,
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Resultado de orden registrado.',
        ]);
    }
}
