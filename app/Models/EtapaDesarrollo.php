<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtapaDesarrollo extends Model
{
    use HasFactory;

    protected $table = 'etapas_desarrollo';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];
}
