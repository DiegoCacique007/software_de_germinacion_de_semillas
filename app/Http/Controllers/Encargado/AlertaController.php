<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\EstadoAlerta;
use App\Models\Incubadora;
use App\Models\NivelAlerta;
use App\Models\TipoAlerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        $alertas = Alerta::with(['incubadora', 'tipo', 'nivel', 'estado', 'atendidaPor'])
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $incubadoras = Incubadora::orderBy('nombre')->get();
        $tipos = TipoAlerta::orderBy('nombre')->get();
        $niveles = NivelAlerta::orderBy('nombre')->get();
        $estados = EstadoAlerta::orderBy('nombre')->get();

        return view('vistas_principales.encargado.alertas.index', compact(
            'alertas',
            'incubadoras',
            'tipos',
            'niveles',
            'estados'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_alerta_id' => 'required|exists:tipos_alerta,id',
            'nivel_alerta_id' => 'required|exists:niveles_alerta,id',
            'estado_alerta_id' => 'required|exists:estados_alerta,id',
            'mensaje' => 'required|string|max:255',
            'fecha_hora' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        Alerta::create([
            'incubadora_id' => $request->incubadora_id,
            'tipo_alerta_id' => $request->tipo_alerta_id,
            'nivel_alerta_id' => $request->nivel_alerta_id,
            'estado_alerta_id' => $request->estado_alerta_id,
            'mensaje' => $request->mensaje,
            'fecha_hora' => $request->fecha_hora,
            'atendida_por' => null,
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Alerta registrada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $alerta = Alerta::findOrFail($id);

        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_alerta_id' => 'required|exists:tipos_alerta,id',
            'nivel_alerta_id' => 'required|exists:niveles_alerta,id',
            'estado_alerta_id' => 'required|exists:estados_alerta,id',
            'mensaje' => 'required|string|max:255',
            'fecha_hora' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $alerta->update([
            'incubadora_id' => $request->incubadora_id,
            'tipo_alerta_id' => $request->tipo_alerta_id,
            'nivel_alerta_id' => $request->nivel_alerta_id,
            'estado_alerta_id' => $request->estado_alerta_id,
            'mensaje' => $request->mensaje,
            'fecha_hora' => $request->fecha_hora,
            'atendida_por' => auth()->id(),
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Alerta actualizada correctamente.');
    }

    public function destroy($id)
    {
        $alerta = Alerta::findOrFail($id);
        $alerta->delete();

        return back()->with('success', 'Alerta eliminada correctamente.');
    }
}
