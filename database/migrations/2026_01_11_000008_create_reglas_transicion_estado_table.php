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
        Schema::create('reglas_transicion_estado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estado_origen_id')->constrained('estados_ticket');
            $table->foreignId('estado_destino_id')->constrained('estados_ticket');
            $table->foreignId('rol_id')->constrained('roles');
            $table->boolean('requiere_responsable')->default(false);
            $table->timestamps();

            $table->unique(['estado_origen_id', 'estado_destino_id', 'rol_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas_transicion_estado');
    }
};
