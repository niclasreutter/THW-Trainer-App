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
            <div class="mb-4">
                <div class="flex flex-wrap gap-2 lg:gap-3">
                    <!-- Fehlgeschlagene Fragen -->
                    <div class="flex-1 min-w-[120px] lg:min-w-[140px] max-w-[calc(33.333%-4px)] lg:max-w-none flex items-center bg-gradient-to-r from-red-100 to-pink-100 rounded-lg px-2 lg:px-3 py-2 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                        <div class="text-base lg:text-lg mr-1 lg:mr-2 flex-shrink-0">❌</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm lg:text-base font-bold text-red-800 truncate">{{ $failedCount }}</div>
                            <div class="text-xs text-red-600">Fehlgeschlagen</div>
                            <!-- Progress Bar -->
                            @php
                                $failedProgressPercent = $totalQuestions > 0 ? ($failedCount / $totalQuestions) * 100 : 0;
                            @endphp
                            <div class="w-full bg-red-200 rounded-full h-1 mt-1">
                                <div class="bg-red-500 h-1 rounded-full transition-all duration-500" style="width: {{ $failedProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Ungelöste Fragen -->
                    <div class="flex-1 min-w-[120px] lg:min-w-[140px] max-w-[calc(33.333%-4px)] lg:max-w-none flex items-center bg-gradient-to-r from-blue-100 to-indigo-100 rounded-lg px-2 lg:px-3 py-2 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                        <div class="text-base lg:text-lg mr-1 lg:mr-2 flex-shrink-0">❓</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm lg:text-base font-bold text-blue-800 truncate">{{ $unsolvedCount }}</div>
                            <div class="text-xs text-blue-600">Ungelöst</div>
                            <!-- Progress Bar -->
                            @php
                                $unsolvedProgressPercent = $totalQuestions > 0 ? ($unsolvedCount / $totalQuestions) * 100 : 0;
                            @endphp
                            <div class="w-full bg-blue-200 rounded-full h-1 mt-1">
                                <div class="bg-blue-500 h-1 rounded-full transition-all duration-500" style="width: {{ $unsolvedProgressPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Gelöste Fragen -->
                    <div class="flex-1 min-w-[120px] lg:min-w-[140px] max-w-[calc(33.333%-4px)] lg:max-w-none flex items-center bg-gradient-to-r from-green-100 to-emerald-100 rounded-lg px-2 lg:px-3 py-2 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                        <div class="text-base lg:text-lg mr-1 lg:mr-2 flex-shrink-0">✅</div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm lg:text-base font-bold text-green-800 truncate">{{ $solvedCount }}</div>
                            <div class="text-xs text-green-600">Gelöst</div>
                            <!-- Progress Bar -->
                            @php
                                $solvedProgressPercent = $totalQuestions > 0 ? ($solvedCount / $totalQuestions) * 100 : 0;
                            @endphp
                            <div class="w-full bg-green-200 rounded-full h-1 mt-1">
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
                   class="block p-4 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 transition-colors">
                    <div class="text-lg font-medium text-blue-800">
                        🎯 
                        @if($failedCount > 0 || $unsolvedCount > 0)
                            Prioritäts-Training starten
                        @else
                            Alle Fragen wiederholen
                        @endif
                    </div>
                    <div class="text-sm text-gray-600">
                        @if($failedCount > 0)
                            Schwierige Fragen zuerst
                        @elseif($unsolvedCount > 0)
                            Ungelöste Fragen zuerst
                        @else
                            Zufällige Reihenfolge
                        @endif
                    </div>
                </a>
            </div>
        </div>

        <!-- Lernabschnitte -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSections()">
                <div>
                    <h2 class="text-xl font-semibold text-blue-800 mb-2">📖 Lernabschnitte</h2>
                    <p class="text-gray-600">Übe gezielt nach Themengebieten strukturiert.</p>
                </div>
                <div class="ml-4">
                    <svg id="sectionsArrow" class="w-6 h-6 text-blue-800 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
            
            <div id="sectionsContent" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                @foreach(range(1, 10) as $section)
                    @php
                        $totalQuestions = $sectionStats[$section]['total'] ?? 0;
                        $solvedQuestions = $sectionStats[$section]['solved'] ?? 0;
                        $progressPercent = $totalQuestions > 0 ? round(($solvedQuestions / $totalQuestions) * 100) : 0;
                        
                        // Lernabschnittsname aus Controller
                        $sectionName = $sectionNames[$section] ?? "Abschnitt $section";
                    @endphp
                    
                    <a href="{{ route('practice.section', $section) }}" 
                       class="block p-6 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-800 mb-3">{{ $section }}</div>
                            <div class="text-sm font-medium text-blue-700 mb-4 leading-relaxed min-h-[3.5rem] flex items-center justify-center px-2">{{ $sectionName }}</div>
                            <div class="text-sm text-gray-600 mb-3">
                                {{ $solvedQuestions }}/{{ $totalQuestions }} Fragen
                            </div>
                            
                            <!-- Fortschrittsbalken -->
                            <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                                <div class="bg-yellow-400 h-4 rounded-full transition-all duration-300" 
                                     style="width: {{ $progressPercent }}%"></div>
                            </div>
                            
                            <div class="text-xs text-gray-500">{{ $progressPercent }}%</div>
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
        function toggleSections() {
            const content = document.getElementById('sectionsContent');
            const arrow = document.getElementById('sectionsArrow');
            
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
