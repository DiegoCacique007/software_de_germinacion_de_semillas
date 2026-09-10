<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\EstadoFrasco;
use App\Models\Frasco;
use App\Models\SeguimientoFrasco;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SeguimientoFrascoController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $seguimientos = SeguimientoFrasco::with([
            'frasco.lote.posicion.incubadora',
            'frasco.lote.especie',
            'estado',
            'user',
        ])
            ->deEncargado($userId)
            ->latest('fecha_revision')
            ->latest('id')
            ->get();

        $frascos = Frasco::with('lote')
            ->deEncargado($userId)
            ->orderBy('lote_id')
            ->orderBy('numero_frasco')
            ->get();

        $frascosOptions = $frascos->map(fn($frasco) => [
            'id' => $frasco->id,
            'etiqueta' => ($frasco->lote?->codigo_lote ?? 'Sin lote').' - Frasco '.$frasco->numero_frasco,
        ])->values();

        $estados = EstadoFrasco::orderBy('nombre')->get();

        return view(
            'vistas_principales.encargado.seguimientos_frasco.index',
            compact('seguimientos', 'frascosOptions', 'estados')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = auth()->id();

        $data = $request->validate([
            'frasco_id' => ['required', 'integer', 'exists:frascos,id'],
            'fecha_revision' => ['required', 'date', 'before_or_equal:today'],
            'semillas_germinadas' => ['required', 'integer', 'min:0'],
            'altura_promedio_cm' => ['nullable', 'numeric', 'min:0'],
            'estado_frasco_id' => ['required', 'integer', 'exists:estados_frasco,id'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $frasco = Frasco::deEncargado($userId)->findOrFail($data['frasco_id']);

        if ($data['semillas_germinadas'] > $frasco->cantidad_semillas) {
            throw ValidationException::withMessages([
                'semillas_germinadas' => 'Las semillas germinadas no pueden superar las '.$frasco->cantidad_semillas.' semillas registradas en este frasco.',
            ]);
        }

        DB::transaction(function () use ($data, $frasco, $userId) {
            SeguimientoFrasco::create([
                'frasco_id' => $frasco->id,
                'fecha_revision' => $data['fecha_revision'],
                'semillas_germinadas' => $data['semillas_germinadas'],
                'altura_promedio_cm' => $data['altura_promedio_cm'] ?? null,
                'estado_frasco_id' => $data['estado_frasco_id'],
                'observaciones' => $data['observaciones'] ?? null,
                'user_id' => $userId,
            ]);

            $frasco->update([
                'estado_frasco_id' => $data['estado_frasco_id'],
            ]);
        });

        return redirect()
            ->route('encargado.seguimientos-frasco.index')
            ->with('success', 'Seguimiento del frasco registrado correctamente.');
    }
}
