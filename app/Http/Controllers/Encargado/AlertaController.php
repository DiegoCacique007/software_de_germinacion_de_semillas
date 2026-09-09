<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\EstadoAlerta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertaController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $alertas = Alerta::with([
            'incubadora',
            'tipo',
            'nivel',
            'estado',
            'atendidaPor',
        ])
            ->deEncargado($user->id)
            ->latest('fecha_hora')
            ->get();

        $estados = EstadoAlerta::orderBy('nombre')->get();

        return view(
            'vistas_principales.encargado.alertas.index',
            compact('alertas', 'estados')
        );
    }

    public function update(Request $request, int $alerta): RedirectResponse
    {
        $user = auth()->user();

        $alerta = Alerta::deEncargado($user->id)
            ->findOrFail($alerta);

        $data = $request->validate([
            'estado_alerta_id' => [
                'required',
                'integer',
                'exists:estados_alerta,id',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $alerta->update([
            'estado_alerta_id' => $data['estado_alerta_id'],
            'observaciones' => $data['observaciones'] ?? null,
            'atendida_por' => $user->id,
        ]);

        return redirect()
            ->route('encargado.alertas.index')
            ->with('success', 'La alerta fue actualizada correctamente.');
    }
}
