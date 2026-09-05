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
        Schema::create('lecturas_microclima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incubadora_id')->constrained('incubadoras')->restrictOnDelete()->cascadeOnUpdate();
            $table->dateTime('fecha_hora');
            $table->decimal('temperatura', 5, 2);
            $table->decimal('humedad', 5, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturas_microclima');
    }
};
