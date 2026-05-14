<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EstadoFrasco;
use App\Models\Frasco;
use App\Models\Lote;
use Illuminate\Http\Request;

class FrascoController extends Controller
{
    public function index()
    {
        $frascos = Frasco::with(['lote', 'estado'])->latest()->get();
        $lotes = Lote::orderBy('codigo_lote')->get();
        $estados = EstadoFrasco::orderBy('nombre')->get();

        return view('vistas_principales.super_admin.frascos.index', compact('frascos', 'lotes', 'estados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'numero_frasco' => 'required|integer|min:1',
            'cantidad_semillas' => 'required|integer|min:1',
            'estado_frasco_id' => 'required|exists:estados_frasco,id',
            'observaciones' => 'nullable|string',
        ]);

        Frasco::create($data);

        return redirect()->route('super_admin.frascos.index')->with('success', 'Frasco registrado correctamente.');
    }

    public function update(Request $request, Frasco $frasco)
    {
        $data = $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'numero_frasco' => 'required|integer|min:1',
            'cantidad_semillas' => 'required|integer|min:1',
            'estado_frasco_id' => 'required|exists:estados_frasco,id',
            'observaciones' => 'nullable|string',
        ]);

        $frasco->update($data);

        return redirect()->route('super_admin.frascos.index')->with('success', 'Frasco actualizado correctamente.');
    }

    public function destroy(Frasco $frasco)
    {
        $frasco->delete();

        return redirect()->route('super_admin.frascos.index')->with('success', 'Frasco eliminado correctamente.');
    }
}
