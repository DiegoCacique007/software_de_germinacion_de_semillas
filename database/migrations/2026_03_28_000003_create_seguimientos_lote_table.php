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
        Schema::create('seguimientos_lote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->restrictOnDelete()->cascadeOnUpdate();
            $table->date('fecha_revision');
            $table->unsignedTinyInteger('frascos_activos');
            $table->unsignedInteger('semillas_germinadas');
            $table->decimal('porcentaje_germinacion', 5, 2);
            $table->decimal('altura_promedio_cm', 8, 2)->nullable();
            $table->foreignId('etapa_desarrollo_id')->constrained('etapas_desarrollo')->restrictOnDelete()->cascadeOnUpdate();
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos_lote');
    }
};
