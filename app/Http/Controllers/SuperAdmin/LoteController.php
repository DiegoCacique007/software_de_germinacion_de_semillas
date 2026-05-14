<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Especie;
use App\Models\EstadoLote;
use App\Models\Lote;
use App\Models\PosicionIncubadora;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index()
    {
        $lotes = Lote::with(['posicion.incubadora', 'especie', 'estado'])->latest()->get();
        $posiciones = PosicionIncubadora::with('incubadora')->orderBy('numero_posicion')->get();
        $especies = Especie::orderBy('nombre_comun')->get();
        $estados = EstadoLote::orderBy('nombre')->get();

        return view('vistas_principales.super_admin.lotes.index', compact('lotes', 'posiciones', 'especies', 'estados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'posicion_incubadora_id' => 'required|exists:posiciones_incubadora,id',
            'especie_id' => 'required|exists:especies,id',
            'codigo_lote' => 'required|string|max:60|unique:lotes,codigo_lote',
            'fecha_siembra' => 'required|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'estado_lote_id' => 'required|exists:estados_lote,id',
            'observaciones' => 'nullable|string',
        ]);

        Lote::create($data);

        return redirect()->route('super_admin.lotes.index')->with('success', 'Lote registrado correctamente.');
    }

    public function update(Request $request, Lote $lote)
    {
        $data = $request->validate([
            'posicion_incubadora_id' => 'required|exists:posiciones_incubadora,id',
            'especie_id' => 'required|exists:especies,id',
            'codigo_lote' => 'required|string|max:60|unique:lotes,codigo_lote,' . $lote->id,
            'fecha_siembra' => 'required|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'estado_lote_id' => 'required|exists:estados_lote,id',
            'observaciones' => 'nullable|string',
        ]);

        $lote->update($data);

        return redirect()->route('super_admin.lotes.index')->with('success', 'Lote actualizado correctamente.');
    }

    public function destroy(Lote $lote)
    {
        $lote->delete();

        return redirect()->route('super_admin.lotes.index')->with('success', 'Lote eliminado correctamente.');
    }
}
