<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\RegistroBiologico;
use App\Models\User;
use Illuminate\Http\Request;

class RegistroBiologicoController extends Controller
{
    public function index()
    {
        $registros = RegistroBiologico::with(['lote', 'user'])->latest('fecha_registro')->get();
        $lotes = Lote::orderBy('codigo_lote')->get();
        $usuarios = User::orderBy('name')->get();

        return view('vistas_principales.super_admin.registros_biologicos.index', compact('registros', 'lotes', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'user_id' => 'nullable|exists:users,id',
            'fecha_registro' => 'required|date',
            'dias_estratificacion' => 'required|integer|min:0',
            'porcentaje_carbono' => 'nullable|numeric|min:0',
            'porcentaje_nitrogeno' => 'nullable|numeric|min:0',
            'porcentaje_fosforo' => 'nullable|numeric|min:0',
            'tasa_germinacion' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        RegistroBiologico::create($data);

        return redirect()->route('super_admin.registros-biologicos.index')->with('success', 'Registro biológico creado correctamente.');
    }

    public function update(Request $request, RegistroBiologico $registroBiologico)
    {
        $data = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'user_id' => 'nullable|exists:users,id',
            'fecha_registro' => 'required|date',
            'dias_estratificacion' => 'required|integer|min:0',
            'porcentaje_carbono' => 'nullable|numeric|min:0',
            'porcentaje_nitrogeno' => 'nullable|numeric|min:0',
            'porcentaje_fosforo' => 'nullable|numeric|min:0',
            'tasa_germinacion' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $registroBiologico->update($data);

        return redirect()->route('super_admin.registros-biologicos.index')->with('success', 'Registro biológico actualizado correctamente.');
    }

    public function destroy(RegistroBiologico $registroBiologico)
    {
        $registroBiologico->delete();

        return redirect()->route('super_admin.registros-biologicos.index')->with('success', 'Registro biológico eliminado correctamente.');
    }
}
