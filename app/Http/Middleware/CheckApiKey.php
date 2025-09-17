<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @param string|null $service  // valor pasado desde la ruta: ->middleware('check_api_key:traza')
     */
    public function handle(Request $request, Closure $next, $service = null)
    {
        // 1) obtener key provista (X-API-KEY o Bearer)
        $clientKey = $request->header('X-API-KEY') ?: $request->bearerToken();

        // 2) determinar servicio si no fue pasado como parámetro
        $serviceFromHeader = $request->header('X-SERVICE');
        $serviceFromPath = null;
        if (preg_match('#^api/([^/]+)#', $request->path(), $m)) {
            $serviceFromPath = $m[1]; // e.g. "traza" si ruta es api/traza/...
        }
        $service = $service ?: $serviceFromHeader ?: $serviceFromPath;

        // 3) obtener key esperada (fallback a default_key)
        $keysByService = config('api.keys_by_service', []);
        $expectedKey = $keysByService[$service] ?? config('api.default_key');

        if (!$clientKey || !$expectedKey || !hash_equals((string) $expectedKey, (string) $clientKey)) {
            Log::warning('Unauthorized - invalid API key', [
                'service' => $service,
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);
            return response()->json(['message' => 'Unauthorized – invalid API key'], Response::HTTP_UNAUTHORIZED);
        }

        // 4) HMAC por servicio (opcional)
        $hmacSecrets = config('api.hmac_secrets', []);
        $hmacSecret = $hmacSecrets[$service] ?? null;
        if ($hmacSecret) {
            $signatureHeader = $request->header('X-SIGNATURE'); // "sha256=<hex>"
            $body = $request->getContent();
            $calculated = 'sha256=' . hash_hmac('sha256', $body, $hmacSecret);

            if (!$signatureHeader || !hash_equals($calculated, $signatureHeader)) {
                Log::warning('Unauthorized - invalid HMAC signature', [
                    'service' => $service,
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                ]);
                return response()->json(['message' => 'Unauthorized – invalid signature'], Response::HTTP_UNAUTHORIZED);
            }
        }

        // 5) allowlist por servicio (opcional)
        $allowlists = config('api.allowlists', []);
        $allowlist = $allowlists[$service] ?? [];
        if (!empty($allowlist) && is_array($allowlist)) {
            if (!in_array($request->ip(), $allowlist)) {
                Log::warning('Unauthorized - ip not allowed', [
                    'service' => $service,
                    'ip' => $request->ip(),
                    'allowed' => $allowlist,
                ]);
                return response()->json(['message' => 'Unauthorized – ip not allowed'], Response::HTTP_UNAUTHORIZED);
            }
        }

        // todo ok
        return $next($request);
    }
}
