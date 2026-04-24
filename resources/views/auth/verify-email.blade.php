@extends('layouts.auth')

@section('title', 'E-Mail Bestätigung - THW Trainer')

@section('content')
<div class="auth-split">
    <div class="auth-form-panel">
        <div class="auth-form-card">
            <a href="{{ url('/') }}" class="auth-form-brand">THW-Trainer</a>
            <h2 class="auth-form-title">E-Mail Bestätigung</h2>
            @if(isset($pendingEmail))
                <p class="auth-form-subtitle">Wir haben einen 6-stelligen Code an <strong>{{ $pendingEmail }}</strong> gesendet. Gib den Code ein, um deine neue E-Mail-Adresse zu bestätigen.</p>
            @else
                <p class="auth-form-subtitle">Wir haben einen 6-stelligen Code an <strong>{{ auth()->user()->email }}</strong> gesendet. Gib den Code hier ein.</p>
            @endif

            @if (session('status') == 'verification-code-sent')
                <div class="auth-success-box">
                    <p>Ein neuer Code wurde an deine E-Mail-Adresse gesendet.</p>
                </div>
            @endif

            @if ($errors->has('code'))
                <div class="auth-error-box">
                    @foreach ($errors->get('code') as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('verification.verify') }}">
                @csrf
                <div class="auth-field">
                    <label for="code" class="auth-label">Bestätigungscode</label>
                    <input type="text" id="code" name="code" class="auth-input" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="123456" autofocus style="text-align:center;font-size:1.8rem;font-weight:700;letter-spacing:0.4rem;font-variant-numeric:tabular-nums;">
                </div>
                <button type="submit" class="auth-submit-btn">Code bestätigen</button>
            </form>

            <div class="auth-secondary-actions">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="auth-ghost-btn">Neuen Code senden</button>
                </form>

                @if(isset($pendingEmail))
                    <form method="POST" action="{{ route('einstellungen.cancel-email-change') }}">
                        @csrf
                        <button type="submit" class="auth-ghost-btn">Abbrechen</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="auth-ghost-btn">Abmelden</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
