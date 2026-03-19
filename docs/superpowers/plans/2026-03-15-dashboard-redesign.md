# Dashboard & Statistik-Seite Redesign — Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Komplett neues Dashboard mit modularen Sektionen (Smart Action Card, Journey-Stepper, Gamification-Row) plus neue Statistik-Seite. Mobile-First, Dark/Light Mode, THW-Blau als Primärfarbe.

**Architecture:** Dashboard-View wird komplett neu geschrieben. Route-Closure in `routes/web.php` wird um Smart Action Logik erweitert. Neue `StatisticsController` + View für ausgelagerte Statistiken. CSS-Erweiterungen sind Dashboard-spezifisch, kein globaler Design-System-Wechsel.

**Tech Stack:** Laravel 12, Blade, Tailwind CSS, Alpine.js, Bootstrap Icons. Kein externes Chart-Library — alle Visualisierungen mit CSS/Tailwind.

**Spec:** `docs/superpowers/specs/2026-03-15-dashboard-redesign.md`

---

## File Structure

| Action | Path | Responsibility |
|--------|------|----------------|
| Rewrite | `resources/views/dashboard.blade.php` | Komplettes Dashboard-View |
| Modify | `resources/css/app.css` | Dashboard-spezifische CSS-Klassen, Light-Mode-Erweiterungen |
| Modify | `routes/web.php` | Smart Action Logik in Dashboard-Route + neue `/statistics` Route |
| Create | `app/Http/Controllers/StatisticsController.php` | Daten-Aggregation für Statistik-Seite |
| Create | `resources/views/statistics.blade.php` | Statistik-Seite View |

---

## Chunk 1: CSS Foundation & Dashboard Route

### Task 1: Dashboard-spezifische CSS-Klassen hinzufügen

**Files:**
- Modify: `resources/css/app.css` (am Ende der Datei anhängen)

- [ ] **Step 1: Read current app.css to find insertion point**

Read `resources/css/app.css` and find the last section. New dashboard-specific CSS will be appended at the end.

- [ ] **Step 2: Add dashboard-specific CSS classes**

Append to `resources/css/app.css`:

```css
/* =====================================================
   DASHBOARD REDESIGN - Modulare Sektionen
   Scope: nur /dashboard und /statistics
   ===================================================== */

/* Dashboard Container */
.dash-container {
    max-width: 1100px;
    margin: 0 auto;
    width: 100%;
}

/* Smart Action Card */
.smart-action {
    background: linear-gradient(135deg, #00337F, #0055cc);
    border-radius: 1rem;
    padding: 1.25rem;
    position: relative;
    overflow: hidden;
    color: #fff;
}

.smart-action::before {
    content: '';
    position: absolute;
    bottom: -30px;
    right: -20px;
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, rgba(91, 154, 255, 0.25), transparent);
    border-radius: 50%;
    pointer-events: none;
}

.smart-action--urgent {
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
}

.smart-action__label {
    font-size: 0.625rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 0.375rem;
}

.smart-action__title {
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.smart-action__desc {
    font-size: 0.8125rem;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 0.75rem;
}

.smart-action__btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none;
    transition: background var(--transition-fast);
}

.smart-action__btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: #fff;
}

.smart-action__countdown {
    font-size: 0.6875rem;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 0.75rem;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* Journey Stepper */
.journey {
    display: flex;
    align-items: flex-start;
    gap: 0;
}

.journey--horizontal {
    flex-direction: row;
    align-items: center;
}

.journey--vertical {
    flex-direction: column;
}

.journey__step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    flex: 1;
}

.journey--vertical .journey__step {
    flex-direction: row;
    text-align: left;
    align-items: flex-start;
    gap: 0.75rem;
    padding-bottom: 1.5rem;
}

.journey__circle {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6875rem;
    font-weight: 700;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.journey__circle--done {
    background: #0055cc;
    color: #fff;
}

.journey__circle--active {
    background: rgba(0, 85, 204, 0.3);
    border: 2px solid #0055cc;
    color: #5b9aff;
}

.journey__circle--locked {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.3);
}

.light-mode .journey__circle--locked {
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(0, 0, 0, 0.15);
    color: rgba(0, 0, 0, 0.3);
}

.journey__line {
    flex: 1;
    height: 3px;
    background: rgba(255, 255, 255, 0.1);
    align-self: center;
}

.light-mode .journey__line {
    background: rgba(0, 0, 0, 0.1);
}

.journey__line--done {
    background: #0055cc;
}

.journey--vertical .journey__line {
    width: 3px;
    height: 1.5rem;
    margin-left: 0.9375rem;
    flex: none;
}

.journey__label {
    font-size: 0.5625rem;
    color: var(--text-muted);
    margin-top: 0.375rem;
    line-height: 1.2;
}

.journey__label--active {
    color: #5b9aff;
}

.light-mode .journey__label--active {
    color: #00337F;
}

.journey__pct {
    font-size: 0.6875rem;
    font-weight: 600;
}

/* Gamification Pills */
.gami-pill {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 0.75rem;
    padding: 0.75rem;
    text-align: center;
    flex: 1;
}

.gami-pill__value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
}

.gami-pill__value--gold {
    color: var(--gold);
}

.gami-pill__value--blue {
    color: #5b9aff;
}

.light-mode .gami-pill__value--blue {
    color: #00337F;
}

.gami-pill__label {
    font-size: 0.5625rem;
    color: var(--text-muted);
    margin-top: 0.125rem;
}

.gami-pill--streak-risk {
    animation: streak-pulse 2s ease-in-out infinite;
    border-color: var(--gold);
}

@keyframes streak-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.3); }
    50% { box-shadow: 0 0 12px 4px rgba(251, 191, 36, 0.15); }
}

/* Weekly Activity Bars */
.activity-bar {
    display: flex;
    align-items: flex-end;
    gap: 0.25rem;
    height: 80px;
}

.activity-bar__col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    height: 100%;
    justify-content: flex-end;
}

.activity-bar__fill {
    width: 100%;
    border-radius: 0.25rem 0.25rem 0 0;
    background: #0055cc;
    min-height: 2px;
    transition: height var(--transition-normal);
}

.activity-bar__fill--today {
    background: #5b9aff;
    box-shadow: 0 0 8px rgba(91, 154, 255, 0.3);
}

.activity-bar__fill--empty {
    background: rgba(255, 255, 255, 0.06);
}

.light-mode .activity-bar__fill--empty {
    background: rgba(0, 0, 0, 0.06);
}

.activity-bar__count {
    font-size: 0.5625rem;
    color: var(--text-muted);
}

.activity-bar__day {
    font-size: 0.5625rem;
    color: var(--text-muted);
}

.activity-bar__day--today {
    color: #5b9aff;
    font-weight: 600;
}

.light-mode .activity-bar__day--today {
    color: #00337F;
}

/* Quick Link Buttons (Dashboard) */
.dash-quick-link {
    background: rgba(0, 51, 127, 0.15);
    border: 1px solid rgba(0, 51, 127, 0.25);
    border-radius: 0.75rem;
    padding: 0.875rem 0.625rem;
    text-align: center;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #5b9aff;
    text-decoration: none;
    transition: background var(--transition-fast);
    flex: 1;
}

.dash-quick-link:hover {
    background: rgba(0, 51, 127, 0.25);
    color: #5b9aff;
}

.light-mode .dash-quick-link {
    background: rgba(0, 51, 127, 0.08);
    color: #00337F;
}

.light-mode .dash-quick-link:hover {
    background: rgba(0, 51, 127, 0.15);
}

/* Statistics Page - Section Cards */
.stat-section-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 0.75rem;
    padding: 1rem;
    transition: transform var(--transition-fast);
}

.stat-section-card:hover {
    transform: translateY(-2px);
}

.stat-section-card__bar {
    height: 0.375rem;
    border-radius: 0.1875rem;
    background: rgba(255, 255, 255, 0.1);
    overflow: hidden;
}

.light-mode .stat-section-card__bar {
    background: rgba(0, 0, 0, 0.08);
}

.stat-section-card__fill--green { background: var(--success); }
.stat-section-card__fill--blue { background: #0055cc; }
.stat-section-card__fill--red { background: var(--error); }

/* Heatmap Calendar */
.heatmap-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.1875rem;
}

.heatmap-cell {
    aspect-ratio: 1;
    border-radius: 0.1875rem;
    background: rgba(255, 255, 255, 0.06);
    transition: transform var(--transition-fast);
}

.light-mode .heatmap-cell {
    background: rgba(0, 0, 0, 0.06);
}

.heatmap-cell:hover {
    transform: scale(1.2);
}

.heatmap-cell--l1 { background: rgba(0, 85, 204, 0.2); }
.heatmap-cell--l2 { background: rgba(0, 85, 204, 0.4); }
.heatmap-cell--l3 { background: rgba(0, 85, 204, 0.6); }
.heatmap-cell--l4 { background: #0055cc; }

/* Desktop Dashboard Layout */
@media (min-width: 1024px) {
    .dash-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
    }
}
```

