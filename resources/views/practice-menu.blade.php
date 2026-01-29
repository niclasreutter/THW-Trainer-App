@extends('layouts.app')
@section('title', 'Übungsmenü - THW Trainer')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    /* Ensure all children don't overflow */
    .dashboard-container * {
        max-width: 100%;
        box-sizing: border-box;
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    /* Bento Grid */
    .bento-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
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
        justify-content: center;
    }

    .bento-wide {
        grid-column: span 3;
        padding: 1.5rem;
    }

    @media (max-width: 900px) {
        .bento-grid {
            grid-template-columns: 1fr 1fr;
        }
        .bento-main { grid-column: span 2; grid-row: span 1; min-height: auto; }
        .bento-wide { grid-column: span 2; }
    }

    @media (max-width: 600px) {
        .bento-grid {
            grid-template-columns: 1fr;
        }
        .bento-main, .bento-wide, .bento-side {
            grid-column: span 1;
        }
        .dashboard-container {
            padding: 1rem;
            width: 100%;
            max-width: 100vw;
        }

        /* Stats row horizontal scroll if needed */
        .stats-row {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 0.5rem;
            margin: 0 -1rem;
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    /* Stats Row */
    .stats-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    /* Action Title */
    .action-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }

    .action-desc {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-left: 1rem;
        border-left: 3px solid var(--gold-start);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }

    /* Search Form */
    .search-form {
        display: flex;
        gap: 1rem;
    }

    @media (max-width: 640px) {
        .search-form { flex-direction: column; }
    }

    /* Stat Cards - Semantic */
    .stat-card {
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-radius: 1rem;
        transition: all var(--transition-normal);
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-card-failed {
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.20);
    }

    .stat-card-unsolved {
        background: rgba(59, 130, 246, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.20);
    }

    .stat-card-solved {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.20);
    }

    .stat-icon { font-size: 1.75rem; flex-shrink: 0; }
    .stat-content { flex: 1; min-width: 0; }
    .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 0.25rem; }
    .stat-card-failed .stat-value { color: #ef4444; }
    .stat-card-unsolved .stat-value { color: #3b82f6; }
    .stat-card-solved .stat-value { color: #22c55e; }
    .stat-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }

    .stat-progress {
        width: 100%;
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        margin-top: 0.5rem;
        overflow: hidden;
    }

    .stat-progress-fill { height: 100%; border-radius: 2px; transition: width 0.8s ease-out; }
    .stat-card-failed .stat-progress-fill { background: linear-gradient(90deg, #ef4444, #dc2626); }
    .stat-card-unsolved .stat-progress-fill { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .stat-card-solved .stat-progress-fill { background: linear-gradient(90deg, #22c55e, #16a34a); }

    /* Priority Hint */
    .priority-hint {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        padding: 0.875rem 1rem;
        background: rgba(251, 191, 36, 0.08);
        border: 1px solid rgba(251, 191, 36, 0.20);
        border-radius: 0.75rem;
    }

    .priority-hint strong { color: var(--gold-start); }

    /* Section Grid for Lernabschnitte */
    .sections-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .sections-grid { grid-template-columns: 1fr; }
    }

    /* Detailed Stats Grid - Responsive */
    .detailed-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    @media (max-width: 600px) {
        .detailed-stats-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .stat-card {
            padding: 1rem;
        }

        .stat-icon {
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 1.25rem;
        }
    }

    .section-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        text-decoration: none;
        transition: all var(--transition-normal);
    }

    .section-link:hover {
        transform: translateY(-2px);
    }

    .section-link:nth-child(odd) {
        border-radius: 1.5rem 0.5rem 1rem 1rem;
    }

    .section-link:nth-child(even) {
        border-radius: 0.5rem 1.5rem 1rem 1rem;
    }

    .section-number {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--thw-blue) 0%, var(--thw-blue-dark) 100%);
        color: var(--gold-start);
        font-size: 1.25rem;
        font-weight: 800;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .section-info { flex: 1; min-width: 0; }
    .section-name { font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; line-height: 1.3; }
    .section-stats { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem; }

    .section-progress {
        width: 100%;
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .section-progress-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: 2px;
        transition: width 1s ease-out;
    }

    .section-percent {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    /* Back Link */
    .back-section {
        text-align: center;
        margin-top: 2rem;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header: Links ausgerichtet -->
    <header class="dashboard-header">
        <h1 class="page-title"><span>Übungsmenü</span></h1>
        <p class="page-subtitle">Wähle deinen Lernmodus</p>
    </header>

    <!-- Stats als horizontale Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #ef4444;"><i class="bi bi-x-circle"></i></span>
            <div>
                <div class="stat-pill-value" style="color: #ef4444;">{{ $failedCount }}</div>
                <div class="stat-pill-label">Fehlgeschlagen</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #3b82f6;"><i class="bi bi-question-circle"></i></span>
            <div>
                <div class="stat-pill-value" style="color: #3b82f6;">{{ $unsolvedCount }}</div>
                <div class="stat-pill-label">Ungelöst</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #22c55e;"><i class="bi bi-check-circle"></i></span>
            <div>
                <div class="stat-pill-value" style="color: #22c55e;">{{ $solvedCount }}</div>
                <div class="stat-pill-label">Gemeistert</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-collection"></i></span>
            <div>
                <div class="stat-pill-value">{{ $totalQuestions }}</div>
                <div class="stat-pill-label">Gesamt</div>
            </div>
        </div>
    </div>

    <!-- Bento Grid Layout -->
    <div class="bento-grid">
        <!-- Main: Training starten -->
        <a href="{{ route('practice.all') }}" class="glass-gold bento-main hover-lift" style="text-decoration: none; position: relative;">
            @if($failedCount == 0 && $unsolvedCount == 0)
                <div class="floating-badge">Alles gemeistert</div>
            @endif
            <div style="margin-bottom: 1.5rem;">
                <span class="badge-thw">Grundausbildung</span>
            </div>
            <h2 class="action-title">
                @if($failedCount > 0 || $unsolvedCount > 0)
                    Training<br>starten
                @else
                    Alle Fragen<br>wiederholen
                @endif
            </h2>
            <p class="action-desc">
                @if($failedCount > 0)
                    Intelligente Priorisierung: Zuerst {{ $failedCount }} fehlgeschlagene Fragen, dann {{ $unsolvedCount }} ungelöste.
                @elseif($unsolvedCount > 0)
                    Intelligente Priorisierung: {{ $unsolvedCount }} ungelöste Fragen werden zuerst geübt.
                @else
                    Alle Fragen gemeistert! Übe in zufälliger Reihenfolge, um dein Wissen zu festigen.
                @endif
            </p>

            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex: 1;">
                    <div class="progress-glass">
                        <div class="progress-fill-gold" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
                <span style="font-weight: 700; color: var(--gold-start);">{{ $progressPercentage }}%</span>
            </div>

            <span class="btn-primary" style="align-self: flex-start;">
                @if($failedCount > 0)
                    Schwierige Fragen zuerst
                @elseif($unsolvedCount > 0)
                    Ungelöste zuerst
                @else
                    Zufällig üben
                @endif
            </span>
        </a>

        <!-- Side: Suche -->
        <div class="glass-tl bento-side">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">Fragen suchen</h3>
            <form action="{{ route('practice.search') }}" method="GET" class="search-form" style="flex-direction: column;">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Suchbegriff..."
                       class="input-glass">
                <button type="submit" class="btn-secondary btn-sm" style="align-self: flex-start;">Suchen</button>
            </form>
        </div>

        <!-- Side: Quick Stats -->
        <div class="glass-br bento-side">
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 800;" class="text-gradient-gold">{{ $progressPercentage }}%</div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Fortschritt</div>
                <div style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-secondary);">
                    {{ $solvedCount }} von {{ $totalQuestions }}
                </div>
            </div>
        </div>

        <!-- Detaillierte Stats -->
        <div class="glass-slash bento-wide">
            <div class="detailed-stats-grid">
                <div class="stat-card stat-card-failed">
                    <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $failedCount }}</div>
                        <div class="stat-label">Fehlgeschlagen</div>
                        @php $failedProgressPercent = $totalQuestions > 0 ? ($failedCount / $totalQuestions) * 100 : 0; @endphp
                        <div class="stat-progress"><div class="stat-progress-fill" style="width: {{ $failedProgressPercent }}%"></div></div>
                    </div>
                </div>

                <div class="stat-card stat-card-unsolved">
                    <div class="stat-icon"><i class="bi bi-question-circle"></i></div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $unsolvedCount }}</div>
                        <div class="stat-label">Ungelöst</div>
                        @php $unsolvedProgressPercent = $totalQuestions > 0 ? ($unsolvedCount / $totalQuestions) * 100 : 0; @endphp
                        <div class="stat-progress"><div class="stat-progress-fill" style="width: {{ $unsolvedProgressPercent }}%"></div></div>
                    </div>
                </div>

                <div class="stat-card stat-card-solved">
                    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $solvedCount }}</div>
                        <div class="stat-label">Gemeistert</div>
                        <div class="stat-progress"><div class="stat-progress-fill" style="width: {{ $progressPercentage }}%"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lernabschnitte Section -->
    <div class="section-header">
        <h2 class="section-title">Lernabschnitte</h2>
    </div>

    <div class="sections-grid">
        @foreach(range(1, 10) as $section)
            @php
                $sectionTotal = $sectionStats[$section]['total'] ?? 0;
                $sectionSolved = $sectionStats[$section]['solved'] ?? 0;
                $sectionPercent = $sectionTotal > 0 ? round(($sectionSolved / $sectionTotal) * 100) : 0;
                $sectionName = $sectionNames[$section] ?? "Abschnitt $section";
            @endphp

            <a href="{{ route('practice.section', $section) }}" class="glass section-link hover-lift">
                <div class="section-number">{{ $section }}</div>
                <div class="section-info">
                    <div class="section-name">{{ $sectionName }}</div>
                    <div class="section-stats">{{ $sectionSolved }}/{{ $sectionTotal }} Fragen</div>
                    <div class="section-progress">
                        <div class="section-progress-fill" id="progressBar{{ $section }}" style="width: 0%"></div>
                    </div>
                    <div class="section-percent">{{ $sectionPercent }}%</div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Zurück zum Dashboard -->
    <div class="back-section">
        <a href="{{ route('dashboard') }}" class="btn-ghost">
            Zurück zum Dashboard
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach(range(1, 10) as $section)
        @php
            $sectionTotal = $sectionStats[$section]['total'] ?? 0;
            $sectionSolved = $sectionStats[$section]['solved'] ?? 0;
            $sectionPercent = $sectionTotal > 0 ? round(($sectionSolved / $sectionTotal) * 100) : 0;
        @endphp

        setTimeout(() => {
            const bar{{ $section }} = document.getElementById('progressBar{{ $section }}');
            if (bar{{ $section }}) {
                bar{{ $section }}.style.transition = 'width 0.8s ease-out';
                bar{{ $section }}.style.width = '{{ $sectionPercent }}%';
            }
        }, 200 + ({{ $section }} * 80));
    @endforeach
});
</script>
@endsection
