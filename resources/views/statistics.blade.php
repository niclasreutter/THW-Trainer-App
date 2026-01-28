@extends('layouts.landing')

@section('title', 'Statistiken - THW Trainer')
@section('description', 'Öffentliche Statistiken über alle beantworteten Fragen im THW-Trainer.')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    /* Charts Grid - 2 Spalten für Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .charts-grid { grid-template-columns: 1fr; }
    }

    /* Bento Grid - 2 Spalten für Fragen */
    .bento-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .bento-wide { grid-column: span 2; }
    .bento-half { grid-column: span 1; }

    @media (max-width: 768px) {
        .bento-grid { grid-template-columns: 1fr; }
        .bento-wide, .bento-half { grid-column: span 1; }
        .dashboard-container { padding: 1rem; }
    }

    /* Section Headers */
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

    /* Card Titles */
    .card-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-title i {
        color: var(--gold-start);
    }

    /* Charts */
    .chart-container {
        height: 220px;
        position: relative;
        width: 100%;
    }

    /* Lernabschnitte */
    .sections-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .section-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.5rem;
        transition: background 0.2s;
    }

    .section-row:hover {
        background: rgba(255, 255, 255, 0.06);
    }

    .section-number {
        font-weight: 700;
        color: var(--gold-start);
        min-width: 24px;
        font-size: 0.85rem;
    }

    .section-info {
        flex: 1;
        min-width: 0;
    }

    .section-name {
        font-size: 0.8rem;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .section-stats {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    .section-bar {
        width: 80px;
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .section-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        border-radius: 2px;
    }

    .section-rate {
        font-size: 0.8rem;
        font-weight: 700;
        color: #22c55e;
        min-width: 40px;
        text-align: right;
    }

    /* Fragen Listen */
    .question-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .question-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.5rem;
        border-left: 3px solid transparent;
    }

    .question-item.wrong { border-left-color: #ef4444; }
    .question-item.correct { border-left-color: #22c55e; }

    .question-rank {
        font-weight: 700;
        color: var(--text-muted);
        font-size: 0.8rem;
        min-width: 18px;
    }

    .question-content {
        flex: 1;
        min-width: 0;
    }

    .question-text {
        font-size: 0.8rem;
        color: var(--text-primary);
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }

    .question-meta {
        font-size: 0.65rem;
        color: var(--text-muted);
    }

    .question-rate {
        font-weight: 700;
        font-size: 0.85rem;
    }

    .question-rate.wrong { color: #ef4444; }
    .question-rate.correct { color: #22c55e; }

    /* Lehrgänge */
    .lehrgang-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.75rem;
    }

    .lehrgang-item {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .lehrgang-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .lehrgang-stats {
        display: flex;
        gap: 0.75rem;
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .lehrgang-bar {
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .lehrgang-bar-fill {
        height: 100%;
        border-radius: 2px;
    }

    .lehrgang-success {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    /* Aktivität */
    .activity-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 640px) {
        .activity-grid { grid-template-columns: 1fr; }
    }

    .activity-section h4 {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .weekday-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
    }

    .weekday-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        width: 22px;
    }

    .weekday-bar-container {
        flex: 1;
        height: 16px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
        overflow: hidden;
    }

    .weekday-bar {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: 3px;
        min-width: 2px;
    }

    .weekday-value {
        font-size: 0.7rem;
        color: var(--text-secondary);
        font-weight: 600;
        width: 32px;
        text-align: right;
    }

    .hours-chart {
        display: flex;
        align-items: flex-end;
        height: 50px;
        gap: 1px;
    }

    .hour-bar {
        flex: 1;
        background: var(--thw-blue);
        border-radius: 2px 2px 0 0;
        min-height: 2px;
        opacity: 0.8;
    }

    .hours-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 0.4rem;
        font-size: 0.6rem;
        color: var(--text-muted);
    }

    /* Info Box */
    .info-box {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 1rem;
    }

    .info-box i {
        color: var(--thw-blue);
        font-size: 1rem;
        margin-top: 0.1rem;
    }

    .info-box p {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.5;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 1.5rem;
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .chart-container { height: 180px; }
        .section-bar { width: 50px; }
        .section-name { font-size: 0.75rem; }
        .lehrgang-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Plattform-<span>Statistiken</span></h1>
        <p class="page-subtitle">Anonyme Daten aller Nutzer</p>
    </header>

    <!-- Stats Row -->
    <div class="stats-row" style="margin-bottom: 2rem;">
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: var(--thw-blue);"><i class="bi bi-chat-dots-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($totalAnswered) }}</div>
                <div class="stat-pill-label">Beantwortet</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-check-circle-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ $successRate }}%</div>
                <div class="stat-pill-label">Richtig</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-error"><i class="bi bi-x-circle-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ $errorRate }}%</div>
                <div class="stat-pill-label">Falsch</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-clipboard-check-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($totalExams) }}</div>
                <div class="stat-pill-label">Prüfungen</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="glass-tl" style="padding: 1.25rem;">
            <h3 class="card-title"><i class="bi bi-graph-up"></i> Fragen (30 Tage)</h3>
            <div class="chart-container">
                <canvas id="questionsChart"></canvas>
            </div>
        </div>
        <div class="glass-br" style="padding: 1.25rem;">
            <h3 class="card-title"><i class="bi bi-bar-chart"></i> Prüfungen (30 Tage)</h3>
            <div class="chart-container">
                <canvas id="examsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Aktivität -->
    <div class="section-header">
        <h2 class="section-title">Wann wird gelernt?</h2>
    </div>
    <div class="glass bento-grid" style="padding: 1.25rem; display: block; margin-bottom: 2rem;">
        <div class="activity-grid">
            <div class="activity-section">
                <h4>Nach Wochentag</h4>
                @php
                    $weekdays = ['', 'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
                    $maxW = max($activityByWeekday ?: [1]);
                @endphp
                @foreach([2,3,4,5,6,7,1] as $day)
                    <div class="weekday-row">
                        <span class="weekday-label">{{ $weekdays[$day] }}</span>
                        <div class="weekday-bar-container">
                            <div class="weekday-bar" style="width: {{ $maxW > 0 ? (($activityByWeekday[$day] ?? 0) / $maxW * 100) : 0 }}%"></div>
                        </div>
                        <span class="weekday-value">{{ number_format($activityByWeekday[$day] ?? 0) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="activity-section">
                <h4>Nach Uhrzeit</h4>
                @php $maxH = max($peakHours ?: [1]); @endphp
                <div class="hours-chart">
                    @for($h = 0; $h < 24; $h++)
                        <div class="hour-bar" style="height: {{ $maxH > 0 ? (($peakHours[$h] ?? 0) / $maxH * 100) : 0 }}%" title="{{ $h }}:00"></div>
                    @endfor
                </div>
                <div class="hours-labels">
                    <span>0h</span><span>6h</span><span>12h</span><span>18h</span><span>24h</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lernabschnitte -->
    @if($sectionStats->isNotEmpty())
    <div class="section-header">
        <h2 class="section-title">Erfolgsrate nach Lernabschnitt</h2>
    </div>
    <div class="glass-slash" style="padding: 1.25rem; margin-bottom: 2rem;">
        <div class="sections-list">
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
                <div class="section-row">
                    <span class="section-number">{{ $stat->lernabschnitt }}.</span>
                    <div class="section-info">
                        <div class="section-name">{{ $sectionNames[$stat->lernabschnitt] ?? 'Unbekannt' }}</div>
                        <div class="section-stats">{{ number_format($stat->total_attempts) }} Versuche</div>
                    </div>
                    <div class="section-bar">
                        <div class="section-bar-fill" style="width: {{ $stat->success_rate }}%"></div>
                    </div>
                    <span class="section-rate">{{ $stat->success_rate }}%</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Fragen -->
    <div class="section-header">
        <h2 class="section-title">Top Fragen</h2>
    </div>
    <div class="bento-grid" style="margin-bottom: 2rem;">
        <div class="glass-error bento-half" style="padding: 1.25rem;">
            <h3 class="card-title"><i class="bi bi-exclamation-triangle" style="color: #ef4444;"></i> Schwierigste Fragen</h3>
            @if($topWrongQuestionsWithDetails->isEmpty())
                <div class="empty-state">Noch nicht genug Daten (min. 5 Versuche)</div>
            @else
                <div class="question-list">
                    @foreach($topWrongQuestionsWithDetails->take(6) as $i => $item)
                        <div class="question-item wrong">
                            <span class="question-rank">{{ $i + 1 }}.</span>
                            <div class="question-content">
                                <div class="question-text">{{ Str::limit($item['question']->frage, 70) }}</div>
                                <div class="question-meta">LA {{ $item['question']->lernabschnitt }} | {{ $item['total_attempts'] }} Versuche</div>
                            </div>
                            <span class="question-rate wrong">{{ $item['error_rate'] }}%</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="glass-success bento-half" style="padding: 1.25rem;">
            <h3 class="card-title"><i class="bi bi-check2-circle" style="color: #22c55e;"></i> Einfachste Fragen</h3>
            @if($topCorrectQuestionsWithDetails->isEmpty())
                <div class="empty-state">Noch nicht genug Daten (min. 5 Versuche)</div>
            @else
                <div class="question-list">
                    @foreach($topCorrectQuestionsWithDetails->take(6) as $i => $item)
                        <div class="question-item correct">
                            <span class="question-rank">{{ $i + 1 }}.</span>
                            <div class="question-content">
                                <div class="question-text">{{ Str::limit($item['question']->frage, 70) }}</div>
                                <div class="question-meta">LA {{ $item['question']->lernabschnitt }} | {{ $item['total_attempts'] }} Versuche</div>
                            </div>
                            <span class="question-rate correct">{{ $item['success_rate'] }}%</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Lehrgänge -->
    @if($lehrgangStats->isNotEmpty())
    <div class="section-header">
        <h2 class="section-title">Lehrgänge</h2>
    </div>
    <div class="glass" style="padding: 1.25rem; margin-bottom: 2rem;">
        <div class="lehrgang-grid">
            @foreach($lehrgangStats as $lg)
                <div class="lehrgang-item">
                    <div class="lehrgang-name">{{ $lg->name }}</div>
                    <div class="lehrgang-stats">
                        <span>{{ $lg->users_count }} Nutzer</span>
                        <span>{{ $lg->questions_count }} Fragen</span>
                    </div>
                    <div class="lehrgang-bar">
                        <div class="lehrgang-bar-fill" style="width: {{ $lg->success_rate }}%; background: {{ $lg->success_rate >= 70 ? '#22c55e' : ($lg->success_rate >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                    </div>
                    <div class="lehrgang-success" style="color: {{ $lg->success_rate >= 70 ? '#22c55e' : ($lg->success_rate >= 50 ? '#f59e0b' : '#ef4444') }};">{{ $lg->success_rate }}% Erfolg</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Info -->
    <div class="glass-thw info-box">
        <i class="bi bi-shield-check"></i>
        <p>Diese Statistiken sind vollständig anonym. Es werden keine persönlichen Daten gespeichert - nur ob Fragen richtig oder falsch beantwortet wurden.</p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    // Dark mode styling
    const isDark = !document.documentElement.classList.contains('light-mode');
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.08)';
    const textColor = isDark ? '#a1a1aa' : '#6b7280';

    Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
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
                    font: { size: 10 },
                    color: textColor
                }
            },
            tooltip: {
                backgroundColor: isDark ? '#1a1a1d' : '#1f2937',
                titleColor: '#f5f5f5',
                bodyColor: '#f5f5f5',
                padding: 10,
                cornerRadius: 8,
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: gridColor },
                ticks: { font: { size: 9 }, color: textColor }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 8 }, maxRotation: 45, color: textColor }
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
                    borderColor: '#fbbf24',
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
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
                    backgroundColor: 'rgba(0, 51, 127, 0.5)',
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
