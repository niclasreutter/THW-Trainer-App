@extends('errors.layout')

@section('title', '419 - Sitzung abgelaufen')

@section('content')
    <div class="error-code">419</div>
    <div class="error-title">Sitzung abgelaufen</div>
    <p class="error-desc">Deine Sitzung ist abgelaufen. Bitte lade die Seite neu oder melde dich erneut an.</p>
    <div class="error-actions">
        <button onclick="window.location.reload()" class="btn-primary">Seite neu laden</button>
        <a href="{{ route('login') }}" class="btn-secondary">Anmelden</a>
    </div>
@endsection
