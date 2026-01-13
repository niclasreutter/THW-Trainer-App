<!-- Modal Format (für AJAX) - Frage bearbeiten -->
<div class="modal-header">
    <h2>Frage bearbeiten</h2>
    <button class="modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">✕</button>
</div>
<form id="editQuestionForm" action="{{ route('ortsverband.lernpools.questions.update', [$ortsverband, $lernpool, $question]) }}" method="POST" onsubmit="return false;">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <p class="text-sm text-gray-600 mb-4">Frage in <strong>{{ $lernpool->name }}</strong> bearbeiten</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="lernabschnitt" class="form-label">
                    Lernabschnitt <span style="color: #6b7280; font-weight: normal;">(optional)</span>
                </label>
                <input type="text" name="lernabschnitt" id="lernabschnitt"
                       class="form-input" placeholder="z.B. 1.1"
                       value="{{ old('lernabschnitt', $question->lernabschnitt) }}">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="nummer" class="form-label">
                    Fragenummer <span class="required">*</span>
                </label>
                <input type="number" name="nummer" id="nummer"
                       class="form-input" min="1" value="{{ old('nummer', $question->nummer) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="frage" class="form-label">
                Frage <span class="required">*</span>
            </label>
            <textarea name="frage" id="frage" rows="2" class="form-textarea" required>{{ old('frage', $question->frage) }}</textarea>
        </div>

        @php
            $currentSolutions = collect(explode(',', $question->loesung))->map(fn($s) => strtolower(trim($s)));
        @endphp

        <div class="form-group">
            <label class="form-label" style="margin-bottom: 0.75rem;">
                Antworten <span class="required">*</span>
            </label>
            <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Wähle die korrekten Antworten mit dem Häkchen</p>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="a" class="answer-checkbox" {{ $currentSolutions->contains('a') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_a" class="form-label" style="margin-bottom: 0.25rem;">
                        Antwort A <span class="required">*</span>
                    </label>
                    <input type="text" name="antwort_a" id="antwort_a" class="form-input" value="{{ old('antwort_a', $question->antwort_a) }}" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="b" class="answer-checkbox" {{ $currentSolutions->contains('b') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_b" class="form-label" style="margin-bottom: 0.25rem;">
                        Antwort B <span class="required">*</span>
                    </label>
                    <input type="text" name="antwort_b" id="antwort_b" class="form-input" value="{{ old('antwort_b', $question->antwort_b) }}" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="c" class="answer-checkbox" {{ $currentSolutions->contains('c') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_c" class="form-label" style="margin-bottom: 0.25rem;">
                        Antwort C <span class="required">*</span>
                    </label>
                    <input type="text" name="antwort_c" id="antwort_c" class="form-input" value="{{ old('antwort_c', $question->antwort_c) }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">
            Abbrechen
        </button>
        <button type="button" id="updateQuestionBtn" class="btn btn-primary">
            ✓ Speichern
        </button>
    </div>
</form>

<style>
.answer-checkbox-toggle {
    position: relative;
    cursor: pointer;
    min-width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.answer-checkbox {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.checkbox-custom {
    width: 28px;
    height: 28px;
    border: 2.5px solid #d1d5db;
    border-radius: 0.5rem;
    background: white;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.answer-checkbox-toggle:hover .checkbox-custom {
    border-color: #00337F;
    background: #f0f4ff;
}

.answer-checkbox:checked + .checkbox-custom {
    background: #00337F;
    border-color: #00337F;
}

.answer-checkbox:checked + .checkbox-custom::after {
    content: '✓';
    color: white;
    font-size: 1.2rem;
    font-weight: bold;
    line-height: 1;
}
</style>
