<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EstadoIncubadora;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstadoIncubadoraController extends Controller
{
    public function index()
    {
        $estados = EstadoIncubadora::orderBy('id', 'desc')->get();

        return view('vistas_principales.super_admin.estados_incubadora.index', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave' => 'required|string|max:50|unique:estados_incubadora,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EstadoIncubadora::create([
            'clave' => $request->clave,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Estado de incubadora registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $estadoIncubadora = EstadoIncubadora::findOrFail($id);

        $request->validate([
            'clave' => [
                'required',
                'string',
                'max:50',
                Rule::unique('estados_incubadora', 'clave')->ignore($estadoIncubadora->id),
            ],
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $estadoIncubadora->update([
            'clave' => $request->clave,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Estado de incubadora actualizado correctamente.');
    }

    public function destroy($id)
    {
        $estadoIncubadora = EstadoIncubadora::findOrFail($id);

        if ($estadoIncubadora->incubadoras()->exists()) {
            return back()->with('error', 'No puedes eliminar este estado porque ya está asignado a una o más incubadoras.');
        }

        $estadoIncubadora->delete();

        return back()->with('success', 'Estado de incubadora eliminado correctamente.');
    }
}
