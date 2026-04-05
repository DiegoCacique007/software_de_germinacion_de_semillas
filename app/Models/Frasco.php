<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoFrasco::class, 'estado_frasco_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoFrasco::class, 'frasco_id');
    }
}
