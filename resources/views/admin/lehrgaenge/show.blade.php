@extends('layouts.app')

@section('title', $lehrgang->lehrgang)

@push('styles')
<style>
    .question-card {
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.75rem;
        background: rgba(255, 255, 255, 0.02);
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .question-card:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.15);
    }
    .question-card.saved {
        background: rgba(34, 197, 94, 0.1);
        border-color: rgba(34, 197, 94, 0.3);
    }
    .answer-grid {
        background: rgba(255, 255, 255, 0.03);
        padding: 1.25rem;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }
    .answer-item {
        margin-bottom: 1rem;
    }
    .answer-item:last-child {
        margin-bottom: 0;
    }
    .code-block {
        background: rgba(0, 0, 0, 0.3);
        color: var(--text-secondary);
        padding: 1rem;
        border-radius: 0.5rem;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        overflow-x: auto;
        margin: 1rem 0;
    }
    .section-divider {
        background: linear-gradient(135deg, var(--thw-blue), #1e40af);
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
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">{{ $lehrgang->lehrgang }} <span>Details</span></h1>
        <p class="page-subtitle">{{ $lehrgang->beschreibung }}</p>
    </header>

    @php
        $totalQuestions = $lehrgang->questions->count();
        $sectionCount = $questionsBySection->count();
    @endphp

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-question-circle"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $totalQuestions }}</div>
                <div class="stat-pill-label">Fragen gesamt</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-collection"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $sectionCount }}</div>
                <div class="stat-pill-label">Lernabschnitte</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success">
                <i class="bi bi-bar-chart"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $totalQuestions > 0 ? round($totalQuestions / max($sectionCount, 1)) : 0 }}</div>
                <div class="stat-pill-label">Fragen/Abschnitt</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Erfolg!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-error" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-exclamation-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Fehler!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2rem;">
        <a href="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge') . '/edit') }}" class="btn-primary" style="padding: 0.75rem 1.5rem;">
            Bearbeiten
        </a>
        <form action="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge')) }}" method="POST" style="display: inline;" onsubmit="return confirm('Wirklich löschen? Alle Fragen werden gelöscht!');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger" style="padding: 0.75rem 1.5rem;">
                Löschen
            </button>
        </form>
        <a href="{{ route('admin.lehrgaenge.index') }}" class="btn-secondary" style="padding: 0.75rem 1.5rem;">
            Zurück zur Liste
        </a>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem; margin-bottom: 2rem; border-left: 3px solid var(--thw-blue);">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1rem 0;">CSV Import</h3>

        <form action="{{ url('admin/lehrgaenge/' . request()->route('lehrgaenge') . '/import-csv') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="background: rgba(255, 255, 255, 0.03); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <p style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem; font-size: 0.9rem;">
                    CSV-Format erforderlich (Tab-getrennt):
                </p>
                <div class="code-block">
                    <div>lernabschnitt	nummer	frage	antwort_a	antwort_b	antwort_c	loesung</div>
                    <div>1	1	Was ist Sicherheit?	Gefahr	Schutz	Schaden	B</div>
                    <div>1	2	Mehrere richtig?	Antwort 1	Antwort 2	Antwort 3	A,B</div>
                </div>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.75rem; line-height: 1.6;">
                    <strong>lernabschnitt:</strong> Ganze Zahl (1, 2, 3...) |
                    <strong>nummer:</strong> Ganze Zahl |
                    <strong>loesung:</strong> A, B, C oder komma-getrennt (A,B)
                </p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="label-glass" style="margin-bottom: 0.5rem; display: block;">CSV-Datei auswahlen</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                       class="input-glass" style="padding: 0.5rem;">
                @error('csv_file')
                    <span style="color: var(--error); font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-secondary" style="padding: 0.625rem 1.25rem;">
                CSV Importieren
            </button>
        </form>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Fragen verwalten</h3>
            <span style="color: var(--text-muted); font-weight: 600; font-size: 0.9rem;">{{ $lehrgang->questions->count() }} Fragen</span>
        </div>

        @forelse($questionsBySection as $section => $questions)
            <div style="margin-bottom: 2.5rem;">
                <div class="section-divider">
                    <span>Lernabschnitt {{ $section }}</span>
                    <span style="font-size: 0.9rem; font-weight: 500;">{{ $questions->count() }} Fragen</span>
                </div>

                @foreach($questions as $question)
                    <div class="question-card" id="question-card-{{ $question->id }}">
                        <form action="{{ route('admin.lehrgaenge.update-question', [$lehrgang->id, $question->id]) }}"
                              method="POST" class="question-form">
                            @csrf
                            @method('PATCH')

                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label class="label-glass" style="margin-bottom: 0.5rem; display: block; font-size: 0.85rem;">Lernabschnitt</label>
                                    <input type="number" name="lernabschnitt" value="{{ $question->lernabschnitt }}"
                                           class="input-glass" min="1" required style="padding: 0.625rem 1rem;">
                                </div>
                                <div>
                                    <label class="label-glass" style="margin-bottom: 0.5rem; display: block; font-size: 0.85rem;">Fragenummer</label>
                                    <input type="number" name="nummer" value="{{ $question->nummer }}"
                                           class="input-glass" min="1" required style="padding: 0.625rem 1rem;">
                                </div>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label class="label-glass" style="margin-bottom: 0.5rem; display: block; font-size: 0.85rem;">Frage</label>
                                <textarea name="frage" rows="3" class="textarea-glass" required style="padding: 0.625rem 1rem;">{{ $question->frage }}</textarea>
                            </div>

                            <div class="answer-grid">
                                <p style="font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; font-size: 0.9rem;">Antwortmoglichkeiten</p>

                                <div class="answer-item">
                                    <label class="label-glass" style="margin-bottom: 0.35rem; display: block; font-size: 0.85rem;">A)</label>
                                    <textarea name="antwort_a" rows="2" class="textarea-glass" required style="padding: 0.625rem 1rem;">{{ $question->antwort_a }}</textarea>
                                </div>

                                <div class="answer-item">
                                    <label class="label-glass" style="margin-bottom: 0.35rem; display: block; font-size: 0.85rem;">B)</label>
                                    <textarea name="antwort_b" rows="2" class="textarea-glass" required style="padding: 0.625rem 1rem;">{{ $question->antwort_b }}</textarea>
                                </div>

                                <div class="answer-item">
                                    <label class="label-glass" style="margin-bottom: 0.35rem; display: block; font-size: 0.85rem;">C)</label>
                                    <textarea name="antwort_c" rows="2" class="textarea-glass" required style="padding: 0.625rem 1rem;">{{ $question->antwort_c }}</textarea>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1); flex-wrap: wrap; gap: 1rem;">
                                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                    <label class="label-glass" style="margin-bottom: 0; font-size: 0.85rem;">Korrekte Losung(en):</label>
                                    <select name="loesung" class="select-glass" style="width: auto; min-width: 100px; padding: 0.5rem 1rem;" required>
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
                                    <button type="submit" class="btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                                        Speichern
                                    </button>
                                    <button type="button" onclick="deleteQuestion({{ $question->id }})" class="btn-danger" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                                        Loschen
                                    </button>
                                </div>
                            </div>
                        </form>

                        <form id="delete-form-{{ $question->id }}" action="{{ route('admin.lehrgaenge.delete-question', $question->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @endforeach
            </div>
        @empty
            <div style="padding: 3rem 1rem; text-align: center; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-inbox"></i></div>
                <p style="margin: 0 0 0.5rem 0; font-weight: 700;">Noch keine Fragen vorhanden</p>
                <p style="margin: 0; font-size: 0.95rem;">Bitte per CSV importieren oder spater hinzufugen.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
function deleteQuestion(questionId) {
    if (confirm('Frage wirklich loschen?')) {
        document.getElementById('delete-form-' + questionId).submit();
    }
}

document.querySelectorAll('.question-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const url = this.getAttribute('action');
        const card = this.closest('.question-card');

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
                card.classList.add('saved');
                setTimeout(() => {
                    card.classList.remove('saved');
                }, 1500);
                console.log('Gespeichert: ' + data.message);
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
