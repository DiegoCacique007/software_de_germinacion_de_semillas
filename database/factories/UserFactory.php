<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'activo' => true,

            'role_id' => fn () => Role::firstOrCreate(
                ['clave' => 'encargado'],
                [
                    'nombre' => 'Encargado',
                    'descripcion' => 'Usuario encargado de la operación y seguimiento de incubadoras.',
                    'activo' => true,
                ]
            )->id,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create the user as a Super Admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(
                ['clave' => 'super_admin'],
                [
                    'nombre' => 'Super Administrador',
                    'descripcion' => 'Usuario con acceso global a MicroSeed Control.',
                    'activo' => true,
                ]
            )->id,
        ]);
    }
}
