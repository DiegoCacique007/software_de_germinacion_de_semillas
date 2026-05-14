<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incubadora extends Model
{
    protected $table = 'incubadoras';

    protected $fillable = [
        'codigo',
        'nombre',
        'ubicacion',
        'descripcion',
        'estado_incubadora_id',
    ];

    public function estado()
    {
        return $this->belongsTo(EstadoIncubadora::class, 'estado_incubadora_id');
    }

    public function lecturasMicroclima()
    {
        return $this->hasMany(LecturaMicroclima::class, 'incubadora_id');
    }

    public function ultimaLecturaMicroclima()
    {
        return $this->hasOne(LecturaMicroclima::class, 'incubadora_id')->latestOfMany('fecha_hora');
    }
}
