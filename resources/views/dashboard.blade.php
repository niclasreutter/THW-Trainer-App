@extends('layouts.app')

@section('title', 'Dashboard - Dein Lernfortschritt')
@section('description', 'Dein persönliches THW-Trainer Dashboard: Verfolge deinen Lernfortschritt, wiederhole falsche Fragen und bereite dich optimal auf deine THW-Prüfung vor.')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
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

    /* ─── Header ────────────────────────────────────── */
    .dash-header {
        margin-bottom: 1.25rem;
    }

    .dash-greeting {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .dash-username {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
        margin-bottom: 0.25rem;
        font-family: 'Barlow Condensed', sans-serif;
    }

    .dash-level-line {
        font-size: 0.8125rem;
        color: #5b9aff;
        font-weight: 600;
    }

    html.light-mode .dash-level-line { color: #00337F; }

    /* ─── Journey Stepper (Desktop Sidebar) ─────────── */
    .journey-sidebar-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .journey-step-detail {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
        line-height: 1.3;
    }

    .journey-step-bar {
        height: 3px;
        background: rgba(255,255,255,0.08);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 0.4rem;
        width: 100%;
    }

    html.light-mode .journey-step-bar {
        background: rgba(0,0,0,0.08);
    }

    .journey-step-bar-fill {
        height: 100%;
        background: #0055cc;
        border-radius: 2px;
    }

    /* ─── Section Labels ─────────────────────────────── */
    .section-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Lehrgang Card ─────────────────────────────── */
    .lg-card {
        display: block;
        padding: 0.875rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .lg-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,51,127,0.12);
        text-decoration: none;
    }

    .lg-card-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lg-bar {
        height: 4px;
        background: rgba(0,51,127,0.12);
        border-radius: 2px;
        overflow: hidden;
        margin: 0.35rem 0;
    }

    html.light-mode .lg-bar { background: rgba(0,51,127,0.1); }

    .lg-bar-fill {
        height: 100%;
        background: #0055cc;
        border-radius: 2px;
    }

    .lg-bar-fill--done { background: #22c55e; }

    .lg-pct {
        font-size: 0.6875rem;
        font-weight: 700;
        color: #5b9aff;
    }

    html.light-mode .lg-pct { color: #00337F; }

    /* ─── Exam Countdown (Sidebar) ──────────────────── */
    .countdown-widget {
        padding: 1rem;
        border-radius: 0.75rem;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
    }

    .countdown-days-big {
        font-size: 2.5rem;
        font-weight: 800;
        color: #5b9aff;
        line-height: 1;
        font-family: 'Barlow Condensed', sans-serif;
        letter-spacing: -0.03em;
    }

    html.light-mode .countdown-days-big { color: #00337F; }

    .countdown-label-small {
        font-size: 0.625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    .countdown-mini-bar {
        height: 4px;
        background: rgba(255,255,255,0.08);
        border-radius: 2px;
        overflow: hidden;
        margin: 0.4rem 0 0.25rem;
    }

    html.light-mode .countdown-mini-bar { background: rgba(0,0,0,0.08); }

    .countdown-mini-fill {
        height: 100%;
        border-radius: 2px;
        transition: width 0.5s ease-out;
    }

    /* ─── Smart Action: Live ───────────────────────── */
    .smart-action--live {
        background: linear-gradient(135deg, #064e3b, #065f46);
    }

    .live-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
        margin-right: 0.25rem;
        vertical-align: middle;
        animation: live-pulse 1.5s ease-in-out infinite;
    }

    @keyframes live-pulse {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6); }
        50% { opacity: 0.7; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0); }
    }

    /* ─── Streak Warning ───────────────────────────── */
    .streak-warning {
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(245, 158, 11, 0.25);
        background: rgba(245, 158, 11, 0.08);
        animation: streak-glow 2s ease-in-out infinite;
    }

    html.light-mode .streak-warning {
        background: rgba(245, 158, 11, 0.1);
        border-color: rgba(245, 158, 11, 0.3);
    }

    @keyframes streak-glow {
        0%, 100% { border-color: rgba(245, 158, 11, 0.25); }
        50% { border-color: rgba(245, 158, 11, 0.5); }
    }

    /* ─── Streak Daily Progress ────────────────────── */
    .streak-progress {
        width: 100%;
        height: 3px;
        background: rgba(255,255,255,0.08);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 0.35rem;
    }

    html.light-mode .streak-progress { background: rgba(0,0,0,0.08); }

    .streak-progress__fill {
        height: 100%;
        background: #f59e0b;
        border-radius: 2px;
        transition: width 0.5s ease-out;
    }

    .streak-progress__fill--done {
        background: #22c55e;
    }

    .streak-progress__text {
        font-size: 0.5rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.02em;
    }

    .streak-progress__text--done {
        color: #22c55e;
    }

    /* ─── Lootbox Nudge ────────────────────────────── */
    .lootbox-nudge {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(251, 191, 36, 0.2);
        background: rgba(251, 191, 36, 0.06);
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .lootbox-nudge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.12);
        text-decoration: none;
    }

    .lootbox-nudge__icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #1a1a2e;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: lootbox-shimmer 3s ease-in-out infinite;
    }

    @keyframes lootbox-shimmer {
        0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0); }
        50% { box-shadow: 0 0 12px 2px rgba(251, 191, 36, 0.3); }
    }

    .lootbox-nudge__title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #fbbf24;
    }

    html.light-mode .lootbox-nudge__title { color: #d97706; }

    .lootbox-nudge__desc {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── League Badge ─────────────────────────────── */
    .league-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--league-color);
        background: color-mix(in srgb, var(--league-color) 12%, transparent);
        border: 1px solid color-mix(in srgb, var(--league-color) 25%, transparent);
        border-radius: 0.375rem;
        padding: 0.2rem 0.5rem;
        line-height: 1;
        white-space: nowrap;
    }

    /* ─── XP Progress Bar ──────────────────────────── */
    .xp-bar {
        height: 4px;
        background: rgba(255,255,255,0.08);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 0.5rem;
        max-width: 200px;
    }

    html.light-mode .xp-bar { background: rgba(0,0,0,0.08); }

    .xp-bar__fill {
        height: 100%;
        background: linear-gradient(90deg, #5b9aff, #0055cc);
        border-radius: 2px;
        transition: width 0.6s ease-out;
    }

    .xp-bar__hint {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Spaced Repetition Nudge ──────────────────── */
    .sr-nudge {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .sr-nudge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,51,127,0.1);
        text-decoration: none;
    }

    .sr-nudge__badge {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #0055cc, #5b9aff);
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Barlow Condensed', sans-serif;
    }

    .sr-nudge__title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .sr-nudge__desc {
        font-size: 0.625rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── Stagger animation ─────────────────────────── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dash-container > * {
        animation: dash-rise 0.45s cubic-bezier(0.22,1,0.36,1) both;
    }

    .dash-container > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > *:nth-child(3) { animation-delay: 0.11s; }
    .dash-container > *:nth-child(4) { animation-delay: 0.15s; }

    /* Activity bars animate in */
    @keyframes bar-rise {
        from { transform: scaleY(0); transform-origin: bottom; opacity: 0; }
        to   { transform: scaleY(1); transform-origin: bottom; opacity: 1; }
    }

    .activity-bar__fill {
        animation: bar-rise 0.5s cubic-bezier(0.22,1,0.36,1) both;
    }

    .activity-bar__col:nth-child(1) .activity-bar__fill { animation-delay: 0.3s; }
    .activity-bar__col:nth-child(2) .activity-bar__fill { animation-delay: 0.35s; }
    .activity-bar__col:nth-child(3) .activity-bar__fill { animation-delay: 0.4s; }
    .activity-bar__col:nth-child(4) .activity-bar__fill { animation-delay: 0.45s; }
    .activity-bar__col:nth-child(5) .activity-bar__fill { animation-delay: 0.5s; }
    .activity-bar__col:nth-child(6) .activity-bar__fill { animation-delay: 0.55s; }
    .activity-bar__col:nth-child(7) .activity-bar__fill { animation-delay: 0.6s; }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();

    /* ── Time-based greeting ─────────────────────── */
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Guten Morgen' : ($hour < 18 ? 'Guten Tag' : 'Guten Abend');

    /* ── League info ─────────────────────────────── */
    $userLeague = $user->league ?? 'bronze';
    $leagueInfo = \App\Services\LeagueService::getLeagueInfo($userLeague);

    /* ── Ausbilder check ─────────────────────────── */
    $userOV = $user->ortsverbände->first();
    $isAusbilder = false;
    if ($userOV) {
        $memberPivot = $userOV->members()->where('user_id', $user->id)->first();
        $isAusbilder = $memberPivot && $memberPivot->pivot->role === 'ausbildungsbeauftragter';
    }

    /* ── OV Card Data ───────────────────────────── */
    $ovCardData = null;
    if ($userOV) {
        if ($isAusbilder) {
            $ovCardData = [
                'type' => 'ausbilder',
                'stats' => $userOV->getAverageStats(),
            ];
        } else {
            $ovEnrolledLernpools = $user->enrolledLernpools()
                ->where('ortsverband_id', $userOV->id)
                ->take(3)
                ->get()
                ->map(fn($lp) => [
                    'id' => $lp->id,
                    'name' => $lp->name,
                    'progress' => $lp->enrollments()
                        ->where('user_id', $user->id)
                        ->first()?->getProgress() ?? 0,
                ]);

            $ovTotalEnrolled = $user->enrolledLernpools()
                ->where('ortsverband_id', $userOV->id)
                ->count();

            $ovRanking = null;
            $ovUserRank = null;
            if ($userOV->ranking_visible) {
                $ovMemberProgress = $userOV->getMemberProgress();
                $ovSorted = collect($ovMemberProgress)->sortByDesc('points')->values();
                $ovRanking = $ovSorted->take(3);
                $ovUserRank = $ovSorted->search(fn($m) => $m['user']->id === $user->id);
                $ovUserRank = $ovUserRank !== false ? $ovUserRank + 1 : null;
            }

            $ovCardData = [
                'type' => 'member',
                'lernpools' => $ovEnrolledLernpools,
                'totalEnrolled' => $ovTotalEnrolled,
                'ranking' => $ovRanking,
                'userRank' => $ovUserRank,
            ];
        }
    }

    /* ── Weekly activity data ────────────────────── */
    $weekStart = \Carbon\Carbon::now()->startOfWeek();
    $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
    $maxCount = max((int) ($weeklyActivity->max('count') ?? 0), 1);
@endphp

<div class="dash-container">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="dash-header">
        <div class="dash-greeting">{{ $greeting }}</div>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            <div class="dash-username">{{ $user->name }}</div>
            @if($user->leaderboard_consent)
            <span class="league-badge" style="--league-color:{{ $leagueInfo['color'] }};">
                <i class="{{ $leagueInfo['icon'] }}" style="font-size:0.55rem;"></i>
                {{ $leagueInfo['name'] }}
            </span>
            @endif
        </div>
        <div class="dash-level-line">Level {{ $user->level ?? 1 }} &middot; {{ number_format($user->points ?? 0) }} Punkte</div>
        @if($nextLevelPoints > 0)
        <div class="xp-bar">
            <div class="xp-bar__fill" style="width:{{ $levelProgress }}%;"></div>
        </div>
        <div class="xp-bar__hint">noch {{ number_format($nextLevelPoints) }} XP bis Level {{ ($user->level ?? 1) + 1 }}</div>
        @endif
    </div>

    {{-- ── Main grid (desktop: 2 cols) ───────────────── --}}
    <div class="dash-grid">

        {{-- ═══ MAIN COLUMN ═══════════════════════════════ --}}
        <div class="space-y-4">

            {{-- 1. Smart Action Card --}}
            @if($smartAction['type'] === 'info')
            <div class="smart-action smart-action--info" data-tour-step="practice">
                <div class="smart-action__label">{{ $smartAction['label'] }}</div>
                <div class="smart-action__title">{{ $smartAction['title'] }}</div>
                <div class="smart-action__desc">{{ $smartAction['desc'] }}</div>
                <span class="smart-action__status">
                    <i class="bi bi-check-circle"></i>
                    Komm später wieder
                </span>
            </div>
            @else
            <a href="{{ $smartAction['route'] }}"
               class="smart-action {{ session('error') ? 'smart-action--error' : ($smartAction['type'] === 'urgent' ? 'smart-action--urgent' : ($smartAction['type'] === 'live' ? 'smart-action--live' : '')) }}"
               data-tour-step="practice"
               style="display:block;text-decoration:none;">
                <div class="smart-action__label">
                    @if(session('error'))
                        Nicht möglich
                    @elseif($smartAction['type'] === 'live')
                        <span class="live-dot"></span>
                        {{ $smartAction['label'] }}
                    @else
                        {{ $smartAction['label'] }}
                    @endif
                </div>
                <div class="smart-action__title">
                    {{ session('error') ?? $smartAction['title'] }}
                </div>
                <div class="smart-action__desc">
                    {{ session('error') ? $smartAction['desc'] : $smartAction['desc'] }}
                </div>
                <span class="smart-action__btn">
                    {{ session('error') ? 'Jetzt weiterlernen' : $smartAction['btn'] }}
                    <i class="bi bi-arrow-right"></i>
                </span>
                @if($examCountdown && !session('error'))
                    <div class="smart-action__countdown">
                        {{ $examCountdown['daysLeft'] }} Tag{{ $examCountdown['daysLeft'] != 1 ? 'e' : '' }} bis zur Prüfung
                        &middot; Tagesziel {{ $examCountdown['dailyTarget'] }} Fragen
                        &middot; heute {{ $examCountdown['todayAnswered'] }}/{{ $examCountdown['dailyTarget'] }}
                    </div>
                @endif
            </a>
            @endif

            {{-- Streak-at-risk Warning --}}
            @if(isset($streakAtRisk) && $streakAtRisk && ($dailyStreakGoal ?? 0) > 0)
            <div class="streak-warning">
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <i class="bi bi-fire" style="color:#f59e0b;font-size:1rem;"></i>
                    <div>
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">
                            {{ $user->streak_days }} Tage Streak in Gefahr!
                        </div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);">
                            Beantworte heute mind. {{ $dailyStreakGoal }} Fragen
                            @if(($user->streak_freezes_available ?? 0) > 0)
                                &middot; {{ $user->streak_freezes_available }} Freeze{{ $user->streak_freezes_available > 1 ? 's' : '' }} verfügbar
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Exam date hint (mobile, if no date set) --}}
            @if(!$examCountdown)
            <a href="{{ route('profile') }}" class="glass lg:hidden" data-tour-step="countdown" style="display:block;text-decoration:none;padding:0.875rem 1rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">Prüfungsdatum eintragen</div>
                        <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:0.15rem;">Erhalte ein tägliches Lernziel</div>
                    </div>
                    <i class="bi bi-arrow-right" style="color:#5b9aff;font-size:0.875rem;"></i>
                </div>
            </a>
            @endif

            {{-- 2. Journey Stepper (mobile only) --}}
            <div class="glass lg:hidden" style="padding:1rem;overflow:hidden;">
                <div class="section-label" style="margin-bottom:0.75rem;">Dein Fortschritt</div>
                <div class="journey journey--horizontal">
                    {{-- Step 1: Fragen lernen --}}
                    <div class="journey__step">
                        <div class="journey__circle {{ $solvedPercent >= 100 ? 'journey__circle--done' : 'journey__circle--active' }}">
                            @if($solvedPercent >= 100)
                                <i class="bi bi-check" style="font-size:0.75rem;"></i>
                            @else
                                1
                            @endif
                        </div>
                        <div class="journey__label {{ $solvedPercent < 100 ? 'journey__label--active' : '' }}">
                            Lernen<br>
                            <span class="journey__pct">{{ $solvedPercent }}%</span>
                        </div>
                    </div>

                    <div class="journey__line {{ $solvedPercent >= 100 ? 'journey__line--done' : '' }}"></div>

                    {{-- Step 2: Alle meistern --}}
                    @php
                        $step2Class = $masteryPercent >= 100 ? 'journey__circle--done' : ($masteryPercent > 0 ? 'journey__circle--active' : 'journey__circle--locked');
                        $step2LabelClass = ($masteryPercent > 0 && $masteryPercent < 100) ? 'journey__label--active' : '';
                    @endphp
                    <div class="journey__step">
                        <div class="journey__circle {{ $step2Class }}">
                            @if($masteryPercent >= 100)
                                <i class="bi bi-check" style="font-size:0.75rem;"></i>
                            @else
                                2
                            @endif
                        </div>
                        <div class="journey__label {{ $step2LabelClass }}">
                            Meistern<br>
                            <span class="journey__pct">{{ $masteryPercent }}%</span>
                        </div>
                    </div>

                    <div class="journey__line {{ $masteryPercent >= 100 ? 'journey__line--done' : '' }}"></div>

                    {{-- Step 3: Prüfung --}}
                    @php
                        $step3Class = $exams >= 5 ? 'journey__circle--done' : ($canStartExam ? 'journey__circle--active' : 'journey__circle--locked');
                        $step3LabelClass = $canStartExam && $exams < 5 ? 'journey__label--active' : '';
                    @endphp
                    <div class="journey__step">
                        <div class="journey__circle {{ $step3Class }}">
                            @if($exams >= 5)
                                <i class="bi bi-check" style="font-size:0.75rem;"></i>
                            @else
                                3
                            @endif
                        </div>
                        <div class="journey__label {{ $step3LabelClass }}">
                            Prüfung<br>
                            <span class="journey__pct">{{ $exams }}/5</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Wochenaktivität --}}
            <div class="glass" style="padding:1rem;" data-tour-step="stats">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;">
                    <span class="section-label">Diese Woche</span>
                    <a href="{{ route('statistics') }}" style="font-size:0.75rem;color:#5b9aff;text-decoration:none;font-weight:600;">Statistiken &rarr;</a>
                </div>

                @php
                    $weekTotal = $weeklyActivity->sum('count');
                    $weekCorrect = $weeklyActivity->sum('correct');
                    $weekHitRate = $weekTotal > 0 ? round(($weekCorrect / $weekTotal) * 100) : 0;
                @endphp

                @if($weekTotal > 0)
                    <div style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:0.625rem;">
                        {{ $weekTotal }} Fragen &middot; {{ $weekHitRate }}% richtig
                        @if($todayAnswered > 0)
                            &middot; heute {{ $todayAnswered }}
                        @endif
                    </div>
                    <div class="activity-bar">
                        @for($d = 0; $d < 7; $d++)
                            @php
                                $dayDate = $weekStart->copy()->addDays($d);
                                $dateStr = $dayDate->format('Y-m-d');
                                $dayData = $weeklyActivity->firstWhere('date', $dateStr);
                                $count   = $dayData->count ?? 0;
                                $barPct  = $count > 0 ? max(4, ($count / $maxCount) * 100) : 0;
                                $isToday = $dayDate->isToday();
                            @endphp
                            <div class="activity-bar__col">
                                <div class="activity-bar__track">
                                    @if($count > 0)
                                        <span class="activity-bar__count">{{ $count }}</span>
                                        <div class="activity-bar__fill {{ $isToday ? 'activity-bar__fill--today' : '' }}"
                                             style="height:{{ $barPct }}%;"
                                             title="{{ $count }} Fragen am {{ $dayDate->format('d.m.') }}"></div>
                                    @else
                                        <div class="activity-bar__fill activity-bar__fill--empty" style="height:4px;"></div>
                                    @endif
                                </div>
                                <span class="activity-bar__day {{ $isToday ? 'activity-bar__day--today' : '' }}">{{ $days[$d] }}</span>
                            </div>
                        @endfor
                    </div>
                @else
                    <div style="text-align:center;padding:1.25rem 0;">
                        <div style="font-size:0.8125rem;color:var(--text-muted);margin-bottom:0.5rem;">Noch keine Aktivität diese Woche</div>
                        <a href="{{ route('practice.all') }}" style="font-size:0.75rem;font-weight:600;color:#5b9aff;text-decoration:none;">Erste Frage beantworten &rarr;</a>
                    </div>
                @endif
            </div>

            {{-- 4. Lehrgänge --}}
            <div class="glass" style="padding:1rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;">
                    <span class="section-label">Lehrgänge</span>
                    <a href="{{ route('lehrgaenge.index') }}" style="font-size:0.75rem;color:#5b9aff;text-decoration:none;font-weight:600;">Alle anzeigen &rarr;</a>
                </div>

                @if($enrolledLehrgaenge->isEmpty())
                    <a href="{{ route('lehrgaenge.index') }}" class="lg-card" style="display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);">Lehrgänge entdecken</div>
                            <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:0.15rem;">Spezialisiere dein Wissen</div>
                        </div>
                        <i class="bi bi-arrow-right" style="color:#5b9aff;font-size:0.875rem;"></i>
                    </a>
                @else
                    <div class="space-y-2">
                        @foreach($enrolledLehrgaenge->take(3) as $lehrgang)
                            @php
                                $lgTotal = $lehrgang->questions()->count();
                                $lgSolved = $lgTotal > 0
                                    ? \App\Models\UserLehrgangProgress::where('user_id', $user->id)
                                        ->whereIn('lehrgang_question_id', $lehrgang->questions()->pluck('id'))
                                        ->where('consecutive_correct', '>=', 3)->count()
                                    : 0;
                                $lgPct = $lgTotal > 0 ? round(($lgSolved / $lgTotal) * 100) : 0;
                                $lgDone = $lgPct >= 100 && $lgSolved > 0;
                            @endphp
                            <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="lg-card">
                                <div class="lg-card-title">{{ $lehrgang->lehrgang }}</div>
                                <div class="lg-bar">
                                    <div class="lg-bar-fill {{ $lgDone ? 'lg-bar-fill--done' : '' }}" style="width:{{ $lgPct }}%;"></div>
                                </div>
                                <div style="display:flex;align-items:center;justify-content:space-between;">
                                    <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $lgSolved }}/{{ $lgTotal }} gemeistert</span>
                                    <span class="lg-pct">{{ $lgPct }}%</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 5. Ortsverband-Karte (alle Mitglieder) --}}
            @if($userOV && $ovCardData)
            <div class="glass-blue" style="padding:1rem;border-radius:0.75rem;">
                @if($ovCardData['type'] === 'ausbilder')
                    {{-- ── Ausbilder View ── --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                        <div style="min-width:0;">
                            <span class="badge-thw" style="display:inline-block;margin-bottom:0.35rem;">Ausbilder</span>
                            <div style="font-size:1rem;font-weight:700;color:var(--text-primary);">{{ $userOV->name }}</div>
                        </div>
                        <a href="{{ route('ortsverband.index') }}" class="btn-secondary btn-sm" style="flex-shrink:0;">Verwalten</a>
                    </div>
                    <div style="display:flex;gap:0.375rem;">
                        <div style="flex:1;background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:0.5rem;padding:0.4rem 0.25rem;text-align:center;">
                            <div style="font-size:1.125rem;font-weight:800;color:var(--text-primary);font-family:'Barlow Condensed',sans-serif;line-height:1;">{{ $ovCardData['stats']['total_members'] ?? 0 }}</div>
                            <div style="font-size:0.4375rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-top:0.15rem;">Mitglieder</div>
                        </div>
                        <div style="flex:1;background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:0.5rem;padding:0.4rem 0.25rem;text-align:center;">
                            <div style="font-size:1.125rem;font-weight:800;color:#22c55e;font-family:'Barlow Condensed',sans-serif;line-height:1;">{{ $ovCardData['stats']['active_members_7days'] ?? 0 }}</div>
                            <div style="font-size:0.4375rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-top:0.15rem;">Aktiv (7d)</div>
                        </div>
                        <div style="flex:1;background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:0.5rem;padding:0.4rem 0.25rem;text-align:center;">
                            <div style="font-size:1.125rem;font-weight:800;color:#fbbf24;font-family:'Barlow Condensed',sans-serif;line-height:1;">{{ $ovCardData['stats']['avg_theory'] ?? 0 }}%</div>
                            <div style="font-size:0.4375rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-top:0.15rem;">Ø Fortschritt</div>
                        </div>
                    </div>
                @else
                    {{-- ── Mitglied View ── --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                        <div style="min-width:0;">
                            <span style="font-size:0.5625rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;">Ortsverband</span>
                            <div style="font-size:1rem;font-weight:700;color:var(--text-primary);">{{ $userOV->name }}</div>
                        </div>
                        @if($ovCardData['totalEnrolled'] > 3)
                            <a href="{{ route('ortsverband.show', $userOV) }}" style="font-size:0.6875rem;color:var(--thw-blue-light, #60a5fa);text-decoration:none;flex-shrink:0;">Alle anzeigen</a>
                        @endif
                    </div>

                    {{-- Lernpools --}}
                    @if($ovCardData['lernpools']->isNotEmpty())
                        <div style="display:flex;flex-direction:column;gap:0.5rem;{{ $ovCardData['ranking'] && $ovCardData['ranking']->isNotEmpty() ? 'margin-bottom:0.75rem;' : '' }}">
                            @foreach($ovCardData['lernpools'] as $lp)
                                @php
                                    $lpProgress = $lp['progress'];
                                    if ($lpProgress >= 60) {
                                        $lpColor = '#22c55e';
                                        $lpGradient = 'linear-gradient(90deg,#22c55e,#16a34a)';
                                    } elseif ($lpProgress >= 30) {
                                        $lpColor = '#fbbf24';
                                        $lpGradient = 'linear-gradient(90deg,#fbbf24,#f59e0b)';
                                    } else {
                                        $lpColor = '#ef4444';
                                        $lpGradient = 'linear-gradient(90deg,#ef4444,#dc2626)';
                                    }
                                @endphp
                                <a href="{{ route('ortsverband.lernpools.show', [$userOV, $lp['id']]) }}" style="display:block;background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:0.5rem;padding:0.5rem 0.65rem;text-decoration:none;transition:border-color 0.15s;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.25rem;">
                                        <span style="font-size:0.75rem;font-weight:600;color:var(--text-primary);">{{ $lp['name'] }}</span>
                                        <span style="font-size:0.65rem;font-weight:700;color:{{ $lpColor }};">{{ $lpProgress }}%</span>
                                    </div>
                                    <div style="height:3px;background:rgba(255,255,255,0.08);border-radius:2px;overflow:hidden;">
                                        <div style="width:{{ $lpProgress }}%;height:100%;background:{{ $lpGradient }};border-radius:2px;"></div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Mini Ranking --}}
                    @if($ovCardData['ranking'] && $ovCardData['ranking']->isNotEmpty())
                        <div style="border-top:1px solid var(--glass-border);padding-top:0.6rem;">
                            <div style="font-size:0.5625rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.4rem;">Ranking</div>
                            <div style="display:flex;gap:0.5rem;font-size:0.6875rem;flex-wrap:wrap;">
                                @foreach($ovCardData['ranking'] as $idx => $member)
                                    @php
                                        $rankColors = ['#fbbf24', '#94a3b8', '#cd7f32'];
                                        $rankColor = $rankColors[$idx] ?? 'var(--text-muted)';
                                        $isCurrentUser = $member['user']->id === $user->id;
                                    @endphp
                                    <div style="display:flex;align-items:center;gap:0.25rem;">
                                        <span style="color:{{ $rankColor }};font-weight:700;">{{ $idx + 1 }}.</span>
                                        <span style="color:{{ $isCurrentUser ? '#3b82f6' : 'var(--text-secondary)' }};font-weight:{{ $isCurrentUser ? '700' : '400' }};">{{ $isCurrentUser ? 'Du' : Str::limit($member['user']->name, 10) }}</span>
                                        <span style="font-size:0.5625rem;color:var(--text-muted);">{{ number_format($member['points'] ?? 0) }} Pkt</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            @endif

        </div>{{-- end main column --}}

        {{-- ═══ DESKTOP SIDEBAR ════════════════════════════ --}}
        <div class="hidden lg:block space-y-4">

            {{-- Journey Stepper (vertical, desktop) --}}
            <div class="glass" style="padding:1rem;">
                <div class="journey-sidebar-label">Dein Fortschritt</div>
                <div class="journey journey--vertical">

                    {{-- Step 1 --}}
                    <div class="journey__step">
                        <div>
                            <div class="journey__circle {{ $solvedPercent >= 100 ? 'journey__circle--done' : 'journey__circle--active' }}">
                                @if($solvedPercent >= 100)
                                    <i class="bi bi-check" style="font-size:0.75rem;"></i>
                                @else
                                    1
                                @endif
                            </div>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="journey__label {{ $solvedPercent < 100 ? 'journey__label--active' : '' }}" style="margin-top:0;font-size:0.75rem;font-weight:600;">
                                Fragen lernen
                            </div>
                            <div class="journey-step-detail">{{ $solvedPercent }}% bearbeitet</div>
                            <div class="journey-step-bar">
                                <div class="journey-step-bar-fill" style="width:{{ $solvedPercent }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="journey__line {{ $solvedPercent >= 100 ? 'journey__line--done' : '' }}"></div>

                    {{-- Step 2 --}}
                    <div class="journey__step">
                        <div>
                            <div class="journey__circle {{ $masteryPercent >= 100 ? 'journey__circle--done' : ($masteryPercent > 0 ? 'journey__circle--active' : 'journey__circle--locked') }}">
                                @if($masteryPercent >= 100)
                                    <i class="bi bi-check" style="font-size:0.75rem;"></i>
                                @else
                                    2
                                @endif
                            </div>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="journey__label {{ $masteryPercent > 0 && $masteryPercent < 100 ? 'journey__label--active' : '' }}" style="margin-top:0;font-size:0.75rem;font-weight:600;">
                                Alle meistern
                            </div>
                            <div class="journey-step-detail">{{ $masteryPercent }}% gemeistert · {{ $solvedTotal }} Fragen</div>
                            <div class="journey-step-bar">
                                <div class="journey-step-bar-fill" style="width:{{ $masteryPercent }}%;background:{{ $masteryPercent >= 100 ? '#22c55e' : '#0055cc' }};"></div>
                            </div>
                        </div>
                    </div>

                    <div class="journey__line {{ $masteryPercent >= 100 ? 'journey__line--done' : '' }}"></div>

                    {{-- Step 3 --}}
                    <div class="journey__step" style="padding-bottom:0;" data-tour-step="exam">
                        <div>
                            <div class="journey__circle {{ $exams >= 5 ? 'journey__circle--done' : ($canStartExam ? 'journey__circle--active' : 'journey__circle--locked') }}">
                                @if($exams >= 5)
                                    <i class="bi bi-check" style="font-size:0.75rem;"></i>
                                @else
                                    3
                                @endif
                            </div>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="journey__label {{ $canStartExam && $exams < 5 ? 'journey__label--active' : '' }}" style="margin-top:0;font-size:0.75rem;font-weight:600;">
                                Prüfung
                            </div>
                            <div class="journey-step-detail">{{ $exams }}/5 bestanden</div>
                            <div class="journey-step-bar">
                                <div class="journey-step-bar-fill" style="width:{{ min(100, $exams * 20) }}%;background:{{ $exams >= 5 ? '#22c55e' : '#0055cc' }};"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Gamification Pills (desktop sidebar) --}}
            <div style="display:flex;gap:0.5rem;">
                <div class="gami-pill {{ isset($streakAtRisk) && $streakAtRisk ? 'gami-pill--streak-risk' : '' }}" data-tour-step="streak">
                    <div class="gami-pill__value gami-pill__value--gold">{{ $user->streak_days ?? 0 }}</div>
                    <div class="gami-pill__label">Streak</div>
                    @if(($user->streak_days ?? 0) >= 1)
                        @if($dailyStreakGoal > 0)
                            <div class="streak-progress">
                                <div class="streak-progress__fill {{ $todayAnswered >= $dailyStreakGoal ? 'streak-progress__fill--done' : '' }}" style="width:{{ min(100, ($todayAnswered / $dailyStreakGoal) * 100) }}%;"></div>
                            </div>
                            <div class="streak-progress__text {{ $todayAnswered >= $dailyStreakGoal ? 'streak-progress__text--done' : '' }}">{{ min($todayAnswered, $dailyStreakGoal) }}/{{ $dailyStreakGoal }}</div>
                        @else
                            <div class="streak-progress">
                                <div class="streak-progress__fill streak-progress__fill--done" style="width:100%;"></div>
                            </div>
                            <div class="streak-progress__text streak-progress__text--done">Keine Fragen f&auml;llig</div>
                        @endif
                    @endif
                </div>
                <div class="gami-pill" data-tour-step="achievements">
                    <div class="gami-pill__value gami-pill__value--blue">{{ $solvedTotal }}</div>
                    <div class="gami-pill__label">Gelöst</div>
                </div>
                @if($leagueRank)
                    <a href="{{ route('gamification.leaderboard') }}" class="gami-pill" style="text-decoration:none;cursor:pointer;">
                        <div class="gami-pill__value" style="color:{{ $leagueInfo['color'] }};">#{{ $leagueRank }}</div>
                        <div class="gami-pill__label">{{ $leagueInfo['name'] }}-Liga</div>
                    </a>
                @else
                    <a href="{{ route('gamification.leaderboard') }}" class="gami-pill" style="text-decoration:none;cursor:pointer;">
                        <div class="gami-pill__value" style="color:{{ $leagueInfo['color'] }};font-size:0.75rem;">{{ $leagueInfo['name'] }}</div>
                        <div class="gami-pill__label" style="font-size:0.5rem;">Teilnehmen &rarr;</div>
                    </a>
                @endif
            </div>

            {{-- Spaced Repetition Nudge --}}
            @if($spacedRepetitionDue > 0)
            <a href="{{ route('practice.spaced-repetition') }}" class="sr-nudge">
                <div style="display:flex;align-items:center;gap:0.625rem;">
                    <div class="sr-nudge__badge">{{ $spacedRepetitionDue }}</div>
                    <div>
                        <div class="sr-nudge__title">Reviews fällig</div>
                        <div class="sr-nudge__desc">Halte dein Wissen frisch</div>
                    </div>
                </div>
                <i class="bi bi-arrow-right" style="color:#5b9aff;font-size:0.75rem;"></i>
            </a>
            @endif

            {{-- Unopened Lootboxes --}}
            @if($unopenedLootboxes > 0)
            <a href="{{ route('gamification.lootboxes') }}" class="lootbox-nudge">
                <div style="display:flex;align-items:center;gap:0.625rem;">
                    <div class="lootbox-nudge__icon">
                        <i class="bi bi-gift-fill" style="font-size:0.875rem;"></i>
                    </div>
                    <div>
                        <div class="lootbox-nudge__title">{{ $unopenedLootboxes }} Belohnung{{ $unopenedLootboxes > 1 ? 'en' : '' }}</div>
                        <div class="lootbox-nudge__desc">Wochenbelohnung wartet</div>
                    </div>
                </div>
                <i class="bi bi-arrow-right" style="color:#fbbf24;font-size:0.75rem;"></i>
            </a>
            @endif

            {{-- Exam Countdown (desktop sidebar) --}}
            @if($examCountdown)
            @php
                $cdPct = $examCountdown['dailyTarget'] > 0
                    ? min(100, round(($examCountdown['todayAnswered'] / $examCountdown['dailyTarget']) * 100))
                    : 0;
                $cdDone = $examCountdown['todayAnswered'] >= $examCountdown['dailyTarget'];
            @endphp
            <div class="countdown-widget" data-tour-step="countdown">
                <div class="countdown-label-small" style="margin-bottom:0.5rem;">Prüfung in</div>
                <div class="countdown-days-big">{{ $examCountdown['daysLeft'] }}</div>
                <div class="countdown-label-small">Tag{{ $examCountdown['daysLeft'] != 1 ? 'en' : '' }}</div>
                @if($examCountdown['dailyTarget'] > 0)
                    <div style="margin-top:0.75rem;">
                        <div style="font-size:0.6875rem;color:var(--text-secondary);margin-bottom:0.25rem;">
                            Tagesziel: {{ $examCountdown['dailyTarget'] }} Fragen
                        </div>
                        <div class="countdown-mini-bar">
                            <div class="countdown-mini-fill" style="width:{{ $cdPct }}%;background:{{ $cdDone ? '#22c55e' : '#0055cc' }};"></div>
                        </div>
                        <div style="font-size:0.625rem;font-weight:600;color:{{ $cdDone ? '#22c55e' : 'var(--text-muted)' }};">
                            @if($cdDone)
                                {{ $examCountdown['todayAnswered'] }}/{{ $examCountdown['dailyTarget'] }} – geschafft!
                            @else
                                noch {{ $examCountdown['dailyTarget'] - $examCountdown['todayAnswered'] }} übrig
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            @else
            <a href="{{ route('profile') }}" class="countdown-widget" data-tour-step="countdown" style="display:block;text-decoration:none;">
                <div class="countdown-label-small" style="margin-bottom:0.5rem;">Prüfungs-Countdown</div>
                <div style="font-size:0.8125rem;color:var(--text-secondary);line-height:1.5;margin-bottom:0.75rem;">
                    Trage dein Prüfungsdatum ein und erhalte ein tägliches Lernziel.
                </div>
                <span style="font-size:0.75rem;font-weight:600;color:#5b9aff;">Prüfungsdatum eintragen &rarr;</span>
            </a>
            @endif

        </div>{{-- end desktop sidebar --}}

    </div>{{-- end dash-grid --}}

    {{-- ── Mobile-only: Gamification + Quick Links ────── --}}
    <div class="lg:hidden space-y-4 mt-4">

        {{-- Gamification Pills --}}
        <div style="display:flex;gap:0.5rem;">
            <div class="gami-pill {{ isset($streakAtRisk) && $streakAtRisk ? 'gami-pill--streak-risk' : '' }}" data-tour-step="streak">
                <div class="gami-pill__value gami-pill__value--gold">{{ $user->streak_days ?? 0 }}</div>
                <div class="gami-pill__label">Streak</div>
                @if(($user->streak_days ?? 0) >= 1)
                    @if($dailyStreakGoal > 0)
                        <div class="streak-progress">
                            <div class="streak-progress__fill {{ $todayAnswered >= $dailyStreakGoal ? 'streak-progress__fill--done' : '' }}" style="width:{{ min(100, ($todayAnswered / $dailyStreakGoal) * 100) }}%;"></div>
                        </div>
                        <div class="streak-progress__text {{ $todayAnswered >= $dailyStreakGoal ? 'streak-progress__text--done' : '' }}">{{ min($todayAnswered, $dailyStreakGoal) }}/{{ $dailyStreakGoal }}</div>
                    @else
                        <div class="streak-progress">
                            <div class="streak-progress__fill streak-progress__fill--done" style="width:100%;"></div>
                        </div>
                        <div class="streak-progress__text streak-progress__text--done">Keine Fragen f&auml;llig</div>
                    @endif
                @endif
            </div>
            <div class="gami-pill" data-tour-step="achievements">
                <div class="gami-pill__value gami-pill__value--blue">{{ $solvedTotal }}</div>
                <div class="gami-pill__label">Gelöst</div>
            </div>
            @if($leagueRank)
                <a href="{{ route('gamification.leaderboard') }}" class="gami-pill" style="text-decoration:none;cursor:pointer;">
                    <div class="gami-pill__value" style="color:{{ $leagueInfo['color'] }};">#{{ $leagueRank }}</div>
                    <div class="gami-pill__label">{{ $leagueInfo['name'] }}-Liga</div>
                </a>
            @else
                <a href="{{ route('gamification.leaderboard') }}" class="gami-pill" style="text-decoration:none;cursor:pointer;">
                    <div class="gami-pill__value" style="color:{{ $leagueInfo['color'] }};font-size:0.75rem;">{{ $leagueInfo['name'] }}</div>
                    <div class="gami-pill__label" style="font-size:0.5rem;">Teilnehmen &rarr;</div>
                </a>
            @endif
        </div>

        {{-- Quick Links --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.5rem;">
            <a href="{{ route('practice.menu') }}" class="dash-quick-link"><i class="bi bi-book" style="font-size:0.875rem;"></i> Üben</a>
            <a href="{{ route('statistics') }}" class="dash-quick-link"><i class="bi bi-bar-chart" style="font-size:0.875rem;"></i> Stats</a>
            <a href="{{ route('gamification.leaderboard') }}" class="dash-quick-link"><i class="bi bi-trophy" style="font-size:0.875rem;"></i> Rang</a>
            <a href="{{ route('shop.index') }}" class="dash-quick-link"><i class="bi bi-bag" style="font-size:0.875rem;"></i> Shop</a>
        </div>

    </div>

</div>{{-- .dash-container --}}

{{-- Leaderboard Consent Modal --}}
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
                <p style="color:white;font-size:0.85rem;margin-bottom:0.5rem;"><strong>Name &amp; Punkte</strong> werden angezeigt</p>
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

@push('scripts')
<script>
function dismissLeaderboardModal(accept) {
    const modal = document.getElementById('leaderboard-modal');
    if (modal) {
        modal.style.animation = 'fadeOutModal 0.3s ease-out forwards';
        setTimeout(() => modal.remove(), 300);
    }
    if (accept === false) {
        document.getElementById('lb-decline-form').submit();
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') dismissLeaderboardModal(false);
});
</script>
@endpush

@endsection
