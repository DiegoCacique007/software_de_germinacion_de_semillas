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
        Schema::create('seguimientos_frasco', function (Blueprint $table) {
            $table->id();
            $table->foreignId('frasco_id')->constrained('frascos')->restrictOnDelete()->cascadeOnUpdate();
            $table->date('fecha_revision');
            $table->unsignedTinyInteger('semillas_germinadas');
            $table->decimal('altura_promedio_cm', 8, 2)->nullable();
            $table->foreignId('estado_frasco_id')->constrained('estados_frasco')->restrictOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('seguimientos_frasco');
    }
};
