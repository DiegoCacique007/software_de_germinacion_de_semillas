<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EtapaDesarrollo;
use App\Models\Lote;
use App\Models\SeguimientoLote;
use App\Models\User;
use Illuminate\Http\Request;

class SeguimientoLoteController extends Controller
{
    public function index()
    {
        $seguimientos = SeguimientoLote::with(['lote', 'etapa', 'user'])->latest('fecha_revision')->get();
        $lotes = Lote::orderBy('codigo_lote')->get();
        $etapas = EtapaDesarrollo::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('vistas_principales.super_admin.seguimientos_lote.index', compact('seguimientos', 'lotes', 'etapas', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'fecha_revision' => 'required|date',
            'frascos_activos' => 'required|integer|min:0',
            'semillas_germinadas' => 'required|integer|min:0',
            'porcentaje_germinacion' => 'required|numeric|min:0',
            'altura_promedio_cm' => 'nullable|numeric|min:0',
            'etapa_desarrollo_id' => 'required|exists:etapas_desarrollo,id',
            'observaciones' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        SeguimientoLote::create($data);

        return redirect()->route('super_admin.seguimientos-lote.index')->with('success', 'Seguimiento de lote registrado correctamente.');
    }

    public function update(Request $request, SeguimientoLote $seguimientoLote)
    {
        $data = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'fecha_revision' => 'required|date',
            'frascos_activos' => 'required|integer|min:0',
            'semillas_germinadas' => 'required|integer|min:0',
            'porcentaje_germinacion' => 'required|numeric|min:0',
            'altura_promedio_cm' => 'nullable|numeric|min:0',
            'etapa_desarrollo_id' => 'required|exists:etapas_desarrollo,id',
            'observaciones' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $seguimientoLote->update($data);

        return redirect()->route('super_admin.seguimientos-lote.index')->with('success', 'Seguimiento de lote actualizado correctamente.');
    }

    public function destroy(SeguimientoLote $seguimientoLote)
    {
        $seguimientoLote->delete();

        return redirect()->route('super_admin.seguimientos-lote.index')->with('success', 'Seguimiento de lote eliminado correctamente.');
    }
}
