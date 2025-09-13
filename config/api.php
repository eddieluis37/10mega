<?php
return [
    // Esta clave tomará el valor de .env, variando por entorno.
    'key' => env('API_KEY'),

    // lista de keys permitidas (separa por comas en .env)
    'keys' => array_filter(array_map('trim', explode(',', env('API_KEYS', env('API_KEY'))))),

    // Secret opcional para HMAC (dejalo vacío si no usas HMAC)
    'hmac_secret' => env('API_HMAC_SECRET', null),
    
    // Lista de IPs permitidas separadas por coma, opcional
    'allowlist_ips' => array_filter(array_map('trim', explode(',', env('API_ALLOWLIST_IPS', '')))),
];