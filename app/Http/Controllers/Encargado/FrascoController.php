<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Frasco;
use Illuminate\View\View;

class FrascoController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $frascos = Frasco::with([
            'lote.posicion.incubadora',
            'lote.especie',
            'estado',
        ])
            ->withCount('seguimientos')
            ->deEncargado($user->id)
            ->orderBy('lote_id')
            ->orderBy('numero_frasco')
            ->get();

        return view(
            'vistas_principales.encargado.frascos.index',
            compact('frascos')
        );
    }
}
