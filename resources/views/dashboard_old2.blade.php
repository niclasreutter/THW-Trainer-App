
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">THW-Trainer Dashboard</h1>
        
        <!-- Willkommen Sektion -->
        <div class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">👋 Willkommen {{ Auth::user()->name }}!</h2>
            
            <!-- Spielfortschritt -->
            <div class="mb-6">
                <div class="flex flex-wrap justify-between gap-2">
                    <!-- Streak -->
                    <div class="flex items-center bg-gradient-to-r from-orange-100 to-red-100 rounded-lg px-3 py-2 flex-1 min-w-0" style="flex-basis: calc(50% - 0.25rem);">
                        <div class="text-lg mr-2 flex-shrink-0">🔥</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-bold text-orange-800 truncate">{{ $user->streak_days ?? 0 }}</div>
                            <div class="text-xs text-orange-600">Streak</div>
                        </div>
                    </div>

                    <!-- Level -->
                    <div class="flex items-center bg-gradient-to-r from-yellow-100 to-orange-100 rounded-lg px-3 py-2 flex-1 min-w-0" style="flex-basis: calc(50% - 0.25rem);">
                        <div class="text-lg mr-2 flex-shrink-0">⭐</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-bold text-yellow-800 truncate">Lvl {{ $user->level ?? 1 }}</div>
                            @php
                                $levelUpPoints = 100 * pow(1.5, ($user->level ?? 1) - 1);
                                $currentProgress = ($user->points ?? 0) % $levelUpPoints;
                            @endphp
                            <div class="text-xs text-yellow-600 truncate">{{ $currentProgress }}/{{ $levelUpPoints }}</div>
                        </div>
                    </div>

                    <!-- Daily Challenge -->
                    <div class="flex items-center bg-gradient-to-r from-green-100 to-blue-100 rounded-lg px-3 py-2 flex-1 min-w-0" style="flex-basis: calc(50% - 0.25rem);">
                        <div class="text-lg mr-2 flex-shrink-0">⚡</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-bold text-green-800 truncate">{{ $user->daily_questions_solved ?? 0 }}/20</div>
                            <div class="text-xs text-green-600">Daily</div>
                        </div>
                    </div>

                    <!-- Achievements -->
                    <div class="flex items-center bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg px-3 py-2 flex-1 min-w-0" style="flex-basis: calc(50% - 0.25rem);">
                        <div class="text-lg mr-2 flex-shrink-0">🏆</div>
                        <div class="min-w-0 flex-1">
                            @php
                                $gamificationService = new \App\Services\GamificationService();
                                $userAchievements = $gamificationService->getUserAchievements($user);
                                $totalAchievements = count(\App\Services\GamificationService::ACHIEVEMENTS);
                                $unlockedCount = count(array_filter($userAchievements, fn($a) => $a['unlocked']));
                            @endphp
                            <div class="text-sm font-bold text-purple-800 truncate">{{ $unlockedCount }}/{{ $totalAchievements }}</div>
                            <div class="text-xs text-purple-600">Awards</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Entwicklungshinweis -->
            <div class="mb-6 p-4 rounded-lg shadow-md" style="background-color: #fef3c7; border: 2px solid #f59e0b;">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 mt-0.5" style="color: #92400e;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium" style="color: #78350f;">Website in Entwicklung</h3>
                        <div class="mt-1 text-sm" style="color: #92400e;">
                            <p>Diese Website befindet sich noch in der Entwicklung. Bei Fehlern oder Verbesserungsvorschlägen wende dich bitte an <strong>Niclas Reutter</strong> über <strong>Hermine</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fortschritt Sektion -->
        <div class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">📊 Dein Fortschritt</h2>
            @php
                $user = Auth::user();
                $total = \App\Models\Question::count();
                $progressArr = is_array($user->solved_questions ?? null) ? $user->solved_questions : json_decode($user->solved_questions ?? '[]', true);
                $progress = count($progressArr);
                $exams = $user->exam_passed_count ?? 0;
                // Nur bei 100% wirklich 100% anzeigen, sonst aufrunden vermeiden
                $progressPercent = $total > 0 ? ($progress == $total ? 100 : floor($progress / $total * 100)) : 0;
            @endphp
            
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Fragen beantwortet</span>
                    <span class="text-sm font-medium text-gray-700">{{ $progress }}/{{ $total }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                    <div class="bg-yellow-400 h-4 rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                </div>
                <span class="text-sm text-gray-600">{{ $progressPercent }}% abgeschlossen</span>
            </div>
            
            <div class="text-sm text-gray-600">
                <p class="mb-2">🎯 {{ $exams }} Prüfung{{ $exams == 1 ? '' : 'en' }} bestanden</p>
                <p>Sobald du alle Fragen einmal erfolgreich beantwortet hast, kannst du mit der Prüfungssimulation beginnen.</p>
            </div>
        </div>

        <!-- Navigation Sektion -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleLearning()">
                <h2 class="text-xl font-semibold text-blue-800">🚀 Weiter lernen</h2>
                <svg id="learningArrow" class="w-6 h-6 text-blue-800 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            
            <div id="learningContent" class="mt-6 grid gap-4" style="display: none;">
                <a href="{{ route('practice.menu') }}" 
                   class="block p-4 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 transition-colors">
                    <div class="text-lg font-medium text-blue-800">📚 Übungsmenü</div>
                    <div class="text-sm text-gray-600">Gezieltes Üben nach Lernabschnitten</div>
                </a>
                
                <a href="{{ route('bookmarks.index') }}" 
                   class="block p-4 bg-purple-100 border border-purple-300 rounded-lg hover:bg-purple-200 transition-colors">
                    <div class="text-lg font-medium text-blue-800">🔖 Gespeicherte Fragen</div>
                    <div class="text-sm text-gray-600">Deine Lesezeichen und Favoriten</div>
                </a>
                
                <a href="{{ route('gamification.achievements') }}" 
                   class="block p-4 bg-gradient-to-r from-purple-100 to-blue-100 border border-purple-300 rounded-lg hover:from-purple-200 hover:to-blue-200 transition-colors">
                    <div class="text-lg font-medium text-blue-800">🏆 Achievements & Bestenliste</div>
                    <div class="text-sm text-gray-600">Deine Erfolge und Vergleich mit anderen</div>
                </a>
                
                @php
                    $failedArr = is_array($user->exam_failed_questions ?? null) ? $user->exam_failed_questions : json_decode($user->exam_failed_questions ?? '[]', true);
                    $disabledExam = $progress < $total || ($failedArr && count($failedArr));
                @endphp
                
                @if($failedArr && count($failedArr))
                    <a href="{{ route('failed.index') }}" 
                       class="block p-4 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200 transition-colors">
                        <div class="text-lg font-medium text-blue-800">🔄 Fehler wiederholen</div>
                        <div class="text-sm text-gray-600">{{ count($failedArr) }} offene Fragen</div>
                    </a>
                @endif
                
                <a href="{{ $disabledExam ? '#' : route('exam.index') }}"
                   class="block p-4 rounded-lg transition-colors {{ $disabledExam ? 'bg-gray-100 border border-gray-300 cursor-not-allowed' : 'bg-blue-100 border border-blue-300 hover:bg-blue-200' }}"
                   @if($disabledExam) aria-disabled="true" tabindex="-1" @endif>
                    <div class="text-lg font-medium {{ $disabledExam ? 'text-gray-500' : 'text-blue-800' }}">🎓 Zur Prüfung</div>
                    <div class="text-sm {{ $disabledExam ? 'text-gray-400' : 'text-gray-600' }}">
                        {{ $disabledExam ? 'Erst alle Fragen lösen' : 'Prüfungssimulation starten' }}
                    </div>
                </a>
                
                @if(Auth::user()->useroll === 'admin')
                    <a href="{{ route('admin.users.index') }}" 
                       class="block p-4 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200 transition-colors">
                        <div class="text-lg font-medium text-blue-800">⚙️ Administration</div>
                        <div class="text-sm text-gray-600">Nutzer- und Fragenverwaltung</div>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleLearning() {
            const content = document.getElementById('learningContent');
            const arrow = document.getElementById('learningArrow');
            
            if (content.style.display === 'none') {
                content.style.display = 'grid';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                content.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    </script>
@endsection
