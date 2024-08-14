<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpusTable extends Migration
{
    public function up(): void
    {
        Schema::create('cpus', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->string('fabricante');
            $table->string('arquitetura');
            $table->integer('cores');
            $table->integer('threads');
            $table->decimal('clock', 5, 2); // GHz
            $table->decimal('boost', 5, 2); // GHz
            $table->string('integrated_graphics')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpus');
    }
};
