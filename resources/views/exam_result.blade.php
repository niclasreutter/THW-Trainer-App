@extends('layouts.app')
@section('title', 'Prüfungsergebnis')
@section('content')
<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded shadow text-center">
    <pre class="text-left text-xs bg-gray-100 p-2">{{ print_r($results, true) }}</pre>
    <div class="mb-2 text-xs text-gray-500">Deine User-ID: {{ Auth::user()->id ?? 'Nicht eingeloggt' }}</div>
    <h2 class="text-2xl font-bold mb-4">Prüfungsergebnis:</h2>
    @if(isset($error))
        @if(isset($fragenMap))
            <pre class="text-left text-xs bg-gray-100 p-2">{{ print_r($fragenMap, true) }}</pre>
        @endif
        <div class="mb-4 text-red-600 font-bold">{{ $error }}</div>
    @elseif($results && $results->count())
        @foreach($results as $result)
            <div class="mb-8 p-4 border-2 rounded-lg bg-gray-100">
                <div class="mb-2 text-lg font-bold">Prüfung vom {{ $result->created_at->format('d.m.Y H:i') }}</div>
                <div class="mb-2">Du hast {{ $result->correct }} von {{ $result->total }} Fragen richtig beantwortet.</div>
                <div class="mb-2">Ergebnis: <span class="font-bold">{{ $result->percentage }}%</span></div>
                <div class="mb-2">Bestanden: <span class="font-bold">{{ $result->passed ? 'Ja' : 'Nein' }}</span></div>
                <div class="mb-2 text-xs text-gray-500">ID: {{ $result->id }} | User-ID: {{ $result->user_id }}</div>
                <div class="mb-2 text-xs text-gray-500">Fragen-IDs: {{ json_encode($result->fragen_ids) }}</div>
                <div class="mb-2 text-xs text-gray-500">Antworten: {{ json_encode($result->answers) }}</div>
                <div class="mb-2 text-xs text-gray-500">User-Antworten: {{ json_encode($result->user_answers) }}</div>
            </div>
        @endforeach
    @else
        <div class="mb-4">Du hast {{ $correct }} von {{ $total }} Fragen richtig beantwortet.</div>
        <div class="mb-4">Ergebnis: <span class="font-bold">{{ round($total > 0 ? ($correct/$total)*100 : 0, 1) }}%</span></div>
        @if($passed)
            <div class="mb-4 text-green-600 font-bold">Bestanden!</div>
            <div class="mb-4">Erfolgreiche Prüfungen: {{ Auth::user()->exam_passed_count ?? 1 }} / 5</div>
            @if($done)
                <div class="mb-4 text-blue-900 font-bold">Du hast den Prozess abgeschlossen!</div>
            @else
                <a href="{{ route('exam.start') }}" class="bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Neue Simulation starten</a>
            @endif
        @else
            <div class="mb-4 text-red-600 font-bold">Nicht bestanden. Mindestens 80% erforderlich.</div>
            <a href="{{ route('exam.start') }}" class="bg-blue-900 text-yellow-400 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-blue-900">Erneut versuchen</a>
        @endif
        <hr class="my-6">
        <h3 class="text-xl font-bold mb-4">Fragenübersicht</h3>
        <div class="space-y-6 text-left">
            @foreach($fragen as $frage)
                <div class="p-4 border rounded bg-gray-50">
                    <div class="font-semibold mb-2">{{ $frage->frage }}</div>
                    <div class="mb-1">
                        <span class="font-bold">Deine Antwort:</span>
                        @php
                            $userAnswer = $userAnswers[$frage->id] ?? [];
                            if(is_array($userAnswer)) $userAnswer = implode(', ', $userAnswer);
                        @endphp
                        <span class="{{ ($userAnswer == implode(', ', explode(',', $frage->loesung))) ? 'text-green-600' : 'text-red-600' }}">{{ $userAnswer ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="font-bold">Lösung:</span>
                        <span class="text-blue-900">{{ implode(', ', explode(',', $frage->loesung)) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
