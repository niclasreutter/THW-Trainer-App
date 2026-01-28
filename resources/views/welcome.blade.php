@extends('layouts.app')

@section('content')
<!-- CACHE BUST v1.0 - COMPACT WELCOME - 2025-10-20-20:45 -->
    <div class="py-6 sm:py-8">
        <div class="max-w-4xl mx-auto mt-4 sm:mt-6 p-4 sm:p-6 bg-white rounded-lg shadow-lg">
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">THW Trainer</h1>
                <p class="text-base sm:text-lg text-gray-600 mb-4 sm:mb-6">Bereite dich optimal auf deine THW-Prüfung vor!<br>Übe mit realistischen Fragen und verfolge deinen Fortschritt.</p>
                
                <div class="grid md:grid-cols-2 gap-4 sm:gap-6 max-w-2xl mx-auto">
                    @guest
                        <div class="bg-blue-50 p-4 sm:p-5 rounded-lg border border-blue-200">
                            <h3 class="text-lg sm:text-xl font-semibold text-blue-900 mb-2 sm:mb-3">Anmelden & Üben</h3>
                            <p class="text-sm sm:text-base text-blue-700 mb-3 sm:mb-4">Erstelle einen kostenlosen Account und nutze alle Funktionen:</p>
                            <ul class="text-left text-sm sm:text-base text-blue-600 space-y-1.5 sm:space-y-2 mb-4 sm:mb-5">
                                <li>• Persönlicher Lernfortschritt</li>
                                <li>• Schwierige Fragen markieren</li>
                                <li>• Statistiken und Erfolge</li>
                                <li>• Gamification & Belohnungen</li>
                            </ul>
                            <div class="space-y-2 sm:space-y-3">
                                <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white font-bold px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg hover:bg-blue-700 text-center transition-colors text-sm sm:text-base">
                                    Anmelden
                                </a>
                                <a href="{{ route('register') }}" class="block w-full bg-green-600 text-white font-bold px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg hover:bg-green-700 text-center transition-colors text-sm sm:text-base">
                                    Registrieren
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 sm:p-5 rounded-lg border border-yellow-200">
                            <h3 class="text-lg sm:text-xl font-semibold text-yellow-900 mb-2 sm:mb-3">Anonym üben</h3>
                            <p class="text-sm sm:text-base text-yellow-700 mb-3 sm:mb-4">Sofort starten ohne Anmeldung:</p>
                            <ul class="text-left text-sm sm:text-base text-yellow-600 space-y-1.5 sm:space-y-2 mb-4 sm:mb-5">
                                <li>• Alle Fragen verfügbar</li>
                                <li>• Sofortiger Start</li>
                                <li>• Keine Registrierung nötig</li>
                                <li>• Perfekt zum Testen</li>
                            </ul>
                            <a href="{{ route('landing.guest.practice.menu') }}" class="block w-full bg-yellow-600 text-white font-bold px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg hover:bg-yellow-700 text-center transition-colors text-sm sm:text-base">
                                Jetzt üben
                            </a>
                        </div>
                    @else
                        <div class="bg-green-50 p-4 sm:p-5 rounded-lg border border-green-200 col-span-2">
                            <h3 class="text-lg sm:text-xl font-semibold text-green-900 mb-2 sm:mb-3">Willkommen zurück!</h3>
                            <p class="text-sm sm:text-base text-green-700 mb-4 sm:mb-5">Du bist bereits angemeldet. Starte direkt mit dem Üben!</p>
                            <a href="{{ route('dashboard') }}" class="inline-block bg-green-600 text-white font-bold px-6 sm:px-8 py-2 sm:py-2.5 rounded-lg hover:bg-green-700 text-center transition-colors text-sm sm:text-base">
                                Zum Dashboard
                            </a>
                        </div>
                    @endguest
                </div>
                
                <div class="mt-8 sm:mt-10 text-center">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-3 sm:mb-4">Warum THW Trainer?</h2>
                    <div class="grid md:grid-cols-3 gap-4 sm:gap-6 text-left">
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-1.5 sm:mb-2 text-sm sm:text-base">📚 Umfangreiche Fragen</h3>
                            <p class="text-gray-600 text-xs sm:text-sm">Hunderte von realistischen THW-Fragen aus allen Bereichen</p>
                        </div>
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-1.5 sm:mb-2 text-sm sm:text-base">📊 Lernfortschritt</h3>
                            <p class="text-gray-600 text-xs sm:text-sm">Verfolge deine Fortschritte und identifiziere Schwachstellen</p>
                        </div>
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-1.5 sm:mb-2 text-sm sm:text-base">🎯 Personalisiert</h3>
                            <p class="text-gray-600 text-xs sm:text-sm">Adaptive Lernmethoden für optimale Vorbereitung</p>
                        </div>
                    </div>
                    
                    <!-- PWA Feature Highlight -->
                    <div class="mt-6 bg-gradient-to-r from-blue-50 to-purple-50 p-4 sm:p-6 rounded-lg border-2 border-blue-200">
                        <div class="flex items-center justify-center gap-3 mb-3">
                            <span class="text-3xl">📲</span>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Als App installierbar!</h3>
                        </div>
                        <p class="text-center text-sm sm:text-base text-gray-700 mb-4">
                            Installiere THW Trainer als App auf deinem Smartphone oder Tablet für noch schnelleren Zugriff – funktioniert auch offline!
                        </p>
                        <div class="grid sm:grid-cols-3 gap-3 text-center text-xs sm:text-sm">
                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                <span class="block text-2xl mb-1">⚡</span>
                                <span class="font-semibold text-gray-800">Schneller Start</span>
                            </div>
                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                <span class="block text-2xl mb-1">📴</span>
                                <span class="font-semibold text-gray-800">Offline nutzbar</span>
                            </div>
                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                <span class="block text-2xl mb-1">🏠</span>
                                <span class="font-semibold text-gray-800">Auf Homescreen</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection