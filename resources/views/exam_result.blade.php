@extends('layouts.app')

@section('title', 'Prüfungsergebnis')

@push('styles')
<style>
    .result-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .result-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    .result-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .result-main {
        grid-column: span 2;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 380px;
    }

    .result-side {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .result-ring-wrap {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 1.25rem;
    }

    .result-ring-wrap svg {
        width: 150px;
        height: 150px;
        transform: rotate(-90deg);
    }

    .result-ring-bg {
        fill: none;
        stroke: var(--bg-overlay);
        stroke-width: 10;
    }

    .result-ring-fill {
        fill: none;
        stroke-width: 10;
        stroke-linecap: round;
        transition: stroke-dasharray 1s ease-out;
    }

    .result-ring-fill.passed {
        stroke: var(--success);
    }

    .result-ring-fill.failed {
        stroke: var(--error);
    }

    .result-ring-label {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .result-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.4rem;
        border-radius: 2rem;
        font-weight: 700;
        font-size: 1.05rem;
        margin-bottom: 1.5rem;
    }

    .result-badge.passed {
        background: rgba(34, 197, 94, 0.15);
        color: var(--success);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .result-badge.failed {
        background: rgba(239, 68, 68, 0.15);
        color: var(--error);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .exam-progress-dots {
        display: flex;
        gap: 0.6rem;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .exam-dot {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        background: var(--bg-overlay);
        color: var(--text-muted);
        transition: background 0.3s, color 0.3s;
    }

    .exam-dot.completed {
        background: var(--success);
        color: #fff;
    }

    .side-stat {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .side-stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .side-stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .side-stat-value.error { color: var(--error); }
    .side-stat-value.success { color: var(--success); }

    .result-divider {
        border: none;
        border-top: 1px solid rgba(255,255,255,0.08);
        margin: 0.5rem 0;
    }

    @media (max-width: 900px) {
        .result-grid { grid-template-columns: 1fr 1fr; }
        .result-main { grid-column: span 2; min-height: auto; }
    }

    @media (max-width: 600px) {
        .result-container { padding: 1rem; }
        .result-grid { grid-template-columns: 1fr; }
        .result-main, .result-side { grid-column: span 1; }
    }

    html.light-mode .result-ring-bg { stroke: #e5e7eb; }
    html.light-mode .result-badge.passed { background: rgba(34,197,94,0.1); }
    html.light-mode .result-badge.failed { background: rgba(239,68,68,0.1); }
    html.light-mode .exam-dot { background: #e5e7eb; color: #6b7280; }
    html.light-mode .result-divider { border-top-color: rgba(0,0,0,0.1); }
</style>
@endpush

@section('content')
@php
    $percent = $total > 0 ? round(($correct / $total) * 100) : 0;
    $circumference = 2 * M_PI * 60; // radius 60
    $dashFilled = ($percent / 100) * $circumference;
    $passedCount = Auth::user()->exam_passed_count ?? ($passed ? 1 : 0);
@endphp

<div class="result-container">

    <div class="result-header">
        <h1 class="page-title">Prüfungs<span>ergebnis</span></h1>
        <p class="page-subtitle">Deine Simulation wurde bewertet</p>
    </div>

    <div class="result-grid">

        <!-- Hauptergebnis -->
        <div class="glass-{{ $passed ? 'success' : 'error' }} result-main">

            <!-- Fortschrittsring -->
            <div class="result-ring-wrap">
                <svg viewBox="0 0 150 150">
                    <circle class="result-ring-bg" cx="75" cy="75" r="60"/>
                    <circle class="result-ring-fill {{ $passed ? 'passed' : 'failed' }}"
                            cx="75" cy="75" r="60"
                            stroke-dasharray="{{ $dashFilled }} {{ $circumference - $dashFilled }}"/>
                </svg>
                <div class="result-ring-label">{{ $percent }}%</div>
            </div>

            <p style="color: var(--text-secondary); margin-bottom: 1.25rem;">
                {{ $correct }} von {{ $total }} Fragen richtig
            </p>

            <!-- Bestanden / Nicht Bestanden Badge -->
            <div class="result-badge {{ $passed ? 'passed' : 'failed' }}">
                <i class="bi bi-{{ $passed ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                {{ $passed ? 'Bestanden' : 'Nicht bestanden' }}
            </div>

            @if($passed)
                <!-- Prüfungszähler 1/5 -->
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.75rem;">
                    Bestandene Prüfungen
                </p>
                <div class="exam-progress-dots">
                    @for($i = 1; $i <= 5; $i++)
                        <div class="exam-dot {{ $i <= $passedCount ? 'completed' : '' }}">
                            @if($i <= $passedCount)
                                <i class="bi bi-check-lg"></i>
                            @else
                                {{ $i }}
                            @endif
                        </div>
                    @endfor
                </div>

                @if($done)
                    <div class="alert-glass alert-success" style="max-width: 380px; margin-bottom: 1.25rem;">
                        <i class="bi bi-trophy-fill"></i>
                        Du hast den gesamten Prozess abgeschlossen.
                    </div>
                @endif

                <a href="{{ route('exam.index') }}" class="btn-primary">
                    <i class="bi bi-play-circle-fill"></i> Neue Simulation starten
                </a>
            @else
                <p style="color: var(--text-secondary); max-width: 340px; margin-bottom: 1.5rem; line-height: 1.6;">
                    Mindestens 80% werden benötigt. Übe die Fehler und versuche es erneut.
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center;">
                    <a href="{{ route('failed.index') }}" class="btn-ghost">
                        <i class="bi bi-arrow-repeat"></i> Fehler wiederholen
                    </a>
                    <a href="{{ route('exam.index') }}" class="btn-primary">
                        <i class="bi bi-play-circle-fill"></i> Erneut versuchen
                    </a>
                </div>
            @endif
        </div>

        <!-- Seiteninfo -->
        <div class="glass-tl result-side">
            <div class="section-header">
                <span class="section-title">Ergebnisinfo</span>
            </div>

            <div class="side-stat">
                <span class="side-stat-label">Richtige Antworten</span>
                <span class="side-stat-value">{{ $correct }} / {{ $total }}</span>
            </div>

            <hr class="result-divider">

            <div class="side-stat">
                <span class="side-stat-label">Erforderlich (80 %)</span>
                <span class="side-stat-value">{{ ceil($total * 0.8) }} / {{ $total }}</span>
            </div>

            <hr class="result-divider">

            <div class="side-stat">
                <span class="side-stat-label">Fehler</span>
                <span class="side-stat-value {{ ($total - $correct) > 0 ? 'error' : 'success' }}">
                    {{ $total - $correct }}
                </span>
            </div>

            @if(!$passed)
                <hr class="result-divider">
                <a href="{{ route('practice.menu') }}" class="btn-ghost btn-sm" style="width: 100%; text-align: center; margin-top: 0.25rem;">
                    <i class="bi bi-book"></i> Zum Übungsmenü
                </a>
            @endif
        </div>

    </div>
</div>
@endsection
