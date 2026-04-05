<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\Especie;
use App\Models\EstadoLote;
use App\Models\PosicionIncubadora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LoteController extends Controller
{
    public function index()
    {
        $lotes = Lote::with(['posicion.incubadora', 'especie', 'estado'])
            ->orderBy('id', 'desc')
            ->get();

        $especies = Especie::orderBy('nombre_comun')->get();
        $posiciones = PosicionIncubadora::with('incubadora')
            ->orderBy('incubadora_id')
            ->orderBy('numero_posicion')
            ->get();
        $estados = EstadoLote::orderBy('nombre')->get();

        return view('vistas_principales.administrador.lotes.index', compact(
            'lotes',
            'especies',
            'posiciones',
            'estados'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'posicion_incubadora_id' => 'required|exists:posiciones_incubadora,id',
            'especie_id' => 'required|exists:especies,id',
            'codigo_lote' => 'required|string|max:60|unique:lotes,codigo_lote',
            'fecha_siembra' => 'required|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado_lote_id' => 'required|exists:estados_lote,id',
            'observaciones' => 'nullable|string',
        ]);

        Lote::create([
            'posicion_incubadora_id' => $request->posicion_incubadora_id,
            'especie_id' => $request->especie_id,
            'codigo_lote' => $request->codigo_lote,
            'fecha_siembra' => $request->fecha_siembra,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado_lote_id' => $request->estado_lote_id,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Lote registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $lote = Lote::findOrFail($id);

        $request->validate([
            'posicion_incubadora_id' => 'required|exists:posiciones_incubadora,id',
            'especie_id' => 'required|exists:especies,id',
            'codigo_lote' => [
                'required',
                'string',
                'max:60',
                Rule::unique('lotes', 'codigo_lote')->ignore($lote->id),
            ],
            'fecha_siembra' => 'required|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado_lote_id' => 'required|exists:estados_lote,id',
            'observaciones' => 'nullable|string',
        ]);

        $lote->update([
            'posicion_incubadora_id' => $request->posicion_incubadora_id,
            'especie_id' => $request->especie_id,
            'codigo_lote' => $request->codigo_lote,
            'fecha_siembra' => $request->fecha_siembra,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado_lote_id' => $request->estado_lote_id,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Lote actualizado correctamente.');
    }

    public function destroy($id)
    {
        $lote = Lote::findOrFail($id);

        $tieneFrascos = DB::table('frascos')
            ->where('lote_id', $lote->id)
            ->exists();

        $tieneSeguimientos = DB::table('seguimientos_lote')
            ->where('lote_id', $lote->id)
            ->exists();

        if ($tieneFrascos || $tieneSeguimientos) {
            return back()->with('error', 'No puedes eliminar este lote porque tiene registros relacionados.');
        }

        $lote->delete();

        return back()->with('success', 'Lote eliminado correctamente.');
    }
}
