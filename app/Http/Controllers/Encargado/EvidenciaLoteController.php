<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\EvidenciaLote;
use App\Models\SeguimientoLote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class EvidenciaLoteController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $evidencias = EvidenciaLote::with([
            'seguimiento.lote.posicion.incubadora',
            'seguimiento.lote.especie',
            'seguimiento.user',
        ])
            ->deEncargado($userId)
            ->latest()
            ->get();

        $seguimientos = SeguimientoLote::with([
            'lote.posicion.incubadora',
            'lote.especie',
        ])
            ->deEncargado($userId)
            ->latest('fecha_revision')
            ->get();

        $seguimientosOptions = $seguimientos->map(fn($seguimiento) => [
            'id' => $seguimiento->id,
            'etiqueta' =>
                ($seguimiento->lote?->codigo_lote ?? 'Sin lote')
                .' - '.
                ($seguimiento->fecha_revision?->format('d/m/Y') ?? 'Sin fecha')
                .' - '.
                ($seguimiento->lote?->posicion?->incubadora?->nombre ?? 'Sin incubadora'),
        ])->values();

        return view(
            'vistas_principales.encargado.evidencias_lote.index',
            compact('evidencias', 'seguimientosOptions')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = auth()->id();

        $data = $request->validate([
            'seguimiento_lote_id' => ['required', 'integer', 'exists:seguimientos_lote,id'],
            'archivo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $seguimiento = SeguimientoLote::deEncargado($userId)
            ->findOrFail($data['seguimiento_lote_id']);

        $ruta = $request->file('archivo')->store(
            'evidencias/lotes',
            'public'
        );

        try {
            EvidenciaLote::create([
                'seguimiento_lote_id' => $seguimiento->id,
                'archivo' => $ruta,
                'descripcion' => $data['descripcion'] ?? null,
            ]);
        } catch (Throwable $e) {
            Storage::disk('public')->delete($ruta);
            throw $e;
        }

        return redirect()
            ->route('encargado.evidencias-lote.index')
            ->with('success', 'Evidencia registrada correctamente.');
    }
}
