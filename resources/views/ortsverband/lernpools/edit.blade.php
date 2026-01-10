@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
        ← Zurück zu Lernpools
    </a>

    <h1 class="text-4xl font-bold text-gray-900 mt-4 mb-8">{{ $lernpool->name }} bearbeiten</h1>

    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ route('ortsverband.lernpools.update', [$ortsverband, $lernpool]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                    Name <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $lernpool->name) }}" 
                       class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none @error('name') border-red-500 @enderror" 
                       required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                    Beschreibung <span class="text-red-600">*</span>
                </label>
                <textarea name="description" id="description" rows="4" 
                          class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none @error('description') border-red-500 @enderror" 
                          required>{{ old('description', $lernpool->description) }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="is_active" class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $lernpool->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span class="ml-3 text-sm font-medium text-gray-900">Aktiv</span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                    Speichern
                </button>
                <a href="{{ route('ortsverband.lernpools.index', $ortsverband) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold py-2 px-6 rounded-lg transition-colors">
                    Abbrechen
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
