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
        Schema::create('registros_tiempo_ticket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('autor_id')->constrained('usuarios');
            $table->unsignedInteger('minutos');
            $table->text('nota')->nullable();
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('autor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_tiempo_ticket');
    }
};

