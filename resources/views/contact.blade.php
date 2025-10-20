@extends('layouts.app')

@section('title', 'Kontakt & Feedback - THW Trainer')
@section('description', 'Kontaktiere mich bei Fragen, Feedback oder Problemen. Ich helfe dir gerne weiter!')

@section('content')
<style>
    /* CACHE BUST v1.0 - CONTACT FORM - 2025-10-20-21:30 */
    
    /* Mobile Optimierung */
    @media (max-width: 640px) {
        #contactContainer {
            padding: 1rem !important;
        }
        
        .radio-option {
            min-height: 60px !important;
            padding: 16px !important;
        }
        
        input[type="text"],
        input[type="email"],
        select,
        textarea {
            font-size: 16px !important; /* Verhindert Auto-Zoom auf iOS */
        }
    }
    
    /* Desktop Kompakt */
    @media (min-width: 641px) {
        #contactContainer {
            margin-top: 1.5rem !important;
        }
    }
    
    /* Radio Button Styling */
    .radio-option {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .radio-option:hover {
        background-color: #f0f9ff !important;
        border-color: #3b82f6 !important;
    }
    
    .radio-option input[type="radio"]:checked + label {
        font-weight: 700;
    }
    
    .radio-option:has(input[type="radio"]:checked) {
        background-color: #eff6ff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
    
    /* Konditionale Felder Animation */
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
</style>

<div class="max-w-2xl mx-auto mt-4 sm:mt-8 p-4 sm:p-6 bg-white rounded-lg shadow-lg" id="contactContainer">
    <h2 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4 text-blue-900">📬 Kontakt & Feedback</h2>
    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
        Ich freue mich über dein Feedback, deine Ideen oder deine Fehlermeldungen! 
        Füll einfach das Formular aus und ich melde mich so schnell wie möglich bei dir.
    </p>

    @if(session('success'))
        <div class="mb-4 sm:mb-6 p-4 bg-green-50 border-2 border-green-300 rounded-lg text-green-800 font-bold animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 sm:mb-6 p-4 bg-red-50 border-2 border-red-300 rounded-lg">
            <p class="font-bold text-red-800 mb-2">❌ Bitte überprüfe deine Eingaben:</p>
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contact.submit') }}" id="contactForm">
        @csrf
        
        <!-- Honeypot (unsichtbar für echte Nutzer) -->
        <input type="text" name="website" style="display:none !important;" tabindex="-1" autocomplete="off">
        
        <!-- Kategorie-Auswahl -->
        <div class="mb-4 sm:mb-6">
            <label class="block text-sm sm:text-base font-bold mb-3 text-blue-900">
                Was möchtest du mir mitteilen? <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="radio-option border-2 rounded-lg p-3 sm:p-4 bg-white">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="type" value="feedback" 
                               class="mr-3 w-5 h-5" 
                               onchange="updateFormType(this.value)"
                               {{ old('type') == 'feedback' ? 'checked' : '' }} required>
                        <div>
                            <div class="font-bold text-sm sm:text-base">💭 Feedback</div>
                            <div class="text-xs sm:text-sm text-gray-600">Lob, Kritik oder Verbesserungsvorschläge</div>
                        </div>
                    </label>
                </div>
                
                <div class="radio-option border-2 rounded-lg p-3 sm:p-4 bg-white">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="type" value="feature" 
                               class="mr-3 w-5 h-5" 
                               onchange="updateFormType(this.value)"
                               {{ old('type') == 'feature' ? 'checked' : '' }} required>
                        <div>
                            <div class="font-bold text-sm sm:text-base">✨ Feature-Wunsch</div>
                            <div class="text-xs sm:text-sm text-gray-600">Neue Funktionen vorschlagen</div>
                        </div>
                    </label>
                </div>
                
                <div class="radio-option border-2 rounded-lg p-3 sm:p-4 bg-white">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="type" value="bug" 
                               class="mr-3 w-5 h-5" 
                               onchange="updateFormType(this.value)"
                               {{ old('type') == 'bug' ? 'checked' : '' }} required>
                        <div>
                            <div class="font-bold text-sm sm:text-base">🐛 Fehler melden</div>
                            <div class="text-xs sm:text-sm text-gray-600">Etwas funktioniert nicht?</div>
                        </div>
                    </label>
                </div>
                
                <div class="radio-option border-2 rounded-lg p-3 sm:p-4 bg-white">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="type" value="other" 
                               class="mr-3 w-5 h-5" 
                               onchange="updateFormType(this.value)"
                               {{ old('type') == 'other' ? 'checked' : '' }} required>
                        <div>
                            <div class="font-bold text-sm sm:text-base">📧 Sonstiges</div>
                            <div class="text-xs sm:text-sm text-gray-600">Allgemeine Anfrage</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- E-Mail -->
        <div class="mb-4 sm:mb-5">
            <label for="email" class="block text-sm sm:text-base font-bold mb-2 text-blue-900">
                Deine E-Mail-Adresse <span class="text-red-500">*</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', auth()->user()->email ?? '') }}"
                   required
                   class="w-full p-3 border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                   placeholder="deine@email.de">
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Du erhältst eine Kopie deiner Anfrage an diese Adresse
            </p>
        </div>

        <!-- Hermine Kontakt -->
        <div class="mb-4 sm:mb-5 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
            <label class="flex items-start cursor-pointer">
                <input type="checkbox" 
                       id="hermine_contact" 
                       name="hermine_contact" 
                       value="1"
                       onchange="toggleHermineFields()"
                       {{ old('hermine_contact') ? 'checked' : '' }}
                       class="mt-1 mr-3 w-5 h-5">
                <div>
                    <div class="font-bold text-sm sm:text-base text-blue-900">📱 Kontakt über Hermine</div>
                    <div class="text-xs sm:text-sm text-gray-700">
                        Ich bin einverstanden, dass ich über die THW-Messenger-App Hermine kontaktiert werde
                    </div>
                </div>
            </label>
        </div>

        <!-- Hermine Felder (konditional) -->
        <div id="hermineFields" class="hidden conditional-field mb-4 sm:mb-5 p-4 bg-blue-50 border-2 border-blue-300 rounded-lg">
            <h3 class="font-bold text-sm sm:text-base mb-3 text-blue-900">👤 Deine Hermine-Daten</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="vorname" class="block text-sm font-bold mb-2">Vorname <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="vorname" 
                           name="vorname" 
                           value="{{ old('vorname') }}"
                           class="w-full p-2 sm:p-3 border-2 rounded-lg focus:border-blue-500"
                           placeholder="Max">
                </div>
                <div>
                    <label for="nachname" class="block text-sm font-bold mb-2">Nachname <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="nachname" 
                           name="nachname" 
                           value="{{ old('nachname') }}"
                           class="w-full p-2 sm:p-3 border-2 rounded-lg focus:border-blue-500"
                           placeholder="Mustermann">
                </div>
            </div>
            
            <div>
                <label for="ortsverband" class="block text-sm font-bold mb-2">Ortsverband <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="ortsverband" 
                       name="ortsverband" 
                       value="{{ old('ortsverband') }}"
                       class="w-full p-2 sm:p-3 border-2 rounded-lg focus:border-blue-500"
                       placeholder="z.B. OV Musterstadt">
            </div>
        </div>

        <!-- Bug-Location (konditional) -->
        <div id="bugFields" class="hidden conditional-field mb-4 sm:mb-5">
            <label for="error_location" class="block text-sm sm:text-base font-bold mb-2 text-red-800">
                🐛 Wo ist der Fehler aufgetreten? <span class="text-red-500">*</span>
            </label>
            <select id="error_location" 
                    name="error_location"
                    class="w-full p-3 border-2 border-red-300 rounded-lg focus:border-red-500 bg-red-50">
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

        <!-- Nachricht -->
        <div class="mb-4 sm:mb-6">
            <label for="message" class="block text-sm sm:text-base font-bold mb-2 text-blue-900">
                <span id="messageLabel">Deine Nachricht</span> <span class="text-red-500">*</span>
            </label>
            <textarea id="message" 
                      name="message" 
                      rows="8" 
                      required
                      minlength="10"
                      maxlength="5000"
                      class="w-full p-3 border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-y"
                      placeholder="Schreib mir dein Anliegen...">{{ old('message') }}</textarea>
            <p class="text-xs sm:text-sm text-gray-500 mt-1" id="messageHint">
                Mindestens 10 Zeichen • <span id="charCount">0</span> / 5000
            </p>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                id="submitBtn"
                class="w-full text-center font-bold text-base sm:text-lg py-3 sm:py-4 px-6 rounded-lg transition-all duration-300"
                style="background-color: #1e3a8a; color: #fbbf24; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);"
                onmouseover="this.style.backgroundColor='#fbbf24'; this.style.color='#1e3a8a'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 6px 16px rgba(251, 191, 36, 0.4)';"
                onmouseout="this.style.backgroundColor='#1e3a8a'; this.style.color='#fbbf24'; this.style.transform='scale(1)'; this.style.boxShadow='0 4px 12px rgba(30, 58, 138, 0.3)';">
            📤 Nachricht absenden
        </button>

        <p class="text-xs text-center text-gray-500 mt-4">
            🔒 Deine Daten werden vertraulich behandelt und nicht an Dritte weitergegeben.
        </p>
    </form>
</div>

<script>
    // Form Type Update
    function updateFormType(type) {
        const bugFields = document.getElementById('bugFields');
        const messageLabel = document.getElementById('messageLabel');
        const messageHint = document.getElementById('messageHint');
        const errorLocation = document.getElementById('error_location');
        
        // Reset Bug Fields
        bugFields.classList.add('hidden');
        errorLocation.removeAttribute('required');
        
        if (type === 'bug') {
            bugFields.classList.remove('hidden');
            errorLocation.setAttribute('required', 'required');
            messageLabel.textContent = '🐛 Beschreibe den Fehler';
            messageHint.innerHTML = 'Was ist passiert? Was hast du gemacht? Was sollte eigentlich passieren? • <span id="charCount">' + document.getElementById('message').value.length + '</span> / 5000';
        } else if (type === 'feedback') {
            messageLabel.textContent = '💭 Dein Feedback';
            messageHint.innerHTML = 'Was gefällt dir? Was könnte besser sein? • <span id="charCount">' + document.getElementById('message').value.length + '</span> / 5000';
        } else if (type === 'feature') {
            messageLabel.textContent = '✨ Beschreibe deinen Feature-Wunsch';
            messageHint.innerHTML = 'Welche neue Funktion wünschst du dir? Wie sollte sie funktionieren? • <span id="charCount">' + document.getElementById('message').value.length + '</span> / 5000';
        } else {
            messageLabel.textContent = '📧 Deine Nachricht';
            messageHint.innerHTML = 'Mindestens 10 Zeichen • <span id="charCount">' + document.getElementById('message').value.length + '</span> / 5000';
        }
    }

    // Toggle Hermine Fields
    function toggleHermineFields() {
        const checkbox = document.getElementById('hermine_contact');
        const fields = document.getElementById('hermineFields');
        const vorname = document.getElementById('vorname');
        const nachname = document.getElementById('nachname');
        const ortsverband = document.getElementById('ortsverband');
        
        if (checkbox.checked) {
            fields.classList.remove('hidden');
            vorname.setAttribute('required', 'required');
            nachname.setAttribute('required', 'required');
            ortsverband.setAttribute('required', 'required');
        } else {
            fields.classList.add('hidden');
            vorname.removeAttribute('required');
            nachname.removeAttribute('required');
            ortsverband.removeAttribute('required');
        }
    }

    // Character Counter
    const messageTextarea = document.getElementById('message');
    messageTextarea.addEventListener('input', function() {
        const charCount = document.getElementById('charCount');
        if (charCount) {
            charCount.textContent = this.value.length;
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Restore selected type
        const selectedType = document.querySelector('input[name="type"]:checked');
        if (selectedType) {
            updateFormType(selectedType.value);
        }
        
        // Restore hermine fields
        const hermineCheckbox = document.getElementById('hermine_contact');
        if (hermineCheckbox && hermineCheckbox.checked) {
            toggleHermineFields();
        }
        
        // Initial char count
        const charCount = document.getElementById('charCount');
        if (charCount) {
            charCount.textContent = messageTextarea.value.length;
        }
    });

    // Form Submit Animation
    document.getElementById('contactForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '⏳ Wird gesendet...';
        btn.style.opacity = '0.7';
    });
</script>
@endsection
