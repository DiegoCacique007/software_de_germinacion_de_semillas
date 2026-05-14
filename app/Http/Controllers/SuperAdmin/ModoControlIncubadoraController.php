<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ModoControlIncubadora;
use Illuminate\Http\Request;

class ModoControlIncubadoraController extends Controller
{
    public function index()
    {
        $registros = ModoControlIncubadora::latest()->get();

        return view('vistas_principales.super_admin.modos_control_incubadora.index', ['modos' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:modos_control_incubadora,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        ModoControlIncubadora::create($data);

        return redirect()->route('super_admin.modos-control-incubadora.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, ModoControlIncubadora $modoControlIncubadora)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:modos_control_incubadora,clave,' . $modoControlIncubadora->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $modoControlIncubadora->update($data);

        return redirect()->route('super_admin.modos-control-incubadora.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(ModoControlIncubadora $modoControlIncubadora)
    {
        $modoControlIncubadora->delete();

        return redirect()->route('super_admin.modos-control-incubadora.index')->with('success', 'Registro eliminado correctamente.');
    }
}
