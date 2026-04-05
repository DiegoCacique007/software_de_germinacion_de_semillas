<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\EtapaDesarrollo;
use App\Models\Lote;
use App\Models\SeguimientoLote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeguimientoLoteController extends Controller
{
    public function index()
    {
        $seguimientos = SeguimientoLote::with(['lote.especie', 'etapa', 'usuario'])
            ->orderBy('fecha_revision', 'desc')
            ->get();

        $lotes = Lote::with(['especie', 'posicion.incubadora'])
            ->orderBy('codigo_lote')
            ->get();

        $etapas = EtapaDesarrollo::orderBy('nombre')->get();

        return view('vistas_principales.encargado.seguimientos_lote.index', compact(
            'seguimientos',
            'lotes',
            'etapas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'fecha_revision' => 'required|date',
            'frascos_activos' => 'required|integer|min:0|max:255',
            'semillas_germinadas' => 'required|integer|min:0',
            'porcentaje_germinacion' => 'required|numeric|min:0|max:100',
            'altura_promedio_cm' => 'nullable|numeric|min:0|max:999999.99',
            'etapa_desarrollo_id' => 'required|exists:etapas_desarrollo,id',
            'observaciones' => 'nullable|string',
        ]);

        SeguimientoLote::create([
            'lote_id' => $request->lote_id,
            'fecha_revision' => $request->fecha_revision,
            'frascos_activos' => $request->frascos_activos,
            'semillas_germinadas' => $request->semillas_germinadas,
            'porcentaje_germinacion' => $request->porcentaje_germinacion,
            'altura_promedio_cm' => $request->altura_promedio_cm,
            'etapa_desarrollo_id' => $request->etapa_desarrollo_id,
            'observaciones' => $request->observaciones,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Seguimiento de lote registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $seguimiento = SeguimientoLote::findOrFail($id);

        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'fecha_revision' => 'required|date',
            'frascos_activos' => 'required|integer|min:0|max:255',
            'semillas_germinadas' => 'required|integer|min:0',
            'porcentaje_germinacion' => 'required|numeric|min:0|max:100',
            'altura_promedio_cm' => 'nullable|numeric|min:0|max:999999.99',
            'etapa_desarrollo_id' => 'required|exists:etapas_desarrollo,id',
            'observaciones' => 'nullable|string',
        ]);

        $seguimiento->update([
            'lote_id' => $request->lote_id,
            'fecha_revision' => $request->fecha_revision,
            'frascos_activos' => $request->frascos_activos,
            'semillas_germinadas' => $request->semillas_germinadas,
            'porcentaje_germinacion' => $request->porcentaje_germinacion,
            'altura_promedio_cm' => $request->altura_promedio_cm,
            'etapa_desarrollo_id' => $request->etapa_desarrollo_id,
            'observaciones' => $request->observaciones,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Seguimiento de lote actualizado correctamente.');
    }

    public function destroy($id)
    {
        $seguimiento = SeguimientoLote::findOrFail($id);

        $tieneEvidencias = DB::table('evidencias_lote')
            ->where('seguimiento_lote_id', $seguimiento->id)
            ->exists();

        if ($tieneEvidencias) {
            return back()->with('error', 'No puedes eliminar este seguimiento porque tiene evidencias relacionadas.');
        }

        $seguimiento->delete();

        return back()->with('success', 'Seguimiento de lote eliminado correctamente.');
    }
}
