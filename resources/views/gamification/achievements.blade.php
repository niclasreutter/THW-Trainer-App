@extends('layouts.app')
@section('title', 'Achievements - THW Trainer')

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Achievements & <span>Fortschritt</span></h1>
        <p class="page-subtitle">Deine Erfolge und täglichen Herausforderungen</p>
    </header>

    @php
        $gamificationService = new \App\Services\GamificationService();
        $achievements = $gamificationService->getUserAchievements(Auth::user());
        $user = Auth::user();
        $nextLevelPoints = $gamificationService->getNextLevelPoints($user);
        $levelProgress = $gamificationService->getLevelProgress($user);
    @endphp

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon">⭐</span>
            <div>
                <div class="stat-pill-value">{{ $user->level }}</div>
                <div class="stat-pill-label">Level</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon">💎</span>
            <div>
                <div class="stat-pill-value">{{ number_format($user->points) }}</div>
                <div class="stat-pill-label">Punkte</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon">🔥</span>
            <div>
                <div class="stat-pill-value">{{ $user->streak_days }}</div>
                <div class="stat-pill-label">Tage Streak</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon">🏆</span>
            <div>
                <div class="stat-pill-value">{{ collect($achievements)->where('unlocked', true)->count() }}/{{ count($achievements) }}</div>
                <div class="stat-pill-label">Achievements</div>
            </div>
        </div>
    </div>

    <div class="bento-grid">
        <!-- Level Progress Card -->
        <div class="glass-gold bento-half p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--text-primary);">Level-Fortschritt</h3>
            <div class="flex items-center gap-4 mb-4">
                <div class="text-5xl">⭐</div>
                <div class="flex-1">
                    <div class="text-3xl font-extrabold text-gradient-gold">Level {{ $user->level }}</div>
                    <div class="text-sm" style="color: var(--text-secondary);">
                        @if($nextLevelPoints > 0)
                            Noch {{ $nextLevelPoints }} Punkte bis Level {{ $user->level + 1 }}
                        @else
                            Max Level erreicht!
                        @endif
                    </div>
                </div>
            </div>
            @if($nextLevelPoints > 0)
                <div class="progress-glass">
                    <div class="progress-fill-gold" style="width: {{ max(0, min(100, $levelProgress)) }}%"></div>
                </div>
                <div class="text-xs mt-2" style="color: var(--text-muted);">{{ number_format($levelProgress, 1) }}% Fortschritt</div>
            @endif
        </div>

        <!-- Daily Challenge Card -->
        <div class="glass-warning bento-half p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--text-primary);">Tägliche Herausforderung</h3>
            <div class="flex items-center gap-4 mb-4">
                <div class="text-5xl">
                    @if(($user->daily_questions_solved ?? 0) >= 20)
                        ✅
                    @else
                        ⚡
                    @endif
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium" style="color: var(--text-secondary);">
                        Beantworte 20 Fragen heute
                    </div>
                    <div class="text-2xl font-bold mt-1" style="color: var(--text-primary);">
                        {{ $user->daily_questions_solved ?? 0 }}/20 Fragen
                    </div>
                </div>
            </div>
            @if(($user->daily_questions_solved ?? 0) < 20)
                <div class="progress-glass">
                    <div class="progress-fill-gold" style="width: {{ min(100, (($user->daily_questions_solved ?? 0) / 20) * 100) }}%"></div>
                </div>
            @else
                <div class="alert-glass success">
                    <i class="bi bi-check-circle" style="color: var(--success);"></i>
                    <span style="color: var(--text-primary); font-weight: 600;">Tagesaufgabe erfüllt!</span>
                </div>
            @endif
        </div>

        <!-- Achievements Section -->
        <div class="glass bento-wide p-6">
            <div class="section-header mb-6">
                <h2 class="section-title">Alle Achievements</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($achievements as $achievement)
                    <div class="{{ $achievement['unlocked'] ? 'glass-success' : 'glass-subtle' }} p-4 transition-all duration-200 {{ $achievement['unlocked'] ? 'hover:scale-[1.02]' : 'opacity-70' }}">
                        <div class="flex items-start gap-3">
                            <div class="text-3xl {{ !$achievement['unlocked'] ? 'grayscale opacity-50' : '' }}">
                                {{ $achievement['icon'] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-bold text-sm truncate" style="color: {{ $achievement['unlocked'] ? 'var(--success)' : 'var(--text-secondary)' }};">
                                        {{ $achievement['title'] }}
                                    </h4>
                                    @if($achievement['unlocked'])
                                        <span class="text-green-500 flex-shrink-0">✓</span>
                                    @else
                                        <span class="flex-shrink-0" style="color: var(--text-muted);">🔒</span>
                                    @endif
                                </div>
                                <p class="text-xs" style="color: {{ $achievement['unlocked'] ? 'var(--success)' : 'var(--text-muted)' }};">
                                    {{ $achievement['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
