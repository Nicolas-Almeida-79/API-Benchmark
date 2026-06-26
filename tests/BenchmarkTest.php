<?php

namespace Tests;

use App\Models\Cpu;
use App\Models\Gpu;
use App\Models\GameMinimum;
use App\Models\Benchmark;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BenchmarkTest extends TestCase
{
    use DatabaseTransactions;

    private function getOrCreateTestData()
    {
        $cpu = Cpu::first() ?: Cpu::create([
            'model' => 'Test CPU',
            'fabricante' => 'Intel',
            'arquitetura' => 'Test',
            'cores' => 4,
            'threads' => 8,
            'clock' => 3.5,
            'boost' => 4.0
        ]);

        $gpu = Gpu::first() ?: Gpu::create([
            'model' => 'Test GPU',
            'fabricante' => 'NVIDIA',
            'arquitetura' => 'Test',
            'cuda_cores' => 1024,
            'base_clock' => 1000,
            'boost_clock' => 1200,
            'memory' => 4096
        ]);

        $game = GameMinimum::first() ?: GameMinimum::create([
            'nome' => 'Test Game',
            'nomeC' => 'Test CPU',
            'nomeR' => '8 GB',
            'nomeG' => 'Test GPU',
            'ano_lancamento' => 2026,
            'desenvolvedora' => 'Test Dev',
            'genero' => 'Test Genre'
        ]);

        return [$cpu, $gpu, $game];
    }

    public function test_can_list_benchmarks()
    {
        $this->get('/benchmarks');
        $this->assertResponseOk();
    }

    public function test_cannot_create_benchmark_without_api_key()
    {
        list($cpu, $gpu, $game) = $this->getOrCreateTestData();

        $this->post('/benchmarks', [
            'jogo' => $game->nome,
            'cpu' => $cpu->model,
            'gpu' => $gpu->model,
            'ram' => '16 GB',
            'configuracao' => 'Ultra',
            'fps_medio' => 60
        ]);

        $this->assertResponseStatus(401);
    }

    public function test_can_create_benchmark_with_api_key()
    {
        list($cpu, $gpu, $game) = $this->getOrCreateTestData();

        $this->post('/benchmarks', [
            'jogo' => $game->nome,
            'cpu' => $cpu->model,
            'gpu' => $gpu->model,
            'ram' => '16 GB',
            'configuracao' => 'Ultra',
            'fps_medio' => 60
        ], ['X-API-KEY' => 'keySecreta']);

        $this->assertResponseStatus(201);
        $this->seeJsonStructure([
            'id',
            'jogo',
            'cpu',
            'gpu',
            'ram',
            'configuracao',
            'fps_medio'
        ]);
        $this->seeJson([
            'jogo' => $game->nome,
            'cpu' => $cpu->model,
            'gpu' => $gpu->model,
        ]);
    }

    public function test_validation_fails_on_missing_fields()
    {
        $this->post('/benchmarks', [], ['X-API-KEY' => 'keySecreta']);
        $this->assertResponseStatus(422);
    }

    public function test_fails_if_hardware_or_game_does_not_exist()
    {
        list($cpu, $gpu, $game) = $this->getOrCreateTestData();

        $this->post('/benchmarks', [
            'jogo' => 'Jogo Inexistente',
            'cpu' => $cpu->model,
            'gpu' => $gpu->model,
            'ram' => '16 GB',
            'configuracao' => 'Ultra',
            'fps_medio' => 60
        ], ['X-API-KEY' => 'keySecreta']);

        $this->assertResponseStatus(422);
        $this->seeJsonStructure([
            'errors' => ['jogo']
        ]);
    }

    public function test_can_update_benchmark()
    {
        list($cpu, $gpu, $game) = $this->getOrCreateTestData();

        $benchmark = Benchmark::create([
            'jogo_id' => $game->id,
            'cpu_id' => $cpu->id,
            'gpu_id' => $gpu->id,
            'ram' => '16 GB',
            'configuracao' => 'Medium',
            'fps_medio' => 45
        ]);

        $this->put('/benchmarks/' . $benchmark->id, [
            'configuracao' => 'High',
            'fps_medio' => 55
        ], ['X-API-KEY' => 'keySecreta']);

        $this->assertResponseStatus(200);
        $this->seeJson([
            'configuracao' => 'High',
            'fps_medio' => 55,
            'jogo' => $game->nome
        ]);
    }

    public function test_can_delete_benchmark()
    {
        list($cpu, $gpu, $game) = $this->getOrCreateTestData();

        $benchmark = Benchmark::create([
            'jogo_id' => $game->id,
            'cpu_id' => $cpu->id,
            'gpu_id' => $gpu->id,
            'ram' => '16 GB',
            'configuracao' => 'Medium',
            'fps_medio' => 45
        ]);

        $this->delete('/benchmarks/' . $benchmark->id, [], ['X-API-KEY' => 'keySecreta']);
        $this->assertResponseStatus(204);

        $this->assertNull(Benchmark::find($benchmark->id));
    }
}
