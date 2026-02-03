@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@push('styles')
<style>
    /* Container — identisch wie dashboard.blade.php */
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

    /* Section-Header — identisch wie dashboard */
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

    /* Hero-Card (eingeschrieben) */
    .lehrgang-hero {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .lehrgang-hero-badges {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .lehrgang-hero-desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1.25rem;
    }

    /* Fortschritt-Block */
    .lehrgang-progress-block {
        margin-bottom: 1.25rem;
    }

    .lehrgang-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .lehrgang-progress-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .lehrgang-progress-percent {
        font-weight: 800;
    }

    .lehrgang-progress-sub {
        display: flex;
        justify-content: space-between;
        margin-top: 0.375rem;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .lehrgang-progress-sub-done {
        color: var(--success);
        font-weight: 600;
    }

    /* Aktions-Buttons */
    .lehrgang-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    /* Abschnitte-Liste */
    .lehrgang-sections {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .lehrgang-section-item {
        padding: 0.875rem 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .lehrgang-section-info {
        flex: 1;
        min-width: 0;
        overflow: hidden;
    }

    .lehrgang-section-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lehrgang-section-meta {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.125rem;
    }

    .lehrgang-section-progress {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .lehrgang-section-bar {
        width: 60px;
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .lehrgang-section-bar-fill {
        height: 100%;
        border-radius: 2px;
    }

    .lehrgang-section-percent {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-secondary);
        min-width: 32px;
        text-align: right;
    }

    /* Austritt-Bereich */
    .lehrgang-unenroll {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .lehrgang-unenroll-hint {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.375rem;
    }

    /* Einschreibungs-Card (nicht eingeschrieben) */
    .lehrgang-enroll {
        padding: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .lehrgang-enroll-icon {
        font-size: 2.5rem;
        color: var(--text-muted);
        opacity: 0.5;
        margin-bottom: 0.75rem;
    }

    .lehrgang-enroll-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .lehrgang-enroll-desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }

    /* Zurück-Button */
    .lehrgang-back {
        text-align: center;
    }

    /* ─── Mobile (600px) — identisch wie dashboard ─── */
    @media (max-width: 600px) {
        .dashboard-container {
            padding: 1rem;
        }

        .lehrgang-hero {
            padding: 1rem;
        }

        .lehrgang-enroll {
            padding: 1.25rem;
        }

        .lehrgang-section-item {
            padding: 0.75rem;
            gap: 0.75rem;
        }

        .lehrgang-section-bar {
            width: 48px;
        }
    }
</style>
@endpush

@section('content')
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
    $lernabschnittNamen = \App\Models\LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
        ->pluck('lernabschnitt', 'lernabschnitt_nr')
        ->toArray();
@endphp

<div class="dashboard-container">
    <!-- Header — gleich wie dashboard -->
    <header class="dashboard-header">
        <h1 class="page-title">{{ $lehrgang->lehrgang }}</h1>
        <p class="page-subtitle">Spezialisierter THW-Lehrgang</p>
    </header>

    <!-- Stats — gleiche .stats-row wie dashboard, flex-wrap: wrap aus global CSS -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-question-circle"></i></span>
            <div>
                <div class="stat-pill-value">{{ $questionCount }}</div>
                <div class="stat-pill-label">Fragen</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-info"><i class="bi bi-collection"></i></span>
            <div>
                <div class="stat-pill-value">{{ $sectionCount }}</div>
                <div class="stat-pill-label">Abschnitte</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon {{ $isCompleted ? 'text-success' : 'text-warning' }}"><i class="bi bi-graph-up"></i></span>
            <div>
                <div class="stat-pill-value">{{ $isEnrolled ? $progressPercent : 0 }}%</div>
                <div class="stat-pill-label">Fortschritt</div>
            </div>
        </div>
    </div>

    @if($isEnrolled)
        <!-- Hero-Card: Eingeschrieben -->
        <div class="glass-gold lehrgang-hero">
            <div class="lehrgang-hero-badges">
                <span class="badge-success">Eingeschrieben</span>
                @if($isCompleted)
                    <span class="badge-gold">Abgeschlossen</span>
                @endif
            </div>

            @if($lehrgang->beschreibung)
                <p class="lehrgang-hero-desc">{{ $lehrgang->beschreibung }}</p>
            @endif

            <!-- Fortschritt -->
            <div class="lehrgang-progress-block">
                <div class="lehrgang-progress-header">
                    <span class="lehrgang-progress-label">Dein Fortschritt</span>
                    <span class="text-gradient-gold lehrgang-progress-percent">{{ $progressPercent }}%</span>
                </div>
                <div class="progress-glass">
                    <div class="{{ $isCompleted ? 'progress-fill-success' : 'progress-fill-gold' }}" style="width: {{ $progressPercent }}%"></div>
                </div>
                <div class="lehrgang-progress-sub">
                    <span>{{ $solvedCount }}/{{ $totalCount }} Fragen bearbeitet</span>
                    @if($isCompleted)
                        <span class="lehrgang-progress-sub-done">Fertig</span>
                    @endif
                </div>
            </div>

            <!-- Aktionen -->
            <div class="lehrgang-actions">
                @if($isCompleted)
                    <span class="btn-ghost btn-sm" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.25);">Lehrgang abgeschlossen</span>
                @else
                    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="btn-primary">Jetzt lernen</a>
                @endif
                <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost btn-sm">Alle Lehrgänge</a>
            </div>
        </div>

        <!-- Abschnitte -->
        <div class="section-header">
            <h2 class="section-title">Lernabschnitte</h2>
        </div>

        <div class="lehrgang-sections">
            @foreach($sections as $section)
                @php
                    $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                    $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Abschnitt {$sectionNr}";
                    $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                    $sectionSolvedCount = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                        ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id)->where('lernabschnitt', $sectionNr))
                        ->where('solved', true)
                        ->count();
                    $sectionProgress = $sectionQuestionCount > 0 ? round(($sectionSolvedCount / $sectionQuestionCount) * 100) : 0;
                    $sectionComplete = $sectionProgress == 100 && $sectionSolvedCount > 0;
                @endphp
                <div class="glass-subtle lehrgang-section-item">
                    <div class="lehrgang-section-info">
                        <div class="lehrgang-section-name">{{ $sectionName }}</div>
                        <div class="lehrgang-section-meta">{{ $sectionQuestionCount }} Fragen, {{ $sectionSolvedCount }} gelöst</div>
                    </div>
                    <div class="lehrgang-section-progress">
                        <div class="lehrgang-section-bar">
                            <div class="lehrgang-section-bar-fill" style="width: {{ $sectionProgress }}%; background: {{ $sectionComplete ? 'linear-gradient(90deg, #22c55e, #16a34a)' : 'var(--gradient-gold)' }};"></div>
                        </div>
                        <span class="lehrgang-section-percent">{{ $sectionProgress }}%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Austritt -->
        <div class="lehrgang-unenroll">
            <form action="{{ route('lehrgaenge.unenroll', $lehrgang->slug) }}" method="POST"
                  onsubmit="return confirm('Lehrgang verlassen? Dein Fortschritt bleibt gespeichert.');">
                @csrf
                <button type="submit" class="btn-ghost btn-sm" style="color: #ef4444; border-color: rgba(239, 68, 68, 0.25);">Lehrgang verlassen</button>
            </form>
            <p class="lehrgang-unenroll-hint">Fortschritt bleibt erhalten</p>
        </div>

    @else
        <!-- Card: Einschreiben -->
        <div class="glass-gold lehrgang-enroll">
            <div class="lehrgang-enroll-icon">
                <i class="bi bi-mortarboard"></i>
            </div>
            <h2 class="lehrgang-enroll-title">Bereit loszulegen?</h2>
            <p class="lehrgang-enroll-desc">
                @if($lehrgang->beschreibung)
                    {{ $lehrgang->beschreibung }}
                @else
                    Schreibe dich ein und erhalte Zugang zu allen {{ $questionCount }} Fragen.
                @endif
            </p>
            <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary">Jetzt beitreten</button>
            </form>
        </div>

        <!-- Abschnitte (Vorschau) -->
        <div class="section-header">
            <h2 class="section-title">Lernabschnitte</h2>
        </div>

        <div class="lehrgang-sections">
            @foreach($sections as $section)
                @php
                    $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                    $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Abschnitt {$sectionNr}";
                    $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                @endphp
                <div class="glass-subtle lehrgang-section-item">
                    <div class="lehrgang-section-info">
                        <div class="lehrgang-section-name">{{ $sectionName }}</div>
                        <div class="lehrgang-section-meta">{{ $sectionQuestionCount }} Fragen</div>
                    </div>
                    <div class="lehrgang-section-progress">
                        <div class="lehrgang-section-bar">
                            <div class="lehrgang-section-bar-fill" style="width: 0%; background: var(--gradient-gold);"></div>
                        </div>
                        <span class="lehrgang-section-percent">0%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Zurück -->
        <div class="lehrgang-back">
            <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost btn-sm">Zurück zur Übersicht</a>
        </div>
    @endif
</div>
@endsection
