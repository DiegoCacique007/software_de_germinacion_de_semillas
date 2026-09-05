<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posicion_incubadora_id')->constrained('posiciones_incubadora')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('especie_id')->constrained('especies')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('codigo_lote', 60)->unique();
            $table->date('fecha_siembra');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->foreignId('estado_lote_id')->constrained('estados_lote')->restrictOnDelete()->cascadeOnUpdate();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
