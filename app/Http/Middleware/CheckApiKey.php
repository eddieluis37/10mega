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
        $clientKey = $request->header('X-API-KEY') ?: $request->bearerToken() ?: '';
        $expectedKey = (string) config('api.key', '');

        // Normalizar y trim
        $clientKey = trim($clientKey);
        $expectedKey = trim($expectedKey);

        // Function to mask (show first 4 and last 4 chars)
        $mask = function (?string $k) {
            if (!$k) return null;
            $len = strlen($k);
            if ($len <= 8) return str_repeat('*', $len);
            return substr($k, 0, 4) . '...' . substr($k, -4);
        };

        Log::info('CheckApiKey debug', [
            'request_path' => $request->path(),
            'provided_key_masked' => $mask($clientKey),
            'expected_key_masked' => $mask($expectedKey),
            'header_sent' => $request->header('X-API-KEY') ? 'X-API-KEY' : ($request->bearerToken() ? 'Authorization:Bearer' : 'none'),
        ]);

        // Comparación segura
        if (!$clientKey || !hash_equals($expectedKey, $clientKey)) {
            return response()->json(['message' => 'Unauthorized – invalid API key'], 401);
        }

        return $next($request);
    }
}
