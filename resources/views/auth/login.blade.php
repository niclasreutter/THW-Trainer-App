@extends('layouts.public')

@section('title', 'Anmelden - THW Trainer')
@section('description', 'Melde dich bei THW-Trainer an und greife auf deinen persönlichen Lernfortschritt zu.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="d-auth">
    <img class="d-in" src="{{ asset('logo-thw-trainer.png') }}" alt="" width="56" height="56" style="margin-bottom:28px">
    <h1 class="d-h2 d-in" style="--i:1;font-size:clamp(2rem,5vw,2.75rem)">Anmelden.</h1>
    <p class="d-copy d-in" style="--i:2;margin:12px 0 32px">Dein Lernstand wartet auf dich.</p>

    @if (session('status'))
        <p class="d-notice" role="status">{{ session('status') }}</p>
    @endif
    @if ($errors->any())
        <div class="d-notice d-notice--error" role="alert">
            <b>Anmeldung fehlgeschlagen</b>
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form class="d-in" style="--i:3" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="d-field">
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder=" " autocomplete="email" inputmode="email" required autofocus @error('email') aria-invalid="true" aria-describedby="email-err" @enderror>
            <label for="email">E-Mail-Adresse</label>
            @error('email')<p class="err" id="email-err">{{ $message }}</p>@enderror
        </div>
        <div class="d-field">
            <input id="password" type="password" name="password" placeholder=" " autocomplete="current-password" required @error('password') aria-invalid="true" aria-describedby="password-err" @enderror>
            <label for="password">Passwort</label>
            @error('password')<p class="err" id="password-err">{{ $message }}</p>@enderror
        </div>
        <label class="d-check"><input type="checkbox" name="remember" id="remember"><span>Angemeldet bleiben</span></label>
        <button class="d-pill" type="submit" style="width:100%;height:52px">Anmelden</button>
    </form>

    <div class="d-in" style="--i:4;margin-top:28px;display:grid;gap:10px;justify-items:start">
        <a class="d-more" href="{{ route('password.request') }}">Passwort vergessen</a>
        <a class="d-more" href="{{ route('register') }}">Noch kein Konto? Jetzt registrieren</a>
        <a class="d-more" href="{{ route('landing.guest.practice.menu') }}">Ohne Konto üben</a>
        @if(!app()->isProduction())
            <a class="d-more" href="{{ route('dev.clear-cookies') }}" style="color:var(--bad)">Dev: Cookies leeren und neu laden</a>
        @endif
    </div>
</section>
@endsection
