<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionIncubadora extends Model
{
    use HasFactory;

    protected $table = 'asignaciones_incubadora';

    protected $fillable = [
        'incubadora_id',
        'user_id',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
    ];

    public function incubadora()
    {
        return $this->belongsTo(Incubadora::class);
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
