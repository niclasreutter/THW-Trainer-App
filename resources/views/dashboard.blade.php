@extends('layouts.app')

@section('title', 'Dashboard - Dein Lernfortschritt')
@section('description', 'Dein persönliches THW-Trainer Dashboard: Verfolge deinen Lernfortschritt, wiederhole falsche Fragen und bereite dich optimal auf deine THW-Prüfung vor.')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
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

    /* ── Weekly activity data ────────────────────── */
    $weekStart = \Carbon\Carbon::now()->startOfWeek();
    $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
    $maxCount = max((int) ($weeklyActivity->max('count') ?? 0), 1);
@endphp

<div class="dash-container">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="dash-header">
        <div class="dash-greeting">{{ $greeting }}</div>
        <div class="dash-username">{{ $user->name }}</div>
        <div class="dash-level-line">Level {{ $user->level ?? 1 }} &middot; {{ number_format($user->points ?? 0) }} Punkte</div>
    </div>

    {{-- ── Main grid (desktop: 2 cols) ───────────────── --}}
    <div class="dash-grid">

        {{-- ═══ MAIN COLUMN ═══════════════════════════════ --}}
        <div class="space-y-4">

            {{-- 1. Smart Action Card --}}
            <a href="{{ $smartAction['route'] }}"
               class="smart-action {{ $smartAction['type'] === 'urgent' ? 'smart-action--urgent' : '' }}"
               style="display:block;text-decoration:none;">
                <div class="smart-action__label">{{ $smartAction['label'] }}</div>
                <div class="smart-action__title">{{ $smartAction['title'] }}</div>
                <div class="smart-action__desc">{{ $smartAction['desc'] }}</div>
                <span class="smart-action__btn">
                    {{ $smartAction['btn'] }}
                    <i class="bi bi-arrow-right"></i>
                </span>
                @if($examCountdown)
                    <div class="smart-action__countdown">
                        {{ $examCountdown['daysLeft'] }} Tag{{ $examCountdown['daysLeft'] != 1 ? 'e' : '' }} bis zur Prüfung
                        &middot; Tagesziel {{ $examCountdown['dailyTarget'] }} Fragen
                        &middot; heute {{ $examCountdown['todayAnswered'] }}/{{ $examCountdown['dailyTarget'] }}
                    </div>
                @endif
            </a>

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
            <div class="glass" style="padding:1rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;">
                    <span class="section-label">Diese Woche</span>
                    <a href="{{ route('statistics') }}" style="font-size:0.75rem;color:#5b9aff;text-decoration:none;font-weight:600;">Statistiken &rarr;</a>
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
                            @if($count > 0)
                                <span class="activity-bar__count">{{ $count }}</span>
                                <div class="activity-bar__fill {{ $isToday ? 'activity-bar__fill--today' : '' }}"
                                     style="height:{{ $barPct }}%;"
                                     title="{{ $count }} Fragen am {{ $dayDate->format('d.m.') }}"></div>
                            @else
                                <div class="activity-bar__fill activity-bar__fill--empty" style="height:4px;"></div>
                            @endif
                            <span class="activity-bar__day {{ $isToday ? 'activity-bar__day--today' : '' }}">{{ $days[$d] }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- 4. Lehrgänge --}}
            <div class="glass" style="padding:1rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.875rem;">
                    <span class="section-label">Lehrgänge</span>
                    <a href="{{ route('lehrgaenge.index') }}" style="font-size:0.75rem;color:#5b9aff;text-decoration:none;font-weight:600;">Alle anzeigen &rarr;</a>
                </div>

                @if($enrolledLehrgaenge->isEmpty())
                    <div style="text-align:center;padding:1.5rem 0;">
                        <div style="font-size:0.8125rem;color:var(--text-muted);margin-bottom:0.75rem;">Noch keine Lehrgänge eingeschrieben</div>
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

            {{-- 5. Ausbilder-Karte (konditionell) --}}
            @if($isAusbilder && $userOV)
            <div class="glass-blue" style="padding:1rem;border-radius:0.75rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                <div style="flex:1;min-width:0;">
                    <span class="badge-thw" style="display:inline-block;margin-bottom:0.35rem;">Ausbilder</span>
                    <div style="font-size:1rem;font-weight:700;color:var(--text-primary);">{{ $userOV->name }}</div>
                </div>
                <div style="margin-left:auto;">
                    <a href="{{ route('ortsverband.index') }}" class="btn-secondary btn-sm">Verwalten</a>
                </div>
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
                    <div class="journey__step" style="padding-bottom:0;">
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
                <div class="gami-pill {{ isset($streakAtRisk) && $streakAtRisk ? 'gami-pill--streak-risk' : '' }}">
                    <div class="gami-pill__value gami-pill__value--gold">{{ $user->streak_days ?? 0 }}</div>
                    <div class="gami-pill__label">Streak</div>
                </div>
                <div class="gami-pill">
                    <div class="gami-pill__value gami-pill__value--blue">{{ $solvedTotal }}</div>
                    <div class="gami-pill__label">Gelöst</div>
                </div>
                @if($leaderboardRank)
                    <div class="gami-pill">
                        <div class="gami-pill__value">{{ $leaderboardRank }}</div>
                        <div class="gami-pill__label">Ranking</div>
                    </div>
                @else
                    <div class="gami-pill">
                        <div class="gami-pill__value" style="color:{{ $leagueInfo['color'] }};">{{ $leagueInfo['name'] }}</div>
                        <div class="gami-pill__label">Liga</div>
                    </div>
                @endif
            </div>

            {{-- Exam Countdown (desktop sidebar, if set) --}}
            @if($examCountdown)
            @php
                $cdPct = $examCountdown['dailyTarget'] > 0
                    ? min(100, round(($examCountdown['todayAnswered'] / $examCountdown['dailyTarget']) * 100))
                    : 0;
                $cdDone = $examCountdown['todayAnswered'] >= $examCountdown['dailyTarget'];
            @endphp
            <div class="countdown-widget">
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
            @endif

        </div>{{-- end desktop sidebar --}}

    </div>{{-- end dash-grid --}}

    {{-- ── Mobile-only: Gamification + Quick Links ────── --}}
    <div class="lg:hidden space-y-4 mt-4">

        {{-- Gamification Pills --}}
        <div style="display:flex;gap:0.5rem;">
            <div class="gami-pill {{ isset($streakAtRisk) && $streakAtRisk ? 'gami-pill--streak-risk' : '' }}">
                <div class="gami-pill__value gami-pill__value--gold">{{ $user->streak_days ?? 0 }}</div>
                <div class="gami-pill__label">Streak</div>
            </div>
            <div class="gami-pill">
                <div class="gami-pill__value gami-pill__value--blue">{{ $solvedTotal }}</div>
                <div class="gami-pill__label">Gelöst</div>
            </div>
            @if($leaderboardRank)
                <div class="gami-pill">
                    <div class="gami-pill__value">{{ $leaderboardRank }}</div>
                    <div class="gami-pill__label">Ranking</div>
                </div>
            @else
                <div class="gami-pill">
                    <div class="gami-pill__value" style="color:{{ $leagueInfo['color'] }};">{{ $leagueInfo['name'] }}</div>
                    <div class="gami-pill__label">Liga</div>
                </div>
            @endif
        </div>

        {{-- Quick Links --}}
        <div style="display:flex;gap:0.5rem;">
            <a href="{{ route('practice.menu') }}" class="dash-quick-link">Üben</a>
            <a href="{{ route('lehrgaenge.index') }}" class="dash-quick-link">Lehrgänge</a>
            <a href="{{ route('shop.index') }}" class="dash-quick-link">Shop</a>
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
