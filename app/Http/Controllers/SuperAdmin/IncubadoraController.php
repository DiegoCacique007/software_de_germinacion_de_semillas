<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EstadoIncubadora;
use App\Models\Incubadora;
use Illuminate\Http\Request;

class IncubadoraController extends Controller
{
    public function index()
    {
        $incubadoras = Incubadora::with('estado')->latest()->get();
        $estados = EstadoIncubadora::orderBy('nombre')->get();

        return view('vistas_principales.super_admin.incubadoras.index', compact('incubadoras', 'estados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:50|unique:incubadoras,codigo',
            'nombre' => 'required|string|max:150',
            'ubicacion' => 'nullable|string|max:180',
            'descripcion' => 'nullable|string',
            'estado_incubadora_id' => 'required|exists:estados_incubadora,id',
        ]);

        Incubadora::create($data);

        return redirect()->route('super_admin.incubadoras.index')->with('success', 'Incubadora registrada correctamente.');
    }

    public function update(Request $request, Incubadora $incubadora)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:50|unique:incubadoras,codigo,' . $incubadora->id,
            'nombre' => 'required|string|max:150',
            'ubicacion' => 'nullable|string|max:180',
            'descripcion' => 'nullable|string',
            'estado_incubadora_id' => 'required|exists:estados_incubadora,id',
        ]);

        $incubadora->update($data);

        return redirect()->route('super_admin.incubadoras.index')->with('success', 'Incubadora actualizada correctamente.');
    }

    public function destroy(Incubadora $incubadora)
    {
        $incubadora->delete();

        return redirect()->route('super_admin.incubadoras.index')->with('success', 'Incubadora eliminada correctamente.');
    }
}
