@auth
<div x-data="pushOptIn()" x-show="showModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="p-6 rounded-2xl max-w-md w-full" style="background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 0 40px rgba(212, 175, 55, 0.15);" @click.outside="dismiss()">
        <h3 class="text-lg font-bold text-white mb-2">Benachrichtigungen aktivieren</h3>
        <p class="text-sm text-white/70 mb-6">
            Erhalte Erinnerungen an deine Lern-Streaks, Liga-Updates und wichtige Neuigkeiten direkt auf dein Geraet.
        </p>
        <div class="flex gap-3 justify-end">
            <button @click="dismiss()" class="btn-ghost text-sm">Spaeter</button>
            <button @click="subscribe()" class="btn-primary text-sm">Aktivieren</button>
        </div>
    </div>
</div>
@endauth
