<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenciaLote extends Model
{
    use HasFactory;

    protected $table = 'evidencias_lote';

    protected $fillable = [
        'seguimiento_lote_id',
        'archivo',
        'descripcion',
    ];

    public function seguimiento()
    {
        return $this->belongsTo(SeguimientoLote::class, 'seguimiento_lote_id');
    }
}
