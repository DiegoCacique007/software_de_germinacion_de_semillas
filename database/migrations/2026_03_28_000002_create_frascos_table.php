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
        Schema::create('frascos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('numero_frasco');
            $table->unsignedTinyInteger('cantidad_semillas');
            $table->foreignId('estado_frasco_id')->constrained('estados_frasco')->restrictOnDelete()->cascadeOnUpdate();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['lote_id', 'numero_frasco'], 'uk_lote_frasco');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frascos');
    }
};
