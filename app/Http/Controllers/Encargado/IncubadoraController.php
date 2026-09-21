<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Incubadora;
use App\Models\Lote;
use Illuminate\View\View;

class IncubadoraController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $incubadoras = Incubadora::with([
            'estado',
            'ultimaLecturaMicroclima',
            'asignaciones' => fn($q) => $q->deUsuario($user->id)->vigentes(),
        ])
            ->asignadasA($user->id)
            ->orderBy('nombre')
            ->get();

        return view(
            'vistas_principales.encargado.incubadoras.index',
            compact('incubadoras')
        );
    }

    public function show(int $incubadora): View
    {
        $user = auth()->user();

        $incubadora = Incubadora::with([
            'estado',
            'ultimaLecturaMicroclima',
            'asignaciones' => fn($q) => $q->deUsuario($user->id)->vigentes(),
        ])
            ->asignadasA($user->id)
            ->findOrFail($incubadora);

        $lecturas = $incubadora->lecturasMicroclima()
            ->latest('fecha_hora')
            ->limit(50)
            ->get();

        $alertas = Alerta::with(['tipo', 'nivel', 'estado'])
            ->where('incubadora_id', $incubadora->id)
            ->whereHas('estado', fn($q) => $q->whereIn('clave', ['pendiente', 'atendida']))
            ->latest('fecha_hora')
            ->limit(5)
            ->get();

        $lotes = Lote::with(['especie', 'estado', 'posicion'])
            ->withCount('frascos')
            ->whereHas('posicion', fn($q) => $q->where('incubadora_id', $incubadora->id))
            ->latest('fecha_siembra')
            ->get();

        return view(
            'vistas_principales.encargado.incubadoras.show',
            compact('incubadora', 'lecturas', 'alertas', 'lotes')
        );
    }
}
