@extends('layouts.app')

@section('title', $ortsverband->name . ' - Dashboard')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Ausbilder <span>{{ $ortsverband->name }}</span></h1>
        <p class="page-subtitle">Überblick über den Lernfortschritt deiner Mitglieder</p>
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

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-people-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ $stats['total_members'] ?? 0 }}</div>
                <div class="stat-pill-label">Mitglieder</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-activity"></i></span>
            <div>
                <div class="stat-pill-value">{{ $stats['active_members'] ?? 0 }}</div>
                <div class="stat-pill-label">Aktiv (7 Tage)</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-info"><i class="bi bi-book"></i></span>
            <div>
                <div class="stat-pill-value">{{ $stats['avg_theory'] ?? 0 }}%</div>
                <div class="stat-pill-label">Theorie</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-bullseye"></i></span>
            <div>
                <div class="stat-pill-value">{{ $stats['avg_exams'] ?? 0 }}</div>
                <div class="stat-pill-label">Prüfungen</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-warning"><i class="bi bi-fire"></i></span>
            <div>
                <div class="stat-pill-value">{{ $stats['avg_streak'] ?? 0 }}</div>
                <div class="stat-pill-label">Streak</div>
            </div>
        </div>
    </div>

    <!-- Bento Grid Layout -->
    <div class="bento-grid">
        <!-- Rangliste (Main) -->
        <div class="glass-gold bento-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                <div class="section-header" style="margin-bottom: 0; padding-left: 0; border-left: none;">
                    <h2 class="section-title" style="font-size: 1.25rem;">Rangliste</h2>
                </div>
                <div style="display: flex; gap: 0.5rem; background: rgba(255, 255, 255, 0.1); padding: 0.25rem; border-radius: 0.5rem;">
                    <form action="{{ route('ortsverband.toggle-ranking', $ortsverband) }}" method="POST" style="margin: 0; display: inline;">
                        @csrf
                        @if(!$ortsverband->ranking_visible)
                            <button type="submit" class="btn-ghost btn-sm" style="padding: 0.4rem 0.75rem;">Alle</button>
                        @else
                            <span class="btn-primary btn-sm" style="padding: 0.4rem 0.75rem;">Alle</span>
                        @endif
                    </form>
                    <form action="{{ route('ortsverband.toggle-ranking', $ortsverband) }}" method="POST" style="margin: 0; display: inline;">
                        @csrf
                        @if($ortsverband->ranking_visible)
                            <button type="submit" class="btn-ghost btn-sm" style="padding: 0.4rem 0.75rem;">Nur Ausbilder</button>
                        @else
                            <span class="btn-primary btn-sm" style="padding: 0.4rem 0.75rem;">Nur Ausbilder</span>
                        @endif
                    </form>
                </div>
            </div>

            <div style="flex: 1; overflow-y: auto; max-height: 400px;">
                @forelse($memberProgress->take(10) as $index => $member)
                <div class="glass-subtle" style="display: flex; align-items: center; gap: 1rem; padding: 0.875rem 1rem; margin-bottom: 0.5rem; border-radius: 0.75rem;">
                    <div style="font-size: 1.25rem; min-width: 36px; text-align: center; font-weight: 700;">
                        @if($index === 0)
                            <span style="color: #fbbf24;">1</span>
                        @elseif($index === 1)
                            <span style="color: #94a3b8;">2</span>
                        @elseif($index === 2)
                            <span style="color: #cd7f32;">3</span>
                        @else
                            <span style="color: var(--text-muted);">{{ $index + 1 }}</span>
                        @endif
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $member['user']->name }}
                            </span>
                            @if($member['role'] === 'ausbildungsbeauftragter')
                                <span class="badge-thw" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">Ausbilder</span>
                            @endif
                        </div>
                        <div style="display: flex; gap: 1rem; font-size: 0.75rem; color: var(--text-secondary);">
                            <span><i class="bi bi-book"></i> {{ $member['theory_progress_percent'] }}%</span>
                            <span><i class="bi bi-bullseye"></i> {{ $member['exams_passed'] }}/5</span>
                            <span><i class="bi bi-fire text-warning"></i> {{ $member['streak'] }}d</span>
                            <span><i class="bi bi-lightning-charge"></i> Lvl {{ $member['level'] }}</span>
                        </div>
                        <div style="height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; margin-top: 0.5rem; overflow: hidden;">
                            <div style="height: 100%; background: var(--gradient-gold); width: {{ $member['theory_progress_percent'] }}%; border-radius: 2px;"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                    <h3 class="empty-state-title">Keine Mitglieder</h3>
                    <p class="empty-state-desc">Lade Mitglieder ein, um ihre Fortschritte zu verfolgen</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Schwachstellen-Analyse (Side) -->
        <div class="glass-tl bento-side" style="grid-row: span 2;">
            <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                <h3 class="section-title" style="font-size: 1rem;">Schwachstellen</h3>
            </div>

            @if($weaknesses['weak_sections']->isNotEmpty())
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 0.5rem;">Schwierigste Abschnitte</div>
                    @php
                        $sectionNames = [
                            1 => 'THW im Gefüge',
                            2 => 'Rettung & Bergung',
                            3 => 'Leinen & Seile',
                            4 => 'Holz/Gestein/Metall',
                            5 => 'Leitern',
                            6 => 'Strom & Licht',
                            7 => 'Wasser',
                            8 => 'Einsatzgrundlagen',
                            9 => 'Wasser (erw.)',
                            10 => 'Rettung (Grundl.)'
                        ];
                    @endphp
                    @foreach($weaknesses['weak_sections']->take(3) as $section)
                    <div class="glass-warning" style="padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary);">LA {{ $section['section'] }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">{{ $section['total_attempts'] }} Versuche</div>
                            </div>
                            <div style="font-size: 1rem; font-weight: 800; color: #f59e0b;">{{ $section['success_rate'] }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            @if($weaknesses['common_errors']->isNotEmpty())
                <div>
                    <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 0.5rem;">Häufigste Fehler</div>
                    @foreach($weaknesses['common_errors']->take(3) as $error)
                        @if($error['question'])
                        <div class="glass-error" style="padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.5rem;">
                                <div style="font-size: 0.8rem; color: var(--text-primary); flex: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ Str::limit($error['question']->frage, 60) }}
                                </div>
                                <div style="font-size: 0.9rem; font-weight: 800; color: #ef4444; white-space: nowrap;">{{ $error['error_count'] }}x</div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($weaknesses['weak_sections']->isEmpty() && $weaknesses['common_errors']->isEmpty())
            <div class="empty-state" style="padding: 1.5rem;">
                <div class="empty-state-icon"><i class="bi bi-graph-up"></i></div>
                <p class="empty-state-desc" style="margin-bottom: 0;">Noch keine Daten verfügbar</p>
            </div>
            @endif
        </div>

        <!-- Schnellzugriff -->
        <div class="glass-slash bento-wide">
            <div class="section-header" style="margin-bottom: 1rem; padding-left: 0.75rem;">
                <h3 class="section-title" style="font-size: 1rem;">Schnellzugriff</h3>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">
                <a href="{{ route('ortsverband.members', $ortsverband) }}" class="glass-subtle hover-lift" style="padding: 1.25rem; text-decoration: none; text-align: center; border-radius: 1rem 0.5rem 0.75rem 0.75rem;">
                    <i class="bi bi-people" style="font-size: 1.5rem; color: var(--gold-start); display: block; margin-bottom: 0.5rem;"></i>
                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem;">Mitglieder</span>
                </a>
                <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="glass-subtle hover-lift" style="padding: 1.25rem; text-decoration: none; text-align: center; border-radius: 0.5rem 1rem 0.75rem 0.75rem;">
                    <i class="bi bi-collection" style="font-size: 1.5rem; color: var(--gold-start); display: block; margin-bottom: 0.5rem;"></i>
                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem;">Lernpools</span>
                </a>
                <a href="{{ route('ortsverband.invitations.index', $ortsverband) }}" class="glass-subtle hover-lift" style="padding: 1.25rem; text-decoration: none; text-align: center; border-radius: 0.75rem;">
                    <i class="bi bi-link-45deg" style="font-size: 1.5rem; color: var(--gold-start); display: block; margin-bottom: 0.5rem;"></i>
                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem;">Einladungen</span>
                </a>
                <a href="{{ route('ortsverband.edit', $ortsverband) }}" class="glass-subtle hover-lift" style="padding: 1.25rem; text-decoration: none; text-align: center; border-radius: 0.5rem 0.75rem 1rem 0.75rem;">
                    <i class="bi bi-gear" style="font-size: 1.5rem; color: var(--gold-start); display: block; margin-bottom: 0.5rem;"></i>
                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem;">Einstellungen</span>
                </a>
                <a href="{{ route('ortsverband.index') }}" class="glass-subtle hover-lift" style="padding: 1.25rem; text-decoration: none; text-align: center; border-radius: 0.75rem 0.5rem 0.75rem 1rem;">
                    <i class="bi bi-arrow-left" style="font-size: 1.5rem; color: var(--text-secondary); display: block; margin-bottom: 0.5rem;"></i>
                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem;">Zurück</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bento-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        grid-template-rows: auto;
        gap: 1rem;
    }

    .bento-main {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .bento-side {
        padding: 1.25rem;
    }

    .bento-wide {
        grid-column: span 2;
        padding: 1.5rem;
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
        padding: 2rem;
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
    }

    @media (max-width: 900px) {
        .bento-grid {
            grid-template-columns: 1fr;
        }
        .bento-wide { grid-column: span 1; }
        .bento-side { grid-row: span 1; }
    }
</style>
@endpush
@endsection
