<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MicroclimaActuadorController extends Controller
{
    private string $archivo = 'microseed_actuadores.json';

    public function show(Request $request, string $actuador)
    {
        if (!$this->tokenValido($request)) {
            return response()->json([
                'ok' => false,
                'message' => 'Token inválido.',
            ], 401);
        }

        if (!$this->actuadorValido($actuador)) {
            return response()->json([
                'ok' => false,
                'message' => 'Actuador no válido.',
            ], 404);
        }

        $estados = $this->leerEstados();

        return response()->json([
            'ok' => true,
            'actuador' => $actuador,
            'comando' => $estados[$actuador]['comando'] ?? 'apagar',
            'actualizado_en' => $estados[$actuador]['actualizado_en'] ?? null,
        ]);
    }

    public function update(Request $request, string $actuador)
    {
        if (!$this->actuadorValido($actuador)) {
            return response()->json([
                'ok' => false,
                'message' => 'Actuador no válido.',
            ], 404);
        }

        $validated = $request->validate([
            'accion' => ['required', 'in:encender,apagar'],
        ]);

        $estados = $this->leerEstados();

        $estados[$actuador] = [
            'comando' => $validated['accion'],
            'actualizado_en' => now('America/Mexico_City')->format('Y-m-d H:i:s'),
            'actualizado_por' => auth()->id(),
        ];

        Storage::disk('local')->put(
            $this->archivo,
            json_encode($estados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return response()->json([
            'ok' => true,
            'message' => 'Orden enviada correctamente.',
            'actuador' => $actuador,
            'comando' => $validated['accion'],
        ]);
    }

    private function leerEstados(): array
    {
        $default = [
            'niebla' => [
                'comando' => 'apagar',
                'actualizado_en' => null,
                'actualizado_por' => null,
            ],
            'luz' => [
                'comando' => 'apagar',
                'actualizado_en' => null,
                'actualizado_por' => null,
            ],
        ];

        if (!Storage::disk('local')->exists($this->archivo)) {
            Storage::disk('local')->put(
                $this->archivo,
                json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            return $default;
        }

        $contenido = Storage::disk('local')->get($this->archivo);
        $estados = json_decode($contenido, true);

        if (!is_array($estados)) {
            return $default;
        }

        return array_replace_recursive($default, $estados);
    }

    private function actuadorValido(string $actuador): bool
    {
        return in_array($actuador, ['niebla', 'luz'], true);
    }

    private function tokenValido(Request $request): bool
    {
        $token = $request->header('X-SENSOR-TOKEN');

        return $token && $token === env('SENSOR_API_TOKEN');
    }
}
