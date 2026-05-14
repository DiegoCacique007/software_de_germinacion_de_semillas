<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModoControlIncubadora extends Model
{
    use HasFactory;

    protected $table = 'modos_control_incubadora';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];
}
