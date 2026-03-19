@extends('layouts.app')
@section('title', $session->title . ' - Live')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Smart Action Card ──────────────────────── */
    .smart-action {
        display: block;
        text-decoration: none;
        background: linear-gradient(135deg, #00337F, #004db3);
        border-radius: 0.75rem;
        padding: 1.125rem 1.25rem;
        position: relative;
        overflow: hidden;
        transition: transform 250ms ease, box-shadow 250ms ease;
    }

    .smart-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,51,127,0.25);
        text-decoration: none;
    }

    .smart-action::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(91,154,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .smart-action__label {
        font-size: 0.625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.5);
        font-weight: 700;
        margin-bottom: 0.35rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .smart-action__title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 0.2rem;
    }

    .smart-action__desc {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.55);
        margin-bottom: 0.75rem;
    }

    .smart-action__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        background: linear-gradient(135deg, #5b9aff, #0055cc);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.875rem;
        border-radius: 0.375rem;
    }

    /* ─── Timer + Stats Grid ─────────────────────── */
    .ls-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .ls-info-card {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
    }

    .timer-display {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #5b9aff;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        line-height: 1.2;
        text-align: center;
    }

    html.light-mode .timer-display { color: #00337F; }

    .timer-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        text-align: center;
        margin-top: 0.125rem;
    }

    .timer-meta {
        font-size: 0.6875rem;
        color: var(--text-muted);
        text-align: center;
        margin-top: 0.5rem;
    }

    .timer-warning {
        color: var(--gold) !important;
        animation: pulse-warn 2s ease-in-out infinite;
    }

    html.light-mode .timer-warning { color: #b8860b !important; }

    @keyframes pulse-warn {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* ─── My Stats ───────────────────────────────── */
    .ms-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.375rem 0;
    }

    .ms-row + .ms-row {
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    html.light-mode .ms-row + .ms-row { border-top-color: rgba(0,0,0,0.05); }

    .ms-label { font-size: 0.75rem; color: var(--text-muted); }

    .ms-value {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .ms-value--blue { color: #5b9aff; }

    html.light-mode .ms-value--blue { color: #0055cc; }

    .ms-empty {
        color: var(--text-muted);
        font-size: 0.8125rem;
        padding: 0.5rem 0;
    }

    /* ─── Leaderboard Rows ───────────────────────── */
    .lb-row {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.375rem;
        border-radius: 0.5rem;
        transition: background 150ms ease;
    }

    .lb-row + .lb-row {
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .lb-row + .lb-row { border-top-color: rgba(0,0,0,0.05); }

    .lb-row.is-me {
        background: rgba(0, 85, 204, 0.06);
        border-top-color: transparent;
    }

    .lb-row.is-me + .lb-row { border-top-color: transparent; }

    html.light-mode .lb-row.is-me { background: rgba(0, 51, 127, 0.05); }

    .lb-row__rank {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 800;
        font-size: 0.9375rem;
        width: 1.75rem;
        text-align: center;
        flex-shrink: 0;
        color: var(--text-muted);
    }

    .lb-row__rank--1 { color: var(--gold); }
    .lb-row__rank--2 { color: #c0c0c0; }
    .lb-row__rank--3 { color: #cd7f32; }

    html.light-mode .lb-row__rank--1 { color: #b8860b; }

    .lb-row__avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        background: var(--glass-bg);
        border: 1.5px solid var(--glass-border);
    }

    .lb-row__avatar--1 { border-color: var(--gold); }
    .lb-row__avatar--2 { border-color: #c0c0c0; }
    .lb-row__avatar--3 { border-color: #cd7f32; }

    html.light-mode .lb-row__avatar--1 { border-color: #b8860b; }

    .lb-row__name {
        flex: 1;
        min-width: 0;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lb-row.is-me .lb-row__name { color: var(--text-primary); }

    .lb-row__stat {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 600;
        color: var(--text-muted);
        text-align: right;
        flex-shrink: 0;
    }

    .lb-row__stat--correct { color: #5b9aff; min-width: 2rem; }
    html.light-mode .lb-row__stat--correct { color: #0055cc; }
    .lb-row__stat--acc { min-width: 2.5rem; }
    .lb-row__stat--q { min-width: 1.5rem; }

    .lb-header {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0 0.375rem 0.5rem;
        border-bottom: 1px solid var(--glass-border);
        margin-bottom: 0.25rem;
    }

    .lb-header__col {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
    }

    .lb-header__rank { width: 1.75rem; text-align: center; flex-shrink: 0; }
    .lb-header__avatar { width: 2rem; flex-shrink: 0; }
    .lb-header__name { flex: 1; min-width: 0; }
    .lb-header__stat { text-align: right; flex-shrink: 0; }
    .lb-header__stat--correct { min-width: 2rem; }
    .lb-header__stat--acc { min-width: 2.5rem; }
    .lb-header__stat--q { min-width: 1.5rem; }

    .lb-empty {
        text-align: center;
        color: var(--text-muted);
        padding: 2.5rem 1rem;
        font-size: 0.8125rem;
    }

    .lb-count {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.625rem;
        color: var(--text-muted);
        text-align: center;
        margin-top: 0.75rem;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(255,255,255,0.04);
    }

    html.light-mode .lb-count { border-top-color: rgba(0,0,0,0.04); }

    /* ─── Boost Badge ────────────────────────────── */
    .ls-boost {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.5rem;
        border-radius: 1rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.5625rem;
        font-weight: 700;
        background: rgba(0, 85, 204, 0.12);
        color: #5b9aff;
        border: 1px solid rgba(91, 154, 255, 0.25);
    }

    html.light-mode .ls-boost {
        background: rgba(0, 51, 127, 0.08);
        color: #00337F;
        border-color: rgba(0, 51, 127, 0.18);
    }

    /* ─── Leave Footer ───────────────────────────── */
    .ls-leave {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
    }

    .ls-leave__text {
        flex: 1;
        font-size: 0.8125rem;
        color: var(--text-secondary);
    }

    /* ─── Join Modal ─────────────────────────────── */
    .join-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        animation: joinFadeIn 0.3s ease-out;
    }

    @keyframes joinFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .join-modal {
        width: 100%;
        max-width: 480px;
        background: var(--bg-surface);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 1.5rem 0.5rem 1.5rem 1.5rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 80px rgba(0, 51, 127, 0.1);
        overflow: hidden;
        animation: joinSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    html.light-mode .join-modal {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.10);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12);
    }

    @keyframes joinSlideUp {
        from { opacity: 0; transform: translateY(24px) scale(0.96); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .join-modal::before {
        content: '';
        display: block;
        height: 3px;
        background: linear-gradient(90deg, #00337F, #0055cc, #5b9aff);
    }

    .join-header { padding: 1.5rem 1.75rem 0; }

    .join-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .join-title {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .join-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        margin-top: 0.625rem;
    }

    .join-meta-tag {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.625rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        background: var(--glass-bg);
        color: var(--text-muted);
        border: 1px solid var(--glass-border);
    }

    .join-body { padding: 1.25rem 1.75rem 1.75rem; }

    .join-highlight {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        background: rgba(0, 85, 204, 0.08);
        border: 1px solid rgba(91, 154, 255, 0.2);
        border-radius: 0.75rem;
        margin-bottom: 1.25rem;
    }

    html.light-mode .join-highlight {
        background: rgba(0, 51, 127, 0.05);
        border-color: rgba(0, 51, 127, 0.12);
    }

    .join-highlight__icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(0, 85, 204, 0.15);
        font-size: 0.75rem;
        color: #5b9aff;
    }

    html.light-mode .join-highlight__icon {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
    }

    .join-highlight__text {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .join-highlight__value {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 800;
        color: #5b9aff;
    }

    html.light-mode .join-highlight__value { color: #0055cc; }

    .join-consent {
        padding: 1rem;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .join-consent__title {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.5625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .join-consent__text {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }

    .join-consent__label {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.8125rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .join-consent__label input[type="checkbox"] {
        accent-color: #0055cc;
        margin-top: 0.2rem;
        flex-shrink: 0;
        width: 1rem;
        height: 1rem;
    }

    .join-actions { display: flex; gap: 0.75rem; }
    .join-actions .btn-primary { flex: 1; text-align: center; }
    .join-btn-loading { opacity: 0.7; pointer-events: none; }

    /* ─── Stagger Animation (wie Bookmarks/Exam-History) ─── */
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

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }

    /* ─── Responsive ─────────────────────────────── */
    @media (max-width: 600px) {
        .ls-info-grid { grid-template-columns: 1fr; }
        .timer-display { font-size: 1.75rem; }
        .lb-header__stat--acc,
        .lb-row__stat--acc { display: none; }
    }

    @media (max-width: 480px) {
        .join-header { padding: 1.25rem 1.25rem 0; }
        .join-body { padding: 1rem 1.25rem 1.25rem; }
        .join-actions { flex-direction: column; }
        .ls-leave { flex-direction: column; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="dash-container" x-data="lernsessionLive()">

    {{-- ── Join-Modal ──────────────────────────────────── --}}
    @if(!empty($showJoinModal))
    <div class="join-overlay"
         x-data="joinModal()"
         x-show="open"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="cancelJoin()">

        <div class="join-modal" @click.outside="cancelJoin()">
            <div class="join-header">
                <div class="join-label">Lernsession beitreten</div>
                <h2 class="join-title">{{ $session->title }}</h2>
                <div class="join-meta">
                    <span class="join-meta-tag">{{ $instance->starts_at->format('H:i') }} – {{ $instance->ends_at->format('H:i') }} Uhr</span>
                    <span class="join-meta-tag">Noch {{ $instance->getTimeRemainingMinutes() }} Min</span>
                    <span class="join-meta-tag">{{ $instance->getParticipantCount() }} Teilnehmer</span>
                </div>
            </div>

            <div class="join-body">
                <div class="join-highlight">
                    <span class="join-highlight__icon"><i class="bi bi-lightning-fill"></i></span>
                    <div class="join-highlight__text">
                        Während dieser Session erhältst du <span class="join-highlight__value">+50% XP</span> auf alle beantworteten Fragen.
                        Der Gewinner erhält eine <span class="join-highlight__value">Gold-Belohnungskiste</span>!
                    </div>
                </div>

                <div class="join-consent">
                    <div class="join-consent__title">Datenschutz-Einwilligung</div>
                    <div class="join-consent__text">
                        Wenn du am Ranking teilnimmst, werden dein Name und deine Punktzahl
                        für andere Teilnehmer dieser Session sichtbar. Diese Einwilligung gilt
                        nur für diese Session-Instanz und wird nicht dauerhaft gespeichert.
                    </div>
                    <label class="join-consent__label">
                        <input type="checkbox" x-model="rankingConsent">
                        Ich stimme der Anzeige meiner Daten im Session-Ranking zu.
                        Ohne Zustimmung nehme ich anonym teil.
                    </label>
                </div>

                <div class="join-actions">
                    <button class="btn-primary" :class="submitting && 'join-btn-loading'"
                            @click="submitJoin()" :disabled="submitting">
                        <span x-show="!submitting">Session beitreten</span>
                        <span x-show="submitting" x-cloak>Beitritt läuft...</span>
                    </button>
                    <a href="{{ route('lernsession.index') }}" class="btn-ghost">Zurück</a>
                </div>

                <template x-if="error">
                    <p style="color:#ef4444;font-size:0.8125rem;margin-top:0.75rem;text-align:center;" x-text="error"></p>
                </template>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Header (exakt wie Bookmarks/Exam-History) ───── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Lernsession · Live</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $session->title }}</h1>
        <p class="text-sm" style="color:var(--text-muted);">
            {{ $instance->starts_at->format('d.m.Y') }} · {{ $instance->starts_at->format('H:i') }} – {{ $instance->ends_at->format('H:i') }} Uhr
            <span class="ls-boost" style="margin-left:0.375rem;">+50% XP</span>
        </p>
    </div>

    {{-- ── Gami Pills (wie Bookmarks/Exam-History) ─────── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap:wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue" x-text="participantCount"></div>
            <div class="gami-pill__label">Teilnehmer</div>
        </div>
        <div class="gami-pill" x-show="myStats">
            <div class="gami-pill__value gami-pill__value--gold" x-text="myStats ? '#' + myStats.rank : ''"></div>
            <div class="gami-pill__label">Dein Platz</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue" x-text="formatTime(timeRemaining)"></div>
            <div class="gami-pill__label">Verbleibend</div>
        </div>
        <div class="gami-pill" x-show="myStats">
            <div class="gami-pill__value" x-text="myStats ? myStats.accuracy.toFixed(0) + '%' : ''" style="color:var(--success);-webkit-text-fill-color:var(--success);"></div>
            <div class="gami-pill__label">Genauigkeit</div>
        </div>
    </div>

    <div class="space-y-4">

        {{-- ── Smart Action Card (CTA) ─────────────────── --}}
        <a href="{{ route('practice.all') }}" class="smart-action">
            <div class="smart-action__label">Jetzt mitmachen</div>
            <div class="smart-action__title">Fragen beantworten & XP sammeln</div>
            <div class="smart-action__desc">Beantworte Fragen um im Ranking aufzusteigen. +50% XP Boost aktiv!</div>
            <span class="smart-action__btn">
                Fragen beantworten
                <i class="bi bi-arrow-right"></i>
            </span>
        </a>

        {{-- ── Timer + Meine Stats nebeneinander ──────── --}}
        <div class="ls-info-grid">
            <div class="glass-tl ls-info-card" style="align-items:center;justify-content:center;">
                <div class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;text-align:center;margin-bottom:0.75rem;">Verbleibend</div>
                <div class="timer-display"
                     :class="timeRemaining < 300 ? 'timer-warning' : ''"
                     x-text="formatTime(timeRemaining)"></div>
                <div class="timer-label">Verbleibende Zeit</div>
                <div class="timer-meta">{{ $instance->starts_at->format('H:i') }} – {{ $instance->ends_at->format('H:i') }} Uhr</div>
            </div>

            <div class="glass ls-info-card" style="border-radius:0.75rem;">
                <div class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;margin-bottom:0.75rem;">Meine Stats</div>

                <template x-if="myStats">
                    <div>
                        <div class="ms-row">
                            <span class="ms-label">Position</span>
                            <span class="ms-value" x-text="myStats.rank ? '#' + myStats.rank : '-'"></span>
                        </div>
                        <div class="ms-row">
                            <span class="ms-label">XP verdient</span>
                            <span class="ms-value ms-value--blue" x-text="myStats.xp_earned"></span>
                        </div>
                        <div class="ms-row">
                            <span class="ms-label">Genauigkeit</span>
                            <span class="ms-value" x-text="myStats.accuracy.toFixed(0) + '%'"></span>
                        </div>
                        <div class="ms-row">
                            <span class="ms-label">Fragen</span>
                            <span class="ms-value" x-text="myStats.questions"></span>
                        </div>
                    </div>
                </template>

                <template x-if="!myStats">
                    <p class="ms-empty">Beantworte Fragen um deine Stats zu sehen.</p>
                </template>
            </div>
        </div>

        {{-- ── Live Ranking ────────────────────────────── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                <span class="text-xs uppercase tracking-wider" style="color:var(--text-muted);font-family:'IBM Plex Mono',monospace;font-size:0.5625rem;font-weight:700;">Live-Ranking</span>
                <span style="font-size:0.6875rem;color:var(--text-muted);" x-text="participantCount + ' Teilnehmer'"></span>
            </div>

            <div class="lb-header">
                <span class="lb-header__col lb-header__rank">#</span>
                <span class="lb-header__col lb-header__avatar"></span>
                <span class="lb-header__col lb-header__name">Name</span>
                <span class="lb-header__col lb-header__stat lb-header__stat--correct">Richtig</span>
                <span class="lb-header__col lb-header__stat lb-header__stat--acc">Genau.</span>
                <span class="lb-header__col lb-header__stat lb-header__stat--q">Fr.</span>
            </div>

            <template x-for="(entry, index) in ranking" :key="index">
                <div class="lb-row" :class="entry.is_current_user ? 'is-me' : ''">
                    <span class="lb-row__rank"
                          :class="{
                              'lb-row__rank--1': entry.rank === 1,
                              'lb-row__rank--2': entry.rank === 2,
                              'lb-row__rank--3': entry.rank === 3,
                          }"
                          x-text="entry.rank"></span>
                    <img class="lb-row__avatar"
                         :class="{
                             'lb-row__avatar--1': entry.rank === 1,
                             'lb-row__avatar--2': entry.rank === 2,
                             'lb-row__avatar--3': entry.rank === 3,
                         }"
                         :src="entry.avatar_url"
                         :alt="entry.user_name"
                         loading="lazy">
                    <span class="lb-row__name" x-text="entry.user_name"></span>
                    <span class="lb-row__stat lb-row__stat--correct" x-text="entry.correct"></span>
                    <span class="lb-row__stat lb-row__stat--acc" x-text="entry.accuracy.toFixed(0) + '%'"></span>
                    <span class="lb-row__stat lb-row__stat--q" x-text="entry.questions"></span>
                </div>
            </template>

            <template x-if="ranking.length === 0">
                <div class="lb-empty">
                    Noch keine Teilnehmer. Beantworte Fragen um das Ranking zu starten!
                </div>
            </template>

            <div class="lb-count" x-text="participantCount + ' Teilnehmer aktiv'"></div>
        </div>

        {{-- ── Session verlassen ───────────────────────── --}}
        <div class="glass p-4" style="border-radius:0.75rem;">
            <div class="ls-leave">
                <span class="ls-leave__text">Session verlassen und zurück zur Übersicht?</span>
                <form action="{{ route('lernsession.leave', $instance) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-ghost" onclick="return confirm('Session wirklich verlassen?')">Verlassen</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function lernsessionLive() {
    return {
        ranking: @json($ranking['ranking']),
        myStats: @json($ranking['myStats']),
        timeRemaining: {{ $ranking['timeRemaining'] }},
        participantCount: {{ $ranking['participantCount'] }},
        joined: {{ !empty($showJoinModal) ? 'false' : 'true' }},
        loading: false,

        init() {
            if (!this.joined) return;
            this.startPolling();
        },

        startPolling() {
            setInterval(() => this.fetchRanking(), 12000);
            setInterval(() => {
                if (this.timeRemaining > 0) this.timeRemaining--;
                if (this.timeRemaining <= 0) window.location.reload();
            }, 1000);
        },

        async fetchRanking() {
            if (this.loading) return;
            this.loading = true;
            try {
                const url = '{{ route("lernsession.ranking", $instance) }}' + '?_t=' + Date.now();
                const response = await fetch(url, { cache: 'no-store' });
                if (response.ok) {
                    const data = await response.json();
                    this.ranking = data.ranking;
                    this.myStats = data.myStats;
                    this.timeRemaining = data.timeRemaining;
                    this.participantCount = data.participantCount;
                }
            } catch (e) {
                console.error('Ranking fetch error:', e);
            }
            this.loading = false;
        },

        formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            if (h > 0) return h + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            return m + ':' + String(s).padStart(2, '0');
        }
    }
}

@if(!empty($showJoinModal))
function joinModal() {
    return {
        open: true,
        rankingConsent: false,
        submitting: false,
        error: null,

        async submitJoin() {
            this.submitting = true;
            this.error = null;

            try {
                const url = '{{ route("lernsession.join", $instance) }}';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        ranking_consent: this.rankingConsent ? 1 : 0
                    }),
                    cache: 'no-store'
                });

                if (response.ok || response.redirected) {
                    window.location.reload();
                } else {
                    const data = await response.json().catch(() => null);
                    this.error = data?.message || 'Beitritt fehlgeschlagen. Bitte versuche es erneut.';
                    this.submitting = false;
                }
            } catch (e) {
                this.error = 'Netzwerkfehler. Bitte prüfe deine Verbindung.';
                this.submitting = false;
            }
        },

        cancelJoin() {
            window.location.href = '{{ route("lernsession.index") }}';
        }
    }
}
@endif
</script>
@endsection
