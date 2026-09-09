<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Incubadora;
use Illuminate\View\View;

class IncubadoraController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $incubadoras = Incubadora::with([
            'estado',
            'ultimaLecturaMicroclima',
            'asignaciones' => fn($query) => $query
                ->deUsuario($user->id)
                ->vigentes(),
        ])
            ->asignadasA($user->id)
            ->orderBy('nombre')
            ->get();

        return view('vistas_principales.encargado.incubadoras.index', compact('incubadoras'));
    }

    public function show(int $incubadora): View
    {
        $user = auth()->user();

        $incubadora = Incubadora::with([
            'estado',
            'ultimaLecturaMicroclima',
            'asignaciones' => fn($query) => $query
                ->deUsuario($user->id)
                ->vigentes(),
        ])
            ->asignadasA($user->id)
            ->findOrFail($incubadora);

        $lecturas = $incubadora->lecturasMicroclima()
            ->orderByDesc('fecha_hora')
            ->limit(50)
            ->get();

        return view('vistas_principales.encargado.incubadoras.show', compact(
            'incubadora',
            'lecturas'
        ));
    }
}
