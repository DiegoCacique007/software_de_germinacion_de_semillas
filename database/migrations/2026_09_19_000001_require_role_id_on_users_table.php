<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No inferir ni reconstruir asignaciones históricas de roles.
        if (DB::table('users')->whereNull('role_id')->exists()
            || DB::table('users')->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                ->whereNull('roles.id')->exists()) {
            throw new RuntimeException('No se puede exigir users.role_id: existen usuarios sin rol válido. Reconcile sus asignaciones antes de ejecutar esta migración.');
        }

        $column = collect(Schema::getColumns('users'))->firstWhere('name', 'role_id');

        if (! $column) {
            throw new RuntimeException('Falta users.role_id; revisa las migraciones anteriores.');
        }

        if (! $column['nullable']) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        // Algunas bases ya tenían NOT NULL antes de esta migración.
        // No relajar esa restricción sin conocer el estado anterior de cada base.
        throw new RuntimeException('Esta restricción requiere una reversión explícita y revisada; no se modifica users.role_id automáticamente.');
    }
};
