@extends('layouts.app')
@section('title', 'Theorie üben')
@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
    @if($question)
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold">
                @if(isset($mode))
                    @switch($mode)
                        @case('unsolved')
                            🎯 Ungelöste Fragen
                            @break
                        @case('section')
                            📖 Lernabschnitt {{ session('practice_parameter') }}
                            @break
                        @case('search')
                            🔍 Suche: "{{ session('practice_parameter') }}"
                            @break
                        @default
                            📚 Alle Fragen
                    @endswitch
                @else
                    Theorie üben
                @endif
            </h2>
            <a href="{{ route('practice.menu') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Zurück zum Menü</a>
        </div>
        
        <div class="mb-4 text-sm text-gray-600">
            Fortschritt: {{ $progress }}/{{ $total }}
            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300" 
                     style="width: {{ $total > 0 ? round(($progress / $total) * 100) : 0 }}%"></div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('practice.submit') }}">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">
            <div class="mb-4 p-4 border rounded bg-gray-50">
                <div class="mb-2 text-[9px] sm:text-xs text-gray-500 flex items-center gap-1">
                    <span>ID: {{ $question->id }}</span>
                    <span class="mx-0.5 sm:mx-2">&middot;</span>
                    <span>Lernabschnitt: {{ $question->lernabschnitt ?? '-' }}.{{ $question->nummer ?? '-' }}</span>
                </div>
                <div class="mb-2 font-bold">Frage:</div>
                <div class="mb-4">{{ $question->frage }}</div>
                <div class="mb-4">
                    <label class="block mb-2 font-semibold">Antwortmöglichkeiten:</label>
                    <div class="flex flex-col gap-2">
                        @foreach(['A','B','C'] as $option)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="answer[]" value="{{ $option }}"
                                    @if(isset($isCorrect) && isset($userAnswer) && $userAnswer->contains($option)) checked @endif
                                    @if(isset($isCorrect)) disabled @endif>
                                <span class="ml-2">{{ $option }}: {{ $question['antwort_'.strtolower($option)] }}</span>
                                @if(isset($isCorrect))
                                    @php
                                        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
                                    @endphp
                                    @if($solution->contains($option))
                                        <span class="ml-2 text-green-600 font-bold">✔</span>
                                    @elseif(isset($userAnswer) && $userAnswer->contains($option))
                                        <span class="ml-2 text-red-600 font-bold">✘</span>
                                    @endif
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(!isset($isCorrect))
                <button type="submit" id="submitBtn" class="bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900" disabled>Antwort absenden</button>
                <script>
                    const checkboxes = document.querySelectorAll('input[type=checkbox][name="answer[]"]');
                    const submitBtn = document.getElementById('submitBtn');
                    function updateBtn() {
                        let checked = 0;
                        checkboxes.forEach(cb => { if(cb.checked) checked++; });
                        submitBtn.disabled = checked === 0;
                    }
                    checkboxes.forEach(cb => cb.addEventListener('change', updateBtn));
                    updateBtn();
                </script>
            @elseif(isset($isCorrect) && $isCorrect)
                <div class="mt-4 text-green-600 font-bold">Richtig! Weiter zur nächsten Frage...</div>
                <a href="{{ route('practice.index') }}" class="mt-4 inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Nächste Frage</a>
            @elseif(isset($isCorrect) && !$isCorrect)
                <div class="mt-4 text-red-600 font-bold">Leider falsch. Die richtigen Antworten sind markiert.</div>
                <a href="{{ route('practice.index', ['skip_id' => $question->id]) }}" class="mt-4 inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Weiter zur nächsten Frage</a>
            @endif
        </form>
    @else
        <div class="text-center text-lg mb-4">Du hast alle Fragen in diesem Modus bearbeitet! 🎉</div>
        <div class="text-center">
            <a href="{{ route('practice.menu') }}" class="inline-block bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900 mr-4">Zurück zum Übungsmenü</a>
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-600 text-white px-6 py-2 rounded font-bold hover:bg-gray-700">Dashboard</a>
        </div>
    @endif
</div>
@endsection
