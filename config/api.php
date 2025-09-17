<?php
return [
    'default_key' => env('API_KEY'),

    // mapa de keys por servicio...
    'keys_by_service' => [
        'traza' => env('API_KEY_TRAZA', env('API_KEY')),
    ],

    // allowlist segura (siempre es array)
    'allowlist_ips' => array_values(array_filter(array_map('trim', explode(',', env('API_ALLOWLIST_IPS', ''))))),
];