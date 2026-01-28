@extends('layouts.app')
@section('title', 'Leaderboard - THW Trainer')

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

    /* Tab Navigation */
    .tabs-container {
        margin-bottom: 2rem;
    }

    /* Week Info Banner */
    .week-banner {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        margin-bottom: 2rem;
    }

    .week-banner-icon {
        font-size: 1.5rem;
        color: var(--gold);
    }

    .week-banner-content h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.25rem 0;
    }

    .week-banner-content p {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Leaderboard Table */
    .leaderboard-table-container {
        overflow-x: auto;
    }

    .leaderboard-row {
        display: flex;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        transition: all 0.2s;
    }

    .leaderboard-row:last-child {
        border-bottom: none;
    }

    .leaderboard-row:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .leaderboard-row.rank-1 {
        background: linear-gradient(90deg, rgba(251, 191, 36, 0.15) 0%, transparent 100%);
    }

    .leaderboard-row.rank-2 {
        background: linear-gradient(90deg, rgba(148, 163, 184, 0.12) 0%, transparent 100%);
    }

    .leaderboard-row.rank-3 {
        background: linear-gradient(90deg, rgba(180, 83, 9, 0.12) 0%, transparent 100%);
    }

    .leaderboard-row.current-user {
        background: linear-gradient(90deg, rgba(0, 51, 127, 0.2) 0%, transparent 100%);
        border-left: 3px solid var(--thw-blue);
    }

    /* Rank Column */
    .rank-col {
        min-width: 80px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .rank-medal {
        font-size: 1.5rem;
    }

    .rank-number {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* User Column */
    .user-col {
        flex: 1;
        min-width: 150px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .you-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.5rem;
        background: rgba(0, 51, 127, 0.3);
        color: #60a5fa;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Stats Columns */
    .stat-col {
        min-width: 100px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stat-icon {
        font-size: 1.1rem;
    }

    .stat-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Desktop Table Header */
    .table-header {
        display: flex;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .table-header span {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .table-header .rank-col { min-width: 80px; }
    .table-header .user-col { flex: 1; min-width: 150px; }
    .table-header .stat-col { min-width: 100px; }

    /* Mobile Cards */
    .mobile-cards {
        display: none;
    }

    .mobile-card {
        display: flex;
        gap: 1rem;
        padding: 1.25rem;
        margin-bottom: 0.75rem;
    }

    .mobile-card.rank-1 {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.15) 0%, rgba(251, 191, 36, 0.05) 100%);
        border: 1px solid rgba(251, 191, 36, 0.2);
    }

    .mobile-card.rank-2 {
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.12) 0%, rgba(148, 163, 184, 0.04) 100%);
        border: 1px solid rgba(148, 163, 184, 0.15);
    }

    .mobile-card.rank-3 {
        background: linear-gradient(135deg, rgba(180, 83, 9, 0.12) 0%, rgba(180, 83, 9, 0.04) 100%);
        border: 1px solid rgba(180, 83, 9, 0.15);
    }

    .mobile-card.current-user {
        background: linear-gradient(135deg, rgba(0, 51, 127, 0.2) 0%, rgba(0, 51, 127, 0.08) 100%);
        border: 1px solid rgba(0, 51, 127, 0.3);
        border-left: 3px solid var(--thw-blue);
    }

    .mobile-rank-section {
        flex-shrink: 0;
        text-align: center;
        min-width: 50px;
    }

    .mobile-rank-medal {
        font-size: 1.75rem;
        margin-bottom: 0.25rem;
    }

    .mobile-rank-number {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--thw-blue);
    }

    .mobile-user-section {
        flex: 1;
    }

    .mobile-user-name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .mobile-stats-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .mobile-stat {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
    }

    .mobile-stat-label {
        color: var(--text-muted);
    }

    .mobile-stat-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Info Box */
    .info-box {
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .info-box h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-box ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-box li {
        padding: 0.5rem 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .info-box li i {
        font-size: 1rem;
    }

    /* User Rank Card (not in top 50) */
    .user-rank-card {
        padding: 1.5rem;
        margin-top: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .user-rank-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .user-rank-number {
        font-size: 2rem;
        font-weight: 800;
        background: var(--gradient-gold);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .user-rank-details {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    .user-rank-stats {
        text-align: right;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-state-desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }

        .desktop-table {
            display: none;
        }

        .mobile-cards {
            display: block;
        }

        .tabs-glass {
            flex-direction: column;
        }

        .tab-glass {
            width: 100%;
            text-align: center;
        }

        .user-rank-card {
            flex-direction: column;
            text-align: center;
        }

        .user-rank-stats {
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title"><span>Leaderboard</span></h1>
        <p class="page-subtitle">Zeige dein Können und klettere die Rangliste hinauf</p>
    </header>

    <!-- Tab Navigation -->
    <div class="tabs-container">
        <div class="tabs-glass">
            <a href="{{ route('gamification.leaderboard', ['tab' => 'gesamt']) }}"
               class="tab-glass {{ $tab === 'gesamt' ? 'active' : '' }}" style="text-decoration: none;">
                <i class="bi bi-globe"></i> Gesamt
            </a>
            <a href="{{ route('gamification.leaderboard', ['tab' => 'woche']) }}"
               class="tab-glass {{ $tab === 'woche' ? 'active' : '' }}" style="text-decoration: none;">
                <i class="bi bi-calendar-week"></i> Diese Woche
            </a>
        </div>
    </div>

    @if($tab === 'woche' && $weekRange)
        <div class="glass-accent week-banner">
            <span class="week-banner-icon"><i class="bi bi-calendar-event"></i></span>
            <div class="week-banner-content">
                <h3>Aktuelle Woche</h3>
                <p>{{ $weekRange['formatted'] }} (Montag - Sonntag)</p>
            </div>
        </div>
    @endif

    <!-- Leaderboard Table (Desktop) -->
    <div class="glass desktop-table">
        <div class="table-header">
            <span class="rank-col">Rang</span>
            <span class="user-col">Name</span>
            <span class="stat-col">{{ $tab === 'woche' ? 'Punkte' : 'Punkte' }}</span>
            <span class="stat-col">Level</span>
            <span class="stat-col">Streak</span>
        </div>
        <div class="leaderboard-table-container">
            @forelse($leaderboard as $index => $user)
                @php
                    $rank = $index + 1;
                    $isCurrentUser = Auth::check() && Auth::user()->name === $user->name;
                    $medal = match($rank) {
                        1 => '<i class="bi bi-trophy-fill" style="color: #fbbf24;"></i>',
                        2 => '<i class="bi bi-trophy-fill" style="color: #94a3b8;"></i>',
                        3 => '<i class="bi bi-trophy-fill" style="color: #b45309;"></i>',
                        default => ''
                    };
                    $rowClass = match($rank) {
                        1 => 'rank-1',
                        2 => 'rank-2',
                        3 => 'rank-3',
                        default => ''
                    };
                    if ($isCurrentUser) $rowClass = 'current-user';
                @endphp

                <div class="leaderboard-row {{ $rowClass }}">
                    <div class="rank-col">
                        @if($rank <= 3)
                            <span class="rank-medal">{!! $medal !!}</span>
                        @endif
                        <span class="rank-number">#{{ $rank }}</span>
                    </div>
                    <div class="user-col">
                        <span class="user-name">{{ $user->name }}</span>
                        @if($isCurrentUser)
                            <span class="you-badge"><i class="bi bi-person-fill"></i> Du</span>
                        @endif
                    </div>
                    <div class="stat-col">
                        <span class="stat-icon" style="color: #06b6d4;"><i class="bi bi-gem"></i></span>
                        <span class="stat-value">{{ number_format($tab === 'woche' ? $user->weekly_points : $user->points) }}</span>
                    </div>
                    <div class="stat-col">
                        <span class="stat-icon" style="color: #fbbf24;"><i class="bi bi-star-fill"></i></span>
                        <span class="stat-value">{{ $user->level }}</span>
                    </div>
                    <div class="stat-col">
                        <span class="stat-icon" style="color: #f97316;"><i class="bi bi-fire"></i></span>
                        <span class="stat-value">{{ $user->streak_days }} Tage</span>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-trophy"></i></div>
                    <p class="empty-state-title">{{ $tab === 'woche' ? 'Noch keine Aktivität diese Woche' : 'Noch keine Einträge' }}</p>
                    <p class="empty-state-desc">{{ $tab === 'woche' ? 'Sei der Erste und sammle Punkte!' : '' }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Mobile Cards -->
    <div class="mobile-cards">
        @forelse($leaderboard as $index => $user)
            @php
                $rank = $index + 1;
                $isCurrentUser = Auth::check() && Auth::user()->name === $user->name;
                $medal = match($rank) {
                    1 => '<i class="bi bi-trophy-fill" style="color: #fbbf24;"></i>',
                    2 => '<i class="bi bi-trophy-fill" style="color: #94a3b8;"></i>',
                    3 => '<i class="bi bi-trophy-fill" style="color: #b45309;"></i>',
                    default => ''
                };
                $cardClass = match($rank) {
                    1 => 'rank-1',
                    2 => 'rank-2',
                    3 => 'rank-3',
                    default => ''
                };
                if ($isCurrentUser) $cardClass = 'current-user';
            @endphp

            <div class="glass mobile-card {{ $cardClass }}">
                <div class="mobile-rank-section">
                    @if($rank <= 3)
                        <div class="mobile-rank-medal">{!! $medal !!}</div>
                    @endif
                    <div class="mobile-rank-number">#{{ $rank }}</div>
                </div>
                <div class="mobile-user-section">
                    <div class="mobile-user-name">
                        {{ $user->name }}
                        @if($isCurrentUser)
                            <span class="you-badge"><i class="bi bi-person-fill"></i></span>
                        @endif
                    </div>
                    <div class="mobile-stats-row">
                        <div class="mobile-stat">
                            <span style="color: #06b6d4;"><i class="bi bi-gem"></i></span>
                            <span class="mobile-stat-label">Punkte:</span>
                            <span class="mobile-stat-value">{{ number_format($tab === 'woche' ? $user->weekly_points : $user->points) }}</span>
                        </div>
                    </div>
                    <div class="mobile-stats-row" style="margin-top: 0.5rem;">
                        <div class="mobile-stat">
                            <span style="color: #fbbf24;"><i class="bi bi-star-fill"></i></span>
                            <span class="mobile-stat-label">Level:</span>
                            <span class="mobile-stat-value">{{ $user->level }}</span>
                        </div>
                        <div class="mobile-stat">
                            <span style="color: #f97316;"><i class="bi bi-fire"></i></span>
                            <span class="mobile-stat-label">Streak:</span>
                            <span class="mobile-stat-value">{{ $user->streak_days }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass empty-state">
                <div class="empty-state-icon"><i class="bi bi-trophy"></i></div>
                <p class="empty-state-title">{{ $tab === 'woche' ? 'Noch keine Aktivität diese Woche' : 'Noch keine Einträge' }}</p>
                <p class="empty-state-desc">{{ $tab === 'woche' ? 'Sei der Erste und sammle Punkte!' : '' }}</p>
            </div>
        @endforelse
    </div>

    <!-- Info Box -->
    <div class="glass-tl info-box">
        <h3><i class="bi bi-lightbulb" style="color: var(--gold);"></i> So sammelst du Punkte</h3>
        <ul>
            <li><i class="bi bi-check-circle-fill" style="color: #22c55e;"></i> <strong>+10 Punkte</strong> pro richtig beantwortete Frage</li>
            <li><i class="bi bi-mortarboard" style="color: var(--thw-blue);"></i> <strong>+100 Punkte</strong> für bestandene Prüfungen</li>
            <li><i class="bi bi-fire" style="color: #f97316;"></i> <strong>Streak-Bonus</strong> bei täglichem Lernen</li>
            @if($tab === 'woche')
                <li><i class="bi bi-arrow-repeat" style="color: #8b5cf6;"></i> <strong>Wöchentliche Rangliste</strong> wird jeden Montag zurückgesetzt</li>
            @endif
        </ul>
    </div>

    <!-- Current User Rank (if not in Top 50) -->
    @if(Auth::check())
        @php
            $currentUser = Auth::user();
            $userRank = null;

            if ($tab === 'woche') {
                $userRank = \App\Models\User::where('weekly_points', '>', $currentUser->weekly_points)->count() + 1;
            } else {
                $userRank = \App\Models\User::where('points', '>', $currentUser->points)->count() + 1;
            }

            $isInTop50 = $userRank <= 50;
        @endphp

        @if(!$isInTop50)
            <div class="glass-gold user-rank-card">
                <div>
                    <div class="user-rank-title">
                        <i class="bi bi-geo-alt"></i> Deine Platzierung
                    </div>
                    <div class="user-rank-number">Rang #{{ $userRank }}</div>
                    <div class="user-rank-details">
                        @if($tab === 'woche')
                            {{ number_format($currentUser->weekly_points) }} Punkte diese Woche
                        @else
                            {{ number_format($currentUser->points) }} Punkte gesamt
                        @endif
                    </div>
                </div>
                <div class="user-rank-stats">
                    <div class="user-rank-details">Level {{ $currentUser->level }}</div>
                    <div class="user-rank-details"><i class="bi bi-fire" style="color: #f97316;"></i> {{ $currentUser->streak_days }} Tage Streak</div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
