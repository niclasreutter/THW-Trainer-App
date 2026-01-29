@extends('layouts.app')
@section('title', 'Fragenverwaltung - THW Trainer Admin')
@section('description', 'Verwalte alle Fragen, Lernabschnitte und Antworten im THW Trainer System.')

@push('styles')
<style>
    @media (min-width: 768px) {
        .desktop-table {
            display: block !important;
        }
        .mobile-cards {
            display: none !important;
        }
    }

    @media (max-width: 767px) {
        .desktop-table {
            display: none !important;
        }
        .mobile-cards {
            display: block !important;
        }
    }

    .inline-edit {
        width: 100%;
    }

    .solution-button {
        border: 2px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.03);
        color: var(--text-muted);
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        min-width: 32px;
        transition: all 0.2s;
    }

    .solution-button.active {
        border-color: var(--success);
        background: var(--success);
        color: white;
    }

    .table-input {
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.03);
        width: 100%;
        padding: 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        color: var(--text-primary);
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }

    .table-input:focus {
        background: rgba(255, 255, 255, 0.06);
        border-color: var(--gold-start);
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1 class="page-title">Fragen <span>Verwaltung</span></h1>
        <p class="page-subtitle">Verwalte alle Fragen, Lernabschnitte und Antworten</p>
    </header>

    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Erfolg!</strong>
                <p style="margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="glass-error" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
            <i class="bi bi-x-circle" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>Fehler!</strong>
                <ul style="margin: 0.25rem 0 0 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="stats-row">
        <div class="stat-pill">
            <span class="stat-pill-icon text-gold">
                <i class="bi bi-card-text"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $questions->count() }}</div>
                <div class="stat-pill-label">Gesamt Fragen</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-thw-blue">
                <i class="bi bi-book"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $questions->pluck('lernabschnitt')->unique()->count() }}</div>
                <div class="stat-pill-label">Lernabschnitte</div>
            </div>
        </div>

        <div class="stat-pill">
            <span class="stat-pill-icon text-success">
                <i class="bi bi-hash"></i>
            </span>
            <div>
                <div class="stat-pill-value">{{ $questions->max('id') ?? 0 }}</div>
                <div class="stat-pill-label">Höchste ID</div>
            </div>
        </div>
    </div>

    <div class="glass-gold hover-lift" style="padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-plus-circle text-gold"></i>
            Neue Frage hinzufügen
        </h3>

        <form method="POST" action="{{ route('admin.questions.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                        Lernabschnitt <span style="color: var(--error);">*</span>
                    </label>
                    <input type="text" name="lernabschnitt" value="{{ old('lernabschnitt') }}"
                           placeholder="z.B. 1" required class="inline-edit">
                </div>

                <div>
                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                        Nummer <span style="color: var(--error);">*</span>
                    </label>
                    <input type="text" name="nummer" value="{{ old('nummer') }}"
                           placeholder="z.B. 1.1" required class="inline-edit">
                </div>

                <div>
                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                        Lösung(en) <span style="color: var(--error);">*</span>
                        <small style="color: var(--text-muted); font-weight: 400; display: block;">Mehrere Antworten möglich</small>
                    </label>
                    <div style="display: flex; gap: 0.75rem; padding: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; background: rgba(255, 255, 255, 0.03);">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="loesung[]" value="A"
                                   {{ is_array(old('loesung')) && in_array('A', old('loesung')) ? 'checked' : '' }}>
                            <span style="background: linear-gradient(135deg, var(--gold-start) 0%, var(--gold-end) 100%); color: #0c0a09; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-weight: 700; font-size: 1rem;">A</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="loesung[]" value="B"
                                   {{ is_array(old('loesung')) && in_array('B', old('loesung')) ? 'checked' : '' }}>
                            <span style="background: linear-gradient(135deg, var(--gold-start) 0%, var(--gold-end) 100%); color: #0c0a09; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-weight: 700; font-size: 1rem;">B</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="loesung[]" value="C"
                                   {{ is_array(old('loesung')) && in_array('C', old('loesung')) ? 'checked' : '' }}>
                            <span style="background: linear-gradient(135deg, var(--gold-start) 0%, var(--gold-end) 100%); color: #0c0a09; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-weight: 700; font-size: 1rem;">C</span>
                        </label>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                    Frage <span style="color: var(--error);">*</span>
                </label>
                <textarea name="frage" required placeholder="Fragentext eingeben..." class="inline-edit" style="min-height: 80px; resize: vertical;">{{ old('frage') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                        Antwort A <span style="color: var(--error);">*</span>
                    </label>
                    <textarea name="antwort_a" required placeholder="Antwort A..." class="inline-edit" style="min-height: 60px; resize: vertical;">{{ old('antwort_a') }}</textarea>
                </div>

                <div>
                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                        Antwort B <span style="color: var(--error);">*</span>
                    </label>
                    <textarea name="antwort_b" required placeholder="Antwort B..." class="inline-edit" style="min-height: 60px; resize: vertical;">{{ old('antwort_b') }}</textarea>
                </div>

                <div>
                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                        Antwort C <span style="color: var(--error);">*</span>
                    </label>
                    <textarea name="antwort_c" required placeholder="Antwort C..." class="inline-edit" style="min-height: 60px; resize: vertical;">{{ old('antwort_c') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                Frage speichern
            </button>
        </form>
    </div>

    <div class="glass hover-lift" style="padding: 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-list-check text-gold"></i>
            Alle Fragen ({{ $questions->count() }})
        </h3>

        @if($questions->count() > 0)
            <div style="overflow-x: auto;" class="desktop-table">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(255, 255, 255, 0.1);">
                            <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">ID</th>
                            <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Lernabschnitt</th>
                            <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Nummer</th>
                            <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Frage</th>
                            <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Lösung</th>
                            <th style="color: var(--text-secondary); padding: 0.75rem 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.06); transition: all 0.2s;" id="question-{{ $question->id }}"
                                onmouseover="this.style.background='rgba(255, 255, 255, 0.03)'"
                                onmouseout="this.style.background='transparent'">
                                <td style="padding: 0.875rem 1rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">{{ $question->id }}</td>

                                <td style="padding: 0.875rem 1rem;">
                                    <input type="text" value="{{ $question->lernabschnitt }}"
                                           data-field="lernabschnitt" data-id="{{ $question->id }}"
                                           onchange="updateQuestion(this)" class="table-input">
                                </td>

                                <td style="padding: 0.875rem 1rem;">
                                    <input type="text" value="{{ $question->nummer }}"
                                           data-field="nummer" data-id="{{ $question->id }}"
                                           onchange="updateQuestion(this)" class="table-input">
                                </td>

                                <td style="padding: 0.875rem 1rem; max-width: 400px;">
                                    <textarea data-field="frage" data-id="{{ $question->id }}"
                                              onchange="updateQuestion(this)" class="table-input" style="min-height: 80px; resize: vertical;">{{ $question->frage }}</textarea>
                                </td>

                                <td style="padding: 0.875rem 1rem;">
                                    <div data-field="loesung" data-id="{{ $question->id }}" style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                        @php
                                            $solutions = explode(',', $question->loesung);
                                        @endphp
                                        <button type="button" data-value="A" onclick="toggleSolution(this)"
                                                class="solution-button {{ in_array('A', $solutions) ? 'active' : '' }}">A</button>
                                        <button type="button" data-value="B" onclick="toggleSolution(this)"
                                                class="solution-button {{ in_array('B', $solutions) ? 'active' : '' }}">B</button>
                                        <button type="button" data-value="C" onclick="toggleSolution(this)"
                                                class="solution-button {{ in_array('C', $solutions) ? 'active' : '' }}">C</button>
                                    </div>
                                </td>

                                <td style="padding: 0.875rem 1rem;">
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <div id="status-{{ $question->id }}" style="width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></div>

                                        <button onclick="toggleAnswers({{ $question->id }})" class="btn-secondary"
                                                style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">
                                            Antworten
                                        </button>

                                        <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}"
                                              style="display: inline;" onsubmit="return confirm('Frage wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost" style="padding: 0.5rem 0.75rem; color: var(--error); font-size: 0.75rem;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <tr id="answers-{{ $question->id }}" style="display: none; background: rgba(255, 255, 255, 0.02); border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                                <td colspan="6" style="padding: 1rem;">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                        <div>
                                            <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort A:</label>
                                            <textarea data-field="antwort_a" data-id="{{ $question->id }}"
                                                      onchange="updateQuestion(this)" class="table-input" style="min-height: 80px; resize: vertical;">{{ $question->antwort_a }}</textarea>
                                        </div>
                                        <div>
                                            <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort B:</label>
                                            <textarea data-field="antwort_b" data-id="{{ $question->id }}"
                                                      onchange="updateQuestion(this)" class="table-input" style="min-height: 80px; resize: vertical;">{{ $question->antwort_b }}</textarea>
                                        </div>
                                        <div>
                                            <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort C:</label>
                                            <textarea data-field="antwort_c" data-id="{{ $question->id }}"
                                                      onchange="updateQuestion(this)" class="table-input" style="min-height: 80px; resize: vertical;">{{ $question->antwort_c }}</textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="display: none;" class="mobile-cards">
                @foreach($questions as $question)
                    <div class="glass hover-lift" style="padding: 1.5rem; margin-bottom: 1rem;" id="question-card-{{ $question->id }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <h4 style="margin: 0; font-weight: 700; font-size: 1.1rem;">Frage #{{ $question->id }}</h4>
                            <div style="display: flex; gap: 0.5rem;">
                                <div id="status-mobile-{{ $question->id }}" style="width: 10px; height: 10px; border-radius: 50%; background: var(--success);"></div>
                                <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}"
                                      style="display: inline;" onsubmit="return confirm('Frage wirklich löschen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost" style="padding: 0.5rem 0.75rem; color: var(--error);">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Lernabschnitt:</label>
                                <input type="text" value="{{ $question->lernabschnitt }}"
                                       data-field="lernabschnitt" data-id="{{ $question->id }}"
                                       onchange="updateQuestion(this)" class="inline-edit">
                            </div>
                            <div>
                                <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Nummer:</label>
                                <input type="text" value="{{ $question->nummer }}"
                                       data-field="nummer" data-id="{{ $question->id }}"
                                       onchange="updateQuestion(this)" class="inline-edit">
                            </div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Frage:</label>
                            <textarea data-field="frage" data-id="{{ $question->id }}"
                                      onchange="updateQuestion(this)" class="inline-edit" style="min-height: 100px; resize: vertical;">{{ $question->frage }}</textarea>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Lösung(en):</label>
                            <div data-field="loesung" data-id="{{ $question->id }}" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @php
                                    $solutions = explode(',', $question->loesung);
                                @endphp
                                <button type="button" data-value="A" onclick="toggleSolution(this)"
                                        class="solution-button {{ in_array('A', $solutions) ? 'active' : '' }}" style="padding: 0.5rem 1rem;">A</button>
                                <button type="button" data-value="B" onclick="toggleSolution(this)"
                                        class="solution-button {{ in_array('B', $solutions) ? 'active' : '' }}" style="padding: 0.5rem 1rem;">B</button>
                                <button type="button" data-value="C" onclick="toggleSolution(this)"
                                        class="solution-button {{ in_array('C', $solutions) ? 'active' : '' }}" style="padding: 0.5rem 1rem;">C</button>
                            </div>
                        </div>

                        <button onclick="toggleAnswersMobile({{ $question->id }})" class="btn-secondary" style="width: 100%; margin-bottom: 1rem;">
                            Antworten bearbeiten
                        </button>

                        <div id="answers-mobile-{{ $question->id }}" style="display: none;">
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div>
                                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort A:</label>
                                    <textarea data-field="antwort_a" data-id="{{ $question->id }}"
                                              onchange="updateQuestion(this)" class="inline-edit" style="min-height: 100px; resize: vertical;">{{ $question->antwort_a }}</textarea>
                                </div>
                                <div>
                                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort B:</label>
                                    <textarea data-field="antwort_b" data-id="{{ $question->id }}"
                                              onchange="updateQuestion(this)" class="inline-edit" style="min-height: 100px; resize: vertical;">{{ $question->antwort_b }}</textarea>
                                </div>
                                <div>
                                    <label style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort C:</label>
                                    <textarea data-field="antwort_c" data-id="{{ $question->id }}"
                                              onchange="updateQuestion(this)" class="inline-edit" style="min-height: 100px; resize: vertical;">{{ $question->antwort_c }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-card-text"></i></div>
                <p>Noch keine Fragen vorhanden. Erstelle deine erste Frage!</p>
            </div>
        @endif
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

function updateQuestion(element) {
    const questionId = element.dataset.id;
    const field = element.dataset.field;
    const value = element.value;
    const statusIndicator = document.getElementById(`status-${questionId}`);

    if (statusIndicator) {
        statusIndicator.style.background = '#fbbf24';
    }

    fetch(`/admin/questions/${questionId}/update-field`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ field: field, value: value })
    })
    .then(response => response.json())
    .then(data => {
        if (statusIndicator) {
            statusIndicator.style.background = data.success ? '#22c55e' : '#ef4444';
        }
        if (!data.success) {
            alert('Fehler beim Speichern: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        if (statusIndicator) {
            statusIndicator.style.background = '#ef4444';
        }
        alert('Netzwerk-Fehler beim Speichern. Bitte versuchen Sie es erneut.');
    });
}

function toggleSolution(button) {
    const container = button.closest('[data-field="loesung"]');
    const questionId = container.dataset.id;
    const statusIndicator = document.getElementById(`status-${questionId}`);

    button.classList.toggle('active');

    const allButtons = container.querySelectorAll('button');
    const activeSolutions = [];
    allButtons.forEach(btn => {
        if (btn.classList.contains('active')) {
            activeSolutions.push(btn.dataset.value);
        }
    });

    if (activeSolutions.length === 0) {
        button.classList.add('active');
        alert('Mindestens eine Lösung muss ausgewählt sein!');
        return;
    }

    if (statusIndicator) {
        statusIndicator.style.background = '#fbbf24';
    }

    fetch(`/admin/questions/${questionId}/update-field`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ field: 'loesung', value: activeSolutions.join(',') })
    })
    .then(response => response.json())
    .then(data => {
        if (statusIndicator) {
            statusIndicator.style.background = data.success ? '#22c55e' : '#ef4444';
        }
        if (!data.success) {
            alert('Fehler beim Speichern: ' + (data.message || 'Unbekannter Fehler'));
            location.reload();
        }
    })
    .catch(error => {
        if (statusIndicator) {
            statusIndicator.style.background = '#ef4444';
        }
        alert('Netzwerk-Fehler beim Speichern. Seite wird neu geladen.');
        location.reload();
    });
}

function toggleAnswers(questionId) {
    const answersRow = document.getElementById(`answers-${questionId}`);
    const button = event.target;

    if (answersRow.style.display === 'none' || answersRow.style.display === '') {
        answersRow.style.display = 'table-row';
        button.innerHTML = '<i class="bi bi-chevron-up"></i> Verstecken';
    } else {
        answersRow.style.display = 'none';
        button.innerHTML = '<i class="bi bi-pencil"></i> Antworten';
    }
}

function toggleAnswersMobile(questionId) {
    const answersDiv = document.getElementById(`answers-mobile-${questionId}`);
    const button = event.target;

    if (answersDiv.style.display === 'none' || answersDiv.style.display === '') {
        answersDiv.style.display = 'block';
        button.innerHTML = 'Antworten verstecken';
    } else {
        answersDiv.style.display = 'none';
        button.innerHTML = 'Antworten bearbeiten';
    }
}

document.addEventListener('keypress', function(e) {
    if (e.target.tagName === 'INPUT' && e.key === 'Enter') {
        e.target.blur();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log(`Alle {{ $questions->count() }} Fragen geladen und inline-editierbar!`);
});
</script>
@endsection
