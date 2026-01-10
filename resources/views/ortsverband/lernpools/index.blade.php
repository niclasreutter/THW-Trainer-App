@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header mit Action Buttons -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <a href="{{ route('ortsverband.show', $ortsverband) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                ← Zurück zum Ortsverband
            </a>
            <h1 class="text-4xl font-bold text-gray-900 mt-4">Lernpools verwalten</h1>
        </div>
        <a href="{{ route('ortsverband.lernpools.create', $ortsverband) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
            + Neuer Lernpool
        </a>
    </div>

    <!-- Tabelle mit Lernpools -->
    @if($lernpools->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Fragen</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Teilnehmer</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Erstellt</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($lernpools as $pool)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $pool->name }}</h3>
                                <p class="text-xs text-gray-600 mt-1">{{ Str::limit($pool->description, 60) }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $pool->getQuestionCount() }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $pool->getEnrollmentCount() }}
                            </td>
                            <td class="px-6 py-4">
                                @if($pool->is_active)
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                        Aktiv
                                    </span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium">
                                        Inaktiv
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $pool->created_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('ortsverband.lernpools.show', [$ortsverband, $pool]) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ansicht</a>
                                    <a href="{{ route('ortsverband.lernpools.edit', [$ortsverband, $pool]) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">Bearbeiten</a>
                                    <a href="{{ route('ortsverband.lernpools.questions.index', [$ortsverband, $pool]) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">Fragen</a>
                                    <form action="{{ route('ortsverband.lernpools.destroy', [$ortsverband, $pool]) }}" 
                                          method="POST" class="inline" 
                                          onsubmit="return confirm('Lernpool wirklich löschen?');">
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
            <p class="text-yellow-800">Noch keine Lernpools erstellt. 
                <a href="{{ route('ortsverband.lernpools.create', $ortsverband) }}" class="font-semibold underline">Jetzt einen erstellen →</a>
            </p>
        </div>
    @endif
</div>
@endsection
