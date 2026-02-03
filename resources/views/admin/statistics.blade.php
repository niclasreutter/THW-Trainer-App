@extends('layouts.app')

@section('title', 'Statistiken - Admin')
@section('description', 'Detaillierte Statistiken und Analysen')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    @media (max-width: 600px) {
        .chart-container {
            height: 250px;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Statistiken <span>& Analysen</span></h1>
        <p class="page-subtitle">30-Tage Übersicht mit interaktiven Charts</p>
    </header>

    <!-- Stats Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-people"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($metrics['total_users']) }}</div>
                <div class="stat-pill-label">Gesamt Benutzer</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-check-circle"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($metrics['verified_users']) }}</div>
                <div class="stat-pill-label">Verifizierte</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: var(--thw-blue-light);"><i class="bi bi-activity"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($metrics['active_users_30d']) }}</div>
                <div class="stat-pill-label">Aktiv (30 Tage)</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-warning"><i class="bi bi-question-circle"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($metrics['total_questions']) }}</div>
                <div class="stat-pill-label">Gesamt Fragen</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-dark-secondary"><i class="bi bi-chat-left-text"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($metrics['total_answers']) }}</div>
                <div class="stat-pill-label">Gesamt Antworten</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: #f59e0b;"><i class="bi bi-fire"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($metrics['users_with_streak']) }}</div>
                <div class="stat-pill-label">Aktive Streaks</div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="bento-grid">
        <!-- Aktivität Chart -->
        <div class="glass-blue bento-half">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-people"></i>
                Aktive Benutzer (30 Tage)
            </h3>
            <div class="chart-container">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Registrierungen Chart -->
        <div class="glass-green bento-half">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-person-plus"></i>
                Neue Registrierungen (30 Tage)
            </h3>
            <div class="chart-container">
                <canvas id="registrationsChart"></canvas>
            </div>
        </div>

        <!-- Fragen Chart -->
        <div class="glass-gold bento-half">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-question-circle"></i>
                Beantwortete Fragen (30 Tage)
            </h3>
            <div class="chart-container">
                <canvas id="questionsChart"></canvas>
            </div>
        </div>

        <!-- Erfolgsquote Chart -->
        <div class="glass-purple bento-half">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-trophy"></i>
                Erfolgsquote % (30 Tage)
            </h3>
            <div class="chart-container">
                <canvas id="successRateChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Zurück Button -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">
            <i class="bi bi-arrow-left"></i> Zurück zum Dashboard
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    // Chart.js Globale Konfiguration für Dark Mode
    Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
    Chart.defaults.color = '#a1a1aa'; // --text-secondary

    // Gemeinsame Chart-Optionen für Dark Mode
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                padding: 12,
                borderRadius: 8,
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                titleFont: { size: 14, weight: 'bold' },
                titleColor: '#f5f5f5',
                bodyFont: { size: 13 },
                bodyColor: '#f5f5f5'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.06)',
                    drawBorder: false
                },
                ticks: {
                    font: { size: 11 },
                    color: '#a1a1aa'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: { size: 10 },
                    color: '#a1a1aa',
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    };

    // Aktivität Chart (Blau)
    new Chart(document.getElementById('activityChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($last30DaysActivity['labels']) !!},
            datasets: [{
                label: 'Aktive Benutzer',
                data: {!! json_encode($last30DaysActivity['values']) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#3b82f6'
            }]
        },
        options: commonOptions
    });

    // Registrierungen Chart (Grün)
    new Chart(document.getElementById('registrationsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($last30DaysRegistrations['labels']) !!},
            datasets: [{
                label: 'Neue Registrierungen',
                data: {!! json_encode($last30DaysRegistrations['values']) !!},
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#22c55e'
            }]
        },
        options: commonOptions
    });

    // Fragen Chart (Gold)
    new Chart(document.getElementById('questionsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($last30DaysQuestions['labels']) !!},
            datasets: [{
                label: 'Beantwortete Fragen',
                data: {!! json_encode($last30DaysQuestions['values']) !!},
                borderColor: '#fbbf24',
                backgroundColor: 'rgba(251, 191, 36, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#fbbf24',
                pointBorderColor: '#fbbf24'
            }]
        },
        options: commonOptions
    });

    // Erfolgsquote Chart (Purple)
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
                backgroundColor: 'rgba(139, 92, 246, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#8b5cf6',
                pointBorderColor: '#8b5cf6'
            }]
        },
        options: successOptions
    });
</script>
@endpush
