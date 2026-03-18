@extends('layouts.app')
@section('title', $session->title . ' - Live')

@push('styles')
{{-- DSGVO-konforme Fonts via bunny.net (EU-hosted, kein Google-Tracking) --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Container & Header (Dashboard-Stil) ─── */
    .ls-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    .dashboard-greeting {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .dashboard-greeting span {
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .dashboard-subtitle {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    .section-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
    }

    /* ─── Bento Grid ─────────────────────────────── */
    .bento-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .bento-main {
        grid-column: span 2;
        grid-row: span 2;
        min-height: 380px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .bento-side {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
    }

    .bento-wide {
        grid-column: span 3;
        padding: 1.5rem;
    }

    /* ─── Leaderboard Table ──────────────────────── */
    .lb-table {
        width: 100%;
        border-collapse: collapse;
    }

    .lb-table th {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    html.light-mode .lb-table th {
        border-bottom-color: rgba(0,0,0,0.08);
    }

    .lb-table td {
        padding: 0.6rem 0.75rem;
        font-size: 0.88rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        color: var(--text-secondary);
    }

    html.light-mode .lb-table td {
        border-bottom-color: rgba(0,0,0,0.04);
    }

    .lb-table tr.is-me td {
        color: var(--text-primary);
        font-weight: 600;
        background: rgba(251, 191, 36, 0.05);
    }

    .lb-rank {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 700;
        min-width: 2rem;
    }

    .lb-rank-1 { color: var(--gold); }
    .lb-rank-2 { color: #c0c0c0; }
    .lb-rank-3 { color: #cd7f32; }

    .lb-xp {
        font-weight: 600;
        color: var(--gold);
    }

    /* ─── Timer ──────────────────────────────────── */
    .timer-display {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--gold);
        font-variant-numeric: tabular-nums;
        text-align: center;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .timer-label {
        font-family: 'IBM Plex Mono', monospace;
        text-align: center;
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    .timer-warning {
        animation: pulse-warning 2s ease-in-out infinite;
    }

    @keyframes pulse-warning {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    /* ─── My Stats ───────────────────────────────── */
    .my-stat {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    html.light-mode .my-stat { border-bottom-color: rgba(0,0,0,0.05); }

    .my-stat:last-child { border-bottom: none; }

    .my-stat-label {
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .my-stat-value {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .boost-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.6rem;
        border-radius: 1rem;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 700;
        background: rgba(251, 191, 36, 0.15);
        color: var(--gold);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .participant-count {
        font-size: 0.85rem;
        color: var(--text-muted);
        text-align: center;
        margin-top: 0.5rem;
    }

    /* ─── Join Modal (Popup-Overlay) ─────────────── */
    .join-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.65);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        animation: join-overlay-in 0.3s ease-out;
    }

    @keyframes join-overlay-in {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .join-modal {
        width: 100%;
        max-width: 480px;
        border-radius: 1.25rem;
        overflow: hidden;
        animation: join-modal-in 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes join-modal-in {
        from { opacity: 0; transform: translateY(24px) scale(0.96); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .join-modal-header {
        padding: 1.75rem 1.75rem 0;
    }

    .join-modal-emoji {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .join-modal-title {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .join-modal-title span {
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .join-modal-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .join-modal-meta-item {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 2rem;
        background: rgba(255,255,255,0.06);
        color: var(--text-muted);
        border: 1px solid rgba(255,255,255,0.08);
    }

    html.light-mode .join-modal-meta-item {
        background: rgba(0,0,0,0.04);
        border-color: rgba(0,0,0,0.08);
    }

    .join-modal-body {
        padding: 1.25rem 1.75rem 1.75rem;
    }

    /* Boost-Highlight */
    .join-boost {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        background: rgba(251, 191, 36, 0.08);
        border: 1px solid rgba(251, 191, 36, 0.2);
        border-radius: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .join-boost-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .join-boost-text {
        font-size: 0.85rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .join-boost-value {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 800;
        color: var(--gold);
    }

    /* Consent Box */
    .join-consent {
        padding: 1rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.75rem;
        margin-bottom: 1.25rem;
    }

    html.light-mode .join-consent {
        background: rgba(0,0,0,0.02);
        border-color: rgba(0,0,0,0.08);
    }

    .join-consent-title {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .join-consent-text {
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }

    .join-consent-label {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.82rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .join-consent-label input[type="checkbox"] {
        accent-color: var(--gold);
        margin-top: 0.2rem;
        flex-shrink: 0;
        width: 1rem;
        height: 1rem;
    }

    /* Action Buttons */
    .join-actions {
        display: flex;
        gap: 0.75rem;
    }

    .join-actions .btn-primary {
        flex: 1;
        text-align: center;
        font-family: 'IBM Plex Mono', monospace;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
    }

    .join-actions .btn-ghost {
        font-family: 'IBM Plex Mono', monospace;
        font-weight: 600;
        font-size: 0.8rem;
    }

    /* Loading State */
    .join-btn-loading {
        opacity: 0.7;
        pointer-events: none;
    }

    @media (max-width: 900px) {
        .bento-grid { grid-template-columns: 1fr; }
        .bento-main, .bento-wide { grid-column: span 1; grid-row: span 1; min-height: auto; }
        .ls-container { padding: 1rem; }
        .timer-display { font-size: 1.8rem; }
        .dashboard-greeting { font-size: 1.4rem; }
    }

    @media (max-width: 480px) {
        .join-modal-header { padding: 1.25rem 1.25rem 0; }
        .join-modal-body { padding: 1rem 1.25rem 1.25rem; }
        .join-actions { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="ls-container" x-data="lernsessionLive()">

    {{-- ── Join-Modal (Popup) ─────────────────────────────── --}}
    @if(!empty($showJoinModal))
    <div class="join-overlay"
         x-data="joinModal()"
         x-show="open"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="cancelJoin()">

        <div class="glass-gold join-modal" @click.outside="cancelJoin()">
            <div class="join-modal-header">
                <div class="join-modal-emoji">🎓</div>
                <h2 class="join-modal-title">{{ $session->title }} <span>beitreten</span></h2>
                <div class="join-modal-meta">
                    <span class="join-modal-meta-item">
                        🕐 {{ $instance->starts_at->format('H:i') }} – {{ $instance->ends_at->format('H:i') }} Uhr
                    </span>
                    <span class="join-modal-meta-item">
                        ⏳ Noch {{ $instance->getTimeRemainingMinutes() }} Min
                    </span>
                    <span class="join-modal-meta-item">
                        👥 {{ $instance->getParticipantCount() }} Teilnehmer
                    </span>
                </div>
            </div>

            <div class="join-modal-body">
                <div class="join-boost">
                    <span class="join-boost-icon">⚡</span>
                    <div class="join-boost-text">
                        Während dieser Session erhältst du <span class="join-boost-value">+50% XP</span> auf alle beantworteten Fragen.
                        Der Gewinner erhält eine <span class="join-boost-value">Gold-Belohnungskiste</span>!
                    </div>
                </div>

                <div class="join-consent">
                    <div class="join-consent-title">🔒 Datenschutz-Einwilligung</div>
                    <div class="join-consent-text">
                        Wenn du am Ranking teilnimmst, werden dein Name und deine Punktzahl für andere
                        Teilnehmer dieser Session sichtbar. Diese Einwilligung gilt nur für diese
                        Session-Instanz und wird nicht dauerhaft gespeichert.
                    </div>
                    <label class="join-consent-label">
                        <input type="checkbox" x-model="rankingConsent">
                        Ich stimme der Anzeige meiner Daten im Session-Ranking zu.
                        Ohne Zustimmung nehme ich anonym teil.
                    </label>
                </div>

                <div class="join-actions">
                    <button class="btn-primary" :class="submitting && 'join-btn-loading'"
                            @click="submitJoin()" :disabled="submitting">
                        <span x-show="!submitting">🚀 Session beitreten</span>
                        <span x-show="submitting" x-cloak>Beitritt läuft…</span>
                    </button>
                    <a href="{{ route('lernsession.index') }}" class="btn-ghost">Abbrechen</a>
                </div>

                <template x-if="error">
                    <p style="color: #ef4444; font-size: 0.82rem; margin-top: 0.75rem; text-align: center;" x-text="error"></p>
                </template>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Page Header (Dashboard-Stil) ───────────────────── --}}
    <header class="dashboard-header">
        <h1 class="dashboard-greeting">🏆 <span>{{ $session->title }}</span> Live</h1>
        <p class="dashboard-subtitle">
            {{ $instance->starts_at->format('d.m.Y') }} · {{ $instance->starts_at->format('H:i') }} – {{ $instance->ends_at->format('H:i') }} Uhr
        </p>
        <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.5rem;">
            <span class="boost-badge">⚡ +50% XP Boost aktiv</span>
        </div>
    </header>

    {{-- ── Bento Grid ─────────────────────────────────────── --}}
    <div class="bento-grid">
        {{-- Live Leaderboard (Hauptcard) --}}
        <div class="glass-gold bento-main">
            <div class="section-label">Live-Ranking</div>

            <div style="flex: 1; overflow-y: auto;">
                <table class="lb-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Richtig</th>
                            <th>Genauigkeit</th>
                            <th>Fragen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(entry, index) in ranking" :key="index">
                            <tr :class="entry.is_current_user ? 'is-me' : ''">
                                <td class="lb-rank" :class="'lb-rank-' + entry.rank" x-text="entry.rank"></td>
                                <td x-text="entry.user_name"></td>
                                <td class="lb-xp" x-text="entry.correct"></td>
                                <td x-text="entry.accuracy.toFixed(0) + '%'"></td>
                                <td x-text="entry.questions"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <template x-if="ranking.length === 0">
                    <p style="text-align: center; color: var(--text-muted); padding: 2rem;">
                        Noch keine Teilnehmer. Beantworte Fragen um das Ranking zu starten!
                    </p>
                </template>
            </div>

            <p class="participant-count" x-text="participantCount + ' Teilnehmer'"></p>
        </div>

        {{-- Timer --}}
        <div class="glass-tl bento-side" style="justify-content: center; align-items: center;">
            <div class="section-label" style="text-align: center;">Verbleibend</div>
            <div class="timer-display" :class="timeRemaining < 300 ? 'timer-warning' : ''"
                 x-text="formatTime(timeRemaining)">
            </div>
            <div class="timer-label">Verbleibende Zeit</div>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.82rem; color: var(--text-muted);">
                {{ $instance->starts_at->format('H:i') }} – {{ $instance->ends_at->format('H:i') }} Uhr
            </div>
        </div>

        {{-- Meine Stats --}}
        <div class="glass-br bento-side">
            <div class="section-label">Meine Stats</div>

            <template x-if="myStats">
                <div>
                    <div class="my-stat">
                        <span class="my-stat-label">Position</span>
                        <span class="my-stat-value" x-text="myStats.rank ? '#' + myStats.rank : '-'"></span>
                    </div>
                    <div class="my-stat">
                        <span class="my-stat-label">XP verdient</span>
                        <span class="my-stat-value" style="color: var(--gold);" x-text="myStats.xp_earned"></span>
                    </div>
                    <div class="my-stat">
                        <span class="my-stat-label">Genauigkeit</span>
                        <span class="my-stat-value" x-text="myStats.accuracy.toFixed(0) + '%'"></span>
                    </div>
                    <div class="my-stat">
                        <span class="my-stat-label">Fragen</span>
                        <span class="my-stat-value" x-text="myStats.questions"></span>
                    </div>
                </div>
            </template>

            <template x-if="!myStats">
                <p style="color: var(--text-muted); font-size: 0.85rem;">Beantworte Fragen um deine Stats zu sehen.</p>
            </template>
        </div>

        {{-- Aktion --}}
        <div class="glass bento-wide" style="text-align: center;">
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                Beantworte Fragen um XP zu sammeln und im Ranking aufzusteigen.
            </p>
            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                <a href="{{ route('practice.all') }}" class="btn-primary">Fragen beantworten</a>
                <form action="{{ route('lernsession.leave', $instance) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-ghost" onclick="return confirm('Session wirklich verlassen?')">Session verlassen</button>
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
            if (!this.joined) return; // Kein Polling wenn noch nicht beigetreten
            this.startPolling();
        },

        startPolling() {
            // Ranking alle 12 Sekunden aktualisieren
            setInterval(() => this.fetchRanking(), 12000);
            // Countdown jede Sekunde
            setInterval(() => {
                if (this.timeRemaining > 0) this.timeRemaining--;
                if (this.timeRemaining <= 0) {
                    window.location.reload();
                }
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
            if (h > 0) {
                return h + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }
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
                    // Seite neu laden um Live-Daten zu bekommen
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
