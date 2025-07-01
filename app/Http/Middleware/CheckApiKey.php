<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // Lee la cabecera
        $clientKey = $request->header('X-API-KEY');

        // Lee la clave esperada de config
        $expectedKey = config('api.key');

        if (!$clientKey || $clientKey !== $expectedKey) {
            return response()->json([
                'message' => 'Unauthorized – invalid API key'
            ], 401);
        }

        return $next($request);
    }
}