<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\EvidenciaLote;
use App\Models\SeguimientoLote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenciaLoteController extends Controller
{
    public function index()
    {
        $evidencias = EvidenciaLote::with(['seguimiento.lote.especie'])
            ->orderBy('id', 'desc')
            ->get();

        $seguimientos = SeguimientoLote::with(['lote.especie'])
            ->orderBy('fecha_revision', 'desc')
            ->get();

        return view('vistas_principales.encargado.evidencias_lote.index', compact('evidencias', 'seguimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seguimiento_lote_id' => 'required|exists:seguimientos_lote,id',
            'archivo' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $rutaArchivo = $request->file('archivo')->store('evidencias_lote', 'public');

        EvidenciaLote::create([
            'seguimiento_lote_id' => $request->seguimiento_lote_id,
            'archivo' => $rutaArchivo,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Evidencia de lote registrada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $evidencia = EvidenciaLote::findOrFail($id);

        $request->validate([
            'seguimiento_lote_id' => 'required|exists:seguimientos_lote,id',
            'archivo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $data = [
            'seguimiento_lote_id' => $request->seguimiento_lote_id,
            'descripcion' => $request->descripcion,
        ];

        if ($request->hasFile('archivo')) {
            if ($evidencia->archivo && Storage::disk('public')->exists($evidencia->archivo)) {
                Storage::disk('public')->delete($evidencia->archivo);
            }

            $data['archivo'] = $request->file('archivo')->store('evidencias_lote', 'public');
        }

        $evidencia->update($data);

        return back()->with('success', 'Evidencia de lote actualizada correctamente.');
    }

    public function destroy($id)
    {
        $evidencia = EvidenciaLote::findOrFail($id);

        if ($evidencia->archivo && Storage::disk('public')->exists($evidencia->archivo)) {
            Storage::disk('public')->delete($evidencia->archivo);
        }

        $evidencia->delete();

        return back()->with('success', 'Evidencia de lote eliminada correctamente.');
    }
}
