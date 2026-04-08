@extends('layouts.auth')

@section('title', 'Neues Passwort - THW Trainer')
@section('description', 'Setze dein neues THW-Trainer Passwort fest und melde dich wieder an.')

@section('content')
<div class="auth-split">
    <div class="auth-form-panel">
        <div class="auth-form-card">
            <a href="{{ url('/') }}" class="auth-form-brand">THW-Trainer</a>
            <h2 class="auth-form-title">Neues Passwort setzen</h2>
            <p class="auth-form-subtitle">Definiere ein sicheres neues Passwort für deinen Account.</p>

            @if ($errors->any())
                <div class="auth-error-box">
                    <strong>Fehler beim Setzen des Passworts</strong>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="auth-field">
                    <label for="email" class="auth-label">E-Mail-Adresse</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="auth-input" readonly>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label for="password" class="auth-label">Neues Passwort</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" class="auth-input" placeholder="Mind. 8 Zeichen">
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation" class="auth-label">Bestätigen</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="auth-input" placeholder="Wiederholen">
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn">Passwort aktualisieren</button>
            </form>

            <div class="auth-divider"><span>oder</span></div>

            <div class="auth-switch-link">
                <a href="{{ route('login') }}">Zurück zum Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
