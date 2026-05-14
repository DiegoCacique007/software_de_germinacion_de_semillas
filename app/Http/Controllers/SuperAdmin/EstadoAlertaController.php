<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EstadoAlerta;
use Illuminate\Http\Request;

class EstadoAlertaController extends Controller
{
    public function index()
    {
        $registros = EstadoAlerta::latest()->get();

        return view('vistas_principales.super_admin.estados_alerta.index', ['estados' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:estados_alerta,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EstadoAlerta::create($data);

        return redirect()->route('super_admin.estados-alerta.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, EstadoAlerta $estadoAlerta)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:estados_alerta,clave,' . $estadoAlerta->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $estadoAlerta->update($data);

        return redirect()->route('super_admin.estados-alerta.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(EstadoAlerta $estadoAlerta)
    {
        $estadoAlerta->delete();

        return redirect()->route('super_admin.estados-alerta.index')->with('success', 'Registro eliminado correctamente.');
    }
}
