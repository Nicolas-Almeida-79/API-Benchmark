<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benchmark extends Model
{
    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jogo_id',
        'cpu_id',
        'gpu_id',
        'ram',
        'configuracao',
        'fps_medio'
    ];

    /**
     * Relacionamento com o jogo (tabela games_minimum)
     */
    public function jogo()
    {
        return $this->belongsTo(GameMinimum::class, 'jogo_id');
    }

    /**
     * Relacionamento com a CPU (tabela cpus)
     */
    public function cpu()
    {
        return $this->belongsTo(Cpu::class, 'cpu_id');
    }

    /**
     * Relacionamento com a GPU (tabela gpus)
     */
    public function gpu()
    {
        return $this->belongsTo(Gpu::class, 'gpu_id');
    }
}
