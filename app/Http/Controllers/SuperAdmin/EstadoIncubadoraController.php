<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EstadoIncubadora;
use Illuminate\Http\Request;

class EstadoIncubadoraController extends Controller
{
    public function index()
    {
        $registros = EstadoIncubadora::latest()->get();

        return view('vistas_principales.super_admin.estados_incubadora.index', ['estados' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:estados_incubadora,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EstadoIncubadora::create($data);

        return redirect()->route('super_admin.estados-incubadora.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, EstadoIncubadora $estadoIncubadora)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:estados_incubadora,clave,' . $estadoIncubadora->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $estadoIncubadora->update($data);

        return redirect()->route('super_admin.estados-incubadora.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(EstadoIncubadora $estadoIncubadora)
    {
        $estadoIncubadora->delete();

        return redirect()->route('super_admin.estados-incubadora.index')->with('success', 'Registro eliminado correctamente.');
    }
}
