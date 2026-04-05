<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\ControlIncubadora;
use App\Models\Incubadora;
use App\Models\ModoControlIncubadora;
use App\Models\TipoControlIncubadora;
use Illuminate\Http\Request;

class ControlIncubadoraController extends Controller
{
    public function index()
    {
        $controles = ControlIncubadora::with(['incubadora', 'tipo', 'modo', 'usuario'])
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $incubadoras = Incubadora::orderBy('nombre')->get();
        $tipos = TipoControlIncubadora::orderBy('nombre')->get();
        $modos = ModoControlIncubadora::orderBy('nombre')->get();

        return view('vistas_principales.administrador.controles_incubadora.index', compact(
            'controles',
            'incubadoras',
            'tipos',
            'modos'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_control_incubadora_id' => 'required|exists:tipos_control_incubadora,id',
            'modo_control_incubadora_id' => 'required|exists:modos_control_incubadora,id',
            'valor_aplicado' => 'nullable|numeric|min:0|max:999999.99',
            'fecha_hora' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        ControlIncubadora::create([
            'incubadora_id' => $request->incubadora_id,
            'tipo_control_incubadora_id' => $request->tipo_control_incubadora_id,
            'modo_control_incubadora_id' => $request->modo_control_incubadora_id,
            'valor_aplicado' => $request->valor_aplicado,
            'fecha_hora' => $request->fecha_hora,
            'user_id' => auth()->id(),
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Control de incubadora registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $control = ControlIncubadora::findOrFail($id);

        $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_control_incubadora_id' => 'required|exists:tipos_control_incubadora,id',
            'modo_control_incubadora_id' => 'required|exists:modos_control_incubadora,id',
            'valor_aplicado' => 'nullable|numeric|min:0|max:999999.99',
            'fecha_hora' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $control->update([
            'incubadora_id' => $request->incubadora_id,
            'tipo_control_incubadora_id' => $request->tipo_control_incubadora_id,
            'modo_control_incubadora_id' => $request->modo_control_incubadora_id,
            'valor_aplicado' => $request->valor_aplicado,
            'fecha_hora' => $request->fecha_hora,
            'user_id' => auth()->id(),
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('success', 'Control de incubadora actualizado correctamente.');
    }

    public function destroy($id)
    {
        $control = ControlIncubadora::findOrFail($id);
        $control->delete();

        return back()->with('success', 'Control de incubadora eliminado correctamente.');
    }
}
