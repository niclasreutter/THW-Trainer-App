@extends('layouts.app')

@section('title', 'Admin Dashboard - THW Trainer')
@section('description', 'Übersicht über System-Status, Benutzerstatistiken und Lernfortschritt')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .admin-wrapper { min-height: 100vh; background: #f3f4f6; position: relative; overflow-x: hidden; }

    .admin-container { max-width: 1200px; margin: 0 auto; padding: 2rem; position: relative; z-index: 1; }

    .admin-header { text-align: center; margin-bottom: 3rem; }

    .admin-header h1 { font-size: 2.5rem; font-weight: 800; color: #00337F; margin-bottom: 0.5rem; line-height: 1.2; }

    .admin-subtitle { font-size: 1.1rem; color: #4b5563; margin: 0; }

    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }

    .stat-card { background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); transition: all 0.3s; }

    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }

    .stat-card.system-status { background: white; padding: 1rem; }

    .stat-label { font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 500; }

    .stat-value { font-size: 1.75rem; font-weight: 800; color: #1f2937; margin-bottom: 0.25rem; }

    .stat-subtext { font-size: 0.8rem; color: #9ca3af; }

    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }

    .kpi-card { background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); display: flex; align-items: center; justify-content: space-between; transition: all 0.3s; }

    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); }

    .kpi-content h3 { font-size: 0.9rem; color: #6b7280; margin: 0 0 0.5rem 0; font-weight: 500; }

    .kpi-value { font-size: 2.5rem; font-weight: 800; color: #00337F; margin: 0; }

    .kpi-sub { font-size: 0.8rem; color: #9ca3af; margin: 0.3rem 0 0 0; }

    .kpi-icon { width: 70px; height: 70px; background: linear-gradient(135deg, rgba(0, 51, 127, 0.1) 0%, rgba(0, 51, 127, 0.05) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem; }

    .card { background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }

    .card h3 { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem; }

    .card-icon { font-size: 1.3rem; color: #00337F; }

    .stat-row { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f3f4f6; }

    .stat-row:last-child { border-bottom: none; }

    .stat-label-col { color: #6b7280; font-size: 0.95rem; }

    .stat-value-col { font-weight: 700; color: #1f2937; font-size: 1rem; }

    .stat-value-col.success { color: #22c55e; }

    .stat-value-col.warning { color: #f59e0b; }

    .stat-value-col.primary { color: #00337F; }

    .leaderboard-item { display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem; background: #f9fafb; border: 1px solid #e5e7eb; transition: all 0.3s; }

    .leaderboard-item:hover { background: #ffffff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }

    .leaderboard-item.top-rank { background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%); border: 1px solid rgba(251, 191, 36, 0.3); }

    .medal { font-size: 1.5rem; margin-right: 1rem; }

    .user-info { flex: 1; }

    .user-name { font-weight: 700; color: #1f2937; font-size: 0.95rem; }

    .user-level { font-size: 0.8rem; color: #9ca3af; }

    .user-stats { text-align: right; }

    .user-score { font-weight: 700; color: #00337F; font-size: 1rem; }

    .user-details { font-size: 0.8rem; color: #9ca3af; }

    .action-buttons { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }

    .action-btn { display: flex; align-items: center; gap: 0.75rem; padding: 1.25rem; border-radius: 10px; text-decoration: none; color: white; font-weight: 600; transition: all 0.3s; border: none; cursor: pointer; }

    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); }

    .action-btn.primary { background: linear-gradient(135deg, #00337F 0%, #003F99 100%); }

    .action-btn.success { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }

    .action-btn.warning { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #1f2937; }

    .action-btn.info { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .section-title { font-size: 1.3rem; font-weight: 700; color: #1f2937; margin: 2rem 0 1.5rem 0; }

    .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }

    .status-indicator { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }

    .status-ok { background: #22c55e; }

    .status-error { background: #ef4444; }

    .status-warning { background: #f59e0b; }

    /* Charts */
    .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .chart-card { background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
    .chart-card h3 { font-size: 1rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .chart-container { position: relative; height: 280px; }

    @media (max-width: 768px) {
        .admin-container { padding: 1rem; }
        .admin-header h1 { font-size: 1.75rem; }
        .stat-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; }
        .kpi-grid { grid-template-columns: 1fr; }
        .kpi-card { flex-direction: column; text-align: center; }
        .kpi-icon { margin-top: 1rem; }
        .action-buttons { grid-template-columns: 1fr; }
        .card { padding: 1.5rem; }
        .charts-grid { grid-template-columns: 1fr; }
        .chart-container { height: 220px; }
    }
</style>
@endpush

@section('content')

<div class="admin-wrapper">
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1>⚙️ Admin Dashboard</h1>
            <p class="admin-subtitle">Übersicht über System-Status, Benutzer und Lernfortschritt</p>
        </div>

        <!-- System Status -->
        <div class="section-title">🔧 System Status</div>
        <div class="stat-grid">
            <div class="stat-card system-status">
                <div class="stat-label">Datenbank</div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="status-indicator {{ $systemStatus['database']['status'] === 'ok' ? 'status-ok' : 'status-error' }}"></span>
                    <span class="stat-value" style="margin: 0; font-size: 1rem;">{{ ucfirst($systemStatus['database']['status']) }}</span>
                </div>
                <p class="stat-subtext">{{ $systemStatus['database']['message'] }}</p>
            </div>
            
            <div class="stat-card system-status">
                <div class="stat-label">Cache</div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="status-indicator {{ $systemStatus['cache']['status'] === 'ok' ? 'status-ok' : 'status-error' }}"></span>
                    <span class="stat-value" style="margin: 0; font-size: 1rem;">{{ ucfirst($systemStatus['cache']['status']) }}</span>
                </div>
                <p class="stat-subtext">{{ $systemStatus['cache']['message'] }}</p>
            </div>
            
            <div class="stat-card system-status">
                <div class="stat-label">Storage</div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="status-indicator {{ $systemStatus['storage']['status'] === 'ok' ? 'status-ok' : 'status-error' }}"></span>
                    <span class="stat-value" style="margin: 0; font-size: 1rem;">{{ ucfirst($systemStatus['storage']['status']) }}</span>
                </div>
                <p class="stat-subtext">{{ $systemStatus['storage']['message'] }}</p>
            </div>
            
            <div class="stat-card system-status">
                <div class="stat-label">Nutzer Online</div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="status-indicator {{ $systemStatus['online_users']['status'] === 'ok' ? 'status-ok' : 'status-warning' }}"></span>
                    <span class="stat-value" style="margin: 0; font-size: 1.5rem;">{{ $systemStatus['online_users']['count'] }}</span>
                </div>
                <p class="stat-subtext">{{ $systemStatus['online_users']['message'] }}</p>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="section-title">📊 Wichtige Kennzahlen</div>
        <div class="kpi-grid">
            <!-- Gesamt Benutzer -->
            <div class="kpi-card">
                <div class="kpi-content">
                    <h3>Gesamt Benutzer</h3>
                    <p class="kpi-value">{{ $totalUsers }}</p>
                    <p class="kpi-sub">+{{ $newUsersToday }} heute</p>
                </div>
                <div class="kpi-icon">👥</div>
            </div>

            <!-- E-Mail bestätigt -->
            <div class="kpi-card">
                <div class="kpi-content">
                    <h3>E-Mail bestätigt</h3>
                    <p class="kpi-value">{{ $verifiedUsers }}</p>
                    <p class="kpi-sub">{{ $verificationRate }}% Rate</p>
                </div>
                <div class="kpi-icon">✅</div>
            </div>

            <!-- Gesamt Fragen -->
            <div class="kpi-card">
                <div class="kpi-content">
                    <h3>Gesamt Fragen</h3>
                    <p class="kpi-value">{{ $totalQuestions }}</p>
                    <p class="kpi-sub">{{ $learningSections }} Lernabschnitte</p>
                </div>
                <div class="kpi-icon">❓</div>
            </div>

            <!-- Beantwortete Fragen -->
            <div class="kpi-card">
                <div class="kpi-content">
                    <h3>Beantwortete Fragen</h3>
                    <p class="kpi-value">{{ number_format($totalAnsweredQuestions) }}</p>
                    <p class="kpi-sub">{{ $wrongAnswerRate }}% Falsch</p>
                </div>
                <div class="kpi-icon">📊</div>
            </div>
        </div>

        <!-- Detail Cards -->
        <div class="section-title">📈 Detaillierte Statistiken</div>
        <div class="grid-2">
            <!-- Fragen-Statistik -->
            <div class="card">
                <h3><span class="card-icon">📉</span> Fragen-Statistik</h3>
                <div class="stat-row">
                    <span class="stat-label-col">Gesamt beantwortet</span>
                    <span class="stat-value-col">{{ number_format($totalAnsweredQuestions) }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label-col">Richtig</span>
                    <span class="stat-value-col success">{{ number_format($totalCorrectAnswers) }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label-col">Falsch</span>
                    <span class="stat-value-col" style="color: #ef4444;">{{ number_format($totalWrongAnswers) }}</span>
                </div>
                <div class="stat-row" style="border-bottom: 2px solid #e5e7eb; padding: 1.5rem 0;">
                    <span class="stat-label-col" style="font-weight: 700; color: #1f2937;">Erfolgsrate</span>
                    <span class="stat-value-col primary" style="font-size: 1.25rem;">{{ $totalAnsweredQuestions > 0 ? round((($totalCorrectAnswers / $totalAnsweredQuestions) * 100), 1) : 0 }}%</span>
                </div>
            </div>

            <!-- Benutzer-Aktivität -->
            <div class="card">
                <h3><span class="card-icon">📈</span> Benutzer-Aktivität</h3>
                <div class="stat-row">
                    <span class="stat-label-col">Heute</span>
                    <span class="stat-value-col">{{ $userActivity['today'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label-col">Diese Woche</span>
                    <span class="stat-value-col">{{ $userActivity['this_week'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label-col">Diesen Monat</span>
                    <span class="stat-value-col">{{ $userActivity['this_month'] }}</span>
                </div>
            </div>

            <!-- Lernfortschritt -->
            <div class="card">
                <h3><span class="card-icon">🎓</span> Lernfortschritt</h3>
                <div class="stat-row">
                    <span class="stat-label-col">Gesamt Punkte</span>
                    <span class="stat-value-col warning">{{ number_format($learningProgress['total_points']) }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label-col">Mit Erfolgen</span>
                    <span class="stat-value-col">{{ $learningProgress['users_with_achievements'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label-col">Ø Fortschritt</span>
                    <span class="stat-value-col primary">{{ $learningProgress['average_progress'] }}%</span>
                </div>
            </div>
        </div>

        <!-- Leaderboard Section -->
        <div class="section-title">🏆 Leaderboard Top-10</div>
        <div class="card">
            <div style="max-height: 400px; overflow-y: auto;">
                @forelse($leaderboard as $index => $user)
                    @php
                        $position = $index + 1;
                        $medal = match($position) {
                            1 => '🥇',
                            2 => '🥈', 
                            3 => '🥉',
                            default => ''
                        };
                    @endphp
                    <div class="leaderboard-item {{ $position <= 3 ? 'top-rank' : '' }}">
                        <div style="display: flex; align-items: center; flex: 1;">
                            <span class="medal">{{ $medal ?: $position . '.' }}</span>
                            <div class="user-info">
                                <div class="user-name">{{ $user['name'] }}</div>
                                <div class="user-level">Level {{ $user['level'] }}</div>
                            </div>
                        </div>
                        <div class="user-stats">
                            <div class="user-score">{{ number_format($user['score']) }} Punkte</div>
                            <div class="user-details">{{ $user['solved_questions'] }} Fragen • {{ $user['exam_passed'] }} Prüfungen</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 3rem 1rem; color: #9ca3af;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                        <p>Noch keine Benutzer-Daten verfügbar</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Statistiken Charts (29 Tage) -->
        <div class="section-title">📊 Statistiken (letzte 29 Tage)</div>
        <div class="charts-grid">
            <!-- Chart 1: Aktive Benutzer + Registrierungen -->
            <div class="chart-card">
                <h3>👥 Benutzeraktivität</h3>
                <div class="chart-container">
                    <canvas id="userActivityChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Beantwortete Fragen (Total, Richtig, Falsch) -->
            <div class="chart-card">
                <h3>❓ Beantwortete Fragen</h3>
                <div class="chart-container">
                    <canvas id="questionsChart"></canvas>
                </div>
            </div>

            <!-- Chart 3: User-Verlauf -->
            <div class="chart-card">
                <h3>📈 User-Wachstum</h3>
                <div class="chart-container">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section-title">⚡ Schnellaktionen</div>
        <div class="action-buttons">
            <a href="{{ route('admin.questions.index') }}" class="action-btn warning">
                <span>❓</span>
                <span>Fragen verwalten</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="action-btn primary">
                <span>👥</span>
                <span>Benutzer verwalten</span>
            </a>
            <a href="{{ route('admin.newsletter.create') }}" class="action-btn success">
                <span>📧</span>
                <span>Newsletter senden</span>
            </a>
            <a href="{{ route('admin.contact-messages.index') }}" class="action-btn info">
                <span>💬</span>
                <span>Kontaktanfragen</span>
                @php
                    $unreadCount = \App\Models\ContactMessage::where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span style="margin-left: auto; background: #ef4444; color: white; font-size: 0.8rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 9999px;">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('dashboard') }}" class="action-btn primary">
                <span>←</span>
                <span>Zurück zum Dashboard</span>
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Warte bis Chart.js geladen ist
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js konnte nicht geladen werden!');
        return;
    }

    console.log('✅ Chart.js geladen');

    // Chart.js Globale Konfiguration
    Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
    Chart.defaults.color = '#6b7280';

    // Gemeinsame Chart-Optionen
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: { size: 11 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f3f4f6' },
                ticks: { font: { size: 10 } }
            },
            x: {
                grid: { display: false },
                ticks: {
                    font: { size: 9 },
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    };

    // Chart 1: Benutzeraktivität (Aktive + Registrierungen)
    try {
        new Chart(document.getElementById('userActivityChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [
                    {
                        label: 'Aktive Benutzer',
                        data: {!! json_encode($chartData['active']) !!},
                        borderColor: '#0066CC',
                        backgroundColor: 'rgba(0, 102, 204, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Neue Registrierungen',
                        data: {!! json_encode($chartData['registrations']) !!},
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: commonOptions
        });
        console.log('✅ Benutzeraktivität Chart erstellt');
    } catch (error) {
        console.error('❌ Fehler bei Benutzeraktivität Chart:', error);
    }

    // Chart 2: Beantwortete Fragen (Total, Richtig, Falsch)
    try {
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
                        pointRadius: 2,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Richtig',
                        data: {!! json_encode($chartData['questionsCorrect']) !!},
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Falsch',
                        data: {!! json_encode($chartData['questionsWrong']) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: commonOptions
        });
        console.log('✅ Fragen Chart erstellt');
    } catch (error) {
        console.error('❌ Fehler bei Fragen Chart:', error);
    }

    // Chart 3: User-Wachstum
    try {
        new Chart(document.getElementById('userGrowthChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Gesamtanzahl User',
                    data: {!! json_encode($chartData['userCount']) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 2,
                    pointHoverRadius: 5
                }]
            },
            options: commonOptions
        });
        console.log('✅ User-Wachstum Chart erstellt');
    } catch (error) {
        console.error('❌ Fehler bei User-Wachstum Chart:', error);
    }
});
</script>
@endpush
