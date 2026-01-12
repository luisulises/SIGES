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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('prioridad_id')->nullable()->constrained('prioridades');
            $table->foreignId('tipo_solicitud_id')->nullable()->constrained('tipos_solicitud');
            $table->timestamp('fecha_compromiso')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->text('resolucion')->nullable();
            $table->timestamp('cerrado_at')->nullable();
            $table->timestamp('cancelado_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['prioridad_id']);
            $table->dropForeign(['tipo_solicitud_id']);
            $table->dropColumn([
                'prioridad_id',
                'tipo_solicitud_id',
                'fecha_compromiso',
                'fecha_entrega',
                'resolucion',
                'cerrado_at',
                'cancelado_at',
            ]);
        });
    }
};
