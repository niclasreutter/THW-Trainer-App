@auth
<div x-data="pushOptIn()" x-show="showModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="push-opt-in-card p-6 rounded-2xl max-w-md w-full" @click.outside="dismiss()">
        <h3 class="text-lg font-bold mb-2" style="color: #ffffff !important;">Benachrichtigungen aktivieren</h3>
        <p class="text-sm mb-6" style="color: rgba(255,255,255,0.7) !important;">
            Erhalte Erinnerungen an deine Lern-Streaks, Liga-Updates und wichtige Neuigkeiten direkt auf dein Gerät.
        </p>
        <div class="flex gap-3 justify-end">
            <button @click="dismiss()" class="btn-ghost text-sm" style="color: rgba(255,255,255,0.8) !important; border-color: rgba(255,255,255,0.2) !important;">Später</button>
            <button @click="subscribe()" class="btn-primary text-sm">Aktivieren</button>
        </div>
    </div>
</div>
@endauth
