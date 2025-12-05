<?php

return [
    'gateway' => [
        'enable' => env('AYA_GATEWAY_ENABLE', 'false'),
        'charge_type' => env('AYA_GATEWAY_CHARGE_TYPE', 'percentage'),
        'charge' => env('AYA_GATEWAY_CHARGE', 0),
        'app_key' => env('AYA_GATEWAY_APP_KEY', ''),
        'app_secret' => env('AYA_GATEWAY_APP_SECRET', ''),
        'payment_url' => env('AYA_GATEWAY_URL', 'https://pgw.ayainnovation.com'),
        'frontend_url' => env('AYA_GATEWAY_FRONTEND_URL', ""),
        'backend_url' => env('AYA_GATEWAY_BACKEND_URL', "")
    ],
    'pay' => [
        'consumer_key' => env('AYA_PAY_CONSUMER_KEY', ''),
        'consumer_secret' => env('AYA_PAY_CONSUMER_SECRET', ''),
        'merchant_phone' => env('AYA_PAY_MERCHANT_PHONE', ''),
        'merchant_password' => env('AYA_PAY_MERCHANT_PASSWORD', ''),
        'payment_url' => env('AYA_PAY_PAYMENT_URL', 'https://opensandbox.ayainnovation.com/'),
        'currency' => env('AYA_PAY_CURRENCY', 'MMK'),
        'callback_url' => env('AYA_PAY_CALLBACK_URL', "")
    ]

];
