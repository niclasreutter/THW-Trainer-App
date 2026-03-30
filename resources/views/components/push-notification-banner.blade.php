@auth
<div x-data="pushNotificationPopup()" x-cloak>

    {{-- Overlay --}}
    <div x-show="showPopup"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="modal-overlay-glass"
         style="z-index: 9999;">

        {{-- Modal --}}
        <div class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 10000;">
            <div x-show="showPopup"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 @click.outside="dismiss()"
                 class="modal-glass w-full max-w-sm">

                <div class="modal-body-glass text-center">
                    {{-- Icon --}}
                    <div class="mx-auto mb-4 w-16 h-16 rounded-full flex items-center justify-center"
                         style="background: linear-gradient(135deg, var(--gold-start), var(--gold-end));">
                        <i class="bi bi-bell-fill text-2xl text-black"></i>
                    </div>

                    {{-- Text --}}
                    <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary)">
                        Push-Benachrichtigungen
                    </h3>
                    <p class="text-sm mb-6" style="color: var(--text-secondary)">
                        Erhalte Mitteilungen zu Level-Ups, Achievements und Liga-Ergebnissen direkt auf dein Geraet.
                    </p>

                    {{-- Buttons --}}
                    <div class="flex flex-col gap-3">
                        <button @click="subscribe()"
                                class="btn-primary w-full py-3"
                                :disabled="loading">
                            <span x-show="!loading">Aktivieren</span>
                            <span x-show="loading">Wird aktiviert...</span>
                        </button>
                        <button @click="dismiss()"
                                class="btn-ghost w-full py-2 text-sm">
                            Nein danke
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function pushNotificationPopup() {
    return {
        showPopup: false,
        loading: false,

        init() {
            if (!('PushManager' in window) || !('serviceWorker' in navigator)) return;
            if (localStorage.getItem('push-banner-dismissed')) return;
            if (localStorage.getItem('push-subscribed')) return;
            if (Notification.permission === 'denied') return;

            // Zeige Popup nur wenn noch nicht subscribed
            setTimeout(() => { this.showPopup = true; }, 1500);
        },

        async subscribe() {
            this.loading = true;
            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    this.loading = false;
                    this.dismiss();
                    return;
                }

                const keyResponse = await fetch('/push/key');
                const keyData = await keyResponse.json();
                if (!keyData.success) throw new Error('VAPID key not available');

                const reg = await navigator.serviceWorker.ready;
                const subscription = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(keyData.publicKey),
                });

                const sub = subscription.toJSON();

                await fetch('/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        endpoint: sub.endpoint,
                        keys: {
                            p256dh: sub.keys.p256dh,
                            auth: sub.keys.auth,
                        },
                        contentEncoding: (PushManager.supportedContentEncodings || ['aes128gcm'])[0],
                    }),
                });

                this.showPopup = false;
                localStorage.setItem('push-subscribed', '1');
                localStorage.setItem('push-endpoint', sub.endpoint);
                localStorage.setItem('push-banner-dismissed', '1');
            } catch (e) {
                console.error('Push subscription failed:', e);
            }
            this.loading = false;
        },

        dismiss() {
            this.showPopup = false;
            localStorage.setItem('push-banner-dismissed', '1');
        },

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        },
    };
}
</script>
@endauth
