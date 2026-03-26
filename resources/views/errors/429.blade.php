@extends('errors.layout')

@section('title', '429 - Zu viele Anfragen')

@section('content')
    <div class="error-code">429</div>
    <div class="error-title">Zu viele Anfragen</div>
    <p class="error-desc">Bitte warte einen Moment, bevor du es erneut versuchst.</p>
    <div class="error-actions">
        <button id="reloadBtn" onclick="startReload()" class="btn-primary">Erneut versuchen</button>
        <a href="{{ url('/') }}" class="btn-secondary">Zur Startseite</a>
    </div>

    <script>
    function startReload() {
        var btn = document.getElementById('reloadBtn');
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.textContent = 'Laden...';
        setTimeout(function() { window.location.reload(); }, 3000);
    }
    </script>
@endsection
