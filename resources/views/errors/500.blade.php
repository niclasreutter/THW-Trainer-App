@extends('errors.layout')

@section('title', '500 - Serverfehler')

@section('content')
<div class="glass-error p-8 text-center">
    <!-- Error Code -->
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4" style="background: rgba(239, 68, 68, 0.15);">
            <i class="bi bi-exclamation-triangle text-4xl" style="color: var(--error);"></i>
        </div>
        <h1 class="text-7xl font-extrabold mb-2">
            <span class="text-gradient-gold">500</span>
        </h1>
        <h2 class="text-2xl font-bold mb-3" style="color: var(--thw-blue);">
            Interner Serverfehler
        </h2>
        <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto;">
            Ups! Etwas ist auf unserer Seite schiefgelaufen.
            Wir wurden automatisch benachrichtigt und kümmern uns darum.
        </p>
    </div>

    <!-- Info Box -->
    <div class="alert-glass warning mb-6" style="text-align: left;">
        <i class="bi bi-lightbulb" style="color: var(--warning);"></i>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
            <strong>Tipp:</strong> Versuche die Seite neu zu laden.
            Sollte das Problem bestehen bleiben, kontaktiere uns bitte.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col gap-3">
        <button onclick="window.location.reload()" class="btn-primary">
            Seite neu laden
        </button>

        <a href="{{ route('landing.home') }}" class="btn-secondary">
            Zur Startseite
        </a>
    </div>
</div>
@endsection
