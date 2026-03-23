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

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// Startseite (Dark Mode Landing Page)
Route::get('/', [LandingController::class, 'startseite'])->name('landing.home');

// Dev-Zugang zur Landingpage (nur in Development, da / von web.php überschrieben wird)
// Hinweis: config() ist beim Route-Laden noch nicht verfügbar, daher env()
if (env('APP_ENV') === 'local') {
    Route::get('/home', [LandingController::class, 'startseite'])->name('landing.home.dev');
}

// Rechtliche Seiten
Route::get('/impressum', function () {
    return view('landing.impressum');
})->name('landing.impressum');

Route::get('/datenschutz', function () {
    return view('landing.datenschutz');
})->name('landing.datenschutz');

// SEO Sub-Landingpages
Route::get('/thw-theorie', [\App\Http\Controllers\LandingController::class, 'thwTheorie'])
    ->name('landing.thw-theorie');

// Öffentliche Statistik (anonym, aggregiert)
Route::get('/statistik', [\App\Http\Controllers\PublicStatisticsController::class, 'index'])
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

# Sekundäre öffentliche Seiten
Allow: /thw-theorie
Allow: /guest/practice-menu
Allow: /statistik

# Login/Register
Allow: /login
Allow: /register

# Rechtliches
Allow: /impressum
Allow: /datenschutz

# Nicht-öffentliche Bereiche blockieren
Disallow: /guest/practice
Disallow: /guest/exam
Disallow: /dashboard
Disallow: /storage/*
Disallow: /vendor/*
Disallow: /_debugbar/*
Disallow: /api/*

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
