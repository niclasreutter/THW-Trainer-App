@extends('layouts.app')

@section('title', 'Lehrgänge')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6">
    <div class="mb-8 sm:mb-12">
        <h1 class="text-3xl sm:text-4xl font-bold text-blue-800">{{ __('Lehrgänge') }}</h1>
        <p class="text-gray-600 mt-2 text-sm sm:text-base">{{ __('Wähle einen Lehrgang, um dein Wissen zu erweitern') }}</p>
    </div>

    @if($lehrgaenge->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-gray-600">{{ __('Noch keine Lehrgänge verfügbar') }}</p>
        </div>
    @else
        @php
            $count = $lehrgaenge->count();
            // Grid-Klassen für Layout basierend auf Anzahl der Lehrgänge
            if ($count == 1) {
                $gridClass = 'grid-cols-1 max-w-2xl';
            } elseif ($count == 2) {
                $gridClass = 'grid-cols-1 sm:grid-cols-2';
            } else {
                $gridClass = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
            }
        @endphp
        <div class="grid {{ $gridClass }} gap-4 sm:gap-6 mx-auto">
            @foreach($lehrgaenge as $lehrgang)
                @php
                    $isEnrolled = in_array($lehrgang->id, $enrolledIds);
                    // Berechne Fortschritt
                    if ($isEnrolled) {
                        $solvedCount = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                            ->where('solved', true)
                            ->count();
                        $totalCount = \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->count();
                        
                        // Neue Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
                        $progressData = \App\Models\UserLehrgangProgress::where('user_id', auth()->id())
                            ->whereHas('lehrgangQuestion', fn($q) => $q->where('lehrgang_id', $lehrgang->id))
                            ->get();
                        
                        $totalProgressPoints = 0;
                        foreach ($progressData as $prog) {
                            $totalProgressPoints += min($prog->consecutive_correct, 2);
                        }
                        $maxProgressPoints = $totalCount * 2;
                        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
                        
                        $isCompleted = $progressPercent == 100 && $solvedCount > 0;
                    }
                @endphp
                
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col h-full">
                    <div class="p-5 sm:p-6 flex flex-col flex-grow">
                        <!-- Header mit Status -->
                        <div class="mb-4">
                            <div class="flex justify-between items-start mb-3">
                                <h2 class="text-lg sm:text-xl font-bold text-gray-900 flex-1 pr-3">{{ $lehrgang->lehrgang }}</h2>
                                @if($isEnrolled)
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full flex-shrink-0" style="box-shadow: 0 0 15px rgba(16, 185, 129, 0.4), 0 0 25px rgba(16, 185, 129, 0.2), 0 0 40px rgba(16, 185, 129, 0.1);">
                                        ✓ {{ __('Eingeschrieben') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 text-xs sm:text-sm line-clamp-2">{{ $lehrgang->beschreibung }}</p>
                        </div>

                        <!-- Statistiken -->
                        @php
                            $questionCount = $lehrgang->questions()->count();
                            $sectionCount = $lehrgang->questions()->distinct('lernabschnitt')->count('lernabschnitt');
                        @endphp
                        <div class="grid grid-cols-2 gap-3 mb-4 py-3 border-y border-gray-100">
                            <div>
                                <p class="text-xs text-gray-600">{{ __('Fragen') }}</p>
                                <p class="text-lg sm:text-xl font-bold text-blue-600">{{ $questionCount }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">{{ __('Abschnitte') }}</p>
                                <p class="text-lg sm:text-xl font-bold text-purple-600">{{ $sectionCount }}</p>
                            </div>
                        </div>

                        <!-- Progress Bar (nur wenn eingeschrieben) -->
                        @if($isEnrolled)
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-xs text-gray-600 font-semibold">{{ $solvedCount }}/{{ $totalCount }} Fragen</p>
                                    <p class="text-xs text-gray-600 font-semibold">{{ $progressPercent }}%</p>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full transition-all duration-500" 
                                         style="width: {{ $progressPercent }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
                                </div>
                            </div>
                        @endif

                        <!-- Spacer für flexible Height -->
                        <div class="flex-grow"></div>

                        <!-- Buttons -->
                        <div class="flex flex-col gap-2 mt-4">
                            <a href="{{ route('lehrgaenge.show', $lehrgang->slug) }}" 
                               style="display: flex; align-items: center; justify-content: center; padding: 12px 16px; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; font-size: 14px; font-weight: bold; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4); transition: all 0.3s ease; transform: scale(1);"
                               onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4), 0 0 25px rgba(37, 99, 235, 0.4), 0 0 50px rgba(37, 99, 235, 0.2)'"
                               onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4)'">
                                ℹ️ Details
                            </a>
                            
                            @if($isEnrolled)
                                @if($isCompleted)
                                    <!-- Abgeschlossen Button (Grün mit Glow) -->
                                    <div style="display: flex; align-items: center; justify-content: center; padding: 12px 16px; background: linear-gradient(to right, #10b981, #059669); color: white; font-size: 14px; font-weight: bold; border-radius: 8px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4), 0 0 20px rgba(16, 185, 129, 0.3), 0 0 40px rgba(16, 185, 129, 0.1); transition: all 0.3s ease; transform: scale(1);"
                                        onmouseover="this.style.background='linear-gradient(to right, #059669, #047857)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.4), 0 0 25px rgba(16, 185, 129, 0.4), 0 0 50px rgba(16, 185, 129, 0.2)'"
                                        onmouseout="this.style.background='linear-gradient(to right, #10b981, #059669)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.4), 0 0 20px rgba(16, 185, 129, 0.3), 0 0 40px rgba(16, 185, 129, 0.1)'">
                                        ✓ Abgeschlossen
                                    </div>
                                @else
                                    <!-- Weitermachen Button (Gelb wie Üben) -->
                                    <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" 
                                       style="display: flex; align-items: center; justify-content: center; padding: 12px 16px; background: linear-gradient(to right, #facc15, #f59e0b); color: #1e40af; font-size: 14px; font-weight: bold; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1); transition: all 0.3s ease; transform: scale(1);"
                                       onmouseover="this.style.background='linear-gradient(to right, #f59e0b, #d97706)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4), 0 0 25px rgba(251, 191, 36, 0.4), 0 0 50px rgba(251, 191, 36, 0.2)'"
                                       onmouseout="this.style.background='linear-gradient(to right, #facc15, #f59e0b)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1)'">
                                        📚 Weitermachen
                                    </a>
                                @endif
                            @else
                                <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST" style="width: 100%;">
                                    @csrf
                                    <button type="submit" 
                                            style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 12px 16px; background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; font-size: 14px; font-weight: bold; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4); transition: all 0.3s ease; transform: scale(1);"
                                            onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4), 0 0 25px rgba(37, 99, 235, 0.4), 0 0 50px rgba(37, 99, 235, 0.2)'"
                                            onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4)'">
                                        ✨ Beitreten
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
