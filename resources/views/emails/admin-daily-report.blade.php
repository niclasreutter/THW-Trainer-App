<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>THW-Trainer Tagesreport - {{ $date }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 3px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #2563eb; margin: 0; font-size: 28px; }
        .header p { color: #666; margin: 10px 0 0 0; font-size: 16px; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #1f2937; border-left: 4px solid #2563eb; padding-left: 15px; margin-bottom: 15px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #2563eb; display: block; }
        .stat-label { font-size: 14px; color: #64748b; margin-top: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table th { background-color: #f8fafc; font-weight: bold; color: #374151; }
        .table tr:hover { background-color: #f8fafc; }
        .alert { padding: 15px; border-radius: 8px; margin: 15px 0; }
        .alert-info { background-color: #dbeafe; border-left: 4px solid #2563eb; color: #1e40af; }
        .alert-success { background-color: #dcfce7; border-left: 4px solid #16a34a; color: #166534; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 14px; }
        .emoji { font-size: 20px; margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 THW-Trainer Tagesreport</h1>
            <p>Automatischer Bericht für {{ $date }} ({{ $report_day }})</p>
        </div>

        <!-- Benutzer-Statistiken -->
        <div class="section">
            <h2>👥 Benutzer-Übersicht</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($users['total']) }}</span>
                    <div class="stat-label">Gesamt Benutzer</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($users['verified']) }}</span>
                    <div class="stat-label">Verifizierte Benutzer</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($users['active_yesterday']) }}</span>
                    <div class="stat-label">Aktiv gestern</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($users['new_yesterday']) }}</span>
                    <div class="stat-label">Neu registriert gestern</div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>📈 Aktivität:</strong> {{ number_format($users['active_last_7_days']) }} aktive Benutzer in den letzten 7 Tagen | {{ number_format($users['active_last_30_days']) }} in den letzten 30 Tagen
            </div>
        </div>

        <!-- Aktivitäts-Statistiken -->
        <div class="section">
            <h2>🎯 Lernaktivität</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($activity['questions_answered_yesterday']) }}</span>
                    <div class="stat-label">Fragen gestern beantwortet</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($activity['correct_answers_yesterday']) }}</span>
                    <div class="stat-label">Richtige Antworten gestern</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($activity['total_questions_answered']) }}</span>
                    <div class="stat-label">Gesamt beantwortet</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($activity['total_correct_answers']) }}</span>
                    <div class="stat-label">Gesamt richtig</div>
                </div>
            </div>

            @if($activity['questions_answered_yesterday'] > 0)
                @php $successRate = round(($activity['correct_answers_yesterday'] / $activity['questions_answered_yesterday']) * 100, 1); @endphp
                <div class="alert alert-success">
                    <strong>✅ Erfolgsquote gestern:</strong> {{ $successRate }}% ({{ $activity['correct_answers_yesterday'] }}/{{ $activity['questions_answered_yesterday'] }})
                </div>
            @endif
        </div>

        <!-- Gamification -->
        <div class="section">
            <h2>🏆 Gamification & Engagement</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($gamification['total_points_awarded']) }}</span>
                    <div class="stat-label">Gesamt vergebene Punkte</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($gamification['avg_points_per_user'], 0) }}</span>
                    <div class="stat-label">Ø Punkte pro Benutzer</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($gamification['users_with_streak']) }}</span>
                    <div class="stat-label">Benutzer mit Streak</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($gamification['top_user_points']) }}</span>
                    <div class="stat-label">Höchste Punktzahl</div>
                </div>
            </div>
        </div>

        <!-- Top Benutzer -->
        @if(count($top_users) > 0)
        <div class="section">
            <h2>🥇 Top 5 Benutzer</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Punkte</th>
                        <th>Level</th>
                        <th>Streak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($top_users as $user)
                    <tr>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ number_format($user['points']) }}</td>
                        <td>{{ $user['level'] }}</td>
                        <td>{{ $user['streak_days'] }} Tage</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- System-Informationen -->
        <div class="section">
            <h2>⚙️ System-Status</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number">{{ number_format($system['total_questions']) }}</span>
                    <div class="stat-label">Fragen in der DB</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $system['database_size'] }}</span>
                    <div class="stat-label">Datenbank-Größe</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $system['cache_hit_rate'] }}</span>
                    <div class="stat-label">Cache Hit-Rate</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $system['server_uptime'] }}</span>
                    <div class="stat-label">Server Uptime</div>
                </div>
            </div>
        </div>

        <!-- Letzte Aktivitäten -->
        <div class="section">
            <h2>📈 Aktivität gestern</h2>
            <div class="alert alert-info">
                <strong>🆕 Neue Benutzer:</strong> {{ $recent_activity['new_users_yesterday'] }}<br>
                <strong>❓ Beantwortete Fragen:</strong> {{ $recent_activity['questions_answered_yesterday'] }}<br>
                <strong>📝 Abgelegte Prüfungen:</strong> {{ $recent_activity['exams_taken_yesterday'] }}
            </div>
        </div>

        <div class="footer">
            <p>🤖 Automatisch generiert am {{ now()->format('d.m.Y H:i:s') }} Uhr</p>
            <p>THW-Trainer.de - Dein Weg zur erfolgreichen THW-Prüfung</p>
        </div>
    </div>
</body>
</html>
