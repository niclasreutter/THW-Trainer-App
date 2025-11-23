@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Lehrgänge Verwalten</h1>
        <a href="{{ route('admin.lehrgaenge.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            + Neuer Lehrgang
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4">
        @forelse($lehrgaenge as $lehrgang)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold">{{ $lehrgang->lehrgang }}</h2>
                        <p class="text-gray-600 text-sm mt-1">{{ Str::limit($lehrgang->beschreibung, 100) }}</p>
                        <p class="text-gray-500 text-xs mt-2">
                            📚 {{ $lehrgang->questions_count }} Fragen
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ url('admin/lehrgaenge/' . $lehrgang->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm">
                            Verwalten
                        </a>
                        <a href="{{ url('admin/lehrgaenge/' . $lehrgang->id . '/edit') }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm">
                            Bearbeiten
                        </a>
                        <form action="{{ url('admin/lehrgaenge/' . $lehrgang->id) }}" method="POST" style="display: inline;"
                              onsubmit="return confirm('Wirklich löschen? Alle Fragen werden gelöscht!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm">
                                Löschen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-100 rounded-lg p-8 text-center">
                <p class="text-gray-600">Noch keine Lehrgänge vorhanden</p>
                <a href="{{ route('admin.lehrgaenge.create') }}" class="text-blue-500 hover:underline mt-2 inline-block">
                    → Jetzt erstellen
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $lehrgaenge->links() }}
    </div>
</div>
@endsection
