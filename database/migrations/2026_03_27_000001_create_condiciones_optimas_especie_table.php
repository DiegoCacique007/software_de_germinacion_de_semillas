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
        Schema::create('condiciones_optimas_especie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('especie_id')->constrained('especies')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('temperatura_min', 5, 2);
            $table->decimal('temperatura_max', 5, 2);
            $table->decimal('humedad_min', 5, 2);
            $table->decimal('humedad_max', 5, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condiciones_optimas_especie');
    }
};
