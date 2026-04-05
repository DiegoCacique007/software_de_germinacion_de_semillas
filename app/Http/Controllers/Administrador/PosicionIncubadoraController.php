<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Incubadora;
use App\Models\PosicionIncubadora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PosicionIncubadoraController extends Controller
{
    public function index()
    {
        $posiciones = PosicionIncubadora::with('incubadora')
            ->orderBy('incubadora_id')
            ->orderBy('numero_posicion')
            ->get();

        $incubadoras = Incubadora::orderBy('nombre')->get();

        return view('vistas_principales.administrador.posiciones_incubadora.index', compact('posiciones', 'incubadoras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'numero_posicion' => [
                'required',
                'integer',
                'min:1',
                'max:5',
                Rule::unique('posiciones_incubadora')->where(function ($query) use ($request) {
                    return $query->where('incubadora_id', $request->incubadora_id);
                }),
            ],
            'descripcion' => 'nullable|string|max:150',
        ], [
            'numero_posicion.unique' => 'Ese número de posición ya existe para la incubadora seleccionada.',
        ]);

        PosicionIncubadora::create([
            'incubadora_id' => $request->incubadora_id,
            'numero_posicion' => $request->numero_posicion,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Posición de incubadora registrada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $posicion = PosicionIncubadora::findOrFail($id);

        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'numero_posicion' => [
                'required',
                'integer',
                'min:1',
                'max:5',
                Rule::unique('posiciones_incubadora')->where(function ($query) use ($request) {
                    return $query->where('incubadora_id', $request->incubadora_id);
                })->ignore($posicion->id),
            ],
            'descripcion' => 'nullable|string|max:150',
        ], [
            'numero_posicion.unique' => 'Ese número de posición ya existe para la incubadora seleccionada.',
        ]);

        $posicion->update([
            'incubadora_id' => $request->incubadora_id,
            'numero_posicion' => $request->numero_posicion,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Posición de incubadora actualizada correctamente.');
    }

    public function destroy($id)
    {
        $posicion = PosicionIncubadora::findOrFail($id);

        $tieneLotes = DB::table('lotes')
            ->where('posicion_incubadora_id', $posicion->id)
            ->exists();

        if ($tieneLotes) {
            return back()->with('error', 'No puedes eliminar esta posición porque ya tiene lotes relacionados.');
        }

        $posicion->delete();

        return back()->with('success', 'Posición de incubadora eliminada correctamente.');
    }
}
