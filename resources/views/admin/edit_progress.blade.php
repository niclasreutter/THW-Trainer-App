@extends('layouts.app')
@section('title', 'Fortschritt bearbeiten - THW Trainer Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Fortschritt <span>bearbeiten</span></h1>
        <p class="page-subtitle">Verwalte den Lernfortschritt von {{ $user->name }}</p>
    </header>

    <!-- Benutzer Info Card -->
    <div class="glass-gold" style="padding: 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--gradient-gold); display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-person" style="font-size: 1.75rem; color: var(--thw-blue-dark);"></i>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">{{ $user->name }}</h2>
                <div style="color: var(--text-secondary); margin-bottom: 0.5rem;">{{ $user->email }}</div>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    @if($user->email_verified_at)
                        <span class="badge-success"><i class="bi bi-check"></i> E-Mail verifiziert</span>
                    @else
                        <span class="badge-error"><i class="bi bi-x"></i> E-Mail nicht verifiziert</span>
                    @endif
                    <span class="badge-glass">Registriert: {{ $user->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn-ghost">
                <i class="bi bi-arrow-left"></i> Zurück zur Nutzerverwaltung
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-glass success" style="margin-bottom: 1.5rem;">
            <i class="bi bi-check-circle" style="font-size: 1.25rem; color: var(--success);"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Spaced Repetition Verwaltung (eigene Form, außerhalb der Haupt-Form) -->
    @if(isset($srStats) && $srStats['total_in_system'] > 0)
        @php
            $futureCount = $srStats['total_in_system'] - $srStats['due_now'];
        @endphp

        <div class="section-header" style="margin-bottom: 1.5rem; padding-left: 1rem; border-left: 3px solid var(--gold-start);">
            <h2 class="section-title">Spaced Repetition</h2>
        </div>

        <div class="glass" style="margin-bottom: 2rem; padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem;">
                    <div style="font-size: 1.75rem; font-weight: 800; color: var(--gold);">{{ $srStats['due_now'] }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Heute fällig</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem;">
                    <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-secondary);">{{ $srStats['due_tomorrow'] }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Morgen fällig</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem;">
                    <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-secondary);">{{ $srStats['due_this_week'] }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Diese Woche</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 0.75rem;">
                    <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-muted);">{{ $srStats['total_in_system'] }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Gesamt im System</div>
                </div>
            </div>

            @if($futureCount > 0)
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $futureCount }} zukünftige Wiederholungen vorziehen</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Setzt alle geplanten SR-Fragen auf heute</div>
                    </div>
                    <form method="POST" action="{{ route('admin.users.progress.sr-pull-forward', $user->id) }}">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm" onclick="return confirm('{{ $futureCount }} Fragen auf heute vorziehen?')">
                            Alle auf heute setzen
                        </button>
                    </form>
                </div>
            @else
                <div style="padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.06); color: var(--text-muted); font-size: 0.875rem;">
                    Alle SR-Fragen sind bereits heute fällig oder es gibt keine zukünftigen Wiederholungen.
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.progress.update', $user->id) }}">
        @csrf
        @method('PUT')

        <!-- Lehrgänge Sektion mit Dropdowns -->
        @if($lehrgangData && !$lehrgangData->isEmpty())
            <div class="section-header" style="margin-bottom: 1.5rem; padding-left: 1rem; border-left: 3px solid var(--gold-start);">
                <h2 class="section-title">Lehrgänge - Fortschritt verwalten</h2>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                @foreach($lehrgangData as $lehrgangId => $data)
                    <div class="glass-tl" style="overflow: hidden;">
                        <button type="button"
                                onclick="toggleLehrgangDropdown('lehrgang-{{ $lehrgangId }}')"
                                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; background: transparent; border: none; cursor: pointer; text-align: left;">
                            <div style="flex: 1;">
                                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="bi bi-journal-text text-gold"></i>
                                    {{ $data['lehrgang']->lehrgang }}
                                </h3>
                                <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                    <span style="color: var(--text-secondary); font-size: 0.9rem;">
                                        <strong>{{ $data['totalSolved'] }}/{{ $data['totalQuestions'] }}</strong> Fragen gemeistert
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1; max-width: 200px;">
                                        <div class="progress-glass" style="flex: 1;">
                                            <div class="progress-fill-gold" style="width: {{ $data['totalPercent'] }}%;"></div>
                                        </div>
                                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); min-width: fit-content;">{{ $data['totalPercent'] }}%</span>
                                    </div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-down" id="lehrgang-{{ $lehrgangId }}-arrow" style="font-size: 1.25rem; color: var(--text-muted); transition: transform 0.2s;"></i>
                        </button>

                        <!-- Dropdown Content -->
                        <div id="lehrgang-{{ $lehrgangId }}" class="hidden" style="border-top: 1px solid rgba(255, 255, 255, 0.06); padding: 1.5rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h4 style="font-weight: 700; color: var(--success);">Gemeisterte Fragen (2x in Folge gelöst)</h4>
                                        <p style="font-size: 0.85rem; color: var(--text-muted);">{{ $data['totalSolved'] }} Fragen</p>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="button" onclick="selectAllLehrgangQuestions('lehrgang-{{ $lehrgangId }}-questions', true)" class="btn-secondary btn-sm">
                                        <i class="bi bi-check-all"></i> Alle auswählen
                                    </button>
                                    <button type="button" onclick="selectAllLehrgangQuestions('lehrgang-{{ $lehrgangId }}-questions', false)" class="btn-ghost btn-sm">
                                        <i class="bi bi-x-lg"></i> Alle abwählen
                                    </button>
                                </div>
                            </div>

                            <!-- Fragen Grid -->
                            <div id="lehrgang-{{ $lehrgangId }}-questions" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.75rem;">
                                @foreach($data['questions'] as $question)
                                    @php
                                        $progress = $data['progressData'][$question->id] ?? null;
                                        $isSolved = $progress && $progress->solved;
                                    @endphp
                                    <label class="question-checkbox {{ $isSolved ? 'checked' : '' }}" style="display: flex; align-items: flex-start; padding: 0.75rem; background: rgba(255, 255, 255, 0.03); border: 1px solid {{ $isSolved ? 'rgba(34, 197, 94, 0.3)' : 'rgba(255, 255, 255, 0.06)' }}; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s;">
                                        <input type="checkbox"
                                               name="lehrgang_{{ $lehrgangId }}_solved[]"
                                               value="{{ $question->id }}"
                                               @if($isSolved) checked @endif
                                               class="checkbox-glass" style="margin-top: 0.125rem; margin-right: 0.75rem;">
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">Frage {{ $question->nummer }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($question->frage, 40) }}</div>
                                            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">LA: {{ $question->lernabschnitt }}</div>
                                            @if($progress)
                                                <div style="font-size: 0.7rem; color: var(--success); font-weight: 600; margin-top: 0.25rem;"><i class="bi bi-check"></i> {{ $progress->consecutive_correct }}x richtig</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Statistiken Dashboard -->
        @php
            $threshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;
            $progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();
            $totalProgressPoints = 0;
            foreach ($progressData as $prog) {
                $totalProgressPoints += min($prog->consecutive_correct, $threshold);
            }
            $maxProgressPoints = $questions->count() * $threshold;
            $trueProgressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
        @endphp

        <div class="section-header" style="margin-bottom: 1.5rem; padding-left: 1rem; border-left: 3px solid var(--gold-start);">
            <h2 class="section-title">Statistik-Übersicht</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="glass-green" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="bi bi-check-circle" style="font-size: 2rem; color: var(--success);"></i>
                    <div>
                        <div id="solvedCount" style="font-size: 2rem; font-weight: 800; color: var(--success);">{{ count($solved) }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Gemeisterte Fragen</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">2x richtig in Folge</div>
                    </div>
                </div>
            </div>

            <div class="glass-error" style="padding: 1.5rem; background: rgba(239, 68, 68, 0.08) !important;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="bi bi-arrow-repeat" style="font-size: 2rem; color: var(--error);"></i>
                    <div>
                        <div id="failedCount" style="font-size: 2rem; font-weight: 800; color: var(--error);">{{ isset($failed) ? count($failed) : 0 }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Wiederholungsfragen</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Aus Prüfungen</div>
                    </div>
                </div>
            </div>

            <div class="glass-blue" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="bi bi-bar-chart" style="font-size: 2rem; color: var(--info);"></i>
                    <div>
                        <div style="font-size: 2rem; font-weight: 800;" class="text-gradient-gold">{{ $trueProgressPercent }}%</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Gesamt-Fortschritt</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Inkl. 1x richtige</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info-Box: Neue 2x-richtig Logik -->
        <div class="glass-accent" style="padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                <i class="bi bi-info-circle" style="font-size: 1.5rem; color: var(--gold);"></i>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Wichtig: "2x richtig in Folge" Logik</h3>
                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                        Seit dem Update müssen User jede Frage <strong>2x hintereinander richtig</strong> beantworten, um sie zu meistern.
                    </p>
                    <ul style="font-size: 0.8rem; color: var(--text-muted); list-style: disc; list-style-position: inside; display: flex; flex-direction: column; gap: 0.25rem;">
                        <li><strong>Gelöste Fragen:</strong> Wurden mindestens 2x richtig in Folge beantwortet</li>
                        <li><strong>Wiederholungsfragen:</strong> Nur aus Prüfungen (nicht aus Übungen)</li>
                        <li>Beim Speichern wird automatisch die <code style="background: rgba(255, 255, 255, 0.1); padding: 0.125rem 0.375rem; border-radius: 0.25rem;">user_question_progress</code> Tabelle aktualisiert</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Gelöste Fragen -->
        <div class="glass" style="margin-bottom: 2rem; overflow: hidden;">
            <div style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    <div>
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--success);">Gemeisterte Fragen (2x richtig)</h2>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Fragen die mindestens 2x in Folge richtig beantwortet wurden</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button type="button" onclick="toggleSection('solved-questions')" class="btn-ghost btn-sm">
                        <i class="bi bi-chevron-down" id="solved-questions-arrow"></i>
                        <span id="solved-questions-toggle-text">Aufklappen</span>
                    </button>
                    <button type="button" onclick="selectAll('solved_questions', true)" class="btn-secondary btn-sm">
                        <i class="bi bi-check-all"></i> Alle auswählen
                    </button>
                    <button type="button" onclick="selectAll('solved_questions', false)" class="btn-ghost btn-sm">
                        <i class="bi bi-x-lg"></i> Alle abwählen
                    </button>
                </div>
            </div>

            <div id="solved-questions-content" class="hidden" style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.75rem;">
                    @foreach($questions as $question)
                        @php $isSolved = in_array($question->id, $solved); @endphp
                        <label class="question-checkbox {{ $isSolved ? 'checked' : '' }}" style="display: flex; align-items: flex-start; padding: 0.75rem; background: rgba(255, 255, 255, 0.03); border: 1px solid {{ $isSolved ? 'rgba(34, 197, 94, 0.3)' : 'rgba(255, 255, 255, 0.06)' }}; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="solved_questions[]" value="{{ $question->id }}"
                                   @if($isSolved) checked @endif
                                   class="checkbox-glass" style="margin-top: 0.125rem; margin-right: 0.75rem;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">Frage {{ $question->id }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($question->frage, 40) }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">LA: {{ $question->lernabschnitt }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Wiederholungsfragen -->
        <div class="glass" style="margin-bottom: 2rem; overflow: hidden;">
            <div style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="bi bi-arrow-repeat text-error" style="font-size: 2rem;"></i>
                    <div>
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--error);">Wiederholungsfragen</h2>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Markiere Fragen, die in der Prüfung falsch beantwortet wurden</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button type="button" onclick="toggleSection('failed-questions')" class="btn-ghost btn-sm">
                        <i class="bi bi-chevron-down" id="failed-questions-arrow"></i>
                        <span id="failed-questions-toggle-text">Aufklappen</span>
                    </button>
                    <button type="button" onclick="selectAll('exam_failed_questions', true)" class="btn-danger btn-sm">
                        <i class="bi bi-check-all"></i> Alle auswählen
                    </button>
                    <button type="button" onclick="selectAll('exam_failed_questions', false)" class="btn-ghost btn-sm">
                        <i class="bi bi-x-lg"></i> Alle abwählen
                    </button>
                </div>
            </div>

            <div id="failed-questions-content" class="hidden" style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.75rem;">
                    @foreach($questions as $question)
                        @php $isFailed = isset($failed) && in_array($question->id, $failed); @endphp
                        <label class="question-checkbox {{ $isFailed ? 'checked-error' : '' }}" style="display: flex; align-items: flex-start; padding: 0.75rem; background: rgba(255, 255, 255, 0.03); border: 1px solid {{ $isFailed ? 'rgba(239, 68, 68, 0.3)' : 'rgba(255, 255, 255, 0.06)' }}; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="exam_failed_questions[]" value="{{ $question->id }}"
                                   @if($isFailed) checked @endif
                                   class="checkbox-glass" style="margin-top: 0.125rem; margin-right: 0.75rem;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">Frage {{ $question->id }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($question->frage, 40) }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">LA: {{ $question->lernabschnitt }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Aktionen -->
        <div class="glass-gold" style="padding: 2rem; text-align: center;">
            <i class="bi bi-save" style="font-size: 2.5rem; color: var(--gold); margin-bottom: 1rem; display: block;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Änderungen speichern</h3>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">Vergiss nicht, deine Änderungen zu speichern!</p>

            <button type="submit" class="btn-primary btn-lg" style="margin-bottom: 1.5rem;">
                <i class="bi bi-check-lg"></i> Änderungen speichern
            </button>

            <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 1.5rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-secondary btn-sm">
                    <i class="bi bi-person"></i> Benutzer bearbeiten
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">
                    <i class="bi bi-list"></i> Zur Übersicht
                </a>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleSection(sectionId) {
        const content = document.getElementById(sectionId + '-content');
        const arrow = document.getElementById(sectionId + '-arrow');
        const text = document.getElementById(sectionId + '-toggle-text');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            arrow.classList.remove('bi-chevron-down');
            arrow.classList.add('bi-chevron-up');
            text.textContent = 'Einklappen';
        } else {
            content.classList.add('hidden');
            arrow.classList.remove('bi-chevron-up');
            arrow.classList.add('bi-chevron-down');
            text.textContent = 'Aufklappen';
        }
    }

    function toggleLehrgangDropdown(dropdownId) {
        const content = document.getElementById(dropdownId);
        const arrow = document.getElementById(dropdownId + '-arrow');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            arrow.classList.remove('bi-chevron-down');
            arrow.classList.add('bi-chevron-up');
        } else {
            content.classList.add('hidden');
            arrow.classList.remove('bi-chevron-up');
            arrow.classList.add('bi-chevron-down');
        }
    }

    function selectAllLehrgangQuestions(containerSelector, checked) {
        const container = document.getElementById(containerSelector);
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = checked;
            const label = cb.closest('label');
            if (checked) {
                label.style.borderColor = 'rgba(34, 197, 94, 0.3)';
                label.style.background = 'rgba(34, 197, 94, 0.08)';
            } else {
                label.style.borderColor = 'rgba(255, 255, 255, 0.06)';
                label.style.background = 'rgba(255, 255, 255, 0.03)';
            }
        });
        showFeedback(checked ? 'Alle ausgewählt' : 'Alle abgewählt');
    }

    function selectAll(name, checked) {
        const checkboxes = document.querySelectorAll('input[name="'+name+'[]"]');
        const isError = name === 'exam_failed_questions';
        checkboxes.forEach(cb => {
            cb.checked = checked;
            const label = cb.closest('label');
            if (checked) {
                if (isError) {
                    label.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                    label.style.background = 'rgba(239, 68, 68, 0.08)';
                } else {
                    label.style.borderColor = 'rgba(34, 197, 94, 0.3)';
                    label.style.background = 'rgba(34, 197, 94, 0.08)';
                }
            } else {
                label.style.borderColor = 'rgba(255, 255, 255, 0.06)';
                label.style.background = 'rgba(255, 255, 255, 0.03)';
            }
        });

        updateCounters();
        showFeedback(checked ? 'Alle ausgewählt' : 'Alle abgewählt');
    }

    function updateCounters() {
        const solvedCount = document.querySelectorAll('input[name="solved_questions[]"]:checked').length;
        const failedCount = document.querySelectorAll('input[name="exam_failed_questions[]"]:checked').length;

        animateCounter('solvedCount', solvedCount);
        animateCounter('failedCount', failedCount);
    }

    function animateCounter(elementId, newValue) {
        const element = document.getElementById(elementId);
        if (!element) return;
        const currentValue = parseInt(element.textContent);

        if (currentValue !== newValue) {
            element.style.transform = 'scale(1.2)';
            setTimeout(() => {
                element.textContent = newValue;
                element.style.transform = 'scale(1)';
            }, 150);
        }
    }

    function showFeedback(message) {
        const feedback = document.createElement('div');
        feedback.style.cssText = 'position: fixed; top: 1rem; right: 1rem; padding: 0.875rem 1.5rem; background: var(--gradient-gold); color: var(--thw-blue-dark); font-weight: 600; border-radius: 0.75rem; z-index: 9999; transform: translateX(100%); transition: transform 0.3s ease; box-shadow: 0 8px 25px rgba(251, 191, 36, 0.4);';
        feedback.textContent = message;
        document.body.appendChild(feedback);

        setTimeout(() => {
            feedback.style.transform = 'translateX(0)';
        }, 100);

        setTimeout(() => {
            feedback.style.transform = 'translateX(calc(100% + 1rem))';
            setTimeout(() => {
                document.body.removeChild(feedback);
            }, 300);
        }, 2000);
    }

    // Event listener for checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox') {
            updateCounters();

            const label = e.target.closest('label');
            const isError = e.target.name === 'exam_failed_questions[]';

            if (e.target.checked) {
                if (isError) {
                    label.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                    label.style.background = 'rgba(239, 68, 68, 0.08)';
                } else {
                    label.style.borderColor = 'rgba(34, 197, 94, 0.3)';
                    label.style.background = 'rgba(34, 197, 94, 0.08)';
                }
            } else {
                label.style.borderColor = 'rgba(255, 255, 255, 0.06)';
                label.style.background = 'rgba(255, 255, 255, 0.03)';
            }
        }
    });
</script>

<style>
    .question-checkbox:hover {
        border-color: rgba(255, 255, 255, 0.15) !important;
        background: rgba(255, 255, 255, 0.06) !important;
    }
</style>
@endsection
