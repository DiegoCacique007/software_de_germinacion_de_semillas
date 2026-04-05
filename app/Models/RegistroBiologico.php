<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroBiologico extends Model
{
    use HasFactory;

    protected $table = 'registros_biologicos';

    protected $fillable = [
        'lote_id',
        'user_id',
        'fecha_registro',
        'dias_estratificacion',
        'porcentaje_carbono',
        'porcentaje_nitrogeno',
        'porcentaje_fosforo',
        'tasa_germinacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'porcentaje_carbono' => 'decimal:2',
        'porcentaje_nitrogeno' => 'decimal:2',
        'porcentaje_fosforo' => 'decimal:2',
        'tasa_germinacion' => 'decimal:2',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
