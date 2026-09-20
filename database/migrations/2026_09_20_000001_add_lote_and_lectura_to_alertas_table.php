<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->nullOnDelete();
            $table->foreignId('lectura_microclima_id')->nullable()->constrained('lecturas_microclima')->nullOnDelete();
            $table->index(['lote_id', 'tipo_alerta_id', 'estado_alerta_id'], 'alertas_lote_tipo_estado_index');
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropForeign(['lectura_microclima_id']);
            $table->dropIndex('alertas_lote_tipo_estado_index');
            $table->dropColumn(['lote_id', 'lectura_microclima_id']);
        });
    }
};
