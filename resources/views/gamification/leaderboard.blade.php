@extends('layouts.app')
@section('title', 'Leaderboard - THW Trainer')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-blue-800">🏆 Leaderboard</h1>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                ← Zurück zum Dashboard
            </a>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 bg-white rounded-lg shadow-md p-2 inline-flex">
            <a href="{{ route('gamification.leaderboard', ['tab' => 'gesamt']) }}" 
               class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $tab === 'gesamt' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                🌍 Gesamt-Rangliste
            </a>
            <a href="{{ route('gamification.leaderboard', ['tab' => 'woche']) }}" 
               class="ml-2 px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $tab === 'woche' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                📅 Diese Woche
            </a>
        </div>

        @if($tab === 'woche' && $weekRange)
            <!-- Wocheninfo -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="text-2xl mr-3">📆</div>
                    <div>
                        <div class="font-semibold text-blue-900">Aktuelle Woche</div>
                        <div class="text-sm text-blue-700">{{ $weekRange['formatted'] }} (Montag - Sonntag)</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Leaderboard Tabelle -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-600 to-blue-700">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            Rang
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            Name
                        </th>
                        @if($tab === 'woche')
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Punkte (diese Woche)
                            </th>
                        @else
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Punkte (gesamt)
                            </th>
                        @endif
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            Level
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">
                            Streak
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaderboard as $index => $user)
                        @php
                            $rank = $index + 1;
                            $isCurrentUser = Auth::check() && Auth::user()->name === $user->name;
                            
                            // Medal Emojis für Top 3
                            $medal = '';
                            if ($rank === 1) $medal = '🥇';
                            elseif ($rank === 2) $medal = '🥈';
                            elseif ($rank === 3) $medal = '🥉';
                            
                            // Rang-Background-Color
                            $bgClass = '';
                            if ($rank === 1) $bgClass = 'bg-yellow-50';
                            elseif ($rank === 2) $bgClass = 'bg-gray-50';
                            elseif ($rank === 3) $bgClass = 'bg-orange-50';
                            elseif ($isCurrentUser) $bgClass = 'bg-blue-50 border-l-4 border-blue-500';
                        @endphp
                        
                        <tr class="{{ $bgClass }} {{ $isCurrentUser ? 'font-bold' : '' }} hover:bg-gray-50 transition-colors">
                            <!-- Rang -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-2">{{ $medal }}</span>
                                    <span class="text-lg font-semibold {{ $rank <= 3 ? 'text-blue-800' : 'text-gray-700' }}">
                                        #{{ $rank }}
                                    </span>
                                </div>
                            </td>
                            
                            <!-- Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                        @if($isCurrentUser)
                                            <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Du</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Punkte -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-xl mr-2">💎</span>
                                    <span class="text-sm font-semibold text-gray-900">
                                        @if($tab === 'woche')
                                            {{ number_format($user->weekly_points) }} Punkte
                                        @else
                                            {{ number_format($user->points) }} Punkte
                                        @endif
                                    </span>
                                </div>
                            </td>
                            
                            <!-- Level -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-xl mr-2">⭐</span>
                                    <span class="text-sm font-medium text-gray-900">Level {{ $user->level }}</span>
                                </div>
                            </td>
                            
                            <!-- Streak -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-xl mr-2">🔥</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $user->streak_days }} Tage</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="text-4xl mb-2">🏆</div>
                                @if($tab === 'woche')
                                    <p class="font-medium">Noch keine Aktivität diese Woche!</p>
                                    <p class="text-sm mt-1">Sei der Erste und sammle Punkte!</p>
                                @else
                                    <p class="font-medium">Noch keine Einträge im Leaderboard</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="text-2xl mr-3">💡</div>
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2">So sammelst du Punkte:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>✅ <strong>+10 Punkte</strong> pro richtig beantwortete Frage</li>
                        <li>🎓 <strong>+100 Punkte</strong> für bestandene Prüfungen</li>
                        <li>🔥 <strong>Streak-Bonus</strong> bei täglichem Lernen</li>
                        @if($tab === 'woche')
                            <li>📅 <strong>Wöchentliche Rangliste</strong> wird jeden Montag zurückgesetzt</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Eigener Rang (wenn nicht in Top 50) -->
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
                <div class="mt-6 bg-white border-2 border-blue-500 rounded-lg p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">📍 Deine Platzierung</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-blue-800">Rang #{{ $userRank }}</div>
                            <div class="text-sm text-gray-600">
                                @if($tab === 'woche')
                                    {{ number_format($currentUser->weekly_points) }} Punkte diese Woche
                                @else
                                    {{ number_format($currentUser->points) }} Punkte gesamt
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Level {{ $currentUser->level }}</div>
                            <div class="text-sm text-gray-600">🔥 {{ $currentUser->streak_days }} Tage Streak</div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
