@extends('layouts.auth')

@section('title', 'Anmelden - THW Trainer')
@section('description', 'Melde dich bei THW-Trainer an und greife auf deinen persönlichen Lernfortschritt zu.')

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
        background-color: #00337F;
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
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('/images/bauhaus-pattern.svg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.2;
        pointer-events: none;
        z-index: 0;
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
        z-index: 0;
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
    }

    .auth-form-container {
        width: 100%;
        max-width: 400px;
    }

    .auth-form-container h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #00337F;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .auth-form-container > p {
        text-align: center;
        color: #6b7280;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.8rem;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }

    .form-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .form-input-icon {
        position: absolute;
        left: 1.2rem;
        font-size: 1.2rem;
        color: #d1d5db;
        pointer-events: none;
        user-select: none;
    }

    .form-input {
        width: 100%;
        padding: 1rem 1.2rem 1rem 3.2rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.8rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
        color: #333;
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: #00337F;
        background: white;
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1), 0 0 15px rgba(0, 51, 127, 0.2);
    }

    .form-input:focus ~ .form-input-icon {
        color: #00337F;
    }

    .form-input::placeholder {
        color: #d1d5db;
        font-weight: 400;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
    }

    .form-checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .form-checkbox-label input {
        margin-right: 0.5rem;
        cursor: pointer;
    }

    .forgot-password {
        color: #00337F;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .forgot-password:hover {
        color: #002a66;
        text-decoration: underline;
    }

    .auth-btn {
        width: 100%;
        padding: 0.9rem;
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        color: white;
        font-weight: 700;
        border: none;
        border-radius: 0.75rem;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.2);
    }

    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.3);
    }

    .auth-btn:active {
        transform: translateY(0);
    }

    .auth-divider {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e5e7eb;
    }

    .auth-divider span {
        padding: 0 1rem;
        color: #9ca3af;
        font-size: 0.875rem;
    }

    .auth-secondary-btn {
        width: 100%;
        padding: 0.75rem;
        background: transparent;
        color: #00337F;
        border: 2px solid #00337F;
        font-weight: 600;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        display: inline-block;
    }

    .auth-secondary-btn:hover {
        background: #00337F;
        color: white;
    }

    .auth-signup-link {
        text-align: center;
        margin-top: 1.5rem;
        color: #6b7280;
        font-size: 0.95rem;
    }

    .auth-signup-link a {
        color: #00337F;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .auth-signup-link a:hover {
        color: #002a66;
        text-decoration: underline;
    }

    .error-box {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
        border: 2px solid rgba(239, 68, 68, 0.3);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .error-box strong {
        color: #dc2626;
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .error-box strong::before {
        content: '⚠️ ';
        margin-right: 0.5rem;
    }

    .error-box p {
        color: #b91c1c;
        margin: 0;
        font-size: 0.875rem;
    }

    /* Responsive */
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
        .auth-right {
            padding: 1.5rem;
        }

        .auth-form-container h2 {
            font-size: 1.5rem;
        }

        .form-input,
        .auth-btn {
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
                    <div class="auth-stat-number">0</div>
                    <div class="auth-stat-label">User</div>
                </div>
                <div class="auth-stat">
                    <div class="auth-stat-number">0</div>
                    <div class="auth-stat-label">Fragen</div>
                </div>
                <div class="auth-stat">
                    <div class="auth-stat-number">0</div>
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

    <!-- Right Section -->
    <div class="auth-right">
        <div class="auth-form-container">
            <h2>Willkommen zurück 👋</h2>
            <p>Melde dich an, um auf deinen Lernfortschritt zuzugreifen.</p>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="error-box">
                    <strong>Anmeldung fehlgeschlagen</strong>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">E-Mail-Adresse</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="form-input"
                           placeholder="max@beispiel.de">
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Passwort</label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           class="form-input"
                           placeholder="••••••••">
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-actions">
                    <label class="form-checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Angemeldet bleiben</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-password">Passwort vergessen?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-btn">🚀 Anmelden</button>
            </form>

            <!-- Divider -->
            <div class="auth-divider"></div>

            <!-- Register Link -->
            <div class="auth-signup-link">
                Noch kein Konto? <a href="{{ route('register') }}">Jetzt registrieren →</a>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        'use strict';

        function animateCounter(element, target, duration) {
            if (!element) return;

            const isPercentage = target === 100;
            const startTime = performance.now();

            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Easing function
                const eased = 1 - Math.pow(1 - progress, 2);
                const currentValue = Math.round(eased * target);

                // Update display
                try {
                    if (isPercentage) {
                        element.textContent = currentValue + '%';
                    } else if (currentValue >= 1000) {
                        // Format für >= 1000: "1.000+"
                        element.textContent = currentValue.toLocaleString('de-DE') + '+';
                    } else {
                        // Format für < 1000: "200+"
                        element.textContent = currentValue + '+';
                    }
                } catch (e) {
                    console.error('Counter update error:', e);
                    element.textContent = target + (isPercentage ? '%' : '+');
                }

                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            }

            requestAnimationFrame(update);
        }

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }

        function init() {
            const stats = document.querySelectorAll('.auth-stat-number');
            const targets = [200, 1000, 100];

            if (stats.length !== 3) {
                console.warn('Expected 3 stat elements, found:', stats.length);
            }

            stats.forEach(function(stat, index) {
                if (targets[index] !== undefined) {
                    animateCounter(stat, targets[index], 2000);
                }
            });
        }
    })();
</script>
@endsection
