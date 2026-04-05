<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\CondicionOptimaEspecie;
use App\Models\EstadoAlerta;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use App\Models\NivelAlerta;
use App\Models\TipoAlerta;
use Illuminate\Support\Collection;

class MicroclimaAlertService
{
    public function evaluarLectura(LecturaMicroclima $lectura): int
    {
        $alertasGeneradas = 0;

        $lotes = Lote::with(['especie', 'estado', 'posicion.incubadora'])
            ->whereHas('posicion', function ($q) use ($lectura) {
                $q->where('incubadora_id', $lectura->incubadora_id);
            })
            ->get();

        if ($lotes->isEmpty()) {
            return 0;
        }

        $estadoPendiente = $this->resolverEstadoPendiente();
        $tipoTemperatura = $this->resolverTipo('temperatura');
        $tipoHumedad = $this->resolverTipo('humedad');

        foreach ($lotes as $lote) {
            $condicion = CondicionOptimaEspecie::where('especie_id', $lote->especie_id)->first();

            if (!$condicion) {
                continue;
            }

            if ($lectura->temperatura < $condicion->temperatura_min || $lectura->temperatura > $condicion->temperatura_max) {
                $nivel = $this->resolverNivel(
                    $lectura->temperatura,
                    $condicion->temperatura_min,
                    $condicion->temperatura_max
                );

                if (!$this->existeAlertaAbierta($lectura->incubadora_id, $tipoTemperatura->id, $estadoPendiente->id, $lote->codigo_lote)) {
                    Alerta::create([
                        'incubadora_id'    => $lectura->incubadora_id,
                        'tipo_alerta_id'   => $tipoTemperatura->id,
                        'nivel_alerta_id'  => $nivel->id,
                        'estado_alerta_id' => $estadoPendiente->id,
                        'mensaje'          => "Temperatura fuera de rango para el lote {$lote->codigo_lote} ({$lote->especie->nombre_comun}). Lectura: {$lectura->temperatura} °C. Rango esperado: {$condicion->temperatura_min} °C - {$condicion->temperatura_max} °C.",
                        'fecha_hora'       => $lectura->fecha_hora,
                        'atendida_por'     => null,
                        'observaciones'    => 'Generada automáticamente por lectura de sensor.',
                    ]);

                    $alertasGeneradas++;
                }
            }

            if ($lectura->humedad < $condicion->humedad_min || $lectura->humedad > $condicion->humedad_max) {
                $nivel = $this->resolverNivel(
                    $lectura->humedad,
                    $condicion->humedad_min,
                    $condicion->humedad_max
                );

                if (!$this->existeAlertaAbierta($lectura->incubadora_id, $tipoHumedad->id, $estadoPendiente->id, $lote->codigo_lote)) {
                    Alerta::create([
                        'incubadora_id'    => $lectura->incubadora_id,
                        'tipo_alerta_id'   => $tipoHumedad->id,
                        'nivel_alerta_id'  => $nivel->id,
                        'estado_alerta_id' => $estadoPendiente->id,
                        'mensaje'          => "Humedad fuera de rango para el lote {$lote->codigo_lote} ({$lote->especie->nombre_comun}). Lectura: {$lectura->humedad} %. Rango esperado: {$condicion->humedad_min} % - {$condicion->humedad_max} %.",
                        'fecha_hora'       => $lectura->fecha_hora,
                        'atendida_por'     => null,
                        'observaciones'    => 'Generada automáticamente por lectura de sensor.',
                    ]);

                    $alertasGeneradas++;
                }
            }
        }

        return $alertasGeneradas;
    }

    protected function existeAlertaAbierta(int $incubadoraId, int $tipoAlertaId, int $estadoPendienteId, string $codigoLote): bool
    {
        return Alerta::where('incubadora_id', $incubadoraId)
            ->where('tipo_alerta_id', $tipoAlertaId)
            ->where('estado_alerta_id', $estadoPendienteId)
            ->where('mensaje', 'like', "%{$codigoLote}%")
            ->exists();
    }

    protected function resolverTipo(string $clave): TipoAlerta
    {
        return TipoAlerta::where('clave', $clave)->first()
            ?? TipoAlerta::where('nombre', ucfirst($clave))->firstOrFail();
    }

    protected function resolverEstadoPendiente(): EstadoAlerta
    {
        return EstadoAlerta::where('clave', 'pendiente')->first()
            ?? EstadoAlerta::where('nombre', 'Pendiente')->firstOrFail();
    }

    protected function resolverNivel(float $valor, float $min, float $max): NivelAlerta
    {
        $desviacion = 0;

        if ($valor < $min) {
            $desviacion = $min - $valor;
        } elseif ($valor > $max) {
            $desviacion = $valor - $max;
        }

        $rango = max($max - $min, 1);
        $porcentaje = ($desviacion / $rango) * 100;

        if ($porcentaje >= 50) {
            return NivelAlerta::where('clave', 'alto')->first()
                ?? NivelAlerta::where('nombre', 'Alto')->firstOrFail();
        }

        if ($porcentaje >= 20) {
            return NivelAlerta::where('clave', 'medio')->first()
                ?? NivelAlerta::where('nombre', 'Medio')->firstOrFail();
        }

        return NivelAlerta::where('clave', 'bajo')->first()
            ?? NivelAlerta::where('nombre', 'Bajo')->firstOrFail();
    }
}
