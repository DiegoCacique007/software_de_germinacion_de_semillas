<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use Illuminate\View\View;

class LoteController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $lotes = Lote::with([
            'posicion.incubadora',
            'especie',
            'estado',
        ])
            ->withCount('frascos')
            ->deEncargado($user->id)
            ->latest('fecha_siembra')
            ->get();

        return view(
            'vistas_principales.encargado.lotes.index',
            compact('lotes')
        );
    }
}
