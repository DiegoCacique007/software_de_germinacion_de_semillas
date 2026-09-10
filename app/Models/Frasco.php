<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Frasco extends Model
{
    use HasFactory;

    protected $table = 'frascos';

    protected $fillable = [
        'lote_id',
        'numero_frasco',
        'cantidad_semillas',
        'estado_frasco_id',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'numero_frasco' => 'integer',
            'cantidad_semillas' => 'integer',
        ];
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoFrasco::class, 'estado_frasco_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoFrasco::class);
    }

    public function scopeDeEncargado(Builder $query, int $userId): Builder
    {
        return $query->whereHas(
            'lote.posicion.incubadora.asignaciones',
            function (Builder $query) use ($userId) {
                $query->deUsuario($userId)->vigentes();
            }
        );
    }
}
