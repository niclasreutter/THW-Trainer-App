@extends('layouts.app')

@section('title', 'Anonym üben - THW Theorie ohne Anmeldung')
@section('description', 'THW Theorie anonym üben ohne Anmeldung. Wähle aus verschiedenen Übungsmodi und starte sofort mit dem Lernen. Kostenlos und ohne Registrierung.')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">THW-Trainer - Anonym üben</h1>
        
        <!-- Info Sektion -->
        <div class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">👋 Anonym üben</h2>
            
            <!-- Anonym Info -->
            <div class="mb-6 p-4 rounded-lg shadow-md" style="background-color: #fef3c7; border: 2px solid #f59e0b;">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 mt-0.5" style="color: #92400e;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium" style="color: #78350f;">Anonymes Üben</h3>
                        <div class="mt-1 text-sm" style="color: #92400e;">
                            <p>Du übst aktuell <strong>anonym</strong> ohne Account. Deine Fortschritte werden <strong>nicht gespeichert</strong> und es ist keine gezielte Wiederholung möglich.</p>
                            <p class="mt-2">Für vollständige Funktionen wie Fortschrittsverfolgung, Lesezeichen und Prüfungsergebnisse erstelle einen <strong>kostenlosen Account</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account erstellen Hinweis -->
            <div style="background-color: #eff6ff; border: 2px solid #3b82f6; border-radius: 12px; padding: 24px; text-align: center;">
                <h3 style="font-size: 18px; font-weight: bold; color: #1e40af; margin-bottom: 12px;">Vollständige Funktionen</h3>
                <p style="color: #1e40af; margin-bottom: 16px;">Erstelle einen kostenlosen Account für:</p>
                <ul style="color: #1e40af; text-align: left; display: inline-block; margin-bottom: 16px;">
                    <li>Fortschrittsverfolgung</li>
                    <li>Lesezeichen für schwierige Fragen</li>
                    <li>Gezielte Wiederholung</li>
                    <li>Prüfungsergebnisse speichern</li>
                    <li>Gamification und Achievements</li>
                </ul>
                <br>
                <a href="{{ route('register') }}" 
                   style="display: inline-flex; align-items: center; padding: 16px 32px; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; font-size: 18px; font-weight: bold; border-radius: 12px; text-decoration: none; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4); transition: all 0.3s ease; transform: scale(1);"
                   onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'; this.style.transform='scale(1.05)'"
                   onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1)'">
                    <svg style="width: 24px; height: 24px; margin-right: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    🚀 Kostenlos registrieren
                </a>
            </div>
        </div>

        <!-- Navigation Sektion -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleLearning()">
                <h2 class="text-xl font-semibold text-blue-800">🚀 Jetzt üben</h2>
                <svg id="learningArrow" class="w-6 h-6 text-blue-800 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            
            <div id="learningContent" class="mt-6 grid gap-4" style="display: none;">
                <a href="{{ route('guest.practice.all') }}" 
                   class="block p-4 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 transition-colors">
                    <div class="text-lg font-medium text-blue-800">📚 Alle Fragen üben</div>
                    <div class="text-sm text-gray-600">Alle THW-Theoriefragen nach Lernabschnitten</div>
                </a>
                
                <a href="{{ route('guest.exam.index') }}" 
                   class="block p-4 bg-blue-100 border border-blue-300 rounded-lg hover:bg-blue-200 transition-colors">
                    <div class="text-lg font-medium text-blue-800">🎓 Prüfungssimulation</div>
                    <div class="text-sm text-gray-600">30 zufällige Fragen unter Prüfungsbedingungen</div>
                </a>
            </div>
        </div>
        
        <!-- Zurück zur Homepage -->
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Zurück zur Startseite
            </a>
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
