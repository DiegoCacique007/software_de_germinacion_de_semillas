<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\RegistroBiologico;
use Illuminate\Http\Request;

class RegistroBiologicoController extends Controller
{
    public function index(Request $request)
    {
        $registros = RegistroBiologico::with(['lote', 'usuario'])
            ->orderByDesc('fecha_registro')
            ->paginate(10);

        $lotes = Lote::orderBy('id')->get();

        $routePrefix = $request->routeIs('super_admin.*') ? 'super_admin' : 'encargado';

        return view('vistas_principales.shared.registros_biologicos.index', compact(
            'registros',
            'lotes',
            'routePrefix'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'fecha_registro' => 'required|date',
            'dias_estratificacion' => 'required|integer|min:0',
            'porcentaje_carbono' => 'nullable|numeric|min:0|max:100',
            'porcentaje_nitrogeno' => 'nullable|numeric|min:0|max:100',
            'porcentaje_fosforo' => 'nullable|numeric|min:0|max:100',
            'tasa_germinacion' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        RegistroBiologico::create([
            'lote_id' => $request->lote_id,
            'user_id' => auth()->id(),
            'fecha_registro' => $request->fecha_registro,
            'dias_estratificacion' => $request->dias_estratificacion,
            'porcentaje_carbono' => $request->porcentaje_carbono,
            'porcentaje_nitrogeno' => $request->porcentaje_nitrogeno,
            'porcentaje_fosforo' => $request->porcentaje_fosforo,
            'tasa_germinacion' => $request->tasa_germinacion,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Registro biológico guardado correctamente.');
    }

    public function update(Request $request, RegistroBiologico $registros_biologico)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
            'fecha_registro' => 'required|date',
            'dias_estratificacion' => 'required|integer|min:0',
            'porcentaje_carbono' => 'nullable|numeric|min:0|max:100',
            'porcentaje_nitrogeno' => 'nullable|numeric|min:0|max:100',
            'porcentaje_fosforo' => 'nullable|numeric|min:0|max:100',
            'tasa_germinacion' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $registros_biologico->update([
            'lote_id' => $request->lote_id,
            'fecha_registro' => $request->fecha_registro,
            'dias_estratificacion' => $request->dias_estratificacion,
            'porcentaje_carbono' => $request->porcentaje_carbono,
            'porcentaje_nitrogeno' => $request->porcentaje_nitrogeno,
            'porcentaje_fosforo' => $request->porcentaje_fosforo,
            'tasa_germinacion' => $request->tasa_germinacion,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Registro biológico actualizado correctamente.');
    }

    public function destroy(RegistroBiologico $registros_biologico)
    {
        $registros_biologico->delete();

        return back()->with('success', 'Registro biológico eliminado correctamente.');
    }
}
