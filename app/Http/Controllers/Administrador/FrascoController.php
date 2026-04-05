<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\EstadoFrasco;
use App\Models\Frasco;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FrascoController extends Controller
{
    public function index()
    {
        $frascos = Frasco::with(['lote.especie', 'estado'])
            ->orderBy('lote_id')
            ->orderBy('numero_frasco')
            ->get();

        $lotes = Lote::with(['especie', 'posicion.incubadora'])
            ->orderBy('codigo_lote')
            ->get();

        $estados = EstadoFrasco::orderBy('nombre')->get();

        return view('vistas_principales.administrador.frascos.index', compact('frascos', 'lotes', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'numero_frasco' => [
                'required',
                'integer',
                'min:1',
                'max:50',
                Rule::unique('frascos')->where(function ($query) use ($request) {
                    return $query->where('lote_id', $request->lote_id);
                }),
            ],
            'cantidad_semillas' => 'required|integer|min:1|max:20',
            'estado_frasco_id' => 'required|exists:estados_frasco,id',
            'observaciones' => 'nullable|string',
        ], [
            'numero_frasco.unique' => 'Ese número de frasco ya existe dentro del lote seleccionado.',
        ]);

        Frasco::create([
            'lote_id' => $request->lote_id,
            'numero_frasco' => $request->numero_frasco,
            'cantidad_semillas' => $request->cantidad_semillas,
            'estado_frasco_id' => $request->estado_frasco_id,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Frasco registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $frasco = Frasco::findOrFail($id);

        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'numero_frasco' => [
                'required',
                'integer',
                'min:1',
                'max:50',
                Rule::unique('frascos')->where(function ($query) use ($request) {
                    return $query->where('lote_id', $request->lote_id);
                })->ignore($frasco->id),
            ],
            'cantidad_semillas' => 'required|integer|min:1|max:20',
            'estado_frasco_id' => 'required|exists:estados_frasco,id',
            'observaciones' => 'nullable|string',
        ], [
            'numero_frasco.unique' => 'Ese número de frasco ya existe dentro del lote seleccionado.',
        ]);

        $frasco->update([
            'lote_id' => $request->lote_id,
            'numero_frasco' => $request->numero_frasco,
            'cantidad_semillas' => $request->cantidad_semillas,
            'estado_frasco_id' => $request->estado_frasco_id,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Frasco actualizado correctamente.');
    }

    public function destroy($id)
    {
        $frasco = Frasco::findOrFail($id);

        $tieneSeguimientos = DB::table('seguimientos_frasco')
            ->where('frasco_id', $frasco->id)
            ->exists();

        if ($tieneSeguimientos) {
            return back()->with('error', 'No puedes eliminar este frasco porque tiene seguimientos relacionados.');
        }

        $frasco->delete();

        return back()->with('success', 'Frasco eliminado correctamente.');
    }
}
