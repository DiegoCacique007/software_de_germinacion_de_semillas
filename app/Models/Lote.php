<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function posicion()
    {
        return $this->belongsTo(PosicionIncubadora::class, 'posicion_incubadora_id');
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class, 'especie_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoLote::class, 'estado_lote_id');
    }

    public function frascos()
    {
        return $this->hasMany(Frasco::class, 'lote_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoLote::class, 'lote_id');
    }
}
