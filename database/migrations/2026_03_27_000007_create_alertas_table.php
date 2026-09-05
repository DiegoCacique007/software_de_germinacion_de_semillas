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
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incubadora_id')->constrained('incubadoras')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('tipo_alerta_id')->constrained('tipos_alerta')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('nivel_alerta_id')->constrained('niveles_alerta')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('estado_alerta_id')->constrained('estados_alerta')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('mensaje', 255);
            $table->dateTime('fecha_hora');
            $table->foreignId('atendida_por')->nullable()->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
