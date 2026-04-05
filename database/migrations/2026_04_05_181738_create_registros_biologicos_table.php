<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_biologicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lote_id')->constrained('lotes')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('fecha_registro');
            $table->unsignedInteger('dias_estratificacion')->default(0);

            $table->decimal('porcentaje_carbono', 5, 2)->nullable();
            $table->decimal('porcentaje_nitrogeno', 5, 2)->nullable();
            $table->decimal('porcentaje_fosforo', 5, 2)->nullable();

            $table->decimal('tasa_germinacion', 5, 2)->default(0);
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_biologicos');
    }
};
