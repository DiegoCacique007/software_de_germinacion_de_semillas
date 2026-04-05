<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\ControlIncubadora;
use App\Models\Incubadora;
use App\Models\LecturaMicroclima;

class DashboardController extends Controller
{
    public function index()
    {
        $incubadoras = Incubadora::with('estado')
            ->orderBy('nombre')
            ->get();

        $resumenIncubadoras = $incubadoras->map(function ($incubadora) {
            $ultimaLectura = LecturaMicroclima::where('incubadora_id', $incubadora->id)
                ->orderByDesc('fecha_hora')
                ->first();

            $alertasAbiertas = Alerta::where('incubadora_id', $incubadora->id)
                ->whereHas('estado', function ($query) {
                    $query->whereIn('clave', ['pendiente', 'en_revision']);
                })
                ->count();

            return [
                'incubadora' => $incubadora,
                'ultima_lectura' => $ultimaLectura,
                'alertas_abiertas' => $alertasAbiertas,
            ];
        });

        $lecturasHoy = LecturaMicroclima::whereDate('fecha_hora', now()->toDateString())->count();

        $controlesHoy = ControlIncubadora::whereDate('fecha_hora', now()->toDateString())->count();

        $alertasActivas = Alerta::whereHas('estado', function ($query) {
            $query->whereIn('clave', ['pendiente', 'en_revision']);
        })->count();

        $ultimaLecturaGlobal = LecturaMicroclima::with('incubadora')
            ->orderByDesc('fecha_hora')
            ->first();

        $temperaturaPromedioActual = $resumenIncubadoras
            ->filter(fn ($item) => $item['ultima_lectura'])
            ->avg(fn ($item) => (float) $item['ultima_lectura']->temperatura);

        $humedadPromedioActual = $resumenIncubadoras
            ->filter(fn ($item) => $item['ultima_lectura'])
            ->avg(fn ($item) => (float) $item['ultima_lectura']->humedad);

        $ultimasLecturas = LecturaMicroclima::with('incubadora')
            ->orderByDesc('fecha_hora')
            ->take(8)
            ->get();

        $ultimosControles = ControlIncubadora::with(['incubadora', 'tipo', 'modo', 'usuario'])
            ->orderByDesc('fecha_hora')
            ->take(6)
            ->get();

        $ultimasAlertas = Alerta::with(['incubadora', 'tipo', 'nivel', 'estado', 'atendidaPor'])
            ->orderByDesc('fecha_hora')
            ->take(6)
            ->get();

        return view('vistas_principales.administrador.dashboard', compact(
            'incubadoras',
            'resumenIncubadoras',
            'lecturasHoy',
            'controlesHoy',
            'alertasActivas',
            'ultimaLecturaGlobal',
            'temperaturaPromedioActual',
            'humedadPromedioActual',
            'ultimasLecturas',
            'ultimosControles',
            'ultimasAlertas'
        ));
    }
}
