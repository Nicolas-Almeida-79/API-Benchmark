<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameMinimum;
use App\Models\GameRecommended;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    /**
     * Lista todos os jogos com requisitos mínimos e recomendados
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $games = GameRecommended::with('minimum')->get()->map(function($game) {
            return [
                'id' => $game->id,
                'nome' => $game->nome,
                'cpu_recomendada' => $game->nomeC,
                'ram_recomendada' => $game->nomeR,
                'gpu_recomendada' => $game->nomeG,
                'cpu_minima' => $game->minimum ? $game->minimum->nomeC : null,
                'ram_minima' => $game->minimum ? $game->minimum->nomeR : null,
                'gpu_minima' => $game->minimum ? $game->minimum->nomeG : null,
                'ano_lancamento' => $game->minimum ? $game->minimum->ano_lancamento : null,
                'desenvolvedora' => $game->minimum ? $game->minimum->desenvolvedora : null,
                'genero' => $game->minimum ? $game->minimum->genero : null,
            ];
        });

        return response()->json($games);
    }

    /**
     * Mostra os detalhes de um jogo específico com requisitos mínimos e recomendados
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $game = GameRecommended::with('minimum')->find($id);

        if (!$game) {
            return response()->json(['error' => 'Jogo não encontrado'], 404);
        }

        return response()->json([
            'id' => $game->id,
            'nome' => $game->nome,
            'cpu_recomendada' => $game->nomeC,
            'ram_recomendada' => $game->nomeR,
            'gpu_recomendada' => $game->nomeG,
            'cpu_minima' => $game->minimum ? $game->minimum->nomeC : null,
            'ram_minima' => $game->minimum ? $game->minimum->nomeR : null,
            'gpu_minima' => $game->minimum ? $game->minimum->nomeG : null,
            'ano_lancamento' => $game->minimum ? $game->minimum->ano_lancamento : null,
            'desenvolvedora' => $game->minimum ? $game->minimum->desenvolvedora : null,
            'genero' => $game->minimum ? $game->minimum->genero : null,
        ]);
    }

    /**
     * Cria um novo jogo com requisitos mínimos e recomendados
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            // Dados básicos
            'nome' => 'required|string|max:255|unique:games_recommended,nome',
            'ano_lancamento' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'desenvolvedora' => 'nullable|string|max:255',
            'genero' => 'nullable|string|max:100',
            
            // Requisitos mínimos
            'min_cpu' => 'required|string|max:255',
            'min_ram' => 'required|string|max:100',
            'min_gpu' => 'required|string|max:255',
            
            // Requisitos recomendados
            'rec_cpu' => 'required|string|max:255',
            'rec_ram' => 'required|string|max:100',
            'rec_gpu' => 'required|string|max:255',
        ]);

        // Inicia transação para garantir consistência dos dados
        return DB::transaction(function () use ($request) {
            // Cria registro de requisitos mínimos
            $gameMin = GameMinimum::create([
                'nome' => $request->nome,
                'nomeC' => $request->min_cpu,
                'nomeR' => $request->min_ram,
                'nomeG' => $request->min_gpu,
                'ano_lancamento' => $request->ano_lancamento,
                'desenvolvedora' => $request->desenvolvedora,
                'genero' => $request->genero,
            ]);

            // Cria registro de requisitos recomendados
            $gameRec = GameRecommended::create([
                'nome' => $request->nome,
                'nomeC' => $request->rec_cpu,
                'nomeR' => $request->rec_ram,
                'nomeG' => $request->rec_gpu,
            ]);

            // Carrega o relacionamento para a resposta
            $gameRec->load('minimum');

            // Retorna os dados completos do jogo
            return response()->json([
                'id' => $gameRec->id,
                'nome' => $gameRec->nome,
                'cpu_recomendada' => $gameRec->nomeC,
                'ram_recomendada' => $gameRec->nomeR,
                'gpu_recomendada' => $gameRec->nomeG,
                'cpu_minima' => $gameMin->nomeC,
                'ram_minima' => $gameMin->nomeR,
                'gpu_minima' => $gameMin->nomeG,
                'ano_lancamento' => $gameMin->ano_lancamento,
                'desenvolvedora' => $gameMin->desenvolvedora,
                'genero' => $gameMin->genero,
            ], 201);
        });
    }

    /**
     * Atualiza um jogo existente com requisitos mínimos e recomendados
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $gameRec = GameRecommended::find($id);

        if (!$gameRec) {
            return response()->json(['error' => 'Jogo não encontrado'], 404);
        }

        $this->validate($request, [
            // Dados básicos
            'nome' => 'sometimes|required|string|max:255|unique:games_recommended,nome,' . $id,
            'ano_lancamento' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'desenvolvedora' => 'nullable|string|max:255',
            'genero' => 'nullable|string|max:100',
            
            // Requisitos mínimos
            'min_cpu' => 'sometimes|required|string|max:255',
            'min_ram' => 'sometimes|required|string|max:100',
            'min_gpu' => 'sometimes|required|string|max:255',
            
            // Requisitos recomendados
            'rec_cpu' => 'sometimes|required|string|max:255',
            'rec_ram' => 'sometimes|required|string|max:100',
            'rec_gpu' => 'sometimes|required|string|max:255',
        ]);

        // Inicia transação para garantir consistência dos dados
        return DB::transaction(function () use ($request, $gameRec) {
            $gameMin = GameMinimum::where('nome', $gameRec->nome)->first();
            
            // Se não existir registro de requisitos mínimos e houver dados para atualizar, cria um novo
            if (!$gameMin && ($request->has('min_cpu') || $request->has('min_ram') || $request->has('min_gpu'))) {
                $gameMin = GameMinimum::create([
                    'nome' => $request->has('nome') ? $request->nome : $gameRec->nome,
                    'nomeC' => $request->min_cpu ?? '',
                    'nomeR' => $request->min_ram ?? '',
                    'nomeG' => $request->min_gpu ?? '',
                    'ano_lancamento' => $request->ano_lancamento ?? null,
                    'desenvolvedora' => $request->desenvolvedora ?? '',
                    'genero' => $request->genero ?? '',
                ]);
            } 
            // Se existir, atualiza os campos fornecidos
            elseif ($gameMin) {
                $updateData = [];
                
                if ($request->has('nome')) {
                    $updateData['nome'] = $request->nome;
                }
                
                $fields = [
                    'nomeC' => 'min_cpu',
                    'nomeR' => 'min_ram',
                    'nomeG' => 'min_gpu',
                    'ano_lancamento' => 'ano_lancamento',
                    'desenvolvedora' => 'desenvolvedora',
                    'genero' => 'genero',
                ];
                
                foreach ($fields as $dbField => $requestField) {
                    if ($request->has($requestField)) {
                        $updateData[$dbField] = $request->$requestField;
                    }
                }
                
                if (!empty($updateData)) {
                    $gameMin->update($updateData);
                }
            }

            // Atualiza registro de requisitos recomendados
            $updateRecData = [];
            
            if ($request->has('nome')) {
                $updateRecData['nome'] = $request->nome;
            }
            
            $recFields = [
                'nomeC' => 'rec_cpu',
                'nomeR' => 'rec_ram',
                'nomeG' => 'rec_gpu',
            ];
            
            foreach ($recFields as $dbField => $requestField) {
                if ($request->has($requestField)) {
                    $updateRecData[$dbField] = $request->$requestField;
                }
            }
            
            if (!empty($updateRecData)) {
                $gameRec->update($updateRecData);
            }

            // Carrega os relacionamentos atualizados para a resposta
            $gameRec->load('minimum');

            // Retorna os dados atualizados do jogo
            return response()->json([
                'id' => $gameRec->id,
                'nome' => $gameRec->nome,
                'cpu_recomendada' => $gameRec->nomeC,
                'ram_recomendada' => $gameRec->nomeR,
                'gpu_recomendada' => $gameRec->nomeG,
                'cpu_minima' => $gameMin ? $gameMin->nomeC : null,
                'ram_minima' => $gameMin ? $gameMin->nomeR : null,
                'gpu_minima' => $gameMin ? $gameMin->nomeG : null,
                'ano_lancamento' => $gameMin ? $gameMin->ano_lancamento : null,
                'desenvolvedora' => $gameMin ? $gameMin->desenvolvedora : null,
                'genero' => $gameMin ? $gameMin->genero : null,
            ]);
        });
    }

    /**
     * Remove um jogo e seus requisitos
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $gameRec = GameRecommended::find($id);

        if (!$gameRec) {
            return response()->json(['error' => 'Jogo não encontrado'], 404);
        }

        // Inicia transação para garantir consistência dos dados
        return DB::transaction(function () use ($gameRec) {
            try {
                // Remove requisitos mínimos
                $deletedMin = GameMinimum::where('nome', $gameRec->nome)->delete();
                
                // Remove requisitos recomendados
                $deletedRec = $gameRec->delete();
                
                if ($deletedRec) {
                    return response()->json([
                        'message' => 'Jogo e seus requisitos removidos com sucesso',
                        'deleted_minimum_requirements' => $deletedMin > 0,
                        'deleted_recommended_requirements' => true
                    ], 200);
                }
                
                return response()->json(['error' => 'Falha ao remover o jogo'], 500);
                
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Erro ao remover o jogo',
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    }
}
