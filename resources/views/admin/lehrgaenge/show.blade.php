@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold">{{ $lehrgang->lehrgang }}</h1>
            <p class="text-gray-600 mt-2">{{ $lehrgang->beschreibung }}</p>
            <p class="text-gray-500 text-sm mt-2">
                📚 {{ $lehrgang->questions->count() }} Fragen
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge') . '/edit') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                Bearbeiten
            </a>
            <form action="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge')) }}" method="POST" style="display: inline;"
                  onsubmit="return confirm('Wirklich löschen? Alle Fragen werden gelöscht!');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Löschen
                </button>
            </form>
        </div>
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

    <!-- CSV Import Section -->
    <div class="bg-blue-50 border border-blue-300 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">📤 Fragen per CSV importieren</h2>
        
        <form action="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge') . '/import-csv') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="bg-white p-4 rounded border border-blue-200">
                <p class="text-sm text-gray-700 mb-3">
                    <strong>CSV-Format erforderlich (Tab-getrennt):</strong>
                </p>
                <div class="bg-gray-100 p-3 rounded text-xs font-mono text-gray-800 overflow-x-auto">
                    <div>lernabschnitt	nummer	frage	antwort_a	antwort_b	antwort_c	loesung</div>
                    <div>1	1	Was ist Sicherheit?	Gefahr	Schutz	Schaden	B</div>
                    <div>1	2	Mehrere richtig?	Antwort 1	Antwort 2	Antwort 3	A,B</div>
                </div>
                <p class="text-xs text-gray-600 mt-2">
                    • <strong>lernabschnitt:</strong> Ganze Zahl (1, 2, 3...)<br>
                    • <strong>nummer:</strong> Ganze Zahl<br>
                    • <strong>loesung:</strong> A, B, C oder komma-getrennt (A,B)
                </p>
            </div>

            <div>
                <label for="csv_file" class="block text-sm font-bold mb-2">CSV-Datei auswählen *</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required 
                       class="w-full border rounded px-3 py-2 @error('csv_file') border-red-500 @enderror">
                @error('csv_file')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded font-bold">
                Importieren
            </button>
        </form>
    </div>

    <!-- Fragen Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-6">Fragen ({{ $lehrgang->questions->count() }})</h2>

        @forelse($questionsBySection as $section => $questions)
            <div class="mb-12">
                <h3 class="text-lg font-bold text-gray-700 bg-gray-100 px-4 py-3 rounded mb-6 sticky top-0 z-10">
                    Lernabschnitt {{ $section }} ({{ $questions->count() }} Fragen)
                </h3>
                
                <div class="space-y-8">
                    @foreach($questions as $question)
                        <div class="border-2 rounded-lg p-6 bg-white hover:shadow-md transition">
                            <form action="{{ route('admin.lehrgaenge.update-question', [$lehrgang->id, $question->id]) }}" 
                                  method="POST" class="question-form space-y-4">
                                @csrf
                                @method('PATCH')
                                
                                <!-- Lernabschnitt und Nummer -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Lernabschnitt</label>
                                        <input type="number" name="lernabschnitt" value="{{ $question->lernabschnitt }}" 
                                               class="w-full border-2 rounded px-3 py-2 text-base question-input focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               min="1" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Fragenummer</label>
                                        <input type="number" name="nummer" value="{{ $question->nummer }}" 
                                               class="w-full border-2 rounded px-3 py-2 text-base question-input focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               min="1" required />
                                    </div>
                                </div>

                                <!-- Frage -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Frage</label>
                                    <textarea name="frage" rows="3" 
                                              class="w-full border-2 rounded px-3 py-2 text-base question-input focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              required>{{ $question->frage }}</textarea>
                                </div>

                                <!-- Antworten -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-bold text-gray-800 mb-4">Antwortmöglichkeiten</p>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">A)</label>
                                            <textarea name="antwort_a" rows="2" 
                                                      class="w-full border-2 rounded px-3 py-2 text-base question-input focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                      required>{{ $question->antwort_a }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">B)</label>
                                            <textarea name="antwort_b" rows="2" 
                                                      class="w-full border-2 rounded px-3 py-2 text-base question-input focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                      required>{{ $question->antwort_b }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">C)</label>
                                            <textarea name="antwort_c" rows="2" 
                                                      class="w-full border-2 rounded px-3 py-2 text-base question-input focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                      required>{{ $question->antwort_c }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lösung und Buttons -->
                                <div class="border-t-2 pt-4 flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <label class="text-sm font-bold text-gray-800">Korrekte Lösung(en):</label>
                                        <select name="loesung" class="border-2 rounded px-3 py-2 text-base question-input focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold" required>
                                            <option value="A" @if($question->loesung === 'A') selected @endif>A</option>
                                            <option value="B" @if($question->loesung === 'B') selected @endif>B</option>
                                            <option value="C" @if($question->loesung === 'C') selected @endif>C</option>
                                            <option value="A,B" @if($question->loesung === 'A,B') selected @endif>A, B</option>
                                            <option value="A,C" @if($question->loesung === 'A,C') selected @endif>A, C</option>
                                            <option value="B,C" @if($question->loesung === 'B,C') selected @endif>B, C</option>
                                            <option value="A,B,C" @if($question->loesung === 'A,B,C') selected @endif>A, B, C</option>
                                        </select>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition shadow-md">
                                            💾 Speichern
                                        </button>
                                        <form action="{{ route('admin.lehrgaenge.delete-question', $question->id) }}" method="POST" style="display: inline;"
                                              onsubmit="return confirm('Frage wirklich löschen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition shadow-md">
                                                🗑️ Löschen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-gray-100 rounded-lg p-8 text-center">
                <p class="text-gray-600">Noch keine Fragen vorhanden. Bitte per CSV importieren oder später hinzufügen.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.lehrgaenge.index') }}" class="text-blue-500 hover:underline">
            ← Zurück zur Liste
        </a>
    </div>
</div>

<script>
document.querySelectorAll('.question-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = this.getAttribute('action');
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            if (response.ok) {
                const data = await response.json();
                // Visual feedback - grüner Hintergrund für kurze Zeit
                form.parentElement.style.backgroundColor = '#d1fae5';
                setTimeout(() => {
                    form.parentElement.style.backgroundColor = '';
                }, 1500);
                
                // Optional: Toast-Nachricht
                console.log('✓ ' + data.message);
            } else {
                alert('Fehler beim Speichern');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Fehler beim Speichern');
        }
    });
});
</script>
@endsection
