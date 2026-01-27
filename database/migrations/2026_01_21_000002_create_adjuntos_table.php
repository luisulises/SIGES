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
        Schema::create('adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('comentario_id')->nullable()->constrained('comentarios_ticket');
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->string('nombre_archivo');
            $table->string('clave_almacenamiento');
            $table->enum('visibilidad', ['publico', 'interno']);
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('comentario_id');
            $table->index('cargado_por_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjuntos');
    }
};

