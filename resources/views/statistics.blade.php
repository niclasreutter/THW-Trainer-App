@extends('layouts.landing')

@section('title', 'Statistiken - THW Trainer')
@section('description', 'Öffentliche Statistiken über alle beantworteten Fragen im THW-Trainer.')

@push('styles')
<style>
    /* ============================================
       STATISTIK PAGE — Landing Light Mode Design
       ============================================ */

    .stats-page {
        background-color: #ffffff;
    }

    /* Hero */
    .stats-hero {
        background: linear-gradient(135deg, #00337F 0%, #004db3 100%);
        padding: 5rem 1.5rem 6rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .stats-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .stats-hero h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 0.75rem;
        letter-spacing: -0.02em;
    }

    .stats-hero h1 span {
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stats-hero p {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.75);
        font-weight: 400;
        max-width: 500px;
        margin: 0 auto;
    }

    @media (max-width: 640px) {
        .stats-hero { padding: 3rem 1rem 4rem; }
        .stats-hero h1 { font-size: 1.75rem; }
        .stats-hero p { font-size: 0.95rem; }
    }

    /* Stats Strip (overlapping hero) */
    .stats-strip {
        max-width: 900px;
        margin: -2.5rem auto 0;
        background: #ffffff;
        border-radius: 1.25rem;
        box-shadow: 0 8px 40px rgba(0, 51, 127, 0.1), 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 1.75rem 2rem;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        position: relative;
        z-index: 10;
    }

    .stats-strip-item {
        text-align: center;
        position: relative;
    }

    .stats-strip-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: -0.75rem;
        top: 15%;
        height: 70%;
        width: 2px;
        background: linear-gradient(to bottom, transparent, #fbbf24, transparent);
        opacity: 0.5;
    }

    .stats-strip-value {
        font-size: 2rem;
        font-weight: 800;
        color: #00337F;
        line-height: 1.2;
    }

    .stats-strip-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .stats-strip {
            grid-template-columns: repeat(2, 1fr);
            margin: -2rem 1rem 0;
            padding: 1.25rem;
            gap: 1rem;
        }
        .stats-strip-item:nth-child(2)::after { display: none; }
        .stats-strip-value { font-size: 1.5rem; }
    }

    @media (max-width: 480px) {
        .stats-strip { grid-template-columns: repeat(2, 1fr); }
        .stats-strip-item::after { display: none; }
        .stats-strip-value { font-size: 1.25rem; }
        .stats-strip-label { font-size: 0.7rem; }
    }

    /* Content Container */
    .stats-content {
        max-width: 1100px;
        margin: 0 auto;
        padding: 3rem 1.5rem;
    }

    /* Section Header */
    .stats-section-header {
        margin-bottom: 1.5rem;
    }

    .stats-section-header h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .stats-section-header p {
        font-size: 0.95rem;
        color: #64748b;
        line-height: 1.6;
    }

    /* Card */
    .stats-card {
        background: #ffffff;
        border: 1px solid rgba(0, 51, 127, 0.07);
        box-shadow: 0 4px 20px rgba(0, 51, 127, 0.05);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        box-shadow: 0 8px 30px rgba(0, 51, 127, 0.08);
    }

    .stats-card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stats-card-title i {
        color: #00337F;
    }

    /* Charts Grid */
    .stats-charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 3rem;
    }

    @media (max-width: 768px) {
        .stats-charts-grid { grid-template-columns: 1fr; }
    }

    .stats-chart-container {
        height: 220px;
        position: relative;
        width: 100%;
    }

    /* Activity Grid */
    .stats-activity-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media (max-width: 640px) {
        .stats-activity-grid { grid-template-columns: 1fr; }
    }

    .stats-activity-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .stats-weekday-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
    }

    .stats-weekday-name {
        font-size: 0.75rem;
        color: #64748b;
        width: 24px;
        font-weight: 500;
    }

    .stats-weekday-bar-bg {
        flex: 1;
        height: 18px;
        background: #f1f5f9;
        border-radius: 4px;
        overflow: hidden;
    }

    .stats-weekday-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 4px;
        min-width: 3px;
        transition: width 0.6s ease;
    }

    .stats-weekday-count {
        font-size: 0.75rem;
        color: #475569;
        font-weight: 600;
        width: 36px;
        text-align: right;
    }

    .stats-hours-chart {
        display: flex;
        align-items: flex-end;
        height: 60px;
        gap: 2px;
    }

    .stats-hour-bar {
        flex: 1;
        background: #00337F;
        border-radius: 3px 3px 0 0;
        min-height: 3px;
        opacity: 0.7;
        transition: opacity 0.2s, height 0.6s ease;
    }

    .stats-hour-bar:hover {
        opacity: 1;
    }

    .stats-hours-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
        font-size: 0.65rem;
        color: #94a3b8;
    }

    /* Sections List */
    .stats-sections-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .stats-section-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 0.75rem;
        background: #f8fafc;
        border-radius: 0.5rem;
        transition: background 0.2s;
    }

    .stats-section-row:hover {
        background: #f1f5f9;
    }

    .stats-section-num {
        font-weight: 700;
        color: #00337F;
        min-width: 24px;
        font-size: 0.85rem;
    }

    .stats-section-info {
        flex: 1;
        min-width: 0;
    }

    .stats-section-name {
        font-size: 0.85rem;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
    }

    .stats-section-attempts {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .stats-section-bar {
        width: 80px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .stats-section-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        border-radius: 3px;
    }

    .stats-section-rate {
        font-size: 0.85rem;
        font-weight: 700;
        color: #22c55e;
        min-width: 42px;
        text-align: right;
    }

    @media (max-width: 640px) {
        .stats-section-bar { width: 50px; }
        .stats-section-name { font-size: 0.75rem; }
    }

    /* Questions Grid */
    .stats-questions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 3rem;
    }

    @media (max-width: 768px) {
        .stats-questions-grid { grid-template-columns: 1fr; }
    }

    .stats-question-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .stats-question-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 0.5rem;
        border-left: 3px solid transparent;
    }

    .stats-question-item.wrong { border-left-color: #ef4444; }
    .stats-question-item.correct { border-left-color: #22c55e; }

    .stats-question-rank {
        font-weight: 700;
        color: #94a3b8;
        font-size: 0.8rem;
        min-width: 20px;
    }

    .stats-question-content {
        flex: 1;
        min-width: 0;
    }

    .stats-question-text {
        font-size: 0.85rem;
        color: #1e293b;
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }

    .stats-question-meta {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .stats-question-rate {
        font-weight: 700;
        font-size: 0.85rem;
    }

    .stats-question-rate.wrong { color: #ef4444; }
    .stats-question-rate.correct { color: #22c55e; }

    /* Empty State */
    .stats-empty {
        text-align: center;
        padding: 1.5rem;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    /* Info Box */
    .stats-info-box {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 1.25rem 1.5rem;
        background: #f0f9ff;
        border: 1px solid rgba(0, 51, 127, 0.1);
        border-radius: 0.75rem;
        margin-top: 2rem;
    }

    .stats-info-box i {
        color: #00337F;
        font-size: 1.1rem;
        margin-top: 0.1rem;
        flex-shrink: 0;
    }

    .stats-info-box p {
        font-size: 0.85rem;
        color: #475569;
        margin: 0;
        line-height: 1.6;
    }

    /* Exam Pass Rate Donut */
    .stats-exam-summary {
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .stats-donut-wrap {
        position: relative;
        width: 100px;
        height: 100px;
        flex-shrink: 0;
    }

    .stats-donut-wrap svg {
        transform: rotate(-90deg);
    }

    .stats-donut-label {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .stats-donut-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: #00337F;
        line-height: 1;
    }

    .stats-donut-text {
        font-size: 0.6rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    .stats-exam-details {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .stats-exam-detail {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #475569;
    }

    .stats-exam-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .stats-exam-dot.passed { background: #22c55e; }
    .stats-exam-dot.failed { background: #ef4444; }
</style>
@endpush

@section('content')
<div class="stats-page">
    {{-- Hero --}}
    <section class="stats-hero" aria-label="Statistiken Übersicht">
        <h1>Plattform-<span>Statistiken</span></h1>
        <p>Vollständig anonyme Daten aller beantworteten Fragen</p>
    </section>

    {{-- Stats Strip --}}
    <section class="px-4 sm:px-6 lg:px-8" style="background-color: #ffffff;" aria-label="Kennzahlen">
        <div class="stats-strip">
            <div class="stats-strip-item">
                <div class="stats-strip-value">{{ number_format($totalAnswered) }}</div>
                <div class="stats-strip-label">Fragen beantwortet</div>
            </div>
            <div class="stats-strip-item">
                <div class="stats-strip-value">{{ $successRate }}%</div>
                <div class="stats-strip-label">Richtig beantwortet</div>
            </div>
            <div class="stats-strip-item">
                <div class="stats-strip-value">{{ number_format($totalExams) }}</div>
                <div class="stats-strip-label">Prüfungen absolviert</div>
            </div>
            <div class="stats-strip-item">
                <div class="stats-strip-value">{{ $examPassRate }}%</div>
                <div class="stats-strip-label">Bestehensquote</div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <div class="stats-content">

        {{-- Charts --}}
        <div class="stats-section-header">
            <h2>Aktivität der letzten 30 Tage</h2>
            <p>Beantwortete Fragen und absolvierte Prüfungen im Zeitverlauf</p>
        </div>
        <div class="stats-charts-grid">
            <div class="stats-card">
                <h3 class="stats-card-title"><i class="bi bi-graph-up"></i> Beantwortete Fragen</h3>
                <div class="stats-chart-container">
                    <canvas id="questionsChart"></canvas>
                </div>
            </div>
            <div class="stats-card">
                <h3 class="stats-card-title"><i class="bi bi-bar-chart"></i> Prüfungen</h3>
                <div class="stats-chart-container">
                    <canvas id="examsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Prüfungs-Übersicht + Aktivität --}}
        <div class="stats-charts-grid" style="margin-bottom: 3rem;">
            {{-- Prüfungs-Übersicht --}}
            <div class="stats-card">
                <h3 class="stats-card-title"><i class="bi bi-clipboard-check"></i> Prüfungs-Übersicht</h3>
                <div class="stats-exam-summary">
                    <div class="stats-donut-wrap">
                        @php
                            $circumference = 2 * M_PI * 40;
                            $passOffset = $circumference - ($circumference * $examPassRate / 100);
                        @endphp
                        <svg width="100" height="100" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="10"/>
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#22c55e" stroke-width="10"
                                    stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $passOffset }}"
                                    stroke-linecap="round"/>
                        </svg>
                        <div class="stats-donut-label">
                            <span class="stats-donut-value">{{ $examPassRate }}%</span>
                            <span class="stats-donut-text">bestanden</span>
                        </div>
                    </div>
                    <div class="stats-exam-details">
                        <div class="stats-exam-detail">
                            <span class="stats-exam-dot passed"></span>
                            <span><strong>{{ number_format($passedExams) }}</strong> bestanden</span>
                        </div>
                        <div class="stats-exam-detail">
                            <span class="stats-exam-dot failed"></span>
                            <span><strong>{{ number_format($failedExams) }}</strong> nicht bestanden</span>
                        </div>
                        <div class="stats-exam-detail" style="color: #94a3b8; font-size: 0.8rem;">
                            {{ number_format($totalExams) }} Prüfungen insgesamt
                        </div>
                    </div>
                </div>
            </div>

            {{-- Wann wird gelernt? --}}
            <div class="stats-card">
                <h3 class="stats-card-title"><i class="bi bi-clock"></i> Wann wird gelernt?</h3>
                <div class="stats-activity-grid" style="grid-template-columns: 1fr;">
                    <div>
                        <div class="stats-activity-label">Nach Wochentag (letzte 30 Tage)</div>
                        @php
                            $weekdays = ['', 'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
                            $maxW = max($activityByWeekday ?: [1]);
                        @endphp
                        @foreach([2,3,4,5,6,7,1] as $day)
                            <div class="stats-weekday-row">
                                <span class="stats-weekday-name">{{ $weekdays[$day] }}</span>
                                <div class="stats-weekday-bar-bg">
                                    <div class="stats-weekday-bar-fill" style="width: {{ $maxW > 0 ? (($activityByWeekday[$day] ?? 0) / $maxW * 100) : 0 }}%"></div>
                                </div>
                                <span class="stats-weekday-count">{{ number_format($activityByWeekday[$day] ?? 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Uhrzeitverteilung --}}
        <div class="stats-card" style="margin-bottom: 3rem;">
            <h3 class="stats-card-title"><i class="bi bi-bar-chart-steps"></i> Aktivität nach Uhrzeit (letzte 30 Tage)</h3>
            @php $maxH = max($peakHours ?: [1]); @endphp
            <div class="stats-hours-chart">
                @for($h = 0; $h < 24; $h++)
                    <div class="stats-hour-bar" style="height: {{ $maxH > 0 ? (($peakHours[$h] ?? 0) / $maxH * 100) : 0 }}%" title="{{ $h }}:00 Uhr: {{ number_format($peakHours[$h] ?? 0) }} Fragen"></div>
                @endfor
            </div>
            <div class="stats-hours-labels">
                <span>0 Uhr</span><span>6 Uhr</span><span>12 Uhr</span><span>18 Uhr</span><span>24 Uhr</span>
            </div>
        </div>

        {{-- Lernabschnitte --}}
        @if($sectionStats->isNotEmpty())
        <div class="stats-section-header">
            <h2>Erfolgsrate nach Lernabschnitt</h2>
            <p>Wie gut werden die Fragen der einzelnen Lernabschnitte beantwortet?</p>
        </div>
        <div class="stats-card" style="margin-bottom: 3rem;">
            <div class="stats-sections-list">
                @php
                    $sectionNames = [
                        1 => 'THW im Gefüge des Zivil- und Katastrophenschutzes',
                        2 => 'Arbeitssicherheit und Gesundheitsschutz',
                        3 => 'Leinen, Drahtseile, Ketten, Schlingen',
                        4 => 'Arbeiten mit Leitern',
                        5 => 'Stromerzeugung und Beleuchtung',
                        6 => 'Metall-, Holz- und Steinbearbeitung',
                        7 => 'Bewegen von Lasten',
                        8 => 'Arbeiten am und auf dem Wasser',
                        9 => 'Einsatzgrundlagen',
                        10 => 'Grundlagen der Rettung und Bergung'
                    ];
                @endphp
                @foreach($sectionStats as $stat)
                    <div class="stats-section-row">
                        <span class="stats-section-num">{{ $stat->lernabschnitt }}.</span>
                        <div class="stats-section-info">
                            <div class="stats-section-name">{{ $sectionNames[$stat->lernabschnitt] ?? 'Unbekannt' }}</div>
                            <div class="stats-section-attempts">{{ number_format($stat->total_attempts) }} Versuche</div>
                        </div>
                        <div class="stats-section-bar">
                            <div class="stats-section-bar-fill" style="width: {{ $stat->success_rate }}%"></div>
                        </div>
                        <span class="stats-section-rate">{{ $stat->success_rate }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Top Fragen --}}
        <div class="stats-section-header">
            <h2>Schwierigste und einfachste Fragen</h2>
            <p>Basierend auf der Fehlerquote aller Antworten (min. 5 Versuche)</p>
        </div>
        <div class="stats-questions-grid">
            <div class="stats-card" style="border-top: 3px solid #ef4444;">
                <h3 class="stats-card-title"><i class="bi bi-exclamation-triangle" style="color: #ef4444;"></i> Schwierigste Fragen</h3>
                @if($topWrongQuestionsWithDetails->isEmpty())
                    <div class="stats-empty">Noch nicht genug Daten</div>
                @else
                    <div class="stats-question-list">
                        @foreach($topWrongQuestionsWithDetails->take(6) as $i => $item)
                            <div class="stats-question-item wrong">
                                <span class="stats-question-rank">{{ $i + 1 }}.</span>
                                <div class="stats-question-content">
                                    <div class="stats-question-text">{{ Str::limit($item['question']->frage, 70) }}</div>
                                    <div class="stats-question-meta">LA {{ $item['question']->lernabschnitt }} &middot; {{ $item['total_attempts'] }} Versuche</div>
                                </div>
                                <span class="stats-question-rate wrong">{{ $item['error_rate'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="stats-card" style="border-top: 3px solid #22c55e;">
                <h3 class="stats-card-title"><i class="bi bi-check2-circle" style="color: #22c55e;"></i> Einfachste Fragen</h3>
                @if($topCorrectQuestionsWithDetails->isEmpty())
                    <div class="stats-empty">Noch nicht genug Daten</div>
                @else
                    <div class="stats-question-list">
                        @foreach($topCorrectQuestionsWithDetails->take(6) as $i => $item)
                            <div class="stats-question-item correct">
                                <span class="stats-question-rank">{{ $i + 1 }}.</span>
                                <div class="stats-question-content">
                                    <div class="stats-question-text">{{ Str::limit($item['question']->frage, 70) }}</div>
                                    <div class="stats-question-meta">LA {{ $item['question']->lernabschnitt }} &middot; {{ $item['total_attempts'] }} Versuche</div>
                                </div>
                                <span class="stats-question-rate correct">{{ $item['success_rate'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- DSGVO Info --}}
        <div class="stats-info-box">
            <i class="bi bi-shield-check"></i>
            <p>
                Alle angezeigten Statistiken sind <strong>vollständig anonym</strong> und DSGVO-konform.
                Es werden keine personenbezogenen Daten erhoben oder angezeigt.
                Die Daten basieren ausschließlich auf aggregierten Antwort-Ergebnissen ohne Bezug zu einzelnen Nutzern.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    // Light mode styling
    const gridColor = 'rgba(0, 0, 0, 0.06)';
    const textColor = '#64748b';

    Chart.defaults.font.family = "'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = textColor;

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 12,
                    font: { size: 11 },
                    color: textColor
                }
            },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#f5f5f5',
                bodyColor: '#f5f5f5',
                padding: 10,
                cornerRadius: 8,
                borderColor: 'rgba(0, 0, 0, 0.1)',
                borderWidth: 1
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: gridColor },
                ticks: { font: { size: 10 }, color: textColor }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 9 }, maxRotation: 45, color: textColor }
            }
        }
    };

    new Chart(document.getElementById('questionsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Gesamt',
                    data: {!! json_encode($chartData['questionsTotal']) !!},
                    borderColor: '#00337F',
                    backgroundColor: 'rgba(0, 51, 127, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4
                },
                {
                    label: 'Richtig',
                    data: {!! json_encode($chartData['questionsCorrect']) !!},
                    borderColor: '#22c55e',
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4
                },
                {
                    label: 'Falsch',
                    data: {!! json_encode($chartData['questionsWrong']) !!},
                    borderColor: '#ef4444',
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4
                }
            ]
        },
        options: baseOptions
    });

    new Chart(document.getElementById('examsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Bestanden',
                    data: {!! json_encode($chartData['examsPassed']) !!},
                    backgroundColor: '#22c55e',
                    borderRadius: 4
                },
                {
                    label: 'Gesamt',
                    data: {!! json_encode($chartData['examsTotal']) !!},
                    backgroundColor: 'rgba(0, 51, 127, 0.3)',
                    borderRadius: 4
                }
            ]
        },
        options: {
            ...baseOptions,
            plugins: {
                ...baseOptions.plugins,
                legend: { ...baseOptions.plugins.legend, reverse: true }
            }
        }
    });
});
</script>
@endpush
