@extends('layouts.app')
@section('title', 'Ligen - THW Trainer')

@push('styles')
<style>
    .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
    .dashboard-header { margin-bottom: 2.5rem; padding-top: 1rem; max-width: 600px; }

    .league-tier-bar {
        display: flex; gap: 0.5rem; overflow-x: auto; padding: 0.5rem 0; margin-bottom: 2rem;
        scrollbar-width: none; -ms-overflow-style: none;
    }
    .league-tier-bar::-webkit-scrollbar { display: none; }

    .league-tier-pill {
        display: flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;
        border-radius: 2rem; font-size: 0.8rem; font-weight: 600; white-space: nowrap;
        text-decoration: none; transition: all 0.2s; cursor: pointer;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        color: var(--text-secondary);
    }
    .league-tier-pill:hover { background: rgba(255,255,255,0.1); }
    .league-tier-pill.active { border-color: var(--league-color); background: rgba(255,255,255,0.08); color: var(--text-primary); }

    .league-hero { text-align: center; padding: 2rem; margin-bottom: 2rem; }
    .league-hero-icon { font-size: 3rem; margin-bottom: 0.75rem; }
    .league-hero-name { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem; }
    .league-hero-rank { font-size: 0.85rem; color: var(--text-secondary); }

    .league-row {
        display: flex; align-items: center; gap: 1rem; padding: 0.75rem 1rem;
        border-radius: 0.75rem; margin-bottom: 0.5rem; transition: background 0.2s;
        background: rgba(255,255,255,0.03);
    }
    .league-row:hover { background: rgba(255,255,255,0.06); }
    .league-row.promo-zone { border-left: 3px solid var(--success); }
    .league-row.demo-zone { border-left: 3px solid var(--error); }
    .league-row.is-user { background: rgba(251, 191, 36, 0.08); border: 1px solid rgba(251, 191, 36, 0.2); }

    .league-rank { font-size: 0.85rem; font-weight: 700; color: var(--text-muted); width: 2rem; text-align: center; }
    .league-name { flex: 1; font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }
    .league-points { font-size: 0.85rem; font-weight: 700; color: var(--gold); }

    .zone-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.5rem 1rem; color: var(--text-muted); }

    @media (max-width: 768px) {
        .dashboard-container { padding: 1rem; }
        .league-hero { padding: 1.5rem; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Liga <span>Rangliste</span></h1>
        <p class="page-subtitle">Sammle wochentlich Punkte und steige in hohere Ligen auf</p>
    </header>

    <!-- Current League Hero -->
    <div class="glass-gold league-hero" style="border-radius: 1.5rem 0.5rem 1.5rem 1.5rem;">
        <div class="league-hero-icon" style="color: {{ $userLeagueInfo['color'] }};">
            <i class="bi {{ $userLeagueInfo['icon'] }}"></i>
        </div>
        <div class="league-hero-name" style="color: {{ $userLeagueInfo['color'] }};">{{ $userLeagueInfo['label'] }}-Liga</div>
        <div class="league-hero-rank">
            Rang {{ $userLeagueInfo['rank'] }}
            @if($userLeagueInfo['total_in_league'] > 0) von {{ $userLeagueInfo['total_in_league'] }} @endif
            @if($userLeagueInfo['promo_zone']) <span style="color: var(--success);">&middot; Aufstiegszone</span> @endif
            @if($userLeagueInfo['demo_zone']) <span style="color: var(--error);">&middot; Abstiegszone</span> @endif
        </div>
    </div>

    <!-- League Tier Bar -->
    <div class="league-tier-bar">
        @foreach($leagues as $league)
            @php $cfg = $leagueConfig[$league]; @endphp
            <a href="{{ route('gamification.leagues', ['league' => $league]) }}"
               class="league-tier-pill {{ $selectedLeague === $league ? 'active' : '' }}"
               style="--league-color: {{ $cfg['color'] }};">
                <i class="bi {{ $cfg['icon'] }}" style="color: {{ $cfg['color'] }};"></i>
                {{ \App\Services\LeagueService::LEAGUE_LABELS[$league] }}
            </a>
        @endforeach
    </div>

    <!-- Leaderboard -->
    <div class="glass" style="padding: 1.5rem; border-radius: 1rem;">
        @php
            $config = $leagueConfig[$selectedLeague];
            $promoteTop = $config['promote_top'];
            $demoteBottom = $config['demote_bottom'];
            $total = $leaderboard->count();
        @endphp

        @if($leaderboard->isEmpty())
            <p style="text-align: center; color: var(--text-muted); padding: 2rem;">Noch keine aktiven Spieler in dieser Liga diese Woche.</p>
        @else
            @if($promoteTop > 0 && $selectedLeague !== 'diamant')
                <div class="zone-label" style="color: var(--success);"><i class="bi bi-arrow-up-short"></i> Aufstiegszone</div>
            @endif

            @foreach($leaderboard as $index => $player)
                @php
                    $rank = $index + 1;
                    $inPromoZone = $promoteTop > 0 && $rank <= $promoteTop && $selectedLeague !== 'diamant';
                    $inDemoZone = $demoteBottom > 0 && $rank > ($total - $demoteBottom) && $selectedLeague !== 'bronze';
                    $isUser = $player->id === auth()->id();
                @endphp

                @if($inDemoZone && ($index === 0 || !($demoteBottom > 0 && $index > ($total - $demoteBottom - 1))))
                    <div class="zone-label" style="color: var(--error); margin-top: 1rem;"><i class="bi bi-arrow-down-short"></i> Abstiegszone</div>
                @endif

                <div class="league-row {{ $inPromoZone ? 'promo-zone' : '' }} {{ $inDemoZone ? 'demo-zone' : '' }} {{ $isUser ? 'is-user' : '' }}">
                    <span class="league-rank">{{ $rank }}</span>
                    <span class="league-name">
                        {{ $player->name }}
                        @if($isUser) <span style="font-size: 0.7rem; color: var(--gold);">(Du)</span> @endif
                    </span>
                    <span class="league-points">{{ number_format($player->weekly_points) }} XP</span>
                </div>
            @endforeach
        @endif
    </div>

    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('gamification.leaderboard') }}" class="btn-ghost btn-sm">Zum Leaderboard</a>
    </div>
</div>
@endsection
