@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <a href="{{ route('ortsverband.lernpools.show', [$ortsverband, $lernpool]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
        ← Zurück zur Fragenübersicht
    </a>

    <h1 class="text-4xl font-bold text-gray-900 mt-4 mb-8">Fragen in {{ $lernpool->name }}</h1>

    @if($questions->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Lernabschnitt</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nr.</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Frage</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Lösung</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($questions as $question)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $question->lernabschnitt }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ $question->nummer }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ Str::limit($question->frage, 60) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600">
                                {{ ucfirst($question->loesung) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
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
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded">
            <p class="text-yellow-800">Noch keine Fragen. 
                <a href="{{ route('ortsverband.lernpools.questions.create', [$ortsverband, $lernpool]) }}" class="font-semibold underline">Jetzt eine erstellen →</a>
            </p>
        </div>
    @endif
</div>
@endsection
