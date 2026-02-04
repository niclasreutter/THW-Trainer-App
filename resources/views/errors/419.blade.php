@extends('errors.layout')

@section('title', '419 - Sitzung abgelaufen')

@section('content')
<div class="glass-warning p-8 text-center">
    <!-- Error Code -->
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4" style="background: rgba(245, 158, 11, 0.15);">
            <i class="bi bi-clock-history text-4xl" style="color: var(--warning);"></i>
        </div>
        <h1 class="text-7xl font-extrabold mb-2">
            <span class="text-gradient-gold">419</span>
        </h1>
        <h2 class="text-2xl font-bold mb-3" style="color: var(--thw-blue);">
            Sitzung abgelaufen
        </h2>
        <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto;">
            Deine Sitzung ist abgelaufen. Das kann passieren, wenn du die Seite
            längere Zeit geöffnet hattest. Bitte lade die Seite neu.
        </p>
    </div>

    <!-- Info Box -->
    <div class="alert-glass warning mb-6" style="text-align: left;">
        <i class="bi bi-lightbulb" style="color: var(--warning);"></i>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
            <strong>Tipp:</strong> Überprüfe, ob Cookies im Browser erlaubt sind,
            und versuche es erneut. Deine Daten wurden nicht verloren.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col gap-3">
        <a href="{{ route('login') }}" class="btn-primary">
            Zum Anmelden
        </a>

        <a href="{{ route('register') }}" class="btn-secondary">
            Zur Registrierung
        </a>

        <a href="{{ route('landing.home') }}" class="btn-ghost">
            Zur Startseite
        </a>
    </div>
</div>
@endsection
