@extends('errors.layout')

@section('title', '403 - Zugriff verweigert')

@section('content')
    <div class="error-code">403</div>
    <div class="error-title">Zugriff verweigert</div>
    <p class="error-desc">
        Du hast keine Berechtigung, auf diese Seite zuzugreifen.
        @guest Melde dich an, um fortzufahren. @endguest
    </p>
    <div class="error-actions">
        @guest
            <a href="{{ route('login') }}" class="btn-primary">Anmelden</a>
        @endguest
        <a href="{{ url('/') }}" class="btn-secondary">Zur Startseite</a>
    </div>
@endsection
