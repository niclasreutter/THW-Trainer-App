<?php

/*
|--------------------------------------------------------------------------
| Application Routes (app.thw-trainer.de)
|--------------------------------------------------------------------------
|
| Diese Routes sind für die authentifizierte Anwendung.
| Sie werden unter app.thw-trainer.de ausgeliefert.
| Öffentliche Landing-Routes sind in routes/landing.php.
|
*/

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Debug-Route zum Prüfen der Route-Konfiguration (temporär)
Route::get('/debug-routes', function () {
    if (!config('app.debug')) {
        abort(404);
    }

    $routes = collect(Route::getRoutes())->map(fn($r) => [
        'uri' => $r->uri(),
        'name' => $r->getName(),
        'methods' => implode('|', $r->methods()),
    ])->filter(fn($r) => str_starts_with($r['name'] ?? '', 'landing.'))->values();

    return response()->json([
        'app_env' => env('APP_ENV'),
        'landing_domain' => env('LANDING_DOMAIN'),
        'app_domain' => env('APP_DOMAIN'),
        'landing_routes_count' => $routes->count(),
        'landing_routes' => $routes,
    ]);
});

// robots.txt für App-Subdomain (blockiert Crawler)
Route::get('/robots.txt', function () {
    $robotsContent = "User-agent: *
Disallow: /

# App-Subdomain sollte nicht gecrawlt werden
# Öffentliche Inhalte sind unter thw-trainer.de verfügbar";

    return response($robotsContent, 200)
        ->header('Content-Type', 'text/plain');
});

