<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosicionIncubadora extends Model
{
    use HasFactory;

    protected $table = 'posiciones_incubadora';

    protected $fillable = [
        'incubadora_id',
        'numero_posicion',
        'descripcion',
    ];

    public function incubadora()
    {
        return $this->belongsTo(Incubadora::class, 'incubadora_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'posicion_incubadora_id');
    }
}
