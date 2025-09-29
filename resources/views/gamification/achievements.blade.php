@extends('layouts.app')
@section('title', 'Achievements - THW Trainer')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-blue-800">🏆 Achievements & Fortschritt</h1>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
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

        <!-- Spieler Status -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6 border border-blue-200">
                <div class="flex items-center">
                    <div class="text-4xl">⭐</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-blue-800">Level {{ $user->level }}</div>
                        <div class="text-sm text-gray-600">
                            @if($nextLevelPoints > 0)
                                {{ $nextLevelPoints }} Punkte bis Level {{ $user->level + 1 }}
                            @else
                                Max Level erreicht!
                            @endif
                        </div>
                    </div>
                </div>
                @if($nextLevelPoints > 0)
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-yellow-400 h-3 rounded-full transition-all duration-300" 
                                 style="width: {{ max(0, min(100, $levelProgress)) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ number_format($levelProgress, 1) }}% Fortschritt</div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border border-green-200">
                <div class="flex items-center">
                    <div class="text-4xl">💎</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-blue-800">{{ number_format($user->points) }}</div>
                        <div class="text-sm text-gray-600">Gesamtpunkte</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border border-orange-200">
                <div class="flex items-center">
                    <div class="text-4xl">🔥</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-blue-800">{{ $user->streak_days }}</div>
                        <div class="text-sm text-gray-600">Tage Streak</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border border-purple-200">
                <div class="flex items-center">
                    <div class="text-4xl">🏆</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-blue-800">{{ collect($achievements)->where('unlocked', true)->count() }}</div>
                        <div class="text-sm text-gray-600">Achievements</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Achievements -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-blue-800 mb-6">🎯 Achievements</h2>
                
                <div class="space-y-4">
                    @foreach($achievements as $achievement)
                        <div class="flex items-center p-4 rounded-lg border-2 transition-all duration-300 {{ $achievement['unlocked'] ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="text-3xl {{ $achievement['unlocked'] ? '' : 'opacity-30' }}">
                                {{ $achievement['icon'] }}
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="font-medium {{ $achievement['unlocked'] ? 'text-green-800' : 'text-gray-500' }}">
                                    {{ $achievement['title'] }}
                                </div>
                                <div class="text-sm {{ $achievement['unlocked'] ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $achievement['description'] }}
                                </div>
                            </div>
                            @if($achievement['unlocked'])
                                <div class="text-green-500 text-xl">✓</div>
                            @else
                                <div class="text-gray-300 text-xl">🔒</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tägliche Herausforderung -->
        <div class="mt-8 bg-gradient-to-r from-yellow-100 to-orange-100 rounded-lg shadow-md p-6 border border-yellow-200">
            <h2 class="text-xl font-semibold text-orange-800 mb-4">⚡ Tägliche Herausforderung</h2>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-medium text-orange-800">
                        {{ $user->daily_questions_solved ?? 0 }}/20 Fragen heute beantwortet
                    </div>
                    <div class="text-sm text-orange-600">
                        Beantworte 20 Fragen an einem Tag für das "Blitzschnell" Achievement!
                    </div>
                </div>
                <div class="text-4xl">
                    @if(($user->daily_questions_solved ?? 0) >= 20)
                        ✅
                    @else
                        ⚡
                    @endif
                </div>
            </div>
            @if($user->daily_questions_solved < 20)
                <div class="mt-4">
                    <div class="w-full bg-orange-200 rounded-full h-4">
                        <div class="bg-orange-500 h-4 rounded-full transition-all duration-300" 
                             style="width: {{ min(100, (($user->daily_questions_solved ?? 0) / 20) * 100) }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
