@extends('layouts.app')

@section('title', 'Kontakt & Feedback - THW Trainer')
@section('description', 'Kontaktiere mich bei Fragen, Feedback oder Problemen. Ich helfe dir gerne weiter!')

@push('styles')
<style>
    * { box-sizing: border-box; }

    .contact-wrapper { min-height: 100vh; background: #f3f4f6; position: relative; overflow-x: hidden; }

    .contact-container { max-width: 800px; margin: 0 auto; padding: 2rem; position: relative; z-index: 1; }

    .contact-header { text-align: center; margin-bottom: 3rem; padding-top: 1rem; }

    .contact-header h1 { font-size: 2.5rem; font-weight: 800; color: #00337F; margin-bottom: 0.5rem; line-height: 1.2; }

    .contact-subtitle { font-size: 1.1rem; color: #4b5563; margin-bottom: 0; }

    .alert-box { border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; display: flex; gap: 1rem; }

    .alert-success { background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%); border: 1px solid #22c55e; }

    .alert-error { background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%); border: 1px solid #ef4444; }

    .alert-icon { font-size: 1.5rem; flex-shrink: 0; }

    .alert-content h3 { margin: 0 0 0.5rem 0; font-weight: 700; font-size: 1rem; }

    .alert-content p, .alert-content li { margin: 0; font-size: 0.95rem; }

    .alert-success .alert-content h3, .alert-success .alert-content p { color: #16a34a; }
    .alert-error .alert-content h3, .alert-error .alert-content p, .alert-error .alert-content li { color: #dc2626; }

    .card { background: white; border-radius: 10px; border: 1px solid #e5e7eb; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }

    .form-group { margin-bottom: 1.5rem; }

    .form-label { display: block; font-weight: 600; color: #1f2937; margin-bottom: 0.75rem; font-size: 0.95rem; }

    .required { color: #ef4444; }

    .form-input, .form-select, .form-textarea { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; transition: all 0.3s; font-family: inherit; }

    .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #00337F; box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1); }

    .form-textarea { resize: vertical; min-height: 150px; }

    .help-text { color: #6b7280; font-size: 0.85rem; margin-top: 0.4rem; }

    .radio-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }

    .radio-option { border: 2px solid #e5e7eb; border-radius: 8px; padding: 1.25rem; cursor: pointer; transition: all 0.3s; }

    .radio-option:hover { border-color: #00337F; background: #f9fafb; }

    .radio-option input[type="radio"]:checked { margin-right: 0; }

    .radio-option:has(input[type="radio"]:checked) { background: linear-gradient(135deg, rgba(0, 51, 127, 0.05) 0%, rgba(0, 63, 153, 0.05) 100%); border-color: #00337F; box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1); }

    .radio-label { display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer; }

    .radio-label-icon { font-size: 1.5rem; flex-shrink: 0; }

    .radio-label-content { flex: 1; }

    .radio-label-title { font-weight: 600; color: #1f2937; display: block; margin-bottom: 0.25rem; }

    .radio-label-desc { font-size: 0.85rem; color: #6b7280; }

    .checkbox-group { display: flex; align-items: flex-start; gap: 0.75rem; padding: 1.25rem; background: linear-gradient(135deg, rgba(0, 51, 127, 0.05) 0%, rgba(0, 63, 153, 0.05) 100%); border: 1px solid #dbeafe; border-radius: 8px; margin-bottom: 1.5rem; }

    .checkbox-input { width: 1.25rem; height: 1.25rem; margin-top: 0.15rem; accent-color: #00337F; cursor: pointer; flex-shrink: 0; }

    .checkbox-label { cursor: pointer; flex: 1; }

    .checkbox-label-text { font-weight: 600; color: #1f2937; margin-bottom: 0.4rem; display: block; }

    .checkbox-description { color: #6b7280; font-size: 0.9rem; margin-bottom: 0.5rem; }

    .conditional-field { animation: slideDown 0.3s ease-out; }

    @keyframes slideDown { from { opacity: 0; max-height: 0; transform: translateY(-10px); } to { opacity: 1; max-height: 500px; transform: translateY(0); } }

    .button { width: 100%; padding: 0.875rem 1.5rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-size: 1rem; }

    .button-primary { background: linear-gradient(135deg, #00337F 0%, #003F99 100%); color: white; }

    .button-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 51, 127, 0.3); }

    .button-primary:disabled { opacity: 0.7; cursor: not-allowed; }

    .privacy-text { font-size: 0.8rem; text-align: center; color: #6b7280; margin-top: 1.5rem; }

    .char-count { font-size: 0.85rem; color: #9ca3af; }

    @media (max-width: 768px) {
        .contact-container { padding: 1rem; }
        .contact-header h1 { font-size: 2rem; }
        .card { padding: 1.5rem; }
        .radio-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="contact-wrapper">
    <div class="contact-container">
        <!-- Header -->
        <div class="contact-header">
            <h1>📬 Kontakt & Feedback</h1>
            <p class="contact-subtitle">Dein Feedback ist mir wichtig! Schreib mir bei Fragen, Ideen oder Problemen.</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert-box alert-success">
                <div class="alert-icon">✅</div>
                <div class="alert-content">
                    <h3>Nachricht gesendet!</h3>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div class="alert-box alert-error">
                <div class="alert-icon">❌</div>
                <div class="alert-content">
                    <h3>Bitte überprüfe deine Eingaben:</h3>
                    <ul style="list-style: disc; margin-left: 1.5rem; margin-top: 0.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Contact Form -->
        <div class="card">
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
                                <div class="radio-label-content">
                                    <span class="radio-label-icon">💭</span>
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
                                <div class="radio-label-content">
                                    <span class="radio-label-icon">✨</span>
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
                                <div class="radio-label-content">
                                    <span class="radio-label-icon">🐛</span>
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
                                <div class="radio-label-content">
                                    <span class="radio-label-icon">📧</span>
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
                           class="form-input @error('email') border-red-500 @enderror" required>
                    <p class="help-text">Du erhältst eine Kopie deiner Anfrage an diese Adresse</p>
                </div>

                <!-- Hermine Contact -->
                <div class="checkbox-group">
                    <input type="checkbox" id="hermine_contact" name="hermine_contact" value="1"
                           onchange="toggleHermineFields()"
                           {{ old('hermine_contact') ? 'checked' : '' }} class="checkbox-input">
                    <label for="hermine_contact" class="checkbox-label">
                        <span class="checkbox-label-text">📱 Kontakt über Hermine</span>
                        <span class="checkbox-description">
                            Ich bin einverstanden, dass ich über die THW-Messenger-App Hermine kontaktiert werde
                        </span>
                    </label>
                </div>

                <!-- Hermine Fields (conditional) -->
                <div id="hermineFields" class="hidden conditional-field" style="display: none; background: linear-gradient(135deg, rgba(0, 51, 127, 0.05) 0%, rgba(0, 63, 153, 0.05) 100%); border: 1px solid #dbeafe; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h3 style="font-weight: 600; color: #00337F; margin: 0 0 1rem 0;">👤 Deine Hermine-Daten</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label for="vorname" class="form-label">Vorname <span class="required">*</span></label>
                            <input type="text" id="vorname" name="vorname" value="{{ old('vorname') }}"
                                   class="form-input" placeholder="Max">
                        </div>
                        <div>
                            <label for="nachname" class="form-label">Nachname <span class="required">*</span></label>
                            <input type="text" id="nachname" name="nachname" value="{{ old('nachname') }}"
                                   class="form-input" placeholder="Mustermann">
                        </div>
                    </div>
                    
                    <div>
                        <label for="ortsverband" class="form-label">Ortsverband <span class="required">*</span></label>
                        <input type="text" id="ortsverband" name="ortsverband" value="{{ old('ortsverband') }}"
                               class="form-input" placeholder="z.B. OV Musterstadt">
                    </div>
                </div>

                <!-- Bug Location (conditional) -->
                <div id="bugFields" class="hidden conditional-field" style="display: none; margin-bottom: 1.5rem;">
                    <label for="error_location" class="form-label">🐛 Wo ist der Fehler aufgetreten? <span class="required">*</span></label>
                    <select id="error_location" name="error_location" class="form-select">
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
                              class="form-textarea @error('message') border-red-500 @enderror"
                              placeholder="Schreib mir dein Anliegen...">{{ old('message') }}</textarea>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                        <p class="help-text">Mindestens 10 Zeichen</p>
                        <p class="char-count"><span id="charCount">0</span> / 5000</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="button button-primary">📤 Nachricht absenden</button>

                <p class="privacy-text">🔒 Deine Daten werden vertraulich behandelt und nicht an Dritte weitergegeben.</p>
            </form>
        </div>
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
            messageLabel.textContent = '🐛 Beschreibe den Fehler';
        } else if (type === 'feedback') {
            messageLabel.textContent = '💭 Dein Feedback';
        } else if (type === 'feature') {
            messageLabel.textContent = '✨ Beschreibe deinen Feature-Wunsch';
        } else {
            messageLabel.textContent = '📧 Deine Nachricht';
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
        submitBtn.textContent = '⏳ Wird gesendet...';
    });
</script>

@endsection
