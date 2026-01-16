<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>THW-Trainer Admin Report - {{ $date }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; color: #1a1a1a; background: #f5f5f5; padding: 20px; }
        .container { max-width: 700px; margin: 0 auto; background: #ffffff; }

        /* Header */
        .header { background: linear-gradient(135deg, #0066CC 0%, #004C99 100%); color: white; padding: 30px; }
        .header h1 { font-size: 24px; font-weight: 600; margin-bottom: 5px; }
        .header .date { opacity: 0.9; font-size: 14px; }

        /* Warnungen */
        .warnings { padding: 20px; background: #f8f9fa; border-left: 4px solid #666; }
        .warnings.has-danger { background: #fff5f5; border-color: #dc3545; }
        .warnings.has-success { background: #f0fdf4; border-color: #16a34a; }
        .warning-item { padding: 8px 0; font-size: 14px; }
        .warning-item.danger { color: #dc3545; font-weight: 500; }
        .warning-item.warning { color: #f59e0b; font-weight: 500; }
        .warning-item.success { color: #16a34a; font-weight: 500; }

        /* Sections */
        .section { padding: 25px 30px; border-bottom: 1px solid #e5e7eb; }
        .section-title { font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #0066CC; }

        /* KPI Grid */
        .kpi-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 15px; }
        .kpi-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; }
        .kpi-label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .kpi-value { font-size: 28px; font-weight: 700; color: #111827; line-height: 1; }
        .kpi-trend { font-size: 13px; margin-top: 5px; font-weight: 500; }
        .kpi-trend.up { color: #16a34a; }
        .kpi-trend.down { color: #dc3545; }
        .kpi-trend.neutral { color: #6b7280; }
        .kpi-subtext { font-size: 12px; color: #6b7280; margin-top: 3px; }
        .sparkline { font-size: 16px; letter-spacing: 1px; font-family: monospace; color: #0066CC; margin-top: 5px; }

        /* Stats Row */
        .stats-row { display: flex; justify-content: space-between; margin: 12px 0; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .stats-row:last-child { border-bottom: none; }
        .stats-label { font-size: 14px; color: #4b5563; }
        .stats-value { font-size: 14px; font-weight: 600; color: #111827; }

        /* Top Users Table */
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th { text-align: left; font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px; border-bottom: 2px solid #e5e7eb; }
        .table td { padding: 10px 8px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
        .table tr:last-child td { border-bottom: none; }
        .rank { display: inline-block; width: 24px; height: 24px; line-height: 24px; text-align: center; border-radius: 50%; font-weight: 600; font-size: 12px; }
        .rank-1 { background: #fbbf24; color: #78350f; }
        .rank-2 { background: #d1d5db; color: #374151; }
        .rank-3 { background: #f59e0b; color: #78350f; }
        .rank-other { background: #f3f4f6; color: #6b7280; }

        /* Footer */
        .footer { padding: 20px 30px; background: #f9fafb; text-align: center; font-size: 12px; color: #6b7280; }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            body { padding: 0; }
            .kpi-grid { grid-template-columns: 1fr; }
            .section { padding: 20px 15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>THW-Trainer Admin Report</h1>
            <div class="date">{{ $date }} ({{ $report_day }})</div>
        </div>

        <!-- Warnungen / Highlights -->
        @if(count($warnings) > 0)
            @php
                $hasDanger = collect($warnings)->contains('type', 'danger');
                $hasSuccess = !$hasDanger && collect($warnings)->contains('type', 'success');
                $warnClass = $hasDanger ? 'has-danger' : ($hasSuccess ? 'has-success' : '');
            @endphp
            <div class="warnings {{ $warnClass }}">
                @foreach($warnings as $warning)
                    <div class="warning-item {{ $warning['type'] }}">
                        @if($warning['type'] == 'danger') ⚠️
                        @elseif($warning['type'] == 'success') ✓
                        @else ℹ️
                        @endif
                        {{ $warning['message'] }}
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Benutzer -->
        <div class="section">
            <div class="section-title">Benutzer</div>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Aktive Gestern</div>
                    <div class="kpi-value">{{ number_format($users['active_yesterday']) }}</div>
                    <div class="sparkline">{{ $users['active_sparkline'] }}</div>
                    @if($users['active_trend']['direction'] != 'neutral')
                        <div class="kpi-trend {{ $users['active_trend']['direction'] }}">
                            @if($users['active_trend']['direction'] == 'up') ↗
                            @else ↘
                            @endif
                            {{ $users['active_trend']['percentage'] }}% vs. Vortag
                        </div>
                    @endif
                    <div class="kpi-subtext">{{ number_format($users['active_last_7_days']) }} in 7 Tagen</div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-label">Neue Registrierungen</div>
                    <div class="kpi-value">{{ number_format($users['new_yesterday']) }}</div>
                    <div class="sparkline">{{ $users['new_sparkline'] }}</div>
                    @if($users['new_trend']['direction'] != 'neutral')
                        <div class="kpi-trend {{ $users['new_trend']['direction'] }}">
                            @if($users['new_trend']['direction'] == 'up') ↗
                            @else ↘
                            @endif
                            {{ $users['new_trend']['percentage'] }}% vs. Vortag
                        </div>
                    @endif
                    <div class="kpi-subtext">{{ number_format($users['new_last_7_days']) }} in 7 Tagen</div>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <div class="stats-row">
                    <span class="stats-label">Gesamt Benutzer</span>
                    <span class="stats-value">{{ number_format($users['total']) }}</span>
                </div>
                <div class="stats-row">
                    <span class="stats-label">Verifizierte Accounts</span>
                    <span class="stats-value">{{ number_format($users['verified']) }} ({{ $users['verification_rate'] }}%)</span>
                </div>
                <div class="stats-row">
                    <span class="stats-label">Aktiv (30 Tage)</span>
                    <span class="stats-value">{{ number_format($users['active_last_30_days']) }}</span>
                </div>
            </div>
        </div>

        <!-- Lernaktivität -->
        <div class="section">
            <div class="section-title">Lernaktivität</div>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Beantwortete Fragen</div>
                    <div class="kpi-value">{{ number_format($activity['questions_answered_yesterday']) }}</div>
                    <div class="sparkline">{{ $activity['questions_sparkline'] }}</div>
                    @if($activity['questions_trend']['direction'] != 'neutral')
                        <div class="kpi-trend {{ $activity['questions_trend']['direction'] }}">
                            @if($activity['questions_trend']['direction'] == 'up') ↗
                            @else ↘
                            @endif
                            {{ $activity['questions_trend']['percentage'] }}% vs. Vortag
                        </div>
                    @endif
                    <div class="kpi-subtext">Ø {{ $activity['avg_questions_per_user'] }} pro aktivem User</div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-label">Erfolgsquote</div>
                    <div class="kpi-value">{{ $activity['success_rate_yesterday'] }}%</div>
                    @if($activity['success_rate_trend']['direction'] != 'neutral')
                        <div class="kpi-trend {{ $activity['success_rate_trend']['direction'] }}">
                            @if($activity['success_rate_trend']['direction'] == 'up') ↗
                            @else ↘
                            @endif
                            {{ $activity['success_rate_trend']['percentage'] }}% vs. Vortag
                        </div>
                    @endif
                    <div class="kpi-subtext">{{ number_format($activity['correct_answers_yesterday']) }} richtige Antworten</div>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <div class="stats-row">
                    <span class="stats-label">Gesamt beantwortet (all-time)</span>
                    <span class="stats-value">{{ number_format($activity['total_questions_answered']) }}</span>
                </div>
            </div>
        </div>

        <!-- Gamification -->
        <div class="section">
            <div class="section-title">Engagement & Gamification</div>

            <div class="stats-row">
                <span class="stats-label">Benutzer mit aktivem Streak</span>
                <span class="stats-value">{{ number_format($gamification['users_with_streak']) }}</span>
            </div>
            <div class="stats-row">
                <span class="stats-label">Durchschnittliche Streak-Länge</span>
                <span class="stats-value">{{ $gamification['avg_streak_length'] }} Tage</span>
            </div>
            <div class="stats-row">
                <span class="stats-label">Längster aktiver Streak</span>
                <span class="stats-value">{{ $gamification['longest_streak'] }} Tage</span>
            </div>
            <div class="stats-row">
                <span class="stats-label">Durchschnitt Punkte/Benutzer</span>
                <span class="stats-value">{{ number_format($gamification['avg_points_per_user']) }}</span>
            </div>
            <div class="stats-row">
                <span class="stats-label">Benutzer Level 5+</span>
                <span class="stats-value">{{ number_format($gamification['users_level_5_plus']) }}</span>
            </div>
        </div>

        <!-- Top 5 Benutzer -->
        @if(count($top_users) > 0)
        <div class="section">
            <div class="section-title">Top 5 Benutzer (Punkte)</div>

            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Name</th>
                        <th style="text-align: right;">Punkte</th>
                        <th style="text-align: center;">Level</th>
                        <th style="text-align: center;">Streak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($top_users as $index => $user)
                    <tr>
                        <td>
                            <span class="rank rank-{{ $index + 1 <= 3 ? $index + 1 : 'other' }}">{{ $index + 1 }}</span>
                        </td>
                        <td style="font-weight: 500;">{{ $user['name'] }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($user['points']) }}</td>
                        <td style="text-align: center;">{{ $user['level'] }}</td>
                        <td style="text-align: center;">{{ $user['streak_days'] }}d</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- System -->
        <div class="section">
            <div class="section-title">System</div>

            <div class="stats-row">
                <span class="stats-label">Gesamt Fragen in Datenbank</span>
                <span class="stats-value">{{ number_format($system['total_questions'] + $system['lehrgang_questions'] + $system['lernpool_questions']) }}</span>
            </div>
            <div class="stats-row" style="padding-left: 20px; font-size: 13px; color: #6b7280;">
                <span class="stats-label">↳ Basis-Fragen</span>
                <span class="stats-value">{{ number_format($system['total_questions']) }}</span>
            </div>
            <div class="stats-row" style="padding-left: 20px; font-size: 13px; color: #6b7280;">
                <span class="stats-label">↳ Lehrgangs-Fragen</span>
                <span class="stats-value">{{ number_format($system['lehrgang_questions']) }}</span>
            </div>
            <div class="stats-row" style="padding-left: 20px; font-size: 13px; color: #6b7280;">
                <span class="stats-label">↳ Lernpool-Fragen</span>
                <span class="stats-value">{{ number_format($system['lernpool_questions']) }}</span>
            </div>
            <div class="stats-row">
                <span class="stats-label">Datenbank-Größe</span>
                <span class="stats-value">{{ $system['database_size'] }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Automatisch generiert am {{ now()->format('d.m.Y H:i') }} Uhr</div>
            <div style="margin-top: 5px;">THW-Trainer.de</div>
        </div>
    </div>
</body>
</html>
