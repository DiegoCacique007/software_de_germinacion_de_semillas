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
        Schema::create('controles_incubadora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incubadora_id')->constrained('incubadoras')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('tipo_control_incubadora_id')->constrained('tipos_control_incubadora')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('modo_control_incubadora_id')->constrained('modos_control_incubadora')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('valor_aplicado', 10, 2)->nullable();
            $table->dateTime('fecha_hora');
            $table->foreignId('user_id')->nullable()->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controles_incubadora');
    }
};
