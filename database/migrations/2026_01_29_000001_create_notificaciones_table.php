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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->string('tipo_evento');
            $table->enum('canal', ['in_app', 'email'])->default('in_app');
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();

            $table->index('usuario_id');
            $table->index('ticket_id');
            $table->index('leido_at');
            $table->index(['usuario_id', 'leido_at']);
            $table->index(['usuario_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};