- [ ] **Step 3: Build CSS**

Run: `npm run build`
Expected: Build succeeds without errors.

- [ ] **Step 4: Commit**

```bash
git add resources/css/app.css
git commit -m "🎨: Dashboard CSS-Klassen"
```

---

### Task 2: Dashboard Route erweitern — Smart Action Logik

**Files:**
- Modify: `routes/web.php` (Dashboard Route-Closure, ~lines 55-105)

- [ ] **Step 1: Read current dashboard route**

Read `routes/web.php` lines 55-110 to see the current route closure.

- [ ] **Step 2: Add Smart Action variables to dashboard route**

Inside the existing dashboard route closure, after the existing data fetching but before `return view(...)`, add Smart Action computation:

```php
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
    $smartAction = [
        'type' => 'ready', 'label' => 'Bereit',
        'title' => 'Alle Fragen gemeistert — Prüfung ablegen!',
        'desc' => $exams . '/5 Prüfungen bestanden',
        'route' => route('exam.index'),
        'btn' => 'Prüfung starten',
    ];
} else {
    // Find best section to continue
    $solvedCount = \App\Models\UserQuestionProgress::where('user_id', $user->id)->count();
    if ($solvedCount > 0) {
        $bestSection = \App\Models\UserQuestionProgress::where('user_id', $user->id)
            ->join('questions', 'user_question_progress.question_id', '=', 'questions.id')
            ->selectRaw('questions.lernabschnitt, COUNT(*) as cnt')
            ->groupBy('questions.lernabschnitt')
            ->orderByDesc('cnt')
            ->first();
        $sectionNum = $bestSection?->lernabschnitt ?? 1;
        $smartAction = [
            'type' => 'continue', 'label' => 'Weitermachen',
            'title' => 'Weiter mit Lernabschnitt ' . $sectionNum,
            'desc' => 'Du bist auf einem guten Weg',
            'route' => route('practice.section', $sectionNum),
            'btn' => 'Starten',
        ];
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

// Leaderboard rank
$leaderboardRank = null;
if ($user->leaderboard_consent) {
    $leaderboardRank = \App\Models\User::where('leaderboard_consent', true)
        ->where('points', '>', $user->points)->count() + 1;
}

// Mastery percent (for journey stepper)
$masteryPercent = $totalQuestions > 0
    ? round((\App\Models\UserQuestionProgress::where('user_id', $user->id)
        ->where('consecutive_correct', '>=', 3)->count() / $totalQuestions) * 100)
    : 0;

// Solved questions total
$solvedTotal = \App\Models\UserQuestionProgress::where('user_id', $user->id)->count();
$solvedPercent = $totalQuestions > 0 ? round(($solvedTotal / $totalQuestions) * 100) : 0;
```

