<?php

namespace App\Http\Controllers;

use App\Models\Incubadora;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use App\Models\RegistroBiologico;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function microclimaPdf(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'incubadora_id' => 'nullable|exists:incubadoras,id',
        ]);

        $query = LecturaMicroclima::with('incubadora')
            ->orderByDesc('fecha_hora');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_fin);
        }

        if ($request->filled('incubadora_id')) {
            $query->where('incubadora_id', $request->incubadora_id);
        }

        $lecturas = $query->get();
        $incubadoras = Incubadora::orderBy('id')->get();

        $pdf = Pdf::loadView('reportes.microclima_pdf', [
            'lecturas' => $lecturas,
            'filtros' => $request->only(['fecha_inicio', 'fecha_fin', 'incubadora_id']),
            'incubadoras' => $incubadoras,
        ]);

        return $pdf->download('reporte_microclima.pdf');
    }

    public function biologicoPdf(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'lote_id' => 'nullable|exists:lotes,id',
        ]);

        $query = RegistroBiologico::with(['lote', 'usuario'])
            ->orderByDesc('fecha_registro');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_registro', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_registro', '<=', $request->fecha_fin);
        }

        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        $registros = $query->get();
        $lotes = Lote::orderBy('id')->get();

        $pdf = Pdf::loadView('reportes.biologico_pdf', [
            'registros' => $registros,
            'filtros' => $request->only(['fecha_inicio', 'fecha_fin', 'lote_id']),
            'lotes' => $lotes,
        ]);

        return $pdf->download('reporte_biologico.pdf');
    }
}
