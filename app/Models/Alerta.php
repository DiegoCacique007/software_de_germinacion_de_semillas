<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    use HasFactory;

    protected $table = 'alertas';

    protected $fillable = [
        'incubadora_id',
        'tipo_alerta_id',
        'nivel_alerta_id',
        'estado_alerta_id',
        'mensaje',
        'fecha_hora',
        'atendida_por',
        'observaciones',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function incubadora()
    {
        return $this->belongsTo(Incubadora::class, 'incubadora_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoAlerta::class, 'tipo_alerta_id');
    }

    public function nivel()
    {
        return $this->belongsTo(NivelAlerta::class, 'nivel_alerta_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoAlerta::class, 'estado_alerta_id');
    }

    public function atendidaPor()
    {
        return $this->belongsTo(User::class, 'atendida_por');
    }
}
