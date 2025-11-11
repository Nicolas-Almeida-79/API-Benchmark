<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameRecommended extends Model
{
    /**
     * Nome da tabela no banco de dados
     *
     * @var string
     */
    protected $table = 'games_recommended';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'nomeC', // CPU recomendada
        'nomeR', // RAM recomendada
        'nomeG', // GPU recomendada
    ];

    /**
     * Relacionamento com o jogo de requisitos mínimos
     */
    public function minimum()
    {
        return $this->hasOne(GameMinimum::class, 'nome', 'nome');
    }
}