Update the `compact()` call to include all new variables:

```php
return view('dashboard', compact(
    'user', 'recentExams', 'totalQuestions', 'spacedRepetitionDue',
    'weeklyActivity', 'sectionStats', 'streakFreezeStatus',
    'smartAction', 'examCountdown', 'enrolledLehrgaenge',
    'streakAtRisk', 'leaderboardRank', 'progressPercent',
    'masteryPercent', 'solvedPercent', 'solvedTotal',
    'canStartExam', 'exams', 'hasFailedQuestions'
));
```

- [ ] **Step 3: Clear caches and verify no errors**

Run: `php artisan view:clear && php artisan cache:clear && php artisan route:list --path=dashboard`
Expected: Route listed without errors.

- [ ] **Step 4: Commit**

```bash
git add routes/web.php
git commit -m "⚡: Dashboard Smart Action Logik"
```

---

## Chunk 2: Dashboard View — Complete Rewrite

### Task 3: Dashboard View neu schreiben

**Files:**
- Rewrite: `resources/views/dashboard.blade.php`

This is the largest task. The entire dashboard.blade.php is rewritten. Read the current file first to preserve any component includes (gamification notifications, leaderboard modal, etc.).

- [ ] **Step 1: Read current dashboard.blade.php completely**

Read the full file to identify all component includes, Alpine.js data bindings, and JavaScript that must be preserved (especially the leaderboard modal, onboarding tour references, and gamification notification triggers).

- [ ] **Step 2: Write the new dashboard.blade.php**

Rewrite the file with the following structure (all sections from the spec):

