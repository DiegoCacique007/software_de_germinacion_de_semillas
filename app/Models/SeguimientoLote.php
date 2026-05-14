<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoLote extends Model
{
    use HasFactory;

    protected $table = 'seguimientos_lote';

    protected $fillable = [
        'lote_id',
        'fecha_revision',
        'frascos_activos',
        'semillas_germinadas',
        'porcentaje_germinacion',
        'altura_promedio_cm',
        'etapa_desarrollo_id',
        'observaciones',
        'user_id',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function etapa()
    {
        return $this->belongsTo(EtapaDesarrollo::class, 'etapa_desarrollo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evidencias()
    {
        return $this->hasMany(EvidenciaLote::class);
    }
}
