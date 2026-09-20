<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\CondicionOptimaEspecie;
use App\Models\EstadoAlerta;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use App\Models\NivelAlerta;
use App\Models\TipoAlerta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class MicroclimaAlertService
{
    public function evaluarLectura(LecturaMicroclima $lectura): int
    {
        // La lectura ya está guardada. Si falla esta evaluación, solo se revierten los cambios de alertas.
        return DB::transaction(function () use ($lectura) {
            $lotes = Lote::with('especie')
                ->whereHas('posicion', fn ($q) => $q->where('incubadora_id', $lectura->incubadora_id))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($lotes->isEmpty()) return 0;

            $estados = EstadoAlerta::whereIn('clave', ['pendiente', 'atendida', 'resuelta'])->get()->keyBy('clave');
            $tipos = TipoAlerta::whereIn('clave', ['temperatura', 'humedad'])->get()->keyBy('clave');
            $niveles = NivelAlerta::whereIn('clave', ['bajo', 'medio', 'alto'])->get()->keyBy('clave');

            if ($estados->count() !== 3 || $tipos->count() !== 2 || $niveles->count() !== 3) {
                throw new RuntimeException('Faltan catálogos requeridos para las alertas de microclima.');
            }

            $estadoPendiente = $estados->get('pendiente');
            $estadoAtendida = $estados->get('atendida');
            $estadoResuelta = $estados->get('resuelta');

            $condiciones = CondicionOptimaEspecie::whereIn('especie_id', $lotes->pluck('especie_id')->unique())
                ->orderBy('id')
                ->get()
                ->groupBy('especie_id')
                ->map->first();

            $alertasActivas = Alerta::whereIn('lote_id', $lotes->modelKeys())
                ->whereIn('tipo_alerta_id', $tipos->modelKeys())
                ->whereIn('estado_alerta_id', [$estadoPendiente->id, $estadoAtendida->id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->groupBy(fn ($alerta) => $alerta->lote_id.':'.$alerta->tipo_alerta_id);

            $generadas = 0;

            foreach ($lotes as $lote) {
                $condicion = $condiciones->get($lote->especie_id);

                if (!$condicion) {
                    throw new RuntimeException("Faltan condiciones óptimas para la especie {$lote->especie_id}.");
                }

                if (!$lote->especie) {
                    throw new RuntimeException("El lote {$lote->id} no tiene una especie válida asociada.");
                }

                foreach ([
                             'temperatura' => ['Temperatura', '°C'],
                             'humedad' => ['Humedad', '%'],
                         ] as $variable => [$etiqueta, $unidad]) {
                    $valor = (float) $lectura->{$variable};
                    $min = (float) $condicion->{$variable.'_min'};
                    $max = (float) $condicion->{$variable.'_max'};

                    if ($min > $max) {
                        throw new RuntimeException("Rango de {$variable} inválido para la especie {$lote->especie_id}.");
                    }

                    $tipo = $tipos->get($variable);
                    $clave = $lote->id.':'.$tipo->id;
                    $activas = $alertasActivas->get($clave, collect());
                    $dentroDeRango = $valor >= $min && $valor <= $max;

                    /*
                    |--------------------------------------------------------------------------
                    | Variable dentro del rango: resolver alertas activas
                    |--------------------------------------------------------------------------
                    */
                    if ($dentroDeRango) {
                        foreach ($activas as $alerta) {
                            $alerta->update([
                                'estado_alerta_id' => $estadoResuelta->id,
                                'fecha_resolucion' => $lectura->fecha_hora,
                            ]);
                        }

                        $alertasActivas->forget($clave);
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Variable fuera del rango: no duplicar una incidencia activa
                    |--------------------------------------------------------------------------
                    */
                    if ($activas->isNotEmpty()) continue;

                    $alerta = Alerta::create([
                        'incubadora_id' => $lectura->incubadora_id,
                        'lote_id' => $lote->id,
                        'lectura_microclima_id' => $lectura->id,
                        'tipo_alerta_id' => $tipo->id,
                        'nivel_alerta_id' => $niveles->get($this->claveNivel($valor, $min, $max))->id,
                        'estado_alerta_id' => $estadoPendiente->id,
                        'mensaje' => Str::limit(
                            "{$etiqueta} fuera de rango para el lote {$lote->codigo_lote} ({$lote->especie->nombre_comun}). Lectura: {$valor} {$unidad}. Rango esperado: {$min} - {$max} {$unidad}.",
                            255,
                            ''
                        ),
                        'fecha_hora' => $lectura->fecha_hora,
                        'fecha_atencion' => null,
                        'fecha_resolucion' => null,
                        'atendida_por' => null,
                        'observaciones' => 'Generada automáticamente por lectura de sensor.',
                    ]);

                    $alertasActivas->put($clave, collect([$alerta]));
                    $generadas++;
                }
            }

            return $generadas;
        }, 3);
    }

    private function claveNivel(float $valor, float $min, float $max): string
    {
        $desviacion = max($min - $valor, $valor - $max, 0);
        $porcentaje = ($desviacion / max($max - $min, 1)) * 100;

        return $porcentaje >= 50 ? 'alto' : ($porcentaje >= 20 ? 'medio' : 'bajo');
    }
}
