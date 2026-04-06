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
        Schema::table('recipes', function (Blueprint $table) {
            // Usamos ->change() para modificar la columna existente
            // Importante: Asegúrate de que no haya datos viejos ('easy') o dará error
            $table->enum('difficulty', ['facil', 'media', 'dificil'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->change();
        });
    }
};
