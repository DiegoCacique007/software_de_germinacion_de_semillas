<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenControlIncubadora extends Model
{
    use HasFactory;

    protected $table = 'ordenes_control_incubadora';

    protected $fillable = [
        'incubadora_id',
        'tipo_control_incubadora_id',
        'modo_control_incubadora_id',
        'accion',
        'valor_aplicado',
        'estado_orden',
        'respuesta_dispositivo',
        'solicitada_por',
        'fecha_solicitud',
        'fecha_aplicacion',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_aplicacion' => 'datetime',
        'valor_aplicado' => 'decimal:2',
    ];

    public function incubadora()
    {
        return $this->belongsTo(Incubadora::class, 'incubadora_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoControlIncubadora::class, 'tipo_control_incubadora_id');
    }

    public function modo()
    {
        return $this->belongsTo(ModoControlIncubadora::class, 'modo_control_incubadora_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'solicitada_por');
    }
}
