<?php

/*
|--------------------------------------------------------------------------
| Landing Routes (thw-trainer.de)
|--------------------------------------------------------------------------
|
| Diese Routes sind für die öffentliche Landingpage/Marketing-Seite.
| Sie werden unter thw-trainer.de (ohne Subdomain) ausgeliefert.
| Alle Seiten nutzen das Light-Mode Landing Layout.
|
*/

use Illuminate\Support\Facades\Route;

// Startseite
// In Development: Prüfe Auth-Status für Redirect zum Dashboard
// In Production: Landing-Page immer anzeigen (App ist auf Subdomain)
Route::get('/', function () {
    if (config('domains.development') && auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing.home');
})->name('landing.home');

// Rechtliche Seiten
Route::get('/impressum', function () {
    return view('landing.impressum');
})->name('landing.impressum');

Route::get('/datenschutz', function () {
    return view('landing.datenschutz');
})->name('landing.datenschutz');

// Öffentliche Statistik
Route::get('/statistik', [\App\Http\Controllers\StatisticsController::class, 'index'])
    ->name('landing.statistics');

// PWA Offline-Seite
Route::get('/offline', function () {
    return view('offline');
})->name('landing.offline');

// SEO Routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])
    ->name('landing.sitemap');

Route::get('/robots.txt', function () {
    $isTestEnvironment = app()->environment('testing')
        || str_contains(request()->getHost(), 'test.')
        || config('app.environment_type') === 'testing';

    if ($isTestEnvironment) {
        return response("User-agent: *\nDisallow: /", 200)
            ->header('Content-Type', 'text/plain');
    }

    $robotsContent = "User-agent: *
Allow: /
Allow: /guest/*

# Wichtige Seiten für Crawler
Allow: /
Allow: /guest/practice-menu
Allow: /guest/exam
Allow: /statistik
# App-Subdomain wird separat gecrawlt
# Siehe: app.thw-trainer.de/robots.txt

# Cache und temporäre Dateien
Disallow: /storage/*
Disallow: /vendor/*

# Sitemap
Sitemap: " . url('/sitemap.xml') . "

# Crawl-Delay für bessere Performance
Crawl-delay: 1";

    return response($robotsContent, 200)
        ->header('Content-Type', 'text/plain');
});

// Guest Routes (anonymes Üben - nur auf Landing)
Route::prefix('guest')->name('landing.guest.')->group(function () {
    Route::get('/practice-menu', [\App\Http\Controllers\LandingGuestPracticeController::class, 'menu'])
        ->name('practice.menu');
    Route::get('/practice/all', [\App\Http\Controllers\LandingGuestPracticeController::class, 'all'])
        ->name('practice.all');
    Route::get('/practice', [\App\Http\Controllers\LandingGuestPracticeController::class, 'show'])
        ->name('practice.index');
    Route::post('/practice', [\App\Http\Controllers\LandingGuestPracticeController::class, 'submit'])
        ->name('practice.submit');
    Route::get('/exam', [\App\Http\Controllers\LandingGuestExamController::class, 'start'])
        ->name('exam.index');
    Route::post('/exam/submit', [\App\Http\Controllers\LandingGuestExamController::class, 'submit'])
        ->name('exam.submit');
});
