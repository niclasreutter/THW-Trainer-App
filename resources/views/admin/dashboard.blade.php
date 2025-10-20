@extends('layouts.app')

@section('title', 'Admin Dashboard - THW Trainer')
@section('description', 'Übersicht über System-Status, Benutzerstatistiken und Lernfortschritt')

@section('content')
<style>
    .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .gradient-green { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .gradient-purple { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
    .gradient-orange { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
    .card-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .hover-scale { transition: transform 0.2s ease-in-out; }
    .hover-scale:hover { transform: scale(1.02); }
    
    /* Runde Ecken für alle Karten */
    .rounded-xl { border-radius: 0.75rem !important; }
    .bg-white { border-radius: 0.75rem !important; }
    
    /* Spezifische runde Ecken für Admin Dashboard Karten */
    .admin-card { border-radius: 12px !important; }
    .admin-kpi-card { border-radius: 12px !important; }
    .admin-detail-card { border-radius: 12px !important; }
    .admin-action-card { border-radius: 12px !important; }
</style>

<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">Admin Dashboard</h1>
            <!-- System Status -->
            <div class="mb-12">
                <div class="flex items-center mb-4">
                    <i class="fas fa-wrench text-gray-600 mr-2"></i>
                    <h2 class="text-xl font-semibold text-gray-800">System Status</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <!-- Database Status -->
                    <div class="bg-white admin-card p-4 card-shadow flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Datenbank</p>
                            <p class="text-xs text-gray-500">{{ $systemStatus['database']['message'] }}</p>
                        </div>
                        <div class="w-3 h-3 rounded-full {{ $systemStatus['database']['status'] === 'ok' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    </div>
                    
                    <!-- Cache Status -->
                    <div class="bg-white admin-card p-4 card-shadow flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Cache</p>
                            <p class="text-xs text-gray-500">{{ $systemStatus['cache']['message'] }}</p>
                        </div>
                        <div class="w-3 h-3 rounded-full {{ $systemStatus['cache']['status'] === 'ok' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    </div>
                    
                    <!-- Storage Status -->
                    <div class="bg-white admin-card p-4 card-shadow flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Storage</p>
                            <p class="text-xs text-gray-500">{{ $systemStatus['storage']['message'] }}</p>
                        </div>
                        <div class="w-3 h-3 rounded-full {{ $systemStatus['storage']['status'] === 'ok' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    </div>
                    
                    <!-- Backup Status -->
                    <div class="bg-white admin-card p-4 card-shadow flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Backup</p>
                            <p class="text-xs text-gray-500">{{ $systemStatus['backup'] }}</p>
                        </div>
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <!-- Gesamt Benutzer -->
                <div class="admin-kpi-card p-6 text-white hover-scale cursor-pointer" 
                     style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Gesamt Benutzer</p>
                            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
                            <p class="text-blue-100 text-sm">+{{ $newUsersToday }} heute</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- E-Mail bestätigt -->
                <div class="admin-kpi-card p-6 text-white hover-scale cursor-pointer"
                     style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4), 0 0 20px rgba(34, 197, 94, 0.3), 0 0 40px rgba(34, 197, 94, 0.1);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">E-Mail bestätigt</p>
                            <p class="text-3xl font-bold">{{ $verifiedUsers }}</p>
                            <p class="text-green-100 text-sm">{{ $verificationRate }}% Rate</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Gesamt Fragen -->
                <div class="admin-kpi-card p-6 text-white hover-scale cursor-pointer"
                     style="background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%); color: #1e40af; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-800 text-sm font-medium">Gesamt Fragen</p>
                            <p class="text-3xl font-bold">{{ $totalQuestions }}</p>
                            <p class="text-blue-800 text-sm">{{ $learningSections }} Lernabschnitte</p>
                        </div>
                        <div class="bg-blue-800 bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-question-circle text-2xl text-blue-800"></i>
                        </div>
                    </div>
                </div>

                <!-- Beantwortete Fragen -->
                <div class="admin-kpi-card p-6 text-white hover-scale cursor-pointer"
                     style="background: linear-gradient(135deg, #00337F 0%, #002a66 100%); box-shadow: 0 4px 15px rgba(0, 51, 127, 0.4), 0 0 20px rgba(0, 51, 127, 0.3), 0 0 40px rgba(0, 51, 127, 0.1);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Beantwortete Fragen</p>
                            <p class="text-3xl font-bold">{{ number_format($totalAnsweredQuestions) }}</p>
                            <p class="text-blue-100 text-sm">{{ $wrongAnswerRate }}% Falsch</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-chart-bar text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Information Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
                <!-- Fragen-Statistik Details -->
                <div class="bg-white rounded-xl p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-chart-pie text-gray-600 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Fragen-Statistik</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Gesamt beantwortet</span>
                            <span class="font-semibold text-gray-900">{{ number_format($totalAnsweredQuestions) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Richtig</span>
                            <span class="font-semibold text-green-600">{{ number_format($totalCorrectAnswers) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Falsch</span>
                            <span class="font-semibold text-red-600">{{ number_format($totalWrongAnswers) }}</span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 font-medium">Erfolgsrate</span>
                                <span class="font-bold text-blue-600">{{ $totalAnsweredQuestions > 0 ? round((($totalCorrectAnswers / $totalAnsweredQuestions) * 100), 1) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Benutzer-Aktivität -->
                <div class="bg-white rounded-xl p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-chart-line text-gray-600 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Benutzer-Aktivität (30 Tage)</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Heute</span>
                            <span class="font-semibold text-gray-900">{{ $userActivity['today'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Diese Woche</span>
                            <span class="font-semibold text-gray-900">{{ $userActivity['this_week'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Diesen Monat</span>
                            <span class="font-semibold text-gray-900">{{ $userActivity['this_month'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Lernfortschritt -->
                <div class="bg-white rounded-xl p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-graduation-cap text-gray-600 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Lernfortschritt</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Gesamt Punkte</span>
                            <span class="font-semibold text-orange-500">{{ number_format($learningProgress['total_points']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Benutzer mit Erfolgen</span>
                            <span class="font-semibold text-gray-900">{{ $learningProgress['users_with_achievements'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Durchschnittlicher Fortschritt</span>
                            <span class="font-semibold text-blue-500">{{ $learningProgress['average_progress'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Leaderboard Section -->
            <div class="grid grid-cols-1 gap-6 mb-12">
                <!-- Leaderboard Top-10 -->
                <div class="bg-white rounded-xl p-6 card-shadow">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-trophy text-gray-600 mr-2"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Leaderboard Top-10</h3>
                    </div>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($leaderboard as $index => $user)
                            @php
                                $position = $index + 1;
                                $medal = match($position) {
                                    1 => '🥇',
                                    2 => '🥈', 
                                    3 => '🥉',
                                    default => $position . '.'
                                };
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl {{ $position <= 3 ? 'bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200' : 'bg-gray-50' }} hover:shadow-md transition-all duration-200">
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg font-bold {{ $position <= 3 ? 'text-yellow-600' : 'text-gray-600' }}">{{ $medal }}</span>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ $user['name'] }}</div>
                                        <div class="text-xs text-gray-500">Level {{ $user['level'] }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-gray-900 text-sm">{{ number_format($user['score']) }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $user['solved_questions'] }} Fragen • {{ $user['exam_passed'] }} Prüfungen
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-users text-4xl mb-2"></i>
                                <p>Noch keine Benutzer-Daten verfügbar</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl p-6 card-shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Schnellaktionen</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.questions.index') }}" 
                       class="flex items-center p-4 rounded-lg transition-all duration-300 hover:scale-105 flex-1 min-w-[200px]"
                       style="background: linear-gradient(to right, #facc15, #f59e0b); color: #1e40af; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1);">
                        <i class="fas fa-question-circle mr-3"></i>
                        <span class="font-medium">Fragen verwalten</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center p-4 rounded-lg transition-all duration-300 hover:scale-105 flex-1 min-w-[200px]"
                       style="background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1);">
                        <i class="fas fa-users mr-3"></i>
                        <span class="font-medium">Benutzer verwalten</span>
                    </a>
                    <a href="{{ route('admin.newsletter.create') }}" 
                       class="flex items-center p-4 rounded-lg transition-all duration-300 hover:scale-105 flex-1 min-w-[200px]"
                       style="background: linear-gradient(to right, #10b981, #059669); color: white; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4), 0 0 20px rgba(16, 185, 129, 0.3), 0 0 40px rgba(16, 185, 129, 0.1);">
                        <i class="fas fa-envelope mr-3"></i>
                        <span class="font-medium">Newsletter senden</span>
                    </a>
                    <a href="{{ route('admin.contact-messages.index') }}" 
                       class="flex items-center p-4 rounded-lg transition-all duration-300 hover:scale-105 flex-1 min-w-[200px]"
                       style="background: linear-gradient(to right, #f59e0b, #d97706); color: white; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4), 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1);">
                        <i class="fas fa-inbox mr-3"></i>
                        <span class="font-medium">Kontaktanfragen</span>
                        @php
                            $unreadCount = \App\Models\ContactMessage::where('is_read', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center p-4 rounded-lg transition-all duration-300 hover:scale-105 flex-1 min-w-[200px]"
                       style="background: linear-gradient(to right, #00337F, #002a66); color: white; box-shadow: 0 4px 15px rgba(0, 51, 127, 0.4), 0 0 20px rgba(0, 51, 127, 0.3), 0 0 40px rgba(0, 51, 127, 0.1);">
                        <i class="fas fa-arrow-left mr-3"></i>
                        <span class="font-medium">Zurück zum Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function takeScreenshot() {
            // Einfache Screenshot-Funktion
            if (navigator.userAgent.indexOf('Chrome') > -1) {
                alert('Verwenden Sie Strg+Shift+P und "Screenshot" für einen Screenshot');
            } else {
                alert('Verwenden Sie die Screenshot-Funktion Ihres Browsers');
            }
        }

        // Auto-refresh alle 30 Sekunden
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</div>
@endsection
