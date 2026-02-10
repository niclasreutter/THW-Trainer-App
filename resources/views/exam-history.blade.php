@extends('layouts.app')

@section('title', 'Prüfungshistorie')
@section('description', 'Analysiere deine THW-Prüfungen: Ergebnisse, Trends und Schwachstellen im Überblick.')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    @media (max-width: 600px) {
        .dashboard-container { padding: 1rem; }
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    /* Section Analysis Grid */
    .section-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.75rem;
    }

    .section-bar-card {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .section-bar-track {
        height: 6px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 3px;
        overflow: hidden;
    }

    .section-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 1s ease-out;
    }

    .section-bar-fill.good { background: linear-gradient(90deg, #22c55e, #16a34a); }
    .section-bar-fill.medium { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
    .section-bar-fill.bad { background: linear-gradient(90deg, #ef4444, #dc2626); }

    html.light-mode .section-bar-track {
        background: rgba(0, 51, 127, 0.08);
    }

    /* Trend Chart */
    .trend-chart {
        position: relative;
        height: 200px;
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
        padding: 1rem 0;
    }

    .trend-bar {
        flex: 1;
        min-width: 30px;
        border-radius: 4px 4px 0 0;
        position: relative;
        transition: height 1s ease-out;
        cursor: pointer;
    }

    .trend-bar.passed {
        background: linear-gradient(180deg, #22c55e, #16a34a);
    }

    .trend-bar.failed {
        background: linear-gradient(180deg, #ef4444, #dc2626);
    }

    .trend-bar-label {
        position: absolute;
        bottom: -24px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.65rem;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .trend-bar-value {
        position: absolute;
        top: -22px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
    }

    .trend-threshold {
        position: absolute;
        left: 0;
        right: 0;
        border-top: 2px dashed rgba(251, 191, 36, 0.3);
        z-index: 1;
    }

    .trend-threshold-label {
        position: absolute;
        right: 0;
        top: -16px;
        font-size: 0.65rem;
        color: var(--gold-start);
        font-weight: 600;
    }

    /* Exam History List */
    .exam-list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        transition: background 0.2s;
    }

    .exam-list-item:last-child { border-bottom: none; }
    .exam-list-item:hover { background: rgba(255, 255, 255, 0.03); }

    html.light-mode .exam-list-item { border-bottom-color: rgba(0, 51, 127, 0.06); }
    html.light-mode .exam-list-item:hover { background: rgba(0, 51, 127, 0.03); }

    .exam-number {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .exam-number.passed {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }

    .exam-number.failed {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    /* Recommendation Card */
    .recommendation-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 0.5rem;
    }

    html.light-mode .recommendation-item {
        background: rgba(0, 51, 127, 0.03);
        border-color: rgba(0, 51, 127, 0.06);
    }

    .bento-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .bento-main { grid-column: span 2; grid-row: span 2; padding: 1.5rem; }
    .bento-side { padding: 1.25rem; }
    .bento-wide { grid-column: span 3; padding: 1.5rem; }

    @media (max-width: 900px) {
        .bento-grid { grid-template-columns: 1fr 1fr; }
        .bento-main { grid-column: span 2; grid-row: span 1; }
        .bento-wide { grid-column: span 2; }
    }

    @media (max-width: 600px) {
        .bento-grid { grid-template-columns: 1fr; }
        .bento-main, .bento-wide, .bento-side { grid-column: span 1; }
    }

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
    }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $totalExams = $exams->count();
    $passedExams = $exams->where('is_passed', true)->count();
    $avgPercent = $totalExams > 0 ? round($exams->avg(fn($e) => ($e->correct_answers / 40) * 100)) : 0;
    $bestPercent = $totalExams > 0 ? round($exams->max(fn($e) => ($e->correct_answers / 40) * 100)) : 0;
@endphp

<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Prüfungs<span>historie</span></h1>
        <p class="page-subtitle">Analysiere deine Ergebnisse und finde Schwachstellen</p>
    </header>

    <!-- Stats Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-info"><i class="bi bi-clipboard-check"></i></span>
            <div>
                <div class="stat-pill-value">{{ $totalExams }}</div>
                <div class="stat-pill-label">Prüfungen</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-check-circle"></i></span>
            <div>
                <div class="stat-pill-value">{{ $passedExams }}</div>
                <div class="stat-pill-label">Bestanden</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-bar-chart"></i></span>
            <div>
                <div class="stat-pill-value">{{ $avgPercent }}%</div>
                <div class="stat-pill-label">Schnitt</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #a855f7;"><i class="bi bi-trophy"></i></span>
            <div>
                <div class="stat-pill-value">{{ $bestPercent }}%</div>
                <div class="stat-pill-label">Bestes</div>
            </div>
        </div>
    </div>

    @if($totalExams === 0)
        <div class="glass-slash" style="text-align: center; padding: 3rem 1.5rem;">
            <div style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem; opacity: 0.6;">
                <i class="bi bi-clipboard"></i>
            </div>
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Noch keine Prüfungen</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                Lerne zuerst alle Fragen und starte dann deine erste Prüfung.
            </p>
            <a href="{{ route('exam.index') }}" class="btn-primary btn-sm">Prüfung starten</a>
        </div>
    @else
        <div class="bento-grid">
            <!-- Trend Chart (Main) -->
            <div class="glass-gold bento-main">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem;">Trend der letzten Prüfungen</div>
                <div class="trend-chart" style="margin-bottom: 2rem;">
                    <div class="trend-threshold" style="bottom: {{ (80 / 100) * 200 }}px;">
                        <span class="trend-threshold-label">80% Bestehensgrenze</span>
                    </div>
                    @foreach($trend as $i => $exam)
                        @php
                            $pct = round(($exam->correct_answers / 40) * 100);
                            $height = max(10, ($pct / 100) * 200);
                        @endphp
                        <div class="trend-bar {{ $exam->is_passed ? 'passed' : 'failed' }}"
                             style="height: {{ $height }}px;">
                            <span class="trend-bar-value">{{ $pct }}%</span>
                            <span class="trend-bar-label">{{ $exam->created_at->format('d.m.') }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Vergleich mit Durchschnitt -->
                <div style="display: flex; gap: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800;" class="text-gradient-gold">{{ $avgPercent }}%</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Dein Schnitt</div>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-secondary);">{{ $globalAvgPercent }}%</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Alle Nutzer</div>
                    </div>
                    @if($avgPercent > $globalAvgPercent)
                        <div style="display: flex; align-items: center; gap: 0.25rem; color: #22c55e; font-size: 0.85rem; font-weight: 600;">
                            <i class="bi bi-arrow-up-circle-fill"></i>
                            +{{ $avgPercent - $globalAvgPercent }}% über Durchschnitt
                        </div>
                    @elseif($avgPercent < $globalAvgPercent)
                        <div style="display: flex; align-items: center; gap: 0.25rem; color: #ef4444; font-size: 0.85rem; font-weight: 600;">
                            <i class="bi bi-arrow-down-circle-fill"></i>
                            {{ $avgPercent - $globalAvgPercent }}% unter Durchschnitt
                        </div>
                    @endif
                </div>
            </div>

            <!-- Empfehlung (Side) -->
            <div class="glass-tl bento-side">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 0.75rem;">Empfehlung</div>
                @if(!empty($weakSections))
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.75rem;">Diese Abschnitte solltest du wiederholen:</p>
                    @foreach($weakSections as $section)
                        <div class="recommendation-item">
                            <div style="width: 32px; height: 32px; border-radius: 0.5rem; background: rgba(239, 68, 68, 0.15); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; color: #ef4444; flex-shrink: 0;">
                                {{ $section }}
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">Lernabschnitt {{ $section }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $sectionAnalysis[$section]['percent'] }}% richtig</div>
                            </div>
                            <a href="{{ route('practice.section', $section) }}" style="margin-left: auto;">
                                <i class="bi bi-arrow-right text-gold"></i>
                            </a>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 1rem;">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                        <p style="font-size: 0.85rem; color: var(--text-secondary);">Alle Abschnitte sehen gut aus!</p>
                    </div>
                @endif
            </div>

            <!-- Passrate (Side) -->
            <div class="glass-br bento-side">
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 800;" class="text-gradient-gold">
                        {{ $totalExams > 0 ? round(($passedExams / $totalExams) * 100) : 0 }}%
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Bestehensquote</div>
                </div>
            </div>
        </div>

        <!-- Lernabschnitt-Analyse -->
        @if(!empty($sectionAnalysis))
        <div class="section-header">
            <h2 class="section-title">Stärken & Schwächen pro Abschnitt ({{ $linkedExamCount ?? 0 }} von {{ $exams->count() }} Prüfungen)</h2>
            @if(($linkedExamCount ?? 0) < $exams->count())
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">
                    Fehlende Daten? Führe <code>php artisan app:backfill-exam-links</code> aus.
                </p>
            @endif
        </div>

        <div class="section-grid" style="margin-bottom: 2rem;">
            @foreach($sectionAnalysis as $section => $data)
                @php
                    $barClass = $data['percent'] >= 80 ? 'good' : ($data['percent'] >= 50 ? 'medium' : 'bad');
                @endphp
                <div class="glass section-bar-card">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary);">LA {{ $section }}</span>
                        <span style="font-size: 0.8rem; font-weight: 600; color: {{ $data['percent'] >= 80 ? '#22c55e' : ($data['percent'] >= 50 ? '#fbbf24' : '#ef4444') }};">{{ $data['percent'] }}%</span>
                    </div>
                    <div class="section-bar-track">
                        <div class="section-bar-fill {{ $barClass }}" style="width: {{ $data['percent'] }}%;"></div>
                    </div>
                    <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $data['correct'] }}/{{ $data['total'] }} richtig</span>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Prüfungsliste -->
        <div class="section-header">
            <h2 class="section-title">Alle Prüfungen</h2>
        </div>

        <div class="glass" style="border-radius: 1.5rem 0.5rem 1rem 1rem;">
            @foreach($exams as $i => $exam)
                @php $pct = round(($exam->correct_answers / 40) * 100); @endphp
                <div class="exam-list-item">
                    <div class="exam-number {{ $exam->is_passed ? 'passed' : 'failed' }}">
                        {{ $totalExams - $i }}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-primary);">
                            {{ $exam->correct_answers }}/40 richtig ({{ $pct }}%)
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            {{ $exam->created_at->format('d.m.Y, H:i') }} Uhr
                        </div>
                    </div>
                    <span class="{{ $exam->is_passed ? 'badge-success' : 'badge-error' }}" style="font-size: 0.7rem; font-weight: 700; padding: 0.25rem 0.6rem;">
                        {{ $exam->is_passed ? 'Bestanden' : 'Nicht best.' }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