```blade
@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    /* Minimal view-specific overrides only — bulk CSS is in app.css */
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- 1. Header --}}
    <div class="mb-4">
        <p class="text-sm" style="color: var(--text-muted);">
            {{ now()->hour < 12 ? 'Guten Morgen' : (now()->hour < 18 ? 'Guten Tag' : 'Guten Abend') }}
        </p>
        <div class="flex items-baseline gap-2">
            <h1 class="text-xl font-bold" style="color: var(--text-primary);">
                {{ $user->name }}
            </h1>
            <span class="text-sm font-semibold" style="color: #5b9aff;">
                Level {{ $user->level }} · {{ number_format($user->points) }} XP
            </span>
        </div>
    </div>

    {{-- Desktop: 2-column grid --}}
    <div class="dash-grid">
        {{-- Main Column --}}
        <div class="space-y-4">

            {{-- 2. Smart Action Card --}}
            <a href="{{ $smartAction['route'] }}"
               class="smart-action block {{ $smartAction['type'] === 'urgent' ? 'smart-action--urgent' : '' }}">
                <div class="smart-action__label">{{ $smartAction['label'] }}</div>
                <div class="smart-action__title">{{ $smartAction['title'] }}</div>
                <div class="smart-action__desc">{{ $smartAction['desc'] }}</div>
                <span class="smart-action__btn">
                    {{ $smartAction['btn'] }}
                    <i class="bi bi-arrow-right"></i>
                </span>
                @if($examCountdown)
                <div class="smart-action__countdown">
                    Noch {{ $examCountdown['daysLeft'] }} Tage bis zur Prüfung ·
                    {{ $examCountdown['dailyTarget'] }} Fragen/Tag empfohlen
                </div>
                @endif
            </a>

            {{-- 3. Journey Stepper (Mobile: horizontal) --}}
            <div class="glass p-4 lg:hidden" style="border-radius: 0.75rem;">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--text-muted);">
                    Dein Fortschritt
                </div>
                <div class="journey journey--horizontal">
                    {{-- Step 1: Fragen lernen --}}
                    <div class="journey__step">
                        <div class="journey__circle {{ $solvedPercent >= 100 ? 'journey__circle--done' : 'journey__circle--active' }}">
                            @if($solvedPercent >= 100) ✓ @else 1 @endif
                        </div>
                        <div class="journey__label {{ $solvedPercent > 0 ? 'journey__label--active' : '' }}">
                            Fragen lernen
                            <div class="journey__pct">{{ $solvedPercent }}%</div>
                        </div>
                    </div>
                    <div class="journey__line {{ $solvedPercent >= 100 ? 'journey__line--done' : '' }}"
                         style="background: linear-gradient(90deg, #0055cc {{ $solvedPercent }}%, rgba(255,255,255,0.1) {{ $solvedPercent }}%);"></div>

                    {{-- Step 2: Alle meistern --}}
                    <div class="journey__step">
                        <div class="journey__circle {{ $masteryPercent >= 100 ? 'journey__circle--done' : ($masteryPercent > 0 ? 'journey__circle--active' : 'journey__circle--locked') }}">
                            @if($masteryPercent >= 100) ✓ @else 2 @endif
                        </div>
                        <div class="journey__label {{ $masteryPercent > 0 ? 'journey__label--active' : '' }}">
                            Alle meistern
                            <div class="journey__pct">{{ $masteryPercent }}%</div>
                        </div>
                    </div>
                    <div class="journey__line {{ $masteryPercent >= 100 ? 'journey__line--done' : '' }}"></div>

                    {{-- Step 3: Prüfung --}}
                    <div class="journey__step">
                        <div class="journey__circle {{ $exams >= 5 ? 'journey__circle--done' : ($canStartExam ? 'journey__circle--active' : 'journey__circle--locked') }}">
                            @if($exams >= 5) ✓ @else 3 @endif
                        </div>
                        <div class="journey__label {{ $canStartExam ? 'journey__label--active' : '' }}">
                            Prüfung
                            <div class="journey__pct">
                                @if($exams >= 5) Bestanden
                                @elseif($canStartExam) Bereit
                                @else Gesperrt
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. Wochenaktivität --}}
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs uppercase tracking-wider" style="color: var(--text-muted);">Diese Woche</span>
                    <a href="{{ route('statistics') }}" class="text-xs font-medium" style="color: #5b9aff; text-decoration: none;">
                        Statistiken →
                    </a>
                </div>
                @php
                    $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
                    $weeklyCollection = collect($weeklyActivity);
                    $maxCount = $weeklyCollection->max('count') ?: 1;
                @endphp
                <div class="activity-bar">
                    @for($d = 0; $d < 7; $d++)
                        @php
                            $date = now()->startOfWeek()->addDays($d)->format('Y-m-d');
                            $dayData = $weeklyCollection->firstWhere('date', $date);
                            $count = $dayData->count ?? 0;
                            $heightPct = $maxCount > 0 ? max(($count / $maxCount) * 100, 3) : 3;
                            $isToday = $date === now()->format('Y-m-d');
                        @endphp
                        <div class="activity-bar__col">
                            <span class="activity-bar__count">{{ $count ?: '' }}</span>
                            <div class="activity-bar__fill {{ $isToday ? 'activity-bar__fill--today' : ($count === 0 ? 'activity-bar__fill--empty' : '') }}"
                                 style="height: {{ $count > 0 ? $heightPct : 8 }}%;"></div>
                            <span class="activity-bar__day {{ $isToday ? 'activity-bar__day--today' : '' }}">
                                {{ $days[$d] }}
                            </span>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- 6. Lehrgänge --}}
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs uppercase tracking-wider" style="color: var(--text-muted);">Lehrgänge</span>
                    <a href="{{ route('lehrgaenge.index') }}" class="text-xs font-medium" style="color: #5b9aff; text-decoration: none;">
                        Alle anzeigen →
                    </a>
                </div>
                @if($enrolledLehrgaenge->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-sm mb-2" style="color: var(--text-muted);">
                            Entdecke Lehrgänge für strukturiertes Lernen
                        </p>
                        <a href="{{ route('lehrgaenge.index') }}" class="btn-secondary btn-sm">Lehrgänge entdecken</a>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($enrolledLehrgaenge->take(3) as $lehrgang)
                            @php
                                $lgTotal = $lehrgang->questions()->count();
                                $lgSolved = $lgTotal > 0
                                    ? \App\Models\UserQuestionProgress::where('user_id', $user->id)
                                        ->whereIn('question_id', $lehrgang->questions()->pluck('question_id'))
                                        ->where('consecutive_correct', '>=', 3)->count()
                                    : 0;
                                $lgPct = $lgTotal > 0 ? round(($lgSolved / $lgTotal) * 100) : 0;
                            @endphp
                            <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}"
                               class="block p-3 rounded-lg hover:opacity-80 transition-opacity"
                               style="background: var(--glass-bg); border: 1px solid var(--glass-border); text-decoration: none;">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium" style="color: var(--text-primary);">
                                        {{ $lehrgang->lehrgang }}
                                    </span>
                                    <span class="text-xs font-semibold" style="color: #5b9aff;">{{ $lgPct }}%</span>
                                </div>
                                <div class="h-1 rounded-full" style="background: rgba(255,255,255,0.1);">
                                    <div class="h-full rounded-full" style="background: #0055cc; width: {{ $lgPct }}%;"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Ausbilder-Karte (konditionell) --}}
            @php
                $userOV = $user->ortsverbande->first();
                $isAusbilder = $userOV && $userOV->members()->where('user_id', $user->id)->first()?->pivot->role === 'ausbildungsbeauftragter';
            @endphp
            @if($isAusbilder && $userOV)
                <div class="glass-blue p-4" style="border-radius: 0.75rem;">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="badge-thw text-xs mb-1">Ausbilder</span>
                            <p class="text-sm font-medium mt-1" style="color: var(--text-primary);">
                                {{ $userOV->name }}
                            </p>
                        </div>
                        <a href="{{ route('ortsverband.index') }}" class="btn-secondary btn-sm">Verwalten</a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar (Desktop only) --}}
        <div class="hidden lg:block space-y-4">

            {{-- Journey Stepper (vertical on desktop) --}}
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--text-muted);">
                    Dein Weg zur Prüfung
                </div>
                <div class="journey journey--vertical">
                    {{-- Step 1 --}}
                    <div class="journey__step">
                        <div class="journey__circle {{ $solvedPercent >= 100 ? 'journey__circle--done' : 'journey__circle--active' }}">
                            @if($solvedPercent >= 100) ✓ @else 1 @endif
                        </div>
                        <div>
                            <div class="text-sm font-medium" style="color: {{ $solvedPercent > 0 ? '#5b9aff' : 'var(--text-muted)' }};">
                                Fragen lernen
                            </div>
                            <div class="text-xs mt-1" style="color: var(--text-muted);">{{ $solvedPercent }}% gelöst</div>
                            <div class="h-1 rounded-full mt-1" style="background: rgba(255,255,255,0.1); width: 100%;">
                                <div class="h-full rounded-full" style="background: #0055cc; width: {{ $solvedPercent }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="journey__line {{ $solvedPercent >= 100 ? 'journey__line--done' : '' }}"></div>

                    {{-- Step 2 --}}
                    <div class="journey__step">
                        <div class="journey__circle {{ $masteryPercent >= 100 ? 'journey__circle--done' : ($masteryPercent > 0 ? 'journey__circle--active' : 'journey__circle--locked') }}">
                            @if($masteryPercent >= 100) ✓ @else 2 @endif
                        </div>
                        <div>
                            <div class="text-sm font-medium" style="color: {{ $masteryPercent > 0 ? '#5b9aff' : 'var(--text-muted)' }};">
                                Alle meistern
                            </div>
                            <div class="text-xs mt-1" style="color: var(--text-muted);">{{ $masteryPercent }}% gemeistert</div>
                            <div class="h-1 rounded-full mt-1" style="background: rgba(255,255,255,0.1); width: 100%;">
                                <div class="h-full rounded-full" style="background: #0055cc; width: {{ $masteryPercent }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="journey__line {{ $masteryPercent >= 100 ? 'journey__line--done' : '' }}"></div>

                    {{-- Step 3 --}}
                    <div class="journey__step">
                        <div class="journey__circle {{ $exams >= 5 ? 'journey__circle--done' : ($canStartExam ? 'journey__circle--active' : 'journey__circle--locked') }}">
                            @if($exams >= 5) ✓ @else 3 @endif
                        </div>
                        <div>
                            <div class="text-sm font-medium" style="color: {{ $canStartExam ? '#5b9aff' : 'var(--text-muted)' }};">
                                Prüfung
                            </div>
                            <div class="text-xs mt-1" style="color: var(--text-muted);">
                                @if($exams >= 5) {{ $exams }}/5 bestanden
                                @elseif($canStartExam) Bereit
                                @else Erst alle Fragen meistern
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Gamification Row --}}
            <div class="flex gap-2">
                <div class="gami-pill {{ $streakAtRisk ? 'gami-pill--streak-risk' : '' }}"
                     style="{{ $user->streak_days > 0 ? 'background: rgba(251,191,36,0.08); border-color: rgba(251,191,36,0.15);' : '' }}">
                    <div class="gami-pill__value gami-pill__value--gold">{{ $user->streak_days }}</div>
                    <div class="gami-pill__label">Tage Streak</div>
                </div>
                <div class="gami-pill">
                    <div class="gami-pill__value">{{ $solvedTotal }}</div>
                    <div class="gami-pill__label">Gelöst</div>
                </div>
                <div class="gami-pill">
                    <div class="gami-pill__value gami-pill__value--blue">
                        {{ $leaderboardRank ? '#' . $leaderboardRank : '—' }}
                    </div>
                    <div class="gami-pill__label">Ranking</div>
                </div>
            </div>

            {{-- Exam Countdown (if set) --}}
            @if($examCountdown)
            <div class="glass p-3" style="border-radius: 0.75rem; border-left: 3px solid #0055cc;">
                <div class="text-xs uppercase tracking-wider mb-2" style="color: var(--text-muted);">Prüfungscountdown</div>
                <div class="text-2xl font-bold" style="color: var(--text-primary);">{{ $examCountdown['daysLeft'] }} Tage</div>
                <div class="text-xs mt-1" style="color: var(--text-muted);">
                    Heute: {{ $examCountdown['todayAnswered'] }}/{{ $examCountdown['dailyTarget'] }} Fragen
                </div>
                <div class="h-1 rounded-full mt-2" style="background: rgba(255,255,255,0.1);">
                    <div class="h-full rounded-full" style="background: #0055cc; width: {{ $examCountdown['dailyTarget'] > 0 ? min(100, round($examCountdown['todayAnswered'] / $examCountdown['dailyTarget'] * 100)) : 0 }}%;"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Mobile: Gamification Row + Quick Links --}}
    <div class="lg:hidden space-y-4 mt-4">
        {{-- Gamification Row --}}
        <div class="flex gap-2">
            <div class="gami-pill {{ $streakAtRisk ? 'gami-pill--streak-risk' : '' }}"
                 style="{{ $user->streak_days > 0 ? 'background: rgba(251,191,36,0.08); border-color: rgba(251,191,36,0.15);' : '' }}">
                <div class="gami-pill__value gami-pill__value--gold">{{ $user->streak_days }}</div>
                <div class="gami-pill__label">Tage Streak</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value">{{ $solvedTotal }}</div>
                <div class="gami-pill__label">Gelöst</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value gami-pill__value--blue">
                    {{ $leaderboardRank ? '#' . $leaderboardRank : '—' }}
                </div>
                <div class="gami-pill__label">Ranking</div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="flex gap-2">
            <a href="{{ route('practice.menu') }}" class="dash-quick-link">Üben</a>
            <a href="{{ route('lehrgaenge.index') }}" class="dash-quick-link">Lehrgänge</a>
            <a href="{{ route('shop.index') }}" class="dash-quick-link">Shop</a>
        </div>
    </div>

</div>

{{-- Preserve existing: Leaderboard Modal --}}
{{-- Copy the leaderboard modal code from the old dashboard verbatim --}}

@endsection

@push('scripts')
<script>
    // Preserve any existing dashboard JS (leaderboard dismiss, etc.)
</script>
@endpush
```

