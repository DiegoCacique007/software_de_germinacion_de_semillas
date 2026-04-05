<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoIncubadora extends Model
{
    use HasFactory;

    protected $table = 'estados_incubadora';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];

    public function incubadoras()
    {
        return $this->hasMany(Incubadora::class, 'estado_incubadora_id');
    }
}
