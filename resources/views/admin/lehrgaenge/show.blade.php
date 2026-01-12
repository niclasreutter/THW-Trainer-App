@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@push('styles')
<style>
    * {
        box-sizing: border-box;
    }

    .dashboard-wrapper {
        min-height: 100vh;
        background: #f3f4f6;
        position: relative;
        overflow-x: hidden;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .dashboard-header {
        text-align: center;
        margin-bottom: 2rem;
        padding-top: 1rem;
    }

    .dashboard-greeting {
        font-size: 2rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .dashboard-greeting span {
        display: inline-block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-subtitle {
        font-size: 1rem;
        color: #4b5563;
        margin-bottom: 0;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1.25rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
    }

    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #00337F;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .info-card {
        background: white;
        padding: 2rem;
        border-radius: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
    }

    .info-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .info-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00337F;
        margin: 0;
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #00337F;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .import-card {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border: 2px solid #93c5fd;
    }

    .code-block {
        background: #1f2937;
        color: #d1d5db;
        padding: 1rem;
        border-radius: 0.5rem;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        overflow-x: auto;
        margin: 1rem 0;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        color: #00337F;
        margin-bottom: 0.5rem;
    }

    .form-input, .form-textarea, .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        transition: border-color 0.2s;
        font-family: inherit;
    }

    .form-input:focus, .form-textarea:focus, .form-select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-textarea {
        resize: vertical;
    }

    .section-header {
        background: linear-gradient(135deg, #00337F, #1e40af);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        font-weight: 700;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .question-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.2s;
    }

    .question-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .question-card.saved {
        background: #dcfce7;
        border-color: #86efac;
    }

    .answer-grid {
        background: #f9fafb;
        padding: 1.25rem;
        border-radius: 0.75rem;
        margin: 1rem 0;
    }

    .answer-item {
        margin-bottom: 1rem;
    }

    .answer-item:last-child {
        margin-bottom: 0;
    }

    .answer-label {
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }

    .question-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e5e7eb;
        flex-wrap: wrap;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .dashboard-container { padding: 1rem; }
        .info-card { padding: 1.25rem; }
        .dashboard-greeting { font-size: 1.5rem; }
        .question-footer { flex-direction: column; align-items: stretch; }
        .question-footer > * { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-greeting">📖 <span>{{ $lehrgang->lehrgang }}</span></h1>
            <p class="dashboard-subtitle">{{ $lehrgang->beschreibung }}</p>
        </div>

        <!-- Schnellstatistiken -->
        @php
            $totalQuestions = $lehrgang->questions->count();
            $sectionCount = $questionsBySection->count();
        @endphp

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">❓</div>
                <div class="stat-value">{{ $totalQuestions }}</div>
                <div class="stat-label">Fragen gesamt</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📑</div>
                <div class="stat-value">{{ $sectionCount }}</div>
                <div class="stat-label">Lernabschnitte</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">✏️</div>
                <div class="stat-value">{{ $totalQuestions > 0 ? round($totalQuestions / max($sectionCount, 1)) : 0 }}</div>
                <div class="stat-label">Ø Fragen/Abschnitt</div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                ✗ {{ session('error') }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="button-group" style="margin-bottom: 1.5rem;">
            <a href="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge') . '/edit') }}" class="btn btn-success">
                ✏️ Lehrgang bearbeiten
            </a>
            <form action="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge')) }}" method="POST" style="display: inline;" onsubmit="return confirm('Wirklich löschen? Alle Fragen werden gelöscht!');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    🗑️ Lehrgang löschen
                </button>
            </form>
            <a href="{{ route('admin.lehrgaenge.index') }}" class="btn btn-secondary">
                ← Zurück zur Liste
            </a>
        </div>

        <!-- CSV Import Section -->
        <div class="info-card import-card">
            <div class="info-card-header">
                <h2 class="info-title">📤 Fragen per CSV importieren</h2>
            </div>

            <form action="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge') . '/import-csv') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                    <p style="font-weight: 600; color: #374151; margin-bottom: 1rem;">
                        CSV-Format erforderlich (Tab-getrennt):
                    </p>
                    <div class="code-block">
                        <div>lernabschnitt	nummer	frage	antwort_a	antwort_b	antwort_c	loesung</div>
                        <div>1	1	Was ist Sicherheit?	Gefahr	Schutz	Schaden	B</div>
                        <div>1	2	Mehrere richtig?	Antwort 1	Antwort 2	Antwort 3	A,B</div>
                    </div>
                    <p style="font-size: 0.85rem; color: #6b7280; margin-top: 0.75rem; line-height: 1.6;">
                        • <strong>lernabschnitt:</strong> Ganze Zahl (1, 2, 3...)<br>
                        • <strong>nummer:</strong> Ganze Zahl<br>
                        • <strong>loesung:</strong> A, B, C oder komma-getrennt (A,B)
                    </p>
                </div>

                <div class="form-group">
                    <label for="csv_file" class="form-label">CSV-Datei auswählen *</label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                           class="form-input @error('csv_file') border-red-500 @enderror">
                    @error('csv_file')
                        <span style="color: #dc2626; font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    📤 CSV Importieren
                </button>
            </form>
        </div>

        <!-- Fragen Section -->
        <div class="info-card">
            <div class="info-card-header">
                <h2 class="info-title">📝 Fragen verwalten</h2>
                <span style="color: #6b7280; font-weight: 600;">{{ $lehrgang->questions->count() }} Fragen</span>
            </div>

            @forelse($questionsBySection as $section => $questions)
                <div style="margin-bottom: 3rem;">
                    <div class="section-header">
                        <span>📑 Lernabschnitt {{ $section }}</span>
                        <span>{{ $questions->count() }} Fragen</span>
                    </div>

                    @foreach($questions as $question)
                        <div class="question-card">
                            <form action="{{ route('admin.lehrgaenge.update-question', [$lehrgang->id, $question->id]) }}"
                                  method="POST" class="question-form">
                                @csrf
                                @method('PATCH')

                                <!-- Lernabschnitt und Nummer -->
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label">Lernabschnitt</label>
                                        <input type="number" name="lernabschnitt" value="{{ $question->lernabschnitt }}"
                                               class="form-input" min="1" required />
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label">Fragenummer</label>
                                        <input type="number" name="nummer" value="{{ $question->nummer }}"
                                               class="form-input" min="1" required />
                                    </div>
                                </div>

                                <!-- Frage -->
                                <div class="form-group">
                                    <label class="form-label">Frage</label>
                                    <textarea name="frage" rows="3" class="form-textarea" required>{{ $question->frage }}</textarea>
                                </div>

                                <!-- Antworten -->
                                <div class="answer-grid">
                                    <p style="font-weight: 700; color: #00337F; margin-bottom: 1rem;">Antwortmöglichkeiten</p>

                                    <div class="answer-item">
                                        <label class="answer-label">A)</label>
                                        <textarea name="antwort_a" rows="2" class="form-textarea" required>{{ $question->antwort_a }}</textarea>
                                    </div>

                                    <div class="answer-item">
                                        <label class="answer-label">B)</label>
                                        <textarea name="antwort_b" rows="2" class="form-textarea" required>{{ $question->antwort_b }}</textarea>
                                    </div>

                                    <div class="answer-item">
                                        <label class="answer-label">C)</label>
                                        <textarea name="antwort_c" rows="2" class="form-textarea" required>{{ $question->antwort_c }}</textarea>
                                    </div>
                                </div>

                                <!-- Lösung und Buttons -->
                                <div class="question-footer">
                                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                        <label class="form-label" style="margin-bottom: 0;">Korrekte Lösung(en):</label>
                                        <select name="loesung" class="form-select" style="width: auto; min-width: 120px;" required>
                                            <option value="A" @if($question->loesung === 'A') selected @endif>A</option>
                                            <option value="B" @if($question->loesung === 'B') selected @endif>B</option>
                                            <option value="C" @if($question->loesung === 'C') selected @endif>C</option>
                                            <option value="A,B" @if($question->loesung === 'A,B') selected @endif>A, B</option>
                                            <option value="A,C" @if($question->loesung === 'A,C') selected @endif>A, C</option>
                                            <option value="B,C" @if($question->loesung === 'B,C') selected @endif>B, C</option>
                                            <option value="A,B,C" @if($question->loesung === 'A,B,C') selected @endif>A, B, C</option>
                                        </select>
                                    </div>
                                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                        <button type="submit" class="btn btn-success">
                                            💾 Speichern
                                        </button>
                                        <button type="button" onclick="deleteQuestion({{ $question->id }})" class="btn btn-danger">
                                            🗑️ Löschen
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Separate Delete Form (hidden) -->
                            <form id="delete-form-{{ $question->id }}" action="{{ route('admin.lehrgaenge.delete-question', $question->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="empty-state">
                    <p>📭 Noch keine Fragen vorhanden</p>
                    <p style="font-size: 0.9rem; color: #9ca3af;">Bitte per CSV importieren oder später hinzufügen.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function deleteQuestion(questionId) {
    if (confirm('Frage wirklich löschen?')) {
        document.getElementById('delete-form-' + questionId).submit();
    }
}

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
