@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@push('styles')
<style>
    /* ===== KOMPLETT MOBILE-FIRST - KEINE EXTERNEN ABHÄNGIGKEITEN ===== */

    /* Global overflow fix für Mobile */
    .lg-page {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        padding: 0.75rem;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    .lg-page * {
        box-sizing: border-box;
    }

    @media (min-width: 640px) {
        .lg-page {
            padding: 2rem;
        }
    }

    /* Header */
    .lg-title {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.125rem;
        line-height: 1.2;
        word-wrap: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
    }

    @media (min-width: 640px) {
        .lg-title {
            font-size: 2rem;
        }
    }

    .lg-subtitle {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    @media (min-width: 640px) {
        .lg-subtitle {
            margin-bottom: 1.5rem;
        }
    }

    /* Hero Box */
    .lg-hero {
        background: rgba(251, 191, 36, 0.08);
        border: 1px solid rgba(251, 191, 36, 0.2);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        width: 100%;
        overflow: hidden;
    }

    @media (min-width: 640px) {
        .lg-hero {
            padding: 1.5rem;
            border-radius: 1.25rem 0.5rem 1.25rem 0.5rem;
            margin-bottom: 1.5rem;
        }
    }

    .lg-hero-badge {
        margin-bottom: 0.75rem;
    }

    .lg-badge-enrolled {
        display: inline-block;
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.625rem;
        border-radius: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .lg-hero-desc {
        font-size: 0.85rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 1rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    @media (min-width: 640px) {
        .lg-hero-desc {
            font-size: 0.95rem;
            line-height: 1.6;
        }
    }

    /* Stats im Hero - Mobile: Vertikal, Desktop: Grid */
    .lg-stats {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    @media (min-width: 640px) {
        .lg-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            padding-top: 1rem;
        }
    }

    .lg-stat {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
    }

    @media (min-width: 640px) {
        .lg-stat {
            flex-direction: column;
            text-align: center;
            padding: 0.875rem;
            border-radius: 0.625rem;
        }
    }

    .lg-stat-label {
        font-size: 0.65rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    @media (min-width: 640px) {
        .lg-stat-label {
            font-size: 0.7rem;
            order: 2;
            margin-top: 0.125rem;
        }
    }

    .lg-stat-val {
        font-size: 1.125rem;
        font-weight: 800;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    @media (min-width: 640px) {
        .lg-stat-val {
            font-size: 1.5rem;
            order: 1;
        }
    }

    /* Progress Box */
    .lg-progress-box {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 0.625rem;
        padding: 0.875rem;
        margin-bottom: 1rem;
        width: 100%;
        overflow: hidden;
    }

    @media (min-width: 640px) {
        .lg-progress-box {
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }
    }

    .lg-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .lg-progress-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .lg-progress-pct {
        font-size: 0.95rem;
        font-weight: 800;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .lg-progress-bar {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 0.375rem;
    }

    .lg-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 3px;
        transition: width 0.4s ease;
    }

    .lg-progress-fill.done {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .lg-progress-info {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .lg-progress-done {
        color: #22c55e;
        font-weight: 600;
    }

    /* Buttons */
    .lg-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
        width: 100%;
    }

    @media (min-width: 640px) {
        .lg-buttons {
            flex-direction: row;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
    }

    .lg-btn {
        display: block;
        width: 100%;
        text-align: center;
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .lg-btn-primary {
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        color: #0a0a0b;
    }

    .lg-btn-ghost {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: var(--text-primary);
    }

    .lg-btn-done {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.25);
        color: #22c55e;
    }

    /* Section Header */
    .lg-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.625rem;
        padding-left: 0.5rem;
        border-left: 3px solid #fbbf24;
    }

    @media (min-width: 640px) {
        .lg-section-title {
            font-size: 1.1rem;
            margin-bottom: 0.875rem;
        }
    }

    /* Section Items */
    .lg-sections {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
        margin-bottom: 1.25rem;
        width: 100%;
        overflow: hidden;
    }

    @media (min-width: 640px) {
        .lg-sections {
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
    }

    .lg-section-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        padding: 0.625rem 0.75rem;
        border-radius: 0.5rem;
        gap: 0.5rem;
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }

    @media (min-width: 640px) {
        .lg-section-item {
            padding: 0.875rem 1rem;
        }
    }

    .lg-section-info {
        flex: 1;
        min-width: 0;
        overflow: hidden;
    }

    .lg-section-name {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        display: block;
    }

    @media (min-width: 640px) {
        .lg-section-name {
            font-size: 0.9rem;
            white-space: normal;
        }
    }

    .lg-section-meta {
        font-size: 0.65rem;
        color: var(--text-muted);
        margin-top: 0.125rem;
    }

    .lg-section-right {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        flex-shrink: 0;
    }

    .lg-section-bar {
        width: 40px;
        height: 4px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 2px;
        overflow: hidden;
    }

    @media (min-width: 640px) {
        .lg-section-bar {
            width: 60px;
        }
    }

    .lg-section-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 2px;
    }

    .lg-section-bar-fill.done {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .lg-section-pct {
        font-size: 0.65rem;
        font-weight: 700;
        color: var(--text-secondary);
        min-width: 28px;
        text-align: right;
    }

    /* Enroll Box */
    .lg-enroll {
        text-align: center;
        padding: 1.25rem 1rem;
        border: 2px dashed rgba(255, 255, 255, 0.1);
        border-radius: 0.75rem;
        margin-bottom: 1.25rem;
    }

    @media (min-width: 640px) {
        .lg-enroll {
            padding: 2rem 1.5rem;
        }
    }

    .lg-enroll-icon {
        font-size: 2rem;
        color: var(--text-muted);
        opacity: 0.5;
        margin-bottom: 0.5rem;
    }

    .lg-enroll-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    @media (min-width: 640px) {
        .lg-enroll-title {
            font-size: 1.25rem;
        }
    }

    .lg-enroll-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    /* Unenroll */
    .lg-unenroll {
        text-align: center;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        width: 100%;
        overflow: hidden;
    }

    .lg-unenroll-hint {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    .lg-unenroll form {
        display: inline-block;
        max-width: 100%;
    }

    .lg-btn-danger {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ef4444;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        max-width: 100%;
        white-space: nowrap;
    }

    /* Back Link */
    .lg-back {
        text-align: center;
        margin-top: 1rem;
    }

    /* ===== Light Mode ===== */
    html.light-mode .lg-hero {
        background: rgba(217, 119, 6, 0.08);
        border-color: rgba(217, 119, 6, 0.2);
    }

    html.light-mode .lg-stats {
        border-top-color: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .lg-stat {
        background: rgba(0, 51, 127, 0.04);
    }

    html.light-mode .lg-stat-val,
    html.light-mode .lg-progress-pct {
        background: linear-gradient(90deg, #d97706, #b45309);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    html.light-mode .lg-progress-box,
    html.light-mode .lg-section-item {
        background: rgba(0, 51, 127, 0.03);
        border-color: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .lg-progress-bar,
    html.light-mode .lg-section-bar {
        background: rgba(0, 51, 127, 0.08);
    }

    html.light-mode .lg-btn-ghost {
        border-color: rgba(0, 51, 127, 0.15);
    }

    html.light-mode .lg-enroll {
        border-color: rgba(0, 51, 127, 0.15);
    }

    html.light-mode .lg-unenroll {
        border-top-color: rgba(0, 51, 127, 0.08);
    }
</style>
@endpush

@section('content')
<div class="lg-page">
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
    <h1 class="lg-title">{{ $lehrgang->lehrgang }}</h1>
    <p class="lg-subtitle">Spezialisierter THW-Lehrgang</p>

    <!-- Hero -->
    <div class="lg-hero">
        @if($isEnrolled)
            <div class="lg-hero-badge">
                <span class="lg-badge-enrolled">Eingeschrieben</span>
            </div>
        @endif

        @if($lehrgang->beschreibung)
            <p class="lg-hero-desc">{{ $lehrgang->beschreibung }}</p>
        @endif

        <div class="lg-stats">
            <div class="lg-stat">
                <span class="lg-stat-label">Fragen</span>
                <span class="lg-stat-val">{{ $questionCount }}</span>
            </div>
            <div class="lg-stat">
                <span class="lg-stat-label">Abschnitte</span>
                <span class="lg-stat-val">{{ $sectionCount }}</span>
            </div>
            <div class="lg-stat">
                <span class="lg-stat-label">Fortschritt</span>
                <span class="lg-stat-val">{{ $isEnrolled ? $progressPercent : 0 }}%</span>
            </div>
        </div>
    </div>

    @if($isEnrolled)
        <!-- Progress -->
        <div class="lg-progress-box">
            <div class="lg-progress-header">
                <span class="lg-progress-title">Dein Fortschritt</span>
                <span class="lg-progress-pct">{{ $progressPercent }}%</span>
            </div>
            <div class="lg-progress-bar">
                <div class="lg-progress-fill {{ $isCompleted ? 'done' : '' }}" style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="lg-progress-info">
                <span>{{ $solvedCount }}/{{ $totalCount }} Fragen</span>
                @if($isCompleted)
                    <span class="lg-progress-done">Abgeschlossen</span>
                @endif
            </div>
        </div>

        <!-- Buttons -->
        <div class="lg-buttons">
            @if($isCompleted)
                <span class="lg-btn lg-btn-done">Lehrgang abgeschlossen</span>
            @else
                <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="lg-btn lg-btn-primary">Jetzt lernen</a>
            @endif
            <a href="{{ route('lehrgaenge.index') }}" class="lg-btn lg-btn-ghost">Alle Lehrgänge</a>
        </div>

        <!-- Sections -->
        <h2 class="lg-section-title">Lernabschnitte</h2>

        @php
            $lernabschnittNamen = \App\Models\LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
                ->pluck('lernabschnitt', 'lernabschnitt_nr')
                ->toArray();
        @endphp

        <div class="lg-sections">
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
                <div class="lg-section-item">
                    <div class="lg-section-info">
                        <div class="lg-section-name">{{ $sectionName }}</div>
                        <div class="lg-section-meta">{{ $sectionQuestionCount }} Fragen, {{ $sectionSolvedCount }} gelöst</div>
                    </div>
                    <div class="lg-section-right">
                        <div class="lg-section-bar">
                            <div class="lg-section-bar-fill {{ $sectionComplete ? 'done' : '' }}" style="width: {{ $sectionProgress }}%"></div>
                        </div>
                        <span class="lg-section-pct">{{ $sectionProgress }}%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Unenroll -->
        <div class="lg-unenroll">
            <form action="{{ route('lehrgaenge.unenroll', $lehrgang->slug) }}" method="POST" style="display: inline;"
                  onsubmit="return confirm('Lehrgang verlassen? Dein Fortschritt bleibt gespeichert.');">
                @csrf
                <button type="submit" class="lg-btn-danger">Lehrgang verlassen</button>
            </form>
            <p class="lg-unenroll-hint">Fortschritt bleibt erhalten</p>
        </div>

    @else
        <!-- Enroll -->
        <div class="lg-enroll">
            <div class="lg-enroll-icon"><i class="bi bi-mortarboard"></i></div>
            <h2 class="lg-enroll-title">Bereit loszulegen?</h2>
            <p class="lg-enroll-desc">Schreibe dich ein und erhalte Zugang zu allen {{ $questionCount }} Fragen.</p>
            <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="lg-btn lg-btn-primary" style="display: inline-block; width: auto; padding: 0.75rem 1.5rem;">Jetzt beitreten</button>
            </form>
        </div>

        <!-- Preview Sections -->
        <h2 class="lg-section-title">Lernabschnitte</h2>

        @php
            $lernabschnittNamen = \App\Models\LehrgangLernabschnitt::where('lehrgang_id', $lehrgang->id)
                ->pluck('lernabschnitt', 'lernabschnitt_nr')
                ->toArray();
        @endphp

        <div class="lg-sections">
            @foreach($sections as $section)
                @php
                    $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                    $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Abschnitt {$sectionNr}";
                    $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                @endphp
                <div class="lg-section-item">
                    <div class="lg-section-info">
                        <div class="lg-section-name">{{ $sectionName }}</div>
                        <div class="lg-section-meta">{{ $sectionQuestionCount }} Fragen</div>
                    </div>
                    <div class="lg-section-right">
                        <div class="lg-section-bar">
                            <div class="lg-section-bar-fill" style="width: 0%"></div>
                        </div>
                        <span class="lg-section-pct">0%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Back -->
        <div class="lg-back">
            <a href="{{ route('lehrgaenge.index') }}" class="lg-btn lg-btn-ghost" style="display: inline-block; width: auto; padding: 0.625rem 1.25rem;">Zurück</a>
        </div>
    @endif
</div>
@endsection
