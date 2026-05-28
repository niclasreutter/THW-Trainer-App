@extends('layouts.app')
@section('title', $leagueInfo['name'] . '-Liga - THW Trainer Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title" style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="color: {{ $leagueInfo['color'] }};"><i class="bi {{ $leagueInfo['icon'] }}"></i></span>
            {{ $leagueInfo['name'] }}<span>-Liga</span>
        </h1>
        <p class="page-subtitle">
            Alle Nutzer der {{ $leagueInfo['name'] }}-Liga
            @if($promotionMinPoints)
                &middot; Aufstieg ab {{ number_format($promotionMinPoints) }} Wochenpunkten
            @endif
        </p>
    </header>

    <!-- Stats Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: {{ $leagueInfo['color'] }};"><i class="bi bi-people"></i></span>
            <div>
                <div class="stat-pill-value">{{ $stats['total'] }}</div>
                <div class="stat-pill-label">Nutzer gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-lightning-charge"></i></span>
            <div>
                <div class="stat-pill-value">{{ $stats['active'] }}</div>
                <div class="stat-pill-label">Aktiv diese Woche</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-trophy"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($stats['total_weekly_points']) }}</div>
                <div class="stat-pill-label">Wochenpunkte gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: var(--thw-blue-light);"><i class="bi bi-bar-chart"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($stats['avg_weekly_points']) }}</div>
                <div class="stat-pill-label">Ø Wochenpunkte</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-dark-secondary"><i class="bi bi-star"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format($stats['avg_total_points']) }}</div>
                <div class="stat-pill-label">Ø Gesamtpunkte</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons & League Navigation -->
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2rem; align-items: center;">
        <a href="{{ route('admin.leagues.index') }}" class="btn-ghost">
            Alle Ligen
        </a>
        @if($previousLeague)
            <a href="{{ route('admin.leagues.show', $previousLeague) }}" class="btn-ghost">
                {{ $leagues[$previousLeague]['name'] }}
            </a>
        @endif
        @if($nextLeague)
            <a href="{{ route('admin.leagues.show', $nextLeague) }}" class="btn-ghost">
                {{ $leagues[$nextLeague]['name'] }}
            </a>
        @endif
    </div>

    @if($promotionMinPoints)
        <div class="glass" style="padding: 1rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="bi bi-info-circle" style="color: var(--thw-blue-light); font-size: 1.1rem;"></i>
            <span style="color: var(--text-secondary); font-size: 0.9rem;">
                Aufstieg in die <strong style="color: {{ $leagues[$nextLeague]['color'] ?? 'var(--text-primary)' }};">{{ $leagues[$nextLeague]['name'] ?? '' }}-Liga</strong>:
                Top {{ \App\Services\LeagueService::PROMOTION_PERCENT }}% (max. {{ \App\Services\LeagueService::MAX_PROMOTION_SLOTS }} Plätze)
                mit mindestens <strong>{{ number_format($promotionMinPoints) }} Wochenpunkten</strong>.
                Mindestens {{ \App\Services\LeagueService::MIN_USERS_FOR_MOVEMENT }} aktive Nutzer erforderlich.
            </span>
        </div>
    @endif

    <!-- User Table -->
    <div class="glass" style="padding: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div class="section-header" style="padding-left: 1rem; border-left: 3px solid {{ $leagueInfo['color'] }};">
                <h2 class="section-title">Nutzer in der {{ $leagueInfo['name'] }}-Liga</h2>
            </div>
            <div style="position: relative; min-width: 220px; max-width: 360px; flex: 1;">
                <i class="bi bi-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem; pointer-events: none;"></i>
                <input type="text" id="league-search" class="input-glass" placeholder="Name suchen..." style="padding-left: 2.5rem; margin: 0;" oninput="filterLeagueUsers(this.value)" />
            </div>
        </div>

        <div id="no-results" class="hidden" style="text-align: center; padding: 2rem; color: var(--text-muted);">
            Keine Nutzer gefunden.
        </div>

        @if($users->isEmpty())
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                <i class="bi bi-people" style="font-size: 2rem; display: block; margin-bottom: 0.75rem;"></i>
                Keine Nutzer in dieser Liga.
            </div>
        @else
            <!-- Desktop Table -->
            <div class="hidden md:block" style="overflow-x: auto;">
                <table class="table-glass">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Name</th>
                            <th>Wochenpunkte</th>
                            <th>Gesamtpunkte</th>
                            <th>Level</th>
                            <th>Streak</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        @php($displayName = $user->leaderboardDisplayName())
                        <tr class="league-user-row" data-name="{{ strtolower($displayName) }}">
                            <td>
                                @if($index < 3 && $user->weekly_points > 0)
                                    <span style="font-weight: 700; color: {{ ['#fbbf24', '#94a3b8', '#cd7f32'][$index] }};">
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    <span style="color: var(--text-muted);">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);">
                                {{ $displayName }}
                                @unless($user->leaderboard_consent)
                                    <span title="Keine Leaderboard-Einwilligung" style="font-size: 0.7rem; color: var(--text-muted); margin-left: 0.4rem;">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>
                                @endunless
                            </td>
                            <td>
                                <span style="font-weight: 600; color: {{ $user->weekly_points > 0 ? 'var(--success)' : 'var(--text-muted)' }};">
                                    {{ number_format($user->weekly_points) }}
                                </span>
                            </td>
                            <td style="color: var(--text-secondary);">
                                {{ number_format($user->points) }}
                            </td>
                            <td>
                                <span class="badge-thw">Lv. {{ $user->level ?? 1 }}</span>
                            </td>
                            <td>
                                @if(($user->streak_days ?? 0) > 0)
                                    <span style="color: var(--gold-start);">{{ $user->streak_days }} Tage</span>
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-ghost btn-sm">
                                    Bearbeiten
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($users as $index => $user)
                @php($displayName = $user->leaderboardDisplayName())
                <div class="glass-subtle league-user-card" data-name="{{ strtolower($displayName) }}" style="padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            @if($index < 3 && $user->weekly_points > 0)
                                <span style="font-weight: 700; color: {{ ['#fbbf24', '#94a3b8', '#cd7f32'][$index] }}; font-size: 1.1rem;">
                                    #{{ $index + 1 }}
                                </span>
                            @else
                                <span style="color: var(--text-muted);">#{{ $index + 1 }}</span>
                            @endif
                            <span style="font-weight: 600; color: var(--text-primary);">{{ $displayName }}</span>
                            @unless($user->leaderboard_consent)
                                <i class="bi bi-shield-lock" title="Keine Leaderboard-Einwilligung" style="font-size: 0.7rem; color: var(--text-muted);"></i>
                            @endunless
                        </div>
                        <span class="badge-thw">Lv. {{ $user->level ?? 1 }}</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">Wochenpunkte</div>
                            <div style="font-weight: 600; color: {{ $user->weekly_points > 0 ? 'var(--success)' : 'var(--text-muted)' }};">
                                {{ number_format($user->weekly_points) }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">Gesamt</div>
                            <div style="color: var(--text-secondary);">{{ number_format($user->points) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">Streak</div>
                            <div style="color: {{ ($user->streak_days ?? 0) > 0 ? 'var(--gold-start)' : 'var(--text-muted)' }};">
                                {{ ($user->streak_days ?? 0) > 0 ? $user->streak_days . ' Tage' : '-' }}
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-ghost btn-sm" style="width: 100%; text-align: center;">
                        Bearbeiten
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    function filterLeagueUsers(query) {
        const q = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.league-user-row');
        const cards = document.querySelectorAll('.league-user-card');
        const noResults = document.getElementById('no-results');
        let found = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const match = !q || name.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) found++;
        });

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const match = !q || name.includes(q);
            card.style.display = match ? '' : 'none';
        });

        noResults.classList.toggle('hidden', found > 0 || !q);
    }
</script>
@endsection
