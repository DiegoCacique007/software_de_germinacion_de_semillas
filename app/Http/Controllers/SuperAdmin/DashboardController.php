<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\ControlIncubadora;
use App\Models\Frasco;
use App\Models\Incubadora;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use App\Models\ModoControlIncubadora;
use App\Models\OrdenControlIncubadora;
use App\Models\TipoControlIncubadora;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $usuariosTotal = User::count();
        $incubadorasTotal = Incubadora::count();
        $lotesTotal = Lote::count();
        $frascosTotal = Frasco::count();

        $lecturasHoy = LecturaMicroclima::whereDate('fecha_hora', now()->toDateString())->count();

        $alertasActivas = Alerta::whereHas('estado', function ($query) {
            $query->whereIn('clave', ['pendiente', 'en_revision']);
        })->count();

        $controlesHoy = ControlIncubadora::whereDate('fecha_hora', now()->toDateString())->count();

        $ordenesPendientes = OrdenControlIncubadora::where('estado_orden', 'pendiente')->count();

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

        $ultimasLecturas = LecturaMicroclima::with('incubadora')
            ->orderByDesc('fecha_hora')
            ->take(8)
            ->get();

        $ultimosControles = ControlIncubadora::with(['incubadora', 'tipo', 'modo', 'usuario'])
            ->orderByDesc('fecha_hora')
            ->take(8)
            ->get();

        $ultimasAlertas = Alerta::with(['incubadora', 'tipo', 'nivel', 'estado', 'atendidaPor'])
            ->orderByDesc('fecha_hora')
            ->take(8)
            ->get();

        $tiposControl = TipoControlIncubadora::orderBy('nombre')->get();
        $modosControl = ModoControlIncubadora::orderBy('nombre')->get();

        $ordenesRecientes = OrdenControlIncubadora::with(['incubadora', 'tipo', 'modo', 'usuario'])
            ->orderByDesc('fecha_solicitud')
            ->take(8)
            ->get();

        return view('vistas_principales.super_admin.dashboard', compact(
            'usuariosTotal',
            'incubadorasTotal',
            'lotesTotal',
            'frascosTotal',
            'lecturasHoy',
            'alertasActivas',
            'controlesHoy',
            'ordenesPendientes',
            'incubadoras',
            'resumenIncubadoras',
            'ultimasLecturas',
            'ultimosControles',
            'ultimasAlertas',
            'tiposControl',
            'modosControl',
            'ordenesRecientes'
        ));
    }
}
