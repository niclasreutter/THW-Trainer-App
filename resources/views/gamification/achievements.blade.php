@extends('layouts.app')
@section('title', 'Achievements - THW Trainer')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .achievements-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .achievements-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .achievements-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        padding-top: 1rem;
    }

    .achievements-title {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.2;
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        color: #4b5563;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        background: #f9fafb;
        border-color: #00337F;
        color: #00337F;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 1.75rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        font-size: 2.5rem;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #00337F;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .stat-card .progress-section {
        margin-top: 1rem;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 4px;
        transition: width 0.5s ease-out;
    }

    .progress-text {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .achievements-section {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        padding: 2rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 1.5rem;
    }

    .achievements-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .achievement-card {
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .achievement-card.unlocked {
        border-color: #22c55e;
        background: rgba(34, 197, 94, 0.05);
    }

    .achievement-card.locked {
        background: #f9fafb;
        opacity: 0.8;
    }

    .achievement-card:hover:not(.locked) {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(34, 197, 94, 0.2);
    }

    .achievement-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .achievement-icon {
        font-size: 2.5rem;
        flex-shrink: 0;
    }

    .achievement-icon.locked {
        opacity: 0.4;
    }

    .achievement-status {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .achievement-content {
        flex: 1;
    }

    .achievement-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .achievement-card.unlocked .achievement-title {
        color: #16a34a;
    }

    .achievement-card.locked .achievement-title {
        color: #6b7280;
    }

    .achievement-description {
        font-size: 0.85rem;
        line-height: 1.4;
    }

    .achievement-card.unlocked .achievement-description {
        color: #16a34a;
    }

    .achievement-card.locked .achievement-description {
        color: #9ca3af;
    }

    .daily-challenge {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.15) 0%, rgba(245, 158, 11, 0.15) 100%);
        border: 2px solid #fbbf24;
        border-radius: 1.25rem;
        padding: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }

    .daily-challenge-content {
        flex: 1;
    }

    .daily-challenge-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .daily-challenge-description {
        font-size: 0.95rem;
        color: #4b5563;
        margin-bottom: 1rem;
    }

    .daily-challenge-progress {
        background: white;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        display: inline-block;
        font-weight: 600;
        color: #00337F;
    }

    .daily-challenge-icon {
        font-size: 3rem;
        flex-shrink: 0;
    }

    .daily-progress-bar {
        margin-top: 1rem;
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .daily-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        border-radius: 4px;
        transition: width 0.5s ease-out;
    }

    @media (max-width: 768px) {
        .achievements-header { flex-direction: column; gap: 1rem; align-items: flex-start; }
        .achievements-title { font-size: 1.75rem; }
        .achievements-grid { grid-template-columns: 1fr; }
        .daily-challenge { flex-direction: column; text-align: center; }
    }

    @media (max-width: 640px) {
        .achievements-container { padding: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="achievements-wrapper">
    <div class="achievements-container">
        <div class="achievements-header">
            <h1 class="achievements-title">Achievements & Fortschritt</h1>
            <a href="{{ route('dashboard') }}" class="back-link">
                ← Zurück zum Dashboard
            </a>
        </div>

        @php
            $gamificationService = new \App\Services\GamificationService();
            $achievements = $gamificationService->getUserAchievements(Auth::user());
            $leaderboard = $gamificationService->getLeaderboard(10);
            $user = Auth::user();
            $nextLevelPoints = $gamificationService->getNextLevelPoints($user);
            $levelProgress = $gamificationService->getLevelProgress($user);
        @endphp

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon text-yellow-500"><i class="bi bi-star-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value">Level {{ $user->level }}</div>
                    <div class="stat-label">
                        @if($nextLevelPoints > 0)
                            {{ $nextLevelPoints }} Punkte bis Level {{ $user->level + 1 }}
                        @else
                            Max Level erreicht!
                        @endif
                    </div>
                    @if($nextLevelPoints > 0)
                        <div class="progress-section">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ max(0, min(100, $levelProgress)) }}%"></div>
                            </div>
                            <div class="progress-text">{{ number_format($levelProgress, 1) }}% Fortschritt</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon text-cyan-500"><i class="bi bi-gem"></i></div>
                <div class="stat-content">
                    <div class="stat-value">{{ number_format($user->points) }}</div>
                    <div class="stat-label">Gesamtpunkte</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon text-orange-500"><i class="bi bi-fire"></i></div>
                <div class="stat-content">
                    <div class="stat-value">{{ $user->streak_days }}</div>
                    <div class="stat-label">Tage Streak</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon text-yellow-600"><i class="bi bi-trophy-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value">{{ collect($achievements)->where('unlocked', true)->count() }}</div>
                    <div class="stat-label">Achievements freigeschaltet</div>
                </div>
            </div>
        </div>

        <!-- Achievements -->
        <div class="achievements-section">
            <h2 class="section-title">Achievements</h2>
            <div class="achievements-grid">
                @foreach($achievements as $achievement)
                    <div class="achievement-card {{ $achievement['unlocked'] ? 'unlocked' : 'locked' }}">
                        <div class="achievement-header">
                            <div class="achievement-icon {{ !$achievement['unlocked'] ? 'locked' : '' }}">
                                {{ $achievement['icon'] }}
                            </div>
                            <div class="achievement-status">
                                @if($achievement['unlocked'])
                                    <span style="color: #22c55e;">✓</span>
                                @else
                                    <span style="color: #d1d5db;">🔒</span>
                                @endif
                            </div>
                        </div>
                        <div class="achievement-content">
                            <h3 class="achievement-title">{{ $achievement['title'] }}</h3>
                            <p class="achievement-description">{{ $achievement['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Daily Challenge -->
        <div class="daily-challenge">
            <div class="daily-challenge-content">
                <h2 class="daily-challenge-title">Tägliche Herausforderung</h2>
                <p class="daily-challenge-description">
                    Beantworte 20 Fragen heute für das "Blitzschnell" Achievement!
                </p>
                <div class="daily-challenge-progress">
                    {{ $user->daily_questions_solved ?? 0 }}/20 Fragen
                </div>
                @if($user->daily_questions_solved < 20)
                    <div class="daily-progress-bar">
                        <div class="daily-progress-fill" style="width: {{ min(100, (($user->daily_questions_solved ?? 0) / 20) * 100) }}%"></div>
                    </div>
                @else
                    <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: rgba(34, 197, 94, 0.1); border-radius: 0.75rem; color: #16a34a; font-weight: 600;">
                        ✓ Tagesaufgabe erfüllt!
                    </div>
                @endif
            </div>
            <div class="daily-challenge-icon">
                @if(($user->daily_questions_solved ?? 0) >= 20)
                    ✅
                @else
                    ⚡
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
