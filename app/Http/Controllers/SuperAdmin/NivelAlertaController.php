<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\NivelAlerta;
use Illuminate\Http\Request;

class NivelAlertaController extends Controller
{
    public function index()
    {
        $registros = NivelAlerta::latest()->get();

        return view('vistas_principales.super_admin.niveles_alerta.index', ['niveles' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:niveles_alerta,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        NivelAlerta::create($data);

        return redirect()->route('super_admin.niveles-alerta.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, NivelAlerta $nivelAlerta)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:niveles_alerta,clave,' . $nivelAlerta->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $nivelAlerta->update($data);

        return redirect()->route('super_admin.niveles-alerta.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(NivelAlerta $nivelAlerta)
    {
        $nivelAlerta->delete();

        return redirect()->route('super_admin.niveles-alerta.index')->with('success', 'Registro eliminado correctamente.');
    }
}