**Important implementation notes:**
- Read the old `dashboard.blade.php` completely before rewriting
- Copy the leaderboard modal section verbatim from the old file
- Copy any Alpine.js data bindings for the leaderboard dismiss logic
- Preserve `@include('components.onboarding-tour')` if it exists
- The `$weeklyActivity` array structure needs to be checked — adapt the `day_of_week` key to match what the route provides
- The `$user->ortsverbande` relation (note: no umlaut in method name) is used throughout
- Lehrgang progress is computed inline (query `LehrgangQuestion` count vs `UserQuestionProgress` mastered count) — no helper method exists on User

- [ ] **Step 3: Build and clear caches**

Run: `npm run build && php artisan view:clear && php artisan cache:clear`
Expected: Build succeeds. No errors.

- [ ] **Step 4: Test in browser**

Open `/dashboard` in browser. Verify:
- Header shows greeting + name + level
- Smart Action Card renders with correct priority
- Journey Stepper shows on mobile (horizontal) and desktop (vertical in sidebar)
- Gamification pills show streak, solved count, ranking
- Weekly activity chart renders
- Lehrgänge section shows enrolled courses or empty state
- Quick links visible on mobile, hidden on desktop

- [ ] **Step 5: Commit**

```bash
git add resources/views/dashboard.blade.php
git commit -m "🎨: Dashboard Modulares Redesign"
```

