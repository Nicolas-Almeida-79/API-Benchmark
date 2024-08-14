<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => 'auth.apikey'], function () use ($router) {
    $router->post('/cpus', 'CpuController@store');
    $router->post('/gpus', 'GpuController@store');
    $router->put('/cpus/{id}', 'CpuController@update');
    $router->put('/gpus/{id}', 'GpuController@update');
    $router->delete('/cpus/{id}', 'CpuController@destroy');
    $router->delete('/gpus/{id}', 'GpuController@destroy');
});
$router->get('/cpus', 'CpuController@index');
$router->get('/cpus/{model}', 'CpuController@show');

$router->get('/gpus', 'GpuController@index');
$router->get('/gpus/{model}', 'GpuController@show');


$router->get('/', function () use ($router) {
    return $router->app->version();
});