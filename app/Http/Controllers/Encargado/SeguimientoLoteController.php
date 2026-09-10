<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\EtapaDesarrollo;
use App\Models\Lote;
use App\Models\SeguimientoLote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SeguimientoLoteController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $seguimientos = SeguimientoLote::with([
            'lote.posicion.incubadora',
            'lote.especie',
            'etapa',
            'user',
        ])
            ->deEncargado($userId)
            ->latest('fecha_revision')
            ->latest('id')
            ->get();

        $lotes = Lote::with([
            'posicion.incubadora',
            'especie',
        ])
            ->withCount('frascos')
            ->withSum('frascos', 'cantidad_semillas')
            ->deEncargado($userId)
            ->orderBy('codigo_lote')
            ->get();

        $lotesOptions = $lotes->map(fn($lote) => [
            'id' => $lote->id,
            'etiqueta' => $lote->codigo_lote
                .' - '.($lote->especie?->nombre_comun ?? 'Sin especie')
                .' - '.($lote->posicion?->incubadora?->nombre ?? 'Sin incubadora'),
        ])->values();

        $etapas = EtapaDesarrollo::orderBy('nombre')->get();

        return view(
            'vistas_principales.encargado.seguimientos_lote.index',
            compact('seguimientos', 'lotesOptions', 'etapas')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = auth()->id();

        $data = $request->validate([
            'lote_id' => ['required', 'integer', 'exists:lotes,id'],
            'fecha_revision' => ['required', 'date', 'before_or_equal:today'],
            'frascos_activos' => ['required', 'integer', 'min:0'],
            'semillas_germinadas' => ['required', 'integer', 'min:0'],
            'altura_promedio_cm' => ['nullable', 'numeric', 'min:0'],
            'etapa_desarrollo_id' => ['required', 'integer', 'exists:etapas_desarrollo,id'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $lote = Lote::with('frascos')
            ->deEncargado($userId)
            ->findOrFail($data['lote_id']);

        $totalFrascos = $lote->frascos->count();
        $totalSemillas = $lote->frascos->sum('cantidad_semillas');

        if ($totalFrascos === 0) {
            throw ValidationException::withMessages([
                'lote_id' => 'El lote seleccionado todavía no tiene frascos registrados.',
            ]);
        }

        if ($data['frascos_activos'] > $totalFrascos) {
            throw ValidationException::withMessages([
                'frascos_activos' => 'El lote solamente tiene '.$totalFrascos.' frascos registrados.',
            ]);
        }

        if ($data['semillas_germinadas'] > $totalSemillas) {
            throw ValidationException::withMessages([
                'semillas_germinadas' => 'Las semillas germinadas no pueden superar las '.$totalSemillas.' semillas registradas en el lote.',
            ]);
        }

        $porcentajeGerminacion = $totalSemillas > 0
            ? round(($data['semillas_germinadas'] / $totalSemillas) * 100, 2)
            : 0;

        SeguimientoLote::create([
            'lote_id' => $lote->id,
            'fecha_revision' => $data['fecha_revision'],
            'frascos_activos' => $data['frascos_activos'],
            'semillas_germinadas' => $data['semillas_germinadas'],
            'porcentaje_germinacion' => $porcentajeGerminacion,
            'altura_promedio_cm' => $data['altura_promedio_cm'] ?? null,
            'etapa_desarrollo_id' => $data['etapa_desarrollo_id'],
            'observaciones' => $data['observaciones'] ?? null,
            'user_id' => $userId,
        ]);

        return redirect()
            ->route('encargado.seguimientos-lote.index')
            ->with('success', 'Seguimiento del lote registrado correctamente.');
    }
}
