<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\EstadoFrasco;
use App\Models\Frasco;
use App\Models\SeguimientoFrasco;
use Illuminate\Http\Request;

class SeguimientoFrascoController extends Controller
{
    public function index()
    {
        $seguimientos = SeguimientoFrasco::with(['frasco.lote.especie', 'estado', 'usuario'])
            ->orderBy('fecha_revision', 'desc')
            ->get();

        $frascos = Frasco::with(['lote.especie'])
            ->orderBy('lote_id')
            ->orderBy('numero_frasco')
            ->get();

        $estados = EstadoFrasco::orderBy('nombre')->get();

        return view('vistas_principales.encargado.seguimientos_frasco.index', compact(
            'seguimientos',
            'frascos',
            'estados'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'frasco_id' => 'required|exists:frascos,id',
            'fecha_revision' => 'required|date',
            'semillas_germinadas' => 'required|integer|min:0',
            'altura_promedio_cm' => 'nullable|numeric|min:0|max:999999.99',
            'estado_frasco_id' => 'required|exists:estados_frasco,id',
            'observaciones' => 'nullable|string',
        ]);

        SeguimientoFrasco::create([
            'frasco_id' => $request->frasco_id,
            'fecha_revision' => $request->fecha_revision,
            'semillas_germinadas' => $request->semillas_germinadas,
            'altura_promedio_cm' => $request->altura_promedio_cm,
            'estado_frasco_id' => $request->estado_frasco_id,
            'observaciones' => $request->observaciones,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Seguimiento de frasco registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $seguimiento = SeguimientoFrasco::findOrFail($id);

        $request->validate([
            'frasco_id' => 'required|exists:frascos,id',
            'fecha_revision' => 'required|date',
            'semillas_germinadas' => 'required|integer|min:0',
            'altura_promedio_cm' => 'nullable|numeric|min:0|max:999999.99',
            'estado_frasco_id' => 'required|exists:estados_frasco,id',
            'observaciones' => 'nullable|string',
        ]);

        $seguimiento->update([
            'frasco_id' => $request->frasco_id,
            'fecha_revision' => $request->fecha_revision,
            'semillas_germinadas' => $request->semillas_germinadas,
            'altura_promedio_cm' => $request->altura_promedio_cm,
            'estado_frasco_id' => $request->estado_frasco_id,
            'observaciones' => $request->observaciones,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Seguimiento de frasco actualizado correctamente.');
    }

    public function destroy($id)
    {
        $seguimiento = SeguimientoFrasco::findOrFail($id);
        $seguimiento->delete();

        return back()->with('success', 'Seguimiento de frasco eliminado correctamente.');
    }
}
