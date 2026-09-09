<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturaMicroclima extends Model
{
    use HasFactory;

    protected $table = 'lecturas_microclima';

    protected $fillable = [
        'incubadora_id',
        'fecha_hora',
        'temperatura',
        'humedad',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'temperatura' => 'decimal:2',
            'humedad' => 'decimal:2',
        ];
    }

    public function incubadora(): BelongsTo
    {
        return $this->belongsTo(Incubadora::class, 'incubadora_id');
    }
}
