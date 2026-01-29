@extends('layouts.app')
@section('title', 'Fragenverwaltung - THW Trainer Admin')
@section('description', 'Verwalte alle Fragen, Lernabschnitte und Antworten im THW Trainer System.')

@push('styles')
<style>
    /* Desktop Table (768px+ screens) */
    @media (min-width: 768px) {
        .desktop-table {
            display: block !important;
        }
        .mobile-cards {
            display: none !important;
        }
    }

    /* Mobile Cards (under 768px) */
    @media (max-width: 767px) {
        .desktop-table {
            display: none !important;
        }
        .mobile-cards {
            display: block !important;
        }
    }
</style>
@endpush


@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Fragen <span>Verwaltung</span></h1>
        <p class="page-subtitle">Verwalte alle Fragen, Lernabschnitte und Antworten</p>
    </header>

    <!-- Status Messages -->
    @if(session('success'))
        <div class="glass-success" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: start;">
            <i class="bi bi-check-circle text-success" style="font-size: 1.25rem;"></i>
            <div>
                <strong style="color: var(--text-primary); font-weight: 700;">Erfolg!</strong>
                <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="glass-error" style="padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: start;">
            <i class="bi bi-x-circle text-error" style="font-size: 1.25rem;"></i>
            <div>
                <strong style="color: var(--text-primary); font-weight: 700;">Fehler!</strong>
                <ul style="color: var(--text-secondary); margin: 0.25rem 0 0 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Stats Row -->
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

    <!-- Neue Frage hinzufügen -->
    <div class="glass-gold hover-lift" style="padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-plus-circle text-gold"></i>
            Neue Frage hinzufügen
        </h3>
            
            <form method="POST" action="{{ route('admin.questions.store') }}">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                            Lernabschnitt <span style="color: var(--error);">*</span>
                        </label>
                        <input type="text" name="lernabschnitt" value="{{ old('lernabschnitt') }}"
                               placeholder="z.B. 1" required
                               style="width: 100%; padding: 0.625rem 1rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); outline: none;">
                    </div>

                    <div>
                        <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                            Nummer <span style="color: var(--error);">*</span>
                        </label>
                        <input type="text" name="nummer" value="{{ old('nummer') }}"
                               placeholder="z.B. 1.1" required
                               style="width: 100%; padding: 0.625rem 1rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); outline: none;">
                    </div>
                    
                    <div>
                        <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                            Lösung(en) <span style="color: var(--error);">*</span>
                            <small style="color: var(--text-muted); font-weight: 400; display: block;">Mehrere Antworten möglich</small>
                        </label>
                        <div style="display: flex; gap: 0.75rem; padding: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; background: rgba(255, 255, 255, 0.03);">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 0.375rem; transition: background 0.2s;"
                                   onmouseover="this.style.background='rgba(255, 255, 255, 0.05)'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="loesung[]" value="A"
                                       {{ is_array(old('loesung')) && in_array('A', old('loesung')) ? 'checked' : '' }}
                                       style="width: 18px; height: 18px;">
                                <span style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #0c0a09; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-weight: 700; font-size: 1rem;">A</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 0.375rem; transition: background 0.2s;"
                                   onmouseover="this.style.background='rgba(255, 255, 255, 0.05)'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="loesung[]" value="B"
                                       {{ is_array(old('loesung')) && in_array('B', old('loesung')) ? 'checked' : '' }}
                                       style="width: 18px; height: 18px;">
                                <span style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #0c0a09; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-weight: 700; font-size: 1rem;">B</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 0.375rem; transition: background 0.2s;"
                                   onmouseover="this.style.background='rgba(255, 255, 255, 0.05)'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="loesung[]" value="C"
                                       {{ is_array(old('loesung')) && in_array('C', old('loesung')) ? 'checked' : '' }}
                                       style="width: 18px; height: 18px;">
                                <span style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #0c0a09; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-weight: 700; font-size: 1rem;">C</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                        Frage <span style="color: var(--error);">*</span>
                    </label>
                    <textarea name="frage" required placeholder="Fragentext eingeben..."
                              style="width: 100%; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); resize: vertical; min-height: 80px; outline: none;">{{ old('frage') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                            Antwort A <span style="color: var(--error);">*</span>
                        </label>
                        <textarea name="antwort_a" required placeholder="Antwort A..."
                                  style="width: 100%; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); resize: vertical; min-height: 60px; outline: none;">{{ old('antwort_a') }}</textarea>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                            Antwort B <span style="color: var(--error);">*</span>
                        </label>
                        <textarea name="antwort_b" required placeholder="Antwort B..."
                                  style="width: 100%; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); resize: vertical; min-height: 60px; outline: none;">{{ old('antwort_b') }}</textarea>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">
                            Antwort C <span style="color: var(--error);">*</span>
                        </label>
                        <textarea name="antwort_c" required placeholder="Antwort C..."
                                  style="width: 100%; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.5rem; color: var(--text-primary); resize: vertical; min-height: 60px; outline: none;">{{ old('antwort_c') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    Frage speichern
                </button>
            </form>
        </div>

    <!-- Fragen-Liste -->
    <div class="glass hover-lift" style="padding: 1.5rem;">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bi bi-list-check text-gold"></i>
                Alle Fragen ({{ $questions->count() }})
            </h3>

            @if($questions->count() > 0)
                <!-- Desktop Tabelle (versteckt auf mobil) -->
                <div style="overflow-x: auto; display: block;" class="desktop-table">
                    <table style="width: 100%; border-collapse: collapse; border-radius: 0.5rem; overflow: hidden;">
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

                                    <!-- Lernabschnitt - Inline editierbar -->
                                    <td style="padding: 0.875rem 1rem;">
                                        <input type="text"
                                               value="{{ $question->lernabschnitt }}"
                                               data-field="lernabschnitt"
                                               data-id="{{ $question->id }}"
                                               onchange="updateQuestion(this)"
                                               style="border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.03); width: 100%; padding: 0.5rem; border-radius: 0.375rem; font-size: 0.875rem; color: var(--text-primary); outline: none; transition: all 0.2s;"
                                               onfocus="this.style.background='rgba(255, 255, 255, 0.06)'; this.style.borderColor='var(--gold-start)';"
                                               onblur="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">
                                    </td>
                                    
                                    <!-- Nummer - Inline editierbar -->
                                    <td style="padding: 0.875rem 1rem;">
                                        <input type="text"
                                               value="{{ $question->nummer }}"
                                               data-field="nummer"
                                               data-id="{{ $question->id }}"
                                               onchange="updateQuestion(this)"
                                               style="border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.03); width: 100%; padding: 0.5rem; border-radius: 0.375rem; font-size: 0.875rem; color: var(--text-primary); outline: none; transition: all 0.2s;"
                                               onfocus="this.style.background='rgba(255, 255, 255, 0.06)'; this.style.borderColor='var(--gold-start)';"
                                               onblur="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">
                                    </td>

                                    <!-- Frage - Inline editierbar -->
                                    <td style="padding: 0.875rem 1rem; max-width: 400px;">
                                        <textarea data-field="frage"
                                                  data-id="{{ $question->id }}"
                                                  onchange="updateQuestion(this)"
                                                  style="border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.03); width: 100%; min-height: 80px; padding: 0.5rem; border-radius: 0.375rem; font-size: 0.875rem; color: var(--text-primary); resize: vertical; font-family: inherit; outline: none; transition: all 0.2s;"
                                                  onfocus="this.style.background='rgba(255, 255, 255, 0.06)'; this.style.borderColor='var(--gold-start)';"
                                                  onblur="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">{{ $question->frage }}</textarea>
                                    </td>
                                    
                                    <!-- Lösung - Kompakte Buttons für Mehrfachauswahl -->
                                    <td style="padding: 0.875rem 1rem;">
                                        <div data-field="loesung" data-id="{{ $question->id }}" style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                            @php
                                                $solutions = explode(',', $question->loesung);
                                            @endphp
                                            <button type="button"
                                                    data-value="A"
                                                    onclick="toggleSolution(this)"
                                                    style="border: 2px solid {{ in_array('A', $solutions) ? 'var(--success)' : 'rgba(255, 255, 255, 0.1)' }};
                                                           background: {{ in_array('A', $solutions) ? 'var(--success)' : 'rgba(255, 255, 255, 0.03)' }};
                                                           color: {{ in_array('A', $solutions) ? 'white' : 'var(--text-muted)' }};
                                                           padding: 0.25rem 0.5rem;
                                                           border-radius: 0.375rem;
                                                           font-weight: 600;
                                                           font-size: 0.875rem;
                                                           cursor: pointer;
                                                           min-width: 32px;
                                                           transition: all 0.2s;">A</button>
                                            <button type="button"
                                                    data-value="B"
                                                    onclick="toggleSolution(this)"
                                                    style="border: 2px solid {{ in_array('B', $solutions) ? 'var(--success)' : 'rgba(255, 255, 255, 0.1)' }};
                                                           background: {{ in_array('B', $solutions) ? 'var(--success)' : 'rgba(255, 255, 255, 0.03)' }};
                                                           color: {{ in_array('B', $solutions) ? 'white' : 'var(--text-muted)' }};
                                                           padding: 0.25rem 0.5rem;
                                                           border-radius: 0.375rem;
                                                           font-weight: 600;
                                                           font-size: 0.875rem;
                                                           cursor: pointer;
                                                           min-width: 32px;
                                                           transition: all 0.2s;">B</button>
                                            <button type="button"
                                                    data-value="C"
                                                    onclick="toggleSolution(this)"
                                                    style="border: 2px solid {{ in_array('C', $solutions) ? 'var(--success)' : 'rgba(255, 255, 255, 0.1)' }};
                                                           background: {{ in_array('C', $solutions) ? 'var(--success)' : 'rgba(255, 255, 255, 0.03)' }};
                                                           color: {{ in_array('C', $solutions) ? 'white' : 'var(--text-muted)' }};
                                                           padding: 0.25rem 0.5rem;
                                                           border-radius: 0.375rem;
                                                           font-weight: 600;
                                                           font-size: 0.875rem;
                                                           cursor: pointer;
                                                           min-width: 32px;
                                                           transition: all 0.2s;">C</button>
                                        </div>
                                    </td>
                                    
                                    <td style="padding: 0.875rem 1rem;">
                                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                                            <!-- Status Indicator -->
                                            <div id="status-{{ $question->id }}" style="width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></div>

                                            <!-- Antworten bearbeiten Button -->
                                            <button onclick="toggleAnswers({{ $question->id }})" class="btn-secondary"
                                                    style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">
                                                Antworten
                                            </button>

                                            <!-- Löschen Button -->
                                            <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Frage wirklich löschen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-ghost"
                                                        style="padding: 0.5rem 0.75rem; color: var(--error); font-size: 0.75rem;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Antworten Row (versteckt by default) -->
                                <tr id="answers-{{ $question->id }}" style="display: none; background: rgba(255, 255, 255, 0.02); border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                                    <td colspan="6" style="padding: 1rem;">
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                            <div>
                                                <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort A:</label>
                                                <textarea data-field="antwort_a"
                                                          data-id="{{ $question->id }}"
                                                          onchange="updateQuestion(this)"
                                                          style="width: 100%; padding: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.03); border-radius: 0.375rem; font-size: 0.875rem; color: var(--text-primary); resize: vertical; min-height: 80px; outline: none; transition: all 0.2s;"
                                                          onfocus="this.style.background='rgba(255, 255, 255, 0.06)'; this.style.borderColor='var(--gold-start)';"
                                                          onblur="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">{{ $question->antwort_a }}</textarea>
                                            </div>
                                            <div>
                                                <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort B:</label>
                                                <textarea data-field="antwort_b"
                                                          data-id="{{ $question->id }}"
                                                          onchange="updateQuestion(this)"
                                                          style="width: 100%; padding: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.03); border-radius: 0.375rem; font-size: 0.875rem; color: var(--text-primary); resize: vertical; min-height: 80px; outline: none; transition: all 0.2s;"
                                                          onfocus="this.style.background='rgba(255, 255, 255, 0.06)'; this.style.borderColor='var(--gold-start)';"
                                                          onblur="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">{{ $question->antwort_b }}</textarea>
                                            </div>
                                            <div>
                                                <label style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem; display: block;">Antwort C:</label>
                                                <textarea data-field="antwort_c"
                                                          data-id="{{ $question->id }}"
                                                          onchange="updateQuestion(this)"
                                                          style="width: 100%; padding: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.03); border-radius: 0.375rem; font-size: 0.875rem; color: var(--text-primary); resize: vertical; min-height: 80px; outline: none; transition: all 0.2s;"
                                                          onfocus="this.style.background='rgba(255, 255, 255, 0.06)'; this.style.borderColor='var(--gold-start)';"
                                                          onblur="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.1)';">{{ $question->antwort_c }}</textarea>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards (versteckt auf Desktop) -->
                <div style="display: none;" class="mobile-cards">
                    @foreach($questions as $question)
                        <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);" id="question-card-{{ $question->id }}">
                            <!-- Header mit ID und Status -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h4 style="margin: 0; color: #1f2937; font-weight: 700; font-size: 1.1rem;">Frage #{{ $question->id }}</h4>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div id="status-mobile-{{ $question->id }}" style="width: 10px; height: 10px; border-radius: 50%; background: #22c55e;"></div>
                                    <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('Frage wirklich löschen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                style="background: #ef4444; color: white; padding: 0.5rem; border-radius: 8px; border: none; cursor: pointer; font-size: 1.2rem;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Lernabschnitt und Nummer -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Lernabschnitt:</label>
                                    <input type="text" 
                                           value="{{ $question->lernabschnitt }}" 
                                           data-field="lernabschnitt" 
                                           data-id="{{ $question->id }}"
                                           onchange="updateQuestionMobile(this)"
                                           style="border: 1px solid #e5e7eb; background: #f9fafb; width: 100%; padding: 0.75rem; border-radius: 8px; font-size: 1rem; transition: all 0.2s;"
                                           onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                           onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Nummer:</label>
                                    <input type="text" 
                                           value="{{ $question->nummer }}" 
                                           data-field="nummer" 
                                           data-id="{{ $question->id }}"
                                           onchange="updateQuestionMobile(this)"
                                           style="border: 1px solid #e5e7eb; background: #f9fafb; width: 100%; padding: 0.75rem; border-radius: 8px; font-size: 1rem; transition: all 0.2s;"
                                           onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                           onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                                </div>
                            </div>

                            <!-- Frage -->
                            <div style="margin-bottom: 1rem;">
                                <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Frage:</label>
                                <textarea data-field="frage" 
                                          data-id="{{ $question->id }}"
                                          onchange="updateQuestionMobile(this)"
                                          style="border: 1px solid #e5e7eb; background: #f9fafb; width: 100%; min-height: 100px; padding: 0.75rem; border-radius: 8px; font-size: 1rem; resize: vertical; font-family: inherit; transition: all 0.2s;"
                                          onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                          onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">{{ $question->frage }}</textarea>
                            </div>

                            <!-- Lösungen -->
                            <div style="margin-bottom: 1rem;">
                                <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Lösung(en):</label>
                                <div data-field="loesung" data-id="{{ $question->id }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                    @php
                                        $solutions = explode(',', $question->loesung);
                                    @endphp
                                    <button type="button"
                                            data-value="A" 
                                            onclick="toggleSolutionMobile(this)"
                                            style="border: 2px solid {{ in_array('A', $solutions) ? '#16a34a' : '#d1d5db' }}; 
                                                   background: {{ in_array('A', $solutions) ? 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)' : 'white' }}; 
                                                   color: {{ in_array('A', $solutions) ? 'white' : '#6b7280' }}; 
                                                   padding: 0.75rem 1rem; 
                                                   border-radius: 8px; 
                                                   font-weight: 700; 
                                                   font-size: 1.1rem; 
                                                   cursor: pointer; 
                                                   min-width: 48px;
                                                   transition: all 0.2s;">A</button>
                                    <button type="button"
                                            data-value="B" 
                                            onclick="toggleSolutionMobile(this)"
                                            style="border: 2px solid {{ in_array('B', $solutions) ? '#16a34a' : '#d1d5db' }}; 
                                                   background: {{ in_array('B', $solutions) ? 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)' : 'white' }}; 
                                                   color: {{ in_array('B', $solutions) ? 'white' : '#6b7280' }}; 
                                                   padding: 0.75rem 1rem; 
                                                   border-radius: 8px; 
                                                   font-weight: 700; 
                                                   font-size: 1.1rem; 
                                                   cursor: pointer; 
                                                   min-width: 48px;
                                                   transition: all 0.2s;">B</button>
                                    <button type="button"
                                            data-value="C" 
                                            onclick="toggleSolutionMobile(this)"
                                            style="border: 2px solid {{ in_array('C', $solutions) ? '#16a34a' : '#d1d5db' }}; 
                                                   background: {{ in_array('C', $solutions) ? 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)' : 'white' }}; 
                                                   color: {{ in_array('C', $solutions) ? 'white' : '#6b7280' }}; 
                                                   padding: 0.75rem 1rem; 
                                                   border-radius: 8px; 
                                                   font-weight: 700; 
                                                   font-size: 1.1rem; 
                                                   cursor: pointer; 
                                                   min-width: 48px;
                                                   transition: all 0.2s;">C</button>
                                </div>
                            </div>

                            <!-- Antworten Button -->
                            <button onclick="toggleAnswersMobile({{ $question->id }})" 
                                    style="background: #00337F; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; border: none; font-size: 1rem; cursor: pointer; width: 100%; margin-bottom: 1rem;">
                                <i class="bi bi-pencil"></i> Antworten bearbeiten
                            </button>

                            <!-- Antworten (versteckt by default) -->
                            <div id="answers-mobile-{{ $question->id }}" style="display: none;">
                                <div style="display: flex; flex-direction: column; gap: 1rem;">
                                    <div>
                                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Antwort A:</label>
                                        <textarea data-field="antwort_a" 
                                                  data-id="{{ $question->id }}"
                                                  onchange="updateQuestionMobile(this)"
                                                  style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 8px; font-size: 1rem; resize: vertical; min-height: 100px; transition: all 0.2s;"
                                                  onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                                  onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">{{ $question->antwort_a }}</textarea>
                                    </div>
                                    <div>
                                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Antwort B:</label>
                                        <textarea data-field="antwort_b" 
                                                  data-id="{{ $question->id }}"
                                                  onchange="updateQuestionMobile(this)"
                                                  style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 8px; font-size: 1rem; resize: vertical; min-height: 100px; transition: all 0.2s;"
                                                  onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                                  onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">{{ $question->antwort_b }}</textarea>
                                    </div>
                                    <div>
                                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Antwort C:</label>
                                        <textarea data-field="antwort_c" 
                                                  data-id="{{ $question->id }}"
                                                  onchange="updateQuestionMobile(this)"
                                                  style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 8px; font-size: 1rem; resize: vertical; min-height: 100px; transition: all 0.2s;"
                                                  onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                                  onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">{{ $question->antwort_c }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1rem; color: #9ca3af;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-card-text"></i></div>
                    <p>Noch keine Fragen vorhanden. Erstelle deine erste Frage!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// CSRF Token für AJAX-Requests
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                  '{{ csrf_token() }}';

// Mobile Funktionen
function updateQuestionMobile(element) {
    const questionId = element.dataset.id;
    const field = element.dataset.field;
    const value = element.value;
    const statusIndicator = document.getElementById(`status-mobile-${questionId}`);
    
    // Status auf "wird gespeichert" setzen
    statusIndicator.style.background = '#fbbf24';
    
    // AJAX Request zum Speichern
    fetch(`/admin/questions/${questionId}/update-field`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            field: field,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusIndicator.style.background = '#22c55e';
        } else {
            statusIndicator.style.background = '#ef4444';
            alert('Fehler beim Speichern: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        statusIndicator.style.background = '#ef4444';
        alert('Netzwerk-Fehler beim Speichern. Bitte versuchen Sie es erneut.');
    });
}

function toggleSolutionMobile(button) {
    const container = button.closest('[data-field="loesung"]');
    const questionId = container.dataset.id;
    const value = button.dataset.value;
    const statusIndicator = document.getElementById(`status-mobile-${questionId}`);
    
    // Toggle visual state
    const isActive = button.style.background.includes('22c55e') || button.style.background.includes('#22c55e');
    
    if (isActive) {
        button.style.border = '2px solid #d1d5db';
        button.style.background = 'white';
        button.style.color = '#6b7280';
    } else {
        button.style.border = '2px solid #16a34a';
        button.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
        button.style.color = 'white';
    }
    
    const allButtons = container.querySelectorAll('button');
    const activeSolutions = [];
    
    allButtons.forEach(btn => {
        if (btn.style.background.includes('22c55e') || btn.style.background.includes('#22c55e')) {
            activeSolutions.push(btn.dataset.value);
        }
    });
    
    if (activeSolutions.length === 0) {
        button.style.border = '2px solid #16a34a';
        button.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
        button.style.color = 'white';
        alert('Mindestens eine Lösung muss ausgewählt sein!');
        return;
    }
    
    const solutionValue = activeSolutions.join(',');
    statusIndicator.style.background = '#fbbf24';
    
    fetch(`/admin/questions/${questionId}/update-field`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            field: 'loesung',
            value: solutionValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusIndicator.style.background = '#22c55e';
        } else {
            statusIndicator.style.background = '#ef4444';
            alert('Fehler beim Speichern: ' + (data.message || 'Unbekannter Fehler'));
            location.reload();
        }
    })
    .catch(error => {
        statusIndicator.style.background = '#ef4444';
        alert('Netzwerk-Fehler beim Speichern. Seite wird neu geladen.');
        location.reload();
    });
}

function toggleAnswersMobile(questionId) {
    const answersDiv = document.getElementById(`answers-mobile-${questionId}`);
    const button = event.target;
    
    if (answersDiv.style.display === 'none' || answersDiv.style.display === '') {
        answersDiv.style.display = 'block';
        button.innerHTML = '<i class="bi bi-chevron-up"></i> Antworten verstecken';
        button.style.background = '#dc2626';
    } else {
        answersDiv.style.display = 'none';
        button.innerHTML = '<i class="bi bi-pencil"></i> Antworten bearbeiten';
        button.style.background = '#00337F';
    }
}

// Toggle-Button für Lösungen (Desktop)
function toggleSolution(button) {
    const container = button.closest('[data-field="loesung"]');
    const questionId = container.dataset.id;
    const value = button.dataset.value;
    const statusIndicator = document.getElementById(`status-${questionId}`);
    
    // Toggle visual state
    const isActive = button.style.background.includes('22c55e') || button.style.background.includes('#22c55e');
    
    if (isActive) {
        // Deactivate
        button.style.border = '2px solid #d1d5db';
        button.style.background = 'white';
        button.style.color = '#6b7280';
    } else {
        // Activate
        button.style.border = '2px solid #16a34a';
        button.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
        button.style.color = 'white';
    }
    
    // Get all active buttons
    const allButtons = container.querySelectorAll('button');
    const activeSolutions = [];
    
    allButtons.forEach(btn => {
        if (btn.style.background.includes('22c55e') || btn.style.background.includes('#22c55e')) {
            activeSolutions.push(btn.dataset.value);
        }
    });
    
    // Mindestens eine Lösung muss ausgewählt sein
    if (activeSolutions.length === 0) {
        // Revert button state
        button.style.border = '2px solid #16a34a';
        button.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
        button.style.color = 'white';
        alert('Mindestens eine Lösung muss ausgewählt sein!');
        return;
    }
    
    const solutionValue = activeSolutions.join(',');
    
    // Status auf "wird gespeichert" setzen
    statusIndicator.style.background = '#fbbf24';
    
    // AJAX Request zum Speichern
    fetch(`/admin/questions/${questionId}/update-field`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            field: 'loesung',
            value: solutionValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Erfolgreich gespeichert - grüner Status
            statusIndicator.style.background = '#22c55e';
            console.log(`Lösung für Frage ${questionId} gespeichert: ${solutionValue}`);
        } else {
            // Fehler - roter Status und Revert
            statusIndicator.style.background = '#ef4444';
            console.error('Fehler beim Speichern:', data.message);
            alert('Fehler beim Speichern: ' + (data.message || 'Unbekannter Fehler'));
            // Revert button state on error
            location.reload();
        }
    })
    .catch(error => {
        console.error('Netzwerk-Fehler:', error);
        statusIndicator.style.background = '#ef4444';
        alert('Netzwerk-Fehler beim Speichern. Seite wird neu geladen.');
        location.reload();
    });
}

// Inline-Bearbeitung von Fragen (für andere Felder)
function updateQuestion(element) {
    const questionId = element.dataset.id;
    const field = element.dataset.field;
    const value = element.value;
    const statusIndicator = document.getElementById(`status-${questionId}`);
    
    // Status auf "wird gespeichert" setzen
    statusIndicator.style.background = '#fbbf24';
    
    // AJAX Request zum Speichern
    fetch(`/admin/questions/${questionId}/update-field`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            field: field,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Erfolgreich gespeichert - grüner Status
            statusIndicator.style.background = '#22c55e';
            
            // Nach 2 Sekunden wieder normal
            setTimeout(() => {
                statusIndicator.style.background = '#22c55e';
            }, 2000);
        } else {
            // Fehler - roter Status
            statusIndicator.style.background = '#ef4444';
            console.error('Fehler beim Speichern:', data.message);
            
            // Alert für Benutzer
            alert('Fehler beim Speichern: ' + (data.message || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Netzwerk-Fehler:', error);
        statusIndicator.style.background = '#ef4444';
        alert('Netzwerk-Fehler beim Speichern. Bitte versuchen Sie es erneut.');
    });
}

// Toggle Antworten anzeigen/verstecken
function toggleAnswers(questionId) {
    const answersRow = document.getElementById(`answers-${questionId}`);
    const button = event.target;
    
    if (answersRow.style.display === 'none' || answersRow.style.display === '') {
        answersRow.style.display = 'table-row';
        button.innerHTML = '<i class="bi bi-chevron-up"></i> Verstecken';
        button.style.background = '#dc2626';
    } else {
        answersRow.style.display = 'none';
        button.innerHTML = '<i class="bi bi-pencil"></i> Antworten';
        button.style.background = '#00337F';
    }
}

// Automatisches Speichern bei Enter (nicht bei Textareas)
document.addEventListener('keypress', function(e) {
    if (e.target.tagName === 'INPUT' && e.key === 'Enter') {
        e.target.blur(); // Trigger onchange
    }
});

// Zeige Anzahl aller Fragen im Header
document.addEventListener('DOMContentLoaded', function() {
    const questionCount = {{ $questions->count() }};
    console.log(`Alle ${questionCount} Fragen geladen und inline-editierbar!`);
});
</script>
@endsection
