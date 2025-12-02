
@extends('layouts.app')

@section('title', 'Dashboard - Dein Lernfortschritt')
@section('description', 'Dein persönliches THW-Trainer Dashboard: Verfolge deinen Lernfortschritt, wiederhole falsche Fragen und bereite dich optimal auf deine THW-Prüfung vor.')

@push('styles')
<style>
    .exam-history-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    
    .exam-history-content.show {
        max-height: 2000px;
        transition: max-height 0.5s ease-in;
    }
    
    .rotate-icon {
        transition: transform 0.3s ease;
    }
    
    .rotate-icon.rotated {
        transform: rotate(180deg);
    }

    /* Leaderboard Popup Modal */
    .leaderboard-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
        animation: fadeIn 0.3s ease-out;
    }

    .leaderboard-modal {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        border-radius: 24px;
        max-width: 600px;
        width: 100%;
        position: relative;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 100px rgba(251, 191, 36, 0.3);
        animation: slideUp 0.4s ease-out;
        overflow: hidden;
    }

    .leaderboard-modal-content {
        padding: 32px 24px;
        position: relative;
    }

    .leaderboard-modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.5);
        color: white;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    .leaderboard-modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .leaderboard-trophy-bg {
        position: absolute;
        top: -30px;
        right: -30px;
        font-size: 200px;
        opacity: 0.15;
        transform: rotate(-15deg);
        pointer-events: none;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 640px) {
        .leaderboard-modal {
            margin: 0;
            max-height: 90vh;
            overflow-y: auto;
        }

        .leaderboard-modal-content {
            padding: 24px 20px;
        }

        .leaderboard-trophy-bg {
            font-size: 120px;
        }
    }
</style>
@endpush

