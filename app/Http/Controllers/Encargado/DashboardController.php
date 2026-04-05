<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\EvidenciaLote;
use App\Models\SeguimientoFrasco;
use App\Models\SeguimientoLote;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $seguimientosLoteTotal = SeguimientoLote::count();
        $seguimientosFrascoTotal = SeguimientoFrasco::count();
        $evidenciasTotal = EvidenciaLote::count();

        $alertasActivas = Alerta::whereHas('estado', function ($query) {
            $query->whereIn('clave', ['pendiente', 'en_revision']);
        })->count();

        $misSeguimientosLote = SeguimientoLote::with(['lote', 'usuario'])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $misSeguimientosFrasco = SeguimientoFrasco::with(['frasco', 'usuario'])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $ultimasEvidencias = EvidenciaLote::with(['seguimientoLote.lote'])
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $alertasRecientes = Alerta::with(['incubadora', 'tipo', 'nivel', 'estado'])
            ->orderByDesc('id')
            ->take(6)
            ->get();

        return view('vistas_principales.encargado.dashboard', compact(
            'user',
            'seguimientosLoteTotal',
            'seguimientosFrascoTotal',
            'evidenciasTotal',
            'alertasActivas',
            'misSeguimientosLote',
            'misSeguimientosFrasco',
            'ultimasEvidencias',
            'alertasRecientes'
        ));
    }
}
