<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dateTime('fecha_atencion')->nullable()->after('fecha_hora');
            $table->dateTime('fecha_resolucion')->nullable()->after('fecha_atencion');
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn(['fecha_atencion', 'fecha_resolucion']);
        });
    }
};
