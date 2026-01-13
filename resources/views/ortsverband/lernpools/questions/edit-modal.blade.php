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

        <div class="form-group">
            <label for="antwort_a" class="form-label">
                Antwort A <span class="required">*</span>
            </label>
            <input type="text" name="antwort_a" id="antwort_a" class="form-input" value="{{ old('antwort_a', $question->antwort_a) }}" required>
        </div>

        <div class="form-group">
            <label for="antwort_b" class="form-label">
                Antwort B <span class="required">*</span>
            </label>
            <input type="text" name="antwort_b" id="antwort_b" class="form-input" value="{{ old('antwort_b', $question->antwort_b) }}" required>
        </div>

        <div class="form-group">
            <label for="antwort_c" class="form-label">
                Antwort C <span class="required">*</span>
            </label>
            <input type="text" name="antwort_c" id="antwort_c" class="form-input" value="{{ old('antwort_c', $question->antwort_c) }}" required>
        </div>

        @php
            $currentSolutions = collect(explode(',', $question->loesung))->map(fn($s) => strtolower(trim($s)));
        @endphp

        <div class="form-group" style="background: #eff6ff; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #3b82f6;">
            <label class="form-label">
                Korrekte Antwort(en) <span class="required">*</span>
            </label>
            <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Wähle eine oder mehrere richtige Antworten</p>
            <div style="display: flex; gap: 0.5rem;">
                <label class="answer-toggle" style="flex: 1;">
                    <input type="checkbox" name="loesung[]" value="a" style="display: none;" {{ $currentSolutions->contains('a') ? 'checked' : '' }}>
                    <span class="answer-btn">A</span>
                </label>
                <label class="answer-toggle" style="flex: 1;">
                    <input type="checkbox" name="loesung[]" value="b" style="display: none;" {{ $currentSolutions->contains('b') ? 'checked' : '' }}>
                    <span class="answer-btn">B</span>
                </label>
                <label class="answer-toggle" style="flex: 1;">
                    <input type="checkbox" name="loesung[]" value="c" style="display: none;" {{ $currentSolutions->contains('c') ? 'checked' : '' }}>
                    <span class="answer-btn">C</span>
                </label>
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
.answer-toggle input:checked + .answer-btn {
    background: #00337F;
    color: white;
    border-color: #00337F;
}
.answer-btn {
    display: block;
    text-align: center;
    padding: 0.75rem 1rem;
    border: 2px solid #d1d5db;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}
.answer-btn:hover {
    border-color: #00337F;
    background: #f0f4ff;
}
</style>
