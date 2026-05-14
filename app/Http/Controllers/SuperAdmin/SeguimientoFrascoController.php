<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EstadoFrasco;
use App\Models\Frasco;
use App\Models\SeguimientoFrasco;
use App\Models\User;
use Illuminate\Http\Request;

class SeguimientoFrascoController extends Controller
{
    public function index()
    {
        $seguimientos = SeguimientoFrasco::with(['frasco', 'estado', 'user'])->latest('fecha_revision')->get();
        $frascos = Frasco::orderBy('numero_frasco')->get();
        $estados = EstadoFrasco::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('vistas_principales.super_admin.seguimientos_frasco.index', compact('seguimientos', 'frascos', 'estados', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'frasco_id' => 'required|exists:frascos,id',
            'fecha_revision' => 'required|date',
            'semillas_germinadas' => 'required|integer|min:0',
            'altura_promedio_cm' => 'nullable|numeric|min:0',
            'estado_frasco_id' => 'required|exists:estados_frasco,id',
            'observaciones' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        SeguimientoFrasco::create($data);

        return redirect()->route('super_admin.seguimientos-frasco.index')->with('success', 'Seguimiento de frasco registrado correctamente.');
    }

    public function update(Request $request, SeguimientoFrasco $seguimientoFrasco)
    {
        $data = $request->validate([
            'frasco_id' => 'required|exists:frascos,id',
            'fecha_revision' => 'required|date',
            'semillas_germinadas' => 'required|integer|min:0',
            'altura_promedio_cm' => 'nullable|numeric|min:0',
            'estado_frasco_id' => 'required|exists:estados_frasco,id',
            'observaciones' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $seguimientoFrasco->update($data);

        return redirect()->route('super_admin.seguimientos-frasco.index')->with('success', 'Seguimiento de frasco actualizado correctamente.');
    }

    public function destroy(SeguimientoFrasco $seguimientoFrasco)
    {
        $seguimientoFrasco->delete();

        return redirect()->route('super_admin.seguimientos-frasco.index')->with('success', 'Seguimiento de frasco eliminado correctamente.');
    }
}
