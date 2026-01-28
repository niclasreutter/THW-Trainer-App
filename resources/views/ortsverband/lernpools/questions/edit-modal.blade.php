<!-- Modal Format (für AJAX) - Frage bearbeiten - Glassmorphism -->
<div class="modal-header-glass">
    <h2>Frage bearbeiten</h2>
    <button class="modal-close-btn" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">&times;</button>
</div>
<form id="editQuestionForm" action="{{ route('ortsverband.lernpools.questions.update', [$ortsverband, $lernpool, $question]) }}" method="POST" onsubmit="return false;">
    @csrf
    @method('PUT')
    <div class="modal-body-glass">
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">Frage in <strong style="color: var(--text-primary);">{{ $lernpool->name }}</strong> bearbeiten</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label for="lernabschnitt" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.85rem;">
                    Lernabschnitt <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                </label>
                <input type="text" name="lernabschnitt" id="lernabschnitt"
                       class="input-glass" placeholder="z.B. 1.1"
                       value="{{ old('lernabschnitt', $question->lernabschnitt) }}">
            </div>

            <div>
                <label for="nummer" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.85rem;">
                    Fragenummer <span style="color: #ef4444;">*</span>
                </label>
                <input type="number" name="nummer" id="nummer"
                       class="input-glass" min="1" value="{{ old('nummer', $question->nummer) }}" required>
            </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
            <label for="frage" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.85rem;">
                Frage <span style="color: #ef4444;">*</span>
            </label>
            <textarea name="frage" id="frage" rows="2" class="textarea-glass" required>{{ old('frage', $question->frage) }}</textarea>
        </div>

        @php
            $currentSolutions = collect(explode(',', $question->loesung))->map(fn($s) => strtolower(trim($s)));
        @endphp

        <div style="margin-bottom: 0.75rem;">
            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.85rem;">
                Antworten <span style="color: #ef4444;">*</span>
            </label>
            <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.75rem;">Wähle die korrekten Antworten mit dem Häkchen</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="a" class="answer-checkbox" {{ $currentSolutions->contains('a') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_a" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort A <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="antwort_a" id="antwort_a" class="input-glass" value="{{ old('antwort_a', $question->antwort_a) }}" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="b" class="answer-checkbox" {{ $currentSolutions->contains('b') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_b" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort B <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="antwort_b" id="antwort_b" class="input-glass" value="{{ old('antwort_b', $question->antwort_b) }}" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="c" class="answer-checkbox" {{ $currentSolutions->contains('c') ? 'checked' : '' }}>
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_c" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort C <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="antwort_c" id="antwort_c" class="input-glass" value="{{ old('antwort_c', $question->antwort_c) }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer-glass">
        <button type="button" class="btn-ghost" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">
            Abbrechen
        </button>
        <button type="button" id="updateQuestionBtn" class="btn-primary">
            Speichern
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
    border: 2.5px solid rgba(255, 255, 255, 0.2);
    border-radius: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.answer-checkbox-toggle:hover .checkbox-custom {
    border-color: var(--gold-start);
    background: rgba(251, 191, 36, 0.1);
}

.answer-checkbox:checked + .checkbox-custom {
    background: var(--gradient-gold);
    border-color: transparent;
}

.answer-checkbox:checked + .checkbox-custom::after {
    content: '\2713';
    color: #1e3a5f;
    font-size: 1rem;
    font-weight: bold;
    line-height: 1;
}
</style>
