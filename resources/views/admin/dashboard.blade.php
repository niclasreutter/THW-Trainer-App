@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('description', 'Übersicht über System-Status, Benutzerstatistiken und Lernfortschritt')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2.5rem;
        padding-top: 1rem;
        max-width: 600px;
    }

    /* Bento Grid Layout */
    .bento-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .bento-side {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
    }

    .bento-wide {
        grid-column: span 4;
        padding: 1.5rem;
    }

    .bento-half {
        grid-column: span 2;
        padding: 1.5rem;
    }

    .bento-third {
        grid-column: span 1;
        padding: 1.5rem;
    }

    @media (max-width: 900px) {
        .bento-grid { grid-template-columns: 1fr 1fr; }
        .bento-wide { grid-column: span 2; }
        .bento-half { grid-column: span 2; }
        .bento-third { grid-column: span 1; }
    }

    @media (max-width: 600px) {
        .bento-grid { grid-template-columns: 1fr; }
        .bento-wide, .bento-half, .bento-third, .bento-side { grid-column: span 1; }
        .dashboard-container { padding: 1rem; }
    }

    /* Section headers */
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

    /* KPI Cards */
    .kpi-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
    }

    .kpi-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .kpi-sub {
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    /* Stat rows */
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .stat-row:last-child { border-bottom: none; }

    .stat-row-label {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .stat-row-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* Leaderboard */
    .leaderboard-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        margin-bottom: 0.5rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        transition: all 0.2s;
    }

    .leaderboard-item:hover {
        background: rgba(255, 255, 255, 0.06);
        transform: translateX(4px);
    }

    .leaderboard-item.top-rank {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
        border: 1px solid rgba(251, 191, 36, 0.2);
    }

    .leaderboard-rank {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .leaderboard-info {
        flex: 1;
        min-width: 0;
    }

    .leaderboard-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .leaderboard-level {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .leaderboard-score {
        text-align: right;
    }

    .leaderboard-points {
        font-weight: 700;
        font-size: 0.9rem;
    }

    .leaderboard-details {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    /* Charts */
    .chart-container {
        height: 240px;
        position: relative;
    }

</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Admin <span>Dashboard</span></h1>
        <p class="page-subtitle">System-Status, Benutzer und Lernfortschritt</p>
    </header>

    <!-- System Status Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon {{ $systemStatus['database']['status'] === 'ok' ? 'text-success' : 'text-error' }}">
                <i class="bi bi-database{{ $systemStatus['database']['status'] === 'ok' ? '-check' : '-x' }}"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ ucfirst($systemStatus['database']['status']) }}</div>
                <div class="stat-pill-label">Datenbank</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon {{ $systemStatus['cache']['status'] === 'ok' ? 'text-success' : 'text-error' }}">
                <i class="bi bi-lightning{{ $systemStatus['cache']['status'] === 'ok' ? '-charge' : '' }}"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ ucfirst($systemStatus['cache']['status']) }}</div>
                <div class="stat-pill-label">Cache</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon {{ $systemStatus['storage']['status'] === 'ok' ? 'text-success' : 'text-error' }}">
                <i class="bi bi-hdd{{ $systemStatus['storage']['status'] === 'ok' ? '' : '-fill' }}"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ ucfirst($systemStatus['storage']['status']) }}</div>
                <div class="stat-pill-label">Storage</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-person-check"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $systemStatus['online_users']['count'] }}</div>
                <div class="stat-pill-label">Online</div>
            </div>
        </div>
    </div>

    <!-- KPI Bento Grid -->
    <div class="bento-grid">
        <!-- Benutzer KPI -->
        <div class="glass-gold bento-side hover-lift">
            <div class="kpi-label">Gesamt Benutzer</div>
            <div class="kpi-value">{{ number_format($totalUsers) }}</div>
            <div class="kpi-sub text-success">+{{ $newUsersToday }} heute</div>
        </div>

        <!-- E-Mail bestätigt -->
        <div class="glass-tl bento-side hover-lift">
            <div class="kpi-label">E-Mail bestätigt</div>
            <div class="kpi-value">{{ number_format($verifiedUsers) }}</div>
            <div class="kpi-sub text-dark-secondary">{{ $verificationRate }}% Rate</div>
        </div>

        <!-- Fragen KPI -->
        <div class="glass-br bento-side hover-lift">
            <div class="kpi-label">Gesamt Fragen</div>
            <div class="kpi-value">{{ number_format($totalQuestions) }}</div>
            <div class="kpi-sub text-dark-secondary">{{ $learningSections }} Lernabschnitte</div>
        </div>

        <!-- Beantwortete Fragen -->
        <div class="glass-slash bento-side hover-lift">
            <div class="kpi-label">Beantwortet</div>
            <div class="kpi-value">{{ number_format($totalAnsweredQuestions) }}</div>
            <div class="kpi-sub text-warning">{{ $wrongAnswerRate }}% Falsch</div>
        </div>
    </div>

    <!-- Detail Statistiken -->
    <div class="section-header">
        <h2 class="section-title">Detaillierte Statistiken</h2>
    </div>

    <div class="bento-grid" style="margin-bottom: 2rem;">
        <!-- Fragen-Statistik -->
        <div class="glass-tl bento-third">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-graph-down text-gold"></i>
                Fragen-Statistik
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Gesamt beantwortet</span>
                <span class="stat-row-value">{{ number_format($totalAnsweredQuestions) }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Richtig</span>
                <span class="stat-row-value text-success">{{ number_format($totalCorrectAnswers) }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Falsch</span>
                <span class="stat-row-value text-error">{{ number_format($totalWrongAnswers) }}</span>
            </div>
            <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(255, 255, 255, 0.05); border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: var(--text-primary);">Erfolgsrate</span>
                <span class="text-gradient-gold" style="font-size: 1.25rem; font-weight: 800;">{{ $totalAnsweredQuestions > 0 ? round((($totalCorrectAnswers / $totalAnsweredQuestions) * 100), 1) : 0 }}%</span>
            </div>
        </div>

        <!-- Benutzer-Aktivität -->
        <div class="glass-br bento-third">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-graph-up text-gold"></i>
                Benutzer-Aktivität
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Heute</span>
                <span class="stat-row-value">{{ $userActivity['today'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Diese Woche</span>
                <span class="stat-row-value">{{ $userActivity['this_week'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Diesen Monat</span>
                <span class="stat-row-value">{{ $userActivity['this_month'] }}</span>
            </div>
        </div>

        <!-- Lernfortschritt -->
        <div class="glass-slash bento-third">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-mortarboard text-gold"></i>
                Lernfortschritt
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Gesamt Punkte</span>
                <span class="stat-row-value text-warning">{{ number_format($learningProgress['total_points']) }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Mit Erfolgen</span>
                <span class="stat-row-value">{{ $learningProgress['users_with_achievements'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Ø Fortschritt</span>
                <span class="stat-row-value" style="color: var(--thw-blue);">{{ $learningProgress['average_progress'] }}%</span>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="glass bento-third">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-trophy text-gold"></i>
                Leaderboard Top-10
            </div>
            <div style="max-height: 280px; overflow-y: auto;">
                @forelse($leaderboard as $index => $user)
                    @php
                        $position = $index + 1;
                        $isTopRank = $position <= 3;
                        $medalColors = [1 => '#fbbf24', 2 => '#9ca3af', 3 => '#d97706'];
                    @endphp
                    <div class="leaderboard-item {{ $isTopRank ? 'top-rank' : '' }}">
                        <div class="leaderboard-rank" style="background: {{ $isTopRank ? 'rgba(251, 191, 36, 0.2)' : 'rgba(255, 255, 255, 0.05)' }}; color: {{ $medalColors[$position] ?? 'var(--text-muted)' }};">
                            @if($position <= 3)
                                <i class="bi bi-trophy-fill"></i>
                            @else
                                {{ $position }}
                            @endif
                        </div>
                        <div class="leaderboard-info">
                            <div class="leaderboard-name">{{ $user['name'] }}</div>
                            <div class="leaderboard-level">Level {{ $user['level'] }}</div>
                        </div>
                        <div class="leaderboard-score">
                            <div class="leaderboard-points {{ $isTopRank ? 'text-gradient-gold' : '' }}">{{ number_format($user['score']) }}</div>
                            <div class="leaderboard-details">{{ $user['solved_questions'] }} Fragen</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        <i class="bi bi-people" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                        Keine Daten
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="section-header">
        <h2 class="section-title">Statistiken (letzte 30 Tage)</h2>
    </div>

    <div class="bento-grid" style="margin-bottom: 2rem;">
        <div class="glass bento-third">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-people text-gold"></i>
                Benutzeraktivität
            </div>
            <div class="chart-container">
                <canvas id="userActivityChart"></canvas>
            </div>
        </div>

        <div class="glass bento-third">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-question-circle text-gold"></i>
                Beantwortete Fragen
            </div>
            <div class="chart-container">
                <canvas id="questionsChart"></canvas>
            </div>
        </div>

        <div class="glass bento-half">
            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-graph-up-arrow text-gold"></i>
                User-Wachstum
            </div>
            <div class="chart-container">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js konnte nicht geladen werden');
        return;
    }

    // Theme-abhängige Farben
    const isLightMode = document.documentElement.classList.contains('light-mode');
    const gridColor = isLightMode ? 'rgba(0, 0, 0, 0.08)' : 'rgba(255, 255, 255, 0.08)';
    const textColor = isLightMode ? '#374151' : '#a1a1aa';

    Chart.defaults.font.family = "'Figtree', -apple-system, BlinkMacSystemFont, sans-serif";
    Chart.defaults.color = textColor;

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: { usePointStyle: true, padding: 12, font: { size: 10 } }
            },
            tooltip: {
                backgroundColor: isLightMode ? 'rgba(255, 255, 255, 0.95)' : 'rgba(0, 0, 0, 0.9)',
                titleColor: isLightMode ? '#1f2937' : '#f5f5f5',
                bodyColor: isLightMode ? '#374151' : '#a1a1aa',
                borderColor: isLightMode ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 6,
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { font: { size: 9 } } },
            x: { grid: { display: false }, ticks: { font: { size: 8 }, maxRotation: 45, minRotation: 45 } }
        }
    };

    // Chart 1: Benutzeraktivität
    new Chart(document.getElementById('userActivityChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Aktive Benutzer',
                    data: {!! json_encode($chartData['active']) !!},
                    borderColor: '#fbbf24',
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 1,
                    pointHoverRadius: 4
                },
                {
                    label: 'Registrierungen',
                    data: {!! json_encode($chartData['registrations']) !!},
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 1,
                    pointHoverRadius: 4
                }
            ]
        },
        options: commonOptions
    });

    // Chart 2: Beantwortete Fragen
    new Chart(document.getElementById('questionsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Gesamt',
                    data: {!! json_encode($chartData['questionsTotal']) !!},
                    borderColor: '#6b7280',
                    backgroundColor: 'rgba(107, 114, 128, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 1,
                    pointHoverRadius: 4
                },
                {
                    label: 'Richtig',
                    data: {!! json_encode($chartData['questionsCorrect']) !!},
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 1,
                    pointHoverRadius: 4
                },
                {
                    label: 'Falsch',
                    data: {!! json_encode($chartData['questionsWrong']) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 1,
                    pointHoverRadius: 4
                }
            ]
        },
        options: commonOptions
    });

    // Chart 3: User-Wachstum
    new Chart(document.getElementById('userGrowthChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Gesamtanzahl User',
                    data: {!! json_encode($chartData['userCount']) !!},
                    borderColor: '#00337F',
                    backgroundColor: 'rgba(0, 51, 127, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 1,
                    pointHoverRadius: 4
                },
                {
                    label: 'Unbestätigt',
                    data: {!! json_encode($chartData['unverifiedCount']) !!},
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 1,
                    pointHoverRadius: 4
                }
            ]
        },
        options: commonOptions
    });
});
</script>
@endpush