Route::get('/dashboard', function () {
    $user = auth()->user()->fresh(); // Fresh reload from database
    
    // Cache total questions count für 1 Stunde
    $totalQuestions = cache()->remember('total_questions_count', 3600, function() {
        return \App\Models\Question::count();
    });
    
    // Hole die letzten 5 Prüfungsergebnisse
    $recentExams = \App\Models\ExamStatistic::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    return view('dashboard', compact('user', 'recentExams', 'totalQuestions'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/dashboard/dismiss-email-consent-banner', function () {
    session(['email_consent_banner_dismissed' => true]);
    return response()->json(['success' => true]);
})->middleware('auth')->name('dashboard.dismiss-email-consent-banner');

Route::middleware('auth')->group(function () {
    Route::get('/profile', function() {
        $user = auth()->user()->fresh(); // Fresh reload from database
        return view('profile', compact('user'));
    })->name('profile');
    Route::patch('/profile', function(Request $request) {
        \Log::info('Profile route reached via PATCH');
        return app(ProfileController::class)->update($request);
    })->name('profile.update');
    Route::patch('/profile/password', function(Request $request) {
        \Log::info('Password update route reached via PATCH');
        return app(ProfileController::class)->updatePassword($request);
    })->name('profile.password.update');
    Route::post('/profile/dismiss-leaderboard-banner', [ProfileController::class, 'dismissLeaderboardBanner'])->name('profile.dismiss.leaderboard.banner');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Contact Routes - Nur in Production als Redirect zur Landing-Domain
// In Development werden die Landing-Routes direkt verwendet
if (!config('domains.development', true)) {
    Route::get('/kontakt', function () {
        return redirect('https://' . config('domains.landing') . '/kontakt');
    })->name('contact.index');
}

Route::middleware('auth')->group(function () {
    // Practice Menu und Modi
    Route::get('/practice-menu', [\App\Http\Controllers\PracticeController::class, 'menu'])->name('practice.menu');
    Route::get('/practice/all', [\App\Http\Controllers\PracticeController::class, 'all'])->name('practice.all');
    Route::get('/practice/unsolved', [\App\Http\Controllers\PracticeController::class, 'unsolved'])->name('practice.unsolved');
    Route::get('/practice/section/{section}', [\App\Http\Controllers\PracticeController::class, 'section'])->name('practice.section');
    Route::get('/practice/search', [\App\Http\Controllers\PracticeController::class, 'search'])->name('practice.search');
    
    // Bookmark Routes
    Route::get('/bookmarks', [\App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/toggle', [\App\Http\Controllers\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/bookmarks/practice', [\App\Http\Controllers\BookmarkController::class, 'practice'])->name('bookmarks.practice');
    
    // Gamification Routes
    Route::get('/achievements', [\App\Http\Controllers\GamificationController::class, 'achievements'])->name('gamification.achievements');
    Route::get('/leaderboard', [\App\Http\Controllers\GamificationController::class, 'leaderboard'])->name('gamification.leaderboard');

    // Notification Routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/clear-read', [\App\Http\Controllers\NotificationController::class, 'clearRead'])->name('notifications.clear-read');
    
    // Lehrgang Routes (für Kurse)
    Route::get('/lehrgaenge', [\App\Http\Controllers\LehrgangController::class, 'index'])->name('lehrgaenge.index');
    Route::get('/lehrgaenge/{slug}', [\App\Http\Controllers\LehrgangController::class, 'show'])->name('lehrgaenge.show');
    Route::post('/lehrgaenge/{slug}/enroll', [\App\Http\Controllers\LehrgangController::class, 'enroll'])->name('lehrgaenge.enroll');
    Route::get('/lehrgaenge/{slug}/practice', [\App\Http\Controllers\LehrgangController::class, 'practice'])->name('lehrgaenge.practice');
    Route::get('/lehrgaenge/{slug}/practice-section/{sectionNr}', [\App\Http\Controllers\LehrgangController::class, 'practiceSection'])->name('lehrgaenge.practice-section');
    Route::post('/lehrgaenge/{slug}/submit', [\App\Http\Controllers\LehrgangController::class, 'submitAnswer'])->name('lehrgaenge.submit');
    Route::post('/lehrgaenge/{slug}/unenroll', [\App\Http\Controllers\LehrgangController::class, 'unenroll'])->name('lehrgaenge.unenroll');
    Route::post('/lehrgaenge/question/{questionId}/report-issue', [\App\Http\Controllers\LehrgangController::class, 'reportIssue'])->name('lehrgaenge.report-issue');
    
    // Alte Practice Routen (jetzt als Fortsetzung der Session)
    Route::get('/practice', [\App\Http\Controllers\PracticeController::class, 'show'])->name('practice.index');
    Route::post('/practice', [\App\Http\Controllers\PracticeController::class, 'submit'])->name('practice.submit');
    
    // Failed Practice - jetzt über PracticeController (gleiche Logik wie andere Modi)
    Route::get('/failed', [\App\Http\Controllers\PracticeController::class, 'failed'])->name('failed.index');
    
    Route::get('/exam', [\App\Http\Controllers\ExamController::class, 'start'])->name('exam.index');
    Route::post('/exam/submit', [\App\Http\Controllers\ExamController::class, 'submit'])->name('exam.submit');
    
    // Ortsverband Routes
    Route::prefix('ortsverband')->name('ortsverband.')->group(function () {
        // Übersicht & Erstellen
        Route::get('/', [\App\Http\Controllers\OrtsverbandController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\OrtsverbandController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\OrtsverbandController::class, 'store'])->name('store');
        
        // Beitreten per Code (für eingeloggte User)
        Route::post('/join', [\App\Http\Controllers\OrtsverbandInvitationController::class, 'joinByCode'])->name('join.code');
        
        // Einzelner Ortsverband (für alle Mitglieder)
        Route::get('/{ortsverband}', [\App\Http\Controllers\OrtsverbandController::class, 'show'])->name('show');
        Route::delete('/{ortsverband}/leave', [\App\Http\Controllers\OrtsverbandController::class, 'leave'])->name('leave');

        // Lernpools für Mitglieder (Einschreiben & Lernen) - für alle Mitglieder
        Route::post('/{ortsverband}/lernpools/{lernpool}/enroll', [\App\Http\Controllers\OrtsverbandLernpoolController::class, 'enroll'])->name('lernpools.enroll');
        Route::get('/{ortsverband}/lernpools/{lernpool}/practice', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'show'])->name('lernpools.practice');
        Route::post('/{ortsverband}/lernpools/{lernpool}/answer', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'answer'])->name('lernpools.answer');
        Route::post('/{ortsverband}/lernpools/{lernpool}/unenroll', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'unenroll'])->name('lernpools.unenroll');

        // Nur für Ausbildungsbeauftragte
        Route::middleware(['ortsverband.ausbildungsbeauftragter'])->group(function () {
            Route::get('/{ortsverband}/edit', [\App\Http\Controllers\OrtsverbandController::class, 'edit'])->name('edit');
            Route::put('/{ortsverband}', [\App\Http\Controllers\OrtsverbandController::class, 'update'])->name('update');
            Route::delete('/{ortsverband}', [\App\Http\Controllers\OrtsverbandController::class, 'destroy'])->name('destroy');

            // Mitglieder verwalten
            Route::get('/{ortsverband}/members', [\App\Http\Controllers\OrtsverbandController::class, 'members'])->name('members');
            Route::delete('/{ortsverband}/members/{user}', [\App\Http\Controllers\OrtsverbandController::class, 'removeMember'])->name('members.remove');
            Route::put('/{ortsverband}/members/{user}/role', [\App\Http\Controllers\OrtsverbandController::class, 'changeRole'])->name('members.role');

            // Dashboard & Statistiken
            Route::get('/{ortsverband}/dashboard', [\App\Http\Controllers\OrtsverbandController::class, 'dashboard'])->name('dashboard');
            Route::get('/{ortsverband}/stats', [\App\Http\Controllers\OrtsverbandController::class, 'stats'])->name('stats');
            Route::post('/{ortsverband}/toggle-ranking', [\App\Http\Controllers\OrtsverbandController::class, 'toggleRankingVisibility'])->name('toggle-ranking');

            // Einladungen
            Route::get('/{ortsverband}/invitations', [\App\Http\Controllers\OrtsverbandInvitationController::class, 'index'])->name('invitations.index');
            Route::post('/{ortsverband}/invitations', [\App\Http\Controllers\OrtsverbandInvitationController::class, 'store'])->name('invitations.store');
            Route::delete('/{ortsverband}/invitations/{invitation}', [\App\Http\Controllers\OrtsverbandInvitationController::class, 'destroy'])->name('invitations.destroy');
            Route::put('/{ortsverband}/invitations/{invitation}/toggle', [\App\Http\Controllers\OrtsverbandInvitationController::class, 'toggle'])->name('invitations.toggle');
            Route::get('/{ortsverband}/invitations/{invitation}/qrcode', [\App\Http\Controllers\OrtsverbandInvitationController::class, 'qrcode'])->name('invitations.qrcode');

            // Lernpools für Ausbilder (CRUD)
            Route::resource('/{ortsverband}/lernpools', \App\Http\Controllers\OrtsverbandLernpoolController::class)->names('lernpools');
            Route::resource('/{ortsverband}/lernpools/{lernpool}/questions', \App\Http\Controllers\OrtsverbandLernpoolQuestionController::class)->names('lernpools.questions');
        });
    });
    
    // Beitritt über Einladungslink (außerhalb Auth)
    Route::get('/join/{code}', [\App\Http\Controllers\OrtsverbandInvitationController::class, 'join'])->name('ortsverband.join');
});

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [\App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics');
    Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class);
    Route::post('questions/{question}/update-field', [\App\Http\Controllers\Admin\QuestionController::class, 'updateField'])->name('questions.update-field');
    Route::resource('lehrgaenge', \App\Http\Controllers\Admin\LehrgangController::class);
    Route::post('lehrgaenge/{lehrgang}/import-csv', [\App\Http\Controllers\Admin\LehrgangController::class, 'importCSV'])->name('lehrgaenge.import-csv');
    Route::patch('lehrgaenge/{lehrgang}/question/{question}', [\App\Http\Controllers\Admin\LehrgangController::class, 'updateQuestion'])->name('lehrgaenge.update-question');
    Route::delete('lehrgaenge/{question}/delete-question', [\App\Http\Controllers\Admin\LehrgangController::class, 'deleteQuestion'])->name('lehrgaenge.delete-question');
    Route::resource('lehrgang-issues', \App\Http\Controllers\Admin\LehrgangIssueController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::model('lehrgang_issue', \App\Models\LehrgangQuestionIssue::class);
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::get('users/{id}/progress', [\App\Http\Controllers\Admin\UserController::class, 'editProgress'])->name('users.progress.edit');
    Route::put('users/{id}/progress', [\App\Http\Controllers\Admin\UserController::class, 'updateProgress'])->name('users.progress.update');
    
    // Newsletter Routes
    Route::get('newsletter/create', [\App\Http\Controllers\NewsletterController::class, 'create'])->name('newsletter.create');
    Route::post('newsletter/test', [\App\Http\Controllers\NewsletterController::class, 'sendTest'])->name('newsletter.test');
    Route::post('newsletter/send', [\App\Http\Controllers\NewsletterController::class, 'sendToAll'])->name('newsletter.send');
    Route::get('newsletter', [\App\Http\Controllers\NewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('newsletter/{newsletter}', [\App\Http\Controllers\NewsletterController::class, 'show'])->name('newsletter.show');
    
    // Contact Messages Routes
    Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::patch('contact-messages/{contactMessage}/mark-read', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-read');
    Route::patch('contact-messages/{contactMessage}/mark-unread', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markAsUnread'])->name('contact-messages.mark-unread');
    Route::delete('contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    Route::delete('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'bulkDelete'])->name('contact-messages.bulk-delete');

    // Ortsverband Routes (Admin) - Nur View und Delete
    Route::get('ortsverband', [\App\Http\Controllers\Admin\OrtsverbandController::class, 'index'])->name('ortsverband.index');
    Route::post('ortsverband/{ortsverband}/view-as', [\App\Http\Controllers\Admin\OrtsverbandController::class, 'viewAs'])->name('ortsverband.view-as');
    Route::post('ortsverband/exit-view', [\App\Http\Controllers\Admin\OrtsverbandController::class, 'exitView'])->name('ortsverband.exit-view');
    Route::delete('ortsverband/{ortsverband}', [\App\Http\Controllers\Admin\OrtsverbandController::class, 'destroy'])->name('ortsverband.destroy');
});

// Test Routes für Error Pages (nur für Development/Testing)
if (config('app.debug')) {
    Route::prefix('test-errors')->group(function () {
        Route::get('/404', function () {
            abort(404);
        });
        
        Route::get('/403', function () {
            abort(403);
        });
        
        Route::get('/500', function () {
            abort(500);
        });
        
        Route::get('/503', function () {
            abort(503);
        });
        
        Route::get('/419', function () {
            abort(419);
        });
        
        Route::get('/429', function () {
            abort(429);
        });
        
        // Übersichtsseite
        Route::get('/', function () {
            return view('errors.test-overview');
        })->name('test.errors');
    });
}

require __DIR__.'/auth.php';
