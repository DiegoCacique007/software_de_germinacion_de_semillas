<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EtapaDesarrollo;
use Illuminate\Http\Request;

class EtapaDesarrolloController extends Controller
{
    public function index()
    {
        $registros = EtapaDesarrollo::latest()->get();

        return view('vistas_principales.super_admin.etapas_desarrollo.index', ['etapas' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:etapas_desarrollo,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EtapaDesarrollo::create($data);

        return redirect()->route('super_admin.etapas-desarrollo.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, EtapaDesarrollo $etapaDesarrollo)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:etapas_desarrollo,clave,' . $etapaDesarrollo->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $etapaDesarrollo->update($data);

        return redirect()->route('super_admin.etapas-desarrollo.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(EtapaDesarrollo $etapaDesarrollo)
    {
        $etapaDesarrollo->delete();

        return redirect()->route('super_admin.etapas-desarrollo.index')->with('success', 'Registro eliminado correctamente.');
    }
}
