@extends('layouts.app')
@section('title', 'Falsche Fragen wiederholen')
@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
    @if(isset($frage))
        <h2 class="text-2xl font-bold mb-4">Wiederholung: Frage {{ $current+1 }} von {{ $total }}</h2>
        <p class="mb-6">{{ $frage->frage }}</p>
        <form method="POST" action="{{ route('exam.repeat.answer', ['nr' => $current]) }}">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-semibold">Antwortmöglichkeiten:</label>
                <div>
                    <label class="inline-flex items-center mr-4">
                        <input type="checkbox" name="answer[]" value="A" @if(isset($userAnswer) && in_array('A', $userAnswer->toArray())) checked @endif @if(isset($isCorrect) && !$isCorrect) disabled @endif>
                        <span class="ml-2">A: {{ $frage->antwort_a }}</span>
                        @if(isset($isCorrect) && !$isCorrect && str_contains($frage->loesung, 'A'))
                            <span class="ml-2 text-green-600 font-bold">✔</span>
                        @endif
                    </label>
                    <label class="inline-flex items-center mr-4">
                        <input type="checkbox" name="answer[]" value="B" @if(isset($userAnswer) && in_array('B', $userAnswer->toArray())) checked @endif @if(isset($isCorrect) && !$isCorrect) disabled @endif>
                        <span class="ml-2">B: {{ $frage->antwort_b }}</span>
                        @if(isset($isCorrect) && !$isCorrect && str_contains($frage->loesung, 'B'))
                            <span class="ml-2 text-green-600 font-bold">✔</span>
                        @endif
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="answer[]" value="C" @if(isset($userAnswer) && in_array('C', $userAnswer->toArray())) checked @endif @if(isset($isCorrect) && !$isCorrect) disabled @endif>
                        <span class="ml-2">C: {{ $frage->antwort_c }}</span>
                        @if(isset($isCorrect) && !$isCorrect && str_contains($frage->loesung, 'C'))
                            <span class="ml-2 text-green-600 font-bold">✔</span>
                        @endif
                    </label>
                </div>
            </div>
            @if(!isset($isCorrect))
                <button type="submit" class="bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Antwort absenden</button>
            @elseif($isCorrect === false)
                <div class="mt-4 text-red-600 font-bold">Leider falsch. Die richtigen Antworten sind markiert.</div>
            @endif

            @if(isset($isCorrect) && !empty(trim($frage->loesungsweg ?? '')))
                <div class="mt-4 p-4 rounded-lg border relative overflow-hidden"
                     style="background: linear-gradient(135deg, rgba(251,191,36,0.10), rgba(255,255,255,0.85)); border-color: rgba(245,158,11,0.30); box-shadow: 0 4px 16px rgba(0,51,127,0.06);">
                    <div style="position:absolute; top:0; left:0; right:0; height:2px; background: linear-gradient(90deg, transparent, #fbbf24, transparent);"></div>
                    <div style="display:flex; align-items:center; gap:0.4rem; font-family: 'IBM Plex Mono', ui-monospace, monospace; font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280; margin-bottom:0.4rem;">
                        <i class="bi bi-lightbulb-fill" style="color:#f59e0b; font-size:0.875rem;"></i>
                        <span>Lösungsweg</span>
                    </div>
                    <p style="margin:0; font-size:0.875rem; line-height:1.55; color:#1e293b;">{!! nl2br(e($frage->loesungsweg)) !!}</p>
                </div>
            @endif
        </form>
    @else
        <div class="text-center text-lg">Alle falsch beantworteten Fragen wurden wiederholt! Du kannst jetzt eine neue Prüfung starten.</div>
        <a href="{{ route('exam.start') }}" class="bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Neue Simulation starten</a>
    @endif
</div>
@endsection
