@extends('errors.layout')

@section('title', '503 - Wartungsmodus')

@section('content')
    <div class="error-code">503</div>
    <div class="error-title">Wartungsmodus</div>
    <p class="error-desc">Wir fuehren gerade Wartungsarbeiten durch. Bitte versuche es in wenigen Minuten erneut.</p>
    <div class="error-actions">
        <button onclick="window.location.reload()" class="btn-primary">Erneut versuchen</button>
    </div>
@endsection
