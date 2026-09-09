<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\AsignacionIncubadora;
use App\Models\Incubadora;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $incubadoras = Incubadora::with([
            'estado',
            'ultimaLecturaMicroclima',
        ])
            ->asignadasA($user->id)
            ->orderBy('nombre')
            ->get();

        $asignaciones = AsignacionIncubadora::with([
            'incubadora.estado',
            'incubadora.ultimaLecturaMicroclima',
        ])
            ->deUsuario($user->id)
            ->vigentes()
            ->orderBy('fecha_inicio')
            ->get();

        $totalIncubadoras = $incubadoras->count();

        $incubadorasConLectura = $incubadoras
            ->filter(fn($incubadora) => $incubadora->ultimaLecturaMicroclima)
            ->count();

        $ultimaLectura = $incubadoras
            ->pluck('ultimaLecturaMicroclima')
            ->filter()
            ->sortByDesc('fecha_hora')
            ->first();

        return view('vistas_principales.encargado.dashboard', compact(
            'user',
            'incubadoras',
            'asignaciones',
            'totalIncubadoras',
            'incubadorasConLectura',
            'ultimaLectura'
        ));
    }
}
