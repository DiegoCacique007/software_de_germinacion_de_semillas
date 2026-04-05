<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAlerta extends Model
{
    use HasFactory;

    protected $table = 'estados_alerta';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];

    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'estado_alerta_id');
    }
}
