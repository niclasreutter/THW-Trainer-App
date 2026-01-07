
@extends('layouts.auth')

@section('title', 'Registrierung - THW Trainer')
@section('description', 'Erstelle deinen kostenlosen THW-Trainer Account und starte sofort mit dem Lernen. Verfolge deinen Fortschritt und bereite dich optimal auf deine THW-Prüfung vor.')

@section('content')
<style>
    * {
        box-sizing: border-box;
    }

    .auth-container {
        display: flex;
        min-height: 100vh;
        background: white;
    }

    .auth-left {
        flex: 1.5;
        background: linear-gradient(160deg, #00337F 0%, #001d4a 100%);
        padding: 3rem 4rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .auth-left::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 80%;
        height: 150%;
        background: radial-gradient(circle, rgba(255,255,255,0.03) 0%, transparent 70%);
        pointer-events: none;
    }

    .auth-left::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -20%;
        width: 60%;
        height: 60%;
        background: radial-gradient(circle, rgba(255,255,255,0.02) 0%, transparent 60%);
        pointer-events: none;
    }

    .auth-left-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .auth-brand {
        margin-bottom: 3rem;
    }

    .auth-brand-text {
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        opacity: 0.9;
    }

    .auth-headline {
        margin-bottom: 2rem;
    }

    .auth-headline h1 {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 1.5rem;
    }

    .auth-headline h1 span {
        display: block;
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .auth-headline p {
        font-size: 1.15rem;
        opacity: 0.85;
        line-height: 1.7;
        max-width: 400px;
    }

    .auth-stats {
        display: flex;
        gap: 3rem;
        margin-top: 2rem;
    }

    .auth-stat {
        text-align: left;
    }

    .auth-stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.3rem;
        font-variant-numeric: tabular-nums;
    }

    .auth-stat-label {
        font-size: 0.85rem;
        opacity: 0.7;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .auth-footer {
        position: relative;
        z-index: 1;
        padding-top: 2rem;
        font-size: 0.85rem;
        opacity: 0.6;
    }

    .auth-footer a {
        color: white;
        text-decoration: none;
        transition: opacity 0.2s ease;
    }

    .auth-footer a:hover {
        opacity: 1;
    }

    .auth-footer-divider {
        display: inline-block;
        margin: 0 0.75rem;
    }

    .auth-right {
        flex: 1;
        background: #f3f4f6;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
    }

    .auth-form-container {
        width: 100%;
        max-width: 450px;
    }

    .auth-form-container h2 {
        font-size: 1.8rem;
        font-weight: 800;
        color: #00337F;
        margin-bottom: 0.5rem;
        text-align: left;
    }

    .auth-form-container > p {
        text-align: left;
        color: #666;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .error-box {
        background: rgba(239, 68, 68, 0.1);
        border: 2px solid rgba(239, 68, 68, 0.3);
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.2);
    }

    .error-box h3 {
        color: #991b1b;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .error-box ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .error-box li {
        color: #7f1d1d;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-row .form-group {
        margin-bottom: 0;
    }

    .form-input {
        width: 100%;
        padding: 1rem 1.2rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 1rem;
        background: white;
        color: #333;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: #00337F;
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1);
        background: white;
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    .consent-box {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem 1.2rem;
        margin-bottom: 1.5rem;
    }

    .consent-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .consent-checkbox input[type="checkbox"] {
        width: 1.2rem;
        height: 1.2rem;
        cursor: pointer;
        margin-top: 0.1rem;
        accent-color: #00337F;
        flex-shrink: 0;
    }

    .consent-checkbox-content {
        flex: 1;
    }

    .consent-checkbox label {
        cursor: pointer;
        margin: 0;
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 0.25rem;
    }

    .consent-description {
        font-size: 0.8rem;
        color: #6b7280;
        line-height: 1.4;
        margin: 0;
    }

    .auth-btn {
        width: 100%;
        padding: 0.9rem 1rem;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        border: none;
        border-radius: 0.8rem;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.3);
        margin-bottom: 1rem;
    }

    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 51, 127, 0.4);
    }

    .auth-btn:active {
        transform: translateY(0);
    }

    .auth-secondary-btn {
        display: block;
        width: 100%;
        padding: 0.9rem 1rem;
        background: white;
        color: #00337F;
        border: 2px solid #00337F;
        border-radius: 0.8rem;
        font-size: 1rem;
        font-weight: 700;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }

    .auth-secondary-btn:hover {
        background: #00337F;
        color: white;
    }

    .auth-divider {
        border-top: 2px solid #e5e7eb;
        margin: 1.5rem 0;
    }

    .auth-signup-link {
        text-align: center;
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 1rem;
    }

    .auth-signup-link a {
        color: #00337F;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    @media (max-width: 768px) {
        .auth-container {
            flex-direction: column;
        }

        .auth-left {
            display: none;
        }

        .auth-right {
            flex: 1;
            padding: 2rem;
            min-height: 100vh;
        }

        .auth-form-container {
            max-width: 100%;
        }
    }

    @media (max-width: 480px) {
        .auth-left {
            padding: 1.5rem;
        }

        .auth-headline h1 {
            font-size: 2rem;
        }

        .auth-headline p {
            font-size: 0.9rem;
        }

        .auth-stats {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .auth-stat-number {
            font-size: 1.8rem;
        }

        .auth-right {
            padding: 1.5rem;
        }

        .auth-form-container h2 {
            font-size: 1.5rem;
        }

        .form-input,
        .auth-btn,
        .auth-secondary-btn {
            font-size: 0.95rem;
            padding: 0.8rem 0.9rem;
        }
    }
</style>

<div class="auth-container">
    <!-- Left Panel: Brand & Info -->
    <div class="auth-left">
        <div class="auth-left-content">
            <div class="auth-brand">
                <div class="auth-brand-text">THW-Trainer</div>
            </div>

            <div class="auth-headline">
                <h1>Lerne smarter.<br><span>Werde besser.</span></h1>
                <p>Bereite dich optimal auf deine THW-Prüfung vor – mit intelligenten Lernmethoden und Fortschrittstracking.</p>
            </div>

            <div class="auth-stats">
                <div class="auth-stat">
                    <div class="auth-stat-number">200+</div>
                    <div class="auth-stat-label">User</div>
                </div>
                <div class="auth-stat">
                    <div class="auth-stat-number">1.000+</div>
                    <div class="auth-stat-label">Fragen</div>
                </div>
                <div class="auth-stat">
                    <div class="auth-stat-number">100%</div>
                    <div class="auth-stat-label">Kostenlos</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            © 2026 THW-Trainer
            <span class="auth-footer-divider">•</span>
            <a href="{{ route('datenschutz') }}">Datenschutz</a>
            <span class="auth-footer-divider">•</span>
            <a href="{{ route('impressum') }}">Impressum</a>
        </div>
    </div>

    <!-- Right Panel: Registration Form -->
    <div class="auth-right">
        <div class="auth-form-container">
            <h2>Konto erstellen 🚀</h2>
            <p>Starte jetzt mit dem THW-Trainer.</p>

@php
            $inviteCode = request('code') ?? request('invite');
            $inviteInfo = null;
            if ($inviteCode) {
                $inviteInfo = \App\Models\OrtsverbandInvitation::where('code', $inviteCode)->with('ortsverband', 'creator')->first();
            }
            @endphp

            @if($inviteInfo && $inviteInfo->isValid())
            <div style="background: linear-gradient(135deg, #00337F 0%, #0047b3 100%); color: white; padding: 0.75rem 1rem; border-radius: 0.75rem; margin-bottom: 1rem; font-size: 0.9rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.25rem;">🚨</span>
                    <div>
                        <strong>Einladung:</strong> {{ $inviteInfo->ortsverband->name }}
                        <span style="opacity: 0.8; font-size: 0.8rem;">• Du trittst automatisch bei</span>
                    </div>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="error-box">
                <h3>❌ {{ session('error') }}</h3>
            </div>
            @endif

            @if ($errors->any())
                <div class="error-box">
                    <h3>❌ Fehler bei der Registrierung</h3>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name Field -->
                <div class="form-group">
                    <label for="name">Vollständiger Name</label>
                    <input id="name"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           class="form-input"
                           placeholder="Max Mustermann">
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">E-Mail-Adresse</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           class="form-input"
                           placeholder="max@beispiel.de">
                </div>

                <!-- Password Fields - Side by Side -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Passwort</label>
                        <input id="password"
                               type="password"
                               name="password"
                               required
                               class="form-input"
                               placeholder="Mindestens 8 Zeichen">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Bestätigen</label>
                        <input id="password_confirmation"
                               type="password"
                               name="password_confirmation"
                               required
                               class="form-input"
                               placeholder="Passwort wiederholen">
                    </div>
                </div>

                <!-- OV-Code (Optional) - nur anzeigen wenn kein gültiger Code in URL -->
                @if(!($inviteInfo && $inviteInfo->isValid()))
                <div class="form-group">
                    <label for="invitation_code">OV-Code (optional)</label>
                    <input id="invitation_code"
                           type="text"
                           name="invitation_code"
                           value="{{ old('invitation_code', request('code') ?? request('invite') ?? '') }}"
                           class="form-input"
                           placeholder="z.B. THW-XXXXXXXX">
                    <p style="font-size: 0.8rem; color: #6b7280; margin-top: 0.5rem;">Hast du einen Einladungscode von deinem Ortsverband erhalten?</p>
                </div>
                @else
                <input type="hidden" name="invitation_code" value="{{ $inviteInfo->code }}">
                @endif

                <!-- Email Consent -->
                <div class="consent-box">
                    <div class="consent-checkbox">
                        <input type="checkbox" name="email_consent" id="email_consent" value="1"
                               {{ old('email_consent') ? 'checked' : '' }}>
                        <div class="consent-checkbox-content">
                            <label for="email_consent">E-Mail-Benachrichtigungen</label>
                            <p class="consent-description">Erhalte Mails zu deinem Lernfortschritt und neuen Features</p>
                        </div>
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" class="auth-btn">🚀 Account erstellen</button>
            </form>

            <!-- Guest Access Button -->
            <a href="{{ route('guest.practice.menu') }}" class="auth-secondary-btn">🎯 Als Gast üben</a>

            <!-- Divider -->
            <div class="auth-divider"></div>

            <!-- Login Link -->
            <div class="auth-signup-link">
                Bereits registriert? <a href="{{ route('login') }}">Jetzt anmelden →</a>
            </div>
        </div>
    </div>
</div>

<script>
    function animateCounter(element, target, duration = 2000) {
        const startTime = Date.now();
        const startValue = 0;
        
        const isPercentage = target === 100;
        const displayTarget = isPercentage ? 100 : target;
        
        function update() {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth animation
            const easeOutQuad = 1 - Math.pow(1 - progress, 2);
            const currentValue = Math.floor(easeOutQuad * displayTarget);
            
            if (isPercentage) {
                element.textContent = currentValue + '%';
            } else {
                element.textContent = currentValue.toLocaleString('de-DE') + '+';
            }
            
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        
        update();
    }
    
    // Start animation when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const stats = document.querySelectorAll('.auth-stat-number');
        const targets = [200, 1000, 100];
        
        stats.forEach((stat, index) => {
            animateCounter(stat, targets[index]);
        });
    });
</script>
@endsection
