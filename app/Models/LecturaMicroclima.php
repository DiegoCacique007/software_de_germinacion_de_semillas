<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LecturaMicroclima extends Model
{
    protected $table = 'lecturas_microclima';

    protected $fillable = [
        'incubadora_id',
        'fecha_hora',
        'temperatura',
        'humedad',
        'observaciones',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'temperatura' => 'float',
        'humedad' => 'float',
    ];

    public function incubadora()
    {
        return $this->belongsTo(Incubadora::class, 'incubadora_id');
    }
}
