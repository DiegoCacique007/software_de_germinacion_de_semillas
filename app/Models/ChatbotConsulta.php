<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotConsulta extends Model
{
    use HasFactory;

    protected $table = 'chatbot_consultas';

    protected $fillable = [
        'user_id',
        'mensaje',
        'intencion',
        'respuesta',
        'reconocida',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'reconocida' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