---

## Chunk 3: Statistics Page

### Task 4: StatisticsController erstellen

**Files:**
- Create: `app/Http/Controllers/StatisticsController.php`

- [ ] **Step 1: Create StatisticsController**

```php
<?php

namespace App\Http\Controllers;

use App\Models\ExamStatistic;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use Illuminate\Support\Facades\Cache;

class StatisticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalQuestions = Cache::remember('total_questions_count', 3600, fn () => Question::count());

        // Overall progress
        $solvedTotal = UserQuestionProgress::where('user_id', $user->id)->count();
        $masteredTotal = UserQuestionProgress::where('user_id', $user->id)
            ->where('consecutive_correct', '>=', 3)->count();
        $progressPercent = $totalQuestions > 0 ? round(($solvedTotal / $totalQuestions) * 100) : 0;
        $masteryPercent = $totalQuestions > 0 ? round(($masteredTotal / $totalQuestions) * 100) : 0;

        // Hit rate
        $totalAnswered = QuestionStatistic::where('user_id', $user->id)->count();
        $totalCorrect = QuestionStatistic::where('user_id', $user->id)->where('is_correct', true)->count();
        $hitRate = $totalAnswered > 0 ? round(($totalCorrect / $totalAnswered) * 100) : 0;

        // Section analysis
        $sectionStats = [];
        for ($s = 1; $s <= 10; $s++) {
            $sectionQuestions = Question::where('lernabschnitt', $s)->count();
            $sectionMastered = UserQuestionProgress::where('user_id', $user->id)
                ->whereHas('question', fn ($q) => $q->where('lernabschnitt', $s))
                ->where('consecutive_correct', '>=', 3)->count();
            $sectionAnswered = QuestionStatistic::where('user_id', $user->id)
                ->whereHas('question', fn ($q) => $q->where('lernabschnitt', $s))->count();
            $sectionCorrect = QuestionStatistic::where('user_id', $user->id)
                ->where('is_correct', true)
                ->whereHas('question', fn ($q) => $q->where('lernabschnitt', $s))->count();

            $sectionStats[] = [
                'section' => $s,
                'total' => $sectionQuestions,
                'mastered' => $sectionMastered,
                'percent' => $sectionQuestions > 0 ? round(($sectionMastered / $sectionQuestions) * 100) : 0,
                'hit_rate' => $sectionAnswered > 0 ? round(($sectionCorrect / $sectionAnswered) * 100) : 0,
            ];
        }

        // Activity (last 30 days)
        $activity = QuestionStatistic::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(is_correct) as correct')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Weekly activity (last 7 days)
        $weeklyActivity = QuestionStatistic::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(is_correct) as correct')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Exam history
        $examHistory = ExamStatistic::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Spaced repetition stats
        $srStats = app(\App\Services\SpacedRepetitionService::class)->getStats($user->id);

        // SR interval distribution
        $intervalDistribution = UserQuestionProgress::where('user_id', $user->id)
            ->where('review_interval', '>', 0)
            ->selectRaw('review_interval, COUNT(*) as count')
            ->groupBy('review_interval')
            ->orderBy('review_interval')
            ->get();

        return view('statistics', compact(
            'totalQuestions', 'solvedTotal', 'masteredTotal',
            'progressPercent', 'masteryPercent', 'hitRate',
            'sectionStats', 'activity', 'weeklyActivity',
            'examHistory', 'srStats', 'intervalDistribution'
        ));
    }
}
```

- [ ] **Step 2: Add route**

In `routes/web.php`, after the dashboard route (around line 107), add:

```php
Route::get('/statistics', [\App\Http\Controllers\StatisticsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('statistics');
```

- [ ] **Step 3: Verify route registers**

Run: `php artisan route:list --path=statistics`
Expected: Route listed successfully.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/StatisticsController.php routes/web.php
git commit -m "✨: StatisticsController und Route"
```

---

### Task 5: Statistics View erstellen

**Files:**
- Create: `resources/views/statistics.blade.php`

- [ ] **Step 1: Create statistics.blade.php**

```blade
@extends('layouts.app')
@section('title', 'Statistiken')

