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
        Schema::create('evidencias_lote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seguimiento_lote_id')->constrained('seguimientos_lote')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('archivo', 255);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencias_lote');
    }
};
