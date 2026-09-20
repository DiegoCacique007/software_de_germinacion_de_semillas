<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            foreach (['super_admin' => 'Super Administrador', 'encargado' => 'Encargado'] as $clave => $nombre) {
                Role::firstOrCreate(['clave' => $clave], [
                    'nombre' => $nombre,
                    'activo' => true,
                ]);
            }

            $this->call(MicroseedConfigSeeder::class);

            // Cuenta de desarrollo: conserva la convención de UserFactory.
            // Nunca crear esta cuenta con contraseña conocida en producción.
            if (! app()->environment(['local', 'testing'])) {
                $this->command?->warn('No se creó una cuenta inicial: las credenciales de desarrollo solo se habilitan en local/testing.');

                return;
            }

            $superAdminRole = Role::where('clave', 'super_admin')->firstOrFail();
            $user = User::where('email', 'test@example.com')->first();

            if (! $superAdminRole->activo || ($user && (int) $user->role_id !== (int) $superAdminRole->id)) {
                throw new RuntimeException('Revisa el rol super_admin y la cuenta de desarrollo existente. El seeder no reasigna roles ni reactiva cuentas.');
            }

            User::firstOrCreate(['email' => 'test@example.com'], [
                'name' => 'Test User',
                'role_id' => $superAdminRole->id,
                'activo' => true,
                'password' => Hash::make('password'), // Solo desarrollo; nunca sobrescribe la contraseña existente.
            ]);
        });
    }
}
