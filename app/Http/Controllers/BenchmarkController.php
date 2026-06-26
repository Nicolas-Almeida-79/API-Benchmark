<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Benchmark;
use App\Models\GameMinimum;
use App\Models\Cpu;
use App\Models\Gpu;

class BenchmarkController extends Controller
{
    /**
     * Lista todos os benchmarks no formato simplificado
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $benchmarks = Benchmark::with(['jogo', 'cpu', 'gpu'])->get();
        
        $transformed = $benchmarks->map(function ($benchmark) {
            return $this->transform($benchmark);
        });

        return response()->json($transformed);
    }

    /**
     * Mostra os detalhes de um benchmark específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $benchmark = Benchmark::with(['jogo', 'cpu', 'gpu'])->find($id);

        if (!$benchmark) {
            return response()->json(['error' => 'Benchmark não encontrado'], 404);
        }

        return response()->json($this->transform($benchmark));
    }

    /**
     * Cria um novo registro de benchmark resolvendo os nomes
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'jogo' => 'required|string|max:255',
            'cpu' => 'required|string|max:255',
            'gpu' => 'required|string|max:255',
            'ram' => 'required|string|max:255',
            'configuracao' => 'required|string|max:255',
            'fps_medio' => 'required|integer|min:0',
        ]);

        // Resolvendo o Jogo
        $game = GameMinimum::whereRaw('LOWER(nome) = ?', [strtolower(trim($request->input('jogo')))])->first();
        if (!$game) {
            return response()->json([
                'errors' => [
                    'jogo' => ["O jogo '" . $request->input('jogo') . "' não foi encontrado no sistema."]
                ]
            ], 422);
        }

        // Resolvendo a CPU
        $cpu = Cpu::whereRaw('LOWER(model) = ?', [strtolower(trim($request->input('cpu')))])->first();
        if (!$cpu) {
            return response()->json([
                'errors' => [
                    'cpu' => ["A CPU '" . $request->input('cpu') . "' não foi encontrada no sistema."]
                ]
            ], 422);
        }

        // Resolvendo a GPU
        $gpu = Gpu::whereRaw('LOWER(model) = ?', [strtolower(trim($request->input('gpu')))])->first();
        if (!$gpu) {
            return response()->json([
                'errors' => [
                    'gpu' => ["A GPU '" . $request->input('gpu') . "' não foi encontrada no sistema."]
                ]
            ], 422);
        }

        $benchmark = new Benchmark();
        $benchmark->jogo_id = $game->id;
        $benchmark->cpu_id = $cpu->id;
        $benchmark->gpu_id = $gpu->id;
        $benchmark->ram = $request->input('ram');
        $benchmark->configuracao = $request->input('configuracao');
        $benchmark->fps_medio = $request->input('fps_medio');
        $benchmark->save();

        // Carrega relacionamentos para retornar a resposta completa
        $benchmark->load(['jogo', 'cpu', 'gpu']);

        return response()->json($this->transform($benchmark), 201);
    }

    /**
     * Atualiza um benchmark existente resolvendo os nomes
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $benchmark = Benchmark::find($id);

        if (!$benchmark) {
            return response()->json(['error' => 'Benchmark não encontrado'], 404);
        }

        $this->validate($request, [
            'jogo' => 'sometimes|required|string|max:255',
            'cpu' => 'sometimes|required|string|max:255',
            'gpu' => 'sometimes|required|string|max:255',
            'ram' => 'sometimes|required|string|max:255',
            'configuracao' => 'sometimes|required|string|max:255',
            'fps_medio' => 'sometimes|required|integer|min:0',
        ]);

        if ($request->has('jogo')) {
            $game = GameMinimum::whereRaw('LOWER(nome) = ?', [strtolower(trim($request->input('jogo')))])->first();
            if (!$game) {
                return response()->json([
                    'errors' => [
                        'jogo' => ["O jogo '" . $request->input('jogo') . "' não foi encontrado no sistema."]
                    ]
                ], 422);
            }
            $benchmark->jogo_id = $game->id;
        }

        if ($request->has('cpu')) {
            $cpu = Cpu::whereRaw('LOWER(model) = ?', [strtolower(trim($request->input('cpu')))])->first();
            if (!$cpu) {
                return response()->json([
                    'errors' => [
                        'cpu' => ["A CPU '" . $request->input('cpu') . "' não foi encontrada no sistema."]
                    ]
                ], 422);
            }
            $benchmark->cpu_id = $cpu->id;
        }

        if ($request->has('gpu')) {
            $gpu = Gpu::whereRaw('LOWER(model) = ?', [strtolower(trim($request->input('gpu')))])->first();
            if (!$gpu) {
                return response()->json([
                    'errors' => [
                        'gpu' => ["A GPU '" . $request->input('gpu') . "' não foi encontrada no sistema."]
                    ]
                ], 422);
            }
            $benchmark->gpu_id = $gpu->id;
        }

        $benchmark->ram = $request->input('ram', $benchmark->ram);
        $benchmark->configuracao = $request->input('configuracao', $benchmark->configuracao);
        $benchmark->fps_medio = $request->input('fps_medio', $benchmark->fps_medio);
        $benchmark->save();

        $benchmark->load(['jogo', 'cpu', 'gpu']);

        return response()->json($this->transform($benchmark), 200);
    }

    /**
     * Remove um benchmark do banco de dados
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $benchmark = Benchmark::find($id);

        if (!$benchmark) {
            return response()->json(['error' => 'Benchmark não encontrado'], 404);
        }

        $benchmark->delete();

        return response()->json(null, 204);
    }

    /**
     * Transforma um objeto Benchmark no formato de resposta simplificado
     *
     * @param Benchmark $benchmark
     * @return array
     */
    private function transform(Benchmark $benchmark)
    {
        return [
            'id' => $benchmark->id,
            'jogo' => $benchmark->jogo ? $benchmark->jogo->nome : null,
            'cpu' => $benchmark->cpu ? $benchmark->cpu->model : null,
            'gpu' => $benchmark->gpu ? $benchmark->gpu->model : null,
            'ram' => $benchmark->ram,
            'configuracao' => $benchmark->configuracao,
            'fps_medio' => $benchmark->fps_medio,
            'created_at' => $benchmark->created_at,
            'updated_at' => $benchmark->updated_at,
        ];
    }
}
