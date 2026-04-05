<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAlerta extends Model
{
    use HasFactory;

    protected $table = 'tipos_alerta';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];

    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'tipo_alerta_id');
    }
}
