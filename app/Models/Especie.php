<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especie extends Model
{
    use HasFactory;

    protected $table = 'especies';

    protected $fillable = [
        'nombre_comun',
        'nombre_cientifico',
        'familia',
        'descripcion',
        'observaciones',
    ];

    public function condicionesOptimas()
    {
        return $this->hasMany(CondicionOptimaEspecie::class, 'especie_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'especie_id');
    }
}
