<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TipoControlIncubadora;
use Illuminate\Http\Request;

class TipoControlIncubadoraController extends Controller
{
    public function index()
    {
        $registros = TipoControlIncubadora::latest()->get();

        return view('vistas_principales.super_admin.tipos_control_incubadora.index', ['tipos' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:tipos_control_incubadora,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        TipoControlIncubadora::create($data);

        return redirect()->route('super_admin.tipos-control-incubadora.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, TipoControlIncubadora $tipoControlIncubadora)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:tipos_control_incubadora,clave,' . $tipoControlIncubadora->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tipoControlIncubadora->update($data);

        return redirect()->route('super_admin.tipos-control-incubadora.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(TipoControlIncubadora $tipoControlIncubadora)
    {
        $tipoControlIncubadora->delete();

        return redirect()->route('super_admin.tipos-control-incubadora.index')->with('success', 'Registro eliminado correctamente.');
    }
}
