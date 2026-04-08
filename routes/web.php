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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Root-Route für App-Subdomain - Redirect zu Dashboard oder Login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('app.home');

// Health-Check für externes Monitoring (Uptime Robot, etc.)
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $db = 'ok';
    } catch (\Exception $e) {
        $db = 'error';
    }
    $status = $db === 'ok' ? 'ok' : 'degraded';
    return response()->json([
        'status'    => $status,
        'db'        => $db,
        'timestamp' => now()->toIso8601String(),
    ], $status === 'ok' ? 200 : 503);
})->name('health');

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

    // Alte Onboarding-Seite überspringen, neue Tour läuft direkt im Dashboard
    if (!$user->onboarding_completed) {
        $user->onboarding_completed = true;
        $user->save();
    }

    // Cache total questions count für 1 Stunde
    $totalQuestions = cache()->remember('total_questions_count', 3600, function() {
        return \App\Models\Question::count();
    });

    // Hole die letzten 5 Prüfungsergebnisse
    $recentExams = \App\Models\ExamStatistic::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    // Spaced Repetition fällige Fragen
    $srService = new \App\Services\SpacedRepetitionService();
    $spacedRepetitionDue = $srService->getDueCount($user->id);

    // Wöchentliche Aktivität (letzte 7 Tage)
    $weeklyActivity = \DB::table('question_statistics')
        ->where('user_id', $user->id)
        ->where('created_at', '>=', now()->subDays(7))
        ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(is_correct) as correct')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Stärken/Schwächen pro Lernabschnitt
    $sectionStats = \DB::table('question_statistics')
        ->join('questions', 'question_statistics.question_id', '=', 'questions.id')
        ->where('question_statistics.user_id', $user->id)
        ->selectRaw('questions.lernabschnitt, COUNT(*) as total, SUM(question_statistics.is_correct) as correct')
        ->groupBy('questions.lernabschnitt')
        ->orderBy('questions.lernabschnitt')
        ->get();

    // Streak Freeze Status
    $gamificationService = new \App\Services\GamificationService();
    $streakFreezeStatus = $gamificationService->getStreakFreezeStatus($user);

    // Smart Action Card Logic
    $activeLernsession = app(\App\Services\LernsessionService::class)
        ->getActiveSessionsForUser($user)->first();

    $failedArr = is_string($user->exam_failed_questions)
        ? json_decode($user->exam_failed_questions, true)
        : ($user->exam_failed_questions ?? []);
    $hasFailedQuestions = !empty($failedArr);

    $progress = \App\Models\UserQuestionProgress::where('user_id', $user->id)
        ->where('consecutive_correct', '>=', 3)->count();
    $canStartExam = ($progress >= $totalQuestions && !$hasFailedQuestions);
    $progressPercent = $totalQuestions > 0 ? round(($progress / $totalQuestions) * 100) : 0;

    $exams = \App\Models\ExamStatistic::where('user_id', $user->id)
        ->where('is_passed', true)->count();

    // Smart Action priority
    $smartAction = null;
    if ($activeLernsession) {
        $smartAction = [
            'type' => 'live', 'label' => 'Live',
            'title' => 'Aktive Session läuft',
            'desc' => $activeLernsession->learningSession->title ?? 'Lernsession',
            'route' => route('lernsession.live', $activeLernsession),
            'btn' => 'Beitreten',
        ];
    } elseif ($hasFailedQuestions) {
        $smartAction = [
            'type' => 'urgent', 'label' => 'Dringend',
            'title' => count($failedArr) . ' Fehlerfragen wiederholen',
            'desc' => 'Korrigiere deine Fehler aus der letzten Prüfung',
            'route' => route('failed.index'),
            'btn' => 'Wiederholen',
        ];
    } elseif ($spacedRepetitionDue > 0) {
        $smartAction = [
            'type' => 'recommended', 'label' => 'Empfohlen',
            'title' => $spacedRepetitionDue . ' Fragen zur Wiederholung fällig',
            'desc' => 'Spaced Repetition — halte dein Wissen frisch',
            'route' => route('practice.spaced-repetition'),
            'btn' => 'Wiederholen',
        ];
    } elseif ($canStartExam) {
        $examPassedCount = $user->exam_passed_count ?? 0;
        $smartAction = match(true) {
            $examPassedCount >= 5 => [
                'type' => 'ready', 'label' => 'Prüfungsbereit',
                'title' => $examPassedCount . '/5 Prüfungen bestanden — bereit für die echte Prüfung!',
                'desc' => 'Du bist bestens vorbereitet. Übe weiter um sicher zu bleiben.',
                'route' => route('exam.index'),
                'btn' => 'Prüfung starten',
            ],
            $examPassedCount === 4 => [
                'type' => 'ready', 'label' => 'Fast geschafft',
                'title' => '4/5 Prüfungen bestanden — noch eine!',
                'desc' => 'Nur noch eine bestandene Prüfung bis zur Prüfungsbereitschaft',
                'route' => route('exam.index'),
                'btn' => 'Prüfung starten',
            ],
            $examPassedCount >= 1 => [
                'type' => 'ready', 'label' => 'Dranbleiben',
                'title' => $examPassedCount . '/5 Prüfungen bestanden',
                'desc' => 'Übe weiter um sicherer zu werden',
                'route' => route('exam.index'),
                'btn' => 'Prüfung starten',
            ],
            default => [
                'type' => 'ready', 'label' => 'Bereit',
                'title' => 'Alle Fragen gemeistert — Prüfung ablegen!',
                'desc' => 'Starte deine erste Prüfungssimulation',
                'route' => route('exam.index'),
                'btn' => 'Prüfung starten',
            ],
        };
    } else {
        $solvedCount = \App\Models\UserQuestionProgress::where('user_id', $user->id)->count();
        if ($solvedCount > 0) {
            // Prio 1: Fällige SR-Wiederholungen
            $dueSrCount = \App\Models\UserQuestionProgress::where('user_id', $user->id)
                ->whereNotNull('next_review_at')
                ->where('next_review_at', '<=', now())
                ->count();

            if ($dueSrCount > 0) {
                $smartAction = [
                    'type' => 'continue', 'label' => 'Wiederholung',
                    'title' => $dueSrCount . ' ' . ($dueSrCount === 1 ? 'Frage' : 'Fragen') . ' zur Wiederholung',
                    'desc' => 'Fällige Spaced-Repetition Fragen',
                    'route' => route('practice.spaced-repetition'),
                    'btn' => 'Wiederholen',
                ];
            } else {
                // Prio 2: Niedrigster Lernabschnitt mit offenen Fragen
                $masteredIds = \App\Models\UserQuestionProgress::getMasteredQuestions($user->id);
                $futureSrIds = \App\Models\UserQuestionProgress::where('user_id', $user->id)
                    ->whereNotNull('next_review_at')
                    ->where('next_review_at', '>', now())
                    ->pluck('question_id')
                    ->toArray();
                $excludedIds = array_unique(array_merge($masteredIds, $futureSrIds));

                $bestSection = \App\Models\Question::whereNotIn('id', $excludedIds)
                    ->selectRaw('lernabschnitt, COUNT(*) as open_count')
                    ->groupBy('lernabschnitt')
                    ->orderByRaw('CAST(lernabschnitt AS UNSIGNED)')
                    ->first();

                if ($bestSection) {
                    $sectionNum = $bestSection->lernabschnitt;
                    $smartAction = [
                        'type' => 'continue', 'label' => 'Weitermachen',
                        'title' => 'Weiter mit Lernabschnitt ' . $sectionNum,
                        'desc' => 'Du bist auf einem guten Weg',
                        'route' => route('practice.section', $sectionNum),
                        'btn' => 'Starten',
                    ];
                } else {
                    // Alle Fragen gemeistert oder in SR geplant
                    if (count($masteredIds) > 0) {
                        // User hat gemeisterte Fragen die frei wiederholbar sind
                        $smartAction = [
                            'type' => 'continue', 'label' => 'Weitermachen',
                            'title' => 'Alle Fragen bearbeitet',
                            'desc' => 'Wiederhole beliebige Fragen',
                            'route' => route('practice.all'),
                            'btn' => 'Starten',
                        ];
                    } else {
                        // Alle Fragen in zukünftiger SR — heute nichts zu tun
                        $nextReview = \App\Models\UserQuestionProgress::where('user_id', $user->id)
                            ->whereNotNull('next_review_at')
                            ->where('next_review_at', '>', now())
                            ->orderBy('next_review_at')
                            ->first();

                        $nextReviewText = 'Komm später wieder';
                        if ($nextReview) {
                            $nextDate = \Carbon\Carbon::parse($nextReview->next_review_at);
                            $diffDays = (int) now()->startOfDay()->diffInDays($nextDate->startOfDay());
                            $nextReviewText = match(true) {
                                $diffDays === 0 => 'Nächste Wiederholung: Heute',
                                $diffDays === 1 => 'Nächste Wiederholung: Morgen',
                                default => 'Nächste Wiederholung in ' . $diffDays . ' Tagen',
                            };
                        }

                        $smartAction = [
                            'type' => 'info', 'label' => 'Erledigt',
                            'title' => 'Super gemacht! Für heute alles geschafft',
                            'desc' => $nextReviewText,
                            'route' => null,
                            'btn' => null,
                        ];
                    }
                }
            }
        } else {
            $smartAction = [
                'type' => 'start', 'label' => "Los geht's",
                'title' => 'Starte mit deiner ersten Frage',
                'desc' => 'Beginne deine Reise zur Grundausbildungsprüfung',
                'route' => route('practice.all'),
                'btn' => 'Erste Frage',
            ];
        }
    }

    // Exam countdown
    $examCountdown = null;
    if ($user->exam_date && $user->exam_date->isFuture()) {
        $daysLeft = (int) now()->startOfDay()->diffInDays($user->exam_date, false);
        $remaining = $totalQuestions - $progress;
        $effectiveDays = max($daysLeft - 1, 1);
        $dailyTarget = $remaining > 0 ? (int) ceil($remaining / $effectiveDays) : 0;
        $todayAnswered = \App\Models\QuestionStatistic::where('user_id', $user->id)
            ->whereDate('created_at', today())->count();
        $examCountdown = compact('daysLeft', 'dailyTarget', 'todayAnswered');
    }

    // Enrolled Lehrgänge
    $enrolledLehrgaenge = $user->enrolledLehrgaenge()->get();

    // Streak at risk
    $streakAtRisk = $user->streak_days > 0
        && (!$user->last_activity_date || \Carbon\Carbon::parse($user->last_activity_date)->lt(\Carbon\Carbon::today()));

    // Liga-Position (innerhalb der aktuellen Liga)
    $leagueRank = null;
    $leagueSize = null;
    if ($user->leaderboard_consent) {
        $userLeague = $user->league ?? 'bronze';
        $leagueRank = \App\Models\User::where('leaderboard_consent', true)
            ->where('league', $userLeague)
            ->where('weekly_points', '>', $user->weekly_points ?? 0)->count() + 1;
        $leagueSize = \App\Models\User::where('leaderboard_consent', true)
            ->where('league', $userLeague)
            ->where('weekly_points', '>', 0)->count();
    }

    // Mastery percent (for journey stepper) — reuse $progress from above
    $masteryPercent = $progressPercent;

    // Solved questions total
    $solvedTotal = \App\Models\UserQuestionProgress::where('user_id', $user->id)->count();
    $solvedPercent = $totalQuestions > 0 ? round(($solvedTotal / $totalQuestions) * 100) : 0;

    // XP level progress
    $gamificationService = app(\App\Services\GamificationService::class);
    $levelProgress = $gamificationService->getLevelProgress($user);
    $nextLevelPoints = $gamificationService->getNextLevelPoints($user);

    // Unopened lootboxes
    $unopenedLootboxes = \App\Models\Lootbox::where('user_id', $user->id)
        ->where('opened', false)->count();

    // Today stats (for weekly summary)
    $todayAnswered = \App\Models\QuestionStatistic::where('user_id', $user->id)
        ->whereDate('created_at', today())->count();
    $todayCorrect = \App\Models\QuestionStatistic::where('user_id', $user->id)
        ->whereDate('created_at', today())->where('is_correct', true)->count();

    // Dynamisches Streak-Tagesziel (max. 20 für Streak-Qualifikation)
    $dailyStreakGoal = min(
        \App\Services\GamificationService::STREAK_MIN_QUESTIONS,
        $user->daily_streak_goal ?? $gamificationService->calculateDailyStreakGoal($user)
    );

    return view('dashboard', compact(
        'user', 'recentExams', 'totalQuestions', 'spacedRepetitionDue',
        'weeklyActivity', 'sectionStats', 'streakFreezeStatus',
        'smartAction', 'examCountdown', 'enrolledLehrgaenge',
        'streakAtRisk', 'leagueRank', 'leagueSize', 'progressPercent',
        'masteryPercent', 'solvedPercent', 'solvedTotal',
        'canStartExam', 'exams', 'hasFailedQuestions',
        'levelProgress', 'nextLevelPoints', 'unopenedLootboxes',
        'todayAnswered', 'todayCorrect', 'dailyStreakGoal'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/user-statistik', [\App\Http\Controllers\StatisticsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('statistics');

Route::post('/dashboard/dismiss-email-consent-banner', function () {
    session(['email_consent_banner_dismissed' => true]);
    return response()->json(['success' => true]);
})->middleware('auth')->name('dashboard.dismiss-email-consent-banner');

// Onboarding Routes (Legacy-Redirects + Tour-Endpoint)
Route::get('/onboarding', function () { return redirect()->route('dashboard'); })->middleware(['auth', 'verified'])->name('onboarding');
Route::post('/onboarding/complete', function () { return redirect()->route('dashboard'); })->middleware(['auth', 'verified'])->name('onboarding.complete');
Route::post('/onboarding/skip', function () { return redirect()->route('dashboard'); })->middleware(['auth', 'verified'])->name('onboarding.skip');
Route::post('/onboarding/tour-complete', [\App\Http\Controllers\OnboardingController::class, 'tourComplete'])->middleware(['auth', 'verified'])->name('onboarding.tour.complete');
Route::post('/streak/freeze', [\App\Http\Controllers\GamificationController::class, 'useStreakFreeze'])->middleware(['auth', 'verified'])->name('streak.freeze');

Route::middleware('auth')->group(function () {
    Route::get('/profile', function() {
        $user = auth()->user()->fresh(); // Fresh reload from database
        $shopService = new \App\Services\ShopService();
        $ownedAccessories = $shopService->getOwnedAccessories($user);
        return view('profile', compact('user', 'ownedAccessories'));
    })->name('profile');
    Route::patch('/profile', function(Request $request) {
        \Log::info('Profile route reached via PATCH');
        return app(ProfileController::class)->update($request);
    })->name('profile.update');
    Route::patch('/profile/password', function(Request $request) {
        \Log::info('Password update route reached via PATCH');
        return app(ProfileController::class)->updatePassword($request);
    })->name('profile.password.update');
    Route::post('/profile/avatar/regenerate', [ProfileController::class, 'regenerateAvatar'])->name('profile.avatar.regenerate');
    Route::post('/profile/accessory/toggle', [ProfileController::class, 'toggleAccessory'])->name('profile.accessory.toggle');
    Route::post('/profile/accessory/color', [ProfileController::class, 'updateAccessoryColor'])->name('profile.accessory.color');
    Route::post('/profile/dismiss-leaderboard-banner', [ProfileController::class, 'dismissLeaderboardBanner'])->name('profile.dismiss.leaderboard.banner');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/cancel-email-change', [ProfileController::class, 'cancelEmailChange'])->name('profile.cancel-email-change');
});

// Contact Routes - für eingeloggte Nutzer
Route::middleware('auth')->group(function () {
    Route::get('/kontakt', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
    Route::post('/kontakt', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.submit');
});

// Nutzerumfrage
Route::middleware('auth')->group(function () {
    Route::get('/umfrage', [\App\Http\Controllers\SurveyController::class, 'index'])->name('umfrage.index');
    Route::post('/umfrage', [\App\Http\Controllers\SurveyController::class, 'store'])->name('umfrage.store');
    Route::delete('/umfrage/{response}', [\App\Http\Controllers\SurveyController::class, 'destroy'])->name('umfrage.destroy');
});

Route::middleware('auth')->group(function () {
    // Practice Menu und Modi
    Route::get('/practice-menu', [\App\Http\Controllers\PracticeController::class, 'menu'])->name('practice.menu');
    Route::get('/practice/all', [\App\Http\Controllers\PracticeController::class, 'all'])->name('practice.all');
    Route::get('/practice/unsolved', [\App\Http\Controllers\PracticeController::class, 'unsolved'])->name('practice.unsolved');
    Route::get('/practice/section/{section}', [\App\Http\Controllers\PracticeController::class, 'section'])->name('practice.section');
    Route::get('/practice/search', [\App\Http\Controllers\PracticeController::class, 'search'])->name('practice.search');
    Route::get('/practice/spaced-repetition', [\App\Http\Controllers\PracticeController::class, 'spacedRepetition'])->name('practice.spaced-repetition');

    // Bookmark Routes
    Route::get('/bookmarks', [\App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/toggle', [\App\Http\Controllers\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/bookmarks/practice', [\App\Http\Controllers\BookmarkController::class, 'practice'])->name('bookmarks.practice');
    
    // Gamification Routes
    Route::get('/achievements', [\App\Http\Controllers\GamificationController::class, 'achievements'])->name('gamification.achievements');
    Route::get('/leaderboard', [\App\Http\Controllers\GamificationController::class, 'leaderboard'])->name('gamification.leaderboard');
    Route::get('/lootboxes', [\App\Http\Controllers\GamificationController::class, 'lootboxes'])->name('gamification.lootboxes');
    Route::post('/lootbox/open', [\App\Http\Controllers\GamificationController::class, 'openLootbox'])->name('gamification.lootbox.open');

    // Shop Routes
    Route::get('/shop', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
    Route::post('/shop/purchase', [\App\Http\Controllers\ShopController::class, 'purchase'])->name('shop.purchase');

    // Lernsession Routes
    Route::prefix('lernsessions')->name('lernsession.')->group(function () {
        Route::get('/', [\App\Http\Controllers\LernsessionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\LernsessionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\LernsessionController::class, 'store'])->name('store');
        Route::get('/{lernsession}', [\App\Http\Controllers\LernsessionController::class, 'show'])->name('show');
        Route::get('/{lernsession}/edit', [\App\Http\Controllers\LernsessionController::class, 'edit'])->name('edit');
        Route::put('/{lernsession}', [\App\Http\Controllers\LernsessionController::class, 'update'])->name('update');
        Route::delete('/{lernsession}', [\App\Http\Controllers\LernsessionController::class, 'destroy'])->name('destroy');

        // Instanz-Teilnahme
        Route::post('/instance/{instance}/join', [\App\Http\Controllers\LernsessionController::class, 'join'])->name('join');
        Route::post('/instance/{instance}/leave', [\App\Http\Controllers\LernsessionController::class, 'leave'])->name('leave');
        Route::get('/instance/{instance}/live', [\App\Http\Controllers\LernsessionController::class, 'live'])->name('live');

        // API für Alpine.js Polling (JSON)
        Route::get('/instance/{instance}/ranking', [\App\Http\Controllers\LernsessionController::class, 'ranking'])->name('ranking');
        Route::get('/instance/{instance}/stats', [\App\Http\Controllers\LernsessionController::class, 'stats'])->name('stats');
    });

    // Notification Routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/clear-read', [\App\Http\Controllers\NotificationController::class, 'clearRead'])->name('notifications.clear-read');

    // Push Notification Routes
    Route::get('/push/public-key', [\App\Http\Controllers\PushController::class, 'publicKey'])->name('push.public-key');
    Route::post('/push/subscribe', [\App\Http\Controllers\PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushController::class, 'unsubscribe'])->name('push.unsubscribe');

    // Lehrgang Routes (für Kurse)
    Route::get('/lehrgaenge', [\App\Http\Controllers\LehrgangController::class, 'index'])->name('lehrgaenge.index');
    Route::get('/lehrgaenge/{slug}', [\App\Http\Controllers\LehrgangController::class, 'show'])->name('lehrgaenge.show');
    Route::post('/lehrgaenge/{slug}/enroll', [\App\Http\Controllers\LehrgangController::class, 'enroll'])->name('lehrgaenge.enroll');
    Route::get('/lehrgaenge/{slug}/practice', [\App\Http\Controllers\LehrgangController::class, 'practice'])->name('lehrgaenge.practice');
    Route::get('/lehrgaenge/{slug}/practice/unsolved', [\App\Http\Controllers\LehrgangController::class, 'practiceUnsolved'])->name('lehrgaenge.practice.unsolved');
    Route::get('/lehrgaenge/{slug}/practice/section/{nr}', [\App\Http\Controllers\LehrgangController::class, 'practiceSection'])->name('lehrgaenge.practice.section');
    Route::get('/lehrgaenge/{slug}/practice/show', [\App\Http\Controllers\LehrgangController::class, 'practiceShow'])->name('lehrgaenge.practice.show');
    Route::post('/lehrgaenge/{slug}/practice/submit', [\App\Http\Controllers\LehrgangController::class, 'practiceSubmit'])->name('lehrgaenge.practice.submit');
    Route::get('/lehrgaenge/{slug}/practice/summary', [\App\Http\Controllers\LehrgangController::class, 'practiceSummary'])->name('lehrgaenge.practice.summary');
    Route::post('/lehrgaenge/{slug}/unenroll', [\App\Http\Controllers\LehrgangController::class, 'unenroll'])->name('lehrgaenge.unenroll');
    Route::post('/lehrgaenge/question/{questionId}/report-issue', [\App\Http\Controllers\LehrgangController::class, 'reportIssue'])->name('lehrgaenge.report-issue');
    Route::post('/practice/question/{questionId}/report-issue', [\App\Http\Controllers\PracticeController::class, 'reportIssue'])->name('practice.report-issue');

    // Practice Session Summary
    Route::get('/practice/summary', [\App\Http\Controllers\PracticeController::class, 'summary'])->name('practice.summary');

    // Alte Practice Routen (jetzt als Fortsetzung der Session)
    Route::get('/practice', [\App\Http\Controllers\PracticeController::class, 'show'])->name('practice.index');
    Route::post('/practice', [\App\Http\Controllers\PracticeController::class, 'submit'])->name('practice.submit');
    
    // Failed Practice - jetzt über PracticeController (gleiche Logik wie andere Modi)
    Route::get('/failed', [\App\Http\Controllers\PracticeController::class, 'failed'])->name('failed.index');
    
    Route::get('/exam', [\App\Http\Controllers\ExamController::class, 'start'])->name('exam.index');
    Route::post('/exam/submit', [\App\Http\Controllers\ExamController::class, 'submit'])->name('exam.submit');
    Route::get('/exam/result/{id}', [\App\Http\Controllers\ExamController::class, 'showResult'])->name('exam.result');
    Route::get('/exam-history', [\App\Http\Controllers\ExamController::class, 'history'])->name('exam.history');
    Route::get('/exam-history/{id}', [\App\Http\Controllers\ExamController::class, 'historyDetail'])->name('exam.history.detail');
    
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
        Route::post('/{ortsverband}/lernpools/{lernpool}/unenroll', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'unenroll'])->name('lernpools.unenroll');

        // Unified Practice Routes für Lernpools
        Route::get('/{ortsverband}/lernpools/{lernpool}/practice', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'practice'])->name('lernpools.practice');
        Route::get('/{ortsverband}/lernpools/{lernpool}/practice/unsolved', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'practiceUnsolved'])->name('lernpools.practice.unsolved');
        Route::get('/{ortsverband}/lernpools/{lernpool}/practice/section/{nr}', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'practiceSection'])->name('lernpools.practice.section');
        Route::get('/{ortsverband}/lernpools/{lernpool}/practice/show', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'practiceShow'])->name('lernpools.practice.show');
        Route::post('/{ortsverband}/lernpools/{lernpool}/practice/submit', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'practiceSubmit'])->name('lernpools.practice.submit');
        Route::get('/{ortsverband}/lernpools/{lernpool}/practice/summary', [\App\Http\Controllers\OrtsverbandLernpoolPracticeController::class, 'practiceSummary'])->name('lernpools.practice.summary');

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
    Route::get('/shop-analytics', [\App\Http\Controllers\Admin\ShopAnalyticsController::class, 'index'])->name('shop-analytics');
    Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class);
    Route::post('questions/{question}/update-field', [\App\Http\Controllers\Admin\QuestionController::class, 'updateField'])->name('questions.update-field');
    Route::resource('lehrgaenge', \App\Http\Controllers\Admin\LehrgangController::class);
    Route::post('lehrgaenge/{lehrgang}/import-csv', [\App\Http\Controllers\Admin\LehrgangController::class, 'importCSV'])->name('lehrgaenge.import-csv');
    Route::patch('lehrgaenge/{lehrgang}/question/{question}', [\App\Http\Controllers\Admin\LehrgangController::class, 'updateQuestion'])->name('lehrgaenge.update-question');
    Route::delete('lehrgaenge/{question}/delete-question', [\App\Http\Controllers\Admin\LehrgangController::class, 'deleteQuestion'])->name('lehrgaenge.delete-question');
    Route::resource('lehrgang-issues', \App\Http\Controllers\Admin\LehrgangIssueController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::model('lehrgang_issue', \App\Models\LehrgangQuestionIssue::class);

    // Ligen-Übersicht (Admin)
    Route::get('leagues', [\App\Http\Controllers\Admin\LeagueController::class, 'index'])->name('leagues.index');
    Route::get('leagues/{league}', [\App\Http\Controllers\Admin\LeagueController::class, 'show'])->name('leagues.show');

    // Neue vereinheitlichte Issues-Verwaltung (Lehrgänge + Grundausbildung)
    Route::get('issues', [\App\Http\Controllers\Admin\IssueController::class, 'index'])->name('issues.index');
    Route::get('issues/{issue}', [\App\Http\Controllers\Admin\IssueController::class, 'show'])->name('issues.show');
    Route::put('issues/{issue}', [\App\Http\Controllers\Admin\IssueController::class, 'update'])->name('issues.update');
    Route::delete('issues/{issue}', [\App\Http\Controllers\Admin\IssueController::class, 'destroy'])->name('issues.destroy');

    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::get('users/{id}/progress', [\App\Http\Controllers\Admin\UserController::class, 'editProgress'])->name('users.progress.edit');
    Route::put('users/{id}/progress', [\App\Http\Controllers\Admin\UserController::class, 'updateProgress'])->name('users.progress.update');
    Route::post('users/{id}/progress/sr-pull-forward', [\App\Http\Controllers\Admin\UserController::class, 'pullForwardSpacedRepetition'])->name('users.progress.sr-pull-forward');
    Route::post('users/{id}/progress/sr-set-tomorrow', [\App\Http\Controllers\Admin\UserController::class, 'setSpacedRepetitionTomorrow'])->name('users.progress.sr-set-tomorrow');
    Route::post('users/{id}/progress/reset', [\App\Http\Controllers\Admin\UserController::class, 'resetProgress'])->name('users.progress.reset');
    Route::get('users/{id}/xp-history', [\App\Http\Controllers\Admin\UserController::class, 'xpHistory'])->name('users.xp-history');

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

    // Prüfungs-Feedback
    Route::get('exam-feedback', [\App\Http\Controllers\Admin\ExamFeedbackController::class, 'index'])->name('exam-feedback.index');
    Route::delete('exam-feedback/{examFeedback}', [\App\Http\Controllers\Admin\ExamFeedbackController::class, 'destroy'])->name('exam-feedback.destroy');

    // Nutzerumfragen
    Route::get('umfragen', [\App\Http\Controllers\Admin\SurveyController::class, 'index'])->name('umfragen.index');
    Route::get('umfragen/export', [\App\Http\Controllers\Admin\SurveyController::class, 'export'])->name('umfragen.export');
    Route::post('umfragen', [\App\Http\Controllers\Admin\SurveyController::class, 'store'])->name('umfragen.store');
    Route::patch('umfragen/{survey}/toggle', [\App\Http\Controllers\Admin\SurveyController::class, 'toggle'])->name('umfragen.toggle');

    // Push Notifications (Admin)
    Route::get('push', [\App\Http\Controllers\Admin\PushController::class, 'index'])->name('push.index');
    Route::post('push/send', [\App\Http\Controllers\Admin\PushController::class, 'send'])->name('push.send');

    // Log Viewer (Scheduler, Worker)
    Route::get('logs/{type}', [\App\Http\Controllers\Admin\SchedulerLogController::class, 'index'])->name('logs.index')->whereIn('type', ['scheduler', 'worker']);
    Route::delete('logs/{type}', [\App\Http\Controllers\Admin\SchedulerLogController::class, 'destroy'])->name('logs.destroy')->whereIn('type', ['scheduler', 'worker']);

    // Ortsverband Routes (Admin) - Nur View und Delete
    Route::get('ortsverband', [\App\Http\Controllers\Admin\OrtsverbandController::class, 'index'])->name('ortsverband.index');
    Route::post('ortsverband/{ortsverband}/view-as', [\App\Http\Controllers\Admin\OrtsverbandController::class, 'viewAs'])->name('ortsverband.view-as');
    Route::post('ortsverband/exit-view', [\App\Http\Controllers\Admin\OrtsverbandController::class, 'exitView'])->name('ortsverband.exit-view');
    Route::delete('ortsverband/{ortsverband}', [\App\Http\Controllers\Admin\OrtsverbandController::class, 'destroy'])->name('ortsverband.destroy');

    // Zeitsimulator (nur Non-Production - Routes werden in Production gar nicht registriert)
    if (!app()->environment('production')) {
        Route::get('/time-simulator', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'index'])->name('time-simulator');
        Route::post('/time-simulator/simulate-activity', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'simulateActivity'])->name('time-simulator.activity');
        Route::post('/time-simulator/daily-reset', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'runDailyReset'])->name('time-simulator.reset');
        Route::post('/time-simulator/streak-reminder', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'runStreakReminder'])->name('time-simulator.reminder');
        Route::post('/time-simulator/reset-user', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'resetUser'])->name('time-simulator.reset-user');

        // Liga-Simulator
        Route::post('/time-simulator/set-weekly-points', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'setWeeklyPoints'])->name('time-simulator.set-weekly-points');
        Route::post('/time-simulator/set-league', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'setLeague'])->name('time-simulator.set-league');
        Route::post('/time-simulator/run-league', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'runLeagueProcessing'])->name('time-simulator.run-league');
        Route::post('/time-simulator/reset-weekly-points', [\App\Http\Controllers\Admin\TimeSimulatorController::class, 'resetWeeklyPoints'])->name('time-simulator.reset-weekly-points');
    }
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

// Exam Feedback (öffentlich via Token, kein Login nötig)
Route::get('/exam-feedback/{token}', [\App\Http\Controllers\ExamFeedbackController::class, 'show'])->name('exam-feedback.show');
Route::post('/exam-feedback/{token}', [\App\Http\Controllers\ExamFeedbackController::class, 'store'])->name('exam-feedback.store');

require __DIR__.'/auth.php';
