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
        Schema::create('posiciones_incubadora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incubadora_id')->constrained('incubadoras')->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('numero_posicion');
            $table->string('descripcion', 150)->nullable();
            $table->timestamps();

            $table->unique(['incubadora_id', 'numero_posicion'], 'uk_incubadora_posicion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posiciones_incubadora');
    }
};