@section('content')
    <!-- Leaderboard Popup Modal -->
    @if(!$user->leaderboard_banner_dismissed && !$user->leaderboard_consent)
        <div class="leaderboard-modal-overlay" id="leaderboard-modal">
            <div class="leaderboard-modal">
                <div class="leaderboard-trophy-bg">🏆</div>
                
                <div class="leaderboard-modal-content">
                    <button class="leaderboard-modal-close" onclick="dismissModal(false)" aria-label="Schließen">×</button>
                    
                    <div style="text-align: center; margin-bottom: 24px;">
                        <div style="display: inline-block; background: rgba(255, 255, 255, 0.2); border-radius: 50%; padding: 20px; margin-bottom: 16px; backdrop-filter: blur(10px);">
                            <svg style="width: 64px; height: 64px; color: white;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-white mb-3" style="text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                            🎉 Neu: Öffentliches Leaderboard!
                        </h2>
                        <p class="text-white text-lg mb-4" style="text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            Messe dich mit anderen THW-Lernenden!
                        </p>
                    </div>

                    <div style="background: rgba(255, 255, 255, 0.15); border-radius: 16px; padding: 20px; margin-bottom: 24px; backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3);">
                        <p class="text-white mb-3" style="font-size: 15px; line-height: 1.6;">
                            📊 <strong>Was wird angezeigt?</strong><br>
                            Dein Name, deine Punkte und deine Position im Ranking
                        </p>
                        <p class="text-white mb-3" style="font-size: 15px; line-height: 1.6;">
                            🔄 <strong>Jederzeit änderbar</strong><br>
                            Du kannst diese Einstellung jederzeit in deinem Profil anpassen
                        </p>
                        <p class="text-white" style="font-size: 15px; line-height: 1.6;">
                            🏆 <strong>Zeige deine Erfolge</strong><br>
                            Motiviere andere und lass dich von ihnen motivieren
                        </p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" id="acceptForm">
                            @csrf
                            <input type="hidden" name="accept" value="1">
                            <button type="submit" 
                                    class="w-full"
                                    style="background: white; color: #d97706; font-weight: 700; font-size: 18px; padding: 16px 24px; border-radius: 16px; border: none; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(0,0,0,0.3);"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 30px rgba(0,0,0,0.4)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.3)';">
                                ✅ Ja, ich möchte teilnehmen!
                            </button>
                        </form>
                        
                        <form action="{{ route('profile.dismiss.leaderboard.banner') }}" method="POST" id="declineForm">
                            @csrf
                            <input type="hidden" name="accept" value="0">
                            <button type="submit" 
                                    class="w-full"
                                    style="background: rgba(255,255,255,0.2); color: white; font-weight: 600; font-size: 16px; padding: 14px 20px; border-radius: 16px; border: 2px solid rgba(255,255,255,0.5); cursor: pointer; transition: all 0.3s ease; backdrop-filter: blur(10px);"
                                    onmouseover="this.style.background='rgba(255,255,255,0.3)';"
                                    onmouseout="this.style.background='rgba(255,255,255,0.2)';">
                                ❌ Nein, danke
                            </button>
                        </form>
                        
                        <a href="{{ route('datenschutz') }}" 
                           target="_blank"
                           class="text-center"
                           style="background: rgba(255,255,255,0.1); color: white; font-weight: 500; font-size: 14px; padding: 12px 16px; border-radius: 12px; border: 2px solid rgba(255,255,255,0.3); cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;"
                           onmouseover="this.style.background='rgba(255,255,255,0.2)';"
                           onmouseout="this.style.background='rgba(255,255,255,0.1)';">
                            <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Mehr Informationen im Datenschutz
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function dismissModal(accept) {
                const modal = document.getElementById('leaderboard-modal');
                if (modal) {
                    modal.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        modal.remove();
                    }, 300);
                }
                
                // Wenn über X geschlossen wird (kein Accept)
                if (accept === false) {
                    document.getElementById('declineForm').submit();
                }
            }

            // ESC-Taste zum Schließen
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    dismissModal(false);
                }
            });

            // Click außerhalb des Modals schließt es NICHT (Nutzer soll entscheiden)
        </script>

        <style>
            @keyframes fadeOut {
                from {
                    opacity: 1;
                }
                to {
                    opacity: 0;
                }
            }
        </style>
    @endif

    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">THW-Trainer Dashboard</h1>
        
        @php 
            // Variablen für Dashboard definieren
            $user = Auth::user();
            $total = $totalQuestions ?? \App\Models\Question::count(); // Nutze gecachten Wert vom Controller
            
            // Sicherstelle dass $total nicht NULL oder 0 ist
            if (empty($total)) {
                $total = \App\Models\Question::count();
            }
            
            $progressArr = is_array($user->solved_questions ?? null) 
                ? $user->solved_questions 
                : (is_string($user->solved_questions) ? json_decode($user->solved_questions, true) ?? [] : []);
            $progress = count($progressArr); // Gemeisterte Fragen (2x richtig)
            $exams = $user->exam_passed_count ?? 0;
            
            // Neue Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
            try {
                $progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();
                
                $totalProgressPoints = 0;
                if ($progressData && $progressData->count() > 0) {
                    foreach ($progressData as $prog) {
                        $totalProgressPoints += min($prog->consecutive_correct ?? 0, 2);
                    }
                }
                $maxProgressPoints = $total * 2;
                $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
            } catch (\Exception $e) {
                // Fallback bei Fehler
                $progressPercent = 0;
                $totalProgressPoints = 0;
            }
        @endphp

        @if(session('error'))
            <div id="error-message" class="mb-6" style="background-color: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1); position: relative;">
                <button onclick="document.getElementById('error-message').style.display='none'" 
                        style="position: absolute; top: 8px; right: 8px; background: none; border: none; font-size: 18px; color: #dc2626; cursor: pointer; padding: 4px; border-radius: 4px; hover:bg-red-200;"
                        onmouseover="this.style.backgroundColor='rgba(239, 68, 68, 0.1)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                    ×
                </button>
                <p class="text-base font-medium" style="color: #dc2626; margin-bottom: 0;">
                    🔥 {{ session('error') }}
                </p>
            </div>
        @endif

        <!-- Willkommen Sektion -->
        <div class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">👋 Willkommen {{ Auth::user()->name }}!</h2>
            
            @php
                // Motivational Messages basierend auf Fortschritt
                
                if ($progressPercent == 100 && $exams >= 5) {
                    $motivationalMessage = "🎉 Fantastisch! Du hast alle Fragen gelöst und 5+ Prüfungen bestanden! Du bist bereit für die Grundausbildung!";
                    $messageColor = "text-green-800 bg-green-50 border-green-300";
                } elseif ($progressPercent == 100) {
                    $motivationalMessage = "🚀 Großartig! Alle Fragen gelöst! Jetzt kannst du mit den Prüfungen beginnen!";
                    $messageColor = "text-green-800 bg-green-50 border-green-300";
                } elseif ($progressPercent >= 75) {
                    $motivationalMessage = "⚡ Fast geschafft! Du hast schon {$progressPercent}% der Fragen gelöst!";
                    $messageColor = "text-yellow-800 bg-yellow-50 border-yellow-300";
                } elseif ($progressPercent >= 50) {
                    $motivationalMessage = "💪 Gut gemacht! Du hast schon {$progressPercent}% der Fragen gelöst! Weiter so!";
                    $messageColor = "text-blue-800 bg-blue-50 border-blue-300";
                } elseif ($progressPercent >= 25) {
                    $motivationalMessage = "🌟 Super Start! Du hast schon {$progressPercent}% der Fragen gelöst!";
                    $messageColor = "text-blue-800 bg-blue-50 border-blue-300";
                } elseif ($progressPercent > 0) {
                    $motivationalMessage = "🌟 Super Start! Du hast schon {$progressPercent}% der Fragen gelöst!";
                    $messageColor = "text-blue-800 bg-blue-50 border-blue-300";
                } else {
                    $motivationalMessage = "🎯 Willkommen beim THW-Trainer! Starte deine Reise zur Grundausbildung!";
                    $messageColor = "text-blue-900 bg-blue-50 border-blue-300";
                }
            @endphp
            
            <!-- Motivational Message -->
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700">{{ $motivationalMessage }}</p>
            </div>
            
            <!-- Spielfortschritt -->
            <div class="mb-6">
                <!-- Mobile: 2x2 Grid, Desktop: 4x1 Grid -->
                <div class="flex flex-wrap gap-2 lg:gap-3">
                    <!-- Streak -->
                    <div class="flex-1 min-w-[140px] lg:min-w-[160px] max-w-[calc(50%-4px)] lg:max-w-none flex items-center bg-gradient-to-r from-yellow-100 to-yellow-200 rounded-lg px-2 lg:px-3 py-2 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                        <div class="text-base lg:text-lg mr-1 lg:mr-2 flex-shrink-0">🔥</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm lg:text-base font-bold text-yellow-900 truncate">{{ $user->streak_days ?? 0 }}</div>
                            <div class="text-xs text-yellow-700">Tage</div>
                            <!-- Streak Progress Bar -->
                            @php
                                $streakGoal = 7; // 7 Tage Streak als Ziel
                                $streakProgressPercent = min(100, (($user->streak_days ?? 0) / $streakGoal) * 100);
                            @endphp
                            <div class="w-full bg-yellow-300 rounded-full h-1 mt-1">
                                <div class="bg-yellow-600 h-1 rounded-full transition-all duration-500" style="width: {{ $streakProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Level -->
                    <div class="flex-1 min-w-[140px] lg:min-w-[160px] max-w-[calc(50%-4px)] lg:max-w-none flex items-center bg-gradient-to-r from-blue-100 to-blue-200 rounded-lg px-2 lg:px-3 py-2 hover:shadow-md transition-all duration-300 cursor-pointer">
                        <div class="text-base lg:text-lg mr-1 lg:mr-2 flex-shrink-0">⭐</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm lg:text-base font-bold text-blue-900 truncate">Lvl {{ $user->level ?? 1 }}</div>
                            @php
                                $levelUpPoints = 100 * pow(1.5, ($user->level ?? 1) - 1);
                                $currentProgress = ($user->points ?? 0) % $levelUpPoints;
                                $levelProgressPercent = $levelUpPoints > 0 ? ($currentProgress / $levelUpPoints) * 100 : 0;
                            @endphp
                            <div class="text-xs text-blue-700 truncate hidden lg:block">{{ $currentProgress }}/{{ $levelUpPoints }}</div>
                            <div class="text-xs text-blue-700 lg:hidden">XP</div>
                            <!-- Mini Progress Bar -->
                            <div class="w-full bg-blue-300 rounded-full h-1 mt-1">
                                <div class="bg-blue-600 h-1 rounded-full transition-all duration-500" style="width: {{ $levelProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Challenge -->
                    <div class="flex-1 min-w-[140px] lg:min-w-[160px] max-w-[calc(50%-4px)] lg:max-w-none flex items-center bg-gradient-to-r from-green-100 to-green-200 rounded-lg px-2 lg:px-3 py-2 hover:shadow-md transition-all duration-300 cursor-pointer">
                        <div class="text-base lg:text-lg mr-1 lg:mr-2 flex-shrink-0">⚡</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm lg:text-base font-bold text-green-900 truncate">{{ $user->daily_questions_solved ?? 0 }}/20</div>
                            <div class="text-xs text-green-700">Täglich</div>
                            <!-- Mini Progress Bar -->
                            @php
                                $dailyProgressPercent = min(100, (($user->daily_questions_solved ?? 0) / 20) * 100);
                            @endphp
                            <div class="w-full bg-green-300 rounded-full h-1 mt-1">
                                <div class="bg-green-600 h-1 rounded-full transition-all duration-500" style="width: {{ $dailyProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Achievements -->
                    <div class="flex-1 min-w-[140px] lg:min-w-[160px] max-w-[calc(50%-4px)] lg:max-w-none flex items-center bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg px-2 lg:px-3 py-2 hover:shadow-md transition-all duration-300 cursor-pointer">
                        <div class="text-base lg:text-lg mr-1 lg:mr-2 flex-shrink-0">🏆</div>
                        <div class="min-w-0 flex-1">
                            @php
                                $gamificationService = new \App\Services\GamificationService();
                                $userAchievements = $gamificationService->getUserAchievements($user);
                                $totalAchievements = count(\App\Services\GamificationService::ACHIEVEMENTS);
                                $unlockedCount = count(array_filter($userAchievements, fn($a) => $a['unlocked']));
                                $achievementProgressPercent = $totalAchievements > 0 ? ($unlockedCount / $totalAchievements) * 100 : 0;
                            @endphp
                            <div class="text-sm lg:text-base font-bold text-blue-900 truncate">{{ $unlockedCount }}/{{ $totalAchievements }}</div>
                            <div class="text-xs text-blue-700">Erfolge</div>
                            <!-- Mini Progress Bar -->
                            <div class="w-full bg-blue-200 rounded-full h-1 mt-1">
                                <div class="bg-blue-500 h-1 rounded-full transition-all duration-500" style="width: {{ $achievementProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- E-Mail-Zustimmung Banner -->
            @if(!$user->email_consent && !session('email_consent_banner_dismissed'))
                <div id="email-consent-banner" class="mt-6 p-4 rounded-lg" style="background-color: #f0f9ff; border: 2px solid #0ea5e9; box-shadow: 0 0 20px rgba(14, 165, 233, 0.3), 0 0 40px rgba(14, 165, 233, 0.1); position: relative;">
                    <button onclick="dismissEmailConsentBanner()" 
                            style="position: absolute; top: 8px; right: 8px; background: none; border: none; font-size: 18px; color: #0284c7; cursor: pointer; padding: 4px; border-radius: 4px;"
                            onmouseover="this.style.backgroundColor='rgba(14, 165, 233, 0.1)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                        ×
                    </button>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 mt-1" style="color: #0284c7;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium" style="color: #0284c7; margin-bottom: 6px;">📧 E-Mail-Benachrichtigungen aktivieren</h3>
                            <p class="text-xs" style="color: #0369a1; margin-bottom: 8px;">
                                Verpasse keine Updates! Aktiviere E-Mail-Benachrichtigungen für deinen Lernfortschritt, neue Features und wichtige Systeminformationen.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('profile') }}" 
                                   style="background: linear-gradient(to right, #0ea5e9, #0284c7); color: white; font-weight: 600; padding: 6px 12px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease; transform: scale(1); font-size: 12px;"
                                   onmouseover="this.style.background='linear-gradient(to right, #0284c7, #0369a1)'; this.style.transform='scale(1.02)'"
                                   onmouseout="this.style.background='linear-gradient(to right, #0ea5e9, #0284c7)'; this.style.transform='scale(1)'">
                                    📧 Jetzt aktivieren
                                </a>
                                <button onclick="dismissEmailConsentBanner()" 
                                        style="background: none; border: 1px solid #0ea5e9; color: #0284c7; font-weight: 500; padding: 6px 12px; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-size: 12px;"
                                        onmouseover="this.style.backgroundColor='rgba(14, 165, 233, 0.1)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                    Später
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Lehrgänge Sektion -->
        @php
            $enrolledLehrgaenge = Auth::user()->enrolledLehrgaenge()->get();
        @endphp

        @if($enrolledLehrgaenge->isNotEmpty())
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-blue-800">📚 Deine Lehrgänge</h2>
                <a href="{{ route('lehrgaenge.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                    Alle anschauen →
                </a>
            </div>
                @php
                    $count = $enrolledLehrgaenge->count();
                    // Grid-Klassen für Layout
                    $gridClass = match($count) {
                        1 => 'grid-cols-1 max-w-2xl mx-auto',                        // 1 Lehrgang: ganze Breite, begrenzte max-width
                        2 => 'grid-cols-1 md:grid-cols-2',                           // 2 Lehrgänge: halbe Breite
                        default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'      // 3+ Lehrgänge: drittel Breite (max 3 pro Zeile)
                    };
                @endphp
                <div class="grid {{ $gridClass }} gap-6">
                    @foreach($enrolledLehrgaenge as $lehrgang)
                        @php
                            // Gleiche Logik wie in lehrgaenge/practice
                            $solvedCount = \App\Models\UserLehrgangProgress::where('user_id', Auth::id())
                                ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                                ->where('solved', true)
                                ->count();
                            
                            $totalCount = \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
                            
                            // Neue Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
                            $progressData = \App\Models\UserLehrgangProgress::where('user_id', Auth::id())
                                ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                                ->get();
                            
                            $totalProgressPoints = 0;
                            foreach ($progressData as $prog) {
                                $totalProgressPoints += min($prog->consecutive_correct, 2);
                            }
                            $maxProgressPoints = $totalCount * 2;
                            $lehrgangProgressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                            
                            $isCompleted = $lehrgangProgressPercent == 100 && $solvedCount > 0;
                        @endphp
                        
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden flex flex-col h-full">
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $lehrgang->lehrgang }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $lehrgang->beschreibung }}</p>
                                
                                <!-- Fortschritt mit Animation -->
                                <div class="mb-4 flex-grow">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>{{ $solvedCount }}/{{ $totalCount }} Fragen</span>
                                        <span>{{ $lehrgangProgressPercent }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ $lehrgangProgressPercent }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
                                    </div>
                                </div>
                                
                                <!-- Button -->
                                @if($isCompleted)
                                    <!-- Abgeschlossen (Grün mit Glow) -->
                                    <div class="w-full text-center px-4 py-2 rounded text-sm font-semibold text-white"
                                         style="background: linear-gradient(to right, #10b981, #059669); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4), 0 0 20px rgba(16, 185, 129, 0.3), 0 0 40px rgba(16, 185, 129, 0.1); transition: all 0.3s ease;"
                                         onmouseover="this.style.background='linear-gradient(to right, #059669, #047857)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.4), 0 0 25px rgba(16, 185, 129, 0.4), 0 0 50px rgba(16, 185, 129, 0.2)'; this.style.transform='scale(1.02)'"
                                         onmouseout="this.style.background='linear-gradient(to right, #10b981, #059669)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.4), 0 0 20px rgba(16, 185, 129, 0.3), 0 0 40px rgba(16, 185, 129, 0.1)'; this.style.transform='scale(1)'">
                                        ✓ Abgeschlossen
                                    </div>
                                @else
                                    <!-- Weitermachen (Gelb mit Glow) -->
                                    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" 
                                       class="w-full inline-block text-center px-4 py-2 rounded text-sm font-semibold transition"
                                       style="background: linear-gradient(to right, #facc15, #f59e0b); color: #1e40af; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1); text-decoration: none;"
                                       onmouseover="this.style.background='linear-gradient(to right, #f59e0b, #d97706)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4), 0 0 25px rgba(251, 191, 36, 0.4), 0 0 50px rgba(251, 191, 36, 0.2)'; this.style.transform='scale(1.02)'"
                                       onmouseout="this.style.background='linear-gradient(to right, #facc15, #f59e0b)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1)'; this.style.transform='scale(1)'">
                                        📚 Weitermachen
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>
        @else
        <!-- Placeholder wenn nicht eingeschrieben -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-blue-800">📚 Deine Lehrgänge</h2>
                <a href="{{ route('lehrgaenge.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                    Alle anschauen →
                </a>
            </div>
            
            <!-- Placeholder Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md border-2 border-dashed border-blue-300 p-4 text-center">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <div class="text-3xl">🎓</div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-blue-800 mb-1">Lehrgänge - Neu & exklusiv!</h3>
                        <p class="text-sm text-gray-700 mb-2">
                            Teste das neue Feature! ✨ Mehr Lehrgänge kommen bald. ✍️ Autoren für Fragen gesucht!
                        </p>
                    </div>
                    <a href="{{ route('lehrgaenge.index') }}" 
                       class="inline-block px-4 py-2 rounded text-xs font-semibold transition whitespace-nowrap"
                       style="background: linear-gradient(to right, #3b82f6, #2563eb); color: white; text-decoration: none; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4), 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1);"
                       onmouseover="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 15px rgba(59, 130, 246, 0.4), 0 0 25px rgba(59, 130, 246, 0.4), 0 0 50px rgba(59, 130, 246, 0.2)'"
                       onmouseout="this.style.background='linear-gradient(to right, #3b82f6, #2563eb)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(59, 130, 246, 0.4), 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1)'">
                        🚀 Erkunden
                    </a>
                </div>
            </div>
        </div>
        @endif

        @php
            // Nur bei 100% wirklich 100% anzeigen, sonst aufrunden vermeiden
            $masteredPercent = $total > 0 ? ($progress == $total ? 100 : floor($progress / $total * 100)) : 0;
            $examsPercent = $exams > 0 ? min(100, floor($exams / 5 * 100)) : 0;
        @endphp

        <!-- Fortschritt Sektion -->
        @php
            $enrolledLehrgaengeCount = Auth::user()->enrolledLehrgaenge()->count();
        @endphp
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-12">
            @if($enrolledLehrgaengeCount > 0)
                <!-- Collapse Header wenn eingeschrieben -->
                <div class="flex items-center justify-between cursor-pointer" onclick="toggleProgress()">
                    <h2 class="text-xl font-semibold text-blue-800">📊 Dein Fortschritt</h2>
                    <svg id="progressArrow" class="w-6 h-6 text-blue-800 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                
                <!-- Collapsible Content -->
                <div id="progressContent" class="mt-6" style="display: none; max-height: 0; overflow: hidden; opacity: 0; transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;">
            @else
                <h2 class="text-xl font-semibold text-blue-800 mb-4">📊 Dein Fortschritt</h2>
                <div>
            @endif
            
            <!-- Info-Karte: 2x richtig Regel -->
            <div id="info-2x-rule" style="margin-bottom: 16px; padding: 12px; background-color: #eff6ff; border: 2px solid #3b82f6; border-radius: 8px; box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1); position: relative;">
                <button onclick="document.getElementById('info-2x-rule').style.display='none'" 
                        style="position: absolute; top: 8px; right: 8px; background: none; border: none; font-size: 18px; color: #2563eb; cursor: pointer; padding: 4px; border-radius: 4px; line-height: 1;"
                        onmouseover="this.style.backgroundColor='rgba(59, 130, 246, 0.1)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                    ×
                </button>
                <div style="display: flex; align-items: flex-start; gap: 8px; padding-right: 20px;">
                    <div style="flex-shrink: 0; font-size: 16px; margin-top: 0px;">
                        ℹ️
                    </div>
                    <div style="flex: 1;">
                        <p style="font-size: 12px; color: #1e40af; margin: 0; line-height: 1.5;">
                            Alle Fragen müssen zweimal in Folge richtig beantwortet werden, damit sie als gemeistert zählen.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Fragen Fortschrittsbalken - immer anzeigen -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Fragen gemeistert</span>
                    <span class="text-sm font-medium text-gray-700">{{ $progress }}/{{ $total }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                    <div id="progressBar" class="h-4 rounded-full shadow-lg" 
                         style="width: 0%; background-color: #facc15; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
                </div>
                <span class="text-sm text-gray-600">{{ $progressPercent }}% Gesamt-Fortschritt</span>
            </div>
            
            @if($progress < $total)
            <!-- Fragen üben Button über der Prüfungs-Info -->
            <div class="mb-4 text-center">
                <a href="{{ route('practice.menu') }}" 
                   style="display: inline-flex; align-items: center; padding: 12px 24px; background: linear-gradient(to right, #facc15, #f59e0b); color: #1e40af; font-size: 14px; font-weight: bold; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1); transition: all 0.3s ease; transform: scale(1);"
                   onmouseover="this.style.background='linear-gradient(to right, #f59e0b, #d97706)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4), 0 0 25px rgba(251, 191, 36, 0.4), 0 0 50px rgba(251, 191, 36, 0.2)'"
                   onmouseout="this.style.background='linear-gradient(to right, #facc15, #f59e0b)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1)'">
                    📚 Fragen üben
                </a>
            </div>
            
            <!-- Durchsichtiger Kasten für Prüfungen wenn noch Fragen offen -->
            <div class="mb-4" style="background-color: #eff6ff; border: 2px solid #3b82f6; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1);">
                <h3 class="text-base font-medium" style="color: #1e40af; margin-bottom: 8px;">🎓 Prüfungen</h3>
                <p class="text-sm" style="color: #1e40af; margin-bottom: 8px;">Sobald du alle Fragen einmal erfolgreich beantwortet hast, kannst du mit der Prüfungssimulation beginnen.</p>
                <p class="text-sm" style="color: #1e40af; margin-bottom: 16px;">Solltest du dennoch Prüfungen machen wollen, nutze den Gästemodus:</p>
                <a href="{{ route('guest.practice.menu') }}" 
                   style="display: inline-flex; align-items: center; padding: 12px 24px; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; font-size: 14px; font-weight: bold; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4); transition: all 0.3s ease; transform: scale(1);"
                   onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'; this.style.transform='scale(1.05)'"
                   onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1)'">
                    🚀 Gästemodus nutzen
                </a>
            </div>
            @else
            <!-- Prüfungs Fortschrittsbalken - nur anzeigen wenn alle Fragen beantwortet -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Prüfungen bestanden</span>
                    <span class="text-sm font-medium text-gray-700">{{ $exams }}/5</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                    <div id="examProgressBar" class="h-4 rounded-full shadow-lg" 
                         style="width: 0%; background-color: #2563eb; box-shadow: 0 0 10px rgba(37, 99, 235, 0.6), 0 0 20px rgba(37, 99, 235, 0.4), 0 0 30px rgba(37, 99, 235, 0.2);"></div>
                </div>
                <span class="text-sm text-gray-600">{{ $examsPercent }}% abgeschlossen</span>
            </div>
            
            <!-- Prüfungshistorie (Letzte 5 Prüfungen) - Aufklappbar -->
            @if(isset($recentExams) && $recentExams->count() > 0)
            <div class="mt-6">
                <!-- Aufklappbarer Header -->
                <button type="button" 
                        onclick="toggleExamHistory()"
                        class="w-full flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 border-2 border-blue-200 rounded-lg transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📊</span>
                        <span class="text-sm font-semibold text-gray-800">Deine letzten Prüfungen</span>
                        <span class="text-xs text-gray-600">({{ $recentExams->count() }})</span>
                    </div>
                    <svg id="examHistoryIcon" class="w-5 h-5 text-blue-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <!-- Aufklappbarer Content -->
                <div id="examHistoryContent" class="hidden mt-2 space-y-2">
                    @foreach($recentExams as $exam)
                        @php
                            $totalQuestions = 40; // Standard Prüfung hat 40 Fragen
                            $percentage = round(($exam->correct_answers / $totalQuestions) * 100);
                            $passed = $exam->is_passed;
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-lg border-2 transition-all duration-200 hover:shadow-lg hover:scale-[1.02]
                                    {{ $passed ? 'bg-green-50 border-green-200 hover:border-green-400' : 'bg-red-50 border-red-200 hover:border-red-400' }}">
                            <!-- Datum & Status -->
                            <div class="flex items-center gap-3 flex-1">
                                <div class="text-2xl">
                                    {{ $passed ? '✅' : '❌' }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium {{ $passed ? 'text-green-800' : 'text-red-800' }}">
                                        {{ $exam->created_at->format('d.m.Y') }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ $exam->created_at->format('H:i') }} Uhr
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ergebnis -->
                            <div class="text-right">
                                <div class="text-lg font-bold {{ $passed ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $percentage }}%
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ $exam->correct_answers }}/{{ $totalQuestions }}
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="ml-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                           {{ $passed ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                    {{ $passed ? 'Bestanden' : 'Durchgefallen' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Durchschnitt anzeigen -->
                @if($recentExams->count() > 0)
                    @php
                        $avgPercentage = round($recentExams->avg(function($exam) {
                            return ($exam->correct_answers / 40) * 100;
                        }));
                        $passRate = round(($recentExams->where('is_passed', true)->count() / $recentExams->count()) * 100);
                    @endphp
                    <div class="mt-3 p-3 bg-blue-50 border-2 border-blue-200 rounded-lg">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">📈 Durchschnitt:</span>
                            <span class="font-bold text-blue-800">{{ $avgPercentage }}%</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-700">✅ Erfolgsquote:</span>
                            <span class="font-bold text-blue-800">{{ $passRate }}%</span>
                        </div>
                    </div>
                @endif
            </div>
            @endif
            @endif
            
            @php
                $failedArr = is_array($user->exam_failed_questions ?? null) 
                    ? $user->exam_failed_questions 
                    : (is_string($user->exam_failed_questions) ? json_decode($user->exam_failed_questions, true) ?? [] : []);
                
                // Prüfungs-Status bestimmen
                if ($exams >= 5) {
                    $examStatus = 'green';
                    $examText = 'Geschafft! Du bist bereit zur Grundausbildung!';
                } elseif ($exams >= 3) {
                    $examStatus = 'yellow';
                    $examText = 'Fast am Ziel!';
                } else {
                    $examStatus = 'red';
                    $examText = 'Noch nicht bereit, mach weiter so';
                }
            @endphp
            
            @if($progress >= $total)
            <!-- Prüfungs-Status Box -->
            <div class="mt-4" style="
                @if($examStatus == 'red')
                    background-color: #fef2f2; border: 2px solid #ef4444; box-shadow: 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1);
                @elseif($examStatus == 'yellow')
                    background-color: #fffbeb; border: 2px solid #f59e0b; box-shadow: 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1);
                @else
                    background-color: #f0fdf4; border: 2px solid #22c55e; box-shadow: 0 0 20px rgba(34, 197, 94, 0.3), 0 0 40px rgba(34, 197, 94, 0.1);
                @endif
                border-radius: 12px; padding: 24px; text-align: center;">
                <p class="text-base font-medium" style="
                    @if($examStatus == 'red')
                        color: #dc2626;
                    @elseif($examStatus == 'yellow')
                        color: #d97706;
                    @else
                        color: #16a34a;
                    @endif
                    margin-bottom: 0;">
                    @if($examStatus == 'red')
                        🔥 Noch nicht bereit, mach weiter so!
                    @elseif($examStatus == 'yellow')
                        ⚡ Fast am Ziel!
                    @else
                        🎉 Geschafft! Du bist bereit zur Grundausbildung!
                    @endif
                </p>
            </div>
            
            <!-- Prüfungs-Button außerhalb der Status-Karte - nur anzeigen wenn keine Fehler zu wiederholen -->
            @if(!$failedArr || count($failedArr) == 0)
            <div class="mt-4 text-center">
                <a href="{{ route('exam.index') }}" 
                   style="display: inline-flex; align-items: center; padding: 12px 24px; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; font-size: 14px; font-weight: bold; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1); transition: all 0.3s ease; transform: scale(1);"
                   onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4), 0 0 25px rgba(37, 99, 235, 0.4), 0 0 50px rgba(37, 99, 235, 0.2)'"
                   onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1)'">
                    🎓 Prüfung starten
                </a>
            </div>
            @endif
            @endif
            
            @if($failedArr && count($failedArr) > 0)
            <!-- Fehler wiederholen Info-Kasten -->
            <div class="mt-4" style="background-color: #eff6ff; border: 2px solid #3b82f6; border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1);">
                <h3 class="text-base font-medium" style="color: #1e40af; margin-bottom: 8px;">🔄 Fehler zu wiederholen</h3>
                <p class="text-sm" style="color: #1e40af; margin-bottom: 8px;">Du hast <strong>{{ count($failedArr) }} offene Frage{{ count($failedArr) == 1 ? '' : 'n' }}</strong> aus deinen Prüfungen, die du noch beantworten musst.</p>
                <p class="text-sm" style="color: #1e40af; margin-bottom: 16px;">Bevor du eine neue Prüfung starten kannst, musst du diese Fehler lösen.</p>
                <a href="{{ route('failed.index') }}" 
                   style="display: inline-flex; align-items: center; padding: 12px 24px; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; font-size: 14px; font-weight: bold; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4); transition: all 0.3s ease; transform: scale(1);"
                   onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'; this.style.transform='scale(1.05)'"
                   onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1)'">
                    🔄 Fehler wiederholen
                </a>
            </div>
            @endif
            
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
                   class="block p-4 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-lg font-medium text-blue-800">📚 Übungsmenü</div>
                    <div class="text-sm text-gray-600">Gezieltes Üben nach Lernabschnitten</div>
                </a>
                
                <a href="{{ route('bookmarks.index') }}" 
                   class="block p-4 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-lg font-medium text-blue-800">🔖 Gespeicherte Fragen</div>
                    <div class="text-sm text-gray-600">Deine Lesezeichen und Favoriten</div>
                </a>
                
                <a href="{{ route('gamification.achievements') }}" 
                   class="block p-4 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-300 rounded-lg hover:from-purple-100 hover:to-purple-200 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-lg font-medium text-blue-800">🏆 Achievements</div>
                    <div class="text-sm text-gray-600">Deine Erfolge & Fortschritte</div>
                </a>
                
                <a href="{{ route('gamification.leaderboard') }}" 
                   class="block p-4 bg-gradient-to-r from-yellow-50 to-orange-100 border border-yellow-300 rounded-lg hover:from-yellow-100 hover:to-orange-200 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-lg font-medium text-blue-800">📊 Leaderboard</div>
                    <div class="text-sm text-gray-600">Wöchentliche & Gesamt-Rangliste</div>
                </a>
                
                @php
                    $failedArr = is_array($user->exam_failed_questions ?? null) 
                    ? $user->exam_failed_questions 
                    : (is_string($user->exam_failed_questions) ? json_decode($user->exam_failed_questions, true) ?? [] : []);
                    $disabledExam = $progress < $total || ($failedArr && count($failedArr));
                @endphp
                
                @if($failedArr && count($failedArr))
                    <a href="{{ route('failed.index') }}" 
                       class="block p-4 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                        <div class="text-lg font-medium text-blue-800">🔄 Fehler wiederholen</div>
                        <div class="text-sm text-gray-600">{{ count($failedArr) }} offene Fragen</div>
                    </a>
                @endif
                
                <a href="{{ $disabledExam ? '#' : route('exam.index') }}"
                   class="block p-4 rounded-lg transition-all duration-300 {{ $disabledExam ? 'bg-gray-100 border border-gray-300 cursor-not-allowed' : 'bg-blue-100 border border-blue-300 hover:bg-blue-200 hover:shadow-lg hover:scale-105 cursor-pointer' }}"
                   @if($disabledExam) aria-disabled="true" tabindex="-1" @endif>
                    <div class="text-lg font-medium {{ $disabledExam ? 'text-gray-500' : 'text-blue-800' }}">🎓 Zur Prüfung</div>
                    <div class="text-sm {{ $disabledExam ? 'text-gray-400' : 'text-gray-600' }}">
                        {{ $disabledExam ? 'Erst alle Fragen lösen' : 'Prüfungssimulation starten' }}
                    </div>
                </a>
                
                @if(Auth::user()->useroll === 'admin')
                    <a href="{{ route('admin.users.index') }}" 
                       class="block p-4 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                        <div class="text-lg font-medium text-blue-800">⚙️ Administration</div>
                        <div class="text-sm text-gray-600">Nutzer- und Fragenverwaltung</div>
                    </a>
                @endif
                
                <a href="{{ route('contact.index') }}" 
                   class="block p-4 bg-yellow-50 border border-yellow-300 rounded-lg hover:bg-yellow-100 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-lg font-medium text-blue-800">📬 Kontakt & Feedback</div>
                    <div class="text-sm text-gray-600">Fragen, Feedback oder Fehler melden</div>
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes emojiFall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        @keyframes progressPulse {
            0%, 100% {
                box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);
            }
            50% {
                box-shadow: 0 0 15px rgba(251, 191, 36, 0.8), 0 0 25px rgba(251, 191, 36, 0.6), 0 0 35px rgba(251, 191, 36, 0.4);
            }
        }
        
        @keyframes examProgressPulse {
            0%, 100% {
                box-shadow: 0 0 10px rgba(37, 99, 235, 0.6), 0 0 20px rgba(37, 99, 235, 0.4), 0 0 30px rgba(37, 99, 235, 0.2);
            }
            50% {
                box-shadow: 0 0 15px rgba(37, 99, 235, 0.8), 0 0 25px rgba(37, 99, 235, 0.6), 0 0 35px rgba(37, 99, 235, 0.4);
            }
        }
        
        .progress-pulse {
            animation: progressPulse 2s ease-in-out infinite;
        }
        
        .exam-progress-pulse {
            animation: examProgressPulse 2s ease-in-out infinite;
        }
    </style>

    <script>
        // Fortschrittsbalken Animation
        document.addEventListener('DOMContentLoaded', function() {
            // Fragen Fortschrittsbalken
            const progressBar = document.getElementById('progressBar');
            const targetProgress = {{ $progressPercent }};
            
            // Prüfungen Fortschrittsbalken
            const examProgressBar = document.getElementById('examProgressBar');
            const targetExamProgress = {{ $examsPercent }};
            
            // Berechne die Animationsdauer proportional zur Zielbreite
            // 1.5s für 100%, also proportional weniger für niedrigere Werte
            const animationDuration = (targetProgress / 100) * 1.5;
            const examAnimationDuration = (targetExamProgress / 100) * 1.5;
            
            // Animation startet nach 200ms Verzögerung
            setTimeout(() => {
                // Fragen Animation
                progressBar.style.transition = `width ${animationDuration}s ease-out`;
                progressBar.style.width = targetProgress + '%';
                
                // Prüfungen Animation (startet 300ms später für Stagger-Effekt)
                setTimeout(() => {
                    examProgressBar.style.transition = `width ${examAnimationDuration}s ease-out`;
                    examProgressBar.style.width = targetExamProgress + '%';
                }, 300);
            }, 200);
            
            // Emoji-Regen für grünen Prüfungsstatus (5+ Prüfungen)
            @if($exams >= 5)
            setTimeout(() => {
                createEmojiRain();
            }, 1000);
            @endif
        });
        
        function createEmojiRain() {
            const emojis = ['🎊', '🎉', '🥳'];
            const container = document.body;
            
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const emoji = document.createElement('div');
                    emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
                    emoji.style.position = 'fixed';
                    emoji.style.fontSize = '2rem';
                    emoji.style.left = Math.random() * 100 + 'vw';
                    emoji.style.top = '-50px';
                    emoji.style.zIndex = '9999';
                    emoji.style.pointerEvents = 'none';
                    emoji.style.animation = 'emojiFall 3s linear forwards';
                    
                    container.appendChild(emoji);
                    
                    // Emoji nach 3 Sekunden entfernen
                    setTimeout(() => {
                        if (emoji.parentNode) {
                            emoji.parentNode.removeChild(emoji);
                        }
                    }, 3000);
                }, i * 100);
            }
        }

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

        function dismissEmailConsentBanner() {
            // Banner ausblenden
            const banner = document.getElementById('email-consent-banner');
            if (banner) {
                banner.style.transition = 'opacity 0.3s ease-out';
                banner.style.opacity = '0';
                setTimeout(() => {
                    banner.style.display = 'none';
                }, 300);
            }
            
            // Session-Flag setzen via AJAX
            fetch('/dashboard/dismiss-email-consent-banner', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(error => {
                console.log('Banner dismissed (session update failed):', error);
            });
        }
        
        // Toggle Exam History
        function toggleExamHistory() {
            const content = document.getElementById('examHistoryContent');
            const icon = document.getElementById('examHistoryIcon');
            
            if (content.classList.contains('hidden')) {
                // Öffnen
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
                
                // Smooth scroll animation
                setTimeout(() => {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.opacity = '1';
                }, 10);
            } else {
                // Schließen
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                icon.style.transform = 'rotate(0deg)';
                
                setTimeout(() => {
                    content.classList.add('hidden');
                }, 300);
            }
        }
        
        // Toggle Progress Section
        function toggleProgress() {
            const content = document.getElementById('progressContent');
            const arrow = document.getElementById('progressArrow');
            
            if (content.style.display === 'none' || content.style.maxHeight === '0px') {
                // Öffnen
                content.classList.remove('hidden');
                content.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
                
                // Smooth scroll animation
                setTimeout(() => {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.opacity = '1';
                }, 10);
            } else {
                // Schließen
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                arrow.style.transform = 'rotate(0deg)';
                
                setTimeout(() => {
                    content.classList.add('hidden');
                }, 300);
            }
        }
    </script>
@endsection
