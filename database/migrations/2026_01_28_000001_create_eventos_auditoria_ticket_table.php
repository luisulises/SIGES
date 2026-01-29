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
        Schema::create('eventos_auditoria_ticket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('actor_id')->constrained('usuarios');
            $table->string('tipo_evento');
            $table->jsonb('valor_antes')->nullable();
            $table->jsonb('valor_despues')->nullable();
            $table->jsonb('metadatos')->nullable();
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('actor_id');
            $table->index('tipo_evento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_auditoria_ticket');
    }
};

