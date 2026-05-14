<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlIncubadora extends Model
{
    use HasFactory;

    protected $table = 'controles_incubadora';

    protected $fillable = [
        'incubadora_id',
        'tipo_control_incubadora_id',
        'modo_control_incubadora_id',
        'valor_aplicado',
        'fecha_hora',
        'user_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function incubadora()
    {
        return $this->belongsTo(Incubadora::class);
    }

    public function tipo()
    {
        return $this->belongsTo(TipoControlIncubadora::class, 'tipo_control_incubadora_id');
    }

    public function modo()
    {
        return $this->belongsTo(ModoControlIncubadora::class, 'modo_control_incubadora_id');
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
