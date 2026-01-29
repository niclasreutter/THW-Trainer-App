@extends('layouts.app')

@section('title', $ortsverband->name . ' - Mitglieder')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Mitglieder <span>verwalten</span></h1>
        <p class="page-subtitle">{{ $ortsverband->name }}</p>
    </header>

    @if(session('success'))
    <div class="alert-compact glass-success" style="margin-bottom: 1.5rem;">
        <i class="bi bi-check-circle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('success') }}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-compact glass-error" style="margin-bottom: 1.5rem;">
        <i class="bi bi-exclamation-triangle alert-compact-icon"></i>
        <div class="alert-compact-content">
            <div class="alert-compact-title">{{ session('error') }}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem;">&times;</button>
    </div>
    @endif

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-people-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ $memberProgress->count() }}</div>
                <div class="stat-pill-label">Mitglieder</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-person-badge-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ $ausbilderProgress->count() }}</div>
                <div class="stat-pill-label">Ausbilder</div>
            </div>
        </div>
        <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="btn-primary btn-sm" style="margin-left: auto;">
            Einladen
        </a>
    </div>

    <!-- Bento Grid -->
    <div class="bento-grid-members">
        <!-- Ausbilder Section -->
        @if($ausbilderProgress->count() > 0)
        <div class="glass-gold bento-ausbilder">
            <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                <h2 class="section-title" style="font-size: 1.1rem;">Ausbilder</h2>
            </div>

            @foreach($ausbilderProgress as $member)
            <div class="glass-subtle member-card-item">
                <div style="display: flex; align-items: start; gap: 1rem;">
                    <div class="member-avatar">{{ strtoupper(substr($member['user']->name, 0, 1)) }}</div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            {{ $member['user']->name }}
                            <span class="badge-thw" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">Ausbilder</span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            {{ $member['user']->email }}
                            @if($member['user']->pivot->joined_at)
                                <br>Beigetreten: {{ \Carbon\Carbon::parse($member['user']->pivot->joined_at)->format('d.m.Y') }}
                            @endif
                        </div>
                    </div>
                    @if($member['user']->id !== auth()->id())
                    <form action="{{ route('ortsverband.members.role', [$ortsverband, $member['user']]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="role" value="member">
                        <button type="submit" class="btn-ghost btn-sm" onclick="return confirm('Möchtest du diesen Ausbilder zum Mitglied degradieren?')">
                            Zum Mitglied
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Mitglieder Section -->
        <div class="glass-tl bento-mitglieder">
            <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                <h2 class="section-title" style="font-size: 1.1rem;">Mitglieder ({{ $memberProgress->count() }})</h2>
            </div>

            <div class="members-list">
                @forelse($memberProgress as $member)
                <div class="glass-subtle member-card-item">
                    <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 0.75rem;">
                        <div class="member-avatar">{{ strtoupper(substr($member['user']->name, 0, 1)) }}</div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">
                                {{ $member['user']->name }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $member['user']->email }}
                                @if($member['last_activity'])
                                    <br>Zuletzt: {{ is_string($member['last_activity']) ? \Carbon\Carbon::parse($member['last_activity'])->diffForHumans() : $member['last_activity']->diffForHumans() }}
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                            <form action="{{ route('ortsverband.members.role', [$ortsverband, $member['user']]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role" value="ausbildungsbeauftragter">
                                <button type="submit" class="btn-secondary btn-sm" onclick="return confirm('Möchtest du dieses Mitglied zum Ausbilder befördern?')">
                                    Befördern
                                </button>
                            </form>
                            <form action="{{ route('ortsverband.members.remove', [$ortsverband, $member['user']]) }}" method="POST" onsubmit="return confirm('Möchtest du dieses Mitglied wirklich entfernen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">Entfernen</button>
                            </form>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div style="margin-bottom: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                            <span>Theorie-Fortschritt</span>
                            <span>{{ $member['theory_progress_count'] }}/268 ({{ $member['theory_progress_percent'] }}%)</span>
                        </div>
                        <div style="height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; background: var(--gradient-gold); width: {{ $member['theory_progress_percent'] }}%; border-radius: 2px;"></div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <div class="mini-stat">
                            <span class="mini-stat-value">{{ $member['exams_passed'] }}/5</span>
                            <span class="mini-stat-label">Prüfungen</span>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-stat-value">{{ $member['streak'] }}</span>
                            <span class="mini-stat-label">Streak</span>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-stat-value">{{ $member['level'] }}</span>
                            <span class="mini-stat-label">Level</span>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-stat-value">{{ number_format($member['points']) }}</span>
                            <span class="mini-stat-label">Punkte</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding: 2rem;">
                    <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                    <h3 class="empty-state-title">Keine Mitglieder</h3>
                    <p class="empty-state-desc">Lade Mitglieder über Einladungslinks ein.</p>
                    <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="btn-primary btn-sm">Einladung erstellen</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Back Link -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('ortsverband.dashboard', $ortsverband) }}" class="btn-ghost btn-sm">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>

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

    .bento-grid-members {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .bento-ausbilder {
        padding: 1.5rem;
    }

    .bento-mitglieder {
        padding: 1.5rem;
    }

    .members-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 600px;
        overflow-y: auto;
    }

    .member-card-item {
        padding: 1rem;
        border-radius: 0.75rem;
    }

    .member-avatar {
        width: 40px;
        height: 40px;
        background: var(--gradient-gold);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e3a5f;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .mini-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: rgba(255, 255, 255, 0.05);
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        min-width: 60px;
    }

    .mini-stat-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .mini-stat-label {
        font-size: 0.65rem;
        color: var(--text-muted);
        text-transform: uppercase;
    }

    .alert-compact {
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-compact-icon { font-size: 1.25rem; }
    .alert-compact-content { flex: 1; }
    .alert-compact-title { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }

    .empty-state {
        text-align: center;
    }

    .empty-state-icon {
        font-size: 2rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        opacity: 0.6;
    }

    .empty-state-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .empty-state-desc {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
        .member-card-item > div:first-child {
            flex-wrap: wrap;
        }
        .member-card-item > div:first-child > div:last-child {
            width: 100%;
            margin-top: 0.75rem;
        }
    }
</style>
@endpush
@endsection
