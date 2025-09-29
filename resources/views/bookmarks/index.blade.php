@extends('layouts.app')
@section('title', 'Gespeicherte Fragen - THW Trainer')

@section('content')
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">📚 Gespeicherte Fragen</h1>
        
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        
        @if($questions->count() > 0)
            <!-- Übungsbutton -->
            <div class="mb-12 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">🎯 Gespeicherte Fragen üben</h2>
                <p class="text-gray-600 mb-4">Übe alle deine gespeicherten Fragen in einer Session.</p>
                
                <div class="max-w-md mx-auto">
                    <a href="{{ route('bookmarks.practice') }}" 
                       class="block p-4 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 transition-colors">
                        <div class="text-lg font-medium text-blue-800">🔖 Alle üben ({{ $questions->count() }} Fragen)</div>
                        <div class="text-sm text-gray-600">Starte Übungssession mit deinen Favoriten</div>
                    </a>
                </div>
            </div>
            
            <!-- Fragenliste -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">📋 Deine gespeicherten Fragen</h2>
                
                <div class="space-y-4">
                    @foreach($questions as $question)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 mb-2">
                                        Lernabschnitt {{ $question->lernabschnitt }}
                                    </div>
                                    <div class="text-gray-700 mb-3">
                                        {{ Str::limit($question->frage, 150) }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Antwort: {{ $question->loesung }} - 
                                        @if($question->loesung === 'A')
                                            {{ $question->antwort_a }}
                                        @elseif($question->loesung === 'B')
                                            {{ $question->antwort_b }}
                                        @else
                                            {{ $question->antwort_c }}
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="ml-4">
                                    <form action="{{ route('bookmarks.toggle') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                                        <button type="submit" 
                                                class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                                                title="Aus Lesezeichen entfernen">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Keine gespeicherten Fragen -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-6xl mb-4">📝</div>
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Noch keine Fragen gespeichert</h2>
                <p class="text-gray-600 mb-6">
                    Du kannst Fragen während des Übens speichern, um sie später nochmal anzuschauen.
                </p>
                <a href="{{ route('practice.menu') }}" 
                   class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Zum Übungsmenü
                </a>
            </div>
        @endif
        
        <!-- Navigation -->
        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                ← Zurück zum Dashboard
            </a>
        </div>
    </div>
@endsection
