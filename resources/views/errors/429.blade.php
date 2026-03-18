@extends('errors.layout')

@section('title', '429 - Zu viele Anfragen')

@section('content')
<div class="glass-purple p-8 text-center">
    <!-- Error Code -->
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4" style="background: rgba(168, 85, 247, 0.15);">
            <i class="bi bi-lightning-charge text-4xl" style="color: #a855f7;"></i>
        </div>
        <h1 class="text-7xl font-extrabold mb-2">
            <span class="text-gradient-gold">429</span>
        </h1>
        <h2 class="text-2xl font-bold mb-3" style="color: var(--thw-blue);">
            Zu viele Anfragen
        </h2>
        <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto;">
            Du warst etwas zu schnell unterwegs. Bitte warte einen kurzen Moment,
            bevor du es erneut versuchst.
        </p>
    </div>

    <!-- Info Box -->
    <div class="alert-glass warning mb-6" style="text-align: left;">
        <i class="bi bi-hourglass-split" style="color: var(--warning);"></i>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
            Bitte warte <strong>einen Moment</strong>, dann kannst du fortfahren.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col gap-3">
        <button
            id="reloadBtn"
            onclick="startReload()"
            class="btn-primary">
            Automatisch neu laden
        </button>

        <a href="{{ url('/') }}" class="btn-secondary">
            Zur Startseite
        </a>
    </div>
</div>

<script>
function startReload() {
    var btn = document.getElementById('reloadBtn');
    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-not-allowed');
    btn.textContent = 'Lädt in 3 Sekunden...';
    setTimeout(function() { window.location.reload(); }, 3000);
}
</script>
@endsection
