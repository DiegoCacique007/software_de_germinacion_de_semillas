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

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'temperatura' => 'decimal:2',
            'humedad' => 'decimal:2',
        ];
    }

    public function incubadora()
    {
        return $this->belongsTo(Incubadora::class, 'incubadora_id');
    }
}
