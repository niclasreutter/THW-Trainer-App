@extends('layouts.app')
@section('title', 'Lernsessions - THW Trainer')
@section('description', 'Gemeinsam lernen und gegeneinander messen – Lernsessions im THW Trainer.')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Session Badges ─────────────────────────── */
    .ls-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.625rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        white-space: nowrap;
        font-family: 'IBM Plex Mono', monospace;
    }

    .ls-badge--active {
        background: rgba(34,197,94,0.12);
        color: #22c55e;
        border: 1px solid rgba(34,197,94,0.25);
    }

    .ls-badge--scheduled {
        background: rgba(251,191,36,0.12);
        color: #f59e0b;
        border: 1px solid rgba(251,191,36,0.25);
    }

    .ls-badge--global {
        background: rgba(91,154,255,0.12);
        color: #5b9aff;
        border: 1px solid rgba(91,154,255,0.25);
    }

    .ls-badge--ov {
        background: rgba(168,85,247,0.12);
        color: #a78bfa;
        border: 1px solid rgba(168,85,247,0.25);
    }

    html.light-mode .ls-badge--active { background: rgba(34,197,94,0.08); }
    html.light-mode .ls-badge--scheduled { background: rgba(251,191,36,0.08); }
    html.light-mode .ls-badge--global { background: rgba(0,85,204,0.08); color: #00337F; }
    html.light-mode .ls-badge--ov { background: rgba(168,85,247,0.08); }

    /* ─── Live Dot ───────────────────────────────── */
    .ls-live-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
        margin-right: 0.25rem;
        vertical-align: middle;
        animation: ls-pulse 1.5s ease-in-out infinite;
    }

    @keyframes ls-pulse {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(34,197,94,0.6); }
        50%      { opacity: 0.7; box-shadow: 0 0 0 4px rgba(34,197,94,0); }
    }

    /* ─── Countdown ──────────────────────────────── */
    .ls-countdown {
        font-size: 1.5rem;
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        color: #fff;
        line-height: 1;
    }

    /* ─── Session Items ──────────────────────────── */
    .ls-session-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0.5rem;
        transition: background 150ms;
        border-radius: 0.375rem;
        text-decoration: none;
    }

    .ls-session-item:hover {
        background: rgba(255,255,255,0.03);
        text-decoration: none;
    }

    html.light-mode .ls-session-item:hover { background: rgba(0,0,0,0.03); }

    .ls-session-item + .ls-session-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .ls-session-item + .ls-session-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .ls-session-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        flex-shrink: 0;
    }

    .ls-session-icon--active {
        background: rgba(34,197,94,0.12);
        color: #22c55e;
    }

    .ls-session-icon--upcoming {
        background: rgba(251,191,36,0.12);
        color: #f59e0b;
    }

    .ls-session-icon--global {
        background: rgba(0,85,204,0.12);
        color: #5b9aff;
    }

    .ls-session-icon--ov {
        background: rgba(168,85,247,0.12);
        color: #a78bfa;
    }

    html.light-mode .ls-session-icon--global { background: rgba(0,51,127,0.08); color: #00337F; }

    /* ─── Result Items ───────────────────────────── */
    .ls-result-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.25rem;
        transition: background 150ms;
        border-radius: 0.375rem;
    }

    .ls-result-item:hover { background: rgba(255,255,255,0.03); }
    html.light-mode .ls-result-item:hover { background: rgba(0,0,0,0.03); }

    .ls-result-item + .ls-result-item {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .ls-result-item + .ls-result-item {
        border-top-color: rgba(0,0,0,0.06);
    }

    .ls-rank-badge {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.75rem;
        flex-shrink: 0;
        font-family: 'Barlow Condensed', sans-serif;
    }

    .ls-rank-badge--gold   { background: rgba(251,191,36,0.15); color: #fbbf24; }
    .ls-rank-badge--silver { background: rgba(148,163,184,0.15); color: #94a3b8; }
    .ls-rank-badge--bronze { background: rgba(217,119,6,0.15);  color: #d97706; }
    .ls-rank-badge--other  { background: rgba(255,255,255,0.06); color: var(--text-muted); }

    html.light-mode .ls-rank-badge--other { background: rgba(0,0,0,0.04); }

    /* ─── Filter Pills ───────────────────────────── */
    .ls-filter-pills {
        display: flex;
        gap: 0.375rem;
        margin-bottom: 0.75rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .ls-filter-pills::-webkit-scrollbar { display: none; }

    .ls-filter-pill {
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
        flex-shrink: 0;
    }

    .ls-filter-pill:hover {
        border-color: rgba(91,154,255,0.3);
        color: var(--text-secondary);
    }

    .ls-filter-pill.active {
        background: rgba(91,154,255,0.12);
        color: #5b9aff;
        border-color: rgba(91,154,255,0.3);
    }

    html.light-mode .ls-filter-pill.active {
        background: rgba(0,51,127,0.08);
        color: #00337F;
        border-color: rgba(0,51,127,0.2);
    }

    /* ─── Create Button ──────────────────────────── */
    .ls-create-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        background: linear-gradient(135deg, #0055cc, #5b9aff);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.875rem;
        border-radius: 0.375rem;
        text-decoration: none;
        transition: transform 200ms, box-shadow 200ms;
    }

    .ls-create-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,85,204,0.3);
        text-decoration: none;
        color: #fff;
    }

    /* ─── Stagger animation ──────────────────────── */
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

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    @media (max-width: 480px) {
        .ls-countdown { font-size: 1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Gemeinsam lernen</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Lernsessions</h1>
        <p class="text-sm" style="color:var(--text-muted);">Gemeinsam lernen, gegeneinander messen — 50 % mehr XP</p>
    </div>

    {{-- ── Gami Pills ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap:wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#22c55e;-webkit-text-fill-color:#22c55e;">{{ $activeSessions->count() }}</div>
            <div class="gami-pill__label">Aktiv</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#f59e0b;-webkit-text-fill-color:#f59e0b;">{{ $upcomingSessions->count() }}</div>
            <div class="gami-pill__label">Geplant</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#fbbf24;-webkit-text-fill-color:#fbbf24;">{{ $wonCount }}</div>
            <div class="gami-pill__label">Gewonnen</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $sessions->count() }}</div>
            <div class="gami-pill__label">Gesamt</div>
        </div>
    </div>

    <div class="space-y-4">

        {{-- ── Smart Action: Aktive Session ── --}}
        @if($activeSessions->isNotEmpty())
            @php
                $firstActive = $activeSessions->first();
                $firstActiveSession = $firstActive->learningSession;
            @endphp
            <a href="{{ route('lernsession.live', $firstActive) }}" class="smart-action" style="background:linear-gradient(135deg,#064e3b,#065f46);">
                <div style="font-size:0.625rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.5);font-weight:700;margin-bottom:0.35rem;font-family:'IBM Plex Mono',monospace;">
                    <span class="ls-live-dot"></span> Session läuft
                </div>
                <div style="font-size:1.125rem;font-weight:700;color:#fff;margin-bottom:0.2rem;">{{ $firstActiveSession->title }}</div>
                <div style="font-size:0.75rem;color:rgba(255,255,255,0.55);margin-bottom:0.75rem;">
                    {{ $firstActive->getParticipantCount() }} Teilnehmer · {{ $firstActiveSession->isGlobal() ? 'Global' : ($firstActiveSession->ortsverband->name ?? 'OV') }}
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="display:inline-flex;align-items:center;gap:0.375rem;background:rgba(255,255,255,0.2);color:#fff;border-radius:0.375rem;padding:0.375rem 0.75rem;font-size:0.75rem;font-weight:600;">
                        @if($currentParticipation && $currentParticipation->instance_id == $firstActive->id)
                            Zur Session <i class="bi bi-arrow-right"></i>
                        @else
                            Jetzt teilnehmen <i class="bi bi-arrow-right"></i>
                        @endif
                    </span>
                    <div class="ls-countdown"
                         x-data="{ remaining: {{ $firstActive->getTimeRemainingSeconds() }} }"
                         x-init="setInterval(() => { if(remaining > 0) remaining-- }, 1000)"
                         x-text="Math.floor(remaining/3600) + ':' + String(Math.floor((remaining%3600)/60)).padStart(2,'0') + ':' + String(remaining%60).padStart(2,'0')">
                    </div>
                </div>
            </a>
        @else
            {{-- Kein aktive Session: Nächste bevorstehende oder Erstell-Hinweis --}}
            @if($upcomingSessions->isNotEmpty())
                @php
                    $nextUp = $upcomingSessions->first();
                    $nextUpSession = $nextUp->learningSession;
                @endphp
                <div class="smart-action" style="cursor:default;">
                    <div style="font-size:0.625rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.5);font-weight:700;margin-bottom:0.35rem;font-family:'IBM Plex Mono',monospace;">Nächste Session</div>
                    <div style="font-size:1.125rem;font-weight:700;color:#fff;margin-bottom:0.2rem;">{{ $nextUpSession->title }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.55);">Startet am {{ $nextUp->starts_at->format('d.m.Y') }} um {{ $nextUp->starts_at->format('H:i') }} Uhr</div>
                </div>
            @else
                <div class="smart-action" style="cursor:default;">
                    <div style="font-size:0.625rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.5);font-weight:700;margin-bottom:0.35rem;font-family:'IBM Plex Mono',monospace;">Lernsessions</div>
                    <div style="font-size:1.125rem;font-weight:700;color:#fff;margin-bottom:0.2rem;">Keine aktive Session</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.55);">Es läuft gerade keine Session. Schau später wieder vorbei!</div>
                </div>
            @endif
        @endif

        {{-- ── Aktive Sessions (falls mehrere) ── --}}
        @if($activeSessions->count() > 1)
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">
                    <span class="ls-live-dot"></span> Weitere aktive Sessions
                </span>
                <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $activeSessions->count() }} aktiv</span>
            </div>
            @foreach($activeSessions->skip(1) as $activeInstance)
                @php $activeSession = $activeInstance->learningSession; @endphp
                <a href="{{ route('lernsession.live', $activeInstance) }}" class="ls-session-item">
                    <div class="ls-session-icon ls-session-icon--active">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $activeSession->title }}</div>
                        <div style="font-size:0.625rem;color:var(--text-muted);margin-top:0.1rem;">
                            {{ $activeInstance->getParticipantCount() }} Teilnehmer ·
                            <span class="ls-badge {{ $activeSession->isGlobal() ? 'ls-badge--global' : 'ls-badge--ov' }}">
                                {{ $activeSession->isGlobal() ? 'Global' : ($activeSession->ortsverband->name ?? 'OV') }}
                            </span>
                        </div>
                    </div>
                    <div class="ls-countdown" style="font-size:1rem;"
                         x-data="{ remaining: {{ $activeInstance->getTimeRemainingSeconds() }} }"
                         x-init="setInterval(() => { if(remaining > 0) remaining-- }, 1000)"
                         x-text="Math.floor(remaining/60) + ':' + String(remaining%60).padStart(2,'0')">
                    </div>
                    <i class="bi bi-chevron-right" style="color:var(--text-muted);font-size:0.75rem;flex-shrink:0;"></i>
                </a>
            @endforeach
        </div>
        @endif

        {{-- ── Bevorstehende Sessions + Ergebnisse (2-Column auf Desktop) ── --}}
        <div class="grid grid-cols-1 gap-3" style="{{ ($upcomingSessions->isNotEmpty() && $recentResults->isNotEmpty()) ? 'grid-template-columns:1fr 1fr;' : '' }}">
            {{-- Bevorstehend --}}
            @if($upcomingSessions->isNotEmpty())
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Bevorstehend</span>
                    <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $upcomingSessions->count() }} geplant</span>
                </div>
                @foreach($upcomingSessions->take(4) as $upcoming)
                    @php $upSession = $upcoming->learningSession; @endphp
                    <a href="{{ route('lernsession.show', $upSession) }}" class="ls-session-item">
                        <div class="ls-session-icon ls-session-icon--upcoming">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $upSession->title }}</div>
                            <div style="font-size:0.625rem;color:var(--text-muted);margin-top:0.1rem;">
                                {{ $upcoming->starts_at->format('d.m. H:i') }} Uhr
                                <span class="ls-badge ls-badge--scheduled">Geplant</span>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right" style="color:var(--text-muted);font-size:0.75rem;flex-shrink:0;"></i>
                    </a>
                @endforeach
            </div>
            @endif

            {{-- Meine Ergebnisse --}}
            @if($recentResults->isNotEmpty())
            <div class="glass p-4" style="border-radius:0.75rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Meine Ergebnisse</span>
                </div>
                @foreach($recentResults->take(4) as $result)
                    @php
                        $rank = $result->final_rank;
                        $rankClass = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : 'other'));
                    @endphp
                    <div class="ls-result-item">
                        <div class="ls-rank-badge ls-rank-badge--{{ $rankClass }}">
                            @if($rank === 1) 🥇
                            @elseif($rank === 2) 🥈
                            @elseif($rank === 3) 🥉
                            @else #{{ $rank }}
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $result->instance->learningSession->title ?? 'Session' }}
                            </div>
                            <div style="font-size:0.625rem;color:var(--text-muted);margin-top:0.1rem;">
                                {{ $result->questions_correct ?? 0 }}/{{ $result->questions_answered ?? 0 }} richtig
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:0.8125rem;font-weight:700;color:#fbbf24;font-family:'IBM Plex Mono',monospace;">+{{ $result->xp_earned }} XP</div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Session erstellen Button (für Berechtigte) ── --}}
        @if(auth()->user()->useroll === 'admin' || auth()->user()->ortsverbände()->wherePivot('role', 'ausbildungsbeauftragter')->exists())
        <div style="display:flex;justify-content:flex-end;">
            <a href="{{ route('lernsession.create') }}" class="ls-create-btn">
                <i class="bi bi-plus-lg"></i> Session erstellen
            </a>
        </div>
        @endif

        {{-- ── Alle Sessions ── --}}
        <div class="glass p-4" style="border-radius:0.75rem;" x-data="{ filter: 'all' }">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Alle Sessions</span>
                <span style="font-size:0.6875rem;color:var(--text-muted);">{{ $sessions->count() }} Sessions</span>
            </div>

            <div class="ls-filter-pills">
                <button class="ls-filter-pill" :class="{ 'active': filter === 'all' }" @click="filter = 'all'">Alle</button>
                <button class="ls-filter-pill" :class="{ 'active': filter === 'global' }" @click="filter = 'global'">Global</button>
                <button class="ls-filter-pill" :class="{ 'active': filter === 'ov' }" @click="filter = 'ov'">Ortsverband</button>
                <button class="ls-filter-pill" :class="{ 'active': filter === 'recurring' }" @click="filter = 'recurring'">Wiederkehrend</button>
            </div>

            @forelse($sessions as $session)
                <a href="{{ route('lernsession.show', $session) }}" class="ls-session-item"
                   x-show="filter === 'all' || (filter === 'global' && {{ $session->isGlobal() ? 'true' : 'false' }}) || (filter === 'ov' && !{{ $session->isGlobal() ? 'true' : 'false' }}) || (filter === 'recurring' && '{{ $session->schedule_type }}' === 'recurring')"
                   x-transition.opacity>
                    <div class="ls-session-icon {{ $session->isGlobal() ? 'ls-session-icon--global' : 'ls-session-icon--ov' }}">
                        <i class="bi {{ $session->isGlobal() ? 'bi-globe2' : 'bi-people-fill' }}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $session->title }}</div>
                        <div style="font-size:0.625rem;color:var(--text-muted);margin-top:0.1rem;">
                            <span class="ls-badge {{ $session->isGlobal() ? 'ls-badge--global' : 'ls-badge--ov' }}">
                                {{ $session->isGlobal() ? 'Global' : ($session->ortsverband->name ?? 'OV') }}
                            </span>
                            @if($session->schedule_type === 'recurring')
                                · Wiederkehrend
                            @elseif($session->starts_at)
                                · {{ $session->starts_at->format('d.m.Y H:i') }}
                            @endif
                            · {{ $session->creator->name }}
                        </div>
                    </div>
                    <i class="bi bi-chevron-right" style="color:var(--text-muted);font-size:0.75rem;flex-shrink:0;transition:color 150ms;"></i>
                </a>
            @empty
                <div style="text-align:center;padding:2rem 1rem;">
                    <div style="width:3rem;height:3rem;margin:0 auto 1rem;border-radius:0.75rem;background:rgba(0,85,204,0.08);display:flex;align-items:center;justify-content:center;color:#5b9aff;font-size:1.25rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <h2 style="font-size:1rem;font-weight:700;color:var(--text-primary);margin-bottom:0.35rem;">Noch keine Sessions</h2>
                    <p style="font-size:0.8125rem;color:var(--text-muted);line-height:1.5;">Es wurden noch keine Lernsessions erstellt.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

@push('styles')
<style>
    @media (max-width: 640px) {
        .grid[style*="grid-template-columns:1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
@endsection
