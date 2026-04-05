<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoFrasco extends Model
{
    use HasFactory;

    protected $table = 'seguimientos_frasco';

    protected $fillable = [
        'frasco_id',
        'fecha_revision',
        'semillas_germinadas',
        'altura_promedio_cm',
        'estado_frasco_id',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'fecha_revision' => 'date',
        'altura_promedio_cm' => 'decimal:2',
    ];

    public function frasco()
    {
        return $this->belongsTo(Frasco::class, 'frasco_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoFrasco::class, 'estado_frasco_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
