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
        Schema::create('games_minimum', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('nomeC'); // CPU mínima recomendada
            $table->string('nomeR'); // RAM mínima recomendada
            $table->string('nomeG'); // GPU mínima recomendada
            $table->integer('ano_lancamento')->nullable();
            $table->string('desenvolvedora')->nullable();
            $table->string('genero')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games_minimum');
    }
};
