<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMinimum extends Model
{
    /**
     * Nome da tabela no banco de dados
     *
     * @var string
     */
    protected $table = 'games_minimum';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'nomeC', // CPU mínima
        'nomeR', // RAM mínima
        'nomeG', // GPU mínima
        'ano_lancamento',
        'desenvolvedora',
        'genero'
    ];

    /**
     * Relacionamento um-para-um com GameRecommended
     */
    public function recommended(): HasOne
    {
        return $this->hasOne(GameRecommended::class, 'nome', 'nome');
    }

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ano_lancamento' => 'integer',
    ];
}
