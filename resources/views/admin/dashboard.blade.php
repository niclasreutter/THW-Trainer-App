@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('description', 'Übersicht über System-Status, Benutzerstatistiken und Lernfortschritt')

@push('styles')
<style>
    /* KPI Cards */
    .kpi-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
    }

    .kpi-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .kpi-sub {
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    /* Stat rows */
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.625rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .stat-row:last-child { border-bottom: none; }

    .stat-row-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .stat-row-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* Card header */
    .card-header {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Activity Feed */
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        transition: all 0.2s;
    }

    .activity-item:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .activity-item.clickable {
        cursor: pointer;
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .activity-icon.success { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .activity-icon.error { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .activity-icon.warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .activity-icon.info { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .activity-icon.gold { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.125rem;
    }

    .activity-desc {
        font-size: 0.75rem;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .activity-time {
        font-size: 0.65rem;
        color: var(--text-muted);
        white-space: nowrap;
    }

    /* Quick Actions */
    .quick-action {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.2s;
    }

    .quick-action:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.1);
        transform: translateX(4px);
    }

    .quick-action-icon {
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .quick-action-text {
        flex: 1;
    }

    .quick-action-title {
        font-size: 0.85rem;
        font-weight: 600;
    }

    .quick-action-desc {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    /* Leaderboard */
    .leaderboard-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0.75rem;
        border-radius: 0.5rem;
        margin-bottom: 0.375rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        transition: all 0.2s;
    }

    .leaderboard-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .leaderboard-item.top-rank {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.08) 0%, rgba(245, 158, 11, 0.04) 100%);
        border: 1px solid rgba(251, 191, 36, 0.15);
    }

    .leaderboard-rank {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        font-weight: 700;
        font-size: 0.75rem;
    }

    .leaderboard-info {
        flex: 1;
        min-width: 0;
    }

    .leaderboard-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.8rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .leaderboard-level {
        font-size: 0.65rem;
        color: var(--text-muted);
    }

    .leaderboard-score {
        text-align: right;
    }

    .leaderboard-points {
        font-weight: 700;
        font-size: 0.8rem;
    }

    .leaderboard-details {
        font-size: 0.6rem;
        color: var(--text-muted);
    }

    /* Charts */
    .chart-container {
        height: 200px;
        position: relative;
    }

    /* Alert badge */
    .alert-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        background: #ef4444;
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
    }

    /* Responsive */
    @media (max-width: 600px) {
        .dashboard-container {
            padding: 1rem;
        }
        .kpi-value {
            font-size: 1.75rem;
        }
        .chart-container {
            height: 180px;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Admin <span>Dashboard</span></h1>
        <p class="page-subtitle">System-Status, Benutzer und Aktivitäten</p>
    </header>

    <!-- System Status + Alerts -->
    <div class="stats-row" style="margin-bottom: 1.5rem;">
        <div class="stat-pill">
            <span class="stat-pill-icon {{ $systemStatus['database']['status'] === 'ok' ? 'text-success' : 'text-error' }}">
                <i class="bi bi-database{{ $systemStatus['database']['status'] === 'ok' ? '-check' : '-x' }}"></i>
            </span>
            <div>
                <div class="stat-pill-value" style="font-size: 1rem;">{{ ucfirst($systemStatus['database']['status']) }}</div>
                <div class="stat-pill-label">Datenbank</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon {{ $systemStatus['cache']['status'] === 'ok' ? 'text-success' : 'text-error' }}">
                <i class="bi bi-lightning{{ $systemStatus['cache']['status'] === 'ok' ? '-charge' : '' }}"></i>
            </span>
            <div>
                <div class="stat-pill-value" style="font-size: 1rem;">{{ ucfirst($systemStatus['cache']['status']) }}</div>
                <div class="stat-pill-label">Cache</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-person-check"></i>
            </span>
            <div>
                <div class="stat-pill-value" style="font-size: 1rem;">{{ $systemStatus['online_users']['count'] }}</div>
                <div class="stat-pill-label">Online</div>
            </div>
        </div>

        @if($openIssues > 0)
        <a href="{{ route('admin.lehrgang-issues.index') }}" class="stat-pill" style="text-decoration: none; position: relative;">
            <span class="stat-pill-icon text-warning">
                <i class="bi bi-exclamation-triangle"></i>
            </span>
            <div>
                <div class="stat-pill-value" style="font-size: 1rem;">{{ $openIssues }}</div>
                <div class="stat-pill-label">Offen</div>
            </div>
            <span class="alert-badge">!</span>
        </a>
        @endif

        @if($unreadMessages > 0)
        <a href="{{ route('admin.contact-messages.index') }}" class="stat-pill" style="text-decoration: none; position: relative;">
            <span class="stat-pill-icon text-info">
                <i class="bi bi-envelope"></i>
            </span>
            <div>
                <div class="stat-pill-value" style="font-size: 1rem;">{{ $unreadMessages }}</div>
                <div class="stat-pill-label">Ungelesen</div>
            </div>
            <span class="alert-badge">!</span>
        </a>
        @endif
    </div>

    <!-- KPI Grid -->
    <div class="bento-grid" style="margin-bottom: 1.5rem;">
        <div class="glass-gold bento-third hover-lift">
            <div class="kpi-label">Gesamt Benutzer</div>
            <div class="kpi-value">{{ number_format($totalUsers) }}</div>
            <div class="kpi-sub text-success">+{{ $newUsersToday }} heute</div>
        </div>

        <div class="glass-tl bento-third hover-lift">
            <div class="kpi-label">E-Mail bestätigt</div>
            <div class="kpi-value">{{ number_format($verifiedUsers) }}</div>
            <div class="kpi-sub text-dark-secondary">{{ $verificationRate }}%</div>
        </div>

        <div class="glass-br bento-third hover-lift">
            <div class="kpi-label">Fragen</div>
            <div class="kpi-value">{{ number_format($totalQuestions) }}</div>
            <div class="kpi-sub text-dark-secondary">{{ $learningSections }} Abschnitte</div>
        </div>

        <div class="glass-slash bento-third hover-lift">
            <div class="kpi-label">Beantwortet</div>
            <div class="kpi-value">{{ number_format($totalAnsweredQuestions) }}</div>
            <div class="kpi-sub text-success">{{ number_format($totalCorrectAnswers) }} richtig</div>
        </div>
    </div>

    <!-- Main Content: Activity Feed + Quick Actions + Leaderboard -->
    <div class="bento-grid" style="margin-bottom: 1.5rem;">
        <!-- Activity Feed -->
        <div class="glass bento-half">
            <div class="card-header">
                <i class="bi bi-activity text-gold"></i>
                Aktivitäts-Feed (24h)
            </div>
            <div style="max-height: 380px; overflow-y: auto;">
                @forelse($activityFeed as $activity)
                    @if($activity['link'])
                    <a href="{{ $activity['link'] }}" class="activity-item clickable" style="text-decoration: none;">
                    @else
                    <div class="activity-item">
                    @endif
                        <div class="activity-icon {{ $activity['color'] }}">
                            <i class="bi bi-{{ $activity['icon'] }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">
                                {{ $activity['title'] }}
                                @if(!empty($activity['unread']))
                                    <span class="badge-error" style="font-size: 0.55rem; padding: 0.1rem 0.3rem; margin-left: 0.25rem;">NEU</span>
                                @endif
                                @if(!empty($activity['open']))
                                    <span class="badge-warning" style="font-size: 0.55rem; padding: 0.1rem 0.3rem; margin-left: 0.25rem;">OFFEN</span>
                                @endif
                            </div>
                            <div class="activity-desc">{{ $activity['description'] }}</div>
                        </div>
                        <div class="activity-time">{{ $activity['time']->diffForHumans(null, true, true) }}</div>
                    @if($activity['link'])
                    </a>
                    @else
                    </div>
                    @endif
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                        Keine Aktivitäten
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions + Stats -->
        <div class="glass-tl bento-half">
            <div class="card-header">
                <i class="bi bi-lightning-charge text-gold"></i>
                Quick Actions
            </div>

            <a href="{{ route('admin.users.index') }}" class="quick-action" style="margin-bottom: 0.5rem;">
                <div class="quick-action-icon" style="background: rgba(0, 51, 127, 0.15); color: var(--thw-blue);">
                    <i class="bi bi-people"></i>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Nutzer verwalten</div>
                    <div class="quick-action-desc">{{ number_format($totalUsers) }} Benutzer</div>
                </div>
                <i class="bi bi-chevron-right text-dark-muted"></i>
            </a>

            <a href="{{ route('admin.questions.index') }}" class="quick-action" style="margin-bottom: 0.5rem;">
                <div class="quick-action-icon" style="background: rgba(251, 191, 36, 0.15); color: #fbbf24;">
                    <i class="bi bi-question-circle"></i>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Fragen bearbeiten</div>
                    <div class="quick-action-desc">{{ number_format($totalQuestions) }} Fragen</div>
                </div>
                <i class="bi bi-chevron-right text-dark-muted"></i>
            </a>

            <a href="{{ route('admin.lehrgaenge.index') }}" class="quick-action" style="margin-bottom: 0.5rem;">
                <div class="quick-action-icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Lehrgänge</div>
                    <div class="quick-action-desc">Verwalten & erstellen</div>
                </div>
                <i class="bi bi-chevron-right text-dark-muted"></i>
            </a>

            <a href="{{ route('admin.newsletter.create') }}" class="quick-action" style="margin-bottom: 0.5rem;">
                <div class="quick-action-icon" style="background: rgba(34, 197, 94, 0.15); color: #22c55e;">
                    <i class="bi bi-megaphone"></i>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Newsletter senden</div>
                    <div class="quick-action-desc">An alle Benutzer</div>
                </div>
                <i class="bi bi-chevron-right text-dark-muted"></i>
            </a>

            <!-- Mini Stats -->
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                <div class="stat-row">
                    <span class="stat-row-label">Aktiv heute</span>
                    <span class="stat-row-value">{{ $userActivity['today'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Aktiv diese Woche</span>
                    <span class="stat-row-value">{{ $userActivity['this_week'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Erfolgsrate</span>
                    <span class="stat-row-value text-success">{{ $totalAnsweredQuestions > 0 ? round((($totalCorrectAnswers / $totalAnsweredQuestions) * 100), 1) : 0 }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row: Details + Leaderboard -->
    <div class="bento-grid" style="margin-bottom: 1.5rem;">
        <!-- Fragen-Statistik -->
        <div class="glass-br bento-third">
            <div class="card-header">
                <i class="bi bi-bar-chart text-gold"></i>
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
            <div style="margin-top: 0.75rem; padding: 0.625rem; background: rgba(255, 255, 255, 0.04); border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; font-size: 0.8rem; color: var(--text-primary);">Fehlerrate</span>
                <span class="text-warning" style="font-size: 1.1rem; font-weight: 800;">{{ $wrongAnswerRate }}%</span>
            </div>
        </div>

        <!-- Lernfortschritt -->
        <div class="glass-slash bento-third">
            <div class="card-header">
                <i class="bi bi-graph-up text-gold"></i>
                Lernfortschritt
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Ø Fortschritt</span>
                <span class="stat-row-value" style="color: var(--thw-blue);">{{ $learningProgress['average_progress'] }}%</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Mit Erfolgen</span>
                <span class="stat-row-value">{{ $learningProgress['users_with_achievements'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Aktiv (Monat)</span>
                <span class="stat-row-value">{{ $userActivity['this_month'] }}</span>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="glass bento-half">
            <div class="card-header">
                <i class="bi bi-trophy text-gold"></i>
                Top 10 Leaderboard
            </div>
            <div style="max-height: 240px; overflow-y: auto;">
                @forelse($leaderboard as $index => $user)
                    @php
                        $position = $index + 1;
                        $isTopRank = $position <= 3;
                        $medalColors = [1 => '#fbbf24', 2 => '#9ca3af', 3 => '#d97706'];
                    @endphp
                    <div class="leaderboard-item {{ $isTopRank ? 'top-rank' : '' }}">
                        <div class="leaderboard-rank" style="background: {{ $isTopRank ? 'rgba(251, 191, 36, 0.15)' : 'rgba(255, 255, 255, 0.05)' }}; color: {{ $medalColors[$position] ?? 'var(--text-muted)' }};">
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
                    <div style="text-align: center; padding: 1.5rem; color: var(--text-muted);">
                        <i class="bi bi-people" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                        Keine Daten
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="section-header" style="margin-bottom: 1rem;">
        <h2 class="section-title">Statistiken (30 Tage)</h2>
    </div>

    <div class="bento-grid">
        <div class="glass bento-half">
            <div class="card-header">
                <i class="bi bi-people text-gold"></i>
                Benutzeraktivität
            </div>
            <div class="chart-container">
                <canvas id="userActivityChart"></canvas>
            </div>
        </div>

        <div class="glass bento-half">
            <div class="card-header">
                <i class="bi bi-question-circle text-gold"></i>
                Beantwortete Fragen
            </div>
            <div class="chart-container">
                <canvas id="questionsChart"></canvas>
            </div>
        </div>

        <div class="glass bento-wide">
            <div class="card-header">
                <i class="bi bi-graph-up-arrow text-gold"></i>
                Benutzer-Wachstum
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

    const isLightMode = document.documentElement.classList.contains('light-mode');
    const gridColor = isLightMode ? 'rgba(0, 0, 0, 0.06)' : 'rgba(255, 255, 255, 0.06)';
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
                labels: { usePointStyle: true, padding: 10, font: { size: 9 } }
            },
            tooltip: {
                backgroundColor: isLightMode ? 'rgba(255, 255, 255, 0.95)' : 'rgba(0, 0, 0, 0.9)',
                titleColor: isLightMode ? '#1f2937' : '#f5f5f5',
                bodyColor: isLightMode ? '#374151' : '#a1a1aa',
                borderColor: isLightMode ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                padding: 8,
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
                    label: 'Gesamtanzahl',
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
