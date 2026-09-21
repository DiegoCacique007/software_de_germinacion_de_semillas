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
            'lote',
            'lecturaMicroclima',
            'tipo',
            'nivel',
            'estado',
            'atendidaPor',
        ])
            ->deEncargado($user->id)
            ->latest('fecha_hora')
            ->get();

        $estados = EstadoAlerta::whereIn('clave', [
            'pendiente',
            'atendida',
            'resuelta',
        ])
            ->orderBy('nombre')
            ->get();

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

        $estado = EstadoAlerta::whereIn('clave', [
            'pendiente',
            'atendida',
            'resuelta',
        ])
            ->findOrFail($data['estado_alerta_id']);

        $actualizacion = [
            'estado_alerta_id' => $estado->id,
            'observaciones' => $data['observaciones'] ?? null,
        ];

        if ($estado->clave === 'pendiente') {
            $actualizacion['atendida_por'] = null;
            $actualizacion['fecha_atencion'] = null;
            $actualizacion['fecha_resolucion'] = null;
        }

        if ($estado->clave === 'atendida') {
            $actualizacion['atendida_por'] = $user->id;
            $actualizacion['fecha_atencion'] =
                $alerta->fecha_atencion ?? now('America/Mexico_City');

            $actualizacion['fecha_resolucion'] = null;
        }

        if ($estado->clave === 'resuelta') {
            $actualizacion['atendida_por'] =
                $alerta->atendida_por ?? $user->id;

            $actualizacion['fecha_atencion'] =
                $alerta->fecha_atencion ?? now('America/Mexico_City');

            $actualizacion['fecha_resolucion'] =
                now('America/Mexico_City');
        }

        $alerta->update($actualizacion);

        return redirect()
            ->route('encargado.alertas.index')
            ->with(
                'success',
                'La alerta fue actualizada correctamente.'
            );
    }

    public function atender(int $alerta): RedirectResponse
    {
        $user = auth()->user();

        $alerta = Alerta::with('estado')
            ->deEncargado($user->id)
            ->findOrFail($alerta);

        if ($alerta->estado?->clave === 'resuelta') {
            return redirect()
                ->route('encargado.alertas.index')
                ->with(
                    'info',
                    'La alerta ya se encuentra resuelta.'
                );
        }

        if ($alerta->estado?->clave === 'atendida') {
            return redirect()
                ->route('encargado.alertas.index')
                ->with(
                    'info',
                    'La alerta ya fue marcada como atendida.'
                );
        }

        $estado = EstadoAlerta::where(
            'clave',
            'atendida'
        )->firstOrFail();

        $alerta->update([
            'estado_alerta_id' => $estado->id,
            'atendida_por' => $user->id,
            'fecha_atencion' =>
                $alerta->fecha_atencion
                ?? now('America/Mexico_City'),
            'fecha_resolucion' => null,
        ]);

        return redirect()
            ->route('encargado.alertas.index')
            ->with(
                'success',
                'La alerta fue marcada como atendida.'
            );
    }

    public function resolver(int $alerta): RedirectResponse
    {
        $user = auth()->user();

        $alerta = Alerta::with('estado')
            ->deEncargado($user->id)
            ->findOrFail($alerta);

        /*
        |--------------------------------------------------------------------------
        | Ya está resuelta
        |--------------------------------------------------------------------------
        */

        if ($alerta->estado?->clave === 'resuelta') {
            return redirect()
                ->route('encargado.alertas.index')
                ->with(
                    'info',
                    'La alerta ya se encuentra resuelta.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | No permitir Pendiente -> Resuelta
        |--------------------------------------------------------------------------
        */

        if ($alerta->estado?->clave === 'pendiente') {
            return redirect()
                ->route('encargado.alertas.index')
                ->with(
                    'warning',
                    'Primero debes marcar la alerta como atendida.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolver alerta atendida
        |--------------------------------------------------------------------------
        */

        $estado = EstadoAlerta::where(
            'clave',
            'resuelta'
        )->firstOrFail();

        $alerta->update([
            'estado_alerta_id' => $estado->id,
            'atendida_por' =>
                $alerta->atendida_por ?? $user->id,

            'fecha_atencion' =>
                $alerta->fecha_atencion
                ?? now('America/Mexico_City'),

            'fecha_resolucion' =>
                now('America/Mexico_City'),
        ]);

        return redirect()
            ->route('encargado.alertas.index')
            ->with(
                'success',
                'La alerta fue resuelta correctamente.'
            );
    }
}
