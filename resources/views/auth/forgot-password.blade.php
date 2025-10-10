@extends('layouts.app')

@section('title', 'Passwort vergessen - THW Trainer')
@section('description', 'Passwort vergessen? Setze dein THW-Trainer Passwort zurück und erhalte einen sicheren Reset-Link per E-Mail.')

@section('content')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }
    .reset-card {
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
    .reset-btn {
        background: linear-gradient(135deg, #00337F 0%, #002a66 100%);
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 51, 127, 0.3);
    }
    .reset-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 51, 127, 0.4);
    }
</style>

<div class="gradient-bg flex flex-col justify-center items-center py-12 px-4">
    <!-- THW Logo -->
    <div class="text-center mb-8">
        <div class="mb-4">
            <img src="{{ asset('logo-thwtrainer.png') }}" alt="THW-Trainer Logo" class="h-16 w-auto mx-auto">
        </div>
    </div>

    <!-- Reset Card -->
    <div class="reset-card w-full p-8" style="max-width: 600px; margin: 0 auto;">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-900 mb-2">🔑 Passwort zurücksetzen</h2>
            <p class="text-gray-600">Gib deine E-Mail-Adresse ein, um einen Reset-Link zu erhalten</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(22, 163, 74, 0.1) 100%); border: 2px solid rgba(34, 197, 94, 0.3);">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold text-green-800">E-Mail gesendet</span>
                </div>
                <p class="text-green-700 text-sm">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%); border: 2px solid rgba(239, 68, 68, 0.3); border-radius: 12px; box-shadow: 0 0 20px rgba(239, 68, 68, 0.2), 0 0 40px rgba(239, 68, 68, 0.1);">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold text-red-800">Fehler beim Senden</span>
                </div>
                @foreach ($errors->all() as $error)
                    <p class="text-red-700 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                <p class="text-xs text-gray-600 mt-2">
                    Sollte ein Account mit dieser E-Mail existieren, senden wir dir einen Reset-Link.
                </p>
            </div>

            <!-- Reset Button -->
            <button type="submit" 
                    class="reset-btn w-full text-white font-bold py-3 px-6 text-lg">
                🔗 Reset-Link senden
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" 
               class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                ← Zurück zum Login
            </a>
        </div>
    </div>
</div>
@endsection
