@extends('layouts.app')

@section('title', 'Statistiken - THW Trainer')
@section('description', 'Öffentliche Statistiken über alle beantworteten Fragen im THW-Trainer.')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .statistics-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        overflow-x: hidden;
    }

    .statistics-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        overflow-x: hidden;
    }

    .statistics-header {
        text-align: center;
        margin-bottom: 2rem;
        padding-top: 1rem;
    }

    .statistics-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .statistics-header h1 span {
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .statistics-header p {
        font-size: 1rem;
        color: #6b7280;
    }

    /* Stats Grid - wie Dashboard */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.25rem;
        text-align: center;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .stat-icon { font-size: 1.75rem; margin-bottom: 0.5rem; }
    .stat-value { font-size: 1.5rem; font-weight: 800; color: #00337F; margin-bottom: 0.25rem; }
    .stat-label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-sub { font-size: 0.8rem; color: #9ca3af; margin-top: 0.25rem; }

    /* Section Cards */
    .section-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        min-width: 0;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i { color: #00337F; }

    /* Charts */
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        min-width: 0;
    }

    .chart-container {
        height: 250px;
        position: relative;
        width: 100%;
        min-width: 0;
    }

    /* Lernabschnitte */
    .sections-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .section-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 0.5rem;
    }

    .section-row:hover {
        background: #f3f4f6;
    }

    .section-number {
        font-weight: 700;
        color: #00337F;
        min-width: 28px;
    }

    .section-info {
        flex: 1;
        min-width: 0;
    }

    .section-name {
        font-size: 0.85rem;
        color: #374151;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .section-stats {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .section-bar {
        width: 120px;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .section-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        border-radius: 3px;
    }

    .section-rate {
        font-size: 0.85rem;
        font-weight: 700;
        color: #22c55e;
        min-width: 45px;
        text-align: right;
    }

    /* Fragen Listen */
    .questions-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

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
        background: #f9fafb;
        border-radius: 0.5rem;
        border-left: 3px solid transparent;
    }

    .question-item.wrong { border-left-color: #ef4444; }
    .question-item.correct { border-left-color: #22c55e; }

    .question-rank {
        font-weight: 700;
        color: #9ca3af;
        font-size: 0.85rem;
        min-width: 20px;
    }

    .question-content {
        flex: 1;
        min-width: 0;
    }

    .question-text {
        font-size: 0.85rem;
        color: #374151;
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }

    .question-meta {
        font-size: 0.7rem;
        color: #9ca3af;
    }

    .question-rate {
        font-weight: 700;
        font-size: 0.9rem;
    }

    .question-rate.wrong { color: #ef4444; }
    .question-rate.correct { color: #22c55e; }

    /* Lehrgänge */
    .lehrgang-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1rem;
    }

    .lehrgang-item {
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
    }

    .lehrgang-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }

    .lehrgang-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .lehrgang-bar {
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
    }

    .lehrgang-bar-fill {
        height: 100%;
        border-radius: 2px;
    }

    /* Aktivität */
    .activity-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .activity-section h4 {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .weekday-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .weekday-label {
        font-size: 0.75rem;
        color: #6b7280;
        width: 24px;
    }

    .weekday-bar-container {
        flex: 1;
        height: 20px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .weekday-bar {
        height: 100%;
        background: linear-gradient(90deg, #00337F, #0066CC);
        border-radius: 3px;
        min-width: 2px;
    }

    .weekday-value {
        font-size: 0.75rem;
        color: #374151;
        font-weight: 600;
        width: 35px;
        text-align: right;
    }

    .hours-chart {
        display: flex;
        align-items: flex-end;
        height: 60px;
        gap: 1px;
    }

    .hour-bar {
        flex: 1;
        background: #f59e0b;
        border-radius: 2px 2px 0 0;
        min-height: 2px;
    }

    .hours-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
        font-size: 0.65rem;
        color: #9ca3af;
    }

    /* Info */
    .info-box {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        margin-top: 1.5rem;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .info-box i {
        color: #0284c7;
        font-size: 1rem;
        margin-top: 0.125rem;
    }

    .info-box p {
        font-size: 0.85rem;
        color: #475569;
        margin: 0;
        line-height: 1.5;
    }

    /* Empty */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-row { grid-template-columns: 1fr; }
        .questions-row { grid-template-columns: 1fr; }
        .activity-grid { grid-template-columns: 1fr; gap: 1.5rem; }
    }

    @media (max-width: 640px) {
        .statistics-container { padding: 1rem; }
        .statistics-header h1 { font-size: 1.75rem; }
        .section-card { padding: 1rem; }
        .chart-container { height: 200px; }
        .section-bar { width: 60px; }
        .section-name { font-size: 0.75rem; }
        .section-row { gap: 0.5rem; padding: 0.5rem; }
        .section-number { min-width: 22px; font-size: 0.85rem; }
        .section-rate { min-width: 38px; font-size: 0.8rem; }
        .lehrgang-grid { grid-template-columns: 1fr; }
        .question-text { font-size: 0.8rem; }
        .weekday-value { width: 30px; font-size: 0.7rem; }
        .hours-chart { height: 50px; }
    }

    @media (max-width: 400px) {
        .stats-grid { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .stat-card { padding: 1rem; }
        .stat-value { font-size: 1.25rem; }
        .stat-icon { font-size: 1.5rem; }
        .section-bar { width: 50px; }
        .section-info { max-width: calc(100% - 130px); }
    }
</style>
@endpush

@section('content')
<div class="statistics-wrapper">
    <div class="statistics-container">

        <div class="statistics-header">
            <h1>Plattform-<span>Statistiken</span></h1>
            <p>Anonyme Daten aller Nutzer</p>
        </div>

        <!-- Haupt-Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-chat-dots text-blue-600"></i></div>
                <div class="stat-value">{{ number_format($totalAnswered) }}</div>
                <div class="stat-label">Fragen beantwortet</div>
                <div class="stat-sub">{{ number_format($totalAnsweredToday) }} heute</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-check-circle text-green-500"></i></div>
                <div class="stat-value">{{ $successRate }}%</div>
                <div class="stat-label">Richtig</div>
                <div class="stat-sub">{{ number_format($totalCorrect) }} gesamt</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-x-circle text-red-500"></i></div>
                <div class="stat-value">{{ $errorRate }}%</div>
                <div class="stat-label">Falsch</div>
                <div class="stat-sub">{{ number_format($totalWrong) }} gesamt</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clipboard-check text-yellow-500"></i></div>
                <div class="stat-value">{{ number_format($totalExams) }}</div>
                <div class="stat-label">Prüfungen</div>
                <div class="stat-sub">{{ $examPassRate }}% bestanden</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-row">
            <div class="section-card">
                <h3 class="section-title"><i class="bi bi-graph-up"></i> Fragen (30 Tage)</h3>
                <div class="chart-container">
                    <canvas id="questionsChart"></canvas>
                </div>
            </div>
            <div class="section-card">
                <h3 class="section-title"><i class="bi bi-bar-chart"></i> Prüfungen (30 Tage)</h3>
                <div class="chart-container">
                    <canvas id="examsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Aktivität -->
        <div class="section-card">
            <h3 class="section-title"><i class="bi bi-clock-history"></i> Wann wird gelernt? <span style="font-weight: 400; font-size: 0.8rem; color: #9ca3af;">(letzte 30 Tage)</span></h3>
            <div class="activity-grid">
                <div class="activity-section">
                    <h4>Aktivität nach Wochentag</h4>
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
                    <h4>Aktivität nach Uhrzeit</h4>
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
        <div class="section-card">
            <h3 class="section-title"><i class="bi bi-journal-text"></i> Erfolgsrate nach Lernabschnitt</h3>
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
        <div class="questions-row">
            <div class="section-card">
                <h3 class="section-title"><i class="bi bi-exclamation-triangle text-red-500"></i> Schwierigste Fragen</h3>
                @if($topWrongQuestionsWithDetails->isEmpty())
                    <div class="empty-state">Noch nicht genug Daten (min. 5 Versuche)</div>
                @else
                    <div class="question-list">
                        @foreach($topWrongQuestionsWithDetails->take(8) as $i => $item)
                            <div class="question-item wrong">
                                <span class="question-rank">{{ $i + 1 }}.</span>
                                <div class="question-content">
                                    <div class="question-text">{{ Str::limit($item['question']->frage, 80) }}</div>
                                    <div class="question-meta">LA {{ $item['question']->lernabschnitt }} · {{ $item['total_attempts'] }} Versuche</div>
                                </div>
                                <span class="question-rate wrong">{{ $item['error_rate'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="section-card">
                <h3 class="section-title"><i class="bi bi-check2-circle text-green-500"></i> Einfachste Fragen</h3>
                @if($topCorrectQuestionsWithDetails->isEmpty())
                    <div class="empty-state">Noch nicht genug Daten (min. 5 Versuche)</div>
                @else
                    <div class="question-list">
                        @foreach($topCorrectQuestionsWithDetails->take(8) as $i => $item)
                            <div class="question-item correct">
                                <span class="question-rank">{{ $i + 1 }}.</span>
                                <div class="question-content">
                                    <div class="question-text">{{ Str::limit($item['question']->frage, 80) }}</div>
                                    <div class="question-meta">LA {{ $item['question']->lernabschnitt }} · {{ $item['total_attempts'] }} Versuche</div>
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
        <div class="section-card">
            <h3 class="section-title"><i class="bi bi-mortarboard"></i> Lehrgänge</h3>
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
                        <div style="font-size: 0.75rem; color: {{ $lg->success_rate >= 70 ? '#16a34a' : ($lg->success_rate >= 50 ? '#d97706' : '#dc2626') }}; font-weight: 600; margin-top: 0.25rem;">{{ $lg->success_rate }}% Erfolg</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="info-box">
            <i class="bi bi-shield-check"></i>
            <p>Diese Statistiken sind vollständig anonym. Es werden keine persönlichen Daten gespeichert - nur ob Fragen richtig oder falsch beantwortet wurden.</p>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = '#6b7280';

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 12, font: { size: 11 } } },
            tooltip: { backgroundColor: '#1f2937', padding: 10, cornerRadius: 6 }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
            x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 45 } }
        }
    };

    new Chart(document.getElementById('questionsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                { label: 'Gesamt', data: {!! json_encode($chartData['questionsTotal']) !!}, borderColor: '#00337F', backgroundColor: 'rgba(0,51,127,0.1)', borderWidth: 2, fill: true, tension: 0.3, pointRadius: 1 },
                { label: 'Richtig', data: {!! json_encode($chartData['questionsCorrect']) !!}, borderColor: '#22c55e', borderWidth: 2, tension: 0.3, pointRadius: 1 },
                { label: 'Falsch', data: {!! json_encode($chartData['questionsWrong']) !!}, borderColor: '#ef4444', borderWidth: 2, tension: 0.3, pointRadius: 1 }
            ]
        },
        options: baseOptions
    });

    new Chart(document.getElementById('examsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                { label: 'Bestanden', data: {!! json_encode($chartData['examsPassed']) !!}, backgroundColor: '#22c55e', borderRadius: 3 },
                { label: 'Gesamt', data: {!! json_encode($chartData['examsTotal']) !!}, backgroundColor: 'rgba(0,51,127,0.3)', borderRadius: 3 }
            ]
        },
        options: { ...baseOptions, plugins: { ...baseOptions.plugins, legend: { ...baseOptions.plugins.legend, reverse: true } } }
    });
});
</script>
@endpush
