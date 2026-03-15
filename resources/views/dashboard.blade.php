@extends('layouts.app')

@section('title', 'Dashboard - Dein Lernfortschritt')
@section('description', 'Dein persönliches THW-Trainer Dashboard: Verfolge deinen Lernfortschritt, wiederhole falsche Fragen und bereite dich optimal auf deine THW-Prüfung vor.')

@push('styles')
<style>
    /* ─── Layout ─────────────────────────────────────── */
    .ops-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* ─── Status-Strip ───────────────────────────────── */
    .status-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        padding: 0.875rem 1.25rem;
        border-left: 4px solid var(--thw-blue);
        border-radius: 0 0.5rem 0.5rem 0;
        flex-wrap: wrap;
    }

    .status-left { display: flex; flex-direction: column; gap: 0.35rem; min-width: 0; }

    .status-name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .status-level-badge {
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.15rem 0.5rem;
        border-radius: 0.25rem;
        background: var(--thw-blue);
        color: white;
        flex-shrink: 0;
    }

    .status-xp-bar {
        height: 3px;
        width: 200px;
        background: rgba(0, 51, 127, 0.15);
        border-radius: 2px;
        overflow: hidden;
    }

    html.light-mode .status-xp-bar {
        background: rgba(0, 51, 127, 0.12);
    }

    .status-xp-fill {
        height: 100%;
        background: var(--thw-blue);
        border-radius: 2px;
        transition: width 0.8s ease-out;
    }

    .status-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-shrink: 0;
    }

    .status-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.1rem;
    }

    .status-stat-value {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
    }

    .status-stat-value.gold { color: var(--gold-start); }

    .status-stat-label {
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .status-divider {
        width: 1px;
        height: 28px;
        background: var(--glass-border);
    }

    .status-freeze-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        font-size: 0.6rem;
        color: #93c5fd;
        font-weight: 600;
    }

    /* ─── Next-Step Hero Card ────────────────────────── */
    .hero-card {
        padding: 1.75rem 2rem;
        border-radius: 0.75rem 0.25rem 0.75rem 0.75rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .hero-card:hover { text-decoration: none; }

    .hero-content { flex: 1; min-width: 0; }

    .hero-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.7);
        margin-bottom: 0.35rem;
        font-weight: 600;
    }

    html.light-mode .hero-label { color: var(--thw-blue); }

    .hero-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .hero-desc {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }

    .hero-ring-wrap {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        position: relative;
    }

    .hero-ring-text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    /* ─── Stats Grid ─────────────────────────────────── */
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .stat-card {
        padding: 1.25rem 1.5rem;
        border-radius: 0.75rem 0.25rem 0.75rem 0.75rem;
        border-top: 3px solid var(--thw-blue);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .stat-card-title {
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        font-weight: 700;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .stat-card-sub {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .stat-bar {
        height: 4px;
        background: rgba(0, 51, 127, 0.12);
        border-radius: 2px;
        overflow: hidden;
        margin: 0.25rem 0;
    }

    html.light-mode .stat-bar {
        background: rgba(0, 51, 127, 0.1);
    }

    .stat-bar-fill {
        height: 100%;
        background: var(--thw-blue);
        border-radius: 2px;
        transition: width 0.6s ease-out;
    }

    .stat-bar-fill.green { background: #22c55e; }

    .stat-card-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .stat-card-detail {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .stat-card-detail.gold { color: var(--gold-start); font-weight: 700; }

    /* ─── Countdown-Strip ───────────────────────────── */
    .countdown-strip {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 0.875rem 1.25rem;
        border-left: 4px solid var(--thw-blue);
        border-radius: 0 0.5rem 0.5rem 0;
        flex-wrap: wrap;
    }

    .countdown-strip.no-date {
        border-left-style: dashed;
        opacity: 0.8;
    }

    .countdown-days {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        flex-shrink: 0;
    }

    .countdown-days-num {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .countdown-days-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .countdown-mid {
        flex: 1;
        min-width: 160px;
    }

    .countdown-target-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-bottom: 0.35rem;
    }

    .countdown-mini-bar {
        height: 4px;
        background: rgba(0, 51, 127, 0.12);
        border-radius: 2px;
        overflow: hidden;
    }

    html.light-mode .countdown-mini-bar { background: rgba(0, 51, 127, 0.1); }

    .countdown-mini-fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.5s ease-out;
    }

    .countdown-status {
        font-size: 0.7rem;
        margin-top: 0.25rem;
        font-weight: 600;
    }

    .countdown-status.done { color: #22c55e; }
    .countdown-status.pending { color: var(--text-muted); }

    /* ─── Activity Chart ─────────────────────────────── */
    .activity-section {
        padding: 1.25rem 1.5rem;
        border-radius: 0.75rem 0.25rem 0.75rem 0.75rem;
    }

    .activity-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .activity-title {
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        font-weight: 700;
    }

    .activity-trend {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
    }

    .activity-chart-wrap {
        display: flex;
        align-items: flex-end;
        gap: 0.4rem;
        height: 100px;
    }

    .activity-bar-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.3rem;
        height: 100%;
    }

    .activity-bar-space {
        flex: 1;
        width: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .activity-bar-new {
        width: 100%;
        max-width: 28px;
        min-height: 4px;
        border-radius: 3px 3px 2px 2px;
        background: var(--thw-blue);
        position: relative;
        transition: height 0.5s ease-out;
    }

    .activity-bar-new.today {
        background: var(--thw-blue-light);
        box-shadow: 0 0 10px rgba(0, 77, 179, 0.35);
    }

    .activity-bar-new.empty {
        background: rgba(0, 51, 127, 0.08);
        min-height: 4px;
    }

    html.light-mode .activity-bar-new.empty {
        background: rgba(0, 51, 127, 0.1);
    }

    .activity-bar-count-new {
        font-size: 0.55rem;
        font-weight: 700;
        color: var(--text-muted);
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
    }

    .activity-day-label {
        font-size: 0.6rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
    }

    .activity-day-label.today { color: var(--thw-blue-light); font-weight: 700; }

    /* ─── Heatmap + Spaced Rep ──────────────────────── */
    .heatmap-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1rem;
    }

    .heatmap-card {
        padding: 1.25rem 1.5rem;
        border-radius: 0.5rem 1.5rem 0.5rem 0.5rem;
    }

    .spaced-card {
        padding: 1.25rem 1.5rem;
        border-radius: 0.75rem 0.25rem 0.75rem 0.75rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        gap: 0.5rem;
    }

    .spaced-count {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .spaced-count.zero { color: #22c55e; }

    .heatmap-grid-new {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.4rem;
        margin-bottom: 0.75rem;
    }

    .heatmap-cell-new {
        aspect-ratio: 1;
        border-radius: 0.4rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform 0.15s, box-shadow 0.15s;
        cursor: pointer;
        text-decoration: none;
    }

    .heatmap-cell-new:hover { transform: scale(1.1); }
    .heatmap-cell-new.strong { background: rgba(34, 197, 94, 0.3); border: 1px solid rgba(34, 197, 94, 0.35); }
    .heatmap-cell-new.medium { background: rgba(245, 158, 11, 0.3); border: 1px solid rgba(245, 158, 11, 0.3); }
    .heatmap-cell-new.weak   { background: rgba(239, 68, 68, 0.25); border: 1px solid rgba(239, 68, 68, 0.25); }
    .heatmap-cell-new.none   { background: rgba(0, 51, 127, 0.06); border: 1px solid rgba(0, 51, 127, 0.1); }

    html.light-mode .heatmap-cell-new.none { background: rgba(0,51,127,0.06); border-color: rgba(0,51,127,0.15); }

    .heatmap-cell-num { font-size: 0.7rem; font-weight: 800; color: var(--text-primary); }
    .heatmap-cell-pct { font-size: 0.5rem; font-weight: 600; color: var(--text-secondary); }
    .heatmap-cell-new.weak .heatmap-cell-num { color: #fca5a5; }
    html.light-mode .heatmap-cell-new.weak .heatmap-cell-num { color: #dc2626; }

    .heatmap-legend-new {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .heatmap-legend-item-new {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.58rem;
        color: var(--text-muted);
    }

    .heatmap-legend-dot-new {
        width: 7px;
        height: 7px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    /* ─── Kurs-Grid ──────────────────────────────────── */
    .kurs-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .kurs-section-title {
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        font-weight: 700;
    }

    .kurs-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.875rem;
    }

    .kurs-card {
        padding: 1rem 1.25rem;
        border-top: 3px solid var(--thw-blue);
        border-radius: 0 0 0.5rem 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .kurs-card-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .kurs-card-desc {
        font-size: 0.75rem;
        color: var(--text-secondary);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }

    .kurs-bar { height: 3px; background: rgba(0,51,127,0.1); border-radius: 2px; overflow: hidden; margin: 0.1rem 0; }
    .kurs-bar-fill { height: 100%; background: var(--thw-blue); border-radius: 2px; }
    .kurs-bar-fill.done { background: #22c55e; }
    .kurs-percent { font-size: 0.7rem; font-weight: 700; color: var(--thw-blue-light); }

    /* ─── Ausbilder-Karte ────────────────────────────── */
    .ausbilder-card {
        padding: 1rem 1.5rem;
        border-radius: 0.5rem 0.25rem 0.5rem 0.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .ausbilder-stats {
        display: flex;
        gap: 2rem;
    }

    .ausbilder-stat-val {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
    }

    .ausbilder-stat-label {
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    /* ─── Live-Session Banner ────────────────────────── */
    .live-session-banner {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1rem 1.25rem;
        border-left: 4px solid #22c55e;
        border-radius: 0 0.5rem 0.5rem 0;
        flex-wrap: wrap;
    }

    .live-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #22c55e;
        flex-shrink: 0;
        animation: pulse-dot 2s ease-in-out infinite;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(0.8); }
    }

    .live-session-info { flex: 1; min-width: 0; }
    .live-session-title { font-size: 0.875rem; font-weight: 700; color: var(--text-primary); }
    .live-session-meta { font-size: 0.75rem; color: var(--text-secondary); line-height: 1.5; }

    /* ─── Leaderboard-Modal ──────────────────────────── */
    .leaderboard-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
        animation: fadeInModal 0.3s ease-out;
    }

    .leaderboard-modal {
        background: var(--gradient-gold-135);
        border-radius: 1.5rem 0.5rem 1.5rem 1.5rem;
        max-width: 480px;
        width: 100%;
        position: relative;
        box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 80px rgba(251,191,36,0.15);
        animation: slideUpModal 0.4s ease-out;
        overflow: hidden;
    }

    .leaderboard-modal-content { padding: 2rem; position: relative; }

    .leaderboard-modal-close {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .leaderboard-modal-close:hover { background: rgba(255,255,255,0.3); }

    @keyframes fadeInModal { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUpModal { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes fadeOutModal { from { opacity: 1; } to { opacity: 0; } }

    /* ─── Alert-Banner ─────────────────────────────────── */
    .alert-compact {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem 0.75rem 0.75rem 0;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .alert-compact-icon { font-size: 1.25rem; }
    .alert-compact-content { flex: 1; min-width: 0; }
    .alert-compact-title { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }
    .alert-compact-desc { font-size: 0.8rem; color: var(--text-muted); }

    /* ─── Responsive ─────────────────────────────────── */
    @media (max-width: 1023px) {
        .stats-grid { grid-template-columns: 1fr; }
        .kurs-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (min-width: 768px) and (max-width: 1023px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .heatmap-row { grid-template-columns: 2fr 1fr; }
    }

    @media (max-width: 767px) {
        .ops-container { padding: 1rem; }
        .status-strip { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
        .status-right { gap: 1rem; flex-wrap: wrap; }
        .status-xp-bar { width: 100%; }
        .hero-card { flex-direction: column; padding: 1.25rem; }
        .hero-ring-wrap { align-self: flex-end; }
        .heatmap-row { grid-template-columns: 1fr; }
        .kurs-grid { grid-template-columns: 1fr; }
        .countdown-strip { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
        .ausbilder-card { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $total = $totalQuestions ?? \App\Models\Question::count();
    if (empty($total)) { $total = \App\Models\Question::count(); }

    $allExams = \App\Models\ExamStatistic::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    $exams = 0;
    foreach ($allExams as $exam) {
        if ($exam->is_passed) { $exams++; } else { break; }
    }

    try {
        $threshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD ?? 2;
        $progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();
        $totalProgressPoints = 0;
        if ($progressData && $progressData->count() > 0) {
            foreach ($progressData as $prog) { $totalProgressPoints += min($prog->consecutive_correct ?? 0, $threshold); }
        }
        $maxProgressPoints = $total * $threshold;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
    } catch (\Exception $e) { $progressPercent = 0; $totalProgressPoints = 0; $progressData = collect(); $threshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD ?? 2; }

    $progress = \App\Models\UserQuestionProgress::countMastered($user->id);

    $enrolledLehrgaenge = $user->enrolledLehrgaenge()->get();

    $gamificationService = new \App\Services\GamificationService();
    $userAchievements = $gamificationService->getUserAchievements($user);
    $totalAchievements = count(\App\Services\GamificationService::ACHIEVEMENTS);
    $unlockedCount = count(array_filter($userAchievements, fn($a) => $a['unlocked']));

    $failedArr = is_array($user->exam_failed_questions ?? null)
        ? $user->exam_failed_questions
        : (is_string($user->exam_failed_questions) ? json_decode($user->exam_failed_questions, true) ?? [] : []);
    $hasFailedQuestions = $failedArr && count($failedArr) > 0;
    $canStartExam = $progress >= $total && !$hasFailedQuestions;

    $circumference = 2 * 3.14159 * 26;
    $theoryOffset  = $circumference - ($progressPercent / 100) * $circumference;
    $examOffset    = $circumference - (min(100, $exams * 20) / 100) * $circumference;

    $userLeague = $user->league ?? 'bronze';
    $leagueInfo = \App\Services\LeagueService::getLeagueInfo($userLeague);

    $streakMinQuestions    = \App\Services\GamificationService::STREAK_MIN_QUESTIONS;
    $todayQuestions        = $user->daily_questions_solved ?? 0;
    $questionsRemaining    = max(0, $streakMinQuestions - $todayQuestions);
    $streakAtRisk = ($user->streak_days ?? 0) > 0
        && $questionsRemaining > 0
        && (!$user->last_activity_date || \Carbon\Carbon::parse($user->last_activity_date)->lt(\Carbon\Carbon::today()));

    $activeLernsession = app(\App\Services\LernsessionService::class)
        ->getActiveSessionsForUser($user)
        ->first();

    $daysLeft = ($user->exam_date && $user->exam_date->isFuture())
        ? (int) now()->startOfDay()->diffInDays($user->exam_date, false)
        : null;
    $unmasteredCount = $total - $progress;
    $dailyTarget     = null;
    $todayAnswered   = \App\Models\QuestionStatistic::where('user_id', $user->id)
        ->whereDate('created_at', today())
        ->count();

    if ($daysLeft && $daysLeft > 0 && $unmasteredCount > 0) {
        $examBuffer   = min(7, max(0, $daysLeft - 1));
        $effectiveDays = max(1, $daysLeft - $examBuffer);
        $statCount    = \App\Models\QuestionStatistic::where('user_id', $user->id)->count();
        $statCorrect  = \App\Models\QuestionStatistic::where('user_id', $user->id)->where('is_correct', true)->count();
        $accuracy     = ($statCount >= 20) ? ($statCorrect / $statCount) : 0.65;
        $errorFactor  = 1 / max(0.4, $accuracy);
        $remainingInteractions = 0;
        $seenQuestionIds = $progressData->pluck('question_id');
        foreach ($progressData as $prog) {
            if ($prog->consecutive_correct < $threshold) {
                $remainingInteractions += ($threshold - $prog->consecutive_correct) * $errorFactor;
            }
        }
        $unseenCount = $total - $seenQuestionIds->count();
        $remainingInteractions += $unseenCount * $threshold * $errorFactor;
        $dailyTarget = max(1, (int) ceil($remainingInteractions / $effectiveDays));
    }

    $userOV      = $user->ortsverbände->first();
    $isAusbilder = false;
    $ovStats     = null;
    if ($userOV) {
        $memberPivot = $userOV->members()->where('user_id', auth()->id())->first();
        $isAusbilder = $memberPivot && $memberPivot->pivot->role === 'ausbildungsbeauftragter';
        if ($isAusbilder) {
            $regularMembers = $userOV->members()->wherePivot('role', 'member')->get();
            $memberProgress = $userOV->getMemberProgress()->filter(fn($m) => $m['role'] === 'member');
            $ovStats = [
                'members'      => $regularMembers->count(),
                'avg_progress' => round($memberProgress->avg('theory_progress_percent') ?? 0),
            ];
        }
    }

    $enrolledLernpools = $user->enrolledLernpools()->where('is_active', true)->get();

    $weeklyChartData = $weeklyActivity ?? collect();
    $thisWeekTotal   = isset($weeklyActivity) ? $weeklyActivity->sum('count') : 0;
    $thisWeekCorrect = isset($weeklyActivity) ? $weeklyActivity->sum('correct') : 0;
    $thisWeekRate    = $thisWeekTotal > 0 ? round(($thisWeekCorrect / $thisWeekTotal) * 100) : 0;
    $lastWeekData    = \DB::table('question_statistics')
        ->where('user_id', $user->id)
        ->where('created_at', '>=', now()->subDays(14))
        ->where('created_at', '<', now()->subDays(7))
        ->selectRaw('COUNT(*) as total, SUM(is_correct) as correct')
        ->first();
    $lastWeekTotal   = $lastWeekData ? $lastWeekData->total : 0;
    $lastWeekRate    = $lastWeekTotal > 0 ? round(($lastWeekData->correct / $lastWeekTotal) * 100) : 0;
    $rateDiff        = $thisWeekRate - $lastWeekRate;
@endphp

<!-- SVG Gradients -->
<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="blueGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#004db3"/>
            <stop offset="100%" style="stop-color:#00337F"/>
        </linearGradient>
        <linearGradient id="goldGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#fbbf24"/>
            <stop offset="100%" style="stop-color:#f59e0b"/>
        </linearGradient>
    </defs>
</svg>

<div class="ops-container">

    {{-- 1. STATUS-STRIP --}}
    <div class="status-strip glass">
        <div class="status-left">
            <div class="status-name">
                {{ $user->name }}
                <span class="status-level-badge">Level {{ $user->level ?? 1 }}</span>
            </div>
            <div class="status-xp-bar">
                <div class="status-xp-fill" style="width: {{ $progressPercent }}%;"></div>
            </div>
        </div>
        <div class="status-right">
            <div class="status-stat">
                <div class="status-stat-value gold">
                    {{ $user->streak_days ?? 0 }}
                    @if(isset($streakFreezeStatus) && $streakFreezeStatus['remaining'] > 0)
                        <span class="status-freeze-badge"><i class="bi bi-snow"></i>{{ $streakFreezeStatus['remaining'] }}</span>
                    @endif
                </div>
                <div class="status-stat-label">Streak</div>
            </div>
            <div class="status-divider"></div>
            <div class="status-stat">
                <div class="status-stat-value">{{ number_format($user->points ?? 0) }}</div>
                <div class="status-stat-label">Punkte</div>
            </div>
            <div class="status-divider"></div>
            <a href="{{ route('gamification.leaderboard', ['tab' => 'liga']) }}" style="text-decoration: none;">
                <div class="status-stat">
                    <div class="status-stat-value" style="color: {{ $leagueInfo['color'] }};">{{ $leagueInfo['name'] }}</div>
                    <div class="status-stat-label">Liga</div>
                </div>
            </a>
        </div>
    </div>

    {{-- 2. ALERT-BANNER (konditionell) --}}
    @if(session('error'))
    <div class="alert-compact glass-error">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('error') }}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:1.1rem;">×</button>
    </div>
    @endif

    @if($hasFailedQuestions)
    <div class="alert-compact glass-warning">
        <i class="bi bi-arrow-repeat alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ count($failedArr) }} Frage{{ count($failedArr) == 1 ? '' : 'n' }} wiederholen</div>
            <div class="alert-compact-desc">Bevor du eine neue Prüfung starten kannst</div>
        </div>
        <a href="{{ route('failed.index') }}" class="btn-primary btn-sm">Fehler wiederholen</a>
    </div>
    @endif

    @if(isset($spacedRepetitionDue) && $spacedRepetitionDue > 0)
    <div class="alert-compact glass" style="border-left: 3px solid var(--thw-blue-light);">
        <i class="bi bi-arrow-repeat alert-compact-icon" style="color: var(--thw-blue-light);"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ $spacedRepetitionDue }} Wiederholung{{ $spacedRepetitionDue == 1 ? '' : 'en' }} fällig</div>
            <div class="alert-compact-desc">Spaced Repetition: Wiederhole Fragen für langfristiges Behalten</div>
        </div>
        <a href="{{ route('practice.spaced-repetition') }}" class="btn-secondary btn-sm">Wiederholen</a>
    </div>
    @endif

    @if($activeLernsession)
        @php $activeLernsessionData = $activeLernsession->learningSession; @endphp
        <div class="live-session-banner glass"
             x-data="{ remaining: {{ $activeLernsession->getTimeRemainingSeconds() }} }"
             x-init="setInterval(() => { if(remaining > 0) remaining-- }, 1000)">
            <div class="live-dot"></div>
            <div class="live-session-info">
                <div class="live-session-title">Lernsession ist live</div>
                <div class="live-session-meta">
                    {{ $activeLernsessionData->title }} &middot;
                    {{ $activeLernsession->starts_at->format('d.m.Y H:i') }} – {{ $activeLernsession->ends_at->format('H:i') }} Uhr
                    &middot; noch <span x-text="Math.floor(remaining/3600) > 0
                        ? Math.floor(remaining/3600) + 'h ' + Math.floor((remaining%3600)/60) + 'min'
                        : Math.floor(remaining/60) + ' min'"></span>
                    @if($activeLernsessionData->description)
                        <br>{{ $activeLernsessionData->description }}
                    @endif
                </div>
            </div>
            <a href="{{ route('lernsession.live', $activeLernsession) }}" class="btn-primary btn-sm">Teilnehmen</a>
        </div>
    @endif

    @if($streakAtRisk)
    <div x-data="{ frozen: false, loading: false, async applyFreeze() { this.loading = true; try { const res = await fetch('{{ route('streak.freeze') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }, cache: 'no-store' }); const data = await res.json(); if (data.success) this.frozen = true; } finally { this.loading = false; } } }"
         class="alert-compact"
         :class="frozen ? 'glass-success' : 'glass-warning'">
        <i class="bi alert-compact-icon" :class="frozen ? 'bi-shield-check' : 'bi-fire'" :style="frozen ? 'color:#10b981;' : 'color:#f59e0b;'"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title" x-show="!frozen">Dein {{ $user->streak_days }}-Tage-Streak läuft ab</div>
            <div class="alert-compact-title" x-show="frozen">Streak gesichert</div>
            <div class="alert-compact-desc" x-show="!frozen">Noch {{ $questionsRemaining }} von {{ $streakMinQuestions }} Fragen nötig ({{ $todayQuestions }}/{{ $streakMinQuestions }} beantwortet)</div>
            <div class="alert-compact-desc" x-show="frozen">Ein Streak Freeze schützt deinen Streak für heute.</div>
            <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;" x-show="!frozen">
                @if(isset($streakFreezeStatus) && $streakFreezeStatus['remaining'] > 0)
                <button class="btn-secondary btn-sm" @click="applyFreeze()" :disabled="loading" x-text="loading ? 'Wird eingesetzt...' : 'Freeze einsetzen ({{ $streakFreezeStatus['remaining'] }})'"></button>
                @endif
                <a href="{{ route('practice.all') }}" class="btn-primary btn-sm">Jetzt lernen</a>
            </div>
        </div>
    </div>
    @endif

    {{-- 3. NEXT-STEP HERO CARD --}}
    @if($hasFailedQuestions)
    {{-- Zustand A: Fehler ausstehend --}}
    <a href="{{ route('failed.index') }}" class="hero-card glass-blue" style="text-decoration:none;">
        <div class="hero-content">
            <div class="hero-label">Nächster Schritt</div>
            <div class="hero-title">{{ count($failedArr) }} Fehler-Fragen ausstehend</div>
            <div class="hero-desc">Wiederhole deine falsch beantworteten Fragen, bevor du eine neue Prüfung starten kannst.</div>
            <span class="btn-primary">Fehler wiederholen</span>
        </div>
        <div class="hero-ring-wrap">
            <svg width="80" height="80" viewBox="0 0 64 64">
                <circle fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6" cx="32" cy="32" r="26"/>
                <circle fill="none" stroke="url(#blueGradient)" stroke-width="6" stroke-linecap="round"
                    cx="32" cy="32" r="26"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $theoryOffset }}"
                    transform="rotate(-90 32 32)"/>
            </svg>
            <div class="hero-ring-text">{{ $progressPercent }}%</div>
        </div>
    </a>

    @elseif($progressPercent < 100)
    {{-- Zustand B: Theorie lernen --}}
    <a href="{{ route('practice.all') }}" class="hero-card glass-blue" style="text-decoration:none;">
        <div class="hero-content">
            <div class="hero-label">Nächster Schritt</div>
            <div class="hero-title">Noch {{ $total - $progress }} Fragen zu meistern</div>
            <div class="hero-desc">Jede Frage muss {{ $threshold }}x richtig beantwortet werden. Du bist bei {{ $progressPercent }}%.</div>
            <span class="btn-primary">Weiter lernen</span>
        </div>
        <div class="hero-ring-wrap">
            <svg width="80" height="80" viewBox="0 0 64 64">
                <circle fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6" cx="32" cy="32" r="26"/>
                <circle fill="none" stroke="url(#blueGradient)" stroke-width="6" stroke-linecap="round"
                    cx="32" cy="32" r="26"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $theoryOffset }}"
                    transform="rotate(-90 32 32)"/>
            </svg>
            <div class="hero-ring-text">{{ $progressPercent }}%</div>
        </div>
    </a>

    @elseif($canStartExam && $exams < 5)
    {{-- Zustand C: Prüfung starten --}}
    <a href="{{ route('exam.index') }}" class="hero-card glass-blue" style="text-decoration:none;">
        <div class="hero-content">
            <div class="hero-label">Nächster Schritt</div>
            <div class="hero-title">Bereit für Prüfung!</div>
            <div class="hero-desc">{{ $exams }}/5 Prüfungen bestanden. Du kannst jetzt die nächste Prüfungssimulation starten.</div>
            <span class="btn-primary">Prüfung starten</span>
        </div>
        <div class="hero-ring-wrap">
            <svg width="80" height="80" viewBox="0 0 64 64">
                <circle fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6" cx="32" cy="32" r="26"/>
                <circle fill="none" stroke="url(#goldGradient)" stroke-width="6" stroke-linecap="round"
                    cx="32" cy="32" r="26"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $examOffset }}"
                    transform="rotate(-90 32 32)"/>
            </svg>
            <div class="hero-ring-text">{{ $exams }}/5</div>
        </div>
    </a>

    @else
    {{-- Zustand D: Abgeschlossen --}}
    <a href="{{ route('exam.index') }}" class="hero-card glass-gold" style="text-decoration:none;">
        <div class="hero-content">
            <div class="hero-label">Abgeschlossen</div>
            <div class="hero-title">5/5 Prüfungen bestanden!</div>
            <div class="hero-desc">Du hast alle Prüfungssimulationen erfolgreich abgeschlossen. Herzlichen Glückwunsch!</div>
            <span class="btn-primary">Prüfung wiederholen</span>
        </div>
        <div class="hero-ring-wrap">
            <svg width="80" height="80" viewBox="0 0 64 64">
                <circle fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="6" cx="32" cy="32" r="26"/>
                <circle fill="none" stroke="url(#goldGradient)" stroke-width="6" stroke-linecap="round"
                    cx="32" cy="32" r="26"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="0"
                    transform="rotate(-90 32 32)"/>
            </svg>
            <div class="hero-ring-text">5/5</div>
        </div>
    </a>
    @endif

    {{-- 4. STATS GRID --}}
    @php
        $lastExam = isset($recentExams) ? $recentExams->first() : null;
        $lastExamPct = $lastExam ? round(($lastExam->correct_answers / 40) * 100) : null;
        $avgExamPct  = isset($recentExams) && $recentExams->count() > 0
            ? round($recentExams->avg(fn($e) => round(($e->correct_answers / 40) * 100)))
            : null;
    @endphp
    <div class="stats-grid">

        {{-- Karte links: Theorie-Fortschritt --}}
        <div class="stat-card glass-tl">
            <div class="stat-card-title">Theorie-Fortschritt</div>
            <div class="stat-card-value">{{ $progressPercent }}<span style="font-size:1rem;font-weight:600;color:var(--text-secondary);">%</span></div>
            <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $progressPercent }}%;"></div></div>
            <div class="stat-card-sub">{{ $progress }} von {{ $total }} gemeistert</div>
            <div class="stat-card-row">
                <span class="stat-card-detail">Heute: {{ $todayAnswered }} Fragen</span>
                @if(isset($spacedRepetitionDue) && $spacedRepetitionDue > 0)
                    <span class="stat-card-detail" style="color:var(--thw-blue-light);">Spaced Rep: {{ $spacedRepetitionDue }} fällig</span>
                @endif
            </div>
            <div style="margin-top:0.25rem;">
                <a href="{{ route('practice.menu') }}" class="btn-secondary btn-sm">Abschnitte üben</a>
            </div>
        </div>

        {{-- Karte rechts: Prüfungs-Status --}}
        <div class="stat-card glass-br">
            <div class="stat-card-title">Prüfungs-Status</div>
            <div class="stat-card-value">{{ $exams }}<span style="font-size:1rem;font-weight:600;color:var(--text-secondary);">/5</span></div>
            <div class="stat-card-sub">bestanden</div>
            <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ min(100, $exams * 20) }}%;"></div></div>
            <div class="stat-card-row">
                @if($lastExamPct !== null)
                    <span class="stat-card-detail">Letzter Versuch:
                        <strong class="{{ $lastExamPct >= 75 ? 'gold ' : '' }}stat-card-detail">{{ $lastExamPct }}%</strong>
                    </span>
                @endif
                @if($avgExamPct !== null)
                    <span class="stat-card-detail">Schnitt:
                        <strong class="{{ $avgExamPct >= 75 ? 'gold ' : '' }}stat-card-detail">{{ $avgExamPct }}%</strong>
                    </span>
                @endif
            </div>
            <div style="margin-top:0.25rem;">
                @if($canStartExam)
                    <a href="{{ route('exam.index') }}" class="btn-secondary btn-sm">Prüfung starten</a>
                @elseif($hasFailedQuestions)
                    <a href="{{ route('failed.index') }}" class="btn-ghost btn-sm">Fehler wiederholen</a>
                @else
                    <span class="btn-ghost btn-sm" style="opacity:0.45;cursor:not-allowed;">Erst Theorie</span>
                @endif
            </div>
        </div>
    </div>

    {{-- 5. COUNTDOWN-STRIP (immer sichtbar) --}}
    @if($daysLeft !== null && $daysLeft > 0)
    @php
        $todayPct  = $dailyTarget ? min(100, round(($todayAnswered / $dailyTarget) * 100)) : 0;
        $todayDone = $dailyTarget && $todayAnswered >= $dailyTarget;
    @endphp
    <div class="countdown-strip glass">
        <div class="countdown-days">
            <div class="countdown-days-num">{{ $daysLeft }}</div>
            <div class="countdown-days-label">Tag{{ $daysLeft != 1 ? 'e' : '' }} bis zur Prüfung</div>
        </div>
        @if($dailyTarget && $unmasteredCount > 0)
        <div class="countdown-mid">
            <div class="countdown-target-label">Tagesziel: {{ $dailyTarget }} Fragen</div>
            <div class="countdown-mini-bar">
                <div class="countdown-mini-fill" style="width:{{ $todayPct }}%; background: {{ $todayDone ? '#22c55e' : 'var(--thw-blue)' }};"></div>
            </div>
            <div class="countdown-status {{ $todayDone ? 'done' : 'pending' }}">
                @if($todayDone)
                    {{ $todayAnswered }}/{{ $dailyTarget }} heute – geschafft!
                @else
                    noch {{ $dailyTarget - $todayAnswered }} übrig
                @endif
            </div>
        </div>
        @elseif($unmasteredCount <= 0)
        <div class="countdown-mid">
            <div class="countdown-status done">Alle Fragen gemeistert – bereit!</div>
        </div>
        @endif
    </div>

    @else
    <div class="countdown-strip no-date glass">
        <div style="flex:1; font-size:0.875rem; color:var(--text-secondary);">
            Prüfungsdatum eintragen für personalisierte Lernempfehlung
        </div>
        <a href="{{ route('profile') }}#exam_date" class="btn-ghost btn-sm">Datum eintragen</a>
    </div>
    @endif

    {{-- 6. AKTIVITÄTS-CHART --}}
    <div class="activity-section glass">
        <div class="activity-header">
            <span class="activity-title">Letzte 7 Tage</span>
            <div class="activity-trend">
                @if($thisWeekTotal > 0)
                    <span style="font-size:0.875rem;font-weight:700;color:{{ $thisWeekRate >= 70 ? '#22c55e' : ($thisWeekRate >= 50 ? '#f59e0b' : '#ef4444') }};">{{ $thisWeekRate }}%</span>
                    <span style="font-size:0.75rem;color:var(--text-muted);">Trefferquote</span>
                    @if($lastWeekTotal > 0)
                        <span style="font-size:0.7rem;font-weight:600;color:{{ $rateDiff >= 0 ? '#22c55e' : '#ef4444' }};">
                            <i class="bi bi-{{ $rateDiff >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>{{ abs($rateDiff) }}%
                        </span>
                    @endif
                    <span style="font-size:0.7rem;color:var(--text-muted);">{{ $thisWeekTotal }} Fragen</span>
                @else
                    <span style="font-size:0.75rem;color:var(--text-muted);">Noch keine Aktivität letzte 7 Tage</span>
                @endif
            </div>
        </div>

        @php
            $maxCount = max((int) $weeklyActivity->max('count'), 1);
            $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
            $chartStart = \Carbon\Carbon::now()->subDays(6)->startOfDay();
        @endphp

        <div class="activity-chart-wrap">
            @for($i = 0; $i < 7; $i++)
                @php
                    $dayDate = $chartStart->copy()->addDays($i);
                    $dateStr = $dayDate->toDateString();
                    $dayData = isset($weeklyActivity) ? $weeklyActivity->firstWhere('date', $dateStr) : null;
                    $count   = $dayData ? $dayData->count : 0;
                    $barHeight = $count > 0 ? max(8, ($count / $maxCount) * 100) : 0;
                    $isToday = $dayDate->isToday();
                @endphp
                <div class="activity-bar-col">
                    <div class="activity-bar-space">
                        @if($count > 0)
                            <div class="activity-bar-new {{ $isToday ? 'today' : '' }}"
                                 style="height:{{ $barHeight }}%;"
                                 title="{{ $count }} Fragen">
                                <span class="activity-bar-count-new">{{ $count }}</span>
                            </div>
                        @else
                            <div class="activity-bar-new empty" style="height:4px;"></div>
                        @endif
                    </div>
                    <span class="activity-day-label {{ $isToday ? 'today' : '' }}">{{ $days[$i] }}</span>
                </div>
            @endfor
        </div>
    </div>

    {{-- 7. HEATMAP + SPACED REP --}}
    <div class="heatmap-row">

        {{-- Heatmap 2/3 --}}
        <div class="heatmap-card glass-br">
            <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:700;margin-bottom:0.75rem;">Stärken & Schwächen</div>
            <div class="heatmap-grid-new">
                @for($s = 1; $s <= 10; $s++)
                    @php
                        $sData    = isset($sectionStats) ? $sectionStats->firstWhere('lernabschnitt', $s) : null;
                        $sTotal   = $sData ? $sData->total : 0;
                        $sCorrect = $sData ? $sData->correct : 0;
                        $sPct     = $sTotal > 0 ? round(($sCorrect / $sTotal) * 100) : -1;
                        $sClass   = $sPct < 0 ? 'none' : ($sPct >= 75 ? 'strong' : ($sPct >= 50 ? 'medium' : 'weak'));
                    @endphp
                    <a href="{{ route('practice.section', $s) }}"
                       class="heatmap-cell-new {{ $sClass }}"
                       title="Abschnitt {{ $s }}: {{ $sPct >= 0 ? $sPct.'% richtig ('.$sTotal.' Fragen)' : 'Noch nicht geübt' }}"
                       style="text-decoration:none;">
                        <span class="heatmap-cell-num">{{ $s }}</span>
                        @if($sPct >= 0)
                            <span class="heatmap-cell-pct">{{ $sPct }}%</span>
                        @endif
                    </a>
                @endfor
            </div>
            <div class="heatmap-legend-new">
                <div class="heatmap-legend-item-new"><span class="heatmap-legend-dot-new" style="background:rgba(239,68,68,0.5);"></span>&lt; 50%</div>
                <div class="heatmap-legend-item-new"><span class="heatmap-legend-dot-new" style="background:rgba(245,158,11,0.5);"></span>50–75%</div>
                <div class="heatmap-legend-item-new"><span class="heatmap-legend-dot-new" style="background:rgba(34,197,94,0.5);"></span>&gt; 75%</div>
                <div class="heatmap-legend-item-new"><span class="heatmap-legend-dot-new" style="background:rgba(0,51,127,0.2);"></span>Noch nicht</div>
            </div>
        </div>

        {{-- Spaced Rep 1/3 --}}
        <div class="spaced-card glass">
            <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:700;">Wiederholungen</div>
            @if(isset($spacedRepetitionDue) && $spacedRepetitionDue > 0)
                <div class="spaced-count">{{ $spacedRepetitionDue }}</div>
                <div style="font-size:0.8rem;color:var(--text-secondary);text-align:center;line-height:1.4;">Fragen fällig</div>
                <div style="font-size:0.7rem;color:var(--text-muted);text-align:center;line-height:1.4;">Spaced Repetition für langfristiges Behalten</div>
                <a href="{{ route('practice.spaced-repetition') }}" class="btn-secondary btn-sm" style="margin-top:0.5rem;">Wiederholen</a>
            @else
                <div class="spaced-count zero"><i class="bi bi-check-lg" style="font-size:2rem;"></i></div>
                <div style="font-size:0.875rem;font-weight:600;color:#22c55e;">Alles aktuell</div>
                <div style="font-size:0.75rem;color:var(--text-muted);text-align:center;">Keine Wiederholungen fällig</div>
            @endif
        </div>
    </div>

    {{-- 8. AUSBILDER-KARTE (konditionell) --}}
    @if($isAusbilder && $userOV)
    <div class="ausbilder-card glass-blue">
        <div>
            <span class="badge-thw" style="display:inline-block;margin-bottom:0.4rem;">Ausbilder</span>
            <div style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">{{ $userOV->name }}</div>
        </div>
        <div class="ausbilder-stats">
            <div>
                <div class="ausbilder-stat-val">{{ $ovStats['members'] }}</div>
                <div class="ausbilder-stat-label">Mitglieder</div>
            </div>
            <div>
                <div class="ausbilder-stat-val" style="color:{{ $ovStats['avg_progress'] >= 50 ? '#22c55e' : '#f59e0b' }};">{{ $ovStats['avg_progress'] }}%</div>
                <div class="ausbilder-stat-label">Ø Fortschritt</div>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('ortsverband.index') }}" class="btn-secondary btn-sm">Verwalten</a>
        </div>
    </div>
    @endif

    {{-- 9. LEHRGÄNGE & LERNPOOLS --}}
    <div>
        <div class="kurs-section-header">
            <span class="kurs-section-title">Lehrgänge & Lernpools</span>
            <a href="{{ route('lehrgaenge.index') }}" style="font-size:0.75rem;color:var(--thw-blue-light);text-decoration:none;font-weight:600;">Alle anzeigen</a>
        </div>

        @if($enrolledLehrgaenge->isNotEmpty() || $enrolledLernpools->isNotEmpty())
        <div class="kurs-grid">
            {{-- Lehrgänge (max 2) --}}
            @foreach($enrolledLehrgaenge->take(2) as $lehrgang)
                @php
                    $lProgress = \App\Models\UserLehrgangProgress::where('user_id', Auth::id())
                        ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                        ->get();
                    $lTotal = \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
                    $lPoints = 0;
                    foreach ($lProgress as $lp) { $lPoints += min($lp->consecutive_correct, \App\Models\UserQuestionProgress::MASTERY_THRESHOLD); }
                    $lMax = $lTotal * \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
                    $lPct = $lMax > 0 ? round(($lPoints / $lMax) * 100) : 0;
                    $lDone = $lPct == 100 && $lProgress->count() > 0;
                @endphp
                <div class="kurs-card glass">
                    <div class="kurs-card-title">{{ $lehrgang->lehrgang }}</div>
                    <div class="kurs-card-desc">{{ $lehrgang->beschreibung }}</div>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <div class="kurs-bar" style="flex:1;"><div class="kurs-bar-fill {{ $lDone ? 'done' : '' }}" style="width:{{ $lPct }}%;"></div></div>
                        <span class="kurs-percent">{{ $lPct }}%</span>
                    </div>
                    @if($lDone)
                        <span class="btn-ghost btn-sm" style="background:rgba(34,197,94,0.15);color:#22c55e;border-color:rgba(34,197,94,0.25);">Fertig</span>
                    @else
                        <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="btn-primary btn-sm">Weiter</a>
                    @endif
                </div>
            @endforeach

            {{-- Lernpools (max 1) --}}
            @foreach($enrolledLernpools->take(1) as $lernpool)
                @php
                    $lpSolved = $user->lernpoolProgress()
                        ->whereHas('question', fn($q) => $q->where('lernpool_id', $lernpool->id))
                        ->where('solved', true)->count();
                    $lpTotal = $lernpool->getQuestionCount();
                    $lpPct   = $lpTotal > 0 ? round(($lpSolved / $lpTotal) * 100) : 0;
                    $lpDone  = $lpPct == 100 && $lpSolved > 0;
                @endphp
                <div class="kurs-card glass">
                    <div style="display:flex;align-items:center;gap:0.4rem;margin-bottom:0.1rem;">
                        <div class="kurs-card-title" style="margin:0;">{{ $lernpool->name }}</div>
                        <span class="badge-thw" style="font-size:0.55rem;padding:0.1rem 0.35rem;">Lernpool</span>
                    </div>
                    <div class="kurs-card-desc">{{ $lernpool->description }}</div>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <div class="kurs-bar" style="flex:1;"><div class="kurs-bar-fill {{ $lpDone ? 'done' : '' }}" style="width:{{ $lpPct }}%;"></div></div>
                        <span class="kurs-percent">{{ $lpPct }}%</span>
                    </div>
                    @if($lpDone)
                        <span class="btn-ghost btn-sm" style="background:rgba(34,197,94,0.15);color:#22c55e;border-color:rgba(34,197,94,0.25);">Gemeistert</span>
                    @else
                        <a href="{{ route('ortsverband.lernpools.practice', [$lernpool->ortsverband_id, $lernpool->id]) }}" class="btn-primary btn-sm">Weiter</a>
                    @endif
                </div>
            @endforeach
        </div>

        @else
        {{-- Leer-Zustand --}}
        <div class="glass" style="text-align:center;padding:2rem;">
            <div style="font-size:0.875rem;color:var(--text-muted);margin-bottom:0.75rem;">Keine Lehrgänge oder Lernpools eingeschrieben</div>
            <a href="{{ route('lehrgaenge.index') }}" class="btn-secondary btn-sm">Lehrgänge entdecken</a>
        </div>
        @endif
    </div>

</div>{{-- .ops-container --}}

{{-- 10. LEADERBOARD-MODAL --}}
@if(!$user->leaderboard_banner_dismissed && !$user->leaderboard_consent)
<div class="leaderboard-modal-overlay" id="leaderboard-modal">
    <div class="leaderboard-modal">
        <div class="leaderboard-modal-content">
            <button class="leaderboard-modal-close" onclick="dismissLeaderboardModal(false)">×</button>
            <div style="text-align:center;margin-bottom:1.5rem;">
                <div style="display:inline-block;background:rgba(255,255,255,0.2);border-radius:1rem;padding:1rem;margin-bottom:1rem;">
                    <i class="bi bi-bar-chart" style="font-size:2.5rem;color:white;"></i>
                </div>
                <h2 style="font-size:1.5rem;font-weight:800;color:white;margin-bottom:0.5rem;">Leaderboard</h2>
                <p style="color:white;font-size:0.95rem;opacity:0.9;">Vergleiche dich mit anderen</p>
            </div>
            <div style="background:rgba(255,255,255,0.15);border-radius:0.75rem;padding:1rem;margin-bottom:1.5rem;">
                <p style="color:white;font-size:0.85rem;margin-bottom:0.5rem;"><strong>Name & Punkte</strong> werden angezeigt</p>
                <p style="color:white;font-size:0.85rem;margin:0;"><strong>Jederzeit änderbar</strong> in den Einstellungen</p>
            </div>
            <div style="display:flex;gap:0.75rem;">
                <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" id="lb-decline-form" style="flex:1;">
                    @csrf
                    <input type="hidden" name="accept" value="0">
                    <button type="submit" style="width:100%;background:rgba(255,255,255,0.2);color:white;font-weight:600;padding:0.75rem;border-radius:0.5rem;border:none;cursor:pointer;">Nein</button>
                </form>
                <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" style="flex:1;">
                    @csrf
                    <input type="hidden" name="accept" value="1">
                    <button type="submit" style="width:100%;background:white;color:#d97706;font-weight:700;padding:0.75rem;border-radius:0.5rem;border:none;cursor:pointer;">Ja, mitmachen</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<x-onboarding-tour />

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.0/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($progressPercent == 100)
    setTimeout(() => confetti({ particleCount: 80, spread: 60, origin: { y: 0.6 } }), 1000);
    @endif
});
function dismissLeaderboardModal(accept) {
    const modal = document.getElementById('leaderboard-modal');
    if (modal) { modal.style.animation = 'fadeOutModal 0.3s ease-out forwards'; setTimeout(() => modal.remove(), 300); }
    if (accept === false) { document.getElementById('lb-decline-form').submit(); }
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') dismissLeaderboardModal(false); });
function dismissEmailConsentBanner() {
    const banner = document.getElementById('email-consent-banner');
    if (banner) { banner.style.opacity = '0'; setTimeout(() => banner.remove(), 300); }
    fetch('/dashboard/dismiss-email-consent-banner', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } });
}
</script>
@endsection
