<!-- Question Edit Modal - Glassmorphism Alpine.js -->
<div class="modal-header-glass">
    <h2>Frage bearbeiten</h2>
    <button class="modal-close-btn" type="button">&times;</button>
</div>

<form id="editQuestionForm" action="{{ route('ortsverband.lernpools.questions.update', [$ortsverband, $lernpool, $question]) }}" method="POST" onsubmit="return false;">
    @csrf
    @method('PUT')
    <div class="modal-body-glass">
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
            Frage in <strong style="color: var(--text-primary);">{{ $lernpool->name }}</strong> bearbeiten
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label class="form-label-alpine">
                    Lernabschnitt <span class="optional-alpine">(optional)</span>
                </label>
                <input type="text" name="lernabschnitt" id="lernabschnitt"
                       class="input-alpine" placeholder="z.B. 1.1"
                       value="{{ old('lernabschnitt', $question->lernabschnitt) }}">
            </div>

            <div>
                <label class="form-label-alpine">
                    Fragenummer <span class="required-alpine">*</span>
                </label>
                <input type="number" name="nummer" id="nummer"
                       class="input-alpine" min="1" value="{{ old('nummer', $question->nummer) }}" required>
            </div>
        </div>

        <div class="form-group-alpine">
            <label class="form-label-alpine">
                Frage <span class="required-alpine">*</span>
            </label>
            <textarea name="frage" id="frage" rows="2" class="textarea-alpine" required>{{ old('frage', $question->frage) }}</textarea>
        </div>

        @php
            $currentSolutions = collect(explode(',', $question->loesung))->map(fn($s) => strtolower(trim($s)));
        @endphp

        <div style="margin-bottom: 0.75rem;">
            <label class="form-label-alpine" style="margin-bottom: 0.25rem;">
                Antworten <span class="required-alpine">*</span>
            </label>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">Wähle die korrekten Antworten mit dem Häkchen</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="a" class="answer-checkbox" {{ $currentSolutions->contains('a') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label class="form-label-alpine" style="margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort A <span class="required-alpine">*</span>
                    </label>
                    <input type="text" name="antwort_a" id="antwort_a" class="input-alpine" value="{{ old('antwort_a', $question->antwort_a) }}" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="b" class="answer-checkbox" {{ $currentSolutions->contains('b') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label class="form-label-alpine" style="margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort B <span class="required-alpine">*</span>
                    </label>
                    <input type="text" name="antwort_b" id="antwort_b" class="input-alpine" value="{{ old('antwort_b', $question->antwort_b) }}" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="c" class="answer-checkbox" {{ $currentSolutions->contains('c') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label class="form-label-alpine" style="margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort C <span class="required-alpine">*</span>
                    </label>
                    <input type="text" name="antwort_c" id="antwort_c" class="input-alpine" value="{{ old('antwort_c', $question->antwort_c) }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer-glass">
        <button type="button" class="btn-ghost modal-close-btn">Abbrechen</button>
        <button type="button" id="updateQuestionBtn" class="btn-primary">Speichern</button>
    </div>
</form>
