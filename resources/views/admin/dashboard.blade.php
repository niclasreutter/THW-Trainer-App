@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('description', 'Übersicht über System-Status, Benutzerstatistiken und Lernfortschritt')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ── System Pills ── */
    .sys-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.3rem 0.625rem;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 2rem;
        font-size: 0.6875rem;
        font-family: 'IBM Plex Mono', monospace;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all var(--transition-fast);
    }
    .sys-pill:hover { border-color: rgba(91, 154, 255, 0.3); }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-dot.ok { background: #22c55e; box-shadow: 0 0 4px rgba(34, 197, 94, 0.4); }
    .status-dot.err { background: #ef4444; box-shadow: 0 0 4px rgba(239, 68, 68, 0.4); }

    /* ── Card Label ── */
    .card-label {
        font-size: 0.5625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 0.75rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ── Activity Feed ── */
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.625rem;
        border-radius: 0.5rem;
        margin-bottom: 0.375rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        transition: all var(--transition-fast);
        text-decoration: none;
        color: inherit;
    }
    .activity-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .activity-icon {
        width: 30px;
        height: 30px;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .activity-icon.success { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .activity-icon.error { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    .activity-icon.warning { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .activity-icon.info { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .activity-icon.gold { background: rgba(251, 191, 36, 0.12); color: #fbbf24; }
    .activity-icon.purple { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }
    .activity-content { flex: 1; min-width: 0; }
    .activity-title {
        font-size: 0.775rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.1rem;
    }
    .activity-desc {
        font-size: 0.7rem;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .activity-time {
        font-size: 0.6rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        white-space: nowrap;
        margin-left: auto;
        flex-shrink: 0;
    }

    /* ── Stat Rows ── */
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .stat-row:last-child { border-bottom: none; }
    .stat-row-label { font-size: 0.8125rem; color: var(--text-secondary); }
    .stat-row-value {
        font-size: 0.875rem;
        font-weight: 700;
        font-family: 'Barlow Condensed', sans-serif;
    }

    /* ── Leaderboard ── */
    .lb-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.625rem;
        border-radius: 0.5rem;
        margin-bottom: 0.3rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        transition: all var(--transition-fast);
    }
    .lb-item:hover { background: rgba(255, 255, 255, 0.04); }
    .lb-item.top-1 {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.08), rgba(245, 158, 11, 0.03));
        border-color: rgba(251, 191, 36, 0.15);
    }
    .lb-item.top-2 {
        background: linear-gradient(135deg, rgba(156, 163, 175, 0.06), rgba(107, 114, 128, 0.03));
        border-color: rgba(156, 163, 175, 0.12);
    }
    .lb-item.top-3 {
        background: linear-gradient(135deg, rgba(217, 119, 6, 0.06), rgba(180, 83, 9, 0.03));
        border-color: rgba(217, 119, 6, 0.1);
    }
    .lb-rank {
        width: 24px;
        height: 24px;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.7rem;
        flex-shrink: 0;
        font-family: 'IBM Plex Mono', monospace;
    }
    .lb-rank.gold-r { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
    .lb-rank.silver-r { background: rgba(156, 163, 175, 0.15); color: #9ca3af; }
    .lb-rank.bronze-r { background: rgba(217, 119, 6, 0.15); color: #d97706; }
    .lb-rank.default-r { background: rgba(255, 255, 255, 0.05); color: var(--text-muted); }
    .lb-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
        object-fit: cover;
        background: rgba(255, 255, 255, 0.05);
    }
    .lb-info { flex: 1; min-width: 0; }
    .lb-name {
        font-size: 0.775rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .lb-level {
        font-size: 0.6rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }
    .lb-score { text-align: right; flex-shrink: 0; }
    .lb-points {
        font-size: 0.8rem;
        font-weight: 700;
        font-family: 'Barlow Condensed', sans-serif;
    }
    .lb-details {
        font-size: 0.575rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ── Badges ── */
    .badge-sm {
        font-size: 0.5rem;
        font-weight: 700;
        padding: 0.1rem 0.3rem;
        border-radius: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        font-family: 'IBM Plex Mono', monospace;
        vertical-align: middle;
        margin-left: 0.2rem;
    }

    /* ── Chart ── */
    .chart-container {
        height: 200px;
        position: relative;
    }
    .chart-container canvas {
        max-width: 100% !important;
        height: auto !important;
    }

    /* ── Stagger Animation ── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .space-y-4 > * {
        opacity: 0;
        animation: dash-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
    .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }
    .space-y-4 > *:nth-child(6) { animation-delay: 0.23s; }
    .space-y-4 > *:nth-child(7) { animation-delay: 0.27s; }
    .space-y-4 > *:nth-child(8) { animation-delay: 0.31s; }
    .space-y-4 > *:nth-child(9) { animation-delay: 0.35s; }
    .space-y-4 > * + * { margin-top: 1rem; }

    /* ── Reduced Motion ── */
    @media (prefers-reduced-motion: reduce) {
        .space-y-4 > * {
            opacity: 1;
            animation: none;
        }
        .hover-lift:hover { transform: none; }
        .activity-item,
        .lb-item,
        .sys-pill { transition: none; }
    }

    /* ── Responsive ── */
    @media (max-width: 600px) {
        .dash-container { padding: 1rem; }
        .lb-2col,
        .feed-2col { grid-template-columns: 1fr !important; }
    }

    /* ── Light Mode ── */
    html.light-mode .sys-pill {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    html.light-mode .activity-item {
        background: rgba(0, 51, 127, 0.02);
        border-color: rgba(0, 51, 127, 0.06);
    }
    html.light-mode .activity-item:hover {
        background: rgba(0, 51, 127, 0.04);
    }
    html.light-mode .lb-item {
        background: rgba(0, 51, 127, 0.02);
        border-color: rgba(0, 51, 127, 0.06);
    }
    html.light-mode .lb-item:hover {
        background: rgba(0, 51, 127, 0.04);
    }
    html.light-mode .lb-item.top-1 {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.06), rgba(245, 158, 11, 0.02));
        border-color: rgba(251, 191, 36, 0.12);
    }
    html.light-mode .lb-avatar {
        border-color: rgba(0, 51, 127, 0.12);
    }
    html.light-mode .stat-row {
        border-bottom-color: rgba(0, 0, 0, 0.06);
    }
</style>
@endpush

@section('content')
<div class="dash-container">
<div class="space-y-4">

    {{-- ── 1. Header + System Status ── --}}
    <div>
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Administration</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">System Dashboard</h1>
        <p style="font-size:0.875rem;color:var(--text-muted);margin-top:0.125rem;">Benutzer, Aktivitäten und Systemstatus</p>
        <div style="display:flex;gap:0.375rem;flex-wrap:wrap;margin-top:0.75rem;">
            <span class="sys-pill">
                <span class="status-dot {{ $systemStatus['database']['status'] === 'ok' ? 'ok' : 'err' }}"></span> DB
            </span>
            <span class="sys-pill">
                <span class="status-dot {{ $systemStatus['cache']['status'] === 'ok' ? 'ok' : 'err' }}"></span> Cache
            </span>
            <span class="sys-pill" style="border-color:rgba(91,154,255,0.2);">
                <span style="font-weight:700;color:#5b9aff;">{{ $systemStatus['online_users']['count'] }}</span>&nbsp;Online
            </span>
            @if($openIssues > 0)
                <a href="{{ route('admin.issues.index') }}" class="sys-pill" style="border-color:rgba(245,158,11,0.2);">
                    <span style="font-weight:700;color:#f59e0b;">{{ $openIssues }}</span>&nbsp;Issues
                </a>
            @endif
            @if($unreadMessages > 0)
                <a href="{{ route('admin.contact-messages.index') }}" class="sys-pill" style="border-color:rgba(59,130,246,0.2);">
                    <span style="font-weight:700;color:#5b9aff;">{{ $unreadMessages }}</span>&nbsp;Ungelesen
                </a>
            @endif
        </div>
    </div>

    {{-- ── 2. Gami Pills ── --}}
    @php
        $successRate = $totalAnsweredQuestions > 0
            ? round(($totalCorrectAnswers / $totalAnsweredQuestions) * 100, 1)
            : 0;
    @endphp
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">{{ number_format($totalUsers, 0, ',', '.') }}</div>
            <div class="gami-pill__label">Benutzer</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--success);">{{ $verificationRate }}%</div>
            <div class="gami-pill__label">Verifiziert</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value">{{ number_format($totalAnsweredQuestions, 0, ',', '.') }}</div>
            <div class="gami-pill__label">Beantwortet</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value" style="color:var(--success);">{{ $successRate }}%</div>
            <div class="gami-pill__label">Erfolgsrate</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--gold">+{{ $newUsersToday }}</div>
            <div class="gami-pill__label">Heute neu</div>
        </div>
    </div>

    {{-- ── 3. Activity (3/4) + Stats (1/4) ── --}}
    <div class="bento-grid">
        <div class="glass-blue bento-2of3">
            <div class="card-label">Aktivitäts-Feed (24h)</div>
            <div class="feed-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:0.375rem;">
                @forelse($activityFeed as $activity)
                    @if($activity['link'])
                        <a href="{{ $activity['link'] }}" class="activity-item" style="cursor:pointer;">
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
                                    <span class="badge-sm badge-error">NEU</span>
                                @endif
                                @if(!empty($activity['open']))
                                    <span class="badge-sm badge-warning">OFFEN</span>
                                @endif
                            </div>
                            <div class="activity-desc">{{ $activity['description'] }}</div>
                        </div>
                        <span class="activity-time">{{ $activity['time']->diffForHumans(null, true, true) }}</span>
                    @if($activity['link'])
                        </a>
                    @else
                        </div>
                    @endif
                @empty
                    <div style="grid-column:span 2;text-align:center;padding:2rem;color:var(--text-muted);">
                        Keine Aktivitäten in den letzten 24h
                    </div>
                @endforelse
            </div>
        </div>

        <div class="glass-tl bento-third">
            <div class="card-label">Übersicht</div>
            <div class="stat-row">
                <span class="stat-row-label">Aktiv heute</span>
                <span class="stat-row-value text-blue">{{ $userActivity['today'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Aktiv Woche</span>
                <span class="stat-row-value">{{ $userActivity['this_week'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Aktiv Monat</span>
                <span class="stat-row-value">{{ $userActivity['this_month'] }}</span>
            </div>
            <div style="border-top:1px solid rgba(255,255,255,0.06);margin-top:0.375rem;padding-top:0.375rem;">
                <div class="stat-row">
                    <span class="stat-row-label">Ø Fortschritt</span>
                    <span class="stat-row-value text-blue">{{ $learningProgress['average_progress'] }}%</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Mit Erfolgen</span>
                    <span class="stat-row-value">{{ $learningProgress['users_with_achievements'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Richtig</span>
                    <span class="stat-row-value text-success">{{ number_format($totalCorrectAnswers, 0, ',', '.') }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Falsch</span>
                    <span class="stat-row-value text-error">{{ number_format($totalWrongAnswers, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 4. Spaced Repetition Section ── --}}
    <div style="padding-left:1rem;border-left:3px solid var(--gold-start);">
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">Spaced Repetition</h2>
    </div>

    <div class="bento-grid">
        {{-- SR Overview --}}
        <div class="glass-purple bento-half">
            <div class="card-label">SR Übersicht</div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;margin-bottom:1rem;">
                <div class="gami-pill">
                    <div class="gami-pill__value text-purple">{{ number_format($srStats['active_users'], 0, ',', '.') }}</div>
                    <div class="gami-pill__label">User aktiv</div>
                </div>
                <div class="gami-pill">
                    <div class="gami-pill__value text-blue">{{ number_format($srStats['total_in_sr'], 0, ',', '.') }}</div>
                    <div class="gami-pill__label">Fragen im SR</div>
                </div>
                <div class="gami-pill">
                    <div class="gami-pill__value text-success">{{ number_format($srStats['mastered'], 0, ',', '.') }}</div>
                    <div class="gami-pill__label">Gemeistert</div>
                </div>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Ø Intervall</span>
                <span class="stat-row-value text-blue">{{ $srStats['avg_interval'] }} Tage</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Ø Easiness Factor</span>
                <span class="stat-row-value">{{ $srStats['avg_easiness'] }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Mastery-Rate</span>
                <span class="stat-row-value text-success">{{ $srStats['mastery_rate'] }}%</span>
            </div>
        </div>

        {{-- SR Due Reviews --}}
        <div class="glass-slash bento-half">
            <div class="card-label">Fällige Reviews</div>

            {{-- Due Today - prominent --}}
            <div style="text-align:center;padding:0.75rem 0;margin-bottom:0.75rem;background:rgba(139,92,246,0.06);border-radius:0.75rem;border:1px solid rgba(139,92,246,0.1);">
                <div style="font-size:2rem;font-weight:800;color:#8b5cf6;font-family:'Barlow Condensed',sans-serif;">{{ number_format($srStats['due_today'], 0, ',', '.') }}</div>
                <div style="font-size:0.5625rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;font-family:'IBM Plex Mono',monospace;">Reviews fällig heute</div>
            </div>

            <div class="stat-row">
                <span class="stat-row-label">Fällig morgen</span>
                <span class="stat-row-value">{{ number_format($srStats['due_tomorrow'], 0, ',', '.') }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Fällig diese Woche</span>
                <span class="stat-row-value">{{ number_format($srStats['due_this_week'], 0, ',', '.') }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-row-label">Überfällig (verpasst)</span>
                <span class="stat-row-value text-warning">{{ number_format($srStats['overdue'], 0, ',', '.') }}</span>
            </div>

            {{-- Interval distribution bar --}}
            @php
                $dist = $srStats['interval_distribution'];
                $distTotal = max(array_sum($dist), 1);
            @endphp
            <div style="margin-top:0.75rem;">
                <div style="font-size:0.5625rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;font-family:'IBM Plex Mono',monospace;margin-bottom:0.375rem;">Intervall-Verteilung</div>
                <div style="display:flex;gap:2px;height:24px;border-radius:0.25rem;overflow:hidden;">
                    <div style="flex:{{ max($dist['1_3'], 1) }};background:rgba(139,92,246,0.4);display:flex;align-items:center;justify-content:center;font-size:0.5rem;font-weight:600;color:white;">1-3d</div>
                    <div style="flex:{{ max($dist['4_7'], 1) }};background:rgba(91,154,255,0.4);display:flex;align-items:center;justify-content:center;font-size:0.5rem;font-weight:600;color:white;">4-7d</div>
                    <div style="flex:{{ max($dist['8_14'], 1) }};background:rgba(34,197,94,0.4);display:flex;align-items:center;justify-content:center;font-size:0.5rem;font-weight:600;color:white;">8-14d</div>
                    <div style="flex:{{ max($dist['15_plus'], 1) }};background:rgba(251,191,36,0.4);display:flex;align-items:center;justify-content:center;font-size:0.5rem;font-weight:600;color:white;">15d+</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 5. Leaderboard ── --}}
    <div style="padding-left:1rem;border-left:3px solid var(--gold-start);">
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">Leaderboard</h2>
    </div>

    <div class="glass-br">
        <div class="card-label">Top 10</div>
        <div class="lb-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:0.375rem;">
            @forelse($leaderboard as $index => $user)
                @php
                    $position = $index + 1;
                    $rankClass = match($position) {
                        1 => 'top-1',
                        2 => 'top-2',
                        3 => 'top-3',
                        default => '',
                    };
                    $badgeClass = match($position) {
                        1 => 'gold-r',
                        2 => 'silver-r',
                        3 => 'bronze-r',
                        default => 'default-r',
                    };
                @endphp
                <div class="lb-item {{ $rankClass }}">
                    <div class="lb-rank {{ $badgeClass }}">
                        @if($position <= 3)
                            <i class="bi bi-trophy-fill"></i>
                        @else
                            {{ $position }}
                        @endif
                    </div>
                    <img class="lb-avatar"
                         src="{{ $user['avatar_url'] ?? 'https://api.dicebear.com/9.x/avataaars/svg?seed=' . urlencode($user['name']) . '&radius=50&backgroundColor=00337f' }}"
                         alt=""
                         loading="lazy">
                    <div class="lb-info">
                        <div class="lb-name">{{ $user['name'] }}</div>
                        <div class="lb-level">Level {{ $user['level'] }}</div>
                    </div>
                    <div class="lb-score">
                        <div class="lb-points {{ $position === 1 ? 'text-gradient-gold' : '' }}">{{ number_format($user['score'], 0, ',', '.') }}</div>
                        <div class="lb-details">{{ number_format($user['solved_questions'], 0, ',', '.') }} Fragen</div>
                    </div>
                </div>
            @empty
                <div style="grid-column:span 2;text-align:center;padding:1.5rem;color:var(--text-muted);">
                    Keine Daten vorhanden
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── 6. Charts ── --}}
    <div style="padding-left:1rem;border-left:3px solid var(--gold-start);">
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">Statistiken (30 Tage)</h2>
    </div>

    <div class="bento-grid">
        <div class="glass bento-half">
            <div class="card-label">Benutzeraktivität</div>
            <div class="chart-container">
                <canvas id="userActivityChart"></canvas>
            </div>
        </div>

        <div class="glass bento-half">
            <div class="card-label">Beantwortete Fragen</div>
            <div class="chart-container">
                <canvas id="questionsChart"></canvas>
            </div>
        </div>

        <div class="glass-slash bento-wide">
            <div class="card-label">Benutzer-Wachstum</div>
            <div class="chart-container">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
@vite('resources/js/admin.js')
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
                    borderColor: '#0055cc',
                    backgroundColor: 'rgba(0, 85, 204, 0.1)',
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

    // Lineare Regression für Trendlinie
    function linearRegression(data) {
        const n = data.length;
        let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
        for (let i = 0; i < n; i++) {
            sumX += i; sumY += data[i];
            sumXY += i * data[i]; sumXX += i * i;
        }
        const slope = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
        const intercept = (sumY - slope * sumX) / n;
        return data.map((_, i) => Math.max(0, Math.round(intercept + slope * i)));
    }

    const questionsTotal = {!! json_encode($chartData['questionsTotal']) !!};

    // Chart 2: Beantwortete Fragen
    new Chart(document.getElementById('questionsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Gesamt',
                    data: questionsTotal,
                    borderColor: '#5b9aff',
                    backgroundColor: 'rgba(91, 154, 255, 0.1)',
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
                },
                {
                    label: 'Trend (Gesamt)',
                    data: linearRegression(questionsTotal),
                    borderColor: '#fbbf24',
                    borderWidth: 2,
                    borderDash: [6, 4],
                    fill: false,
                    tension: 0,
                    pointRadius: 0,
                    pointHoverRadius: 0
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
