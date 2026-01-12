@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <a href="{{ route('ortsverband.lernpools.show', [$ortsverband, $lernpool]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
        ← Zurück zur Fragenübersicht
    </a>

    <h1 class="text-4xl font-bold text-gray-900 mt-4 mb-8">Neue Frage in {{ $lernpool->name }}</h1>

    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ route('ortsverband.lernpools.questions.store', [$ortsverband, $lernpool]) }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="lernabschnitt" class="block text-sm font-semibold text-gray-900 mb-2">
                        Lernabschnitt <span style="font-weight: normal; color: #6b7280;">(optional)</span>
                    </label>
                    <input type="text" name="lernabschnitt" id="lernabschnitt" value="{{ old('lernabschnitt') }}" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none @error('lernabschnitt') border-red-500 @enderror" 
                           placeholder="z.B. 1.1"
                           list="lernabschnitt-suggestions"
                           onchange="updateNummer()">
                    <datalist id="lernabschnitt-suggestions">
                        @foreach($existingSections as $section)
                            <option value="{{ $section }}">
                        @endforeach
                    </datalist>
                    @error('lernabschnitt')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nummer" class="block text-sm font-semibold text-gray-900 mb-2">
                        Fragenummer <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="nummer" id="nummer" value="{{ old('nummer', $nextNumber) }}" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none @error('nummer') border-red-500 @enderror" 
                           min="1" required>
                    @error('nummer')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="frage" class="block text-sm font-semibold text-gray-900 mb-2">
                    Frage <span class="text-red-600">*</span>
                </label>
                <textarea name="frage" id="frage" rows="3" 
                          class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none @error('frage') border-red-500 @enderror" 
                          required>{{ old('frage') }}</textarea>
                @error('frage')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 mb-6">
                <div>
                    <label for="antwort_a" class="block text-sm font-semibold text-gray-900 mb-2">
                        Antwort A <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="antwort_a" id="antwort_a" value="{{ old('antwort_a') }}" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none @error('antwort_a') border-red-500 @enderror" 
                           required>
                    @error('antwort_a')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="antwort_b" class="block text-sm font-semibold text-gray-900 mb-2">
                        Antwort B <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="antwort_b" id="antwort_b" value="{{ old('antwort_b') }}" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none @error('antwort_b') border-red-500 @enderror" 
                           required>
                    @error('antwort_b')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="antwort_c" class="block text-sm font-semibold text-gray-900 mb-2">
                        Antwort C <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="antwort_c" id="antwort_c" value="{{ old('antwort_c') }}" 
                           class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none @error('antwort_c') border-red-500 @enderror" 
                           required>
                    @error('antwort_c')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    Korrekte Antwort(en) <span class="text-red-600">*</span>
                </label>
                <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem;">Wähle eine oder mehrere richtige Antworten</p>
                <div style="display: flex; gap: 0.5rem;">
                    <label class="answer-toggle" style="flex: 1; cursor: pointer;">
                        <input type="checkbox" name="loesung[]" value="a" style="display: none;">
                        <span class="answer-btn" style="display: block; text-align: center; padding: 0.75rem 1rem; border: 2px solid #d1d5db; border-radius: 0.5rem; font-weight: 600; background: white; transition: all 0.2s;">A</span>
                    </label>
                    <label class="answer-toggle" style="flex: 1; cursor: pointer;">
                        <input type="checkbox" name="loesung[]" value="b" style="display: none;">
                        <span class="answer-btn" style="display: block; text-align: center; padding: 0.75rem 1rem; border: 2px solid #d1d5db; border-radius: 0.5rem; font-weight: 600; background: white; transition: all 0.2s;">B</span>
                    </label>
                    <label class="answer-toggle" style="flex: 1; cursor: pointer;">
                        <input type="checkbox" name="loesung[]" value="c" style="display: none;">
                        <span class="answer-btn" style="display: block; text-align: center; padding: 0.75rem 1rem; border: 2px solid #d1d5db; border-radius: 0.5rem; font-weight: 600; background: white; transition: all 0.2s;">C</span>
                    </label>
                </div>
                @error('loesung')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <style>
            .answer-toggle input:checked + .answer-btn {
                background: #00337F !important;
                color: white !important;
                border-color: #00337F !important;
            }
            .answer-btn:hover {
                border-color: #00337F;
                background: #f0f4ff;
            }
            </style>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                    Frage erstellen
                </button>
                <a href="{{ route('ortsverband.lernpools.show', [$ortsverband, $lernpool]) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold py-2 px-6 rounded-lg transition-colors">
                    Abbrechen
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const sectionNumbers = @json($sectionNumbers);

function updateNummer() {
    const section = document.getElementById('lernabschnitt').value;
    const nummerInput = document.getElementById('nummer');
    
    if (section && sectionNumbers[section]) {
        nummerInput.value = sectionNumbers[section] + 1;
    } else {
        nummerInput.value = {{ $nextNumber }};
    }
}
</script>
@endsection
