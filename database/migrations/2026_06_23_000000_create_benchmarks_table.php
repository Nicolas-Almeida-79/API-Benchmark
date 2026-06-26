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
        Schema::create('benchmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jogo_id')->constrained('games_minimum')->onDelete('cascade');
            $table->foreignId('cpu_id')->constrained('cpus')->onDelete('cascade');
            $table->foreignId('gpu_id')->constrained('gpus')->onDelete('cascade');
            $table->string('ram');
            $table->string('configuracao');
            $table->integer('fps_medio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benchmarks');
    }
};
