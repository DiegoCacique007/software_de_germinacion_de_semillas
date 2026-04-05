<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoControlIncubadora extends Model
{
    use HasFactory;

    protected $table = 'tipos_control_incubadora';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];

    public function controles()
    {
        return $this->hasMany(ControlIncubadora::class, 'tipo_control_incubadora_id');
    }
}
