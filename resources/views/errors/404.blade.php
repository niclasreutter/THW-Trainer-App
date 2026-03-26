@extends('errors.layout')

@section('title', '404 - Seite nicht gefunden')

@section('content')
    <div class="error-code">404</div>
    <div class="error-title">Seite nicht gefunden</div>
    <p class="error-desc">Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
    <div class="error-actions">
        <a href="{{ url('/') }}" class="btn-primary">Zur Startseite</a>
        @auth
            <a href="{{ route('dashboard') }}" class="btn-secondary">Dashboard</a>
        @endauth
    </div>
@endsection
