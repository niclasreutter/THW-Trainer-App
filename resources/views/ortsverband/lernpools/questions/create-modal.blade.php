<!-- Modal Format (für AJAX) - Neue Frage erstellen -->
<div class="modal-header">
    <h2>Neue Frage erstellen</h2>
    <button class="modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">✕</button>
</div>
<form id="createQuestionForm" action="{{ route('ortsverband.lernpools.questions.store', [$ortsverband, $lernpool]) }}" method="POST" onsubmit="return false;">
    @csrf
    <div class="modal-body">
        <p class="text-sm text-gray-600 mb-4">Frage für <strong>{{ $lernpool->name }}</strong></p>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="lernabschnitt" class="form-label">
                    Lernabschnitt <span style="color: #6b7280; font-weight: normal;">(optional)</span>
                </label>
                <input type="text" name="lernabschnitt" id="lernabschnitt" 
                       class="form-input" placeholder="z.B. 1.1"
                       list="lernabschnitt-suggestions"
                       onchange="updateNummer()">
                <datalist id="lernabschnitt-suggestions">
                    @foreach($existingSections as $section)
                        <option value="{{ $section }}">
                    @endforeach
                </datalist>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="nummer" class="form-label">
                    Fragenummer <span class="required">*</span>
                </label>
                <input type="number" name="nummer" id="nummer" 
                       class="form-input" min="1" value="{{ $nextNumber }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="frage" class="form-label">
                Frage <span class="required">*</span>
            </label>
            <textarea name="frage" id="frage" rows="2" class="form-textarea" required></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" style="margin-bottom: 0.75rem;">
                Antworten <span class="required">*</span>
            </label>
            <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Wähle die korrekten Antworten mit dem Häkchen</p>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="a" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_a" class="form-label" style="margin-bottom: 0.25rem;">
                        Antwort A <span class="required">*</span>
                    </label>
                    <input type="text" name="antwort_a" id="antwort_a" class="form-input" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="b" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_b" class="form-label" style="margin-bottom: 0.25rem;">
                        Antwort B <span class="required">*</span>
                    </label>
                    <input type="text" name="antwort_b" id="antwort_b" class="form-input" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label class="answer-checkbox-toggle">
                    <input type="checkbox" name="loesung[]" value="c" class="answer-checkbox">
                    <span class="checkbox-custom"></span>
                </label>
                <div style="flex: 1;">
                    <label for="antwort_c" class="form-label" style="margin-bottom: 0.25rem;">
                        Antwort C <span class="required">*</span>
                    </label>
                    <input type="text" name="antwort_c" id="antwort_c" class="form-input" required>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">
            Abbrechen
        </button>
        <button type="button" id="submitFinishBtn" class="btn btn-secondary" style="background: #6b7280; color: white;">
            ✓ Speichern & Fertig
        </button>
        <button type="button" id="submitContinueBtn" class="btn btn-primary">
            ✓ Speichern & Weitere hinzufügen
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

// Event-Handler werden jetzt via Event-Delegation von der Parent-Seite (index.blade.php) behandelt
// Keine Scripts hier nötig!
</script>
