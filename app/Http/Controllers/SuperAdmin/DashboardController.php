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
use App\Models\TipoControlIncubadora;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $incubadoraActualId = 106;

        $usuariosTotal = User::count();
        $incubadorasTotal = Incubadora::count();
        $lotesTotal = Lote::count();
        $frascosTotal = Frasco::count();

        $lecturasHoy = LecturaMicroclima::whereDate('fecha_hora', now('America/Mexico_City')->toDateString())->count();

        $alertasActivas = Alerta::whereHas('estado', function ($query) {
            $query->whereIn('clave', ['pendiente', 'en_revision']);
        })->count();

        $controlesHoy = ControlIncubadora::whereDate('fecha_hora', now('America/Mexico_City')->toDateString())->count();

        $incubadoras = Incubadora::with('estado')
            ->orderBy('nombre')
            ->get();

        if (!$incubadoras->contains('id', $incubadoraActualId) && $incubadoras->isNotEmpty()) {
            $incubadoraActualId = $incubadoras->first()->id;
        }

        $resumenIncubadoras = $incubadoras->map(function ($incubadora) {
            $ultimaLectura = LecturaMicroclima::where('incubadora_id', $incubadora->id)
                ->orderByDesc('id')
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
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $ultimosControles = ControlIncubadora::with(['incubadora', 'tipo', 'modo', 'usuario'])
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $ultimasAlertas = Alerta::with(['incubadora', 'tipo', 'nivel', 'estado', 'atendidaPor'])
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $tiposControl = TipoControlIncubadora::orderBy('nombre')->get();
        $modosControl = ModoControlIncubadora::orderBy('nombre')->get();

        return view('vistas_principales.super_admin.dashboard', compact(
            'incubadoraActualId',
            'usuariosTotal',
            'incubadorasTotal',
            'lotesTotal',
            'frascosTotal',
            'lecturasHoy',
            'alertasActivas',
            'controlesHoy',
            'incubadoras',
            'resumenIncubadoras',
            'ultimasLecturas',
            'ultimosControles',
            'ultimasAlertas',
            'tiposControl',
            'modosControl'
        ));
    }

    public function tiempoReal(): JsonResponse
    {
        $incubadoraId = request()->integer('incubadora_id', 106);

        $historial = LecturaMicroclima::where('incubadora_id', $incubadoraId)
            ->orderByDesc('id')
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        $ultima = $historial->last();

        $incubadoras = Incubadora::with('estado')
            ->orderBy('nombre')
            ->get();

        $resumenIncubadoras = $incubadoras->map(function ($incubadora) {
            $lectura = LecturaMicroclima::where('incubadora_id', $incubadora->id)
                ->orderByDesc('id')
                ->first();

            $alertas = Alerta::where('incubadora_id', $incubadora->id)
                ->whereHas('estado', function ($query) {
                    $query->whereIn('clave', ['pendiente', 'en_revision']);
                })
                ->count();

            return [
                'id' => $incubadora->id,
                'nombre' => $incubadora->nombre,
                'codigo' => $incubadora->codigo,
                'estado' => $incubadora->estado->nombre ?? 'Sin estado',
                'alertas_abiertas' => $alertas,
                'temperatura' => $lectura ? number_format((float) $lectura->temperatura, 1, '.', '') : null,
                'humedad' => $lectura ? number_format((float) $lectura->humedad, 1, '.', '') : null,
                'fecha' => $lectura ? $this->formatearFecha($lectura->fecha_hora, 'd/m/Y H:i:s') : null,
            ];
        })->values();

        $ultimasLecturas = LecturaMicroclima::with('incubadora')
            ->orderByDesc('id')
            ->take(8)
            ->get()
            ->map(function ($lectura) {
                return [
                    'incubadora' => $lectura->incubadora->nombre ?? 'Sin incubadora',
                    'temperatura' => number_format((float) $lectura->temperatura, 1, '.', ''),
                    'humedad' => number_format((float) $lectura->humedad, 1, '.', ''),
                    'fecha' => $this->formatearFecha($lectura->fecha_hora, 'd/m H:i:s'),
                ];
            })
            ->values();

        $ultimosControles = ControlIncubadora::with(['incubadora', 'tipo', 'modo', 'usuario'])
            ->orderByDesc('id')
            ->take(8)
            ->get()
            ->map(function ($control) {
                return [
                    'incubadora' => $control->incubadora->nombre ?? 'Sin incubadora',
                    'tipo' => $control->tipo->nombre ?? 'Sin tipo',
                    'modo' => $control->modo->nombre ?? 'Sin modo',
                    'usuario' => $control->usuario->name ?? 'Sin usuario',
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,

            'metricas' => [
                'usuarios_total' => User::count(),
                'incubadoras_total' => Incubadora::count(),
                'lecturas_hoy' => LecturaMicroclima::whereDate('fecha_hora', now('America/Mexico_City')->toDateString())->count(),
                'alertas_activas' => Alerta::whereHas('estado', function ($query) {
                    $query->whereIn('clave', ['pendiente', 'en_revision']);
                })->count(),
                'controles_hoy' => ControlIncubadora::whereDate('fecha_hora', now('America/Mexico_City')->toDateString())->count(),
                'lotes_total' => Lote::count(),
                'frascos_total' => Frasco::count(),
            ],

            'dht22' => [
                'id' => $ultima?->id,
                'temperatura' => $ultima ? number_format((float) $ultima->temperatura, 1, '.', '') : null,
                'humedad' => $ultima ? number_format((float) $ultima->humedad, 1, '.', '') : null,
                'fecha_hora' => $ultima ? $this->formatearFecha($ultima->fecha_hora, 'H:i:s') : '--:--:--',
                'fecha_completa' => $ultima ? $this->formatearFecha($ultima->fecha_hora, 'd/m/Y H:i:s') : null,
            ],

            'grafica' => [
                'labels' => $historial->map(function ($lectura) {
                    return $this->formatearFecha($lectura->fecha_hora, 'H:i:s');
                })->values()->all(),

                'temperaturas' => $historial->map(function ($lectura) {
                    return round((float) $lectura->temperatura, 1);
                })->values()->all(),

                'humedades' => $historial->map(function ($lectura) {
                    return round((float) $lectura->humedad, 1);
                })->values()->all(),
            ],

            'resumen_incubadoras' => $resumenIncubadoras,
            'ultimas_lecturas' => $ultimasLecturas,
            'ultimos_controles' => $ultimosControles,
        ]);
    }

    public function getUltimaLectura(Incubadora $incubadora): JsonResponse
    {
        $historial = LecturaMicroclima::where('incubadora_id', $incubadora->id)
            ->orderByDesc('id')
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        if ($historial->isEmpty()) {
            return response()->json([
                'ok' => false,
                'message' => 'No hay lecturas registradas.',
                'grafica' => [
                    'labels' => [],
                    'temperaturas' => [],
                    'humedades' => [],
                ],
            ], 404);
        }

        $lectura = $historial->last();

        return response()->json([
            'ok' => true,
            'temperatura' => number_format((float) $lectura->temperatura, 1, '.', ''),
            'humedad' => number_format((float) $lectura->humedad, 1, '.', ''),
            'fecha_hora' => $this->formatearFecha($lectura->fecha_hora, 'H:i:s'),
            'fecha_completa' => $this->formatearFecha($lectura->fecha_hora, 'd/m/Y H:i:s'),

            'grafica' => [
                'labels' => $historial->map(function ($item) {
                    return $this->formatearFecha($item->fecha_hora, 'H:i:s');
                })->values()->all(),

                'temperaturas' => $historial->map(function ($item) {
                    return round((float) $item->temperatura, 1);
                })->values()->all(),

                'humedades' => $historial->map(function ($item) {
                    return round((float) $item->humedad, 1);
                })->values()->all(),
            ],
        ]);
    }

    private function formatearFecha($fecha, string $formato): string
    {
        return Carbon::parse($fecha)->format($formato);
    }
}
