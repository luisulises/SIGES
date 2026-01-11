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
        Schema::create('sistemas_coordinadores', function (Blueprint $table) {
            $table->unsignedBigInteger('sistema_id');
            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();

            $table->unique(['sistema_id', 'usuario_id']);
            $table->foreign('sistema_id')->references('id')->on('sistemas');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistemas_coordinadores');
    }
};
