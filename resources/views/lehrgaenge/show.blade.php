@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@push('styles')
<style>
    /* ===== Mobile-First Layout ===== */
    .lehrgang-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 1rem;
    }

    @media (min-width: 640px) {
        .lehrgang-container {
            padding: 2rem;
        }
    }

    /* ===== Header ===== */
    .lehrgang-header {
        margin-bottom: 1.25rem;
    }

    @media (min-width: 640px) {
        .lehrgang-header {
            margin-bottom: 2rem;
        }
    }

    .lehrgang-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    @media (min-width: 640px) {
        .lehrgang-title {
            font-size: 2rem;
        }
    }

    .lehrgang-subtitle {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    /* ===== Stats Row - Desktop only ===== */
    .stats-row-desktop {
        display: none;
    }

    @media (min-width: 640px) {
        .stats-row-desktop {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
    }

    /* ===== Hero Card ===== */
    .hero-card {
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border-radius: 1rem;
    }

    @media (min-width: 640px) {
        .hero-card {
            padding: 2rem;
            margin-bottom: 1.5rem;
            border-radius: 1.5rem 0.5rem 1.5rem 0.5rem;
        }
    }

    .hero-badge {
        margin-bottom: 1rem;
    }

    .hero-description {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1.25rem;
    }

    @media (min-width: 640px) {
        .hero-description {
            font-size: 1rem;
            line-height: 1.7;
        }
    }

    /* ===== Hero Stats - Responsive Grid ===== */
    .hero-stats {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    @media (min-width: 640px) {
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            padding-top: 1.5rem;
        }
    }

    .hero-stat {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.75rem;
    }

    @media (min-width: 640px) {
        .hero-stat {
            flex-direction: column;
            text-align: center;
            padding: 1rem;
            border-radius: 12px;
        }
    }

    .hero-stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (min-width: 640px) {
        .hero-stat-label {
            order: 2;
            margin-top: 0.25rem;
        }
    }

    .hero-stat-value {
        font-size: 1.25rem;
        font-weight: 800;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    @media (min-width: 640px) {
        .hero-stat-value {
            font-size: 1.75rem;
            order: 1;
        }
    }

    /* ===== Progress Card ===== */
    .progress-card {
        padding: 1rem;
        margin-bottom: 1.25rem;
        border-radius: 0.75rem;
    }

    @media (min-width: 640px) {
        .progress-card {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .progress-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    @media (min-width: 640px) {
        .progress-title {
            font-size: 1.1rem;
        }
    }

    .progress-percentage {
        font-size: 1rem;
        font-weight: 800;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    @media (min-width: 640px) {
        .progress-percentage {
            font-size: 1.25rem;
        }
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    @media (min-width: 640px) {
        .progress-bar {
            height: 10px;
            border-radius: 5px;
        }
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: inherit;
        transition: width 0.5s ease-out;
    }

    .progress-bar-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .progress-info {
        font-size: 0.8rem;
        color: var(--text-secondary);
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .progress-complete {
        color: #22c55e;
        font-weight: 600;
    }

    /* ===== Action Buttons ===== */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
        margin-bottom: 1.5rem;
    }

    @media (min-width: 640px) {
        .action-buttons {
            flex-direction: row;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .action-buttons > * {
            flex: 1;
        }
    }

    .action-btn {
        display: block;
        text-align: center;
        padding: 0.875rem 1rem;
        border-radius: 0.625rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    @media (min-width: 640px) {
        .action-btn {
            padding: 1rem;
        }
    }

    /* ===== Section Header ===== */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-left: 0.625rem;
        border-left: 3px solid var(--gold-start);
    }

    @media (min-width: 640px) {
        .section-header {
            margin-bottom: 1rem;
            padding-left: 0.75rem;
        }
    }

    .section-header-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    @media (min-width: 640px) {
        .section-header-title {
            font-size: 1.15rem;
        }
    }

    /* ===== Sections List ===== */
    .sections-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    @media (min-width: 640px) {
        .sections-list {
            gap: 0.625rem;
            margin-bottom: 2rem;
        }
    }

    .section-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        transition: all 0.2s ease;
    }

    @media (min-width: 640px) {
        .section-item {
            padding: 1rem 1.25rem;
        }
    }

    .section-item:nth-child(odd) {
        border-radius: 0.875rem 0.375rem 0.875rem 0.375rem;
    }

    .section-item:nth-child(even) {
        border-radius: 0.375rem 0.875rem 0.375rem 0.875rem;
    }

    .section-item-info {
        flex: 1;
        min-width: 0;
        margin-right: 0.75rem;
    }

    .section-item-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (min-width: 640px) {
        .section-item-name {
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }
    }

    .section-item-meta {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    @media (min-width: 640px) {
        .section-item-meta {
            font-size: 0.75rem;
        }
    }

    .section-item-progress {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .section-progress-bar {
        width: 50px;
        height: 4px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 2px;
        overflow: hidden;
    }

    @media (min-width: 640px) {
        .section-progress-bar {
            width: 70px;
            height: 5px;
        }
    }

    .section-progress-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: inherit;
    }

    .section-progress-fill.complete {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .section-progress-text {
        font-size: 0.7rem;
        font-weight: 700;
        min-width: 32px;
        text-align: right;
        color: var(--text-secondary);
    }

    @media (min-width: 640px) {
        .section-progress-text {
            font-size: 0.8rem;
            min-width: 38px;
        }
    }

    /* ===== Enroll Card ===== */
    .enroll-card {
        padding: 1.5rem 1.25rem;
        text-align: center;
        margin-bottom: 1.5rem;
        border: 2px dashed rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.01);
        border-radius: 1rem;
    }

    @media (min-width: 640px) {
        .enroll-card {
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
        }
    }

    .enroll-icon {
        font-size: 2.25rem;
        color: var(--text-muted);
        margin-bottom: 0.625rem;
        opacity: 0.6;
    }

    @media (min-width: 640px) {
        .enroll-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    }

    .enroll-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.375rem;
    }

    @media (min-width: 640px) {
        .enroll-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
    }

    .enroll-description {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }

    @media (min-width: 640px) {
        .enroll-description {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }
    }

    /* ===== Unenroll Section ===== */
    .unenroll-section {
        text-align: center;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .unenroll-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.375rem;
    }

    /* ===== Light Mode ===== */
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

    html.light-mode .progress-bar,
    html.light-mode .section-progress-bar {
        background: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .progress-percentage {
        background: linear-gradient(90deg, #d97706, #b45309);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    html.light-mode .unenroll-section {
        border-top-color: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .enroll-card {
        border-color: rgba(0, 51, 127, 0.15);
        background: rgba(0, 51, 127, 0.02);
    }
</style>
@endpush

@section('content')
<div class="lehrgang-container">
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
    <header class="lehrgang-header">
        <h1 class="lehrgang-title"><span>{{ $lehrgang->lehrgang }}</span></h1>
        <p class="lehrgang-subtitle">Spezialisierter THW-Lehrgang</p>
    </header>

    <!-- Stats Row - Desktop Only -->
    <div class="stats-row-desktop">
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
    <div class="glass-gold hero-card">
        @if($isEnrolled)
            <div class="hero-badge">
                <span class="badge-success">Eingeschrieben</span>
            </div>
        @endif

        @if($lehrgang->beschreibung)
            <p class="hero-description">{{ $lehrgang->beschreibung }}</p>
        @endif

        <div class="hero-stats">
            <div class="hero-stat">
                <span class="hero-stat-label">Fragen</span>
                <span class="hero-stat-value">{{ $questionCount }}</span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-label">Abschnitte</span>
                <span class="hero-stat-value">{{ $sectionCount }}</span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-label">Fortschritt</span>
                <span class="hero-stat-value">{{ $isEnrolled ? $progressPercent : 0 }}%</span>
            </div>
        </div>
    </div>

    @if($isEnrolled)
        <!-- Progress Card -->
        <div class="glass-tl progress-card">
            <div class="progress-header">
                <h2 class="progress-title">Dein Fortschritt</h2>
                <span class="progress-percentage">{{ $progressPercent }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar-fill {{ $isCompleted ? 'complete' : '' }}" style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="progress-info">
                <span>{{ $solvedCount }}/{{ $totalCount }} Fragen beantwortet</span>
                @if($isCompleted)
                    <span class="progress-complete"><i class="bi bi-check-circle-fill"></i> Abgeschlossen</span>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            @if($isCompleted)
                <span class="action-btn btn-ghost" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.25);">
                    Lehrgang abgeschlossen
                </span>
            @else
                <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="action-btn btn-primary">
                    Jetzt weiterlernen
                </a>
            @endif
            <a href="{{ route('lehrgaenge.index') }}" class="action-btn btn-ghost">
                Alle Lehrgänge
            </a>
        </div>

        <!-- Sections -->
        <div class="section-header">
            <h2 class="section-header-title">Lernabschnitte</h2>
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
                <div class="glass section-item">
                    <div class="section-item-info">
                        <h3 class="section-item-name">{{ $sectionName }}</h3>
                        <div class="section-item-meta">{{ $sectionQuestionCount }} Fragen, {{ $sectionSolvedCount }} gelöst</div>
                    </div>
                    <div class="section-item-progress">
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
        <!-- Enroll Card -->
        <div class="glass-slash enroll-card">
            <div class="enroll-icon"><i class="bi bi-mortarboard"></i></div>
            <h2 class="enroll-title">Bereit für diesen Lehrgang?</h2>
            <p class="enroll-description">
                Schreibe dich jetzt ein und beginne mit dem Lernen. Du wirst Zugang zu allen {{ $questionCount }} Fragen erhalten.
            </p>
            <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST" style="display: inline-block;">
                @csrf
                <button type="submit" class="btn-primary" style="padding: 0.875rem 2rem;">Jetzt beitreten</button>
            </form>
        </div>

        <!-- Preview Sections -->
        <div class="section-header">
            <h2 class="section-header-title">Lernabschnitte (Vorschau)</h2>
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
                <div class="glass section-item">
                    <div class="section-item-info">
                        <h3 class="section-item-name">{{ $sectionName }}</h3>
                        <div class="section-item-meta">{{ $sectionQuestionCount }} Fragen</div>
                    </div>
                    <div class="section-item-progress">
                        <div class="section-progress-bar">
                            <div class="section-progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="section-progress-text">0%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Back Link -->
        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost">
                Zurück zur Übersicht
            </a>
        </div>
    @endif
</div>
@endsection
