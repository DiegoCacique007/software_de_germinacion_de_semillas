<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'foto_perfil', 'role_id', 'activo', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'ultimo_acceso_at' => 'datetime',
            'activo' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Rol
    |--------------------------------------------------------------------------
    */

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function getRolClaveAttribute(): ?string
    {
        return $this->rol?->clave;
    }

    public function getRolNombreAttribute(): string
    {
        return $this->rol?->nombre ?? 'Sin rol';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->rol_clave, $roles, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isEncargado(): bool
    {
        return $this->hasRole('encargado');
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function asignacionesIncubadora(): HasMany
    {
        return $this->hasMany(AsignacionIncubadora::class);
    }

    public function alertasAtendidas(): HasMany
    {
        return $this->hasMany(Alerta::class, 'atendida_por');
    }

    public function controlesIncubadora(): HasMany
    {
        return $this->hasMany(ControlIncubadora::class);
    }

    public function seguimientosLote(): HasMany
    {
        return $this->hasMany(SeguimientoLote::class);
    }

    public function seguimientosFrasco(): HasMany
    {
        return $this->hasMany(SeguimientoFrasco::class);
    }

    public function registrosBiologicos(): HasMany
    {
        return $this->hasMany(RegistroBiologico::class);
    }
}
