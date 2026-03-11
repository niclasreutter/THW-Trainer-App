@extends('layouts.app')
@section('title', $session->title . ' - Live')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2rem;
        padding-top: 1rem;
        max-width: 600px;
    }

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

    /* Leaderboard */
    .lb-table {
        width: 100%;
        border-collapse: collapse;
    }

    .lb-table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .lb-table td {
        padding: 0.6rem 0.75rem;
        font-size: 0.88rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        color: var(--text-secondary);
    }

    .lb-table tr.is-me td {
        color: var(--text-primary);
        font-weight: 600;
        background: rgba(251, 191, 36, 0.05);
    }

    .lb-rank {
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

    /* Timer */
    .timer-display {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--gold);
        font-variant-numeric: tabular-nums;
        text-align: center;
        line-height: 1.2;
    }

    .timer-label {
        text-align: center;
        font-size: 0.8rem;
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

    /* My Stats */
    .my-stat {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .my-stat:last-child { border-bottom: none; }

    .my-stat-label {
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .my-stat-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .boost-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.6rem;
        border-radius: 1rem;
        font-size: 0.75rem;
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

    @media (max-width: 900px) {
        .bento-grid { grid-template-columns: 1fr; }
        .bento-main, .bento-wide { grid-column: span 1; grid-row: span 1; min-height: auto; }
        .dashboard-container { padding: 1rem; }
        .timer-display { font-size: 1.8rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container" x-data="lernsessionLive()">
    <header class="dashboard-header">
        <h1 class="page-title">{{ $session->title }} <span>Live</span></h1>
        <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.5rem;">
            <span class="boost-badge">+50% XP Boost aktiv</span>
        </div>
    </header>

    <div class="bento-grid">
        {{-- Live Leaderboard (Hauptcard) --}}
        <div class="glass-gold bento-main">
            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-primary);">Live-Ranking</h3>

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
            <div class="timer-display" :class="timeRemaining < 300 ? 'timer-warning' : ''"
                 x-text="formatTime(timeRemaining)">
            </div>
            <div class="timer-label">Verbleibende Zeit</div>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.82rem; color: var(--text-muted);">
                {{ $instance->starts_at->format('H:i') }} - {{ $instance->ends_at->format('H:i') }} Uhr
            </div>
        </div>

        {{-- Meine Stats --}}
        <div class="glass-br bento-side">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-primary);">Meine Stats</h3>

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
        loading: false,

        init() {
            // Ranking alle 12 Sekunden aktualisieren
            setInterval(() => this.fetchRanking(), 12000);
            // Countdown jede Sekunde
            setInterval(() => {
                if (this.timeRemaining > 0) this.timeRemaining--;
                if (this.timeRemaining <= 0) {
                    // Session beendet - Seite neu laden
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
</script>
@endsection
