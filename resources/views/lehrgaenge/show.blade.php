@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-start mb-4">
            <a href="{{ route('lehrgaenge.index') }}" class="text-blue-600 hover:text-blue-700 inline-block font-semibold">
                ← {{ __('Zurück zu Lehrgängen') }}
            </a>
            @if($isEnrolled)
                <form action="{{ route('lehrgaenge.unenroll', $lehrgang->slug) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" 
                            class="text-red-600 hover:text-red-700 font-semibold text-sm inline-block"
                            onclick="return confirm('{{ __('Möchtest du dich wirklich abmelden?') }}')">
                        🚪 {{ __('Lehrgang verlassen') }}
                    </button>
                </form>
            @endif
        </div>
        
        <h1 class="text-3xl sm:text-4xl font-bold text-blue-800 mb-2">{{ $lehrgang->lehrgang }}</h1>
        <p class="text-gray-600 text-sm sm:text-base">{{ $lehrgang->beschreibung }}</p>
    </div>

    @if($isEnrolled)
        <!-- User ist eingeschrieben -->
        
        <!-- Fortschritt und Statistiken -->
        <div class="mb-8 sm:mb-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <!-- Gelöste Fragen -->
                <div class="flex items-center bg-gradient-to-r from-green-100 to-emerald-100 rounded-lg px-4 py-3 sm:py-4 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-green-200">
                    <div class="text-2xl sm:text-3xl mr-4 flex-shrink-0">✅</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-lg sm:text-2xl font-bold text-green-800">{{ $userProgress['solved'] ?? 0 }}/{{ $userProgress['total'] ?? 0 }}</div>
                        <div class="text-xs sm:text-sm text-green-600 font-medium">{{ __('Fragen gelöst') }}</div>
                        @if(($userProgress['total'] ?? 0) > 0)
                            <div class="w-full bg-green-200 rounded-full h-2 mt-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ (($userProgress['solved'] ?? 0) / ($userProgress['total'] ?? 1)) * 100 }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Lernabschnitte -->
                <div class="flex items-center bg-gradient-to-r from-blue-100 to-indigo-100 rounded-lg px-4 py-3 sm:py-4 hover:shadow-lg hover:scale-105 transition-all duration-300 border border-blue-200">
                    <div class="text-2xl sm:text-3xl mr-4 flex-shrink-0">📚</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-lg sm:text-2xl font-bold text-blue-800">{{ $questions->count() }}</div>
                        <div class="text-xs sm:text-sm text-blue-600 font-medium">{{ __('Lernabschnitte') }}</div>
                        <div class="text-xs text-blue-500 mt-1">{{ $questions->sum(fn($group) => $group->count()) }} {{ __('Fragen insgesamt') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lernabschnitte Übersicht -->
        <div class="mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-blue-800 mb-4">{{ __('Lernabschnitte') }}</h2>
            
            @if($questions->isEmpty())
                <p class="text-gray-600">{{ __('Noch keine Fragen vorhanden') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($questions as $section => $sectionQuestions)
                        @php
                            $progress = $sectionProgress[$section] ?? null;
                        @endphp
                        <a href="{{ route('lehrgaenge.practice-section', ['slug' => $lehrgang->slug, 'sectionNr' => $section]) }}" class="p-4 sm:p-5 bg-white border border-gray-200 rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900 text-base sm:text-lg">{{ $lernabschnitte->get($section, 'Lernabschnitt ' . $section) }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $sectionQuestions->count() }} {{ __('Fragen') }}</p>
                                </div>
                                <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold">{{ $section }}</span>
                            </div>
                            
                            <!-- Fortschrittsbalken (nur wenn eingeschrieben) -->
                            @if($progress)
                                <div class="border-t border-gray-100 pt-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-xs text-gray-600 font-semibold">{{ $progress['solved'] }}/{{ $progress['total'] }} gelöst</p>
                                        <p class="text-xs text-gray-600 font-semibold">{{ $progress['percentage'] }}%</p>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ $progress['percentage'] }}%; box-shadow: 0 0 10px rgba(251, 191, 36, 0.6), 0 0 20px rgba(251, 191, 36, 0.4), 0 0 30px rgba(251, 191, 36, 0.2);"></div>
                                    </div>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <a href="{{ route('lehrgaenge.practice', $lehrgang->slug) }}" 
               class="flex-1 p-5 sm:p-6 bg-white border border-green-200 rounded-lg text-center hover:shadow-lg hover:scale-105 transition-all duration-300"
               style="background: linear-gradient(to right, #86efac, #4ade80); color: #15803d; box-shadow: 0 4px 15px rgba(74, 222, 128, 0.4), 0 0 20px rgba(74, 222, 128, 0.3), 0 0 40px rgba(74, 222, 128, 0.1);">
                <div class="text-xl sm:text-2xl mb-2">🎯</div>
                <div class="font-bold text-sm sm:text-base">{{ __('Jetzt üben') }}</div>
                <div class="text-xs mt-1 opacity-80">{{ __('Teste dein Wissen') }}</div>
            </a>
        </div>
    @else
        <!-- User nicht eingeschrieben -->
        <div class="mb-8 p-6 sm:p-8 bg-white rounded-lg border-2 border-blue-200 text-center hover:shadow-lg transition-all duration-300">
            <div class="mb-6">
                <div class="text-5xl mb-4">📚</div>
                <h2 class="text-2xl sm:text-3xl font-bold text-blue-800 mb-2">{{ __('Bist du bereit?') }}</h2>
                <p class="text-gray-600 text-sm sm:text-base">{{ __('Schließe dich diesem Lehrgang an und beginne zu lernen') }}</p>
            </div>

            <div class="mb-8">
                <div class="inline-block bg-gradient-to-r from-blue-100 to-indigo-100 rounded-lg px-6 py-4 border border-blue-200">
                    <p class="font-semibold text-blue-800 mb-2">📚 {{ __('Inhalte') }}</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $questions->sum(fn($group) => $group->count()) }}</p>
                    <p class="text-xs text-blue-600 mt-1">{{ __('Lernfragen') }}</p>
                </div>
            </div>

            <form action="{{ route('lehrgaenge.enroll', $lehrgang->slug) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="px-8 py-3 rounded-lg font-bold text-white inline-block hover:scale-110 transition-all duration-300 text-sm sm:text-base"
                        style="background: linear-gradient(to right, #86efac, #4ade80); box-shadow: 0 4px 15px rgba(74, 222, 128, 0.4), 0 0 20px rgba(74, 222, 128, 0.3), 0 0 40px rgba(74, 222, 128, 0.1);">
                    ✨ {{ __('Jetzt beitreten') }}
                </button>
            </form>

            <p class="text-gray-500 text-xs sm:text-sm mt-6">{{ __('Du kannst dich jederzeit abmelden') }}</p>
        </div>

        <!-- Vorschau der Inhalte -->
        <div class="mt-8">
            <h2 class="text-xl sm:text-2xl font-bold text-blue-800 mb-4">{{ __('Lernabschnitte') }}</h2>
            
            @if($questions->isEmpty())
                <p class="text-gray-600">{{ __('Noch keine Fragen vorhanden') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($questions as $section => $sectionQuestions)
                        <div class="p-4 sm:p-5 bg-white border border-gray-200 rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-default">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900 text-base sm:text-lg">{{ $lernabschnitte->get($section, 'Lernabschnitt ' . $section) }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $sectionQuestions->count() }} {{ __('Fragen') }}</p>
                                </div>
                                <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold">{{ $section }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
