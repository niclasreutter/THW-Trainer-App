<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            // Direkt env() verwenden, da config() hier noch nicht verfügbar ist
            $appEnv = env('APP_ENV', 'local');
            $landingDomain = env('LANDING_DOMAIN', 'thw-trainer.de');
            $appDomain = env('APP_DOMAIN', 'app.thw-trainer.de');

            // Multi-Domain nur in Production (wenn Landing und App unterschiedliche Domains sind)
            $useMultiDomain = $appEnv === 'production' && $landingDomain !== $appDomain;

            if ($useMultiDomain) {
                // Production: Domain-basiertes Routing (thw-trainer.de + app.thw-trainer.de)
                Route::middleware('web')
                    ->domain($landingDomain)
                    ->group(base_path('routes/landing.php'));

                Route::middleware('web')
                    ->domain($appDomain)
                    ->group(base_path('routes/web.php'));
            } else {
                // Local/Testing/Dev: Alle Routes ohne Domain-Constraint
                // Landing Routes zuerst (spezifischere Routes)
                Route::middleware('web')
                    ->group(base_path('routes/landing.php'));

                // App Routes (inkl. Auth)
                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ortsverband.ausbildungsbeauftragter' => \App\Http\Middleware\OrtsverbandAusbildungsbeauftragterMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
