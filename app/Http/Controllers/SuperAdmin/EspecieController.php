<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Especie;
use Illuminate\Http\Request;

class EspecieController extends Controller
{
    public function index()
    {
        $especies = Especie::latest()->get();

        return view('vistas_principales.super_admin.especies.index', compact('especies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_comun' => 'required|string|max:150',
            'nombre_cientifico' => 'nullable|string|max:180',
            'familia' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        Especie::create($data);

        return redirect()->route('super_admin.especies.index')->with('success', 'Especie registrada correctamente.');
    }

    public function update(Request $request, Especie $especie)
    {
        $data = $request->validate([
            'nombre_comun' => 'required|string|max:150',
            'nombre_cientifico' => 'nullable|string|max:180',
            'familia' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $especie->update($data);

        return redirect()->route('super_admin.especies.index')->with('success', 'Especie actualizada correctamente.');
    }

    public function destroy(Especie $especie)
    {
        $especie->delete();

        return redirect()->route('super_admin.especies.index')->with('success', 'Especie eliminada correctamente.');
    }
}