@section('content')
<div class="dash-container">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold" style="color: var(--text-primary);">Statistiken</h1>
        <p class="text-sm" style="color: var(--text-muted);">Dein detaillierter Lernfortschritt</p>
    </div>

    {{-- Overview Stats --}}
    <div class="flex gap-3 mb-6">
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ $progressPercent }}%</div>
            <div class="gami-pill__label">Fortschritt</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value">{{ $solvedTotal }}/{{ $totalQuestions }}</div>
            <div class="gami-pill__label">Gelöst</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ $hitRate }}%</div>
            <div class="gami-pill__label">Trefferquote</div>
        </div>
    </div>

    {{-- Desktop 2-column --}}
    <div class="dash-grid">
        {{-- Main: Section Analysis --}}
        <div class="space-y-4">
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--text-muted);">
                    Lernabschnitte
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($sectionStats as $section)
                        <a href="{{ route('practice.section', $section['section']) }}"
                           class="stat-section-card block" style="text-decoration: none;">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold" style="color: var(--text-primary);">
                                    Abschnitt {{ $section['section'] }}
                                </span>
                                <span class="text-xs font-bold" style="color: {{ $section['percent'] > 80 ? 'var(--success)' : ($section['percent'] >= 50 ? '#5b9aff' : 'var(--error)') }};">
                                    {{ $section['percent'] }}%
                                </span>
                            </div>
                            <div class="stat-section-card__bar">
                                <div class="h-full rounded-sm transition-all {{ $section['percent'] > 80 ? 'stat-section-card__fill--green' : ($section['percent'] >= 50 ? 'stat-section-card__fill--blue' : 'stat-section-card__fill--red') }}"
                                     style="width: {{ $section['percent'] }}%;"></div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-xs" style="color: var(--text-muted);">
                                    {{ $section['mastered'] }}/{{ $section['total'] }} gemeistert
                                </span>
                                <span class="text-xs" style="color: var(--text-muted);">
                                    {{ $section['hit_rate'] }}% Trefferquote
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Activity Chart --}}
            <div class="glass p-4" style="border-radius: 0.75rem;" x-data="{ view: 'week' }">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs uppercase tracking-wider" style="color: var(--text-muted);">Aktivität</span>
                    <div class="flex gap-1">
                        <button @click="view = 'week'"
                                :class="view === 'week' ? 'font-semibold' : ''"
                                class="text-xs px-2 py-1 rounded"
                                :style="view === 'week' ? 'color: #5b9aff; background: rgba(0,85,204,0.15);' : 'color: var(--text-muted);'">
                            Woche
                        </button>
                        <button @click="view = 'month'"
                                :class="view === 'month' ? 'font-semibold' : ''"
                                class="text-xs px-2 py-1 rounded"
                                :style="view === 'month' ? 'color: #5b9aff; background: rgba(0,85,204,0.15);' : 'color: var(--text-muted);'">
                            Monat
                        </button>
                    </div>
                </div>

                {{-- Weekly Barchart --}}
                <div x-show="view === 'week'">
                    @php
                        $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
                        $maxWeekCount = $weeklyActivity->max('count') ?: 1;
                    @endphp
                    <div class="activity-bar" style="height: 120px;">
                        @for($d = 0; $d < 7; $d++)
                            @php
                                $date = now()->startOfWeek()->addDays($d)->format('Y-m-d');
                                $dayData = $weeklyActivity->firstWhere('date', $date);
                                $count = $dayData->count ?? 0;
                                $heightPct = $maxWeekCount > 0 ? max(($count / $maxWeekCount) * 100, 3) : 3;
                                $isToday = $date === now()->format('Y-m-d');
                            @endphp
                            <div class="activity-bar__col">
                                <span class="activity-bar__count">{{ $count ?: '' }}</span>
                                <div class="activity-bar__fill {{ $isToday ? 'activity-bar__fill--today' : ($count === 0 ? 'activity-bar__fill--empty' : '') }}"
                                     style="height: {{ $count > 0 ? $heightPct : 8 }}%;"></div>
                                <span class="activity-bar__day {{ $isToday ? 'activity-bar__day--today' : '' }}">
                                    {{ $days[$d] }}
                                </span>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Monthly Heatmap --}}
                <div x-show="view === 'month'" x-cloak>
                    @php
                        $maxDayCount = $activity->max('count') ?: 1;
                    @endphp
                    <div class="heatmap-grid">
                        @for($d = 29; $d >= 0; $d--)
                            @php
                                $date = now()->subDays($d)->format('Y-m-d');
                                $dayData = $activity->get($date);
                                $count = $dayData->count ?? 0;
                                $level = $count === 0 ? '' : ($count <= $maxDayCount * 0.25 ? 'heatmap-cell--l1' : ($count <= $maxDayCount * 0.5 ? 'heatmap-cell--l2' : ($count <= $maxDayCount * 0.75 ? 'heatmap-cell--l3' : 'heatmap-cell--l4')));
                            @endphp
                            <div class="heatmap-cell {{ $level }}" title="{{ $date }}: {{ $count }} Fragen"></div>
                        @endfor
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-xs" style="color: var(--text-muted);">{{ now()->subDays(29)->format('d.m.') }}</span>
                        <span class="text-xs" style="color: var(--text-muted);">{{ now()->format('d.m.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Exam History --}}
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--text-muted);">
                    Prüfungshistorie
                </div>
                @if($examHistory->isEmpty())
                    <p class="text-sm text-center py-4" style="color: var(--text-muted);">
                        Noch keine Prüfungen abgelegt
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach($examHistory->take(10) as $index => $exam)
                            @php
                                $pct = round(($exam->correct_answers / 40) * 100);
                                $prevExam = $examHistory->get($index + 1);
                                $prevPct = $prevExam ? round(($prevExam->correct_answers / 40) * 100) : null;
                            @endphp
                            <div class="flex items-center gap-3 p-2 rounded-lg" style="background: var(--glass-bg);">
                                <span class="text-xs" style="color: var(--text-muted); min-width: 60px;">
                                    {{ $exam->created_at->format('d.m.Y') }}
                                </span>
                                <div class="flex-1 h-1 rounded-full" style="background: rgba(255,255,255,0.1);">
                                    <div class="h-full rounded-full" style="background: {{ $exam->is_passed ? 'var(--success)' : 'var(--error)' }}; width: {{ $pct }}%;"></div>
                                </div>
                                <span class="text-sm font-bold" style="color: {{ $exam->is_passed ? 'var(--success)' : 'var(--error)' }}; min-width: 35px;">
                                    {{ $pct }}%
                                </span>
                                @if($prevPct !== null)
                                    <span class="text-xs" style="color: {{ $pct > $prevPct ? 'var(--success)' : ($pct < $prevPct ? 'var(--error)' : 'var(--text-muted)') }};">
                                        @if($pct > $prevPct) ↑ @elseif($pct < $prevPct) ↓ @else → @endif
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Spaced Repetition --}}
            <div class="glass p-4" style="border-radius: 0.75rem;">
                <div class="text-xs uppercase tracking-wider mb-3" style="color: var(--text-muted);">
                    Spaced Repetition
                </div>
                <div class="flex gap-3 mb-3">
                    <div class="text-center flex-1">
                        <div class="text-xl font-bold" style="color: #5b9aff;">{{ $srStats['due_now'] }}</div>
                        <div class="text-xs" style="color: var(--text-muted);">Heute fällig</div>
                    </div>
                    <div class="text-center flex-1">
                        <div class="text-xl font-bold" style="color: var(--text-primary);">{{ $srStats['due_this_week'] }}</div>
                        <div class="text-xs" style="color: var(--text-muted);">Diese Woche</div>
                    </div>
                    <div class="text-center flex-1">
                        <div class="text-xl font-bold" style="color: var(--text-primary);">{{ $masteryPercent }}%</div>
                        <div class="text-xs" style="color: var(--text-muted);">Gemeistert</div>
                    </div>
                </div>
                @if($intervalDistribution->isNotEmpty())
                    <div class="text-xs uppercase tracking-wider mb-2 mt-4" style="color: var(--text-muted);">
                        Review-Intervalle
                    </div>
                    <div class="space-y-1">
                        @foreach($intervalDistribution->take(5) as $interval)
                            <div class="flex justify-between text-xs">
                                <span style="color: var(--text-secondary);">{{ $interval->review_interval }} Tage</span>
                                <span style="color: var(--text-muted);">{{ $interval->count }} Fragen</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 2: Add navigation link to sidebar**

