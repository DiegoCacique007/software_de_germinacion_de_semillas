<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ControlIncubadora;
use App\Models\Incubadora;
use App\Models\ModoControlIncubadora;
use App\Models\TipoControlIncubadora;
use App\Models\User;
use Illuminate\Http\Request;

class ControlIncubadoraController extends Controller
{
    public function index()
    {
        $controles = ControlIncubadora::with(['incubadora', 'tipo', 'modo', 'user'])->latest('fecha_hora')->get();
        $incubadoras = Incubadora::orderBy('nombre')->get();
        $tipos = TipoControlIncubadora::orderBy('nombre')->get();
        $modos = ModoControlIncubadora::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('vistas_principales.super_admin.controles_microclima.index', compact('controles', 'incubadoras', 'tipos', 'modos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_control_incubadora_id' => 'required|exists:tipos_control_incubadora,id',
            'modo_control_incubadora_id' => 'required|exists:modos_control_incubadora,id',
            'valor_aplicado' => 'nullable|numeric',
            'fecha_hora' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
            'observaciones' => 'nullable|string',
        ]);

        ControlIncubadora::create($data);

        return redirect()->route('super_admin.controles-incubadora.index')->with('success', 'Control registrado correctamente.');
    }

    public function update(Request $request, ControlIncubadora $controlIncubadora)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_control_incubadora_id' => 'required|exists:tipos_control_incubadora,id',
            'modo_control_incubadora_id' => 'required|exists:modos_control_incubadora,id',
            'valor_aplicado' => 'nullable|numeric',
            'fecha_hora' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
            'observaciones' => 'nullable|string',
        ]);

        $controlIncubadora->update($data);

        return redirect()->route('super_admin.controles-incubadora.index')->with('success', 'Control actualizado correctamente.');
    }

    public function destroy(ControlIncubadora $controlIncubadora)
    {
        $controlIncubadora->delete();

        return redirect()->route('super_admin.controles-incubadora.index')->with('success', 'Control eliminado correctamente.');
    }
}
