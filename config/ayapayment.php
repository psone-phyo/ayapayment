<?php

return [
    'gateway' => [
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
        'callback_url' => env('AYA_PAY_CALLBACK_URL', ""),
        'service_code' => env('AYA_PAY_SERVICE_CODE', ""),
        'deep_link' => env('AYA_PAY_DEEP_LINK', ""),
        'decryption_key' => env('AYA_PAY_DECRYPTION_KEY', ""),
    ]

];
