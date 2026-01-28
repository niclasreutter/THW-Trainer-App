<!-- Question Create Modal - Glassmorphism Alpine.js -->
<div class="modal-header-glass">
    <h2>Neue Frage erstellen</h2>
    <button class="modal-close-btn" type="button">&times;</button>
</div>

<form id="createQuestionForm" action="{{ route('ortsverband.lernpools.questions.store', [$ortsverband, $lernpool]) }}" method="POST" onsubmit="return false;">
    @csrf
    <div class="modal-body-glass">
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
            Frage für <strong style="color: var(--text-primary);">{{ $lernpool->name }}</strong>
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label class="form-label-alpine">
                    Lernabschnitt <span class="optional-alpine">(optional)</span>
                </label>
                <input type="text" name="lernabschnitt" id="lernabschnitt"
                       class="input-alpine" placeholder="z.B. 1.1"
                       list="lernabschnitt-suggestions"
                       onchange="updateNummer()">
                <datalist id="lernabschnitt-suggestions">
                    @foreach($existingSections as $section)
                        <option value="{{ $section }}">
                    @endforeach
                </datalist>
            </div>

            <div>
                <label class="form-label-alpine">
                    Fragenummer <span class="required-alpine">*</span>
                </label>
                <input type="number" name="nummer" id="nummer"
                       class="input-alpine" min="1" value="{{ $nextNumber }}" required>
            </div>
        </div>

        <div class="form-group-alpine">
            <label class="form-label-alpine">
                Frage <span class="required-alpine">*</span>
            </label>
            <textarea name="frage" id="frage" rows="2" class="textarea-alpine" required></textarea>
        </div>

        <div style="margin-bottom: 0.75rem;">
            <label class="form-label-alpine" style="margin-bottom: 0.25rem;">
                Antworten <span class="required-alpine">*</span>
            </label>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">Wähle die korrekten Antworten mit dem Häkchen</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="a" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label class="form-label-alpine" style="margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort A <span class="required-alpine">*</span>
                    </label>
                    <input type="text" name="antwort_a" id="antwort_a" class="input-alpine" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="b" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label class="form-label-alpine" style="margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort B <span class="required-alpine">*</span>
                    </label>
                    <input type="text" name="antwort_b" id="antwort_b" class="input-alpine" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="c" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label class="form-label-alpine" style="margin-bottom: 0.25rem; font-size: 0.8rem;">
                        Antwort C <span class="required-alpine">*</span>
                    </label>
                    <input type="text" name="antwort_c" id="antwort_c" class="input-alpine" required>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer-glass">
        <button type="button" class="btn-ghost modal-close-btn">Abbrechen</button>
        <button type="button" id="submitFinishBtn" class="btn-secondary">Speichern & Fertig</button>
        <button type="button" id="submitContinueBtn" class="btn-primary">Speichern & Weitere</button>
    </div>
</form>

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
