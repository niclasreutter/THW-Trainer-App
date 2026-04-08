@extends('layouts.auth')

@section('title', 'Passwort bestätigen - THW Trainer')

@section('content')
<div class="auth-split">
    <div class="auth-form-panel">
        <div class="auth-form-card">
            <a href="{{ url('/') }}" class="auth-form-brand">THW-Trainer</a>
            <h2 class="auth-form-title">Passwort bestätigen</h2>
            <p class="auth-form-subtitle">Gib dein Passwort ein, um fortzufahren.</p>

            @if ($errors->any())
                <div class="auth-error-box">
                    <strong>Fehler</strong>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="auth-field">
                    <label for="password" class="auth-label">Passwort</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="auth-input" placeholder="Dein Passwort">
                </div>

                <button type="submit" class="auth-submit-btn">Bestätigen</button>
            </form>
        </div>
    </div>
</div>
@endsection
