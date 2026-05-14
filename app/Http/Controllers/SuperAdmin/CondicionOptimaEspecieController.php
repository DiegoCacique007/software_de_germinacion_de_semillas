<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CondicionOptimaEspecie;
use App\Models\Especie;
use Illuminate\Http\Request;

class CondicionOptimaEspecieController extends Controller
{
    public function index()
    {
        $condiciones = CondicionOptimaEspecie::with('especie')->latest()->get();
        $especies = Especie::orderBy('nombre_comun')->get();

        return view('vistas_principales.super_admin.condiciones_optimas_especie.index', compact('condiciones', 'especies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'especie_id' => 'required|exists:especies,id',
            'temperatura_min' => 'required|numeric',
            'temperatura_max' => 'required|numeric',
            'humedad_min' => 'required|numeric',
            'humedad_max' => 'required|numeric',
            'observaciones' => 'nullable|string',
        ]);

        CondicionOptimaEspecie::create($data);

        return redirect()->route('super_admin.condiciones-optimas-especie.index')->with('success', 'Condición óptima registrada correctamente.');
    }

    public function update(Request $request, CondicionOptimaEspecie $condicionOptimaEspecie)
    {
        $data = $request->validate([
            'especie_id' => 'required|exists:especies,id',
            'temperatura_min' => 'required|numeric',
            'temperatura_max' => 'required|numeric',
            'humedad_min' => 'required|numeric',
            'humedad_max' => 'required|numeric',
            'observaciones' => 'nullable|string',
        ]);

        $condicionOptimaEspecie->update($data);

        return redirect()->route('super_admin.condiciones-optimas-especie.index')->with('success', 'Condición óptima actualizada correctamente.');
    }

    public function destroy(CondicionOptimaEspecie $condicionOptimaEspecie)
    {
        $condicionOptimaEspecie->delete();

        return redirect()->route('super_admin.condiciones-optimas-especie.index')->with('success', 'Condición óptima eliminada correctamente.');
    }
}
