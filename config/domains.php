<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Landing Domain
    |--------------------------------------------------------------------------
    |
    | The domain for the public landing/marketing site.
    | This domain shows the SEO-optimized landing page in light mode.
    |
    */

    'landing' => env('LANDING_DOMAIN', 'thw-trainer.de'),

    /*
    |--------------------------------------------------------------------------
    | Application Domain
    |--------------------------------------------------------------------------
    |
    | The subdomain for the full application with authentication.
    | This domain shows the dark mode glassmorphism design.
    |
    */

    'app' => env('APP_DOMAIN', 'app.thw-trainer.de'),

    /*
    |--------------------------------------------------------------------------
    | Development/Testing Domain
    |--------------------------------------------------------------------------
    |
    | The subdomain for development/testing environment.
    | This domain also shows the full application like app.thw-trainer.de.
    |
    */

    'dev' => env('DEV_DOMAIN', 'dev.thw-trainer.de'),

    /*
    |--------------------------------------------------------------------------
    | Development Mode
    |--------------------------------------------------------------------------
    |
    | In development mode, all routes are available on localhost without
    | domain constraints. This allows local development without subdomain setup.
    |
    */

    'development' => env('APP_ENV') === 'local',

];
