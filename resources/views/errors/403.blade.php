@extends('errors.layout')

@section('title', '403 - Zugriff verweigert')

@section('content')
<div class="glass-gold p-8 text-center">
    <!-- Error Code -->
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4" style="background: rgba(251, 191, 36, 0.15);">
            <i class="bi bi-shield-lock text-4xl" style="color: var(--gold);"></i>
        </div>
        <h1 class="text-7xl font-extrabold mb-2">
            <span class="text-gradient-gold">403</span>
        </h1>
        <h2 class="text-2xl font-bold mb-3" style="color: var(--thw-blue);">
            Zugriff verweigert
        </h2>
        <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto;">
            Du hast keine Berechtigung, auf diese Seite zuzugreifen.
            @guest
                Melde dich an, um fortzufahren.
            @else
                Kontaktiere einen Administrator, falls du Zugriff benötigst.
            @endguest
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col gap-3 mt-6">
        @guest
            <a href="{{ route('login') }}" class="btn-primary">
                Anmelden
            </a>
        @endguest

        <a href="{{ url('/') }}" class="btn-secondary">
            Zur Startseite
        </a>
    </div>
</div>
@endsection
