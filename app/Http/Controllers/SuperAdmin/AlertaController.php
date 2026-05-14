<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\EstadoAlerta;
use App\Models\Incubadora;
use App\Models\NivelAlerta;
use App\Models\TipoAlerta;
use App\Models\User;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        $alertas = Alerta::with(['incubadora', 'tipo', 'nivel', 'estado', 'atendidaPor'])->latest('fecha_hora')->get();
        $incubadoras = Incubadora::orderBy('nombre')->get();
        $tipos = TipoAlerta::orderBy('nombre')->get();
        $niveles = NivelAlerta::orderBy('nombre')->get();
        $estados = EstadoAlerta::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('vistas_principales.super_admin.alertas.index', compact('alertas', 'incubadoras', 'tipos', 'niveles', 'estados', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_alerta_id' => 'required|exists:tipos_alerta,id',
            'nivel_alerta_id' => 'required|exists:niveles_alerta,id',
            'estado_alerta_id' => 'required|exists:estados_alerta,id',
            'mensaje' => 'required|string|max:255',
            'fecha_hora' => 'required|date',
            'atendida_por' => 'nullable|exists:users,id',
            'observaciones' => 'nullable|string',
        ]);

        Alerta::create($data);

        return redirect()->route('super_admin.alertas.index')->with('success', 'Alerta registrada correctamente.');
    }

    public function update(Request $request, Alerta $alerta)
    {
        $data = $request->validate([
            'incubadora_id' => 'required|exists:incubadoras,id',
            'tipo_alerta_id' => 'required|exists:tipos_alerta,id',
            'nivel_alerta_id' => 'required|exists:niveles_alerta,id',
            'estado_alerta_id' => 'required|exists:estados_alerta,id',
            'mensaje' => 'required|string|max:255',
            'fecha_hora' => 'required|date',
            'atendida_por' => 'nullable|exists:users,id',
            'observaciones' => 'nullable|string',
        ]);

        $alerta->update($data);

        return redirect()->route('super_admin.alertas.index')->with('success', 'Alerta actualizada correctamente.');
    }

    public function destroy(Alerta $alerta)
    {
        $alerta->delete();

        return redirect()->route('super_admin.alertas.index')->with('success', 'Alerta eliminada correctamente.');
    }
}
