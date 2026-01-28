@extends('layouts.auth')

@section('title', 'Passwort vergessen - THW Trainer')
@section('description', 'Passwort vergessen? Setze dein THW-Trainer Passwort zurück und erhalte einen sicheren Reset-Link per E-Mail.')

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
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('/images/bauhaus-pattern.svg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.05;
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
        text-align: center;
        color: #666;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .success-box {
        background: rgba(34, 197, 94, 0.1);
        border: 2px solid rgba(34, 197, 94, 0.3);
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 0 15px rgba(34, 197, 94, 0.1);
    }

    .success-box h3 {
        color: #166534;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .success-box p {
        color: #15803d;
        font-size: 0.9rem;
        margin: 0;
    }

    .error-box {
        background: rgba(239, 68, 68, 0.1);
        border: 2px solid rgba(239, 68, 68, 0.3);
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.1);
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
        font-size: 0.9rem;
        font-weight: 600;
        color: #00337F;
        margin-bottom: 0.6rem;
    }

    .form-input {
        width: 100%;
        padding: 0.9rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.8rem;
        font-size: 1rem;
        background: white;
        color: #333;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: #00337F;
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1), 0 0 15px rgba(0, 51, 127, 0.2);
        background: white;
    }

    .form-input::placeholder {
        color: #999;
    }

    .form-help {
        font-size: 0.85rem;
        color: #666;
        margin-top: 0.5rem;
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

    .auth-back-link {
        text-align: center;
        font-size: 0.95rem;
        color: #666;
    }

    .auth-back-link a {
        color: #00337F;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .auth-back-link a:hover {
        color: #002a66;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .auth-container {
            flex-direction: column;
        }

        .auth-left {
            padding: 2rem;
            min-height: auto;
            text-align: center;
        }

        .auth-left h1 {
            font-size: 2rem;
        }

        .auth-left p {
            font-size: 1rem;
        }

        .auth-right {
            padding: 2rem;
            min-height: auto;
        }

        .auth-form-container {
            max-width: 100%;
        }
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
            <a href="{{ route('landing.datenschutz') }}">Datenschutz</a>
            <span class="auth-footer-divider">•</span>
            <a href="{{ route('landing.impressum') }}">Impressum</a>
        </div>
    </div>

    <!-- Right Panel: Reset Form -->
    <div class="auth-right">
        <div class="auth-form-container">
            <h2>🔑 Passwort zurücksetzen</h2>
            <p>Gib deine E-Mail-Adresse ein</p>

            @if (session('status'))
                <div class="success-box">
                    <h3>✅ E-Mail gesendet</h3>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="error-box">
                    <h3>❌ Fehler beim Senden</h3>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">E-Mail-Adresse</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="form-input"
                           placeholder="deine@email.de">
                    <p class="form-help">Sollte ein Account mit dieser E-Mail existieren, senden wir dir einen Reset-Link.</p>
                </div>

                <!-- Reset Button -->
                <button type="submit" class="auth-btn">Reset-Link senden</button>
            </form>

            <!-- Back to Login -->
            <div class="auth-back-link">
                <a href="{{ route('login') }}">← Zurück zum Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
