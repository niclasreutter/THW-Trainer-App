
@extends('layouts.app')

@section('title', 'Login - THW Trainer')
@section('description', 'Melde dich bei THW-Trainer an und greife auf deinen persönlichen Lernfortschritt zu. Übe THW-Theoriefragen mit gespeichertem Fortschritt.')

@section('content')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }
    .login-card {
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
    .login-btn {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.3);
    }
    .login-btn:hover {
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
</style>

<div class="gradient-bg flex flex-col justify-center items-center py-12 px-4">
    <!-- THW Logo -->
    <div class="text-center mb-8">
        <div class="mb-4">
            <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" class="h-16 w-auto mx-auto">
        </div>
    </div>

    <!-- Login Card -->
    <div class="login-card w-full p-8" style="max-width: 600px; margin: 0 auto;">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-900 mb-2">🔐 Anmeldung</h2>
            <p class="text-gray-600">Gib deine Zugangsdaten ein</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />
        
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%); border: 2px solid rgba(239, 68, 68, 0.3);">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold text-red-800">Fehler bei der Anmeldung</span>
                </div>
                @foreach ($errors->all() as $error)
                    <p class="text-red-700 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
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
                       autofocus 
                       class="form-input w-full px-4 py-3 text-blue-900 placeholder-gray-400"
                       placeholder="deine@email.de">
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
                       placeholder="Dein Passwort">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 text-sm font-medium text-blue-900 cursor-pointer">
                        Angemeldet bleiben
                    </label>
                </div>
                <a href="{{ route('password.request') }}" 
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    Passwort vergessen?
                </a>
            </div>

            <!-- Login Button -->
            <button type="submit" 
                    class="login-btn w-full text-white font-bold py-3 px-6 text-lg">
                🚀 Anmelden
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 text-sm">
                Noch kein Account? 
                <a href="{{ route('register') }}" 
                   class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                    Jetzt registrieren →
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
</div>
@endsection
