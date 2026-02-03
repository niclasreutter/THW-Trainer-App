@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

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
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">{{ $lehrgang->lehrgang }}</h1>
        <p class="page-subtitle">Spezialisierter THW-Lehrgang</p>
    </header>

    <!-- Stats Row -->
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
        <!-- Enrolled: Hero Card -->
        <div class="glass-gold" style="padding: 1.5rem; margin-bottom: 1.5rem; position: relative;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <span class="badge-success">Eingeschrieben</span>
                @if($isCompleted)
                    <span class="badge-gold">Abgeschlossen</span>
                @endif
            </div>

            @if($lehrgang->beschreibung)
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem;">{{ $lehrgang->beschreibung }}</p>
            @endif

            <!-- Progress -->
            <div style="margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">Dein Fortschritt</span>
                    <span class="text-gradient-gold" style="font-weight: 800;">{{ $progressPercent }}%</span>
                </div>
                <div class="progress-glass">
                    <div class="{{ $isCompleted ? 'progress-fill-success' : 'progress-fill-gold' }}" style="width: {{ $progressPercent }}%"></div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 0.375rem; font-size: 0.75rem; color: var(--text-muted);">
                    <span>{{ $solvedCount }}/{{ $totalCount }} Fragen bearbeitet</span>
                    @if($isCompleted)
                        <span style="color: var(--success); font-weight: 600;">Fertig</span>
                    @endif
                </div>
            </div>

            <!-- Buttons -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                @if($isCompleted)
                    <span class="btn-ghost btn-sm" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.25);">Lehrgang abgeschlossen</span>
                @else
                    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" class="btn-primary">Jetzt lernen</a>
                @endif
                <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost btn-sm">Alle Lehrgänge</a>
            </div>
        </div>

        <!-- Section Header -->
        <div class="section-header">
            <h2 class="section-title">Lernabschnitte</h2>
        </div>

        <!-- Sections List -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.5rem;">
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
                <div class="glass-subtle" style="padding: 0.875rem 1rem; display: flex; align-items: center; gap: 1rem;">
                    <div style="flex: 1; min-width: 0; overflow: hidden;">
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $sectionName }}</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.125rem;">{{ $sectionQuestionCount }} Fragen, {{ $sectionSolvedCount }} gelöst</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                        <div style="width: 60px; height: 4px; background: rgba(255, 255, 255, 0.1); border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $sectionProgress }}%; background: {{ $sectionComplete ? 'linear-gradient(90deg, #22c55e, #16a34a)' : 'var(--gradient-gold)' }}; border-radius: 2px;"></div>
                        </div>
                        <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-secondary); min-width: 32px; text-align: right;">{{ $sectionProgress }}%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Unenroll -->
        <div style="text-align: center; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.06);">
            <form action="{{ route('lehrgaenge.unenroll', $lehrgang->slug) }}" method="POST" style="display: inline-block;"
                  onsubmit="return confirm('Lehrgang verlassen? Dein Fortschritt bleibt gespeichert.');">
                @csrf
                <button type="submit" class="btn-ghost btn-sm" style="color: #ef4444; border-color: rgba(239, 68, 68, 0.25);">Lehrgang verlassen</button>
            </form>
            <p style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.375rem;">Fortschritt bleibt erhalten</p>
        </div>

    @else
        <!-- Not Enrolled: Enroll Card -->
        <div class="glass-gold" style="padding: 2rem; margin-bottom: 1.5rem; text-align: center;">
            <div style="font-size: 2.5rem; color: var(--text-muted); opacity: 0.5; margin-bottom: 0.75rem;">
                <i class="bi bi-mortarboard"></i>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Bereit loszulegen?</h2>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
                @if($lehrgang->beschreibung)
                    {{ $lehrgang->beschreibung }}
                @else
                    Schreibe dich ein und erhalte Zugang zu allen {{ $questionCount }} Fragen.
                @endif
            </p>
            <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST" style="display: inline-block;">
                @csrf
                <button type="submit" class="btn-primary">Jetzt beitreten</button>
            </form>
        </div>

        <!-- Section Header -->
        <div class="section-header">
            <h2 class="section-title">Lernabschnitte</h2>
        </div>

        <!-- Preview Sections List -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.5rem;">
            @foreach($sections as $section)
                @php
                    $sectionNr = $section->lernabschnitt_nr ?? $section->lernabschnitt ?? null;
                    $sectionName = $lernabschnittNamen[(int)$sectionNr] ?? $lernabschnittNamen[$sectionNr] ?? "Abschnitt {$sectionNr}";
                    $sectionQuestionCount = $lehrgang->questions()->where('lernabschnitt', $sectionNr)->count();
                @endphp
                <div class="glass-subtle" style="padding: 0.875rem 1rem; display: flex; align-items: center; gap: 1rem;">
                    <div style="flex: 1; min-width: 0; overflow: hidden;">
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $sectionName }}</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.125rem;">{{ $sectionQuestionCount }} Fragen</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                        <div style="width: 60px; height: 4px; background: rgba(255, 255, 255, 0.1); border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; width: 0%; background: var(--gradient-gold); border-radius: 2px;"></div>
                        </div>
                        <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-secondary); min-width: 32px; text-align: right;">0%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Back Button -->
        <div style="text-align: center;">
            <a href="{{ route('lehrgaenge.index') }}" class="btn-ghost btn-sm">Zurück zur Übersicht</a>
        </div>
    @endif
</div>
@endsection
