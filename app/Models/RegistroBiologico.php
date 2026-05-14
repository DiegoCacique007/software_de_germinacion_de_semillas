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
        'tasa_germinacion' => 'float',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
