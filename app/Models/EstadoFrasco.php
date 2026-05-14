<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoFrasco extends Model
{
    use HasFactory;

    protected $table = 'estados_frasco';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];
}
