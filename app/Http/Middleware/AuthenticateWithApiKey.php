<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateWithApiKey
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');

        // Aqui você pode comparar com uma chave fixa ou buscar em um banco de dados.
        if ($apiKey !== 'keySecreta') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
