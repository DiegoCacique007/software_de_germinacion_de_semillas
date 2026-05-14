<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EstadoLote;
use Illuminate\Http\Request;

class EstadoLoteController extends Controller
{
    public function index()
    {
        $registros = EstadoLote::latest()->get();

        return view('vistas_principales.super_admin.estados_lote.index', ['estados' => $registros]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:estados_lote,clave',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EstadoLote::create($data);

        return redirect()->route('super_admin.estados-lote.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, EstadoLote $estadoLote)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:50|unique:estados_lote,clave,' . $estadoLote->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $estadoLote->update($data);

        return redirect()->route('super_admin.estados-lote.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(EstadoLote $estadoLote)
    {
        $estadoLote->delete();

        return redirect()->route('super_admin.estados-lote.index')->with('success', 'Registro eliminado correctamente.');
    }
}
