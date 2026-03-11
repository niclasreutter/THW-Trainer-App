@extends('layouts.app')
@section('title', 'Lernsessions - THW Trainer')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    .stats-row {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .stat-pill {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .stat-pill-value {
        font-weight: 700;
        color: var(--text-primary);
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
        min-height: 320px;
        padding: 2rem;
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

    .session-card {
        padding: 1rem;
        border-radius: 0.75rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        margin-bottom: 0.75rem;
    }

    .session-card:last-child {
        margin-bottom: 0;
    }

    .session-card-title {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .session-card-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .session-card-badge {
        display: inline-flex;
        padding: 0.15rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-active {
        background: rgba(34, 197, 94, 0.15);
        color: var(--success);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .badge-scheduled {
        background: rgba(251, 191, 36, 0.15);
        color: var(--gold);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .badge-global {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .badge-ov {
        background: rgba(168, 85, 247, 0.15);
        color: #a78bfa;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .countdown-large {
        font-size: 2rem;
        font-weight: 800;
        color: var(--gold);
        font-variant-numeric: tabular-nums;
    }

    .result-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .result-item:last-child {
        border-bottom: none;
    }

    .result-rank {
        font-weight: 700;
        color: var(--gold);
        min-width: 2rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--text-muted);
    }

    @media (max-width: 900px) {
        .bento-grid {
            grid-template-columns: 1fr;
        }
        .bento-main, .bento-wide {
            grid-column: span 1;
            grid-row: span 1;
            min-height: auto;
        }
        .dashboard-container {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Lern<span>sessions</span></h1>
        <p class="page-subtitle">Gemeinsam lernen, gegeneinander messen. 50% mehr XP in jeder Session.</p>
    </header>

    <div class="stats-row">
        <div class="stat-pill">
            Aktiv <span class="stat-pill-value">{{ $activeSessions->count() }}</span>
        </div>
        <div class="stat-pill">
            Bevorstehend <span class="stat-pill-value">{{ $upcomingSessions->count() }}</span>
        </div>
        <div class="stat-pill">
            Gewonnen <span class="stat-pill-value">{{ $wonCount }}</span>
        </div>
    </div>

    @if(auth()->user()->useroll === 'admin' || auth()->user()->ortsverbände()->wherePivot('role', 'ausbildungsbeauftragter')->exists())
        <div style="margin-bottom: 1.5rem;">
            <a href="{{ route('lernsession.create') }}" class="btn-primary">Session erstellen</a>
        </div>
    @endif

    <div class="bento-grid">
        {{-- Aktive Session (Hauptcard) --}}
        <div class="glass-gold bento-main">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-primary);">Aktive Sessions</h3>

            @if($activeSessions->isNotEmpty())
                @foreach($activeSessions as $activeInstance)
                    @php $activeSession = $activeInstance->learningSession; @endphp
                    <div class="session-card">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <div>
                                <div class="session-card-title">{{ $activeSession->title }}</div>
                                <div class="session-card-meta">
                                    <span class="session-card-badge {{ $activeSession->isGlobal() ? 'badge-global' : 'badge-ov' }}">
                                        {{ $activeSession->isGlobal() ? 'Global' : $activeSession->ortsverband->name }}
                                    </span>
                                    {{ $activeInstance->getParticipantCount() }} Teilnehmer
                                </div>
                            </div>
                            <div class="countdown-large" x-data="{ remaining: {{ $activeInstance->getTimeRemainingSeconds() }} }"
                                 x-init="setInterval(() => { if(remaining > 0) remaining-- }, 1000)"
                                 x-text="Math.floor(remaining/3600) + ':' + String(Math.floor((remaining%3600)/60)).padStart(2,'0') + ':' + String(remaining%60).padStart(2,'0')">
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                            @if($currentParticipation && $currentParticipation->instance_id == $activeInstance->id)
                                <a href="{{ route('lernsession.live', $activeInstance) }}" class="btn-primary btn-sm">Zur Session</a>
                            @else
                                <a href="{{ route('lernsession.live', $activeInstance) }}" class="btn-primary btn-sm">Teilnehmen</a>
                            @endif
                            <a href="{{ route('lernsession.show', $activeSession) }}" class="btn-ghost btn-sm">Details</a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <p>Keine aktive Session gerade.</p>
                </div>
            @endif
        </div>

        {{-- Bevorstehende Sessions --}}
        <div class="glass-tl bento-side">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-primary);">Bevorstehend</h3>
            @forelse($upcomingSessions->take(3) as $upcoming)
                @php $upSession = $upcoming->learningSession; @endphp
                <div class="session-card">
                    <div class="session-card-title">{{ $upSession->title }}</div>
                    <div class="session-card-meta">
                        {{ $upcoming->starts_at->format('d.m. H:i') }} Uhr
                        <span class="session-card-badge badge-scheduled">Geplant</span>
                    </div>
                </div>
            @empty
                <p style="color: var(--text-muted); font-size: 0.85rem;">Keine geplanten Sessions.</p>
            @endforelse
        </div>

        {{-- Letzte Ergebnisse --}}
        <div class="glass-br bento-side">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-primary);">Meine Ergebnisse</h3>
            @forelse($recentResults->take(3) as $result)
                <div class="result-item">
                    <div>
                        <span class="result-rank">#{{ $result->final_rank }}</span>
                        <span style="font-size: 0.85rem; color: var(--text-secondary);">
                            {{ $result->instance->learningSession->title ?? 'Session' }}
                        </span>
                    </div>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">{{ $result->xp_earned }} XP</span>
                </div>
            @empty
                <p style="color: var(--text-muted); font-size: 0.85rem;">Noch keine Ergebnisse.</p>
            @endforelse
        </div>

        {{-- Alle Sessions --}}
        <div class="glass bento-wide">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-primary);">Alle Sessions</h3>
            @forelse($sessions as $session)
                <div class="session-card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="session-card-title">{{ $session->title }}</div>
                        <div class="session-card-meta">
                            <span class="session-card-badge {{ $session->isGlobal() ? 'badge-global' : 'badge-ov' }}">
                                {{ $session->isGlobal() ? 'Global' : ($session->ortsverband->name ?? 'OV') }}
                            </span>
                            @if($session->schedule_type === 'recurring')
                                Wiederkehrend
                            @else
                                {{ $session->starts_at ? $session->starts_at->format('d.m.Y H:i') : '' }}
                            @endif
                            &middot; Erstellt von {{ $session->creator->name }}
                        </div>
                    </div>
                    <a href="{{ route('lernsession.show', $session) }}" class="btn-ghost btn-sm">Details</a>
                </div>
            @empty
                <div class="empty-state">
                    <p>Noch keine Sessions erstellt.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
