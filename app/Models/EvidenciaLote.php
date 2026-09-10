<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenciaLote extends Model
{
    use HasFactory;

    protected $table = 'evidencias_lote';

    protected $fillable = [
        'seguimiento_lote_id',
        'archivo',
        'descripcion',
    ];

    public function seguimiento(): BelongsTo
    {
        return $this->belongsTo(SeguimientoLote::class, 'seguimiento_lote_id');
    }

    public function scopeDeEncargado(Builder $query, int $userId): Builder
    {
        return $query->whereHas(
            'seguimiento.lote.posicion.incubadora.asignaciones',
            function (Builder $query) use ($userId) {
                $query->deUsuario($userId)->vigentes();
            }
        );
    }
}
