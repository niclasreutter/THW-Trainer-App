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

    /* League Banner */
    .league-banner {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
    }

    .league-banner-icon {
        font-size: 2.5rem;
        flex-shrink: 0;
    }

    .league-banner-content h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.25rem 0;
    }

    .league-banner-content p {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .league-badge-inline {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid;
    }

    /* Promotion/Relegation Zone Markers */
    .zone-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1.25rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .zone-divider::after {
        content: '';
        flex: 1;
        height: 1px;
    }

    .zone-divider.promotion {
        color: #22c55e;
    }

    .zone-divider.promotion::after {
        background: linear-gradient(90deg, rgba(34, 197, 94, 0.4), transparent);
    }

    .zone-divider.relegation {
        color: #ef4444;
    }

    .zone-divider.relegation::after {
        background: linear-gradient(90deg, rgba(239, 68, 68, 0.4), transparent);
    }

    .promotion-row {
        border-left: 3px solid rgba(34, 197, 94, 0.5);
    }

    .relegation-row {
        border-left: 3px solid rgba(239, 68, 68, 0.5);
        opacity: 0.85;
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

    .mobile-card.promotion-row {
        border-left: 3px solid rgba(34, 197, 94, 0.5);
    }

    .mobile-card.relegation-row {
        border-left: 3px solid rgba(239, 68, 68, 0.5);
        opacity: 0.85;
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

    /* User Rank Card (not in top list) */
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

    /* Profilrahmen auf Desktop-Zeilen */
    .leaderboard-row.profile-frame-gold,
    .leaderboard-row.profile-frame-diamond {
        margin: 0.25rem 0.5rem;
        border-radius: 0.75rem;
    }

    /* Profilrahmen auf Mobile-Karten */
    .mobile-card.profile-frame-gold,
    .mobile-card.profile-frame-diamond {
        border-radius: 1rem;
    }

    /* Lootbox indicator */
    .lootbox-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--gold);
        background: rgba(251, 191, 36, 0.1);
        border: 1px solid rgba(251, 191, 36, 0.2);
        transition: all 0.2s;
    }

    .lootbox-link:hover {
        background: rgba(251, 191, 36, 0.15);
        border-color: rgba(251, 191, 36, 0.3);
        transform: translateY(-1px);
    }

    .lootbox-count {
        background: var(--gold);
        color: #000;
        padding: 0.1rem 0.4rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 800;
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

        .league-banner {
            flex-direction: column;
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

    @if($unopenedLootboxCount > 0)
        <a href="{{ route('gamification.lootboxes') }}" class="lootbox-link" style="margin-bottom: 1.5rem; display: inline-flex;">
            <i class="bi bi-gift-fill"></i> Lootboxen öffnen
            <span class="lootbox-count">{{ $unopenedLootboxCount }}</span>
        </a>
    @endif

    <!-- Tab Navigation -->
    <div class="tabs-container">
        <div class="tabs-glass">
            <a href="{{ route('gamification.leaderboard', ['tab' => 'liga']) }}"
               class="tab-glass {{ $tab === 'liga' ? 'active' : '' }}" style="text-decoration: none;">
                <i class="bi bi-shield-fill"></i> Liga
            </a>
            <a href="{{ route('gamification.leaderboard', ['tab' => 'gesamt']) }}"
               class="tab-glass {{ $tab === 'gesamt' ? 'active' : '' }}" style="text-decoration: none;">
                <i class="bi bi-globe"></i> Gesamt
            </a>
        </div>
    </div>

    @if($tab === 'liga')
        <!-- League Info Banner -->
        <div class="glass-accent league-banner">
            <span class="league-banner-icon"><i class="{{ $leagueInfo['icon'] }}" style="color: {{ $leagueInfo['color'] }};"></i></span>
            <div class="league-banner-content">
                <h3>{{ $leagueInfo['name'] }}-Liga</h3>
                <p>
                    @if($weekRange)
                        {{ $weekRange['formatted'] }} (Montag - Sonntag)
                    @endif
                    @if($nextLeague)
                        &middot; Top {{ \App\Services\LeagueService::PROMOTION_SLOTS }} steigen auf in {{ \App\Services\LeagueService::getLeagueName($nextLeague) }}
                    @endif
                </p>
            </div>
        </div>
    @endif

    @php
        $totalUsers = $leaderboard->count();
        $promotionSlots = \App\Services\LeagueService::PROMOTION_SLOTS;
        $relegationSlots = \App\Services\LeagueService::RELEGATION_SLOTS;
        $hasNextLeague = $tab === 'liga' && $nextLeague;
        $hasPrevLeague = $tab === 'liga' && $prevLeague;
        $relegationStart = $hasPrevLeague && $totalUsers > $relegationSlots ? $totalUsers - $relegationSlots : null;
    @endphp

    <!-- Leaderboard Table (Desktop) -->
    <div class="glass desktop-table">
        <div class="table-header">
            <span class="rank-col">Rang</span>
            <span class="user-col">Name</span>
            <span class="stat-col">{{ $tab === 'liga' ? 'Wochen-Punkte' : 'Punkte' }}</span>
            <span class="stat-col">Level</span>
            <span class="stat-col">Streak</span>
        </div>
        <div class="leaderboard-table-container">
            @if($hasNextLeague && $totalUsers > 0)
                <div class="zone-divider promotion">
                    <i class="bi bi-arrow-up-circle-fill"></i>
                    Aufstiegszone &rarr; {{ \App\Services\LeagueService::getLeagueName($nextLeague) }}
                </div>
            @endif

            @forelse($leaderboard as $index => $user)
                @php
                    $rank = $index + 1;
                    $isCurrentUser = Auth::check() && Auth::user()->id === $user->id;
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

                    // Zone classes
                    $zoneClass = '';
                    if ($tab === 'liga') {
                        if ($hasNextLeague && $rank <= $promotionSlots) {
                            $zoneClass = 'promotion-row';
                        }
                        if ($hasPrevLeague && $relegationStart && $rank > $relegationStart) {
                            $zoneClass = 'relegation-row';
                        }
                    }
                @endphp

                @if($tab === 'liga' && $hasPrevLeague && $relegationStart && $rank === $relegationStart + 1)
                    <div class="zone-divider relegation">
                        <i class="bi bi-arrow-down-circle-fill"></i>
                        Abstiegszone &rarr; {{ \App\Services\LeagueService::getLeagueName($prevLeague) }}
                    </div>
                @endif

                @if($tab === 'liga' && $hasNextLeague && $rank === $promotionSlots + 1 && $totalUsers > $promotionSlots)
                    <div style="height: 1px; background: rgba(255,255,255,0.06); margin: 0.25rem 0;"></div>
                @endif

                @php
                    $nameClasses = 'user-name';
                    $hasFrame = $user->profile_frame_until && \Carbon\Carbon::parse($user->profile_frame_until)->isFuture();
                    $hasGlow = $user->glowing_name_until && \Carbon\Carbon::parse($user->glowing_name_until)->isFuture();
                    $hasRankColor = $user->rank_color_until && \Carbon\Carbon::parse($user->rank_color_until)->isFuture() && $user->rank_color;
                    $hasTitle = $user->active_title_until && \Carbon\Carbon::parse($user->active_title_until)->isFuture() && $user->active_title;

                    if ($hasGlow) {
                        $nameClasses .= $user->glowing_name_type === 'shimmer' ? ' glowing-name-shimmer' : ' glowing-name-static';
                    }
                    if ($hasRankColor) {
                        $nameClasses .= ' rank-color-' . $user->rank_color;
                    }
                    $frameClass = '';
                    if ($hasFrame) {
                        $frameClass = ($user->profile_frame_type ?? 'gold') === 'diamond' ? 'profile-frame-diamond' : 'profile-frame-gold';
                    }
                @endphp
                <div class="leaderboard-row {{ $rowClass }} {{ $zoneClass }} {{ $frameClass }}">
                    <div class="rank-col">
                        @if($rank <= 3)
                            <span class="rank-medal">{!! $medal !!}</span>
                        @endif
                        <span class="rank-number">#{{ $rank }}</span>
                    </div>
                    <div class="user-col">
                        <div>
                            <span class="{{ $nameClasses }}">{{ $user->name }}</span>
                            @if($hasTitle)
                                <div class="user-title">{{ $user->active_title }}</div>
                            @endif
                        </div>
                        @if($isCurrentUser)
                            <span class="you-badge"><i class="bi bi-person-fill"></i> Du</span>
                        @endif
                    </div>
                    <div class="stat-col">
                        <span class="stat-icon" style="color: #06b6d4;"><i class="bi bi-gem"></i></span>
                        <span class="stat-value">{{ number_format($tab === 'liga' ? $user->weekly_points : ($user->total_points_earned ?? $user->points)) }}</span>
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
                    <p class="empty-state-title">{{ $tab === 'liga' ? 'Noch keine Aktivität in deiner Liga diese Woche' : 'Noch keine Einträge' }}</p>
                    <p class="empty-state-desc">{{ $tab === 'liga' ? 'Sei der Erste und sammle Punkte!' : '' }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Mobile Cards -->
    <div class="mobile-cards">
        @if($hasNextLeague && $totalUsers > 0)
            <div class="zone-divider promotion">
                <i class="bi bi-arrow-up-circle-fill"></i>
                Aufstiegszone
            </div>
        @endif

        @forelse($leaderboard as $index => $user)
            @php
                $rank = $index + 1;
                $isCurrentUser = Auth::check() && Auth::user()->id === $user->id;
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

                $mZoneClass = '';
                if ($tab === 'liga') {
                    if ($hasNextLeague && $rank <= $promotionSlots) {
                        $mZoneClass = 'promotion-row';
                    }
                    if ($hasPrevLeague && $relegationStart && $rank > $relegationStart) {
                        $mZoneClass = 'relegation-row';
                    }
                }
            @endphp

            @if($tab === 'liga' && $hasPrevLeague && $relegationStart && $rank === $relegationStart + 1)
                <div class="zone-divider relegation">
                    <i class="bi bi-arrow-down-circle-fill"></i>
                    Abstiegszone
                </div>
            @endif

            @if($tab === 'liga' && $hasNextLeague && $rank === $promotionSlots + 1 && $totalUsers > $promotionSlots)
                <div style="height: 1px; background: rgba(255,255,255,0.06); margin: 0.5rem 0;"></div>
            @endif

            @php
                $mNameClasses = 'mobile-user-name';
                $mHasFrame = $user->profile_frame_until && \Carbon\Carbon::parse($user->profile_frame_until)->isFuture();
                $mHasGlow = $user->glowing_name_until && \Carbon\Carbon::parse($user->glowing_name_until)->isFuture();
                $mHasRankColor = $user->rank_color_until && \Carbon\Carbon::parse($user->rank_color_until)->isFuture() && $user->rank_color;
                $mHasTitle = $user->active_title_until && \Carbon\Carbon::parse($user->active_title_until)->isFuture() && $user->active_title;
                $mFrameClass = '';
                if ($mHasFrame) {
                    $mFrameClass = ($user->profile_frame_type ?? 'gold') === 'diamond' ? 'profile-frame-diamond' : 'profile-frame-gold';
                }
            @endphp
            <div class="glass mobile-card {{ $cardClass }} {{ $mZoneClass }} {{ $mFrameClass }}">
                <div class="mobile-rank-section">
                    @if($rank <= 3)
                        <div class="mobile-rank-medal">{!! $medal !!}</div>
                    @endif
                    <div class="mobile-rank-number">#{{ $rank }}</div>
                </div>
                <div class="mobile-user-section">
                    <div class="{{ $mNameClasses }}">
                        <span class="{{ $mHasGlow ? ($user->glowing_name_type === 'shimmer' ? 'glowing-name-shimmer' : 'glowing-name-static') : '' }} {{ $mHasRankColor ? 'rank-color-' . $user->rank_color : '' }}">{{ $user->name }}</span>
                        @if($isCurrentUser)
                            <span class="you-badge"><i class="bi bi-person-fill"></i></span>
                        @endif
                    </div>
                    @if($mHasTitle)
                        <div class="user-title" style="margin-bottom: 0.5rem;">{{ $user->active_title }}</div>
                    @endif
                    <div class="mobile-stats-row">
                        <div class="mobile-stat">
                            <span style="color: #06b6d4;"><i class="bi bi-gem"></i></span>
                            <span class="mobile-stat-label">Punkte:</span>
                            <span class="mobile-stat-value">{{ number_format($tab === 'liga' ? $user->weekly_points : ($user->total_points_earned ?? $user->points)) }}</span>
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
                <p class="empty-state-title">{{ $tab === 'liga' ? 'Noch keine Aktivität in deiner Liga' : 'Noch keine Einträge' }}</p>
                <p class="empty-state-desc">{{ $tab === 'liga' ? 'Sei der Erste und sammle Punkte!' : '' }}</p>
            </div>
        @endforelse
    </div>

    <!-- Info Box -->
    <div class="glass-tl info-box">
        <h3><i class="bi bi-lightbulb" style="color: var(--gold);"></i> So funktioniert das Ligen-System</h3>
        <ul>
            <li><i class="bi bi-shield-fill" style="color: var(--gold);"></i> <strong>8 Ligen</strong> von Bronze bis Diamant</li>
            <li><i class="bi bi-arrow-up-circle-fill" style="color: #22c55e;"></i> <strong>Top {{ $promotionSlots }}</strong> steigen wöchentlich in die nächste Liga auf</li>
            <li><i class="bi bi-arrow-down-circle-fill" style="color: #ef4444;"></i> <strong>Letzte {{ $relegationSlots }}</strong> steigen in die untere Liga ab</li>
            <li><i class="bi bi-gift-fill" style="color: #c084fc;"></i> <strong>Platz 1-3</strong> erhalten jede Woche eine Lootbox</li>
            @if($tab === 'liga')
                <li><i class="bi bi-arrow-repeat" style="color: #8b5cf6;"></i> <strong>Liga-Wertung</strong> wird jeden Montag zurückgesetzt</li>
            @endif
        </ul>
    </div>

    <!-- Current User Rank (if not visible in league) -->
    @if(Auth::check() && $tab === 'gesamt')
        @php
            $currentUser = Auth::user();
            $totalEarned = $currentUser->points + ($currentUser->total_points_spent ?? 0);
            $userRank = \App\Models\User::whereRaw('(points + COALESCE(total_points_spent, 0)) > ?', [$totalEarned])->count() + 1;
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
                        {{ number_format($currentUser->points + ($currentUser->total_points_spent ?? 0)) }} Punkte gesamt
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
