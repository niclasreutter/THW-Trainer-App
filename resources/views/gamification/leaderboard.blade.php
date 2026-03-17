@extends('layouts.app')
@section('title', 'Leaderboard - THW Trainer')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Layout ──────────────────────────────────── */
    .lb-container {
        max-width: 520px;
        margin: 0 auto;
        padding: 1.25rem 1rem;
    }

    /* ─── Header ──────────────────────────────────── */
    .lb-label {
        font-size: 0.625rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 0.25rem;
        font-family: 'IBM Plex Mono', monospace;
    }

    .lb-title {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.2;
        font-family: 'Barlow Condensed', sans-serif;
        background: linear-gradient(135deg, #5b9aff, #0055cc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.15rem;
    }

    .lb-subtitle {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* ─── Tabs ────────────────────────────────────── */
    .lb-tabs {
        display: flex;
        gap: 0.25rem;
        margin-bottom: 1.25rem;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 0.5rem;
        padding: 0.2rem;
    }

    html.light-mode .lb-tabs {
        background: rgba(0, 0, 0, 0.04);
    }

    .lb-tab {
        flex: 1;
        text-align: center;
        padding: 0.45rem;
        border-radius: 0.375rem;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .lb-tab:hover {
        color: var(--text-primary);
        text-decoration: none;
    }

    .lb-tab.active {
        background: rgba(91, 154, 255, 0.15);
        color: #5b9aff;
        font-weight: 700;
    }

    html.light-mode .lb-tab.active {
        background: rgba(0, 51, 127, 0.1);
        color: #00337F;
    }

    /* ─── Podium ──────────────────────────────────── */
    .lb-podium {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.75rem;
        padding: 1.25rem 0.75rem 1rem;
        margin-bottom: 0.75rem;
        text-align: center;
    }

    .lb-podium__label {
        font-size: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        font-weight: 700;
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 1rem;
    }

    .lb-podium__row {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 0.75rem;
    }

    .lb-podium__user {
        text-align: center;
        flex: 1;
    }

    .lb-podium__avatar-wrap {
        position: relative;
        display: inline-block;
    }

    .lb-podium__avatar {
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.1);
    }

    html.light-mode .lb-podium__avatar {
        border-color: rgba(0, 0, 0, 0.1);
    }

    .lb-podium__badge {
        position: absolute;
        bottom: -2px;
        right: -2px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #fff;
        border: 1.5px solid var(--bg-primary, #0a0e1a);
    }

    html.light-mode .lb-podium__badge {
        border-color: #f8fafc;
    }

    .lb-podium__name {
        font-size: 0.6875rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 0.35rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lb-podium__title {
        font-size: 0.5rem;
        color: var(--text-muted);
        font-style: italic;
        margin-top: 0.1rem;
    }

    .lb-podium__points {
        font-size: 0.625rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    .lb-podium__placeholder {
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-weight: 700;
        margin: 0 auto;
    }

    html.light-mode .lb-podium__placeholder {
        background: rgba(0, 0, 0, 0.06);
    }

    /* ─── Bento Grid ──────────────────────────────── */
    .lb-bento {
        display: grid;
        grid-template-columns: 1fr 120px;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    @media (max-width: 400px) {
        .lb-bento {
            grid-template-columns: 1fr;
        }
    }

    /* ─── Ranking List ────────────────────────────── */
    .lb-list {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.75rem;
        padding: 0.625rem;
    }

    .lb-row {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.375rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }

    html.light-mode .lb-row {
        border-bottom-color: rgba(0, 0, 0, 0.04);
    }

    .lb-row:last-child {
        border-bottom: none;
    }

    .lb-row--current {
        background: rgba(0, 51, 127, 0.2);
        border-left: 2px solid #0055cc;
        border-radius: 0.375rem;
        margin-bottom: 0.25rem;
        border-bottom: none;
    }

    html.light-mode .lb-row--current {
        background: rgba(0, 51, 127, 0.08);
    }

    .lb-row--relegation {
        border-left: 2px solid rgba(239, 68, 68, 0.5);
        border-radius: 0.25rem;
        opacity: 0.75;
    }

    .lb-row__rank {
        font-size: 0.6875rem;
        font-weight: 800;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        min-width: 1.25rem;
    }

    .lb-row--current .lb-row__rank {
        color: #5b9aff;
    }

    html.light-mode .lb-row--current .lb-row__rank {
        color: #00337F;
    }

    .lb-row__avatar {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .lb-row__name {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lb-row--current .lb-row__name {
        color: var(--text-primary);
    }

    .lb-row__points {
        font-size: 0.6875rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    .lb-row--current .lb-row__points {
        color: #fbbf24;
        font-weight: 700;
    }

    /* ─── Zone Divider ────────────────────────────── */
    .lb-zone {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.3rem 0.375rem;
        font-size: 0.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .lb-zone::after {
        content: '';
        flex: 1;
        height: 1px;
    }

    .lb-zone--promotion { color: #22c55e; }
    .lb-zone--promotion::after { background: linear-gradient(90deg, rgba(34, 197, 94, 0.4), transparent); }

    .lb-zone--safe { color: #94a3b8; }
    .lb-zone--safe::after { background: linear-gradient(90deg, rgba(148, 163, 184, 0.25), transparent); }

    .lb-zone--relegation { color: #ef4444; }
    .lb-zone--relegation::after { background: linear-gradient(90deg, rgba(239, 68, 68, 0.4), transparent); }

    /* ─── Side Cards ──────────────────────────────── */
    .lb-side {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .lb-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.75rem;
        padding: 0.625rem;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .lb-card__label {
        font-size: 0.4375rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
        margin-bottom: 0.2rem;
    }

    .lb-card__value {
        font-weight: 800;
        font-family: 'Barlow Condensed', sans-serif;
    }

    .lb-card__bar {
        height: 3px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 0.35rem;
    }

    html.light-mode .lb-card__bar {
        background: rgba(0, 0, 0, 0.06);
    }

    .lb-card__bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        border-radius: 2px;
    }

    /* ─── Info Line ───────────────────────────────── */
    .lb-info {
        text-align: center;
        padding: 0.5rem;
        font-size: 0.625rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Empty State ─────────────────────────────── */
    .lb-empty {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 0.75rem;
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .lb-empty__icon {
        font-size: 2.5rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }

    .lb-empty__title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .lb-empty__desc {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* ─── Stagger Animation ───────────────────────── */
    @keyframes lb-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .lb-container > * {
        animation: lb-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lb-container > *:nth-child(1) { animation-delay: 0.03s; }
    .lb-container > *:nth-child(2) { animation-delay: 0.07s; }
    .lb-container > *:nth-child(3) { animation-delay: 0.11s; }
    .lb-container > *:nth-child(4) { animation-delay: 0.15s; }
    .lb-container > *:nth-child(5) { animation-delay: 0.19s; }
    .lb-container > *:nth-child(6) { animation-delay: 0.23s; }

    @media (prefers-reduced-motion: reduce) {
        .lb-container > * { animation: none !important; }
    }
</style>
@endpush

@section('content')
<div class="dash-container">
<div class="lb-container">

    {{-- ── Header ── --}}
    <div style="margin-bottom:1rem;">
        <p class="lb-label">Rangliste</p>
        @if($tab === 'liga')
            <h1 class="lb-title">{{ $leagueInfo['name'] }}-Liga</h1>
            <p class="lb-subtitle">
                @if($weekRange)
                    {{ $weekRange['formatted'] }}
                @endif
            </p>
        @else
            <h1 class="lb-title">Gesamt-Rangliste</h1>
            <p class="lb-subtitle">Alle Punkte seit Beginn</p>
        @endif
    </div>

    {{-- ── Stat Pills ── --}}
    @php
        $totalUsers = $leaderboard->count();
        $currentUser = Auth::user();
        $userIndex = $leaderboard->search(fn($u) => $u->id === $currentUser->id);
        $userRankInList = $userIndex !== false ? $userIndex + 1 : null;

        $promotionSlots = min(
            \App\Services\LeagueService::getMaxPromotionSlots($userLeague),
            max(1, (int) floor($totalUsers * \App\Services\LeagueService::getPromotionPercent($userLeague) / 100))
        );
        $relegationSlots = min(
            \App\Services\LeagueService::MAX_RELEGATION_SLOTS,
            max(1, (int) floor($totalUsers * \App\Services\LeagueService::RELEGATION_PERCENT / 100))
        );
        $hasNextLeague = $tab === 'liga' && $nextLeague && $totalUsers >= \App\Services\LeagueService::MIN_USERS_FOR_MOVEMENT;
        $hasPrevLeague = $tab === 'liga' && $prevLeague && $totalUsers >= \App\Services\LeagueService::MIN_USERS_FOR_MOVEMENT;
        $relegationStart = $hasPrevLeague && $totalUsers > $relegationSlots ? $totalUsers - $relegationSlots : null;
    @endphp

    <div class="flex gap-2 mb-5" style="flex-wrap:wrap;">
        <div class="gami-pill" style="flex:1;min-width:70px;">
            <div class="gami-pill__value" style="color:#5b9aff;-webkit-text-fill-color:#5b9aff;">{{ $userRankInList ? '#' . $userRankInList : '–' }}</div>
            <div class="gami-pill__label">Dein Rang</div>
        </div>
        <div class="gami-pill" style="flex:1;min-width:70px;">
            <div class="gami-pill__value" style="color:#fbbf24;-webkit-text-fill-color:#fbbf24;">{{ number_format($tab === 'liga' ? ($currentUser->weekly_points ?? 0) : ($currentUser->points + ($currentUser->total_points_spent ?? 0))) }}</div>
            <div class="gami-pill__label">{{ $tab === 'liga' ? 'Wochen-Pkt' : 'Gesamt-Pkt' }}</div>
        </div>
        <div class="gami-pill" style="flex:1;min-width:70px;">
            <div class="gami-pill__value" style="color:#f97316;-webkit-text-fill-color:#f97316;">{{ $totalUsers }}</div>
            <div class="gami-pill__label">Spieler</div>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="lb-tabs">
        <a href="{{ route('gamification.leaderboard', ['tab' => 'liga']) }}" class="lb-tab {{ $tab === 'liga' ? 'active' : '' }}">Liga</a>
        <a href="{{ route('gamification.leaderboard', ['tab' => 'gesamt']) }}" class="lb-tab {{ $tab === 'gesamt' ? 'active' : '' }}">Gesamt</a>
    </div>

    @if($totalUsers === 0)
        {{-- ── Empty State ── --}}
        <div class="lb-empty">
            <div class="lb-empty__icon"><i class="bi bi-trophy"></i></div>
            <p class="lb-empty__title">{{ $tab === 'liga' ? 'Noch keine Aktivität in deiner Liga' : 'Noch keine Einträge' }}</p>
            <p class="lb-empty__desc">{{ $tab === 'liga' ? 'Sei der Erste und sammle Punkte!' : '' }}</p>
        </div>
    @else
        {{-- ── Podium (Top 3) ── --}}
        @php
            $top3 = $leaderboard->take(3);
            $rest = $leaderboard->slice(3);

            // Helper: get avatar frame inline styles
            $getAvatarFrameStyle = function($user) {
                $hasFrame = $user->profile_frame_until && \Carbon\Carbon::parse($user->profile_frame_until)->isFuture();
                if (!$hasFrame) return 'border:2px solid rgba(255,255,255,0.1);';
                $type = $user->profile_frame_type ?? 'gold';
                if ($type === 'diamond') {
                    return 'border:2px solid rgba(147,197,253,0.7);box-shadow:0 0 10px rgba(147,197,253,0.35),0 0 20px rgba(167,139,250,0.2);';
                }
                return 'border:2px solid rgba(251,191,36,0.6);box-shadow:0 0 8px rgba(251,191,36,0.25);';
            };

            // Helper: get name CSS classes
            $getNameClasses = function($user) {
                $classes = '';
                $hasGlow = $user->glowing_name_until && \Carbon\Carbon::parse($user->glowing_name_until)->isFuture();
                $hasRankColor = $user->rank_color_until && \Carbon\Carbon::parse($user->rank_color_until)->isFuture() && $user->rank_color;
                if ($hasGlow) {
                    $classes .= $user->glowing_name_type === 'shimmer' ? ' glowing-name-shimmer' : ' glowing-name-static';
                }
                if ($hasRankColor) {
                    $classes .= ' rank-color-' . $user->rank_color;
                }
                return $classes;
            };

            // Helper: has active title
            $hasActiveTitle = function($user) {
                return $user->active_title_until && \Carbon\Carbon::parse($user->active_title_until)->isFuture() && $user->active_title;
            };
        @endphp

        <div class="lb-podium">
            <p class="lb-podium__label">Top 3</p>
            <div class="lb-podium__row">
                @php
                    // Order: 2nd, 1st, 3rd
                    $podiumOrder = [1, 0, 2];
                    $podiumSizes = [44, 58, 44];
                    $badgeSizes = [16, 18, 16];
                    $badgeFontSizes = ['0.5rem', '0.5625rem', '0.5rem'];
                    $badgeColors = ['#94a3b8', 'linear-gradient(135deg,#fbbf24,#f59e0b)', '#b45309'];
                    $nameFirst = ['0.6875rem', '0.75rem', '0.6875rem'];
                    $pointColors = ['#94a3b8', '#f59e0b', '#b45309'];
                    $marginBottom = ['0', '0.75rem', '0'];
                @endphp

                @foreach($podiumOrder as $pi)
                    <div class="lb-podium__user" style="margin-bottom:{{ $marginBottom[$pi] }};">
                        @if(isset($top3[$pi]))
                            @php $user = $top3[$pi]; @endphp
                            <div class="lb-podium__avatar-wrap">
                                <img src="{{ $user->avatar_url }}" class="lb-podium__avatar" style="width:{{ $podiumSizes[$pi] }}px;height:{{ $podiumSizes[$pi] }}px;{{ $getAvatarFrameStyle($user) }}" alt="" />
                                <div class="lb-podium__badge" style="width:{{ $badgeSizes[$pi] }}px;height:{{ $badgeSizes[$pi] }}px;font-size:{{ $badgeFontSizes[$pi] }};background:{{ $badgeColors[$pi] }};{{ $pi === 0 ? 'box-shadow:0 0 8px rgba(251,191,36,0.3);color:#1a1a2e;' : '' }}">{{ $pi + 1 }}</div>
                            </div>
                            <div class="lb-podium__name{{ $getNameClasses($user) }}" style="font-size:{{ $nameFirst[$pi] }};">{{ $user->name }}</div>
                            @if($hasActiveTitle($user))
                                <div class="lb-podium__title">{{ $user->active_title }}</div>
                            @endif
                            <div class="lb-podium__points" style="color:{{ $pointColors[$pi] }};">{{ number_format($tab === 'liga' ? $user->weekly_points : ($user->total_points_earned ?? $user->points)) }}</div>
                        @else
                            {{-- Placeholder for missing user --}}
                            <div class="lb-podium__placeholder" style="width:{{ $podiumSizes[$pi] }}px;height:{{ $podiumSizes[$pi] }}px;font-size:0.75rem;">?</div>
                            <div class="lb-podium__name" style="font-size:{{ $nameFirst[$pi] }};color:var(--text-muted);">–</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Bento Grid (Ranking List + Side Cards) ── --}}
        @if($rest->count() > 0 || $tab === 'gesamt')
        <div class="lb-bento">
            {{-- Left: Ranking List --}}
            <div class="lb-list">
                @if($tab === 'liga' && $hasNextLeague && $promotionSlots <= 3 && $totalUsers > 3)
                    <div class="lb-zone lb-zone--safe">
                        <span>Verbleibszone</span>
                    </div>
                @endif

                @foreach($rest as $index => $user)
                    @php
                        $rank = $index + 4;
                        $isCurrentUser = $user->id === $currentUser->id;

                        $rowClass = 'lb-row';
                        if ($isCurrentUser) $rowClass .= ' lb-row--current';

                        if ($tab === 'liga') {
                            if ($hasPrevLeague && $relegationStart && $rank > $relegationStart) {
                                $rowClass .= ' lb-row--relegation';
                            }
                        }
                    @endphp

                    @if($tab === 'liga' && $hasNextLeague && $rank === $promotionSlots + 1 && $promotionSlots > 3)
                        <div class="lb-zone lb-zone--safe">
                            <span>Verbleibszone</span>
                        </div>
                    @endif

                    @if($tab === 'liga' && $hasPrevLeague && $relegationStart && $rank === $relegationStart + 1)
                        <div class="lb-zone lb-zone--relegation">
                            <span>Abstieg</span>
                        </div>
                    @endif

                    <div class="{{ $rowClass }}">
                        <span class="lb-row__rank">{{ $rank }}</span>
                        <img src="{{ $user->avatar_url }}" class="lb-row__avatar" style="{{ $getAvatarFrameStyle($user) }}" alt="" />
                        <span class="lb-row__name{{ $getNameClasses($user) }}">{{ $isCurrentUser ? 'Du' : $user->name }}</span>
                        <span class="lb-row__points">{{ number_format($tab === 'liga' ? $user->weekly_points : ($user->total_points_earned ?? $user->points)) }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Right: Side Cards --}}
            <div class="lb-side">
                {{-- Global Rank Card --}}
                <div class="lb-card">
                    <div class="lb-card__label">Gesamt</div>
                    @php
                        if ($tab === 'gesamt') {
                            $globalRank = $userRankInList ? '#' . $userRankInList : '#' . (\App\Models\User::whereRaw('(points + COALESCE(total_points_spent, 0)) > ?', [$currentUser->points + ($currentUser->total_points_spent ?? 0)])->count() + 1);
                        } else {
                            $totalEarned = $currentUser->points + ($currentUser->total_points_spent ?? 0);
                            $globalRank = '#' . (\App\Models\User::whereRaw('(points + COALESCE(total_points_spent, 0)) > ?', [$totalEarned])->count() + 1);
                        }
                    @endphp
                    <div class="lb-card__value" style="font-size:1.5rem;color:#5b9aff;">{{ $globalRank }}</div>
                </div>

                @if($tab === 'liga' && $hasNextLeague)
                    {{-- Promotion Progress Card --}}
                    @php
                        $promotionUser = $leaderboard[$promotionSlots - 1] ?? null;
                        $promotionThreshold = $promotionUser ? ($tab === 'liga' ? $promotionUser->weekly_points : 0) : 0;
                        $minPoints = \App\Services\LeagueService::PROMOTION_MIN_POINTS[$userLeague] ?? 0;
                        $target = max($promotionThreshold, $minPoints);
                        $userPoints = $currentUser->weekly_points ?? 0;
                        $remaining = max(0, $target - $userPoints);
                        $progress = $target > 0 ? min(100, round(($userPoints / $target) * 100)) : 0;
                    @endphp
                    <div class="lb-card">
                        <div class="lb-card__label">Aufstieg</div>
                        @if($remaining > 0)
                            <div class="lb-card__value" style="font-size:1rem;color:#22c55e;">{{ number_format($remaining) }} Pkt</div>
                        @else
                            <div class="lb-card__value" style="font-size:0.75rem;color:#22c55e;">Aufstieg!</div>
                        @endif
                        <div class="lb-card__bar">
                            <div class="lb-card__bar-fill" style="width:{{ $progress }}%;"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── Info Line ── --}}
        @if($tab === 'liga' && ($hasNextLeague || $hasPrevLeague))
            <div class="lb-info">
                @if($hasNextLeague)Top {{ $promotionSlots }} steigen auf@endif
                @if($hasNextLeague && $hasPrevLeague) · @endif
                @if($hasPrevLeague)Letzte {{ $relegationSlots }} steigen ab@endif
            </div>
        @endif
    @endif

</div>
</div>
@endsection
