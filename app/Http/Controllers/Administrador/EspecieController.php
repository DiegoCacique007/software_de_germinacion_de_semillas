<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Especie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EspecieController extends Controller
{
    public function index()
    {
        $especies = Especie::orderBy('id', 'desc')->get();

        return view('vistas_principales.administrador.especies.index', compact('especies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_comun' => 'required|string|max:150',
            'nombre_cientifico' => 'nullable|string|max:180',
            'familia' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        Especie::create([
            'nombre_comun' => $request->nombre_comun,
            'nombre_cientifico' => $request->nombre_cientifico,
            'familia' => $request->familia,
            'descripcion' => $request->descripcion,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Especie registrada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $especie = Especie::findOrFail($id);

        $request->validate([
            'nombre_comun' => 'required|string|max:150',
            'nombre_cientifico' => 'nullable|string|max:180',
            'familia' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $especie->update([
            'nombre_comun' => $request->nombre_comun,
            'nombre_cientifico' => $request->nombre_cientifico,
            'familia' => $request->familia,
            'descripcion' => $request->descripcion,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Especie actualizada correctamente.');
    }

    public function destroy($id)
    {
        $especie = Especie::findOrFail($id);

        $tieneCondiciones = DB::table('condiciones_optimas_especie')
            ->where('especie_id', $especie->id)
            ->exists();

        $tieneLotes = DB::table('lotes')
            ->where('especie_id', $especie->id)
            ->exists();

        if ($tieneCondiciones || $tieneLotes) {
            return back()->with('error', 'No puedes eliminar esta especie porque tiene registros relacionados.');
        }

        $especie->delete();

        return back()->with('success', 'Especie eliminada correctamente.');
    }
}
