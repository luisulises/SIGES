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
        Schema::table('sistemas', function (Blueprint $table) {
            $table->index('activo');
        });

        Schema::table('prioridades', function (Blueprint $table) {
            $table->index('activo');
        });

        Schema::table('tipos_solicitud', function (Blueprint $table) {
            $table->index('activo');
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sistemas', function (Blueprint $table) {
            $table->dropIndex(['activo']);
        });

        Schema::table('prioridades', function (Blueprint $table) {
            $table->dropIndex(['activo']);
        });

        Schema::table('tipos_solicitud', function (Blueprint $table) {
            $table->dropIndex(['activo']);
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropIndex(['activo']);
        });
    }
};

