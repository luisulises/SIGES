<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos_auditoria_ticket', function (Blueprint $table) {
            $table->index(['ticket_id', 'created_at'], 'eventos_auditoria_ticket_ticket_id_created_at_index');
        });

        Schema::table('registros_tiempo_ticket', function (Blueprint $table) {
            $table->index(['ticket_id', 'created_at'], 'registros_tiempo_ticket_ticket_id_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('eventos_auditoria_ticket', function (Blueprint $table) {
            $table->dropIndex('eventos_auditoria_ticket_ticket_id_created_at_index');
        });

        Schema::table('registros_tiempo_ticket', function (Blueprint $table) {
            $table->dropIndex('registros_tiempo_ticket_ticket_id_created_at_index');
        });
    }
};

