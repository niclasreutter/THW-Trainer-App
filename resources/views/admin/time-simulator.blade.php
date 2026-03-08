@extends('layouts.app')
@section('title', 'Zeitsimulator - THW Trainer Admin')

@push('styles')
<style>
    .simulator-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 768px) {
        .simulator-actions {
            grid-template-columns: 1fr;
        }
    }
    .action-card {
        padding: 1.25rem;
        border-radius: 0.75rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
    }
    .action-card h3 {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
    }
    .action-card p {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    .log-output {
        background: rgba(0,0,0,0.4);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.5rem;
        padding: 1rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem;
        line-height: 1.6;
        color: var(--text-secondary);
        max-height: 400px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .log-output .log-success { color: var(--success); }
    .log-output .log-warning { color: var(--warning); }
    .log-output .log-error { color: var(--error); }
    .log-output .log-header { color: var(--gold); font-weight: 600; }
    .user-status-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
    }
    .user-status-table th {
        text-align: left;
        padding: 0.5rem 0.75rem;
        color: var(--text-secondary);
        font-weight: 500;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .user-status-table td {
        padding: 0.5rem 0.75rem;
        color: var(--text-primary);
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .env-banner {
        background: rgba(var(--warning-rgb, 245, 158, 11), 0.15);
        border: 1px solid rgba(var(--warning-rgb, 245, 158, 11), 0.3);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
        color: var(--warning);
        margin-bottom: 1.5rem;
    }
    .form-row {
        display: flex;
        gap: 0.75rem;
        align-items: end;
        flex-wrap: wrap;
    }
    .form-group {
        flex: 1;
        min-width: 150px;
    }
    .form-group label {
        display: block;
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-bottom: 0.35rem;
        font-weight: 500;
    }
    .form-group select,
    .form-group input[type="date"] {
        width: 100%;
        padding: 0.5rem 0.75rem;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.5rem;
        color: var(--text-primary);
        font-size: 0.85rem;
    }
    .form-group select:focus,
    .form-group input[type="date"]:focus {
        outline: none;
        border-color: var(--gold);
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Zeit<span>simulator</span></h1>
        <p class="page-subtitle">Simuliere mehrtaegige Streak-Ablaeufe an einem Tag</p>
    </header>

    <div class="env-banner">
        <i class="bi bi-exclamation-triangle"></i>
        Nur in Test-Umgebungen verfuegbar. Umgebung: <strong>{{ app()->environment() }}</strong>
    </div>

    <!-- User Status Overview -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-people"></i></span>
            <div>
                <div class="stat-pill-value">{{ count($users) }}</div>
                <div class="stat-pill-label">Nutzer</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-fire"></i></span>
            <div>
                <div class="stat-pill-value">{{ collect($users)->where('streak_days', '>', 0)->count() }}</div>
                <div class="stat-pill-label">Aktive Streaks</div>
            </div>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: var(--thw-blue-light);"><i class="bi bi-shield-check"></i></span>
            <div>
                <div class="stat-pill-value">{{ collect($users)->sum('freezes_remaining') }}</div>
                <div class="stat-pill-label">Freezes gesamt</div>
            </div>
        </div>
    </div>

    <div class="bento-grid">
        <!-- Simulator Actions -->
        <div class="glass-gold bento-main">
            <h2 class="text-lg font-semibold text-dark-primary mb-4">Aktionen</h2>

            <div class="simulator-actions">
                <!-- 1. Simulate Activity -->
                <div class="action-card">
                    <h3>Aktivitaet simulieren</h3>
                    <p>Setzt last_activity_date und daily_questions auf das gewaehlte Datum. Streak wird entsprechend angepasst.</p>
                    <form method="POST" action="{{ route('admin.time-simulator.activity') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }} ({{ $user['email'] }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Datum</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary mt-3 w-full">Aktivitaet simulieren</button>
                    </form>
                </div>

                <!-- 2. Daily Reset -->
                <div class="action-card">
                    <h3>Daily Reset ausfuehren</h3>
                    <p>Fuehrt die Mitternachts-Logik aus: Streak pruefen, Freeze einsetzen oder Streak zuruecksetzen. Sendet E-Mails.</p>
                    <form method="POST" action="{{ route('admin.time-simulator.reset') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }} ({{ $user['email'] }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Simuliertes Datum</label>
                                <input type="date" name="date" value="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-secondary mt-3 w-full">Daily Reset ausfuehren</button>
                    </form>
                </div>

                <!-- 3. Streak Reminder -->
                <div class="action-card">
                    <h3>Streak Reminder senden</h3>
                    <p>Simuliert die 18-Uhr-Erinnerung. Sendet eine E-Mail wenn der User heute noch nicht gelernt hat.</p>
                    <form method="POST" action="{{ route('admin.time-simulator.reminder') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }} ({{ $user['email'] }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Simuliertes Datum</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-ghost mt-3 w-full">Reminder senden</button>
                    </form>
                </div>

                <!-- 4. Reset User -->
                <div class="action-card">
                    <h3>User zuruecksetzen</h3>
                    <p>Setzt Streak, Last Activity und Daily Questions auf 0/null zurueck. Punkte und Level bleiben erhalten.</p>
                    <form method="POST" action="{{ route('admin.time-simulator.reset-user') }}" onsubmit="return confirm('Streak-Daten wirklich zuruecksetzen?')">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }} ({{ $user['email'] }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn-danger mt-3 w-full">Zuruecksetzen</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Log Output & User Status -->
        <div class="glass-tl bento-side">
            <h2 class="text-lg font-semibold text-dark-primary mb-4">Ergebnis</h2>

            @if(session('simulator_log'))
                <div class="log-output">
                    @foreach(session('simulator_log') as $line)
                        @if(str_starts_with($line, '==='))
                            <span class="log-header">{{ $line }}</span>
                        @elseif(str_contains($line, 'FEHLER') || str_contains($line, 'WARNUNG'))
                            <span class="log-warning">{{ $line }}</span>
                        @elseif(str_contains($line, 'gesendet') || str_contains($line, 'Nachher'))
                            <span class="log-success">{{ $line }}</span>
                        @else
                            {{ $line }}
                        @endif
{{ '' }}
                    @endforeach
                </div>
            @else
                <div class="log-output">
                    <span class="log-header">Noch keine Aktion ausgefuehrt.</span>

Waehle eine Aktion links aus, um den Streak-Flow zu simulieren.

Beispiel-Flow:
1. User zuruecksetzen
2. Aktivitaet simulieren (Tag 1)
3. Aktivitaet simulieren (Tag 2)
4. Streak Reminder (Tag 3)
5. Daily Reset (Tag 4) → Freeze oder Streak verloren
                </div>
            @endif

            <h2 class="text-lg font-semibold text-dark-primary mt-6 mb-4">User-Status</h2>
            <div style="overflow-x: auto;">
                <table class="user-status-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Streak</th>
                            <th>Last Activity</th>
                            <th>Freezes</th>
                            <th>Mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user['name'] }}</td>
                                <td>
                                    @if($user['streak_days'] > 0)
                                        <span class="text-success">{{ $user['streak_days'] }}d</span>
                                    @else
                                        <span class="text-dark-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user['last_activity_date'])
                                        {{ \Carbon\Carbon::parse($user['last_activity_date'])->format('d.m.Y') }}
                                    @else
                                        <span class="text-dark-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $user['freezes_remaining'] }}</td>
                                <td>
                                    @if($user['email_consent'])
                                        <span class="text-success">Ja</span>
                                    @else
                                        <span class="text-dark-muted">Nein</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
