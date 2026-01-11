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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('asunto');
            $table->text('descripcion');
            $table->foreignId('solicitante_id')->constrained('usuarios');
            $table->foreignId('sistema_id')->constrained('sistemas');
            $table->foreignId('estado_id')->constrained('estados_ticket');
            $table->foreignId('responsable_actual_id')->nullable()->constrained('usuarios');
            $table->boolean('interno')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
