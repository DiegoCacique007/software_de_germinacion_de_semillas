<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeguimientoFrasco extends Model
{
    use HasFactory;

    protected $table = 'seguimientos_frasco';

    protected $fillable = [
        'frasco_id',
        'fecha_revision',
        'semillas_germinadas',
        'altura_promedio_cm',
        'estado_frasco_id',
        'observaciones',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_revision' => 'date',
            'semillas_germinadas' => 'integer',
            'altura_promedio_cm' => 'decimal:2',
        ];
    }

    public function frasco(): BelongsTo
    {
        return $this->belongsTo(Frasco::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoFrasco::class, 'estado_frasco_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDeEncargado(Builder $query, int $userId): Builder
    {
        return $query->whereHas(
            'frasco.lote.posicion.incubadora.asignaciones',
            function (Builder $query) use ($userId) {
                $query->deUsuario($userId)->vigentes();
            }
        );
    }
}
