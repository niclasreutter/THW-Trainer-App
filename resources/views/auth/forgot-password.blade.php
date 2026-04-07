@extends('layouts.auth')

@section('title', 'Passwort vergessen - THW Trainer')
@section('description', 'Passwort vergessen? Setze dein THW-Trainer Passwort zurück und erhalte einen sicheren Reset-Link per E-Mail.')

@section('content')
<div class="auth-split">
    <div class="auth-form-panel">
        <div class="auth-form-card">
            <a href="{{ url('/') }}" class="auth-form-brand">THW-Trainer</a>
            <h2 class="auth-form-title">Passwort zurücksetzen</h2>
            <p class="auth-form-subtitle">Gib deine E-Mail-Adresse ein und wir senden dir einen Reset-Link.</p>

            @if (session('status'))
                <div class="auth-success-box">
                    <strong>E-Mail gesendet</strong>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-error-box">
                    <strong>Fehler beim Senden</strong>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="auth-field">
                    <label for="email" class="auth-label">E-Mail-Adresse</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="auth-input" placeholder="max@beispiel.de">
                    <p class="auth-helper">Sollte ein Account mit dieser E-Mail existieren, senden wir dir einen Reset-Link.</p>
                </div>

                <button type="submit" class="auth-submit-btn">Reset-Link senden</button>
            </form>

            <div class="auth-divider"><span>oder</span></div>

            <div class="auth-switch-link">
                <a href="{{ route('login') }}">Zurück zum Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
