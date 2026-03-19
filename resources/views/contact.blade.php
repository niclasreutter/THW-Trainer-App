@extends('layouts.app')

@section('title', 'Kontakt & Feedback - THW Trainer')
@section('description', 'Kontaktiere mich bei Fragen, Feedback oder Problemen. Ich helfe dir gerne weiter!')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ─── Category Selection ──────────────────── */
    .ct-categories {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    @media (max-width: 400px) {
        .ct-categories { grid-template-columns: 1fr; }
    }

    .ct-category {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 0.625rem;
        padding: 0.75rem 0.875rem;
        cursor: pointer;
        transition: all 200ms ease;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .ct-category:hover {
        border-color: rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.05);
    }

    .ct-category:has(input:checked) {
        background: rgba(0, 51, 127, 0.12);
        border-color: rgba(91, 154, 255, 0.35);
    }

    html.light-mode .ct-category {
        background: #ffffff;
        border-color: rgba(0, 51, 127, 0.12);
    }

    html.light-mode .ct-category:hover {
        background: rgba(0, 51, 127, 0.03);
        border-color: rgba(0, 51, 127, 0.25);
    }

    html.light-mode .ct-category:has(input:checked) {
        background: rgba(0, 51, 127, 0.08);
        border-color: rgba(0, 51, 127, 0.35);
    }

    .ct-category input { display: none; }

    .ct-cat-icon {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.06);
        color: var(--text-muted);
        transition: all 200ms ease;
    }

    html.light-mode .ct-cat-icon {
        background: rgba(0, 51, 127, 0.06);
    }

    .ct-category:has(input:checked) .ct-cat-icon {
        background: linear-gradient(135deg, #0055cc, #5b9aff);
        color: #fff;
    }

    .ct-cat-title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .ct-cat-desc {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── Form Elements ───────────────────────── */
    .ct-form-section {
        margin-bottom: 1.25rem;
    }

    .ct-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.375rem;
    }

    .ct-label .required {
        color: var(--error);
        margin-left: 0.125rem;
    }

    .ct-help {
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
    }

    .ct-char-row {
        display: flex;
        justify-content: space-between;
        margin-top: 0.35rem;
    }

    .ct-char-count {
        font-size: 0.6875rem;
        color: var(--text-muted);
        font-family: 'IBM Plex Mono', monospace;
    }

    /* ─── Hermine Checkbox ────────────────────── */
    .ct-hermine-toggle {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        background: rgba(0, 51, 127, 0.08);
        border: 1px solid rgba(0, 51, 127, 0.18);
        border-radius: 0.625rem;
        margin-bottom: 1.25rem;
        cursor: pointer;
    }

    html.light-mode .ct-hermine-toggle {
        background: rgba(0, 51, 127, 0.04);
        border-color: rgba(0, 51, 127, 0.15);
    }

    .ct-hermine-toggle input[type="checkbox"] {
        width: 1.125rem;
        height: 1.125rem;
        margin-top: 0.1rem;
        accent-color: #0055cc;
        cursor: pointer;
        flex-shrink: 0;
    }

    .ct-hermine-title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .ct-hermine-desc {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 0.15rem;
    }

    /* ─── Hermine Fields ──────────────────────── */
    .ct-hermine-fields {
        background: rgba(0, 51, 127, 0.05);
        border: 1px solid rgba(0, 51, 127, 0.12);
        border-radius: 0.625rem;
        padding: 1rem;
        margin-bottom: 1.25rem;
        animation: ct-slide-in 0.25s ease-out;
    }

    html.light-mode .ct-hermine-fields {
        background: rgba(0, 51, 127, 0.03);
        border-color: rgba(0, 51, 127, 0.1);
    }

    @keyframes ct-slide-in {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .ct-hermine-fields__title {
        font-size: 0.75rem;
        font-weight: 700;
        color: #5b9aff;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-family: 'IBM Plex Mono', monospace;
    }

    html.light-mode .ct-hermine-fields__title { color: var(--thw-blue); }

    .ct-hermine-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    @media (max-width: 400px) {
        .ct-hermine-grid { grid-template-columns: 1fr; }
    }

    /* ─── Bug Location ────────────────────────── */
    .ct-bug-section {
        animation: ct-slide-in 0.25s ease-out;
    }

    /* ─── Privacy ─────────────────────────────── */
    .ct-privacy {
        text-align: center;
        font-size: 0.6875rem;
        color: var(--text-muted);
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    /* ─── Stagger Animation ───────────────────── */
    @keyframes dash-rise {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dash-container > .space-y-4 > * {
        animation: dash-rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .dash-container > .space-y-4 > *:nth-child(1) { animation-delay: 0.03s; }
    .dash-container > .space-y-4 > *:nth-child(2) { animation-delay: 0.07s; }
    .dash-container > .space-y-4 > *:nth-child(3) { animation-delay: 0.11s; }
    .dash-container > .space-y-4 > *:nth-child(4) { animation-delay: 0.15s; }
    .dash-container > .space-y-4 > *:nth-child(5) { animation-delay: 0.19s; }

    @media (prefers-reduced-motion: reduce) {
        .dash-container > .space-y-4 > * { animation: none; }
    }
</style>
@endpush

@section('content')
<div class="dash-container">

    {{-- ── Header (wie Practice-Menu) ── --}}
    <div class="mb-6">
        <p style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);font-weight:600;margin-bottom:0.25rem;">Support</p>
        <h1 style="font-size:1.5rem;font-weight:800;line-height:1.2;font-family:'Barlow Condensed',sans-serif;background:linear-gradient(135deg,#5b9aff,#0055cc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Kontakt & Feedback</h1>
        <p class="text-sm" style="color: var(--text-muted);">Dein Feedback ist mir wichtig! Schreib mir bei Fragen, Ideen oder Problemen.</p>
    </div>

    {{-- ── Info Pills (wie gami-pills) ── --}}
    <div class="flex gap-3 mb-6" style="flex-wrap: wrap;">
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue">24-48h</div>
            <div class="gami-pill__label">Antwortzeit</div>
        </div>
        <div class="gami-pill">
            <div class="gami-pill__value gami-pill__value--blue" style="font-size: 0.875rem;">kontakt@thw-trainer.de</div>
            <div class="gami-pill__label">E-Mail</div>
        </div>
    </div>

    <div class="space-y-4">

        {{-- ── Success Message ── --}}
        @if(session('success'))
            <div class="alert-compact glass-success">
                <i class="bi bi-check-circle-fill alert-compact-icon text-success"></i>
                <div class="alert-compact-content">
                    <div class="alert-compact-title">Nachricht gesendet!</div>
                    <div class="alert-compact-desc">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        {{-- ── Error Messages ── --}}
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

        {{-- ── Form Card ── --}}
        <div class="glass-tl" style="padding: 1.25rem;">
            <form method="POST" action="{{ route('contact.submit') }}" id="contactForm">
                @csrf

                <!-- Honeypot -->
                <input type="text" name="website" style="display:none !important;" tabindex="-1" autocomplete="off">

                <!-- Category Selection -->
                <div class="ct-form-section">
                    <label class="ct-label">Was möchtest du mir mitteilen? <span class="required">*</span></label>
                    <div class="ct-categories">
                        <label class="ct-category">
                            <input type="radio" name="type" value="feedback"
                                   onchange="updateFormType(this.value)"
                                   {{ old('type') == 'feedback' ? 'checked' : '' }} required>
                            <div class="ct-cat-icon"><i class="bi bi-chat-dots"></i></div>
                            <div>
                                <div class="ct-cat-title">Feedback</div>
                                <div class="ct-cat-desc">Lob, Kritik, Vorschläge</div>
                            </div>
                        </label>
                        <label class="ct-category">
                            <input type="radio" name="type" value="feature"
                                   onchange="updateFormType(this.value)"
                                   {{ old('type') == 'feature' ? 'checked' : '' }} required>
                            <div class="ct-cat-icon"><i class="bi bi-lightbulb"></i></div>
                            <div>
                                <div class="ct-cat-title">Feature-Wunsch</div>
                                <div class="ct-cat-desc">Neue Funktionen</div>
                            </div>
                        </label>
                        <label class="ct-category">
                            <input type="radio" name="type" value="bug"
                                   onchange="updateFormType(this.value)"
                                   {{ old('type') == 'bug' ? 'checked' : '' }} required>
                            <div class="ct-cat-icon"><i class="bi bi-bug"></i></div>
                            <div>
                                <div class="ct-cat-title">Fehler melden</div>
                                <div class="ct-cat-desc">Etwas kaputt?</div>
                            </div>
                        </label>
                        <label class="ct-category">
                            <input type="radio" name="type" value="other"
                                   onchange="updateFormType(this.value)"
                                   {{ old('type') == 'other' ? 'checked' : '' }} required>
                            <div class="ct-cat-icon"><i class="bi bi-envelope"></i></div>
                            <div>
                                <div class="ct-cat-title">Sonstiges</div>
                                <div class="ct-cat-desc">Allgemeine Anfrage</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Email -->
                <div class="ct-form-section">
                    <label for="email" class="ct-label">Deine E-Mail-Adresse <span class="required">*</span></label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', auth()->user()->email ?? '') }}"
                           class="input-glass @error('email') border-red-500 @enderror"
                           placeholder="deine@email.de"
                           required>
                    <p class="ct-help">Du erhältst eine Kopie deiner Anfrage an diese Adresse</p>
                </div>

                <!-- Hermine Contact -->
                <label class="ct-hermine-toggle">
                    <input type="checkbox" id="hermine_contact" name="hermine_contact" value="1"
                           onchange="toggleHermineFields()"
                           {{ old('hermine_contact') ? 'checked' : '' }}>
                    <div>
                        <div class="ct-hermine-title">Kontakt über Hermine</div>
                        <div class="ct-hermine-desc">Ich bin einverstanden, über die THW-Messenger-App kontaktiert zu werden</div>
                    </div>
                </label>

                <!-- Hermine Fields (conditional) -->
                <div id="hermineFields" class="ct-hermine-fields" style="display: none;">
                    <div class="ct-hermine-fields__title">Deine Hermine-Daten</div>
                    <div class="ct-hermine-grid" style="margin-bottom: 0.75rem;">
                        <div>
                            <label for="vorname" class="ct-label">Vorname <span class="required">*</span></label>
                            <input type="text" id="vorname" name="vorname" value="{{ old('vorname') }}"
                                   class="input-glass" placeholder="Max">
                        </div>
                        <div>
                            <label for="nachname" class="ct-label">Nachname <span class="required">*</span></label>
                            <input type="text" id="nachname" name="nachname" value="{{ old('nachname') }}"
                                   class="input-glass" placeholder="Mustermann">
                        </div>
                    </div>
                    <div>
                        <label for="ortsverband" class="ct-label">Ortsverband <span class="required">*</span></label>
                        <input type="text" id="ortsverband" name="ortsverband" value="{{ old('ortsverband') }}"
                               class="input-glass" placeholder="z.B. OV Musterstadt">
                    </div>
                </div>

                <!-- Bug Location (conditional) -->
                <div id="bugFields" class="ct-form-section ct-bug-section" style="display: none;">
                    <label for="error_location" class="ct-label">Wo ist der Fehler aufgetreten? <span class="required">*</span></label>
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
                <div class="ct-form-section">
                    <label for="message" class="ct-label">
                        <span id="messageLabel">Deine Nachricht</span> <span class="required">*</span>
                    </label>
                    <textarea id="message" name="message" required minlength="10" maxlength="5000"
                              class="textarea-glass @error('message') border-red-500 @enderror"
                              placeholder="Schreib mir dein Anliegen..."
                              style="min-height: 140px;">{{ old('message') }}</textarea>
                    <div class="ct-char-row">
                        <span class="ct-help">Mindestens 10 Zeichen</span>
                        <span class="ct-char-count"><span id="charCount">0</span> / 5000</span>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" id="submitBtn" class="btn-primary" style="width: 100%;">
                    Nachricht absenden
                </button>

                <p class="ct-privacy">
                    <i class="bi bi-lock"></i>
                    Deine Daten werden vertraulich behandelt und nicht an Dritte weitergegeben.
                </p>
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
        submitBtn.innerHTML = 'Wird gesendet...';
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const message = document.getElementById('message');
        if (message.value) {
            document.getElementById('charCount').textContent = message.value.length;
        }

        if (document.getElementById('hermine_contact').checked) {
            toggleHermineFields();
        }

        const selectedType = document.querySelector('input[name="type"]:checked');
        if (selectedType) {
            updateFormType(selectedType.value);
        }
    });
</script>

@endsection
