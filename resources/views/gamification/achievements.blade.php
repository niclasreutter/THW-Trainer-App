@extends('layouts.app')
@section('title', 'Achievements - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Achievement Items ─── */
    .ach-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0.5rem;
        transition: background 150ms ease;
        border-radius: 0.375rem;
    }

    .ach-item:hover { background: rgba(255,255,255,0.03); }
    html.light-mode .ach-item:hover { background: rgba(0,0,0,0.03); }

    .ach-item + .ach-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .ach-item + .ach-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .ach-item--locked { opacity: 0.45; }

    .ach-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .ach-icon--unlocked {
        background: rgba(0,85,204,0.12);
        color: #5b9aff;
    }

    html.light-mode .ach-icon--unlocked {
        background: rgba(0,51,127,0.08);
        color: #00337F;
    }

    .ach-icon--locked {
        background: rgba(255,255,255,0.04);
        color: var(--text-muted);
    }

    html.light-mode .ach-icon--locked {
        background: rgba(0,0,0,0.04);
    }

    .ach-title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .ach-item--locked .ach-title {
        color: var(--text-secondary);
    }

    .ach-desc {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    .ach-status {
        font-size: 0.625rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        margin-left: auto;
        flex-shrink: 0;
    }

    .ach-status--unlocked {
        background: rgba(0,85,204,0.12);
        color: #5b9aff;
    }

    html.light-mode .ach-status--unlocked {
        background: rgba(0,51,127,0.08);
        color: #00337F;
    }

    .ach-status--locked {
        background: rgba(255,255,255,0.04);
        color: var(--text-muted);
    }

    html.light-mode .ach-status--locked {
        background: rgba(0,0,0,0.04);
    }

    /* ─── Category Card Header ─── */
    .ach-cat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .ach-cat-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
    }

    .ach-cat-count {
        font-size: 0.625rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Top Row Grid ─── */
    .ach-top-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    @media (max-width: 600px) {
        .ach-top-grid { grid-template-columns: 1fr; }
    }

    /* ─── Level Ring ─── */
    .ach-ring {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 50%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ach-ring svg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .ach-ring__track {
        fill: none;
        stroke: rgba(255,255,255,0.06);
        stroke-width: 3;
    }

    html.light-mode .ach-ring__track { stroke: rgba(0,0,0,0.06); }

    .ach-ring__fill {
        fill: none;
        stroke: var(--gold-start);
        stroke-width: 3;
        stroke-linecap: round;
        transition: stroke-dashoffset 1s ease-out;
    }

    .ach-ring__text {
        font-size: 0.8125rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        color: var(--gold-start);
        position: relative;
        z-index: 1;
    }

    /* ─── Daily Icon ─── */
    .ach-daily-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        flex-shrink: 0;
    }

    .ach-daily-icon--active {
        background: rgba(0,85,204,0.12);
        color: #5b9aff;
    }

    html.light-mode .ach-daily-icon--active {
        background: rgba(0,51,127,0.08);
        color: #00337F;
    }

    .ach-daily-icon--done {
        background: rgba(34,197,94,0.12);
        color: #22c55e;
    }

    html.light-mode .ach-daily-icon--done {
        background: rgba(34,197,94,0.1);
    }

    /* ─── Blue Progress Fill ─── */
    .progress-fill-blue {
        height: 100%;
        background: linear-gradient(90deg, #0055cc, #5b9aff);
        border-radius: 3px;
        box-shadow: 0 0 8px rgba(0,85,204,0.3);
        transition: width 0.8s ease-out;
    }

    html.light-mode .progress-fill-blue {
        background: linear-gradient(90deg, #00337F, #004db3);
    }

    /* ─── Filter Pills ─── */
    .ach-filter-pills {
        display: flex;
        gap: 0.375rem;
        overflow-x: auto;
    }

    .ach-filter-pill {
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 0.3rem 0.75rem;
        border-radius: 2rem;
        border: 1px solid var(--glass-border);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 150ms;
        font-family: 'IBM Plex Mono', monospace;
        white-space: nowrap;
    }

    .ach-filter-pill:hover {
        border-color: rgba(91,154,255,0.3);
        color: var(--text-secondary);
    }

    .ach-filter-pill.active {
        background: rgba(91,154,255,0.12);
        color: #5b9aff;
        border-color: rgba(91,154,255,0.3);
    }

    html.light-mode .ach-filter-pill.active {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.2);
    }

    /* ─── Stagger Animation ─── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dash-container > .space-y-4 > * {
        animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    .dash-container > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    .dash-container > .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
    .dash-container > .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }
    .dash-container > .space-y-4 > *:nth-child(6) { animation-delay: 0.23s; }
    .dash-container > .space-y-4 > *:nth-child(7) { animation-delay: 0.27s; }
    .dash-container > .space-y-4 > *:nth-child(8) { animation-delay: 0.31s; }
    .dash-container > .space-y-4 > *:nth-child(9) { animation-delay: 0.35s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }
</style>
@endpush

@section('content')
@php
    $gamificationService = new \App\Services\GamificationService();
    $achievements = $gamificationService->getUserAchievements(Auth::user());
    $user = Auth::user();
    $nextLevelPoints = $gamificationService->getNextLevelPoints($user);
    $levelProgress = $gamificationService->getLevelProgress($user);

    $unlockedCount = collect($achievements)->where('unlocked', true)->count();
    $totalCount = count($achievements);

    // Achievements nach Kategorien gruppieren
    $categories = [
        'Streak' => ['streak_3', 'streak_7', 'streak_30'],
        'Fragen' => ['first_question', 'questions_50', 'questions_100', 'questions_500'],
        'Prüfungen' => ['exam_first', 'exam_perfect'],
        'Spezial' => ['speed_demon', 'section_master'],
        'Level' => ['level_5', 'level_10', 'level_15', 'level_20'],
    ];

    $achievementsByKey = collect($achievements)->keyBy('key');

    // SVG Ring Berechnung (Umfang = 2 * PI * r, r=24)
    $circumference = 150.8;
    $ringOffset = $circumference - ($circumference * (max(0, min(100, $levelProgress)) / 100));

    $dailySolved = $user->daily_questions_solved ?? 0;
    $dailyDone = $dailySolved >= 20;
    $dailyPercent = min(100, ($dailySolved / 20) * 100);
@endphp

<div class="dash-container" x-data="{ filter: 'all' }">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Gamification</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Achievements</h1>
        <p class="text-sm" style="color: var(--text-muted);">Deine Erfolge und täglichen Herausforderungen</p>
    </div>

    {{-- ── Gami Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--gold">{{ $user->level }}</div>
            <div class="gami-pill__label">Level</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ number_format($user->points) }}</div>
            <div class="gami-pill__label">Punkte</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--warning);-webkit-text-fill-color:var(--warning);">{{ $user->streak_days }}</div>
            <div class="gami-pill__label">Tage Streak</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ $unlockedCount }}/{{ $totalCount }}</div>
            <div class="gami-pill__label">Achievements</div>
        </div>
    </div>

    <div class="space-y-4">

        {{-- ── Top Row: Level + Daily ── --}}
        <div class="ach-top-grid">
            {{-- Level Progress (Gold = konsistent mit Dashboard) --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="ach-ring">
                        <svg viewBox="0 0 56 56">
                            <circle class="ach-ring__track" cx="28" cy="28" r="24"></circle>
                            <circle class="ach-ring__fill" cx="28" cy="28" r="24"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $ringOffset }}"></circle>
                        </svg>
                        <span class="ach-ring__text">Lv.{{ $user->level }}</span>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);">Level-Fortschritt</div>
                        <div style="font-size:0.625rem;color:var(--text-muted);margin-top:0.15rem;">
                            @if($nextLevelPoints > 0)
                                Noch {{ number_format($nextLevelPoints) }} Punkte bis Level {{ $user->level + 1 }}
                            @else
                                Max Level erreicht
                            @endif
                        </div>
                        @if($nextLevelPoints > 0)
                            <div class="progress-glass" style="margin-top:0.5rem;">
                                <div class="progress-fill-gold" style="width: {{ max(0, min(100, $levelProgress)) }}%"></div>
                            </div>
                            <div style="font-size:0.5625rem;color:var(--text-muted);margin-top:0.25rem;">{{ number_format($levelProgress, 1) }}% Fortschritt</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Daily Challenge (Blau) --}}
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="ach-daily-icon {{ $dailyDone ? 'ach-daily-icon--done' : 'ach-daily-icon--active' }}">
                        <i class="bi {{ $dailyDone ? 'bi-check-circle-fill' : 'bi-lightning-fill' }}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);">Tagesaufgabe</div>
                        <div style="font-size:0.625rem;color:var(--text-muted);margin-top:0.15rem;">Beantworte 20 Fragen heute</div>
                        <div style="font-size:1.125rem;font-weight:800;font-family:'Barlow Condensed',sans-serif;color:var(--text-primary);margin-top:0.25rem;">{{ $dailySolved }}/20</div>
                        @if(!$dailyDone)
                            <div class="progress-glass" style="margin-top:0.35rem;">
                                <div class="progress-fill-blue" style="width: {{ $dailyPercent }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Filter Pills ── --}}
        <div>
            <div class="ach-filter-pills">
                <button class="ach-filter-pill" :class="{ 'active': filter === 'all' }" @click="filter = 'all'">Alle</button>
                <button class="ach-filter-pill" :class="{ 'active': filter === 'unlocked' }" @click="filter = 'unlocked'">Freigeschaltet</button>
                <button class="ach-filter-pill" :class="{ 'active': filter === 'locked' }" @click="filter = 'locked'">Ausstehend</button>
            </div>
        </div>

        {{-- ── Achievement Cards per Category ── --}}
        @foreach($categories as $categoryName => $keys)
            @php
                $catAchievements = collect($keys)->map(fn($k) => $achievementsByKey->get($k))->filter();
                $catUnlocked = $catAchievements->where('unlocked', true)->count();
                $catTotal = $catAchievements->count();
                $hasUnlocked = $catUnlocked > 0;
                $hasLocked = $catTotal - $catUnlocked > 0;
            @endphp
            <div class="glass p-4" style="border-radius:0.75rem;"
                 x-show="filter === 'all' || (filter === 'unlocked' && {{ $hasUnlocked ? 'true' : 'false' }}) || (filter === 'locked' && {{ $hasLocked ? 'true' : 'false' }})"
                 x-transition.opacity>
                <div class="ach-cat-header">
                    <span class="ach-cat-label">{{ $categoryName }}</span>
                    <span class="ach-cat-count">{{ $catUnlocked }}/{{ $catTotal }}</span>
                </div>

                @foreach($catAchievements as $achievement)
                    <div class="ach-item {{ !$achievement['unlocked'] ? 'ach-item--locked' : '' }}"
                         x-show="filter === 'all' || (filter === 'unlocked' && {{ $achievement['unlocked'] ? 'true' : 'false' }}) || (filter === 'locked' && {{ !$achievement['unlocked'] ? 'true' : 'false' }})"
                         x-transition.opacity>
                        <div class="ach-icon {{ $achievement['unlocked'] ? 'ach-icon--unlocked' : 'ach-icon--locked' }}">
                            @if(str_starts_with($achievement['icon'], 'bi-'))
                                <i class="bi {{ $achievement['icon'] }}"></i>
                            @else
                                {{ $achievement['icon'] }}
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="ach-title">{{ $achievement['title'] }}</div>
                            <div class="ach-desc">{{ $achievement['description'] }}</div>
                        </div>
                        @if($achievement['unlocked'])
                            <span class="ach-status ach-status--unlocked">Erhalten</span>
                        @else
                            <span class="ach-status ach-status--locked"><i class="bi bi-lock-fill"></i></span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach

    </div>
</div>
@endsection
