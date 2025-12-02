<?php

return [
    'enable' => env('AYA_GATEWAY_ENABLE', 'false'),
    'charge_type' => env('AYA_GATEWAY_CHARGE_TYPE', 'percentage'),
    'charge' => env('AYA_GATEWAY_CHARGE', 0),
    'app_key' => env('AYA_GATEWAY_APP_KEY', ''),
    'app_secret' => env('AYA_GATEWAY_APP_SECRET', ''),
    'payment_url' => env('AYA_GATEWAY_URL', 'https://pgw.ayainnovation.com'),
    'frontend_url' => env('AYA_GATEWAY_FRONTEND_URL', ""),
    'backend_url' => env('AYA_GATEWAY_BACKEND_URL', "")
];
