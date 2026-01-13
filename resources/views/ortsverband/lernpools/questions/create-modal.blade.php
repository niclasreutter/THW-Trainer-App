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
            <label for="antwort_a" class="form-label">
                Antwort A <span class="required">*</span>
            </label>
            <input type="text" name="antwort_a" id="antwort_a" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="antwort_b" class="form-label">
                Antwort B <span class="required">*</span>
            </label>
            <input type="text" name="antwort_b" id="antwort_b" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="antwort_c" class="form-label">
                Antwort C <span class="required">*</span>
            </label>
            <input type="text" name="antwort_c" id="antwort_c" class="form-input" required>
        </div>

        <div class="form-group" style="background: #eff6ff; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #3b82f6;">
            <label class="form-label">
                Korrekte Antwort(en) <span class="required">*</span>
            </label>
            <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Wähle eine oder mehrere richtige Antworten</p>
            <div style="display: flex; gap: 0.5rem;">
                <label class="answer-toggle" style="flex: 1;">
                    <input type="checkbox" name="loesung[]" value="a" style="display: none;">
                    <span class="answer-btn">A</span>
                </label>
                <label class="answer-toggle" style="flex: 1;">
                    <input type="checkbox" name="loesung[]" value="b" style="display: none;">
                    <span class="answer-btn">B</span>
                </label>
                <label class="answer-toggle" style="flex: 1;">
                    <input type="checkbox" name="loesung[]" value="c" style="display: none;">
                    <span class="answer-btn">C</span>
                </label>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-modal-close" onclick="document.getElementById('genericModalBackdrop').classList.remove('active')">
            Abbrechen
        </button>
        <button type="button" name="action" value="finish" class="btn btn-secondary" style="background: #6b7280; color: white;" onclick="handleSubmit('finish')">
            ✓ Speichern & Fertig
        </button>
        <button type="button" name="action" value="continue" class="btn btn-primary" onclick="handleSubmit('continue')">
            ✓ Speichern & Weitere hinzufügen
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

// Globale Funktion für Button-Clicks
window.handleSubmit = function(action) {
    console.log('handleSubmit aufgerufen mit action:', action);

    const form = document.getElementById('createQuestionForm');
    if (!form) {
        console.error('Form nicht gefunden');
        alert('Fehler: Formular nicht gefunden!');
        return;
    }
    console.log('Form gefunden:', form);

    // Prüfe ob mindestens eine Lösung ausgewählt ist
    const loesungCheckboxes = form.querySelectorAll('input[name="loesung[]"]:checked');
    if (loesungCheckboxes.length === 0) {
        alert('Bitte wähle mindestens eine richtige Antwort aus (A, B oder C)!');
        return;
    }

    // Prüfe Browser-Validierung für required Felder
    const requiredInputs = form.querySelectorAll('[required]');
    let allValid = true;
    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            allValid = false;
            input.focus();
            input.reportValidity();
        }
    });

    if (!allValid) {
        console.log('Validierung fehlgeschlagen');
        return;
    }

    console.log('Validierung erfolgreich, sende Daten...');

    const formData = new FormData(form);
    const buttons = form.querySelectorAll('button[name="action"]');

    // Debug: Zeige FormData
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    // Disable buttons während Submit
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.style.opacity = '0.6';
    });

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response erhalten:', response);
        return response.json();
    })
    .then(data => {
        console.log('Data:', data);
        if (data.success) {
            showToast('✓ ' + data.message, 'success');

            if (action === 'continue') {
                console.log('Lade neues Formular...');
                // Lade Modal neu mit leerem Formular
                const createUrl = '{{ route("ortsverband.lernpools.questions.create", [$ortsverband, $lernpool]) }}?ajax=1&_t=' + Date.now();
                fetch(createUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('genericModal').innerHTML = '<div class="modal">' + html + '</div>';
                    console.log('Neues Formular geladen');
                });
            } else {
                console.log('Schließe Modal...');
                // Schließe Modal und lade Seite neu
                document.getElementById('genericModalBackdrop').classList.remove('active');
                setTimeout(() => {
                    location.reload();
                }, 300);
            }
        } else {
            showToast('✗ ' + (data.message || 'Fehler beim Speichern'), 'error');
            buttons.forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('✗ Fehler beim Speichern der Frage', 'error');
        buttons.forEach(btn => {
            btn.disabled = false;
            btn.style.opacity = '1';
        });
    });
};

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        border-radius: 8px;
        font-weight: 600;
        z-index: 10000;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        animation: slideInRight 0.3s ease-out;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Animation CSS hinzufügen
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
