<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Incubadora extends Model
{
    use HasFactory;

    protected $table = 'incubadoras';

    protected $fillable = [
        'codigo',
        'nombre',
        'ubicacion',
        'descripcion',
        'estado_incubadora_id',
    ];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoIncubadora::class, 'estado_incubadora_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionIncubadora::class, 'incubadora_id');
    }

    public function lecturasMicroclima(): HasMany
    {
        return $this->hasMany(LecturaMicroclima::class, 'incubadora_id');
    }

    public function ultimaLecturaMicroclima(): HasOne
    {
        return $this->hasOne(LecturaMicroclima::class, 'incubadora_id')
            ->latestOfMany('fecha_hora');
    }

    public function scopeAsignadasA(Builder $query, int $userId): Builder
    {
        return $query->whereHas('asignaciones', function (Builder $query) use ($userId) {
            $query->deUsuario($userId)->vigentes();
        });
    }
}
