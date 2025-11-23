@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-8">Neuer Lehrgang</h1>

    <form action="{{ route('admin.lehrgaenge.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-6">
            <label for="lehrgang" class="block text-sm font-bold mb-2">Lehrgang Name *</label>
            <input type="text" id="lehrgang" name="lehrgang" class="w-full border rounded px-3 py-2 @error('lehrgang') border-red-500 @enderror"
                   value="{{ old('lehrgang') }}" placeholder="z.B. Grundlagen der Sicherheit">
            @error('lehrgang')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6">
            <label for="beschreibung" class="block text-sm font-bold mb-2">Beschreibung *</label>
            <textarea id="beschreibung" name="beschreibung" rows="4" class="w-full border rounded px-3 py-2 @error('beschreibung') border-red-500 @enderror"
                      placeholder="Kurze Beschreibung des Lehrgangs...">{{ old('beschreibung') }}</textarea>
            @error('beschreibung')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded font-bold">
                Erstellen
            </button>
            <a href="{{ route('admin.lehrgaenge.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded font-bold">
                Abbrechen
            </a>
        </div>
    </form>
</div>
@endsection
