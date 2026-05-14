<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Incubadora;
use App\Models\LecturaMicroclima;
use Illuminate\Http\Request;

class LecturaMicroclimaController extends Controller
{
    public function index()
    {
        $lecturas = LecturaMicroclima::with('incubadora')->latest('fecha_hora')->get();
        $incubadoras = Incubadora::orderBy('nombre')->get();

        return view('vistas_principales.super_admin.lecturas_microclima.index', compact('lecturas', 'incubadoras'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'fecha_hora' => 'required|date',
            'temperatura' => 'required|numeric',
            'humedad' => 'required|numeric',
            'observaciones' => 'nullable|string',
        ]);

        LecturaMicroclima::create($data);

        return redirect()->route('super_admin.lecturas-microclima.index')->with('success', 'Lectura registrada correctamente.');
    }

    public function update(Request $request, LecturaMicroclima $lecturaMicroclima)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'fecha_hora' => 'required|date',
            'temperatura' => 'required|numeric',
            'humedad' => 'required|numeric',
            'observaciones' => 'nullable|string',
        ]);

        $lecturaMicroclima->update($data);

        return redirect()->route('super_admin.lecturas-microclima.index')->with('success', 'Lectura actualizada correctamente.');
    }

    public function destroy(LecturaMicroclima $lecturaMicroclima)
    {
        $lecturaMicroclima->delete();

        return redirect()->route('super_admin.lecturas-microclima.index')->with('success', 'Lectura eliminada correctamente.');
    }
}
