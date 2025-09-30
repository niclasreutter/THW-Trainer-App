@extends('layouts.app')
@section('title', 'Übungsmenü - THW Trainer')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">Übungsmenü</h1>
        
        <!-- Suchfeld -->
        <div class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">🔍 Fragen suchen</h2>
            <form action="{{ route('practice.search') }}" method="GET" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Suchbegriff eingeben..." 
                       class="flex-1 px-4 py-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        style="background-color: #2563eb; color: white; padding: 0.5rem 1.5rem; border-radius: 0.5rem; border: none; cursor: pointer;">
                    Suchen
                </button>
            </form>
        </div>

        <!-- Alle Fragen Modus -->
        <div class="mb-12 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">📚 Alle Fragen</h2>
            
            <!-- Statistiken anzeigen -->
            <div class="mb-6">
                <div class="flex flex-wrap gap-3 lg:gap-4">
                    <!-- Fehlgeschlagene Fragen -->
                    <div class="flex-1 min-w-[140px] lg:min-w-[160px] max-w-[calc(33.333%-8px)] lg:max-w-none flex items-center bg-gradient-to-r from-red-100 to-pink-100 rounded-lg px-3 lg:px-4 py-3 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer border border-red-200">
                        <div class="text-lg lg:text-xl mr-2 lg:mr-3 flex-shrink-0">❌</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-lg lg:text-xl font-bold text-red-800 truncate">{{ $failedCount }}</div>
                            <div class="text-xs lg:text-sm text-red-600 font-medium">Fehlgeschlagen</div>
                            <!-- Progress Bar -->
                            @php
                                $failedProgressPercent = $totalQuestions > 0 ? ($failedCount / $totalQuestions) * 100 : 0;
                            @endphp
                            <div class="w-full bg-red-200 rounded-full h-1 mt-2">
                                <div class="bg-red-500 h-1 rounded-full transition-all duration-500" style="width: {{ $failedProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Ungelöste Fragen -->
                    <div class="flex-1 min-w-[140px] lg:min-w-[160px] max-w-[calc(33.333%-8px)] lg:max-w-none flex items-center bg-gradient-to-r from-blue-100 to-indigo-100 rounded-lg px-3 lg:px-4 py-3 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer border border-blue-200">
                        <div class="text-lg lg:text-xl mr-2 lg:mr-3 flex-shrink-0">❓</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-lg lg:text-xl font-bold text-blue-800 truncate">{{ $unsolvedCount }}</div>
                            <div class="text-xs lg:text-sm text-blue-600 font-medium">Ungelöst</div>
                            <!-- Progress Bar -->
                            @php
                                $unsolvedProgressPercent = $totalQuestions > 0 ? ($unsolvedCount / $totalQuestions) * 100 : 0;
                            @endphp
                            <div class="w-full bg-blue-200 rounded-full h-1 mt-2">
                                <div class="bg-blue-500 h-1 rounded-full transition-all duration-500" style="width: {{ $unsolvedProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Gelöste Fragen -->
                    <div class="flex-1 min-w-[140px] lg:min-w-[160px] max-w-[calc(33.333%-8px)] lg:max-w-none flex items-center bg-gradient-to-r from-green-100 to-emerald-100 rounded-lg px-3 lg:px-4 py-3 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer border border-green-200">
                        <div class="text-lg lg:text-xl mr-2 lg:mr-3 flex-shrink-0">✅</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-lg lg:text-xl font-bold text-green-800 truncate">{{ $solvedCount }}</div>
                            <div class="text-xs lg:text-sm text-green-600 font-medium">Gelöst</div>
                            <!-- Progress Bar -->
                            @php
                                $solvedProgressPercent = $totalQuestions > 0 ? ($solvedCount / $totalQuestions) * 100 : 0;
                            @endphp
                            <div class="w-full bg-green-200 rounded-full h-1 mt-2">
                                <div class="bg-green-500 h-1 rounded-full transition-all duration-500" style="width: {{ $solvedProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($failedCount > 0 || $unsolvedCount > 0)
                <p class="text-gray-600 mb-4">
                    <strong>Intelligente Priorisierung:</strong> 
                    @if($failedCount > 0)
                        Zuerst werden {{ $failedCount }} fehlgeschlagene Fragen geübt, 
                    @endif
                    @if($unsolvedCount > 0)
                        dann {{ $unsolvedCount }} ungelöste Fragen.
                    @endif
                </p>
            @else
                <p class="text-gray-600 mb-4">
                    <strong>Alle Fragen gemeistert!</strong> Jetzt kannst du alle Fragen in zufälliger Reihenfolge wiederholen.
                </p>
            @endif
            
            <div class="max-w-md mx-auto">
                <a href="{{ route('practice.all') }}" 
                   class="block p-6 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 hover:shadow-lg hover:scale-105 transition-all duration-300"
                   style="background: linear-gradient(to right, #facc15, #f59e0b); color: #1e40af; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1);">
                    <div class="flex items-center">
                        <div class="text-2xl mr-3">🎯</div>
                        <div class="flex-1">
                            <div class="text-lg font-bold">
                                @if($failedCount > 0 || $unsolvedCount > 0)
                                    Prioritäts-Training starten
                                @else
                                    Alle Fragen wiederholen
                                @endif
                            </div>
                            <div class="text-sm opacity-90">
                                @if($failedCount > 0)
                                    Schwierige Fragen zuerst
                                @elseif($unsolvedCount > 0)
                                    Ungelöste Fragen zuerst
                                @else
                                    Zufällige Reihenfolge
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Lernabschnitte -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-blue-800 mb-2">📖 Lernabschnitte</h2>
                <p class="text-gray-600">Übe gezielt nach Themengebieten strukturiert.</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach(range(1, 10) as $section)
                    @php
                        $totalQuestions = $sectionStats[$section]['total'] ?? 0;
                        $solvedQuestions = $sectionStats[$section]['solved'] ?? 0;
                        $progressPercent = $totalQuestions > 0 ? round(($solvedQuestions / $totalQuestions) * 100) : 0;
                        
                        // Lernabschnittsname aus Controller
                        $sectionName = $sectionNames[$section] ?? "Abschnitt $section";
                    @endphp
                    
                    <a href="{{ route('practice.section', $section) }}" 
                       class="block p-4 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 hover:shadow-lg hover:scale-105 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="text-2xl font-bold text-blue-800 mr-4 flex-shrink-0">{{ $section }}</div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-blue-700 mb-1 leading-tight">{{ $sectionName }}</div>
                                <div class="text-xs text-gray-600 mb-2">
                                    {{ $solvedQuestions }}/{{ $totalQuestions }} Fragen
                                </div>
                                
                                <!-- Fortschrittsbalken mit Glow-Effekt wie im Dashboard -->
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                    <div id="progressBar{{ $section }}" class="h-1 rounded-full shadow-lg" 
                                         style="width: 0%; background-color: #facc15; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
                                </div>
                                
                                <div class="text-xs text-gray-500 mt-1">{{ $progressPercent }}%</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Zurück zum Dashboard -->
        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>

    <script>
        // Fortschrittsbalken Animation wie im Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Alle Lernabschnitt-Fortschrittsbalken animieren
            @foreach(range(1, 10) as $section)
                @php
                    $totalQuestions = $sectionStats[$section]['total'] ?? 0;
                    $solvedQuestions = $sectionStats[$section]['solved'] ?? 0;
                    $progressPercent = $totalQuestions > 0 ? round(($solvedQuestions / $totalQuestions) * 100) : 0;
                @endphp
                
                const progressBar{{ $section }} = document.getElementById('progressBar{{ $section }}');
                const targetProgress{{ $section }} = {{ $progressPercent }};
                
                // Berechne die Animationsdauer proportional zur Zielbreite
                const animationDuration{{ $section }} = (targetProgress{{ $section }} / 100) * 1.5;
                
                // Animation startet nach 200ms Verzögerung + Stagger-Effekt
                setTimeout(() => {
                    progressBar{{ $section }}.style.transition = `width ${animationDuration{{ $section }}}s ease-out`;
                    progressBar{{ $section }}.style.width = targetProgress{{ $section }} + '%';
                }, 200 + ({{ $section }} * 100)); // Stagger-Effekt: 100ms pro Abschnitt
            @endforeach
        });
    </script>

@endsection
