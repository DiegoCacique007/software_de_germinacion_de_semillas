<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\AsignacionIncubadora;
use App\Models\Incubadora;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
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

        $incubadoraIds = $incubadoras->pluck('id');

        $asignaciones = AsignacionIncubadora::with([
            'incubadora.estado',
            'incubadora.ultimaLecturaMicroclima',
        ])
            ->deUsuario($user->id)
            ->vigentes()
            ->orderBy('fecha_inicio')
            ->get();

        $alertasActivas = Alerta::with([
            'incubadora',
            'lote',
            'lecturaMicroclima',
            'tipo',
            'nivel',
            'estado',
            'atendidaPor',
        ])
            ->deEncargado($user->id)
            ->whereHas('estado', fn($query) => $query->whereIn('clave', ['pendiente', 'atendida']))
            ->latest('fecha_hora')
            ->get();

        $alertasPrioritarias = $alertasActivas->take(5);

        $alertasActivasPorIncubadora = $alertasActivas
            ->groupBy('incubadora_id')
            ->map(fn($alertas) => $alertas->count());

        $lecturasHoy = LecturaMicroclima::whereIn('incubadora_id', $incubadoraIds)
            ->whereDate('fecha_hora', now('America/Mexico_City')->toDateString())
            ->count();

        $lecturasRecientes = LecturaMicroclima::whereIn('incubadora_id', $incubadoraIds)
            ->latest('fecha_hora')
            ->limit(8)
            ->get();

        $ultimaLectura = $lecturasRecientes->first();

        $lotesEnSeguimiento = Lote::whereHas('posicion', function ($query) use ($incubadoraIds) {
            $query->whereIn('incubadora_id', $incubadoraIds);
        })->count();

        $totalIncubadoras = $incubadoras->count();
        $totalAlertasActivas = $alertasActivas->count();

        $incubadorasConLectura = $incubadoras
            ->filter(fn($incubadora) => $incubadora->ultimaLecturaMicroclima)
            ->count();

        $nombresIncubadoras = $incubadoras->pluck('nombre', 'id');

        return view('vistas_principales.encargado.dashboard', compact(
            'user',
            'incubadoras',
            'asignaciones',
            'alertasActivas',
            'alertasPrioritarias',
            'alertasActivasPorIncubadora',
            'lecturasHoy',
            'lecturasRecientes',
            'ultimaLectura',
            'lotesEnSeguimiento',
            'totalIncubadoras',
            'totalAlertasActivas',
            'incubadorasConLectura',
            'nombresIncubadoras'
        ));
    }
}
