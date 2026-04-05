<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Incubadora;
use App\Models\LecturaMicroclima;
use Illuminate\Http\Request;

class LecturaMicroclimaController extends Controller
{
    public function index()
    {
        $lecturas = LecturaMicroclima::with('incubadora')
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $incubadoras = Incubadora::orderBy('nombre')->get();

        return view('vistas_principales.administrador.lecturas_microclima.index', compact('lecturas', 'incubadoras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'fecha_hora' => 'required|date',
            'temperatura' => 'required|numeric|min:-50|max:100',
            'humedad' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string',
        ]);

        LecturaMicroclima::create([
            'incubadora_id' => $request->incubadora_id,
            'fecha_hora' => $request->fecha_hora,
            'temperatura' => $request->temperatura,
            'humedad' => $request->humedad,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Lectura de microclima registrada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $lectura = LecturaMicroclima::findOrFail($id);

        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'fecha_hora' => 'required|date',
            'temperatura' => 'required|numeric|min:-50|max:100',
            'humedad' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string',
        ]);

        $lectura->update([
            'incubadora_id' => $request->incubadora_id,
            'fecha_hora' => $request->fecha_hora,
            'temperatura' => $request->temperatura,
            'humedad' => $request->humedad,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Lectura de microclima actualizada correctamente.');
    }

    public function destroy($id)
    {
        $lectura = LecturaMicroclima::findOrFail($id);
        $lectura->delete();

        return back()->with('success', 'Lectura de microclima eliminada correctamente.');
    }
}
