@extends('errors.layout')

@section('title', '404 - Seite nicht gefunden')

@section('content')
<div class="glass-blue p-8 text-center">
    <!-- Error Code -->
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4" style="background: rgba(59, 130, 246, 0.15);">
            <i class="bi bi-search text-4xl" style="color: var(--info);"></i>
        </div>
        <h1 class="text-7xl font-extrabold mb-2">
            <span class="text-gradient-gold">404</span>
        </h1>
        <h2 class="text-2xl font-bold mb-3" style="color: var(--thw-blue);">
            Seite nicht gefunden
        </h2>
        <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto;">
            Die von dir gesuchte Seite existiert leider nicht.
            Vielleicht wurde sie verschoben oder gelöscht.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col gap-3 mt-6">
        <a href="{{ url('/') }}" class="btn-primary">
            Zur Startseite
        </a>

        @auth
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                Zum Dashboard
            </a>
        @else
            <a href="{{ route('landing.guest.practice.menu') }}" class="btn-secondary">
                Anonym üben
            </a>
        @endauth
    </div>
</div>
@endsection
