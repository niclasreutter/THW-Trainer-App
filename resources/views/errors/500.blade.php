@extends('errors.layout')

@section('title', '500 - Serverfehler')

@section('content')
    <div class="error-code">500</div>
    <div class="error-title">Interner Serverfehler</div>
    <p class="error-desc">Etwas ist schiefgelaufen. Wir wurden benachrichtigt und arbeiten daran.</p>
    <div class="error-actions">
        <button onclick="window.location.reload()" class="btn-primary">Seite neu laden</button>
        <a href="{{ url('/') }}" class="btn-secondary">Zur Startseite</a>
    </div>
@endsection
