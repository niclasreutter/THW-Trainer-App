<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Configuration
    |--------------------------------------------------------------------------
    |
    | VAPID (Voluntary Application Server Identification) keys are used to
    | identify your application to push services.
    |
    | Generate keys with: vendor/bin/web-push generate-vapid-keys
    |
    */

    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:niclas@thw-trainer.de'),
        'public_key' => env('VAPID_PUBLIC_KEY', ''),
        'private_key' => env('VAPID_PRIVATE_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default TTL
    |--------------------------------------------------------------------------
    |
    | Time to live for push notifications in seconds.
    |
    */

    'ttl' => env('WEBPUSH_TTL', 2419200), // 4 weeks

    /*
    |--------------------------------------------------------------------------
    | Default Urgency
    |--------------------------------------------------------------------------
    |
    | Urgency of push notifications: very-low, low, normal, high
    |
    */

    'urgency' => env('WEBPUSH_URGENCY', 'normal'),
];
