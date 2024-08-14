<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cpu extends Model
{
    protected $fillable = [
        'model', 'fabricante', 'arquitetura', 'cores', 'threads', 'clock', 'boost', 'integrated_graphics'
    ];
}