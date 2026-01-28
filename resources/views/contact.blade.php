@extends('layouts.app')

@section('title', 'Kontakt & Feedback - THW Trainer')
@section('description', 'Kontaktiere mich bei Fragen, Feedback oder Problemen. Ich helfe dir gerne weiter!')

@push('styles')
<style>
    .contact-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2rem;
        padding-top: 1rem;
    }

    /* Radio Options Grid */
    .radio-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 640px) {
        .radio-grid {
            grid-template-columns: 1fr;
        }
        .contact-container {
            padding: 1rem;
        }
    }

    .radio-option {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 0.75rem;
        padding: 1rem;
        cursor: pointer;
        transition: all var(--transition-normal);
    }

    .radio-option:hover {
        border-color: rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.05);
    }

    .radio-option:has(input[type="radio"]:checked) {
        background: rgba(251, 191, 36, 0.08);
        border-color: rgba(251, 191, 36, 0.3);
    }

    .radio-label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
    }

    .radio-label-icon {
        font-size: 1.25rem;
        color: var(--text-muted);
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    .radio-option:has(input[type="radio"]:checked) .radio-label-icon {
        color: var(--gold-start);
    }

    .radio-label-content {
        flex: 1;
    }

    .radio-label-title {
        font-weight: 600;
        color: var(--text-primary);
        display: block;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .radio-label-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .radio-label input[type="radio"] {
        display: none;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .required {
        color: var(--error);
    }

    .help-text {
        color: var(--text-muted);
        font-size: 0.8rem;
        margin-top: 0.4rem;
    }

    /* Checkbox Group */
    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        background: rgba(0, 51, 127, 0.08);
        border: 1px solid rgba(0, 51, 127, 0.2);
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .checkbox-input {
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 0.15rem;
        accent-color: var(--gold-start);
        cursor: pointer;
        flex-shrink: 0;
    }

    .checkbox-label {
        cursor: pointer;
        flex: 1;
    }

    .checkbox-label-text {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
        display: block;
        font-size: 0.9rem;
    }

    .checkbox-description {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    /* Conditional Fields */
    .conditional-field {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            max-height: 500px;
            transform: translateY(0);
        }
    }

    /* Hermine Fields Box */
    .hermine-fields-box {
        background: rgba(0, 51, 127, 0.06);
        border: 1px solid rgba(0, 51, 127, 0.15);
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .hermine-fields-box h3 {
        font-weight: 600;
        color: var(--thw-blue-light);
        margin: 0 0 1rem 0;
        font-size: 0.95rem;
    }

    .hermine-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 500px) {
        .hermine-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Character Counter */
    .char-count {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* Privacy Text */
    .privacy-text {
        font-size: 0.8rem;
        text-align: center;
        color: var(--text-muted);
        margin-top: 1.5rem;
    }

    /* Alert Styling */
    .alert-compact {
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .alert-compact-icon {
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .alert-compact-content {
        flex: 1;
    }

    .alert-compact-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .alert-compact-desc {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .alert-compact ul {
        list-style: disc;
        margin-left: 1.25rem;
        margin-top: 0.5rem;
    }

    .alert-compact li {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    /* Light Mode Overrides */
    html.light-mode .radio-option {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.15);
    }

    html.light-mode .radio-option:hover {
        background: rgba(0, 51, 127, 0.03);
        border-color: rgba(0, 51, 127, 0.25);
    }

    html.light-mode .radio-option:has(input[type="radio"]:checked) {
        background: rgba(217, 119, 6, 0.08);
        border-color: rgba(217, 119, 6, 0.4);
    }

    html.light-mode .radio-option:has(input[type="radio"]:checked) .radio-label-icon {
        color: #d97706;
    }

    html.light-mode .checkbox-group {
        background: linear-gradient(135deg, rgba(0, 51, 127, 0.06) 0%, rgba(0, 77, 179, 0.04) 100%);
        border-color: rgba(0, 51, 127, 0.2);
    }

    html.light-mode .hermine-fields-box {
        background: linear-gradient(135deg, rgba(0, 51, 127, 0.05) 0%, rgba(0, 77, 179, 0.03) 100%);
        border-color: rgba(0, 51, 127, 0.15);
    }

    html.light-mode .hermine-fields-box h3 {
        color: var(--thw-blue);
    }

    /* Light Mode: Textarea mit sichtbarem Rahmen */
    html.light-mode .textarea-glass {
        background: #ffffff !important;
        border: 1px solid rgba(0, 51, 127, 0.2) !important;
        color: #1e293b !important;
    }

    html.light-mode .textarea-glass:focus {
        border-color: var(--thw-blue) !important;
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1) !important;
    }

    html.light-mode .textarea-glass::placeholder {
        color: #94a3b8 !important;
    }
</style>
@endpush

@section('content')
<div class="contact-container">
    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="page-title">Kontakt & <span>Feedback</span></h1>
        <p class="page-subtitle">Dein Feedback ist mir wichtig! Schreib mir bei Fragen, Ideen oder Problemen.</p>
    </header>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert-compact glass-success">
            <i class="bi bi-check-circle-fill alert-compact-icon text-success"></i>
            <div class="alert-compact-content">
                <div class="alert-compact-title">Nachricht gesendet!</div>
                <div class="alert-compact-desc">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert-compact glass-error">
            <i class="bi bi-x-circle-fill alert-compact-icon text-error"></i>
            <div class="alert-compact-content">
                <div class="alert-compact-title">Bitte überprüfe deine Eingaben:</div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Contact Form -->
    <div class="glass-tl" style="padding: 1.5rem;">
        <form method="POST" action="{{ route('contact.submit') }}" id="contactForm">
            @csrf

            <!-- Honeypot -->
            <input type="text" name="website" style="display:none !important;" tabindex="-1" autocomplete="off">

            <!-- Category Selection -->
            <div class="form-group">
                <label class="form-label">Was möchtest du mir mitteilen? <span class="required">*</span></label>
                <div class="radio-grid">
                    <div class="radio-option">
                        <label class="radio-label">
                            <input type="radio" name="type" value="feedback"
                                   onchange="updateFormType(this.value)"
                                   {{ old('type') == 'feedback' ? 'checked' : '' }} required>
                            <span class="radio-label-icon"><i class="bi bi-chat-dots"></i></span>
                            <div class="radio-label-content">
                                <span class="radio-label-title">Feedback</span>
                                <span class="radio-label-desc">Lob, Kritik, Verbesserungsvorschläge</span>
                            </div>
                        </label>
                    </div>

                    <div class="radio-option">
                        <label class="radio-label">
                            <input type="radio" name="type" value="feature"
                                   onchange="updateFormType(this.value)"
                                   {{ old('type') == 'feature' ? 'checked' : '' }} required>
                            <span class="radio-label-icon"><i class="bi bi-lightbulb"></i></span>
                            <div class="radio-label-content">
                                <span class="radio-label-title">Feature-Wunsch</span>
                                <span class="radio-label-desc">Neue Funktionen vorschlagen</span>
                            </div>
                        </label>
                    </div>

                    <div class="radio-option">
                        <label class="radio-label">
                            <input type="radio" name="type" value="bug"
                                   onchange="updateFormType(this.value)"
                                   {{ old('type') == 'bug' ? 'checked' : '' }} required>
                            <span class="radio-label-icon"><i class="bi bi-bug"></i></span>
                            <div class="radio-label-content">
                                <span class="radio-label-title">Fehler melden</span>
                                <span class="radio-label-desc">Etwas funktioniert nicht?</span>
                            </div>
                        </label>
                    </div>

                    <div class="radio-option">
                        <label class="radio-label">
                            <input type="radio" name="type" value="other"
                                   onchange="updateFormType(this.value)"
                                   {{ old('type') == 'other' ? 'checked' : '' }} required>
                            <span class="radio-label-icon"><i class="bi bi-envelope"></i></span>
                            <div class="radio-label-content">
                                <span class="radio-label-title">Sonstiges</span>
                                <span class="radio-label-desc">Allgemeine Anfrage</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Deine E-Mail-Adresse <span class="required">*</span></label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', auth()->user()->email ?? '') }}"
                       class="input-glass @error('email') border-red-500 @enderror"
                       placeholder="deine@email.de"
                       required>
                <p class="help-text">Du erhältst eine Kopie deiner Anfrage an diese Adresse</p>
            </div>

            <!-- Hermine Contact -->
            <div class="checkbox-group">
                <input type="checkbox" id="hermine_contact" name="hermine_contact" value="1"
                       onchange="toggleHermineFields()"
                       {{ old('hermine_contact') ? 'checked' : '' }} class="checkbox-input">
                <label for="hermine_contact" class="checkbox-label">
                    <span class="checkbox-label-text"><i class="bi bi-phone"></i> Kontakt über Hermine</span>
                    <span class="checkbox-description">
                        Ich bin einverstanden, dass ich über die THW-Messenger-App Hermine kontaktiert werde
                    </span>
                </label>
            </div>

            <!-- Hermine Fields (conditional) -->
            <div id="hermineFields" class="hermine-fields-box conditional-field" style="display: none;">
                <h3><i class="bi bi-person"></i> Deine Hermine-Daten</h3>

                <div class="hermine-grid">
                    <div>
                        <label for="vorname" class="form-label">Vorname <span class="required">*</span></label>
                        <input type="text" id="vorname" name="vorname" value="{{ old('vorname') }}"
                               class="input-glass" placeholder="Max">
                    </div>
                    <div>
                        <label for="nachname" class="form-label">Nachname <span class="required">*</span></label>
                        <input type="text" id="nachname" name="nachname" value="{{ old('nachname') }}"
                               class="input-glass" placeholder="Mustermann">
                    </div>
                </div>

                <div>
                    <label for="ortsverband" class="form-label">Ortsverband <span class="required">*</span></label>
                    <input type="text" id="ortsverband" name="ortsverband" value="{{ old('ortsverband') }}"
                           class="input-glass" placeholder="z.B. OV Musterstadt">
                </div>
            </div>

            <!-- Bug Location (conditional) -->
            <div id="bugFields" class="conditional-field form-group" style="display: none;">
                <label for="error_location" class="form-label"><i class="bi bi-bug"></i> Wo ist der Fehler aufgetreten? <span class="required">*</span></label>
                <select id="error_location" name="error_location" class="select-glass">
                    <option value="">Bitte auswählen...</option>
                    <option value="dashboard" {{ old('error_location') == 'dashboard' ? 'selected' : '' }}>Dashboard</option>
                    <option value="questions" {{ old('error_location') == 'questions' ? 'selected' : '' }}>Fragen üben</option>
                    <option value="failed_questions" {{ old('error_location') == 'failed_questions' ? 'selected' : '' }}>Fehler wiederholen</option>
                    <option value="statistics" {{ old('error_location') == 'statistics' ? 'selected' : '' }}>Statistiken</option>
                    <option value="achievements" {{ old('error_location') == 'achievements' ? 'selected' : '' }}>Achievements</option>
                    <option value="profile" {{ old('error_location') == 'profile' ? 'selected' : '' }}>Profil</option>
                    <option value="login" {{ old('error_location') == 'login' ? 'selected' : '' }}>Login/Registrierung</option>
                    <option value="other" {{ old('error_location') == 'other' ? 'selected' : '' }}>Sonstiges</option>
                </select>
            </div>

            <!-- Message -->
            <div class="form-group">
                <label for="message" class="form-label">
                    <span id="messageLabel">Deine Nachricht</span> <span class="required">*</span>
                </label>
                <textarea id="message" name="message" required minlength="10" maxlength="5000"
                          class="textarea-glass @error('message') border-red-500 @enderror"
                          placeholder="Schreib mir dein Anliegen..."
                          style="min-height: 150px;">{{ old('message') }}</textarea>
                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                    <p class="help-text">Mindestens 10 Zeichen</p>
                    <p class="char-count"><span id="charCount">0</span> / 5000</p>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="btn-primary" style="width: 100%;">
                Nachricht absenden
            </button>

            <p class="privacy-text"><i class="bi bi-lock"></i> Deine Daten werden vertraulich behandelt und nicht an Dritte weitergegeben.</p>
        </form>
    </div>
</div>

<script>
    function updateFormType(type) {
        const bugFields = document.getElementById('bugFields');
        const messageLabel = document.getElementById('messageLabel');
        const errorLocation = document.getElementById('error_location');

        bugFields.style.display = 'none';
        errorLocation.removeAttribute('required');

        if (type === 'bug') {
            bugFields.style.display = 'block';
            errorLocation.setAttribute('required', 'required');
            messageLabel.textContent = 'Beschreibe den Fehler';
        } else if (type === 'feedback') {
            messageLabel.textContent = 'Dein Feedback';
        } else if (type === 'feature') {
            messageLabel.textContent = 'Beschreibe deinen Feature-Wunsch';
        } else {
            messageLabel.textContent = 'Deine Nachricht';
        }
    }

    function toggleHermineFields() {
        const checkbox = document.getElementById('hermine_contact');
        const fields = document.getElementById('hermineFields');
        const vorname = document.getElementById('vorname');
        const nachname = document.getElementById('nachname');
        const ortsverband = document.getElementById('ortsverband');

        if (checkbox.checked) {
            fields.style.display = 'block';
            vorname.setAttribute('required', 'required');
            nachname.setAttribute('required', 'required');
            ortsverband.setAttribute('required', 'required');
        } else {
            fields.style.display = 'none';
            vorname.removeAttribute('required');
            nachname.removeAttribute('required');
            ortsverband.removeAttribute('required');
        }
    }

    // Character counter
    document.getElementById('message').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });

    // Form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Wird gesendet...';
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize character count
        const message = document.getElementById('message');
        if (message.value) {
            document.getElementById('charCount').textContent = message.value.length;
        }

        // Initialize hermine fields if checked
        if (document.getElementById('hermine_contact').checked) {
            toggleHermineFields();
        }

        // Initialize form type
        const selectedType = document.querySelector('input[name="type"]:checked');
        if (selectedType) {
            updateFormType(selectedType.value);
        }
    });
</script>

@endsection
