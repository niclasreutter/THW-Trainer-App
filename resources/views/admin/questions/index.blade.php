@extends('layouts.app')
@section('title', 'Fragenverwaltung - THW Trainer Admin')
@section('description', 'Verwalte alle Fragen, Lernabschnitte und Antworten im THW Trainer System.')

@push('styles')
<style>
    /* RESPONSIVE ADMIN DESIGN */
    body { background: #f3f4f6 !important; }
    
    .admin-wrapper { 
        background: #f3f4f6; 
        padding: 2rem; 
        min-height: 100vh;
    }
    
    .admin-container { 
        max-width: 1400px; 
        margin: 0 auto; 
    }
    
    .page-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 2rem; 
        flex-wrap: wrap; 
        gap: 1rem; 
    }
    
    .page-title { 
        font-size: 2rem; 
        font-weight: 800; 
        color: #00337F; 
        margin: 0; 
    }
    
    .btn { 
        display: inline-flex; 
        align-items: center; 
        gap: 0.5rem; 
        padding: 0.75rem 1.5rem; 
        border-radius: 8px; 
        font-weight: 600; 
        text-decoration: none; 
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-primary { 
        background: linear-gradient(135deg, #00337F 0%, #003F99 100%); 
        color: white; 
        border: none;
    }

    .btn-secondary { 
        background: white; 
        color: #6b7280; 
        border: 1px solid #d1d5db; 
    }
    
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
        .admin-wrapper {
            padding: 0.5rem;
        }
        
        .desktop-table {
            display: none !important;
        }
        .mobile-cards {
            display: block !important;
        }

        .page-header {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
        
        .page-title {
            font-size: 1.5rem;
        }

        .btn {
            padding: 1rem 1.5rem;
            font-size: 1rem;
            justify-content: center;
        }
    }

    /* Touch-optimized inputs on mobile */
    @media (max-width: 767px) {
        input, textarea, button {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }
        
        button[type="button"] {
            min-height: 48px; /* Better touch targets */
        }
    } 
        padding: 0.75rem 1.5rem; 
        border-radius: 8px; 
        text-decoration: none; 
        font-weight: 600; 
    }
    
    .btn-primary { 
        background: #00337F; 
        color: white; 
    }
    
    .btn-secondary { 
        background: #e5e7eb; 
        color: #374151; 
    }
</style>
@endpush


@section('content')
<div class="admin-wrapper">
    <div class="admin-container">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">📝 Fragenverwaltung</h1>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Admin Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">👥 Benutzer</a>
            </div>
        </div>

        <!-- Status Messages -->
        @if(session('success'))
            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid #22c55e; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem;">
                <span>✅</span>
                <div>
                    <strong style="color: #16a34a;">Erfolg!</strong>
                    <p style="color: #16a34a; margin: 0;">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem;">
                <span>❌</span>
                <div>
                    <strong style="color: #dc2626;">Fehler!</strong>
                    <ul style="color: #dc2626; margin: 0; padding-left: 1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Statistiken -->
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.3rem; font-weight: 700; color: #1f2937; margin-bottom: 1.5rem;">📊 Statistiken</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); display: flex; gap: 1rem; align-items: center;">
                    <div style="font-size: 2rem;">📝</div>
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0; color: #6b7280; font-size: 0.9rem; font-weight: 500;">Gesamt Fragen</h3>
                        <p style="font-size: 1.75rem; font-weight: 800; color: #00337F; margin: 0;">{{ $questions->count() }}</p>
                    </div>
                </div>

                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); display: flex; gap: 1rem; align-items: center;">
                    <div style="font-size: 2rem;">📚</div>
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0; color: #6b7280; font-size: 0.9rem; font-weight: 500;">Lernabschnitte</h3>
                        <p style="font-size: 1.75rem; font-weight: 800; color: #00337F; margin: 0;">{{ $questions->pluck('lernabschnitt')->unique()->count() }}</p>
                    </div>
                </div>

                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); display: flex; gap: 1rem; align-items: center;">
                    <div style="font-size: 2rem;">🏆</div>
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0; color: #6b7280; font-size: 0.9rem; font-weight: 500;">Höchste ID</h3>
                        <p style="font-size: 1.75rem; font-weight: 800; color: #00337F; margin: 0;">{{ $questions->max('id') ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Neue Frage hinzufügen -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); margin-bottom: 2rem;">
            <h3 style="font-size: 1.3rem; font-weight: 700; color: #1f2937; margin: 0 0 1.5rem 0;">➕ Neue Frage hinzufügen</h3>
            
            <form method="POST" action="{{ route('admin.questions.store') }}">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.95rem; display: block;">
                            Lernabschnitt <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="lernabschnitt" value="{{ old('lernabschnitt') }}" 
                               placeholder="z.B. 1" required
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem;">
                    </div>
                    
                    <div>
                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.95rem; display: block;">
                            Nummer <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="nummer" value="{{ old('nummer') }}" 
                               placeholder="z.B. 1.1" required
                               style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem;">
                    </div>
                    
                    <div>
                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.95rem; display: block;">
                            Lösung(en) <span style="color: #ef4444;">*</span>
                            <small style="color: #6b7280; font-weight: 400; display: block;">Mehrere Antworten möglich</small>
                        </label>
                        <div style="display: flex; gap: 0.75rem; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; background: white;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 6px; transition: background 0.2s;" 
                                   onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="loesung[]" value="A" 
                                       {{ is_array(old('loesung')) && in_array('A', old('loesung')) ? 'checked' : '' }}
                                       style="width: 18px; height: 18px;">
                                <span style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 1rem;">A</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 6px; transition: background 0.2s;"
                                   onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="loesung[]" value="B"
                                       {{ is_array(old('loesung')) && in_array('B', old('loesung')) ? 'checked' : '' }}
                                       style="width: 18px; height: 18px;">
                                <span style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 1rem;">B</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 6px; transition: background 0.2s;"
                                   onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="loesung[]" value="C"
                                       {{ is_array(old('loesung')) && in_array('C', old('loesung')) ? 'checked' : '' }}
                                       style="width: 18px; height: 18px;">
                                <span style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 1rem;">C</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.95rem; display: block;">
                        Frage <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="frage" required placeholder="Fragentext eingeben..." 
                              style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; resize: vertical; min-height: 80px;">{{ old('frage') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.95rem; display: block;">
                            Antwort A <span style="color: #ef4444;">*</span>
                        </label>
                        <textarea name="antwort_a" required placeholder="Antwort A..." 
                                  style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; resize: vertical; min-height: 60px;">{{ old('antwort_a') }}</textarea>
                    </div>
                    
                    <div>
                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.95rem; display: block;">
                            Antwort B <span style="color: #ef4444;">*</span>
                        </label>
                        <textarea name="antwort_b" required placeholder="Antwort B..." 
                                  style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; resize: vertical; min-height: 60px;">{{ old('antwort_b') }}</textarea>
                    </div>
                    
                    <div>
                        <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.95rem; display: block;">
                            Antwort C <span style="color: #ef4444;">*</span>
                        </label>
                        <textarea name="antwort_c" required placeholder="Antwort C..." 
                                  style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; resize: vertical; min-height: 60px;">{{ old('antwort_c') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" 
                        style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 0.75rem 1.5rem;">
                    💾 Frage speichern
                </button>
            </form>
        </div>

        <!-- Fragen-Liste -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <h3 style="font-size: 1.3rem; font-weight: 700; color: #1f2937; margin: 0 0 1.5rem 0;">📋 Alle Fragen ({{ $questions->count() }})</h3>
            
            @if($questions->count() > 0)
                <!-- Desktop Tabelle (versteckt auf mobil) -->
                <div style="overflow-x: auto; display: block;" class="desktop-table">
                    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden;">
                        <thead style="background: linear-gradient(135deg, #00337F 0%, #003F99 100%);">
                            <tr>
                                <th style="color: white; padding: 1rem; text-align: left; font-weight: 600;">ID</th>
                                <th style="color: white; padding: 1rem; text-align: left; font-weight: 600;">Lernabschnitt</th>
                                <th style="color: white; padding: 1rem; text-align: left; font-weight: 600;">Nummer</th>
                                <th style="color: white; padding: 1rem; text-align: left; font-weight: 600;">Frage</th>
                                <th style="color: white; padding: 1rem; text-align: left; font-weight: 600;">Lösung</th>
                                <th style="color: white; padding: 1rem; text-align: left; font-weight: 600;">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $question)
                                <tr style="border-bottom: 1px solid #e5e7eb;" id="question-{{ $question->id }}">
                                    <td style="padding: 1rem; font-weight: 600;">{{ $question->id }}</td>
                                    
                                    <!-- Lernabschnitt - Inline editierbar -->
                                    <td style="padding: 1rem;">
                                        <input type="text" 
                                               value="{{ $question->lernabschnitt }}" 
                                               data-field="lernabschnitt" 
                                               data-id="{{ $question->id }}"
                                               onchange="updateQuestion(this)"
                                               style="border: 1px solid #e5e7eb; background: #f9fafb; width: 100%; padding: 0.5rem; border-radius: 6px; font-size: 1rem; transition: all 0.2s;"
                                               onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                               onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                                    </td>
                                    
                                    <!-- Nummer - Inline editierbar -->
                                    <td style="padding: 1rem;">
                                        <input type="text" 
                                               value="{{ $question->nummer }}" 
                                               data-field="nummer" 
                                               data-id="{{ $question->id }}"
                                               onchange="updateQuestion(this)"
                                               style="border: 1px solid #e5e7eb; background: #f9fafb; width: 100%; padding: 0.5rem; border-radius: 6px; font-size: 1rem; transition: all 0.2s;"
                                               onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                               onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                                    </td>
                                    
                                    <!-- Frage - Inline editierbar -->
                                    <td style="padding: 1rem; max-width: 400px;">
                                        <textarea data-field="frage" 
                                                  data-id="{{ $question->id }}"
                                                  onchange="updateQuestion(this)"
                                                  style="border: 1px solid #e5e7eb; background: #f9fafb; width: 100%; min-height: 80px; padding: 0.5rem; border-radius: 6px; font-size: 1rem; resize: vertical; font-family: inherit; transition: all 0.2s;"
                                                  onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                                  onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">{{ $question->frage }}</textarea>
                                    </td>
                                    
                                    <!-- Lösung - Kompakte Buttons für Mehrfachauswahl -->
                                    <td style="padding: 1rem;">
                                        <div data-field="loesung" data-id="{{ $question->id }}" style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                            @php
                                                $solutions = explode(',', $question->loesung);
                                            @endphp
                                            <button type="button"
                                                    data-value="A" 
                                                    onclick="toggleSolution(this)"
                                                    style="border: 2px solid {{ in_array('A', $solutions) ? '#16a34a' : '#d1d5db' }}; 
                                                           background: {{ in_array('A', $solutions) ? 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)' : 'white' }}; 
                                                           color: {{ in_array('A', $solutions) ? 'white' : '#6b7280' }}; 
                                                           padding: 0.25rem 0.5rem; 
                                                           border-radius: 6px; 
                                                           font-weight: 600; 
                                                           font-size: 0.9rem; 
                                                           cursor: pointer; 
                                                           min-width: 32px;
                                                           transition: all 0.2s;">A</button>
                                            <button type="button"
                                                    data-value="B" 
                                                    onclick="toggleSolution(this)"
                                                    style="border: 2px solid {{ in_array('B', $solutions) ? '#16a34a' : '#d1d5db' }}; 
                                                           background: {{ in_array('B', $solutions) ? 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)' : 'white' }}; 
                                                           color: {{ in_array('B', $solutions) ? 'white' : '#6b7280' }}; 
                                                           padding: 0.25rem 0.5rem; 
                                                           border-radius: 6px; 
                                                           font-weight: 600; 
                                                           font-size: 0.9rem; 
                                                           cursor: pointer; 
                                                           min-width: 32px;
                                                           transition: all 0.2s;">B</button>
                                            <button type="button"
                                                    data-value="C" 
                                                    onclick="toggleSolution(this)"
                                                    style="border: 2px solid {{ in_array('C', $solutions) ? '#16a34a' : '#d1d5db' }}; 
                                                           background: {{ in_array('C', $solutions) ? 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)' : 'white' }}; 
                                                           color: {{ in_array('C', $solutions) ? 'white' : '#6b7280' }}; 
                                                           padding: 0.25rem 0.5rem; 
                                                           border-radius: 6px; 
                                                           font-weight: 600; 
                                                           font-size: 0.9rem; 
                                                           cursor: pointer; 
                                                           min-width: 32px;
                                                           transition: all 0.2s;">C</button>
                                        </div>
                                    </td>
                                    
                                    <td style="padding: 1rem;">
                                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                                            <!-- Status Indicator -->
                                            <div id="status-{{ $question->id }}" style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></div>
                                            
                                            <!-- Antworten bearbeiten Button -->
                                            <button onclick="toggleAnswers({{ $question->id }})" 
                                                    style="background: #00337F; color: white; padding: 0.5rem 0.75rem; border-radius: 6px; border: none; font-size: 0.8rem; cursor: pointer;">
                                                📝 Antworten
                                            </button>
                                            
                                            <!-- Löschen Button -->
                                            <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Frage wirklich löschen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        style="background: #ef4444; color: white; padding: 0.5rem 0.75rem; border-radius: 6px; border: none; font-size: 0.8rem; cursor: pointer;">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Antworten Row (versteckt by default) -->
                                <tr id="answers-{{ $question->id }}" style="display: none; background: #f8fafc; border-bottom: 1px solid #e5e7eb;">
                                    <td colspan="6" style="padding: 1rem;">
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                            <div>
                                                <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Antwort A:</label>
                                                <textarea data-field="antwort_a" 
                                                          data-id="{{ $question->id }}"
                                                          onchange="updateQuestion(this)"
                                                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 6px; font-size: 0.9rem; resize: vertical; min-height: 80px; transition: all 0.2s;"
                                                          onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                                          onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">{{ $question->antwort_a }}</textarea>
                                            </div>
                                            <div>
                                                <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Antwort B:</label>
                                                <textarea data-field="antwort_b" 
                                                          data-id="{{ $question->id }}"
                                                          onchange="updateQuestion(this)"
                                                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 6px; font-size: 0.9rem; resize: vertical; min-height: 80px; transition: all 0.2s;"
                                                          onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                                          onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">{{ $question->antwort_b }}</textarea>
                                            </div>
                                            <div>
                                                <label style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 0.9rem; display: block;">Antwort C:</label>
                                                <textarea data-field="antwort_c" 
                                                          data-id="{{ $question->id }}"
                                                          onchange="updateQuestion(this)"
                                                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 6px; font-size: 0.9rem; resize: vertical; min-height: 80px; transition: all 0.2s;"
                                                          onfocus="this.style.background='white'; this.style.borderColor='#00337F'; this.style.boxShadow='0 0 0 2px rgba(0, 51, 127, 0.1)';"
                                                          onblur="this.style.background='#f9fafb'; this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">{{ $question->antwort_c }}</textarea>
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
                                            🗑️
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
                                📝 Antworten bearbeiten
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
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
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
        button.textContent = '🔼 Antworten verstecken';
        button.style.background = '#dc2626';
    } else {
        answersDiv.style.display = 'none';
        button.textContent = '📝 Antworten bearbeiten';
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
        button.textContent = '🔼 Verstecken';
        button.style.background = '#dc2626';
    } else {
        answersRow.style.display = 'none';
        button.textContent = '📝 Antworten';
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
