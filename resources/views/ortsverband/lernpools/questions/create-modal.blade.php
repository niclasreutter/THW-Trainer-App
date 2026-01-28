<!-- Modal Format (für AJAX) - Neue Frage erstellen - Glassmorphism -->
<div class="modal-header-glass">
    <h2>Neue Frage erstellen</h2>
    <button class="modal-close-btn" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">&times;</button>
</div>
<form id="createQuestionForm" action="{{ route('ortsverband.lernpools.questions.store', [$ortsverband, $lernpool]) }}" method="POST" onsubmit="return false;">
    @csrf
    <div class="modal-body-glass">
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">Frage für <strong style="color: var(--text-primary);">{{ $lernpool->name }}</strong></p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label for="lernabschnitt" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.85rem;">
                    Lernabschnitt <span style="color: var(--text-muted); font-weight: normal;">(optional)</span>
                </label>
                <input type="text" name="lernabschnitt" id="lernabschnitt"
                       class="input-glass" placeholder="z.B. 1.1"
                       list="lernabschnitt-suggestions"
                       onchange="updateNummer()">
                <datalist id="lernabschnitt-suggestions">
                    @foreach($existingSections as $section)
                        <option value="{{ $section }}">
                    @endforeach
                </datalist>
            </div>

            <div>
                <label for="nummer" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.85rem;">
                    Fragenummer <span style="color: #ef4444;">*</span>
                </label>
                <input type="number" name="nummer" id="nummer"
                       class="input-glass" min="1" value="{{ $nextNumber }}" required>
            </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
            <label for="frage" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.85rem;">
                Frage <span style="color: #ef4444;">*</span>
            </label>
            <textarea name="frage" id="frage" rows="2" class="textarea-glass" required></textarea>
        </div>

        <div style="margin-bottom: 0.75rem;">
            <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.85rem;">
                Antworten <span style="color: #ef4444;">*</span>
            </label>
            <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.75rem;">Wähle die korrekten Antworten mit dem Häkchen</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="a" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_a" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort A <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="antwort_a" id="antwort_a" class="input-glass" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="b" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_b" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort B <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="antwort_b" id="antwort_b" class="input-glass" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="c" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_c" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort C <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="antwort_c" id="antwort_c" class="input-glass" required>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer-glass">
        <button type="button" class="btn-ghost" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">
            Abbrechen
        </button>
        <button type="button" id="submitFinishBtn" class="btn-secondary">
            Speichern & Fertig
        </button>
        <button type="button" id="submitContinueBtn" class="btn-primary">
            Speichern & Weitere
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

<script>
const sectionNumbers = @json($sectionNumbers);

function updateNummer() {
    const section = document.getElementById('lernabschnitt').value;
    const nummerInput = document.getElementById('nummer');

    if (section && sectionNumbers[section]) {
        nummerInput.value = sectionNumbers[section] + 1;
    } else {
        nummerInput.value = {{ $nextNumber }};
    }
}
</script>
