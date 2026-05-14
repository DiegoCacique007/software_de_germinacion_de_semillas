<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EstadoFrasco;
use Illuminate\Http\Request;

class EstadoFrascoController extends Controller
{
    public function index()
    {
        $registros = EstadoFrasco::latest()->get();

        return view('vistas_principales.super_admin.estados_frasco.index', ['estados' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:estados_frasco,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EstadoFrasco::create($data);

        return redirect()->route('super_admin.estados-frasco.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, EstadoFrasco $estadoFrasco)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:estados_frasco,clave,' . $estadoFrasco->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $estadoFrasco->update($data);

        return redirect()->route('super_admin.estados-frasco.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(EstadoFrasco $estadoFrasco)
    {
        $estadoFrasco->delete();

        return redirect()->route('super_admin.estados-frasco.index')->with('success', 'Registro eliminado correctamente.');
    }
}
