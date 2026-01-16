@extends('layouts.app')

@section('title', 'Statistiken - Admin')
@section('description', 'Detaillierte Statistiken und Analysen')

@push('styles')
<style>
    .stats-wrapper { min-height: 100vh; background: #f3f4f6; padding: 2rem 0; }
    .stats-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; }

    .stats-header { text-align: center; margin-bottom: 3rem; }
    .stats-header h1 { font-size: 2.5rem; font-weight: 800; color: #00337F; margin-bottom: 0.5rem; }
    .stats-subtitle { font-size: 1.1rem; color: #6b7280; }

    .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
    .metric-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem; text-align: center; transition: transform 0.2s; }
    .metric-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); }
    .metric-label { font-size: 0.85rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
    .metric-value { font-size: 2.5rem; font-weight: 800; color: #00337F; }

    .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 2rem; }
    .chart-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 2rem; }
    .chart-title { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .chart-icon { font-size: 1.3rem; color: #00337F; }
    .chart-container { position: relative; height: 300px; }

    @media (max-width: 768px) {
        .charts-grid { grid-template-columns: 1fr; }
        .chart-container { height: 250px; }
    }
</style>
@endpush

@section('content')
<div class="stats-wrapper">
    <div class="stats-container">
        <!-- Header -->
        <div class="stats-header">
            <h1>📊 Statistiken & Analysen</h1>
            <p class="stats-subtitle">30-Tage Übersicht mit interaktiven Charts</p>
        </div>

        <!-- Metriken -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Gesamt Benutzer</div>
                <div class="metric-value">{{ number_format($metrics['total_users']) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Verifizierte</div>
                <div class="metric-value">{{ number_format($metrics['verified_users']) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Aktiv (30 Tage)</div>
                <div class="metric-value">{{ number_format($metrics['active_users_30d']) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Gesamt Fragen</div>
                <div class="metric-value">{{ number_format($metrics['total_questions']) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Gesamt Antworten</div>
                <div class="metric-value">{{ number_format($metrics['total_answers']) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Aktive Streaks</div>
                <div class="metric-value">{{ number_format($metrics['users_with_streak']) }}</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <!-- Aktivität Chart -->
            <div class="chart-card">
                <div class="chart-title">
                    <span class="chart-icon">👥</span>
                    Aktive Benutzer (30 Tage)
                </div>
                <div class="chart-container">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            <!-- Registrierungen Chart -->
            <div class="chart-card">
                <div class="chart-title">
                    <span class="chart-icon">🆕</span>
                    Neue Registrierungen (30 Tage)
                </div>
                <div class="chart-container">
                    <canvas id="registrationsChart"></canvas>
                </div>
            </div>

            <!-- Fragen Chart -->
            <div class="chart-card">
                <div class="chart-title">
                    <span class="chart-icon">❓</span>
                    Beantwortete Fragen (30 Tage)
                </div>
                <div class="chart-container">
                    <canvas id="questionsChart"></canvas>
                </div>
            </div>

            <!-- Erfolgsquote Chart -->
            <div class="chart-card">
                <div class="chart-title">
                    <span class="chart-icon">✅</span>
                    Erfolgsquote % (30 Tage)
                </div>
                <div class="chart-container">
                    <canvas id="successRateChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Zurück Button -->
        <div style="text-align: center; margin-top: 3rem;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    // Chart.js Globale Konfiguration
    Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
    Chart.defaults.color = '#6b7280';

    // Gemeinsame Chart-Optionen
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f3f4f6'
                },
                ticks: {
                    font: { size: 11 }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: { size: 10 },
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    };

    // Aktivität Chart
    new Chart(document.getElementById('activityChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($last30DaysActivity['labels']) !!},
            datasets: [{
                label: 'Aktive Benutzer',
                data: {!! json_encode($last30DaysActivity['values']) !!},
                borderColor: '#0066CC',
                backgroundColor: 'rgba(0, 102, 204, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#0066CC'
            }]
        },
        options: commonOptions
    });

    // Registrierungen Chart
    new Chart(document.getElementById('registrationsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($last30DaysRegistrations['labels']) !!},
            datasets: [{
                label: 'Neue Registrierungen',
                data: {!! json_encode($last30DaysRegistrations['values']) !!},
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#16a34a'
            }]
        },
        options: commonOptions
    });

    // Fragen Chart
    new Chart(document.getElementById('questionsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($last30DaysQuestions['labels']) !!},
            datasets: [{
                label: 'Beantwortete Fragen',
                data: {!! json_encode($last30DaysQuestions['values']) !!},
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#f59e0b'
            }]
        },
        options: commonOptions
    });

    // Erfolgsquote Chart
    const successOptions = { ...commonOptions };
    successOptions.scales.y.max = 100;
    successOptions.scales.y.ticks = {
        ...successOptions.scales.y.ticks,
        callback: function(value) {
            return value + '%';
        }
    };

    new Chart(document.getElementById('successRateChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($last30DaysSuccessRate['labels']) !!},
            datasets: [{
                label: 'Erfolgsquote',
                data: {!! json_encode($last30DaysSuccessRate['values']) !!},
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#8b5cf6'
            }]
        },
        options: successOptions
    });
</script>
@endpush
