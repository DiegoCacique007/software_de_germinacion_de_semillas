<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TipoAlerta;
use Illuminate\Http\Request;

class TipoAlertaController extends Controller
{
    public function index()
    {
        $registros = TipoAlerta::latest()->get();

        return view('vistas_principales.super_admin.tipos_alerta.index', ['tipos' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:tipos_alerta,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        TipoAlerta::create($data);

        return redirect()->route('super_admin.tipos-alerta.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, TipoAlerta $tipoAlerta)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:tipos_alerta,clave,' . $tipoAlerta->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tipoAlerta->update($data);

        return redirect()->route('super_admin.tipos-alerta.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(TipoAlerta $tipoAlerta)
    {
        $tipoAlerta->delete();

        return redirect()->route('super_admin.tipos-alerta.index')->with('success', 'Registro eliminado correctamente.');
    }
}
