<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Incubadora;
use App\Models\PosicionIncubadora;
use Illuminate\Http\Request;

class PosicionIncubadoraController extends Controller
{
    public function index()
    {
        $posiciones = PosicionIncubadora::with('incubadora')->latest()->get();
        $incubadoras = Incubadora::orderBy('nombre')->get();

        return view('vistas_principales.super_admin.posiciones_incubadora.index', compact('posiciones', 'incubadoras'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'numero_posicion' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:150',
        ]);

        PosicionIncubadora::create($data);

        return redirect()->route('super_admin.posiciones-incubadora.index')->with('success', 'Posición registrada correctamente.');
    }

    public function update(Request $request, PosicionIncubadora $posicionIncubadora)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'numero_posicion' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:150',
        ]);

        $posicionIncubadora->update($data);

        return redirect()->route('super_admin.posiciones-incubadora.index')->with('success', 'Posición actualizada correctamente.');
    }

    public function destroy(PosicionIncubadora $posicionIncubadora)
    {
        $posicionIncubadora->delete();

        return redirect()->route('super_admin.posiciones-incubadora.index')->with('success', 'Posición eliminada correctamente.');
    }
}
