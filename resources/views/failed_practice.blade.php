@extends('layouts.app')

@section('title', 'Fehler wiederholen - THW Trainer')
@section('description', 'Wiederhole deine falschen THW-Theoriefragen und verbessere dein Wissen. Lerne aus deinen Fehlern und bereite dich optimal auf die Prüfung vor.')

@section('content')
    <div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
        @if($question)
            <h2 class="text-2xl font-bold mb-4">Fehler wiederholen</h2>
            <form method="POST" action="{{ route('failed.submit') }}">
                @csrf
                <input type="hidden" name="question_id" value="{{ $question->id }}">
                <div class="mb-6 p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="mb-2 text-xs text-gray-500 flex items-center gap-2">
                        <span>ID: {{ $question->id }}</span>
                        <span class="mx-2">&middot;</span>
                        <span>Lernabschnitt: {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
                    </div>
                    <div class="mb-2 font-bold">Frage:</div>
                    <div class="mb-4">{{ $question->frage }}</div>
                    <div class="mb-4">
                        <label class="block mb-2 font-semibold">Antwortmöglichkeiten:</label>
                        <div class="flex flex-col gap-3">
                            @foreach(['A','B','C'] as $option)
                                <label class="inline-flex items-center p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                    @if(isset($isCorrect))
                                        @php
                                            $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
                                        @endphp
                                        @if($solution->contains($option))
                                            <span class="mr-2 text-green-600 text-lg">✅</span>
                                        @elseif(isset($userAnswer) && $userAnswer->contains($option))
                                            <span class="mr-2 text-red-600 text-lg">❌</span>
                                        @else
                                            <span class="mr-2 text-gray-400 text-lg">⚪</span>
                                        @endif
                                    @endif
                                    <input type="checkbox" name="answer[]" value="{{ $option }}"
                                        @if(isset($isCorrect) && isset($userAnswer) && $userAnswer->contains($option)) checked @endif
                                        @if(isset($isCorrect)) disabled @endif
                                        class="mr-2 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <span class="ml-2">{{ $option }}: {{ $question['antwort_'.strtolower($option)] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if(!isset($isCorrect))
                <button type="submit" class="bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300">Antwort prüfen</button>
                @endif
            </form>
            @if(isset($isCorrect) && $isCorrect)
                @if(isset($questionProgress) && $questionProgress->consecutive_correct == 1)
                    <div class="mt-4 p-4 bg-blue-50 border-2 border-blue-300 rounded-lg text-blue-700 font-bold text-center">
                        <div class="flex items-center justify-center">
                            <div class="text-2xl mr-3">👍</div>
                            <div>
                                <div>Richtig! Aber noch nicht gemeistert.</div>
                                <div class="text-xs mt-1 font-normal">Beantworte die Frage noch <strong>1x richtig</strong>, um sie zu meistern!</div>
                            </div>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('failed.index') }}">
                        <button type="submit" class="mt-4 bg-blue-900 text-yellow-400 font-bold px-6 py-2 rounded hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300">Nächste Frage</button>
                    </form>
                @else
                    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 font-bold animate-pulse text-center">✅ Richtig! Frage gemeistert!</div>
                    <form method="GET" action="{{ route('failed.index') }}">
                        <button type="submit" class="mt-4 bg-blue-900 text-yellow-400 font-bold px-6 py-2 rounded hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300">Nächste Frage</button>
                    </form>
                @endif
            @elseif(isset($isCorrect) && !$isCorrect)
                <div class="mt-4 p-4 rounded-lg font-bold shadow-lg text-center" style="background-color: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); color: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.3), 0 0 30px rgba(239, 68, 68, 0.1);">
                    <div class="flex items-center justify-center">
                        <div class="text-2xl mr-3">❌</div>
                        <span>Leider falsch! Versuche es nochmal.</span>
                    </div>
                </div>
                <form method="GET" action="{{ route('failed.index') }}">
                    <button type="submit" class="mt-4 bg-blue-900 text-yellow-400 font-bold px-6 py-2 rounded hover:bg-yellow-400 hover:text-blue-900 hover:shadow-lg hover:scale-105 transition-all duration-300">Weiter zum nächsten Fehler</button>
                </form>
            @endif
        @else
            <div class="text-center text-lg font-bold">Keine Fehler zum Wiederholen!</div>
        @endif
    </div>
@endsection
