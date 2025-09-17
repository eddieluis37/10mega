<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // obtener key provista (X-API-KEY o Authorization Bearer)
        $clientKey = $request->header('X-API-KEY') ?: $request->bearerToken() ?: '';
        $clientKey = is_string($clientKey) ? trim($clientKey) : '';

        // obtener esperado desde config; puede ser "key1,key2"
        $expectedRaw = config('api.key', '') ?? '';
        $expectedRaw = is_string($expectedRaw) ? trim($expectedRaw) : '';

        // preparar lista permitida
        $allowed = [];
        if ($expectedRaw !== '') {
            $allowed = array_map('trim', explode(',', $expectedRaw));
        }

        // función para enmascarar la key en logs (mostrar 4 primeros y 4 últimos)
        $mask = function(?string $k) {
            if (!$k) return null;
            $len = strlen($k);
            if ($len <= 8) return str_repeat('*', $len);
            return substr($k, 0, 4) . '...' . substr($k, -4);
        };

        Log::info('CheckApiKey debug', [
            'path' => $request->path(),
            'provided_masked' => $mask($clientKey),
            'expected_masked' => $mask($expectedRaw),
            'allowed_count' => count($allowed),
            'header_used' => $request->header('X-API-KEY') ? 'X-API-KEY' : ($request->bearerToken() ? 'Authorization:Bearer' : 'none'),
        ]);

        // validación segura: si expectedRaw vacío tratamos como no autorizado
        if (empty($clientKey) || empty($allowed) || !in_array($clientKey, $allowed, true)) {
            return response()->json(['message' => 'Unauthorized – invalid API key'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}