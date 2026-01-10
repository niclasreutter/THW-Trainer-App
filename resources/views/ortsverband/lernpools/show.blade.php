@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
        ← Zurück zu Lernpools
    </a>

    <div class="flex justify-between items-center mt-4 mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">{{ $lernpool->name }}</h1>
            <p class="text-gray-600 mt-2">{{ $lernpool->description }}</p>
        </div>
        <a href="{{ route('ortsverband.lernpools.questions.create', [$ortsverband, $lernpool]) }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
            + Neue Frage
        </a>
    </div>

    <!-- Statistiken -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">Gesamt Fragen</p>
            <p class="text-3xl font-bold text-blue-600">{{ $lernpool->getQuestionCount() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">Teilnehmer</p>
            <p class="text-3xl font-bold text-green-600">{{ $lernpool->getEnrollmentCount() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">Durchschn. Fortschritt</p>
            <p class="text-3xl font-bold text-yellow-600">{{ round($lernpool->getAverageProgress()) }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">Status</p>
            <p class="text-lg font-bold {{ $lernpool->is_active ? 'text-green-600' : 'text-gray-600' }}">
                {{ $lernpool->is_active ? 'Aktiv' : 'Inaktiv' }}
            </p>
        </div>
    </div>

    <!-- Fragen nach Lernabschnitten -->
    @forelse($questionsBySection as $section => $sectionQuestions)
        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">{{ $section }}</h2>
                <span class="bg-white bg-opacity-20 text-white px-3 py-1 rounded-full text-sm font-medium">
                    {{ count($sectionQuestions) }} Fragen
                </span>
            </div>
            
            <div class="divide-y">
                @foreach($sectionQuestions as $question)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 flex-1">
                                <span class="text-gray-600 text-sm">Frage {{ $question->nummer }}:</span>
                                {{ $question->frage }}
                            </h3>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4 text-sm">
                            <div>
                                <p class="text-gray-600 font-medium mb-1">A)</p>
                                <p class="text-gray-900">{{ $question->antwort_a }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium mb-1">B)</p>
                                <p class="text-gray-900">{{ $question->antwort_b }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium mb-1">C)</p>
                                <p class="text-gray-900">{{ $question->antwort_c }}</p>
                            </div>
                        </div>

                        <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded mb-4 text-sm">
                            <p class="text-gray-700">
                                <strong>Lösung:</strong> {{ ucfirst($question->loesung) }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('ortsverband.lernpools.questions.edit', [$ortsverband, $lernpool, $question]) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">Bearbeiten</a>
                            <form action="{{ route('ortsverband.lernpools.questions.destroy', [$ortsverband, $lernpool, $question]) }}" 
                                  method="POST" class="inline" 
                                  onsubmit="return confirm('Frage wirklich löschen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Löschen
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded">
            <p class="text-yellow-800">Noch keine Fragen in diesem Lernpool. 
                <a href="{{ route('ortsverband.lernpools.questions.create', [$ortsverband, $lernpool]) }}" class="font-semibold underline">Jetzt eine erstellen →</a>
            </p>
        </div>
    @endforelse
</div>
@endsection
