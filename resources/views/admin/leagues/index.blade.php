@extends('layouts.app')
@section('title', 'Ligen-Übersicht - THW Trainer Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Ligen<span>übersicht</span></h1>
        <p class="page-subtitle">Alle Ligen und ihre Teilnehmer im Überblick</p>
    </header>

    <!-- Stats Pills -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold"><i class="bi bi-shield-fill"></i></span>
            <div>
                <div class="stat-pill-value">{{ array_sum(array_column($leagueStats, 'total_users')) }}</div>
                <div class="stat-pill-label">Nutzer gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success"><i class="bi bi-lightning-charge"></i></span>
            <div>
                <div class="stat-pill-value">{{ array_sum(array_column($leagueStats, 'active_users')) }}</div>
                <div class="stat-pill-label">Aktive diese Woche</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon" style="color: var(--thw-blue-light);"><i class="bi bi-trophy"></i></span>
            <div>
                <div class="stat-pill-value">{{ number_format(array_sum(array_column($leagueStats, 'total_weekly_points'))) }}</div>
                <div class="stat-pill-label">Wochenpunkte gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-dark-secondary"><i class="bi bi-layers"></i></span>
            <div>
                <div class="stat-pill-value">{{ count($leagueStats) }}</div>
                <div class="stat-pill-label">Ligen</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2rem;">
        <a href="{{ route('admin.dashboard') }}" class="btn-ghost">
            Zum Admin Dashboard
        </a>
    </div>

    <!-- League Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        @foreach($leagueStats as $key => $league)
            <a href="{{ route('admin.leagues.show', $key) }}"
               class="glass" style="padding: 1.5rem; text-decoration: none; transition: transform 0.2s ease, box-shadow 0.2s ease; display: block; cursor: pointer;"
               onmouseenter="this.style.transform='translateY(-2px)'"
               onmouseleave="this.style.transform='translateY(0)'">

                <!-- League Header -->
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: {{ $league['info']['color'] }}20; color: {{ $league['info']['color'] }};">
                        <i class="bi {{ $league['info']['icon'] }}"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: {{ $league['info']['color'] }};">
                            {{ $league['info']['name'] }}
                        </h3>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">
                            Liga {{ $league['info']['order'] }} von {{ count($leagueStats) }}
                        </span>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                    <div style="padding: 0.75rem; border-radius: 8px; background: var(--glass-bg);">
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">{{ $league['total_users'] }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Nutzer</div>
                    </div>
                    <div style="padding: 0.75rem; border-radius: 8px; background: var(--glass-bg);">
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--success);">{{ $league['active_users'] }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Aktiv</div>
                    </div>
                    <div style="padding: 0.75rem; border-radius: 8px; background: var(--glass-bg);">
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">{{ number_format($league['total_weekly_points']) }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Wochenpunkte</div>
                    </div>
                    <div style="padding: 0.75rem; border-radius: 8px; background: var(--glass-bg);">
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-secondary);">{{ number_format($league['avg_weekly_points']) }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Ø Punkte</div>
                    </div>
                </div>

                <!-- Top User -->
                @if($league['top_user'])
                    <div style="padding: 0.75rem; border-radius: 8px; background: var(--glass-bg); display: flex; align-items: center; gap: 0.75rem;">
                        <i class="bi bi-trophy-fill" style="color: {{ $league['info']['color'] }};"></i>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">{{ $league['top_user']->leaderboardDisplayName() }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ number_format($league['top_user']->weekly_points) }} Wochenpunkte</div>
                        </div>
                    </div>
                @else
                    <div style="padding: 0.75rem; border-radius: 8px; background: var(--glass-bg); text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                        Keine aktiven Nutzer
                    </div>
                @endif
            </a>
        @endforeach
    </div>
</div>
@endsection
