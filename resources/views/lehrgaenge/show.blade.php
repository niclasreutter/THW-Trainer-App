@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@push('styles')
<style>
    .dashboard-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2rem;
        padding-top: 1rem;
        max-width: 700px;
    }

    /* Stats Row */
    .stats-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    /* Hero Card */
    .hero-card {
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .hero-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.25rem;
        gap: 1rem;
    }

    .hero-description {
        font-size: 1rem;
        color: var(--text-secondary);
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }

    .hero-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .hero-stat {
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 12px;
    }

    .hero-stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    /* Progress Section */
    .progress-card {
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .progress-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .progress-percentage {
        font-size: 1.25rem;
        font-weight: 800;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .progress-bar-wrapper {
        width: 100%;
        height: 10px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 0.75rem;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: 5px;
        transition: width 0.5s ease-out;
        box-shadow: 0 0 10px rgba(251, 191, 36, 0.4);
    }

    .progress-bar-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
        box-shadow: 0 0 10px rgba(34, 197, 94, 0.4);
    }

    .progress-details {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .progress-complete-badge {
        color: #22c55e;
        font-weight: 600;
    }

    /* Section Header */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        padding-left: 0.75rem;
        border-left: 3px solid var(--gold-start);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* Sections Grid */
    .sections-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    .section-card {
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .section-card:nth-child(odd) {
        border-radius: 1rem 0.5rem 1rem 0.5rem;
    }

    .section-card:nth-child(even) {
        border-radius: 0.5rem 1rem 0.5rem 1rem;
    }

    .section-info {
        flex: 1;
        min-width: 0;
    }

    .section-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.35rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .section-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .section-progress {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0;
    }

    .section-progress-bar {
        width: 80px;
        height: 6px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 3px;
        overflow: hidden;
    }

    .section-progress-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .section-progress-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .section-progress-text {
        font-size: 0.8rem;
        font-weight: 700;
        min-width: 42px;
        text-align: right;
        color: var(--text-secondary);
    }

    /* Enroll Section */
    .enroll-card {
        padding: 3rem 2rem;
        text-align: center;
        margin-bottom: 2rem;
        border: 2px dashed rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.01);
    }

    .enroll-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        opacity: 0.6;
    }

    .enroll-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .enroll-description {
        font-size: 1rem;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        max-width: 450px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    /* Action Buttons Row */
    .action-row {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    @media (min-width: 640px) {
        .action-row {
            flex-direction: row;
        }

        .action-row .btn-primary,
        .action-row .btn-secondary,
        .action-row .btn-ghost {
            flex: 1;
        }
    }

    /* Unenroll Section */
    .unenroll-section {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .unenroll-hint {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
    }

    /* Light Mode Overrides */
    html.light-mode .hero-stats {
        border-top-color: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .hero-stat {
        background: rgba(0, 51, 127, 0.03);
    }

    html.light-mode .hero-stat-value {
        background: linear-gradient(90deg, #d97706, #b45309);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    html.light-mode .progress-bar-wrapper {
        background: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .progress-percentage {
        background: linear-gradient(90deg, #d97706, #b45309);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    html.light-mode .section-progress-bar {
        background: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .unenroll-section {
        border-top-color: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .enroll-card {
        border-color: rgba(0, 51, 127, 0.15);
        background: rgba(0, 51, 127, 0.02);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-container { padding: 1rem; }
        .hero-card { padding: 1.5rem; }
        .hero-stats { grid-template-columns: 1fr; gap: 0.75rem; }
        .hero-stat { padding: 0.75rem; }
        .hero-stat-value { font-size: 1.5rem; }

        .section-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .section-progress {
            width: 100%;
        }

        .section-progress-bar {
            flex: 1;
        }
    }

    @media (max-width: 640px) {
        .page-title { font-size: 1.75rem; }
        .hero-header { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    @php
        $isEnrolled = in_array($lehrgang->id, $enrolledIds ?? []);
        $solvedCount = 0;
        $totalCount = 0;
        $progressPercent = 0;
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
        $sections = $lehrgang->questions()->select('lernabschnitt')->distinct()->orderBy('lernabschnitt')->get();
    @endphp

    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title"><span>{{ $lehrgang->lehrgang }}</span></h1>
        <p class="page-subtitle">Spezialisierter THW-Lehrgang</p>
    </header>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-question-circle"></i></span>
            <div>
                <div class="stat-pill-value">{{ $questionCount }}</div>
                <div class="stat-pill-label">Fragen</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon"><i class="bi bi-layers"></i></span>
            <div>
                <div class="stat-pill-value">{{ $sectionCount }}</div>
                <div class="stat-pill-label">Abschnitte</div>
            </div>
        </div>
        @if($isEnrolled)
            <div class="stat-pill">
                <span class="stat-pill-icon text-success"><i class="bi bi-check-circle"></i></span>
                <div>
                    <div class="stat-pill-value">{{ $progressPercent }}%</div>
                    <div class="stat-pill-label">Fortschritt</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Hero Card -->
    <div class="glass-gold hero-card" style="border-radius: 1.5rem 0.5rem 1.5rem 0.5rem;">
        <div class="hero-header">
            <div style="flex: 1;">
                @if($lehrgang->beschreibung)
                    <p class="hero-description">{{ $lehrgang->beschreibung }}</p>
                @endif
            </div>
            @if($isEnrolled)
                <span class="badge-success">Eingeschrieben</span>
            @endif
        </div>

        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-value">{{ $questionCount }}</div>
                <div class="hero-stat-label">Fragen</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value">{{ $sectionCount }}</div>
                <div class="hero-stat-label">Abschnitte</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value">{{ $isEnrolled ? $progressPercent : 0 }}%</div>
                <div class="hero-stat-label">Fortschritt</div>
            </div>
        </div>
    </div>

    @if($isEnrolled)
        <!-- Progress Section -->
        <div class="glass-tl progress-card">
            <div class="progress-header">
                <h2 class="progress-title">Dein Fortschritt</h2>
                <span class="progress-percentage">{{ $progressPercent }}%</span>
            </div>
            <div class="progress-bar-wrapper">
                <div class="progress-bar-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="progress-details">
                <span>{{ $solvedCount }}/{{ $totalCount }} Fragen beantwortet</span>
                @if($isCompleted)
                    <span class="progress-complete-badge"><i class="bi bi-check-circle-fill"></i> Abgeschlossen</span>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-row">
            @if($isCompleted)
                <span class="btn-ghost" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.25); text-align: center; padding: 1rem;">Lehrgang abgeschlossen</span>
            @else
                <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="btn-primary" style="text-align: center; padding: 1rem;">Jetzt weiterlernen</a>
            @endif
            <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost" style="text-align: center; padding: 1rem;">Alle Lehrgänge</a>
        </div>

        <!-- Sections -->
        <div class="section-header">
            <h2 class="section-title">Lernabschnitte</h2>
        </div>

        @php
            $lernabschnittNamen = \App\Models\LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
                ->pluck('lernabschnitt', 'lernabschnitt_nr')
                ->toArray();
        @endphp

        <div class="sections-list">
            @foreach($sections as $section)
                @php
                    $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                    $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Lernabschnitt {$sectionNr}";
                    $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                    $sectionSolvedCount = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                        ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id)->where('lernabschnitt', $sectionNr))
                        ->where('solved', true)
                        ->count();
                    $sectionProgress = $sectionQuestionCount > 0 ? round(($sectionSolvedCount / $sectionQuestionCount) * 100) : 0;
                    $sectionComplete = $sectionProgress == 100 && $sectionSolvedCount > 0;
                @endphp
                <div class="glass section-card hover-lift">
                    <div class="section-info">
                        <h3 class="section-name">{{ $sectionName }}</h3>
                        <div class="section-meta">
                            <span>{{ $sectionQuestionCount }} Fragen</span>
                            <span>{{ $sectionSolvedCount }} gelöst</span>
                        </div>
                    </div>
                    <div class="section-progress">
                        <div class="section-progress-bar">
                            <div class="section-progress-fill {{ $sectionComplete ? 'complete' : '' }}" style="width: {{ $sectionProgress }}%"></div>
                        </div>
                        <span class="section-progress-text">{{ $sectionProgress }}%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Unenroll Section -->
        <div class="unenroll-section">
            <form action="{{ route('lehrgaenge.unenroll', $lehrgang->slug) }}" method="POST" style="display: inline-block;"
                  onsubmit="return confirm('Möchtest du diesen Lehrgang wirklich verlassen? Dein Fortschritt bleibt gespeichert.');">
                @csrf
                <button type="submit" class="btn-danger">Lehrgang verlassen</button>
            </form>
            <p class="unenroll-hint">Dein Fortschritt bleibt erhalten</p>
        </div>

    @else
        <!-- Enroll Section -->
        <div class="glass-slash enroll-card">
            <div class="enroll-icon"><i class="bi bi-mortarboard"></i></div>
            <h2 class="enroll-title">Bereit für diesen Lehrgang?</h2>
            <p class="enroll-description">
                Schreibe dich jetzt ein und beginne mit dem Lernen. Du wirst Zugang zu allen {{ $questionCount }} Fragen erhalten und deinen Fortschritt verfolgen können.
            </p>
            <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST" style="display: inline-block;">
                @csrf
                <button type="submit" class="btn-primary" style="padding: 1rem 2.5rem;">Jetzt beitreten</button>
            </form>
        </div>

        <!-- Preview Sections -->
        <div class="section-header">
            <h2 class="section-title">Lernabschnitte (Vorschau)</h2>
        </div>

        @php
            $lernabschnittNamen = \App\Models\LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
                ->pluck('lernabschnitt', 'lernabschnitt_nr')
                ->toArray();
        @endphp

        <div class="sections-list">
            @foreach($sections as $section)
                @php
                    $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                    $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Lernabschnitt {$sectionNr}";
                    $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                @endphp
                <div class="glass section-card hover-lift">
                    <div class="section-info">
                        <h3 class="section-name">{{ $sectionName }}</h3>
                        <div class="section-meta">
                            <span>{{ $sectionQuestionCount }} Fragen</span>
                        </div>
                    </div>
                    <div class="section-progress">
                        <div class="section-progress-bar">
                            <div class="section-progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="section-progress-text">0%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Back Link -->
        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost">
                <i class="bi bi-arrow-left"></i> Zurück zur Übersicht
            </a>
        </div>
    @endif
</div>
@endsection
