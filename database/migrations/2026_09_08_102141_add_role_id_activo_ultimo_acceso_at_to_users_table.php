<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addRoleId = !Schema::hasColumn('users', 'role_id');
        $addActivo = !Schema::hasColumn('users', 'activo');
        $addUltimoAcceso = !Schema::hasColumn('users', 'ultimo_acceso_at');

        if ($addRoleId || $addActivo || $addUltimoAcceso) {
            Schema::table('users', function (Blueprint $table) use ($addRoleId, $addActivo, $addUltimoAcceso) {
                if ($addRoleId) {
                    $table->foreignId('role_id')->nullable()->index();
                }

                if ($addActivo) {
                    $table->boolean('activo')->default(true)->index();
                }

                if ($addUltimoAcceso) {
                    $table->timestamp('ultimo_acceso_at')->nullable();
                }
            });
        }

        if ($addRoleId && Schema::hasTable('roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {

    }
};
