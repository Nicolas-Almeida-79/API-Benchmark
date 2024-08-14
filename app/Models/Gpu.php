<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gpu extends Model
{
    protected $table = 'gpus'; // Define a tabela no banco de dados
    protected $fillable = [
        'model', 'fabricante', 'arquitetura', 'cuda_cores', 'base_clock', 'boost_clock', 'memory'
    ];
}