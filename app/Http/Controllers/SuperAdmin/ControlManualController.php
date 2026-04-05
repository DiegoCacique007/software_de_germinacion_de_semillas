<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\OrdenControlIncubadora;
use Illuminate\Http\Request;

class ControlManualController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_control_incubadora_id' => 'required|exists:tipos_control_incubadora,id',
            'modo_control_incubadora_id' => 'required|exists:modos_control_incubadora,id',
            'accion' => 'required|in:encender,apagar,ajustar',
            'valor_aplicado' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        OrdenControlIncubadora::create([
            'incubadora_id' => $request->incubadora_id,
            'tipo_control_incubadora_id' => $request->tipo_control_incubadora_id,
            'modo_control_incubadora_id' => $request->modo_control_incubadora_id,
            'accion' => $request->accion,
            'valor_aplicado' => $request->valor_aplicado,
            'estado_orden' => 'pendiente',
            'solicitada_por' => auth()->id(),
            'fecha_solicitud' => now(),
        ]);

        return back()->with('success', 'Orden manual enviada correctamente.');
    }
}
