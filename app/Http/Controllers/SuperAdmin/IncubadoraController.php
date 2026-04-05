<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Incubadora;
use App\Models\EstadoIncubadora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IncubadoraController extends Controller
{
    public function index()
    {
        $incubadoras = Incubadora::with('estado')
            ->orderBy('id', 'desc')
            ->get();

        $estados = EstadoIncubadora::orderBy('nombre')->get();

        return view('vistas_principales.super_admin.incubadoras.index', compact('incubadoras', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:incubadoras,codigo',
            'nombre' => 'required|string|max:150',
            'ubicacion' => 'nullable|string|max:180',
            'descripcion' => 'nullable|string',
            'estado_incubadora_id' => 'required|exists:estados_incubadora,id',
        ]);

        Incubadora::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'ubicacion' => $request->ubicacion,
            'descripcion' => $request->descripcion,
            'estado_incubadora_id' => $request->estado_incubadora_id,
        ]);

        return back()->with('success', 'Incubadora registrada correctamente.');
    }

    public function update(Request $request, Incubadora $incubadora)
    {
        $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('incubadoras', 'codigo')->ignore($incubadora->id),
            ],
            'nombre' => 'required|string|max:150',
            'ubicacion' => 'nullable|string|max:180',
            'descripcion' => 'nullable|string',
            'estado_incubadora_id' => 'required|exists:estados_incubadora,id',
        ]);

        $incubadora->update([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'ubicacion' => $request->ubicacion,
            'descripcion' => $request->descripcion,
            'estado_incubadora_id' => $request->estado_incubadora_id,
        ]);

        return back()->with('success', 'Incubadora actualizada correctamente.');
    }

    public function destroy(Incubadora $incubadora)
    {
        $tieneRelacionados =
            DB::table('posiciones_incubadora')->where('incubadora_id', $incubadora->id)->exists() ||
            DB::table('alertas')->where('incubadora_id', $incubadora->id)->exists() ||
            DB::table('lecturas_microclima')->where('incubadora_id', $incubadora->id)->exists() ||
            DB::table('controles_incubadora')->where('incubadora_id', $incubadora->id)->exists() ||
            DB::table('asignaciones_incubadora')->where('incubadora_id', $incubadora->id)->exists();

        if ($tieneRelacionados) {
            return back()->with('error', 'No puedes eliminar esta incubadora porque tiene registros relacionados.');
        }

        $incubadora->delete();

        return back()->with('success', 'Incubadora eliminada correctamente.');
    }
}
