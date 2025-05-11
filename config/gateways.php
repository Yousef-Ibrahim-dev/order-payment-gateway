<?php


return [
    /**
     * Definition of payment gateways for Strategy Pattern.
     * Each gateway has a 'class' and optional 'config' settings.
     */

    'credit_card' => [
        // Fully qualified class name
        'class'  => App\Services\Gateways\CreditCardGateway::class,
        // Configuration passed to the gateway constructor
        'config' => [
            // e.g. 'api_key' => env('CC_API_KEY'),
        ],
    ],

    'paypal' => [
        'class'  => App\Services\Gateways\PayPalGateway::class,
        'config' => [
            'mode'          => env('PAYPAL_MODE', 'sandbox'),
            'client_id'     => env('PAYPAL_SANDBOX_CLIENT_ID'),
            'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET'),
            'live_client_id'=> env('PAYPAL_LIVE_CLIENT_ID'),
            'live_client_secret'=> env('PAYPAL_LIVE_CLIENT_SECRET'),
        ],
    ],

    // Add additional gateways here...
];
