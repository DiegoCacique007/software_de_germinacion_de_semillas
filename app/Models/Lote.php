<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';

    protected $fillable = [
        'posicion_incubadora_id',
        'especie_id',
        'codigo_lote',
        'fecha_siembra',
        'fecha_inicio',
        'fecha_fin',
        'estado_lote_id',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_siembra' => 'date',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function posicion(): BelongsTo
    {
        return $this->belongsTo(PosicionIncubadora::class, 'posicion_incubadora_id');
    }

    public function especie(): BelongsTo
    {
        return $this->belongsTo(Especie::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoLote::class, 'estado_lote_id');
    }

    public function frascos(): HasMany
    {
        return $this->hasMany(Frasco::class);
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoLote::class);
    }

    public function registrosBiologicos(): HasMany
    {
        return $this->hasMany(RegistroBiologico::class);
    }

    public function scopeDeEncargado(Builder $query, int $userId): Builder
    {
        return $query->whereHas(
            'posicion.incubadora.asignaciones',
            function (Builder $query) use ($userId) {
                $query->deUsuario($userId)->vigentes();
            }
        );
    }
}
