<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_control_incubadora', function (Blueprint $table) {
            $table->id();

            $table->foreignId('incubadora_id')->constrained('incubadoras')->restrictOnDelete();
            $table->foreignId('tipo_control_incubadora_id')->constrained('tipos_control_incubadora')->restrictOnDelete();
            $table->foreignId('modo_control_incubadora_id')->constrained('modos_control_incubadora')->restrictOnDelete();

            $table->enum('accion', ['encender', 'apagar', 'ajustar']);
            $table->decimal('valor_aplicado', 10, 2)->nullable();

            $table->enum('estado_orden', ['pendiente', 'aplicada', 'fallida', 'cancelada'])->default('pendiente');
            $table->text('respuesta_dispositivo')->nullable();

            $table->foreignId('solicitada_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_aplicacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_control_incubadora');
    }
};
