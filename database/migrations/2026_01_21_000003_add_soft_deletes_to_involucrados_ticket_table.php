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
        Schema::table('involucrados_ticket', function (Blueprint $table) {
            $table->softDeletes();

            $table->index('ticket_id');
            $table->index('usuario_id');
        });

        Schema::table('involucrados_ticket', function (Blueprint $table) {
            $table->dropUnique(['ticket_id', 'usuario_id']);
        });

        DB::statement(
            'CREATE UNIQUE INDEX involucrados_ticket_ticket_id_usuario_id_unique_active
            ON involucrados_ticket (ticket_id, usuario_id)
            WHERE deleted_at IS NULL'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS involucrados_ticket_ticket_id_usuario_id_unique_active');

        Schema::table('involucrados_ticket', function (Blueprint $table) {
            $table->unique(['ticket_id', 'usuario_id']);

            $table->dropIndex(['ticket_id']);
            $table->dropIndex(['usuario_id']);
            $table->dropSoftDeletes();
        });
    }
};

