
@extends('layouts.app')

@section('title', 'Registrierung - THW Trainer')
@section('description', 'Erstelle deinen kostenlosen THW-Trainer Account und starte sofort mit dem Lernen. Verfolge deinen Fortschritt und bereite dich optimal auf deine THW-Prüfung vor.')

@section('content')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }
    .register-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 51, 127, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.05);
        border-radius: 20px;
    }
    .form-input {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(0, 51, 127, 0.1);
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .form-input:focus {
        border-color: #00337F;
        box-shadow: 0 0 0 3px rgba(0, 51, 127, 0.1), 0 0 20px rgba(0, 51, 127, 0.2);
        background: rgba(255, 255, 255, 1);
    }
    .register-btn {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.3);
    }
    .register-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.4);
    }
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .thw-logo {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .benefit-item {
        background: linear-gradient(135deg, rgba(0, 51, 127, 0.05) 0%, rgba(0, 42, 102, 0.05) 100%);
        border: 1px solid rgba(0, 51, 127, 0.1);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    .benefit-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.1);
    }
</style>

<div class="gradient-bg flex flex-col justify-center items-center py-12 px-4">
    <!-- THW Logo & Header -->
    <div class="text-center mb-8">
        <div class="floating-icon mb-4">
            <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" class="h-16 w-auto mx-auto">
        </div>
        <h1 class="thw-logo text-4xl font-bold mb-2">Starte dein THW-Training!</h1>
        <p class="text-gray-600 text-lg">Erstelle deinen kostenlosen Account und lerne effektiv</p>
    </div>

    <!-- Register Card -->
    <div class="register-card w-full p-8" style="max-width: 500px; margin: 0 auto;">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-900 mb-2">✨ Kostenlos registrieren</h2>
            <p class="text-gray-600">Erstelle deinen Account in wenigen Sekunden</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%); border: 2px solid rgba(239, 68, 68, 0.3);">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold text-red-800">Fehler bei der Registrierung</span>
                </div>
                @foreach ($errors->all() as $error)
                    <p class="text-red-700 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-semibold text-blue-900 mb-2">
                    👤 Vollständiger Name
                </label>
                <input id="name" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       class="form-input w-full px-4 py-3 text-blue-900 placeholder-gray-400"
                       placeholder="Max Mustermann">
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-semibold text-blue-900 mb-2">
                    📧 E-Mail-Adresse
                </label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       class="form-input w-full px-4 py-3 text-blue-900 placeholder-gray-400"
                       placeholder="max@beispiel.de">
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-semibold text-blue-900 mb-2">
                    🔒 Passwort
                </label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       class="form-input w-full px-4 py-3 text-blue-900 placeholder-gray-400"
                       placeholder="Mindestens 8 Zeichen">
            </div>

            <!-- Password Confirmation Field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-blue-900 mb-2">
                    🔒 Passwort bestätigen
                </label>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       class="form-input w-full px-4 py-3 text-blue-900 placeholder-gray-400"
                       placeholder="Passwort wiederholen">
            </div>
            
            <!-- E-Mail-Zustimmung -->
            <div class="p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(2, 132, 199, 0.1) 100%); border: 2px solid rgba(14, 165, 233, 0.3); box-shadow: 0 0 20px rgba(14, 165, 233, 0.2);">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 mt-1" style="color: #0284c7;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium" style="color: #0284c7; margin-bottom: 6px;">📧 E-Mail-Benachrichtigungen</h3>
                        <p class="text-xs" style="color: #0369a1; margin-bottom: 8px;">
                            Erhalte E-Mails zu deinem Lernfortschritt, neuen Features und wichtigen Systeminformationen.
                        </p>
                        <div class="flex items-center">
                            <input type="checkbox" name="email_consent" id="email_consent" value="1" 
                                   {{ old('email_consent') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="email_consent" class="ml-2 text-xs font-medium" style="color: #0369a1;">
                                Ich möchte E-Mail-Benachrichtigungen erhalten
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Register Button -->
            <button type="submit" 
                    class="register-btn w-full text-white font-bold py-3 px-6 text-lg">
                🚀 Account erstellen
            </button>
        </form>

        <!-- Login Link -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 text-sm">
                Bereits registriert? 
                <a href="{{ route('login') }}" 
                   class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                    Jetzt anmelden →
                </a>
            </p>
        </div>

        <!-- Guest Access -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-center text-gray-600 text-sm mb-3">
                Oder lerne direkt ohne Anmeldung:
            </p>
            <a href="{{ route('guest.practice.menu') }}" 
               class="block w-full text-center py-2 px-4 border-2 border-blue-600 text-blue-600 rounded-lg font-medium hover:bg-blue-600 hover:text-white transition-all duration-200">
                🎯 Als Gast üben
            </a>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="w-full max-w-4xl mt-12">
        <h3 class="text-center text-2xl font-bold text-blue-900 mb-8">Warum THW-Trainer?</h3>
        <div class="benefits-grid">
            <div class="benefit-item">
                <div class="text-3xl mb-2">📊</div>
                <h4 class="font-semibold text-blue-900 mb-1">Fortschritt verfolgen</h4>
                <p class="text-sm text-gray-600">Sieh deine Lernstatistiken und Erfolge</p>
            </div>
            <div class="benefit-item">
                <div class="text-3xl mb-2">🏆</div>
                <h4 class="font-semibold text-blue-900 mb-1">Achievements</h4>
                <p class="text-sm text-gray-600">Sammle Punkte und schalte Erfolge frei</p>
            </div>
            <div class="benefit-item">
                <div class="text-3xl mb-2">📚</div>
                <h4 class="font-semibold text-blue-900 mb-1">Prüfungssimulation</h4>
                <p class="text-sm text-gray-600">Bereite dich optimal auf die echte Prüfung vor</p>
            </div>
            <div class="benefit-item">
                <div class="text-3xl mb-2">📈</div>
                <h4 class="font-semibold text-blue-900 mb-1">Schwäche-Analyse</h4>
                <p class="text-sm text-gray-600">Erkenne deine Lernlücken und arbeite gezielt</p>
            </div>
        </div>
    </div>
</div>
@endsection
