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
        // La lectura ya está guardada. Solo las alertas se revierten si falla la evaluación.
        return DB::transaction(function () use ($lectura) {
            // Serializa evaluaciones concurrentes de los mismos lotes, incluso sin alertas previas.
            $lotes = Lote::with('especie')
                ->whereHas('posicion', fn ($q) => $q->where('incubadora_id', $lectura->incubadora_id))
                ->orderBy('id')->lockForUpdate()->get();

            if ($lotes->isEmpty()) {
                return 0;
            }

            $estado = EstadoAlerta::where('clave', 'pendiente')->first();
            $tipos = TipoAlerta::whereIn('clave', ['temperatura', 'humedad'])->get()->keyBy('clave');
            $niveles = NivelAlerta::whereIn('clave', ['bajo', 'medio', 'alto'])->get()->keyBy('clave');
            if (!$estado || $tipos->count() !== 2 || $niveles->count() !== 3) {
                throw new RuntimeException('Faltan catálogos de alertas: pendiente, temperatura/humedad o niveles bajo/medio/alto.');
            }

            // Conserva la primera condición por especie; el esquema permite varias.
            $condiciones = CondicionOptimaEspecie::whereIn('especie_id', $lotes->pluck('especie_id')->unique())
                ->orderBy('id')->get()->groupBy('especie_id')->map->first();
            // Lectura bloqueante: observa las alertas confirmadas por la evaluación anterior.
            $abiertas = Alerta::whereIn('lote_id', $lotes->modelKeys())
                ->whereIn('tipo_alerta_id', $tipos->modelKeys())
                ->where('estado_alerta_id', $estado->id)
                ->lockForUpdate()->get()->keyBy(fn ($alerta) => $alerta->lote_id.':'.$alerta->tipo_alerta_id);

            $generadas = 0;
            foreach ($lotes as $lote) {
                $condicion = $condiciones->get($lote->especie_id);
                if (!$condicion) {
                    throw new RuntimeException("Faltan condiciones óptimas para la especie {$lote->especie_id}.");
                }

                foreach (['temperatura' => ['Temperatura', '°C'], 'humedad' => ['Humedad', '%']] as $variable => [$etiqueta, $unidad]) {
                    $valor = (float) $lectura->{$variable};
                    $min = (float) $condicion->{$variable.'_min'};
                    $max = (float) $condicion->{$variable.'_max'};
                    if ($min > $max) {
                        throw new RuntimeException("Rango de {$variable} inválido para la especie {$lote->especie_id}.");
                    }
                    $tipo = $tipos->get($variable);
                    $clave = $lote->id.':'.$tipo->id;
                    if (($valor >= $min && $valor <= $max) || $abiertas->has($clave)) {
                        continue;
                    }

                    $alerta = Alerta::create([
                        'incubadora_id' => $lectura->incubadora_id,
                        'lote_id' => $lote->id,
                        'lectura_microclima_id' => $lectura->id,
                        'tipo_alerta_id' => $tipo->id,
                        'nivel_alerta_id' => $niveles->get($this->claveNivel($valor, $min, $max))->id,
                        'estado_alerta_id' => $estado->id,
                        'mensaje' => Str::limit("{$etiqueta} fuera de rango para el lote {$lote->codigo_lote} ({$lote->especie->nombre_comun}). Lectura: {$valor} {$unidad}. Rango esperado: {$min} - {$max} {$unidad}.", 255, ''),
                        'fecha_hora' => $lectura->fecha_hora,
                        'atendida_por' => null,
                        'observaciones' => 'Generada automáticamente por lectura de sensor.',
                    ]);
                    $abiertas->put($clave, $alerta);
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
