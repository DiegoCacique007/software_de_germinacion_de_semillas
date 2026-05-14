<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function asignacionesIncubadora()
    {
        return $this->hasMany(AsignacionIncubadora::class);
    }

    public function alertasAtendidas()
    {
        return $this->hasMany(Alerta::class, 'atendida_por');
    }

    public function controlesIncubadora()
    {
        return $this->hasMany(ControlIncubadora::class);
    }

    public function seguimientosLote()
    {
        return $this->hasMany(SeguimientoLote::class);
    }

    public function seguimientosFrasco()
    {
        return $this->hasMany(SeguimientoFrasco::class);
    }

    public function registrosBiologicos()
    {
        return $this->hasMany(RegistroBiologico::class);
    }


}
