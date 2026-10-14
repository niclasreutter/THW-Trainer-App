@extends('layouts.public')

@section('title', 'Registrierung - THW Trainer')
@section('description', 'Erstelle deinen kostenlosen THW-Trainer Account und starte sofort mit dem Lernen. Verfolge deinen Fortschritt und bereite dich optimal auf deine THW-Prüfung vor.')
@section('robots', 'noindex, nofollow')

@section('content')
@php
    $inviteCode = request('code') ?? request('invite');
    $inviteInfo = $inviteCode
        ? \App\Models\OrtsverbandInvitation::where('code', $inviteCode)->with('ortsverband', 'creator')->first()
        : null;
    $inviteValid = $inviteInfo && $inviteInfo->isValid();
@endphp
<section class="d-auth">
    <img class="d-in" src="{{ asset('logo-thw-trainer.png') }}" alt="" width="56" height="56" style="margin-bottom:28px">
    <h1 class="d-h2 d-in" style="--i:1;font-size:clamp(2rem,5vw,2.75rem)">Konto erstellen.</h1>
    <p class="d-copy d-in" style="--i:2;margin:12px 0 32px">Kostenlos. Dein Lernstand auf jedem Gerät.</p>

    @if($inviteValid)
        <p class="d-notice" role="status"><b>Einladung: {{ $inviteInfo->ortsverband->name }}</b> · Du trittst automatisch bei.</p>
    @endif
    @if(session('error'))
        <p class="d-notice d-notice--error" role="alert"><b>{{ session('error') }}</b></p>
    @endif
    @if ($errors->any())
        <div class="d-notice d-notice--error" role="alert">
            <b>Fehler bei der Registrierung</b>
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form class="d-in" style="--i:3" method="POST" action="{{ route('register') }}">
        @csrf
        <div class="d-field">
            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder=" " autocomplete="name" required autofocus @error('name') aria-invalid="true" aria-describedby="name-err" @enderror>
            <label for="name">Vollständiger Name</label>
            @error('name')<p class="err" id="name-err">{{ $message }}</p>@enderror
        </div>
        <div class="d-field">
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder=" " autocomplete="email" inputmode="email" required @error('email') aria-invalid="true" aria-describedby="email-err" @enderror>
            <label for="email">E-Mail-Adresse</label>
            @error('email')<p class="err" id="email-err">{{ $message }}</p>@enderror
        </div>
        <div class="d-field">
            <input id="password" type="password" name="password" placeholder=" " autocomplete="new-password" required aria-describedby="password-help @error('password') password-err @enderror" @error('password') aria-invalid="true" @enderror>
            <label for="password">Passwort</label>
            <p class="help" id="password-help">Mindestens 8 Zeichen.</p>
            @error('password')<p class="err" id="password-err">{{ $message }}</p>@enderror
        </div>
        <div class="d-field">
            <input id="password_confirmation" type="password" name="password_confirmation" placeholder=" " autocomplete="new-password" required>
            <label for="password_confirmation">Passwort wiederholen</label>
        </div>
        @if($inviteValid)
            <input type="hidden" name="invitation_code" value="{{ $inviteInfo->code }}">
        @else
            <div class="d-field">
                <input id="invitation_code" type="text" name="invitation_code" value="{{ old('invitation_code', $inviteCode ?? '') }}" placeholder=" " autocomplete="off" aria-describedby="invitation-help @error('invitation_code') invitation-err @enderror" @error('invitation_code') aria-invalid="true" @enderror>
                <label for="invitation_code">OV-Code (optional)</label>
                <p class="help" id="invitation-help">Hast du einen Einladungscode von deinem Ortsverband erhalten? Format THW-XXXXXXXX.</p>
                @error('invitation_code')<p class="err" id="invitation-err">{{ $message }}</p>@enderror
            </div>
        @endif
        <label class="d-check"><input type="checkbox" name="email_consent" id="email_consent" value="1" @checked(old('email_consent'))><span>E-Mails zu Lernfortschritt und neuen Funktionen erhalten</span></label>
        <button class="d-pill" type="submit" style="width:100%;height:52px">Konto erstellen</button>
    </form>

    <div class="d-in" style="--i:4;margin-top:28px;display:grid;gap:10px;justify-items:start">
        <a class="d-more" href="{{ route('login') }}">Schon registriert? Anmelden</a>
        <a class="d-more" href="{{ route('landing.guest.practice.menu') }}">Als Gast üben</a>
    </div>
</section>
@endsection
