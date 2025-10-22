<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/impressum', function () {
    return view('impressum');
})->name('impressum');

Route::get('/datenschutz', function () {
    return view('datenschutz');
})->name('datenschutz');

// Offline-Seite für PWA
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// Kontaktformular (öffentlich zugänglich)
Route::get('/kontakt', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/kontakt', [\App\Http\Controllers\ContactController::class, 'store'])
    ->middleware('throttle:3,60') // Max 3 Anfragen pro Stunde
    ->name('contact.submit');

// Öffentliche Statistik-Seite
Route::get('/statistik', [\App\Http\Controllers\StatisticsController::class, 'index'])->name('statistics');

// Dynamische robots.txt basierend auf Umgebung
Route::get('/robots.txt', function () {
    $isTestEnvironment = app()->environment('testing') || str_contains(request()->getHost(), 'test.') || config('app.environment_type') === 'testing';
    
    if ($isTestEnvironment) {
        return response("User-agent: *\nDisallow: /", 200)
            ->header('Content-Type', 'text/plain');
    } else {
        $robotsContent = "User-agent: *
Allow: /
Allow: /register
Allow: /login
Allow: /guest/*
Allow: /assets/*
Allow: /build/*

# Wichtige Seiten für Crawler
Allow: /
Allow: /guest/practice/menu
Allow: /guest/exam

# Geschützte Bereiche
Disallow: /dashboard
Disallow: /practice
Disallow: /exam
Disallow: /admin
Disallow: /profile
Disallow: /failed
Disallow: /bookmarks

# Admin und private Bereiche
Disallow: /admin/*
Disallow: /api/*
Disallow: /telescope/*

# Cache und temporäre Dateien
Disallow: /storage/*
Disallow: /vendor/*
Disallow: /node_modules/*

# Sitemap
Sitemap: " . url('/sitemap.xml') . "

# Crawl-Delay für bessere Performance
Crawl-delay: 1";
        
        return response($robotsContent, 200)
            ->header('Content-Type', 'text/plain');
    }
});

// Guest Routes (ohne Auth)
Route::prefix('guest')->name('guest.')->group(function () {
    Route::get('/practice-menu', [\App\Http\Controllers\GuestPracticeController::class, 'menu'])->name('practice.menu');
    Route::get('/practice/all', [\App\Http\Controllers\GuestPracticeController::class, 'all'])->name('practice.all');
    Route::get('/practice', [\App\Http\Controllers\GuestPracticeController::class, 'show'])->name('practice.index');
    Route::post('/practice', [\App\Http\Controllers\GuestPracticeController::class, 'submit'])->name('practice.submit');
    Route::get('/exam', [\App\Http\Controllers\GuestExamController::class, 'start'])->name('exam.index');
    Route::post('/exam/submit', [\App\Http\Controllers\GuestExamController::class, 'submit'])->name('exam.submit');
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
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Contact Routes (verfügbar für alle - auch Gäste)
Route::get('/kontakt', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/kontakt', [\App\Http\Controllers\ContactController::class, 'store'])
    ->middleware('throttle:3,60') // 3 Anfragen pro 60 Minuten
    ->name('contact.submit');

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
    
    // Test route for notifications (temporary)
    Route::get('/test-notification', function() {
        $service = new \App\Services\GamificationService();
        $user = auth()->user();
        
        // Test achievement unlock
        $service->unlockAchievement($user, 'first_question');
        
        return redirect()->route('dashboard')->with('success', 'Test notification triggered');
    })->name('test.notification');
    
    // Alte Practice Routen (jetzt als Fortsetzung der Session)
    Route::get('/practice', [\App\Http\Controllers\PracticeController::class, 'show'])->name('practice.index');
    Route::post('/practice', [\App\Http\Controllers\PracticeController::class, 'submit'])->name('practice.submit');
    
    // Failed Practice - jetzt über PracticeController (gleiche Logik wie andere Modi)
    Route::get('/failed', [\App\Http\Controllers\PracticeController::class, 'failed'])->name('failed.index');
    
    Route::get('/exam', [\App\Http\Controllers\ExamController::class, 'start'])->name('exam.index');
    Route::post('/exam/submit', [\App\Http\Controllers\ExamController::class, 'submit'])->name('exam.submit');
});

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class);
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
