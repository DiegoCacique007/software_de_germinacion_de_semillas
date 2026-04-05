<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\CondicionOptimaEspecie;
use App\Models\Especie;
use Illuminate\Http\Request;

class CondicionOptimaEspecieController extends Controller
{
    public function index()
    {
        $condiciones = CondicionOptimaEspecie::with('especie')
            ->orderBy('id', 'desc')
            ->get();

        $especies = Especie::orderBy('nombre_comun')->get();

        return view('vistas_principales.administrador.condiciones_optimas_especie.index', compact('condiciones', 'especies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'especie_id' => 'required|exists:especies,id',
            'temperatura_min' => 'required|numeric|min:-50|max:100|lte:temperatura_max',
            'temperatura_max' => 'required|numeric|min:-50|max:100|gte:temperatura_min',
            'humedad_min' => 'required|numeric|min:0|max:100|lte:humedad_max',
            'humedad_max' => 'required|numeric|min:0|max:100|gte:humedad_min',
            'observaciones' => 'nullable|string',
        ]);

        CondicionOptimaEspecie::create([
            'especie_id' => $request->especie_id,
            'temperatura_min' => $request->temperatura_min,
            'temperatura_max' => $request->temperatura_max,
            'humedad_min' => $request->humedad_min,
            'humedad_max' => $request->humedad_max,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Condición óptima registrada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $condicion = CondicionOptimaEspecie::findOrFail($id);

        $request->validate([
            'especie_id' => 'required|exists:especies,id',
            'temperatura_min' => 'required|numeric|min:-50|max:100|lte:temperatura_max',
            'temperatura_max' => 'required|numeric|min:-50|max:100|gte:temperatura_min',
            'humedad_min' => 'required|numeric|min:0|max:100|lte:humedad_max',
            'humedad_max' => 'required|numeric|min:0|max:100|gte:humedad_min',
            'observaciones' => 'nullable|string',
        ]);

        $condicion->update([
            'especie_id' => $request->especie_id,
            'temperatura_min' => $request->temperatura_min,
            'temperatura_max' => $request->temperatura_max,
            'humedad_min' => $request->humedad_min,
            'humedad_max' => $request->humedad_max,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Condición óptima actualizada correctamente.');
    }

    public function destroy($id)
    {
        $condicion = CondicionOptimaEspecie::findOrFail($id);
        $condicion->delete();

        return back()->with('success', 'Condición óptima eliminada correctamente.');
    }
}
