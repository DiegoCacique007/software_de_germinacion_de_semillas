<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EvidenciaLote;
use App\Models\SeguimientoLote;
use Illuminate\Http\Request;

class EvidenciaLoteController extends Controller
{
    public function index()
    {
        $evidencias = EvidenciaLote::with('seguimiento')->latest()->get();
        $seguimientos = SeguimientoLote::latest()->get();

        return view('vistas_principales.super_admin.evidencias_lote.index', compact('evidencias', 'seguimientos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'seguimiento_lote_id' => 'required|exists:seguimientos_lote,id',
            'archivo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EvidenciaLote::create($data);

        return redirect()->route('super_admin.evidencias-lote.index')->with('success', 'Evidencia registrada correctamente.');
    }

    public function update(Request $request, EvidenciaLote $evidenciaLote)
    {
        $data = $request->validate([
            'seguimiento_lote_id' => 'required|exists:seguimientos_lote,id',
            'archivo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $evidenciaLote->update($data);

        return redirect()->route('super_admin.evidencias-lote.index')->with('success', 'Evidencia actualizada correctamente.');
    }

    public function destroy(EvidenciaLote $evidenciaLote)
    {
        $evidenciaLote->delete();

        return redirect()->route('super_admin.evidencias-lote.index')->with('success', 'Evidencia eliminada correctamente.');
    }
}
