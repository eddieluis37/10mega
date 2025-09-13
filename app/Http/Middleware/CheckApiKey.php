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
        // 1) Soporte X-API-KEY o Authorization: Bearer <key>
        $clientKey = $request->header('X-API-KEY') ?: $request->bearerToken();
        $expectedKey = config('api.key');

        if (!$clientKey || !$expectedKey || !hash_equals((string) $expectedKey, (string) $clientKey)) {
            Log::warning('Unauthorized request - invalid API key', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'headers' => ['x-api-key' => $request->header('X-API-KEY') ? 'present' : 'absent']
            ]);
            return response()->json([
                'message' => 'Unauthorized – invalid API key'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // 2) Opcional: validar HMAC signature si hay secret configurado
        $hmacSecret = config('api.hmac_secret');
        if ($hmacSecret) {
            $signatureHeader = $request->header('X-SIGNATURE'); // esperamos formato: "sha256=<hex>"
            $body = $request->getContent(); // cuerpo raw
            $calculated = 'sha256=' . hash_hmac('sha256', $body, $hmacSecret);

            if (!$signatureHeader || !hash_equals($calculated, $signatureHeader)) {
                Log::warning('Unauthorized request - invalid HMAC signature', [
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                    'provided_signature' => $signatureHeader ? 'present' : 'absent'
                ]);
                return response()->json([
                    'message' => 'Unauthorized – invalid signature'
                ], Response::HTTP_UNAUTHORIZED);
            }
        }

       /*  // 3) Opcional: allowlist de IPs (si está configurada)
        $allowlist = config('api.allowlist_ips', []);
        if (!empty($allowlist) && is_array($allowlist)) {
            if (!in_array($request->ip(), $allowlist)) {
                Log::warning('Unauthorized request - ip not allowed', [
                    'ip' => $request->ip(),
                    'allowed' => $allowlist
                ]);
                return response()->json([
                    'message' => 'Unauthorized – ip not allowed'
                ], Response::HTTP_UNAUTHORIZED);
            }
        } */

        // Si todo OK, seguimos
        return $next($request);
    }
}