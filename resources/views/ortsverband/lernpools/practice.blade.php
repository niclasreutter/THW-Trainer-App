@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header mit Lernpool-Info -->
    <div class="mb-8">
        <a href="{{ route('ortsverband.show', $lernpool->ortsverband) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            ← Zurück zum Ortsverband
        </a>
        <h1 class="text-4xl font-bold text-gray-900 mt-4">{{ $lernpool->name }}</h1>
        <p class="text-gray-600 mt-2">{{ $lernpool->description }}</p>
    </div>

    <!-- Fortschritt & Statistiken -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Gesamtfortschritt -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Gesamtfortschritt</h3>
            <div class="mb-4">
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $enrollment->getProgress() }}%"></div>
                </div>
            </div>
            <p class="text-2xl font-bold text-blue-600">{{ round($enrollment->getProgress()) }}%</p>
            <p class="text-sm text-gray-600">{{ $enrollment->getSolvedCount() }} von {{ $totalQuestions }} beantwortet</p>
        </div>

        <!-- Bestandene Fragen -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Bestandene Fragen</h3>
            <p class="text-4xl font-bold text-green-600">{{ $enrollment->getSolvedCount() }}</p>
            <p class="text-sm text-gray-600 mt-2">2x korrekt beantwortet</p>
        </div>

        <!-- Offene Fragen -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Offene Fragen</h3>
            <p class="text-4xl font-bold text-yellow-600">{{ $totalQuestions - $enrollment->getSolvedCount() }}</p>
            <p class="text-sm text-gray-600 mt-2">Noch zu bearbeiten</p>
        </div>
    </div>

    <!-- Fragen nach Lernabschnitten gruppiert -->
    @forelse($questionsBySection as $section => $sectionQuestions)
        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">{{ $section }}</h2>
            </div>
            
            <div class="divide-y">
                @foreach($sectionQuestions as $question)
                    @php
                        $progress = $question->progress()->where('user_id', auth()->id())->first();
                        $isSolved = $progress && $progress->solved;
                        $correctAttempts = $progress->correct_attempts ?? 0;
                    @endphp
                    
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <span class="text-gray-600 text-sm">Frage {{ $question->nummer }}:</span>
                                    {{ $question->frage }}
                                </h3>
                            </div>
                            <div class="ml-4">
                                @if($isSolved)
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                        ✓ Bestanden
                                    </span>
                                @elseif($progress && $correctAttempts >= 1)
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $correctAttempts }}/2 korrekt
                                    </span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                        Offen
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if(!$isSolved)
                            <!-- Antwortvormöglichkeiten -->
                            <form action="{{ route('ortsverband.lernpools.practice.answer', [$lernpool->ortsverband, $lernpool]) }}" 
                                  method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                
                                <div class="space-y-3">
                                    @foreach(['a' => $question->antwort_a, 'b' => $question->antwort_b, 'c' => $question->antwort_c] as $key => $answer)
                                        <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                            <input type="radio" name="answer" value="{{ $key }}" class="w-4 h-4 text-blue-600" required>
                                            <span class="ml-3 text-gray-700">{{ $answer }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <button type="submit" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                    Antwort absenden
                                </button>
                            </form>
                        @else
                            <!-- Korrekte Antwort anzeigen (nach Bestehen) -->
                            <div class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Korrekte Antwort:</p>
                                @if($question->loesung === 'a')
                                    <p class="text-gray-900">{{ $question->antwort_a }}</p>
                                @elseif($question->loesung === 'b')
                                    <p class="text-gray-900">{{ $question->antwort_b }}</p>
                                @else
                                    <p class="text-gray-900">{{ $question->antwort_c }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Versuchsinformationen -->
                        @if($progress)
                            <div class="mt-4 text-sm text-gray-600 bg-gray-50 p-3 rounded">
                                <p>Versuche: {{ $progress->total_attempts }} | Korrekt: {{ $progress->correct_attempts }}</p>
                                @if($progress->consecutive_correct > 0)
                                    <p class="text-green-600 font-medium">✓ {{ $progress->consecutive_correct }} aufeinanderfolgende korrekte Antworten</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <p class="text-yellow-800">Keine Fragen in diesem Lernpool verfügbar.</p>
        </div>
    @endforelse

    <!-- Ausschreiben Button -->
    <div class="mt-8 flex justify-center">
        <form action="{{ route('ortsverband.lernpools.practice.unenroll', [$lernpool->ortsverband, $lernpool]) }}" 
              method="POST" onsubmit="return confirm('Möchtest du dich wirklich aus diesem Lernpool ausschreiben? Dein Fortschritt wird gelöscht.');">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800 font-medium underline">
                Aus Lernpool ausschreiben
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-pulse">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
        {{ $errors->first() }}
    </div>
@endif
@endsection
