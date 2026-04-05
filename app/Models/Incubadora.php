<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incubadora extends Model
{
    use HasFactory;

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

    public function posiciones()
    {
        return $this->hasMany(PosicionIncubadora::class, 'incubadora_id');
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'incubadora_id');
    }

    public function lecturas()
    {
        return $this->hasMany(LecturaMicroclima::class, 'incubadora_id');
    }

    public function controles()
    {
        return $this->hasMany(ControlIncubadora::class, 'incubadora_id');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionIncubadora::class, 'incubadora_id');
    }
}
