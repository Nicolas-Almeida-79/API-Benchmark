<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpusTable extends Migration
{
    public function up()
    {
        Schema::create('gpus', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->string('fabricante');
            $table->string('arquitetura');
            $table->integer('cuda_cores');
            $table->float('base_clock');
            $table->float('boost_clock');
            $table->integer('memory'); // Memória em MB ou GB
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gpus');
    }
}