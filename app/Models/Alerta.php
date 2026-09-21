<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    use HasFactory;

    protected $table = 'alertas';

    protected $fillable = [
        'incubadora_id', 'lote_id', 'lectura_microclima_id', 'tipo_alerta_id', 'nivel_alerta_id',
        'estado_alerta_id', 'mensaje', 'fecha_hora', 'fecha_atencion', 'fecha_resolucion',
        'atendida_por', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'fecha_atencion' => 'datetime',
            'fecha_resolucion' => 'datetime',
        ];
    }

    public function incubadora(): BelongsTo { return $this->belongsTo(Incubadora::class); }
    public function lote(): BelongsTo { return $this->belongsTo(Lote::class); }
    public function lecturaMicroclima(): BelongsTo { return $this->belongsTo(LecturaMicroclima::class); }
    public function tipo(): BelongsTo { return $this->belongsTo(TipoAlerta::class, 'tipo_alerta_id'); }
    public function nivel(): BelongsTo { return $this->belongsTo(NivelAlerta::class, 'nivel_alerta_id'); }
    public function estado(): BelongsTo { return $this->belongsTo(EstadoAlerta::class, 'estado_alerta_id'); }
    public function atendidaPor(): BelongsTo { return $this->belongsTo(User::class, 'atendida_por'); }

    public function getOrigenAttribute(): string
    {
        return $this->lectura_microclima_id ? 'Automática' : 'Manual';
    }

    public function getLecturaCausanteAttribute(): string
    {
        $lectura = $this->lecturaMicroclima;

        if (!$lectura) return 'No aplica';

        return match ($this->tipo?->clave) {
            'temperatura' => number_format((float) $lectura->temperatura, 1).' °C',
            'humedad' => number_format((float) $lectura->humedad, 1).' %',
            default => 'Lectura #'.$lectura->id,
        };
    }

    public function getDuracionIncidenteAttribute(): string
    {
        if (!$this->fecha_hora) return '—';

        if (
            $this->origen === 'Manual' &&
            $this->estado?->clave === 'atendida' &&
            !$this->fecha_atencion &&
            !$this->fecha_resolucion
        ) {
            return 'Histórica';
        }

        $fin = $this->fecha_resolucion ?? now('America/Mexico_City');
        $minutos = max(0, (int) $this->fecha_hora->diffInMinutes($fin));

        if ($minutos < 60) $duracion = $minutos.' min';
        elseif ($minutos < 1440) $duracion = intdiv($minutos, 60).' h '.($minutos % 60).' min';
        else $duracion = intdiv($minutos, 1440).' d '.intdiv($minutos % 1440, 60).' h';

        return $this->fecha_resolucion ? $duracion : 'En curso · '.$duracion;
    }

    public function scopeDeEncargado(Builder $query, int $userId): Builder
    {
        return $query->whereHas('incubadora.asignaciones', function (Builder $query) use ($userId) {
            $query->deUsuario($userId)->vigentes();
        });
    }
}
