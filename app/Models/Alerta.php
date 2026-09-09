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
        'incubadora_id',
        'tipo_alerta_id',
        'nivel_alerta_id',
        'estado_alerta_id',
        'mensaje',
        'fecha_hora',
        'atendida_por',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    public function incubadora(): BelongsTo
    {
        return $this->belongsTo(Incubadora::class);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoAlerta::class, 'tipo_alerta_id');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(NivelAlerta::class, 'nivel_alerta_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoAlerta::class, 'estado_alerta_id');
    }

    public function atendidaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atendida_por');
    }

    public function scopeDeEncargado(Builder $query, int $userId): Builder
    {
        return $query->whereHas('incubadora.asignaciones', function (Builder $query) use ($userId) {
            $query->deUsuario($userId)->vigentes();
        });
    }
}
