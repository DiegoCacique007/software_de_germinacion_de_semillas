<?php

namespace App\Http\Controllers;

use App\Models\Incubadora;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use App\Models\RegistroBiologico;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReporteController extends Controller
{
    private int $limiteDetalle=300;

    public function microclimaPdf(Request $request)
    {
        $validated=$this->validarMicroclima($request);
        $query=$this->queryMicroclima($validated);

        $estadisticas=(clone $query)
            ->selectRaw('
                COUNT(*) as total,
                COUNT(DISTINCT incubadora_id) as incubadoras,
                AVG(temperatura) as temperatura_promedio,
                MIN(temperatura) as temperatura_min,
                MAX(temperatura) as temperatura_max,
                AVG(humedad) as humedad_promedio,
                MIN(humedad) as humedad_min,
                MAX(humedad) as humedad_max,
                MIN(fecha_hora) as primera_lectura,
                MAX(fecha_hora) as ultima_lectura
            ')
            ->first();

        $resumen=[
            'total'=>(int)($estadisticas->total??0),
            'incubadoras'=>(int)($estadisticas->incubadoras??0),
            'temperatura_promedio'=>$estadisticas->temperatura_promedio!==null?(float)$estadisticas->temperatura_promedio:null,
            'temperatura_min'=>$estadisticas->temperatura_min!==null?(float)$estadisticas->temperatura_min:null,
            'temperatura_max'=>$estadisticas->temperatura_max!==null?(float)$estadisticas->temperatura_max:null,
            'humedad_promedio'=>$estadisticas->humedad_promedio!==null?(float)$estadisticas->humedad_promedio:null,
            'humedad_min'=>$estadisticas->humedad_min!==null?(float)$estadisticas->humedad_min:null,
            'humedad_max'=>$estadisticas->humedad_max!==null?(float)$estadisticas->humedad_max:null,
            'primera_lectura'=>$estadisticas->primera_lectura,
            'ultima_lectura'=>$estadisticas->ultima_lectura,
        ];

        $lecturas=(clone $query)
            ->with('incubadora:id,codigo,nombre')
            ->orderByDesc('fecha_hora')
            ->orderByDesc('id')
            ->limit($this->limiteDetalle)
            ->get();

        $incubadoraSeleccionada=!empty($validated['incubadora_id'])
            ?Incubadora::find($validated['incubadora_id'])
            :null;

        $codigo=$incubadoraSeleccionada?->codigo??'todas';

        $archivo='microseed_microclima_'
            .Str::slug($codigo,'_')
            .'_'
            .now('America/Mexico_City')->format('Y-m-d_H-i-s')
            .'.pdf';

        return Pdf::loadView('reportes.microclima_pdf',[
            'lecturas'=>$lecturas,
            'filtros'=>$validated,
            'incubadoraSeleccionada'=>$incubadoraSeleccionada,
            'resumen'=>$resumen,
            'limiteDetalle'=>$this->limiteDetalle,
            'detalleLimitado'=>$resumen['total']>$this->limiteDetalle,
        ])
            ->setPaper('a4','landscape')
            ->download($archivo);
    }

    public function microclimaCsv(Request $request)
    {
        $validated=$this->validarMicroclima($request);
        $query=$this->queryMicroclima($validated);

        $incubadoraSeleccionada=!empty($validated['incubadora_id'])
            ?Incubadora::find($validated['incubadora_id'])
            :null;

        $codigo=$incubadoraSeleccionada?->codigo??'todas';

        $archivo='microseed_microclima_completo_'
            .Str::slug($codigo,'_')
            .'_'
            .now('America/Mexico_City')->format('Y-m-d_H-i-s')
            .'.csv';

        return response()->streamDownload(function() use($query){
            $handle=fopen('php://output','w');

            fwrite($handle,"\xEF\xBB\xBF");

            fputcsv($handle,[
                'ID',
                'Código incubadora',
                'Incubadora',
                'Temperatura (°C)',
                'Humedad relativa (%)',
                'Fecha y hora',
                'Observaciones',
            ],';');

            (clone $query)
                ->with('incubadora:id,codigo,nombre')
                ->chunkById(1000,function($lecturas) use($handle){
                    foreach($lecturas as $lectura){
                        fputcsv($handle,[
                            $lectura->id,
                            $this->csvSeguro($lectura->incubadora?->codigo??''),
                            $this->csvSeguro($lectura->incubadora?->nombre??''),
                            $lectura->temperatura,
                            $lectura->humedad,
                            $lectura->fecha_hora,
                            $this->csvSeguro($lectura->observaciones??''),
                        ],';');
                    }

                    fflush($handle);
                });

            fclose($handle);

        },$archivo,[
            'Content-Type'=>'text/csv; charset=UTF-8',
            'Content-Disposition'=>'attachment; filename="'.$archivo.'"',
            'Cache-Control'=>'no-store, no-cache, must-revalidate',
        ]);
    }

    public function biologicoPdf(Request $request)
    {
        $validated=$this->validarBiologico($request);
        $query=$this->queryBiologico($validated);

        $estadisticas=(clone $query)
            ->selectRaw('
                COUNT(*) as total,
                COUNT(DISTINCT lote_id) as lotes,
                AVG(tasa_germinacion) as germinacion_promedio,
                MIN(tasa_germinacion) as germinacion_min,
                MAX(tasa_germinacion) as germinacion_max,
                AVG(dias_estratificacion) as estratificacion_promedio,
                MIN(fecha_registro) as primer_registro,
                MAX(fecha_registro) as ultimo_registro
            ')
            ->first();

        $resumen=[
            'total'=>(int)($estadisticas->total??0),
            'lotes'=>(int)($estadisticas->lotes??0),
            'germinacion_promedio'=>$estadisticas->germinacion_promedio!==null?(float)$estadisticas->germinacion_promedio:null,
            'germinacion_min'=>$estadisticas->germinacion_min!==null?(float)$estadisticas->germinacion_min:null,
            'germinacion_max'=>$estadisticas->germinacion_max!==null?(float)$estadisticas->germinacion_max:null,
            'estratificacion_promedio'=>$estadisticas->estratificacion_promedio!==null?(float)$estadisticas->estratificacion_promedio:null,
            'primer_registro'=>$estadisticas->primer_registro,
            'ultimo_registro'=>$estadisticas->ultimo_registro,
        ];

        $registros=(clone $query)
            ->with([
                'lote:id,especie_id,codigo_lote',
                'lote.especie:id,nombre_comun,nombre_cientifico',
                'usuario:id,name'
            ])
            ->orderByDesc('fecha_registro')
            ->orderByDesc('id')
            ->limit($this->limiteDetalle)
            ->get();

        $loteSeleccionado=!empty($validated['lote_id'])
            ?Lote::with('especie:id,nombre_comun,nombre_cientifico')
                ->find($validated['lote_id'])
            :null;

        $codigo=$loteSeleccionado?->codigo_lote??'todos';

        $archivo='microseed_biologico_'
            .Str::slug($codigo,'_')
            .'_'
            .now('America/Mexico_City')->format('Y-m-d_H-i-s')
            .'.pdf';

        return Pdf::loadView('reportes.biologico_pdf',[
            'registros'=>$registros,
            'filtros'=>$validated,
            'loteSeleccionado'=>$loteSeleccionado,
            'resumen'=>$resumen,
            'limiteDetalle'=>$this->limiteDetalle,
            'detalleLimitado'=>$resumen['total']>$this->limiteDetalle,
        ])
            ->setPaper('a4','landscape')
            ->download($archivo);
    }

    public function biologicoCsv(Request $request)
    {
        $validated=$this->validarBiologico($request);
        $query=$this->queryBiologico($validated);

        $loteSeleccionado=!empty($validated['lote_id'])
            ?Lote::find($validated['lote_id'])
            :null;

        $codigo=$loteSeleccionado?->codigo_lote??'todos';

        $archivo='microseed_biologico_completo_'
            .Str::slug($codigo,'_')
            .'_'
            .now('America/Mexico_City')->format('Y-m-d_H-i-s')
            .'.csv';

        return response()->streamDownload(function() use($query){
            $handle=fopen('php://output','w');

            fwrite($handle,"\xEF\xBB\xBF");

            fputcsv($handle,[
                'ID',
                'Lote',
                'Especie',
                'Nombre científico',
                'Fecha de registro',
                'Días de estratificación',
                'Tasa de germinación (%)',
                'Responsable',
                'Observaciones',
            ],';');

            (clone $query)
                ->with([
                    'lote:id,especie_id,codigo_lote',
                    'lote.especie:id,nombre_comun,nombre_cientifico',
                    'usuario:id,name'
                ])
                ->chunkById(1000,function($registros) use($handle){
                    foreach($registros as $registro){
                        fputcsv($handle,[
                            $registro->id,
                            $this->csvSeguro($registro->lote?->codigo_lote??''),
                            $this->csvSeguro($registro->lote?->especie?->nombre_comun??''),
                            $this->csvSeguro($registro->lote?->especie?->nombre_cientifico??''),
                            $registro->fecha_registro,
                            $registro->dias_estratificacion,
                            $registro->tasa_germinacion,
                            $this->csvSeguro($registro->usuario?->name??''),
                            $this->csvSeguro($registro->observaciones??''),
                        ],';');
                    }

                    fflush($handle);
                });

            fclose($handle);

        },$archivo,[
            'Content-Type'=>'text/csv; charset=UTF-8',
            'Content-Disposition'=>'attachment; filename="'.$archivo.'"',
            'Cache-Control'=>'no-store, no-cache, must-revalidate',
        ]);
    }

    private function validarMicroclima(Request $request):array
    {
        return $request->validate([
            'fecha_inicio'=>['nullable','date'],
            'fecha_fin'=>['nullable','date','after_or_equal:fecha_inicio'],
            'incubadora_id'=>['nullable','integer','exists:incubadoras,id'],
        ]);
    }

    private function validarBiologico(Request $request):array
    {
        return $request->validate([
            'fecha_inicio'=>['nullable','date'],
            'fecha_fin'=>['nullable','date','after_or_equal:fecha_inicio'],
            'lote_id'=>['nullable','integer','exists:lotes,id'],
        ]);
    }

    private function queryMicroclima(array $validated)
    {
        $query=LecturaMicroclima::query();

        if(!empty($validated['fecha_inicio'])){
            $query->whereDate('fecha_hora','>=',$validated['fecha_inicio']);
        }

        if(!empty($validated['fecha_fin'])){
            $query->whereDate('fecha_hora','<=',$validated['fecha_fin']);
        }

        if(!empty($validated['incubadora_id'])){
            $query->where('incubadora_id',$validated['incubadora_id']);
        }

        return $query;
    }

    private function queryBiologico(array $validated)
    {
        $query=RegistroBiologico::query();

        if(!empty($validated['fecha_inicio'])){
            $query->whereDate('fecha_registro','>=',$validated['fecha_inicio']);
        }

        if(!empty($validated['fecha_fin'])){
            $query->whereDate('fecha_registro','<=',$validated['fecha_fin']);
        }

        if(!empty($validated['lote_id'])){
            $query->where('lote_id',$validated['lote_id']);
        }

        return $query;
    }

    private function csvSeguro($valor)
    {
        if(!is_string($valor)){
            return $valor;
        }

        $valor=trim($valor);

        if(preg_match('/^[=\-+@]/',$valor)){
            return "'".$valor;
        }

        return $valor;
    }
}