In `resources/views/layouts/app.blade.php`, find the sidebar navigation and add a "Statistiken" link after the Dashboard link. Look for the pattern of nav links and add:

```blade
<a href="{{ route('statistics') }}"
   class="nav-link {{ request()->routeIs('statistics') ? 'active' : '' }}">
    <i class="bi bi-bar-chart"></i>
    <span>Statistiken</span>
</a>
```

- [ ] **Step 3: Build and clear caches**

Run: `npm run build && php artisan view:clear && php artisan cache:clear`
Expected: Build succeeds.

- [ ] **Step 4: Test in browser**

Open `/statistics`. Verify:
- Overview stats show at top
- 10 section cards render with color coding
- Activity chart shows week/month toggle
- Exam history shows or shows empty state
- Spaced repetition stats render

- [ ] **Step 5: Commit**

```bash
git add resources/views/statistics.blade.php resources/views/layouts/app.blade.php
git commit -m "✨: Statistik-Seite View"
```

---

## Chunk 4: Polish & Integration

### Task 6: Dark/Light Mode für Dashboard verifizieren

**Files:**
- Modify: `resources/css/app.css` (add any missing `.light-mode` overrides)

- [ ] **Step 1: Test light mode in browser**

Toggle to light mode. Check all dashboard and statistics page elements for readability.

- [ ] **Step 2: Add missing `.light-mode` overrides**

After testing, add any needed light mode overrides to `app.css`. Common issues:
- Progress bar backgrounds too transparent
- Text contrast insufficient
- Glass borders not visible enough

The spec's light mode values:
- Base background: `#f0f2f5`
- Card background: `rgba(255,255,255,0.7)` with blue shadows
- Text primary: `#111`, secondary: `#666`
- Gold accent: `#d97706`

Key overrides to add if missing:

```css
.light-mode .smart-action {
    box-shadow: 0 4px 24px rgba(0, 51, 127, 0.2);
}

.light-mode .gami-pill {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(0, 51, 127, 0.1);
}

.light-mode .stat-section-card {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(0, 51, 127, 0.1);
}

.light-mode .stat-section-card__bar {
    background: rgba(0, 0, 0, 0.08);
}
```

- [ ] **Step 3: Build**

Run: `npm run build && php artisan view:clear && php artisan cache:clear`

- [ ] **Step 4: Commit**

```bash
git add resources/css/app.css
git commit -m "🎨: Dashboard Light-Mode Fixes"
```

---

### Task 7: Responsive Testing & Mobile-Fixes

- [ ] **Step 1: Test at mobile breakpoint (375px width)**

Open browser DevTools, set to 375px width. Verify:
- Smart Action Card readable, button tappable
- Journey Stepper horizontal, all 3 steps visible
- Gamification pills don't overflow
- Activity bars visible
- Quick links row visible
- Lehrgänge cards stack vertically

- [ ] **Step 2: Test at tablet breakpoint (768px)**

Verify: Layout still single-column, gamification pills side by side.

- [ ] **Step 3: Test at desktop (1280px)**

Verify: 2-column layout, Journey in sidebar, Quick Links hidden.

- [ ] **Step 4: Fix any responsive issues found**

Apply Tailwind responsive utilities or CSS fixes as needed.

- [ ] **Step 5: Commit if changes were made**

```bash
git add resources/views/dashboard.blade.php resources/views/statistics.blade.php resources/css/app.css
git commit -m "🎨: Dashboard Responsive Fixes"
```

---

### Task 8: Final Integration — Preserve Existing Features

**Files:**
- Modify: `resources/views/dashboard.blade.php`

- [ ] **Step 1: Verify leaderboard modal is preserved**

Read the old dashboard (from git history if needed: `git show HEAD~4:resources/views/dashboard.blade.php`) and ensure the leaderboard modal HTML + JS is copied into the new dashboard.

- [ ] **Step 2: Verify onboarding tour reference**

If `@include('components.onboarding-tour')` was in the old dashboard, add it to the new one.

- [ ] **Step 3: Verify gamification notifications work**

Navigate around, solve a question, return to dashboard. Ensure `@include('components.gamification-notifications')` is loaded via the layout (it is — in `layouts/app.blade.php`).

- [ ] **Step 4: Final build and cache clear**

Run: `npm run build && php artisan view:clear && php artisan cache:clear`

- [ ] **Step 5: Commit**

```bash
git add resources/views/dashboard.blade.php
git commit -m "🎨: Dashboard Integration Fixes"
```
