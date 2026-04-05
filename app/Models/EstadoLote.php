<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoLote extends Model
{
    use HasFactory;

    protected $table = 'estados_lote';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'estado_lote_id');
    }
}
