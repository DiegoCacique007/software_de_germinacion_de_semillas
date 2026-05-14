<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionIncubadora;
use App\Models\Incubadora;
use App\Models\User;
use Illuminate\Http\Request;

class AsignacionIncubadoraController extends Controller
{
    public function index()
    {
        $asignaciones = AsignacionIncubadora::with(['incubadora', 'user'])->latest()->get();
        $incubadoras = Incubadora::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('vistas_principales.super_admin.asignaciones_incubadora.index', compact('asignaciones', 'incubadoras', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'user_id' => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        AsignacionIncubadora::create($data);

        return redirect()->route('super_admin.asignaciones-incubadora.index')->with('success', 'Asignación registrada correctamente.');
    }

    public function update(Request $request, AsignacionIncubadora $asignacionIncubadora)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'user_id' => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $asignacionIncubadora->update($data);

        return redirect()->route('super_admin.asignaciones-incubadora.index')->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy(AsignacionIncubadora $asignacionIncubadora)
    {
        $asignacionIncubadora->delete();

        return redirect()->route('super_admin.asignaciones-incubadora.index')->with('success', 'Asignación eliminada correctamente.');
    }
}
