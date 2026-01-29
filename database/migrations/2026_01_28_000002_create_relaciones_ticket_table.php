<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('relaciones_ticket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('ticket_relacionado_id')->constrained('tickets');
            $table->enum('tipo_relacion', ['relacionado', 'duplicado_de', 'reabre']);
            $table->foreignId('creado_por_id')->constrained('usuarios');
            $table->timestamps();

            $table->unique(['ticket_id', 'ticket_relacionado_id', 'tipo_relacion'], 'relaciones_ticket_unique');
            $table->index('ticket_id');
            $table->index('ticket_relacionado_id');
            $table->index('creado_por_id');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE relaciones_ticket ADD CONSTRAINT relaciones_ticket_no_auto_relacion CHECK (ticket_id <> ticket_relacionado_id)'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relaciones_ticket');
    }
};

