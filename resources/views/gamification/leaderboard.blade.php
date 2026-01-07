@extends('layouts.app')
@section('title', 'Leaderboard - THW Trainer')

@push('styles')
<style>
.leaderboard-page { background: #f3f4f6; min-height: 100vh; }

.leaderboard-wrapper { min-height: 100vh; background: #f3f4f6; position: relative; overflow-x: hidden; }

.leaderboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; position: relative; z-index: 1; }

.leaderboard-header { text-align: center; margin-bottom: 3rem; padding-top: 1rem; }

.leaderboard-header h1 { font-size: 2.5rem; font-weight: 800; color: #00337F; margin-bottom: 0.5rem; line-height: 1.2; }

.leaderboard-subtitle { font-size: 1.1rem; color: #4b5563; margin-bottom: 0; }

.leaderboard-container { max-width: 80rem; margin: 0 auto; padding: 0 1rem 3rem 1rem; }

.tab-nav { display: flex; gap: 1rem; margin-bottom: 2rem; background: white; padding: 0.5rem; border-radius: 10px; border: 1px solid #e5e7eb; }

.tab-link { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; background: transparent; color: #6b7280; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.3s; }

.tab-link.active { background: linear-gradient(135deg, #00337F 0%, #003F99 100%); color: white; }

.week-info { background: linear-gradient(135deg, rgba(0, 51, 127, 0.1) 0%, rgba(0, 63, 153, 0.1) 100%); border-left: 4px solid #00337F; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem; }

.week-info-icon { font-size: 2rem; }

.week-info-content h3 { font-weight: 600; color: #00337F; margin: 0 0 0.5rem 0; }

.week-info-content p { color: #6b7280; margin: 0; font-size: 0.9rem; }

.leaderboard-card { background: white; border-radius: 10px; border: 1px solid #e5e7eb; overflow: hidden; }

.table-wrapper { overflow-x: auto; }

table { width: 100%; border-collapse: collapse; }

thead th { background: #f9fafb; padding: 1.25rem; text-align: left; font-weight: 600; color: #6b7280; font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }

tbody td { padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; }

tbody tr { transition: all 0.3s; }

tbody tr:hover { background: #fafafa; }

.leaderboard-card-mobile { display: none; }

.leaderboard-card-desktop { display: block; }

@media (max-width: 768px) {
    .leaderboard-card-desktop { display: none; }
    .leaderboard-card-mobile { display: block; }
    
    .leaderboard-card-mobile .leaderboard-item {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .leaderboard-card-mobile .leaderboard-item.rank-1 { background: linear-gradient(135deg, #fef3c7 0%, #fef3c7 100%); }
    .leaderboard-card-mobile .leaderboard-item.rank-2 { background: linear-gradient(135deg, #f3f4f6 0%, #f3f4f6 100%); }
    .leaderboard-card-mobile .leaderboard-item.rank-3 { background: linear-gradient(135deg, #fed7aa 0%, #fed7aa 100%); }
    .leaderboard-card-mobile .leaderboard-item.current-user { background: linear-gradient(135deg, #dbeafe 0%, #dbeafe 100%); border-left: 4px solid #00337F; }
    
    .leaderboard-card-mobile .rank-section {
        flex-shrink: 0;
        text-align: center;
    }
    
    .leaderboard-card-mobile .rank-medal {
        font-size: 2rem;
        margin-bottom: 0.25rem;
    }
    
    .leaderboard-card-mobile .rank-number {
        font-size: 1rem;
        font-weight: 700;
        color: #00337F;
    }
    
    .leaderboard-card-mobile .user-section {
        flex: 1;
    }
    
    .leaderboard-card-mobile .user-info {
        margin-bottom: 1rem;
    }
    
    .leaderboard-card-mobile .user-info-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .leaderboard-card-mobile .stat-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.5rem 0;
    }
    
    .leaderboard-card-mobile .stat-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.9rem;
    }
    
    .leaderboard-card-mobile .stat-label {
        color: #6b7280;
        font-weight: 500;
    }
    
    .leaderboard-card-mobile .stat-value {
        color: #1f2937;
        font-weight: 600;
    }
}

.rank-1 { background: linear-gradient(to right, #fef3c7 0%, #fef3c7 100%); }
.rank-2 { background: linear-gradient(to right, #f3f4f6 0%, #f3f4f6 100%); }
.rank-3 { background: linear-gradient(to right, #fed7aa 0%, #fed7aa 100%); }
.current-user { background: linear-gradient(to right, #dbeafe 0%, #dbeafe 100%); border-left: 4px solid #00337F; }

.medal { font-size: 1.5rem; margin-right: 0.5rem; }

.rank-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: white; border-radius: 6px; }

.badge-value { font-weight: 700; color: #1f2937; }

.user-name { font-weight: 600; color: #1f2937; }

.you-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.3rem 0.6rem; background: #dbeafe; color: #1e40af; border-radius: 6px; font-size: 0.75rem; font-weight: 600; margin-left: 0.5rem; }

.stat-col { display: flex; align-items: center; gap: 0.5rem; }

.stat-icon { font-size: 1.25rem; }

.stat-text { font-weight: 500; color: #1f2937; }

.info-box { background: linear-gradient(135deg, rgba(0, 51, 127, 0.08) 0%, rgba(0, 63, 153, 0.08) 100%); border: 1px solid #dbeafe; border-radius: 10px; padding: 1.5rem; margin-top: 2rem; }

.info-box h3 { font-weight: 700; color: #00337F; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem; }

.info-box ul { list-style: none; padding: 0; margin: 0; }

.info-box li { padding: 0.5rem 0; color: #4b5563; font-size: 0.95rem; }

.user-rank-card { background: white; border: 2px solid #fbbf24; border-radius: 10px; padding: 2rem; margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; }

.rank-number { font-size: 2rem; font-weight: 700; color: #00337F; }

.rank-details { font-size: 0.9rem; color: #6b7280; margin-top: 0.5rem; }

.empty-state { text-align: center; padding: 3rem 1rem; color: #9ca3af; }

.empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }

@media (max-width: 768px) {
    .leaderboard-header { flex-direction: column; align-items: flex-start; }
    .tab-nav { flex-direction: column; }
    .tab-link { width: 100%; text-align: center; }
    table { font-size: 0.85rem; }
    thead th, tbody td { padding: 0.75rem; }
    .user-rank-card { flex-direction: column; text-align: center; gap: 1rem; }
}
</style>
@endpush

@section('content')
<div class="leaderboard-wrapper">
    <div class="leaderboard-container">
        <!-- Header -->
        <div class="leaderboard-header">
            <h1>🏆 Leaderboard</h1>
            <p class="leaderboard-subtitle">Zeige dein Können und klettere die Rangliste hinauf</p>
        </div>

        <!-- Tab Navigation -->
        <div class="tab-nav">
            <a href="{{ route('gamification.leaderboard', ['tab' => 'gesamt']) }}" class="tab-link {{ $tab === 'gesamt' ? 'active' : '' }}">🌍 Gesamt-Rangliste</a>
            <a href="{{ route('gamification.leaderboard', ['tab' => 'woche']) }}" class="tab-link {{ $tab === 'woche' ? 'active' : '' }}">📅 Diese Woche</a>
        </div>

        @if($tab === 'woche' && $weekRange)
            <div class="week-info">
                <div class="week-info-icon">📆</div>
                <div class="week-info-content">
                    <h3>Aktuelle Woche</h3>
                    <p>{{ $weekRange['formatted'] }} (Montag - Sonntag)</p>
                </div>
            </div>
        @endif

        <!-- Leaderboard Table -->
        <div class="leaderboard-card leaderboard-card-desktop">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Name</th>
                            <th>{{ $tab === 'woche' ? 'Punkte (diese Woche)' : 'Punkte (gesamt)' }}</th>
                            <th>Level</th>
                            <th>Streak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaderboard as $index => $user)
                            @php
                                $rank = $index + 1;
                                $isCurrentUser = Auth::check() && Auth::user()->name === $user->name;
                                $medal = match($rank) {
                                    1 => '🥇',
                                    2 => '🥈',
                                    3 => '🥉',
                                    default => $rank . '.'
                                };
                                $rowClass = match($rank) {
                                    1 => 'rank-1',
                                    2 => 'rank-2',
                                    3 => 'rank-3',
                                    default => ''
                                };
                                if ($isCurrentUser) $rowClass = 'current-user';
                            @endphp
                            
                            <tr class="{{ $rowClass }}">
                                <td>
                                    <div class="rank-badge">
                                        <span class="medal">{{ $medal }}</span>
                                        <span class="badge-value">#{{ $rank }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="user-name">{{ $user->name }}</span>
                                    @if($isCurrentUser)
                                        <span class="you-badge">👤 Du</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="stat-col">
                                        <span class="stat-icon">💎</span>
                                        <span class="stat-text">{{ number_format($tab === 'woche' ? $user->weekly_points : $user->points) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="stat-col">
                                        <span class="stat-icon">⭐</span>
                                        <span class="stat-text">{{ $user->level }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="stat-col">
                                        <span class="stat-icon">🔥</span>
                                        <span class="stat-text">{{ $user->streak_days }} Tage</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">🏆</div>
                                        <p style="font-weight: 600;">{{ $tab === 'woche' ? 'Noch keine Aktivität diese Woche!' : 'Noch keine Einträge im Leaderboard' }}</p>
                                        <p style="font-size: 0.9rem; margin-top: 0.5rem;">{{ $tab === 'woche' ? 'Sei der Erste und sammle Punkte!' : '' }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Leaderboard Mobile Cards -->
        <div class="leaderboard-card-mobile">
            @forelse($leaderboard as $index => $user)
                @php
                    $rank = $index + 1;
                    $isCurrentUser = Auth::check() && Auth::user()->name === $user->name;
                    $medal = match($rank) {
                        1 => '🥇',
                        2 => '🥈',
                        3 => '🥉',
                        default => $rank . '.'
                    };
                    $rowClass = match($rank) {
                        1 => 'rank-1',
                        2 => 'rank-2',
                        3 => 'rank-3',
                        default => ''
                    };
                    if ($isCurrentUser) $rowClass = 'current-user';
                @endphp
                
                <div class="leaderboard-item {{ $rowClass }}">
                    <div class="rank-section">
                        <div class="rank-medal">{{ $medal }}</div>
                        <div class="rank-number">#{{ $rank }}</div>
                    </div>
                    <div class="user-section">
                        <div class="user-info">
                            <div class="user-info-name">
                                {{ $user->name }}
                                @if($isCurrentUser)
                                    <span class="you-badge">👤</span>
                                @endif
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-item">
                                <span>💎</span>
                                <span class="stat-label">Punkte:</span>
                                <span class="stat-value">{{ number_format($tab === 'woche' ? $user->weekly_points : $user->points) }}</span>
                            </div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-item">
                                <span>⭐</span>
                                <span class="stat-label">Level:</span>
                                <span class="stat-value">{{ $user->level }}</span>
                            </div>
                            <div class="stat-item">
                                <span>🔥</span>
                                <span class="stat-label">Streak:</span>
                                <span class="stat-value">{{ $user->streak_days }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="margin: 2rem 0;">
                    <div class="empty-state-icon">🏆</div>
                    <p style="font-weight: 600;">{{ $tab === 'woche' ? 'Noch keine Aktivität diese Woche!' : 'Noch keine Einträge im Leaderboard' }}</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">{{ $tab === 'woche' ? 'Sei der Erste und sammle Punkte!' : '' }}</p>
                </div>
            @endforelse
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <h3>💡 So sammelst du Punkte:</h3>
            <ul>
                <li>✅ <strong>+10 Punkte</strong> pro richtig beantwortete Frage</li>
                <li>🎓 <strong>+100 Punkte</strong> für bestandene Prüfungen</li>
                <li>🔥 <strong>Streak-Bonus</strong> bei täglichem Lernen</li>
                @if($tab === 'woche')
                    <li>📅 <strong>Wöchentliche Rangliste</strong> wird jeden Montag zurückgesetzt</li>
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
                <div class="user-rank-card">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #00337F; margin: 0;">📍 Deine Platzierung</h3>
                        <div class="rank-number">Rang #{{ $userRank }}</div>
                        <div class="rank-details">
                            @if($tab === 'woche')
                                {{ number_format($currentUser->weekly_points) }} Punkte diese Woche
                            @else
                                {{ number_format($currentUser->points) }} Punkte gesamt
                            @endif
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div class="rank-details">Level {{ $currentUser->level }}</div>
                        <div class="rank-details">🔥 {{ $currentUser->streak_days }} Tage Streak</div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

@endsection
