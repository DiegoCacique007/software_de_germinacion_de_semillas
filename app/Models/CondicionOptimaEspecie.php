<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CondicionOptimaEspecie extends Model
{
    use HasFactory;

    protected $table = 'condiciones_optimas_especie';

    protected $fillable = [
        'especie_id',
        'temperatura_min',
        'temperatura_max',
        'humedad_min',
        'humedad_max',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'temperatura_min' => 'decimal:2',
            'temperatura_max' => 'decimal:2',
            'humedad_min' => 'decimal:2',
            'humedad_max' => 'decimal:2',
        ];
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class);
    }
}
