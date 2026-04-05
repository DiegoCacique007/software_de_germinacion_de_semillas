<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelAlerta extends Model
{
    use HasFactory;

    protected $table = 'niveles_alerta';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];

    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'nivel_alerta_id');
    }
}
