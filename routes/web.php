<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// Rotas protegidas (requerem autenticação)
$router->group(['middleware' => 'auth.apikey'], function () use ($router) {
    // Rotas para CPUs
    $router->post('/cpus', 'CpuController@store');
    $router->put('/cpus/{id}', 'CpuController@update');
    $router->delete('/cpus/{id}', 'CpuController@destroy');
    
    // Rotas para GPUs
    $router->post('/gpus', 'GpuController@store');
    $router->put('/gpus/{id}', 'GpuController@update');
    $router->delete('/gpus/{id}', 'GpuController@destroy');
    
    // Rotas para Jogos
    $router->post('/games', 'GameController@store');
    $router->put('/games/{id}', 'GameController@update');
    $router->delete('/games/{id}', 'GameController@destroy');
    
    // Rotas para Benchmarks
    $router->post('/benchmarks', 'BenchmarkController@store');
    $router->put('/benchmarks/{id}', 'BenchmarkController@update');
    $router->delete('/benchmarks/{id}', 'BenchmarkController@destroy');
});

// Rotas públicas para CPUs
$router->get('/cpus', 'CpuController@index');
$router->get('/cpus/{model}', 'CpuController@show');

// Rotas públicas para GPUs
$router->get('/gpus', 'GpuController@index');
$router->get('/gpus/{model}', 'GpuController@show');

// Rotas públicas para Jogos
$router->get('/games', 'GameController@index');
$router->get('/games/{id}', 'GameController@show');

// Rotas públicas para Benchmarks
$router->get('/benchmarks', 'BenchmarkController@index');
$router->get('/benchmarks/{id}', 'BenchmarkController@show');

// Rota para comparação de hardware
$router->post('/compare', 'CompareController@compareHardware');

// Rota de status da API
$router->get('/', function () use ($router) {
    return response()->json([
        'status' => 'online',
        'version' => $router->app->version(),
        'endpoints' => [
            'cpus' => ['GET /cpus', 'GET /cpus/{model}'],
            'gpus' => ['GET /gpus', 'GET /gpus/{model}'],
            'games' => ['GET /games', 'GET /games/{id}'],
            'benchmarks' => ['GET /benchmarks', 'GET /benchmarks/{id}', 'POST /benchmarks', 'PUT /benchmarks/{id}', 'DELETE /benchmarks/{id}'],
            'compare' => ['POST /compare']
        ]
    ]);
});