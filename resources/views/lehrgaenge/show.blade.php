@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@push('styles')
<style>
    /* ─── Container — Mobile-first, keine Überlappung mit Layout-Padding ─── */
    .lehrgang-container {
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
        padding: 0;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .lehrgang-header {
        margin-bottom: 1.5rem;
        max-width: 100%;
    }

    .lehrgang-header .page-title {
        word-break: break-word;
        overflow-wrap: break-word;
    }

    /* ─── Stats Row — Mobile-optimiert ─── */
    .lehrgang-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .lehrgang-stats .stat-pill {
        flex: 1 1 auto;
        min-width: 0;
        max-width: 100%;
    }

    /* ─── Bento Grid — 3 Spalten Desktop, überschreibt globale 4-Spalten ─── */
    .lehrgang-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr 1fr !important;
        grid-template-rows: auto !important;
        gap: 1rem !important;
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .lehrgang-grid .bento-main {
        grid-column: span 2 !important;
        grid-row: span 2 !important;
        min-height: 280px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .lehrgang-grid .bento-side {
        grid-column: span 1 !important;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* ─── Section Header ─── */
    .lehrgang-section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        padding-left: 0.75rem;
        border-left: 3px solid var(--gold-start);
    }

    .lehrgang-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* ─── Progress Ring ─── */
    .progress-indicator {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .progress-ring {
        width: 56px;
        height: 56px;
        position: relative;
        flex-shrink: 0;
    }

    .progress-ring-bg {
        fill: none;
        stroke: rgba(255, 255, 255, 0.1);
        stroke-width: 6;
    }

    .progress-ring-fill {
        fill: none;
        stroke: url(#goldGradient);
        stroke-width: 6;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: center;
        transition: stroke-dashoffset 1s ease-out;
    }

    .progress-ring-fill-success {
        fill: none;
        stroke: #22c55e;
        stroke-width: 6;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: center;
    }

    .progress-ring-text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .progress-info {
        flex: 1;
        min-width: 0;
    }

    .progress-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .progress-value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* ─── Hero Content ─── */
    .hero-badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .hero-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .hero-desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 1rem;
        flex-grow: 1;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: auto;
    }

    /* ─── Section Items ─── */
    .section-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .section-item {
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        box-sizing: border-box;
    }

    .section-item-info {
        flex: 1;
        min-width: 0;
        overflow: hidden;
    }

    .section-item-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .section-item-meta {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.125rem;
    }

    .section-item-progress {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .section-item-bar {
        width: 50px;
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .section-item-bar-fill {
        height: 100%;
        border-radius: 2px;
    }

    .section-item-percent {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-secondary);
        min-width: 28px;
        text-align: right;
    }

    /* ─── Unenroll Block ─── */
    .unenroll-block {
        text-align: center;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .unenroll-hint {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.375rem;
    }

    /* ─── Empty State ─── */
    .empty-state-icon {
        font-size: 2rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        opacity: 0.6;
    }

    /* ─── Back Link ─── */
    .back-link {
        text-align: center;
        margin-top: 1rem;
    }

    /* ─── Responsive: Tablet (768px) ─── */
    @media (max-width: 768px) {
        .lehrgang-grid {
            grid-template-columns: 1fr 1fr !important;
        }
        .lehrgang-grid .bento-main {
            grid-column: span 2 !important;
            grid-row: span 1 !important;
            min-height: auto;
        }
        .lehrgang-grid .bento-side {
            grid-column: span 1 !important;
        }
    }

    /* ─── Responsive: Mobile (480px) ─── */
    @media (max-width: 480px) {
        .lehrgang-header {
            margin-bottom: 1rem;
        }

        .lehrgang-header .page-title {
            font-size: 1.5rem;
        }

        .lehrgang-header .page-subtitle {
            font-size: 0.9rem;
        }

        .lehrgang-stats {
            gap: 0.375rem;
        }

        .lehrgang-stats .stat-pill {
            padding: 0.5rem 0.75rem;
        }

        .lehrgang-stats .stat-pill-value {
            font-size: 1rem;
        }

        .lehrgang-stats .stat-pill-label {
            font-size: 0.6rem;
        }

        .lehrgang-grid {
            grid-template-columns: 1fr !important;
            gap: 0.75rem !important;
        }

        .lehrgang-grid .bento-main,
        .lehrgang-grid .bento-side {
            grid-column: span 1 !important;
            grid-row: span 1 !important;
            padding: 1rem;
        }

        .lehrgang-grid .bento-main {
            min-height: auto;
        }

        .hero-title {
            font-size: 1.25rem;
        }

        .hero-desc {
            font-size: 0.85rem;
        }

        .progress-ring {
            width: 48px;
            height: 48px;
        }

        .progress-ring-text {
            font-size: 0.8rem;
        }

        .progress-value {
            font-size: 0.9rem;
        }

        .section-item {
            padding: 0.625rem;
            gap: 0.5rem;
        }

        .section-item-name {
            font-size: 0.8rem;
        }

        .section-item-bar {
            width: 40px;
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

    $circumference = 2 * 3.14159 * 26;
    $progressOffset = $circumference - ($progressPercent / 100) * $circumference;
@endphp

<!-- SVG Gradient Definition -->
<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="goldGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#fbbf24"/>
            <stop offset="100%" style="stop-color:#f59e0b"/>
        </linearGradient>
    </defs>
</svg>

<div class="lehrgang-container">
    <!-- Header -->
    <header class="lehrgang-header">
        <h1 class="page-title">{{ $lehrgang->lehrgang }}</h1>
        <p class="page-subtitle">Spezialisierter THW-Lehrgang</p>
    </header>

    <!-- Stats Row -->
    <div class="lehrgang-stats">
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
        <!-- Bento Grid: Enrolled -->
        <div class="lehrgang-grid">
            <!-- Main Hero Card -->
            <div class="glass-gold bento-main">
                <div class="hero-badges">
                    <span class="badge-success">Eingeschrieben</span>
                    @if($isCompleted)
                        <span class="badge-gold">Abgeschlossen</span>
                    @endif
                </div>

                <h2 class="hero-title">Dein<br>Lernfortschritt</h2>

                @if($lehrgang->beschreibung)
                    <p class="hero-desc">{{ $lehrgang->beschreibung }}</p>
                @else
                    <p class="hero-desc">Lerne alle {{ $questionCount }} Fragen dieses Lehrgangs. Jede Frage muss 2x richtig beantwortet werden.</p>
                @endif

                <div class="progress-indicator">
                    <div class="progress-ring">
                        <svg width="64" height="64" viewBox="0 0 64 64">
                            <circle class="progress-ring-bg" cx="32" cy="32" r="26"/>
                            <circle class="{{ $isCompleted ? 'progress-ring-fill-success' : 'progress-ring-fill' }}" cx="32" cy="32" r="26"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $progressOffset }}"/>
                        </svg>
                        <div class="progress-ring-text">{{ $progressPercent }}%</div>
                    </div>
                    <div class="progress-info">
                        <div class="progress-label">Gemeistert</div>
                        <div class="progress-value">{{ $solvedCount }} von {{ $totalCount }}</div>
                    </div>
                </div>

                <div class="hero-actions">
                    @if($isCompleted)
                        <span class="btn-ghost btn-sm" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.25);">Lehrgang abgeschlossen</span>
                    @else
                        <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="btn-primary">Jetzt lernen</a>
                    @endif
                    <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost btn-sm">Alle Lehrgänge</a>
                </div>
            </div>

            <!-- Side: Quick Stats -->
            <div class="glass-tl bento-side">
                <div style="margin-bottom: 0.75rem;">
                    @if($isCompleted)
                        <span class="badge-success">Fertig</span>
                    @else
                        <span class="badge-thw">Aktiv</span>
                    @endif
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Status</h3>
                <p style="font-size: 0.8rem; color: var(--text-secondary);">
                    {{ $solvedCount }}/{{ $totalCount }} Fragen gelöst
                </p>
            </div>

            <!-- Side: Section Overview -->
            <div class="glass-br bento-side">
                <div style="font-size: 2rem; font-weight: 800;" class="text-gradient-gold">{{ $sectionCount }}</div>
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Lernabschnitte</div>
            </div>
        </div>

        <!-- Sections List -->
        <div class="lehrgang-section-header">
            <h2 class="lehrgang-section-title">Lernabschnitte</h2>
        </div>

        <div class="section-list">
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
                <div class="glass-subtle section-item">
                    <div class="section-item-info">
                        <div class="section-item-name">{{ $sectionName }}</div>
                        <div class="section-item-meta">{{ $sectionQuestionCount }} Fragen, {{ $sectionSolvedCount }} gelöst</div>
                    </div>
                    <div class="section-item-progress">
                        <div class="section-item-bar">
                            <div class="section-item-bar-fill" style="width: {{ $sectionProgress }}%; background: {{ $sectionComplete ? 'linear-gradient(90deg, #22c55e, #16a34a)' : 'var(--gradient-gold)' }};"></div>
                        </div>
                        <span class="section-item-percent">{{ $sectionProgress }}%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Unenroll -->
        <div class="unenroll-block">
            <form action="{{ route('lehrgaenge.unenroll', $lehrgang->slug) }}" method="POST"
                  onsubmit="return confirm('Lehrgang verlassen? Dein Fortschritt bleibt gespeichert.');">
                @csrf
                <button type="submit" class="btn-ghost btn-sm" style="color: #ef4444; border-color: rgba(239, 68, 68, 0.25);">Lehrgang verlassen</button>
            </form>
            <p class="unenroll-hint">Fortschritt bleibt erhalten</p>
        </div>

    @else
        <!-- Bento Grid: Not Enrolled -->
        <div class="lehrgang-grid">
            <!-- Main Enroll Card -->
            <div class="glass-gold bento-main" style="justify-content: center; align-items: center; text-align: center;">
                <div class="empty-state-icon">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <h2 class="hero-title" style="text-align: center;">Bereit loszulegen?</h2>
                <p class="hero-desc" style="text-align: center; max-width: 400px;">
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

            <!-- Side: Questions -->
            <div class="glass-tl bento-side">
                <div style="font-size: 2rem; font-weight: 800;" class="text-gradient-gold">{{ $questionCount }}</div>
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Fragen</div>
            </div>

            <!-- Side: Sections -->
            <div class="glass-br bento-side">
                <div style="font-size: 2rem; font-weight: 800;" class="text-gradient-gold">{{ $sectionCount }}</div>
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Abschnitte</div>
            </div>
        </div>

        <!-- Sections Preview -->
        <div class="lehrgang-section-header">
            <h2 class="lehrgang-section-title">Lernabschnitte</h2>
        </div>

        <div class="section-list">
            @foreach($sections as $section)
                @php
                    $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                    $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Abschnitt {$sectionNr}";
                    $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                @endphp
                <div class="glass-subtle section-item">
                    <div class="section-item-info">
                        <div class="section-item-name">{{ $sectionName }}</div>
                        <div class="section-item-meta">{{ $sectionQuestionCount }} Fragen</div>
                    </div>
                    <div class="section-item-progress">
                        <div class="section-item-bar">
                            <div class="section-item-bar-fill" style="width: 0%; background: var(--gradient-gold);"></div>
                        </div>
                        <span class="section-item-percent">0%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Back Link -->
        <div class="back-link">
            <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost btn-sm">Zurück zur Übersicht</a>
        </div>
    @endif
</div>
@endsection
