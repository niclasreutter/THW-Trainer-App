@extends('layouts.app')

@section('title', 'Dashboard - Dein Lernfortschritt')
@section('description', 'Dein persönliches THW-Trainer Dashboard: Verfolge deinen Lernfortschritt, wiederhole falsche Fragen und bereite dich optimal auf deine THW-Prüfung vor.')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Asymmetric Header - Left aligned, not centered */
    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    /* Bento Grid Layout - Intentionally uneven */
    .bento-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: auto;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    /* Main feature spans 2 columns, taller */
    .bento-main {
        grid-column: span 2;
        grid-row: span 2;
        min-height: 320px;
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }

    /* Side widgets - stacked */
    .bento-side {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Wide card - full width */
    .bento-wide {
        grid-column: span 3;
        padding: 1.5rem;
    }

    /* Two-thirds width */
    .bento-2of3 {
        grid-column: span 2;
        padding: 1.5rem;
    }

    /* One-third width */
    .bento-1of3 {
        padding: 1.5rem;
    }

    @media (max-width: 900px) {
        .bento-grid {
            grid-template-columns: 1fr 1fr;
        }
        .bento-main { grid-column: span 2; grid-row: span 1; min-height: auto; }
        .bento-wide { grid-column: span 2; }
        .bento-2of3 { grid-column: span 2; }
        .bento-1of3 { grid-column: span 1; }
    }

    @media (max-width: 600px) {
        .bento-grid {
            grid-template-columns: 1fr;
        }
        .bento-main, .bento-wide, .bento-2of3, .bento-1of3, .bento-side {
            grid-column: span 1;
        }
        .dashboard-container { padding: 1rem; }
    }

    /* Action card styling */
    .action-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }

    .action-desc {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    /* Progress indicator - asymmetric */
    .progress-indicator {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .progress-ring {
        width: 64px;
        height: 64px;
        position: relative;
    }

    .progress-ring-bg {
        fill: none;
        stroke: rgba(255, 255, 255, 0.1);
        stroke-width: 6;
    }

    .progress-ring-fill {
        fill: none;
        stroke: url(#goldGradient);
        stroke-width: 6;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: center;
        transition: stroke-dashoffset 1s ease-out;
    }

    .progress-ring-text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .progress-info {
        flex: 1;
    }

    .progress-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .progress-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* Section headers - left aligned with accent */
    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-left: 1rem;
        border-left: 3px solid var(--gold-start);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }

    .section-link {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gold-start);
        text-decoration: none;
        margin-left: auto;
        transition: color 0.2s;
    }

    .section-link:hover { color: var(--gold-end); }

    /* Lehrgang cards - varied shapes */
    .lehrgang-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .lehrgang-card {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
    }

    .lehrgang-card:nth-child(odd) {
        border-radius: 1.5rem 0.5rem 1rem 1rem;
    }

    .lehrgang-card:nth-child(even) {
        border-radius: 0.5rem 1.5rem 1rem 1rem;
    }

    .lehrgang-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .lehrgang-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .lehrgang-progress {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .lehrgang-progress-bar {
        flex: 1;
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .lehrgang-progress-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: 2px;
        transition: width 0.5s ease-out;
    }

    .lehrgang-progress-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .lehrgang-percent {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--gold-start);
        min-width: 36px;
        text-align: right;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 2.5rem 1.5rem;
    }

    .empty-state-icon {
        font-size: 2.5rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        opacity: 0.6;
    }

    .empty-state-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-state-desc {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 1.25rem;
    }

    /* Exam results - compact inline */
    .exam-inline {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .exam-inline:last-child { border-bottom: none; }

    .exam-inline-icon {
        font-size: 1.25rem;
    }

    .exam-inline-percent {
        font-size: 1rem;
        font-weight: 800;
        min-width: 50px;
    }

    .exam-inline-date {
        font-size: 0.75rem;
        color: var(--text-muted);
        flex: 1;
    }

    .exam-inline-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        text-transform: uppercase;
    }

    /* Alert styling */
    .alert-compact {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem 0.75rem 0.75rem 0;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-compact-icon { font-size: 1.25rem; }
    .alert-compact-content { flex: 1; }
    .alert-compact-title { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }
    .alert-compact-desc { font-size: 0.8rem; color: var(--text-secondary); }

    /* Leaderboard Modal */
    .leaderboard-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
        animation: fadeIn 0.3s ease-out;
    }

    .leaderboard-modal {
        background: var(--gradient-gold-135);
        border-radius: 1.5rem 0.5rem 1.5rem 1.5rem;
        max-width: 480px;
        width: 100%;
        position: relative;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 80px rgba(251, 191, 36, 0.15);
        animation: slideUp 0.4s ease-out;
        overflow: hidden;
    }

    .leaderboard-modal-content { padding: 2rem; position: relative; }

    .leaderboard-modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 1.25rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .leaderboard-modal-close:hover { background: rgba(255, 255, 255, 0.3); }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $total = $totalQuestions ?? \App\Models\Question::count();
    if (empty($total)) { $total = \App\Models\Question::count(); }

    $progressArr = is_array($user->solved_questions ?? null)
        ? $user->solved_questions
        : (is_string($user->solved_questions) ? json_decode($user->solved_questions, true) ?? [] : []);
    $progress = count($progressArr);

    $allExams = \App\Models\ExamStatistic::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    $exams = 0;
    foreach ($allExams as $exam) {
        if ($exam->is_passed) { $exams++; } else { break; }
    }

    try {
        $threshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();
        $totalProgressPoints = 0;
        if ($progressData && $progressData->count() > 0) {
            foreach ($progressData as $prog) { $totalProgressPoints += min($prog->consecutive_correct ?? 0, $threshold); }
        }
        $maxProgressPoints = $total * $threshold;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
    } catch (\Exception $e) { $progressPercent = 0; $totalProgressPoints = 0; }

    $enrolledLehrgaenge = Auth::user()->enrolledLehrgaenge()->get();

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
    $theoryOffset = $circumference - ($progressPercent / 100) * $circumference;
    $examOffset = $circumference - (min(100, $exams * 20) / 100) * $circumference;
@endphp

<!-- SVG Gradient Definition -->
<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="goldGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#fbbf24"/>
            <stop offset="100%" style="stop-color:#f59e0b"/>
        </linearGradient>
    </defs>
</svg>

@if(!$user->leaderboard_banner_dismissed && !$user->leaderboard_consent)
<div class="leaderboard-modal-overlay" id="leaderboard-modal">
    <div class="leaderboard-modal">
        <div class="leaderboard-modal-content">
            <button class="leaderboard-modal-close" onclick="dismissModal(false)">×</button>
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="display: inline-block; background: rgba(255, 255, 255, 0.2); border-radius: 1rem; padding: 1rem; margin-bottom: 1rem;">
                    <i class="bi bi-bar-chart" style="font-size: 2.5rem; color: white;"></i>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">Leaderboard</h2>
                <p style="color: white; font-size: 0.95rem; opacity: 0.9;">Vergleiche dich mit anderen</p>
            </div>
            <div style="background: rgba(255, 255, 255, 0.15); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <p style="color: white; font-size: 0.85rem; margin-bottom: 0.5rem;"><strong>Name & Punkte</strong> werden angezeigt</p>
                <p style="color: white; font-size: 0.85rem; margin: 0;"><strong>Jederzeit änderbar</strong> in den Einstellungen</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" id="declineForm" style="flex: 1;">
                    @csrf
                    <input type="hidden" name="accept" value="0">
                    <button type="submit" style="width: 100%; background: rgba(255,255,255,0.2); color: white; font-weight: 600; padding: 0.75rem; border-radius: 0.5rem; border: none; cursor: pointer;">Nein</button>
                </form>
                <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" id="acceptForm" style="flex: 1;">
                    @csrf
                    <input type="hidden" name="accept" value="1">
                    <button type="submit" style="width: 100%; background: white; color: #d97706; font-weight: 700; padding: 0.75rem; border-radius: 0.5rem; border: none; cursor: pointer;">Ja, mitmachen</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function dismissModal(accept) {
    const modal = document.getElementById('leaderboard-modal');
    if (modal) { modal.style.animation = 'fadeOut 0.3s ease-out forwards'; setTimeout(() => modal.remove(), 300); }
    if (accept === false) { document.getElementById('declineForm').submit(); }
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') dismissModal(false); });
</script>
@endif

<div class="dashboard-container">
    <!-- Left-aligned header -->
    <header class="dashboard-header">
        <h1 class="page-title">Hallo, <span>{{ $user->name }}</span></h1>
        <p class="page-subtitle">{{ $progressPercent }}% Fortschritt · Level {{ $user->level ?? 1 }}</p>
    </header>

    <!-- Alerts -->
    @if(session('error'))
    <div class="alert-compact glass-error">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('error') }}</div>
        </div>
        <button class="text-dark-secondary hover:text-dark-primary" onclick="this.parentElement.remove()">×</button>
    </div>
    @endif

    @if($hasFailedQuestions)
    <div class="alert-compact glass-warning">
        <i class="bi bi-arrow-repeat alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ count($failedArr) }} Frage{{ count($failedArr) == 1 ? '' : 'n' }} wiederholen</div>
            <div class="alert-compact-desc">Bevor du eine neue Prüfung starten kannst</div>
        </div>
        <a href="{{ route('failed.index') }}" class="btn-primary btn-sm">Los</a>
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

    @php
        $streakAtRisk = ($user->streak_days ?? 0) > 0
            && (!$user->last_activity_date || \Carbon\Carbon::parse($user->last_activity_date)->lt(\Carbon\Carbon::today()));
    @endphp
    @if($streakAtRisk)
    <div class="alert-compact glass-warning" style="animation: pulse-subtle 3s ease-in-out infinite;">
        <i class="bi bi-fire alert-compact-icon" style="color: #f59e0b;"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">Dein {{ $user->streak_days }}-Tage-Streak läuft ab</div>
            <div class="alert-compact-desc">Beantworte heute noch eine Frage, um deinen Streak zu halten</div>
        </div>
        <a href="{{ route('practice.all') }}" class="btn-primary btn-sm">Jetzt lernen</a>
    </div>
    @endif

    <!-- Stats as horizontal pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-warning"><i class="bi bi-fire"></i></span>
            <div>
                <div class="stat-pill-value">{{ $user->streak_days ?? 0 }}</div>
                <div class="stat-pill-label">Streak</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-star-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($user->points ?? 0) }}</div>
                <div class="stat-pill-label">Punkte</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-info"><i class="bi bi-lightning-charge-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ $user->daily_questions_solved ?? 0 }}/20</div>
                <div class="stat-pill-label">Heute</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #a855f7;"><i class="bi bi-trophy"></i></span>
            <div>
                <div class="stat-pill-value">{{ $unlockedCount }}/{{ $totalAchievements }}</div>
                <div class="stat-pill-label">Erfolge</div>
            </div>
        </div>
    </div>

    <!-- Bento Grid Layout -->
    <div class="bento-grid">
        <!-- Main: Theory Learning (spans 2 cols, 2 rows) -->
        <a href="{{ route('practice.menu') }}" class="glass-gold bento-main hover-lift" style="text-decoration: none; position: relative;">
            @if($progressPercent == 100)
                <div class="floating-badge">Abgeschlossen</div>
            @endif
            <div style="margin-bottom: 1.5rem;">
                <span class="badge-thw">Grundausbildung</span>
            </div>
            <h2 class="action-title">Theorie<br>Lernen</h2>
            <p class="action-desc">Alle {{ $total }} Fragen der THW-Grundausbildung. Jede Frage muss 2x richtig beantwortet werden.</p>

            <div class="progress-indicator">
                <div class="progress-ring">
                    <svg width="64" height="64" viewBox="0 0 64 64">
                        <circle class="progress-ring-bg" cx="32" cy="32" r="26"/>
                        <circle class="progress-ring-fill" cx="32" cy="32" r="26"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $theoryOffset }}"
                                id="theoryRing"/>
                    </svg>
                    <div class="progress-ring-text">{{ $progressPercent }}%</div>
                </div>
                <div class="progress-info">
                    <div class="progress-label">Gemeistert</div>
                    <div class="progress-value">{{ $progress }} von {{ $total }}</div>
                </div>
            </div>

            <span onclick="event.preventDefault(); event.stopPropagation(); window.location='{{ route('practice.all') }}';" class="btn-primary" style="align-self: flex-start;">
                Fragen üben
            </span>
        </a>

        <!-- Side: Exam Status -->
        <div class="glass-tl bento-side">
            <div style="margin-bottom: 0.75rem;">
                @if($exams >= 5)
                    <span class="badge-success">Bereit</span>
                @elseif($canStartExam)
                    <span class="badge-thw">Freigeschaltet</span>
                @else
                    <span class="badge-glass">Gesperrt</span>
                @endif
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Prüfung</h3>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">
                {{ $exams }}/5 bestanden
            </p>
            @if($canStartExam)
                <a href="{{ route('exam.index') }}" class="btn-secondary btn-sm" style="align-self: flex-start;">Starten</a>
            @elseif($hasFailedQuestions)
                <a href="{{ route('failed.index') }}" class="btn-ghost btn-sm" style="align-self: flex-start;">Erst Fehler lösen</a>
            @else
                <span class="btn-ghost btn-sm" style="opacity: 0.5; align-self: flex-start;">Erst Theorie</span>
            @endif
        </div>

        <!-- Side: Quick Stats -->
        <div class="glass-br bento-side">
            <div class="progress-indicator" style="margin-bottom: 0;">
                <div class="progress-ring" style="width: 56px; height: 56px;">
                    <svg width="56" height="56" viewBox="0 0 64 64">
                        <circle class="progress-ring-bg" cx="32" cy="32" r="26"/>
                        <circle class="progress-ring-fill" cx="32" cy="32" r="26"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $examOffset }}"
                                style="stroke: url(#goldGradient)"/>
                    </svg>
                    <div class="progress-ring-text" style="font-size: 0.85rem;">{{ min(100, $exams * 20) }}%</div>
                </div>
                <div class="progress-info">
                    <div class="progress-label">Prüfungsstreak</div>
                    <div class="progress-value">{{ $exams }} Prüfungen</div>
                </div>
            </div>
        </div>

        <!-- Recent Exams (if any) -->
        @if(!empty($recentExams) && $recentExams->count() > 0)
        <div class="glass-slash bento-2of3">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Letzte Prüfungen</span>
                <a href="{{ route('exam.history') }}" style="font-size: 0.75rem; color: var(--gold-start); text-decoration: none; font-weight: 600;">Alle anzeigen</a>
            </div>
            @foreach($recentExams->take(3) as $exam)
                @php $percentage = round(($exam->correct_answers / 40) * 100); @endphp
                <div class="exam-inline">
                    <span class="exam-inline-icon {{ $exam->is_passed ? 'text-success' : 'text-error' }}">
                        <i class="bi bi-{{ $exam->is_passed ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                    </span>
                    <span class="exam-inline-percent {{ $exam->is_passed ? 'text-success' : 'text-error' }}">{{ $percentage }}%</span>
                    <span class="exam-inline-date">{{ $exam->created_at->format('d.m.') }}</span>
                    <span class="exam-inline-badge {{ $exam->is_passed ? 'badge-success' : 'badge-error' }}">
                        {{ $exam->is_passed ? 'Bestanden' : 'Nicht best.' }}
                    </span>
                </div>
            @endforeach
        </div>

        <!-- Exam Summary -->
        <div class="glass-organic bento-1of3" style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
            @php
                $avgPercent = $recentExams->avg(fn($e) => round(($e->correct_answers / 40) * 100));
            @endphp
            <div style="font-size: 2rem; font-weight: 800;" class="text-gradient-gold">{{ round($avgPercent) }}%</div>
            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Durchschnitt</div>
        </div>
        @endif

        <!-- Ortsverband Card (for Ausbilder) -->
        @php
            $userOV = auth()->user()->ortsverbände->first();
            $isAusbilder = false;
            $ovStats = null;

            if ($userOV) {
                $memberPivot = $userOV->members()->where('user_id', auth()->id())->first();
                $isAusbilder = $memberPivot && $memberPivot->pivot->role === 'ausbildungsbeauftragter';

                if ($isAusbilder) {
                    $regularMembers = $userOV->members()->wherePivot('role', 'member')->get();
                    $memberCount = $regularMembers->count();
                    $memberProgress = $userOV->getMemberProgress()->filter(fn($m) => $m['role'] === 'member');
                    $avgProgress = $memberProgress->avg('theory_progress_percent') ?? 0;
                    $ovStats = ['members' => $memberCount, 'avg_progress' => round($avgProgress)];
                }
            }
        @endphp

        @if($isAusbilder && $userOV)
        <div class="glass-thw bento-wide" style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
            <div>
                <span class="badge-thw" style="margin-bottom: 0.5rem; display: inline-block;">Ausbilder</span>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">{{ $userOV->name }}</h3>
            </div>
            <div style="display: flex; gap: 2rem; flex: 1; justify-content: center;">
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 800;" class="text-gradient-gold">{{ $ovStats['members'] }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Mitglieder</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 800; color: {{ $ovStats['avg_progress'] >= 50 ? '#22c55e' : '#f59e0b' }};">{{ $ovStats['avg_progress'] }}%</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Ø Fortschritt</div>
                </div>
            </div>
            <a href="{{ route('ortsverband.index') }}" class="btn-secondary btn-sm">Verwalten</a>
        </div>
        @endif
    </div>

    <!-- Lehrgänge Section -->
    <div class="section-header">
        <h2 class="section-title">Deine Lehrgänge</h2>
        <a href="{{ route('lehrgaenge.index') }}" class="section-link">Alle anzeigen</a>
    </div>

    @if($enrolledLehrgaenge->isNotEmpty())
    <div class="lehrgang-grid">
        @foreach($enrolledLehrgaenge->take(4) as $lehrgang)
            @php
                $solvedCount = \App\Models\UserLehrgangProgress::where('user_id', Auth::id())->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))->where('solved', true)->count();
                $totalCount = \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
                $progressData = \App\Models\UserLehrgangProgress::where('user_id', Auth::id())->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))->get();
                $totalProgressPoints = 0;
                foreach ($progressData as $prog) { $totalProgressPoints += min($prog->consecutive_correct, \App\Models\UserQuestionProgress::MASTERY_THRESHOLD); }
                $maxProgressPoints = $totalCount * \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
                $lehrgangProgressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                $isCompleted = $lehrgangProgressPercent == 100 && $solvedCount > 0;
            @endphp
            <div class="glass lehrgang-card hover-lift">
                <h4 class="lehrgang-title">{{ $lehrgang->lehrgang }}</h4>
                <p class="lehrgang-desc">{{ $lehrgang->beschreibung }}</p>
                <div class="lehrgang-progress">
                    <div class="lehrgang-progress-bar">
                        <div class="lehrgang-progress-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $lehrgangProgressPercent }}%"></div>
                    </div>
                    <span class="lehrgang-percent">{{ $lehrgangProgressPercent }}%</span>
                </div>
                @if($isCompleted)
                    <span class="btn-ghost btn-sm" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.25);">Fertig</span>
                @else
                    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="btn-primary btn-sm">Weiter</a>
                @endif
            </div>
        @endforeach
    </div>
    @else
    <div class="glass-slash empty-state">
        <div class="empty-state-icon"><i class="bi bi-mortarboard"></i></div>
        <h3 class="empty-state-title">Keine Lehrgänge</h3>
        <p class="empty-state-desc">Spezialisiere dich auf bestimmte THW-Themen</p>
        <a href="{{ route('lehrgaenge.index') }}" class="btn-primary btn-sm">Entdecken</a>
    </div>
    @endif

    <!-- Lernpools Section -->
    @php
        $enrolledLernpools = auth()->user()->enrolledLernpools()->where('is_active', true)->get();
    @endphp

    @if($enrolledLernpools->isNotEmpty())
    <div class="section-header" style="margin-top: 1.5rem;">
        <h2 class="section-title">Deine Lernpools</h2>
        <a href="{{ route('ortsverband.index') }}" class="section-link">Alle anzeigen</a>
    </div>

    <div class="lehrgang-grid">
        @foreach($enrolledLernpools->take(3) as $lernpool)
            @php
                $solvedCount = auth()->user()->lernpoolProgress()
                    ->whereHas('question', fn($q) => $q->where('lernpool_id', $lernpool->id))
                    ->where('solved', true)
                    ->count();
                $totalCount = $lernpool->getQuestionCount();
                $lernpoolProgress = $totalCount > 0 ? round(($solvedCount / $totalCount) * 100) : 0;
                $isCompleted = $lernpoolProgress == 100 && $solvedCount > 0;
            @endphp
            <div class="glass lehrgang-card hover-lift">
                <h4 class="lehrgang-title">{{ $lernpool->name }}</h4>
                @if($lernpool->tags && count($lernpool->tags) > 0)
                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                    @foreach(array_slice($lernpool->tags, 0, 2) as $tag)
                        <span class="badge-thw" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">{{ $tag }}</span>
                    @endforeach
                </div>
                @endif
                <p class="lehrgang-desc">{{ $lernpool->description }}</p>
                <div class="lehrgang-progress">
                    <div class="lehrgang-progress-bar">
                        <div class="lehrgang-progress-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $lernpoolProgress }}%"></div>
                    </div>
                    <span class="lehrgang-percent">{{ $lernpoolProgress }}%</span>
                </div>
                @if($isCompleted)
                    <span class="btn-ghost btn-sm" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.25);">Gemeistert</span>
                @else
                    <a href="{{ route('ortsverband.lernpools.practice', [$lernpool->ortsverband_id, $lernpool->id]) }}" class="btn-primary btn-sm">Weiter</a>
                @endif
            </div>
        @endforeach
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.0/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($progressPercent == 100)
    setTimeout(() => {
        confetti({ particleCount: 80, spread: 60, origin: { y: 0.6 } });
    }, 1000);
    @endif
});

function dismissEmailConsentBanner() {
    const banner = document.getElementById('email-consent-banner');
    if (banner) { banner.style.opacity = '0'; setTimeout(() => banner.remove(), 300); }
    fetch('/dashboard/dismiss-email-consent-banner', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } });
}
</script>
@endsection
