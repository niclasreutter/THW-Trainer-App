@extends('layouts.app')

@section('title', 'Lehrgänge')

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

    /* Stats row */
    .stats-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    /* Lehrgang Grid */
    .lehrgaenge-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .lehrgaenge-grid { grid-template-columns: 1fr; }
        .dashboard-container { padding: 1rem; }
    }

    /* Lehrgang Cards - abwechselnd asymmetrisch */
    .lehrgang-card {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .lehrgang-card:nth-child(3n+1) {
        border-radius: 1.5rem 0.5rem 1rem 1rem;
    }

    .lehrgang-card:nth-child(3n+2) {
        border-radius: 0.5rem 1.5rem 1rem 1rem;
    }

    .lehrgang-card:nth-child(3n) {
        border-radius: 1rem 1rem 1.5rem 0.5rem;
    }

    .lehrgang-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .lehrgang-name {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.3;
        flex: 1;
        padding-right: 0.75rem;
    }

    .lehrgang-description {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .lehrgang-stats {
        display: flex;
        gap: 1.5rem;
        padding: 0.875rem 0;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 1rem;
    }

    .stat-item {
        text-align: center;
        flex: 1;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 800;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .lehrgang-progress {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .lehrgang-progress-info {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .lehrgang-progress-bar {
        flex: 1;
        height: 6px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 3px;
        overflow: hidden;
    }

    .lehrgang-progress-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: 3px;
        transition: width 0.5s ease-out;
        box-shadow: 0 0 8px rgba(251, 191, 36, 0.4);
    }

    .lehrgang-progress-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
    }

    .lehrgang-percent {
        font-size: 0.8rem;
        font-weight: 700;
        min-width: 40px;
        text-align: right;
    }

    .lehrgang-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-top: auto;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-state-desc {
        font-size: 0.95rem;
        color: var(--text-secondary);
    }

    /* Light Mode Overrides */
    html.light-mode .lehrgang-stats {
        border-top-color: rgba(0, 51, 127, 0.08);
        border-bottom-color: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .lehrgang-progress-bar {
        background: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .stat-value {
        background: linear-gradient(90deg, #d97706, #b45309);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    html.light-mode .lehrgang-percent {
        color: #d97706;
    }

    @media (max-width: 640px) {
        .page-title { font-size: 2rem; }
        .lehrgang-card { padding: 1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Weitere <span>Lehrgänge</span></h1>
        <p class="page-subtitle">Spezialisiere dich auf bestimmte THW-Themenbereiche</p>
    </header>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-mortarboard"></i></span>
            <div>
                <div class="stat-pill-value">{{ $lehrgaenge->count() }}</div>
                <div class="stat-pill-label">Lehrgänge</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-check-circle"></i></span>
            <div>
                <div class="stat-pill-value">{{ count($enrolledIds) }}</div>
                <div class="stat-pill-label">Eingeschrieben</div>
            </div>
        </div>
    </div>

    @if($lehrgaenge->isEmpty())
        <div class="glass-slash empty-state">
            <div class="empty-state-icon"><i class="bi bi-mortarboard"></i></div>
            <h3 class="empty-state-title">Noch keine Lehrgänge verfügbar</h3>
            <p class="empty-state-desc">Bald werden hier weitere Lehrgänge erscheinen</p>
        </div>
    @else
        <div class="lehrgaenge-grid">
            @foreach($lehrgaenge as $lehrgang)
                @php
                    $isEnrolled = in_array($lehrgang->id, $enrolledIds);
                    $progressPercent = 0;
                    $solvedCount = 0;
                    $totalCount = 0;
                    $isCompleted = false;

                    if ($isEnrolled) {
                        $solvedCount = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                            ->where('solved', true)
                            ->count();
                        $totalCount = \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();

                        $progressData = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                            ->get();

                        $totalProgressPoints = 0;
                        foreach ($progressData as $prog) {
                            $totalProgressPoints += min($prog->consecutive_correct, 2);
                        }
                        $maxProgressPoints = $totalCount * 2;
                        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                        $isCompleted = $progressPercent == 100 && $solvedCount > 0;
                    }

                    $questionCount = $lehrgang->questions()->count();
                    $sectionCount = $lehrgang->questions()->distinct('lernabschnitt')->count('lernabschnitt');
                @endphp

                <div class="glass lehrgang-card hover-lift">
                    <div class="lehrgang-header">
                        <h2 class="lehrgang-name">{{ $lehrgang->lehrgang }}</h2>
                        @if($isEnrolled)
                            <span class="badge-success">Eingeschrieben</span>
                        @endif
                    </div>

                    <p class="lehrgang-description">{{ $lehrgang->beschreibung }}</p>

                    <div class="lehrgang-stats">
                        <div class="stat-item">
                            <div class="stat-value">{{ $questionCount }}</div>
                            <div class="stat-label">Fragen</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $sectionCount }}</div>
                            <div class="stat-label">Abschnitte</div>
                        </div>
                    </div>

                    @if($isEnrolled)
                        <div>
                            <div class="lehrgang-progress-info">
                                <span>{{ $solvedCount }}/{{ $totalCount }} Fragen</span>
                                <span>{{ $progressPercent }}%</span>
                            </div>
                            <div class="lehrgang-progress">
                                <div class="lehrgang-progress-bar">
                                    <div class="lehrgang-progress-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $progressPercent }}%"></div>
                                </div>
                                <span class="lehrgang-percent text-gradient-gold">{{ $progressPercent }}%</span>
                            </div>
                        </div>
                    @endif

                    <div class="lehrgang-actions">
                        <a href="{{ route('lehrgaenge.show', $lehrgang->slug) }}" class="btn-ghost btn-sm">Details anzeigen</a>

                        @if($isEnrolled)
                            @if($isCompleted)
                                <span class="btn-ghost btn-sm" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.25);">Abgeschlossen</span>
                            @else
                                <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="btn-primary btn-sm">Weiterlernen</a>
                            @endif
                        @else
                            <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm" style="width: 100%;">Beitreten</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Back Link -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn-ghost">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>
@endsection
