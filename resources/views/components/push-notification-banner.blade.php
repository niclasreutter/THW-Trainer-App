@auth
<div x-data="pushNotificationBanner()"
     x-show="showBanner"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="glass p-4 mb-4 border border-white/10 rounded-xl">

    {{-- Nicht subscribed: Opt-In anzeigen --}}
    <template x-if="!subscribed">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <i class="bi bi-bell text-xl" style="color: var(--gold-start)"></i>
                <div class="min-w-0">
                    <p class="text-sm font-medium" style="color: var(--text-primary)">
                        Push-Benachrichtigungen aktivieren
                    </p>
                    <p class="text-xs" style="color: var(--text-secondary)">
                        Erhalte Mitteilungen zu Level-Ups, Achievements und Liga-Ergebnissen.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button @click="subscribe()"
                        class="btn-primary text-sm px-3 py-1.5"
                        :disabled="loading">
                    <span x-show="!loading">Aktivieren</span>
                    <span x-show="loading">...</span>
                </button>
                <button @click="dismiss()"
                        class="btn-ghost text-sm px-2 py-1.5">
                    Nein danke
                </button>
            </div>
        </div>
    </template>

    {{-- Bereits subscribed: Status anzeigen --}}
    <template x-if="subscribed">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="bi bi-bell-fill text-xl" style="color: var(--success)"></i>
                <p class="text-sm" style="color: var(--text-primary)">
                    Push-Benachrichtigungen sind aktiviert.
                </p>
            </div>
            <button @click="unsubscribe()" class="btn-ghost text-sm px-3 py-1.5" style="color: var(--error)">
                Deaktivieren
            </button>
        </div>
    </template>
</div>

<script>
function pushNotificationBanner() {
    return {
        showBanner: false,
        subscribed: false,
        loading: false,

        init() {
            if (!('PushManager' in window) || !('serviceWorker' in navigator)) return;
            if (localStorage.getItem('push-banner-dismissed')) return;

            if (Notification.permission === 'granted') {
                this.checkExistingSubscription();
            } else if (Notification.permission !== 'denied') {
                this.showBanner = true;
            }
        },

        async checkExistingSubscription() {
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            if (sub) {
                this.subscribed = true;
                this.showBanner = true;
            } else {
                this.showBanner = true;
            }
        },

        async subscribe() {
            this.loading = true;
            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    this.loading = false;
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

                const response = await fetch('/push/subscribe', {
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

                if (response.ok) {
                    this.subscribed = true;
                }
            } catch (e) {
                console.error('Push subscription failed:', e);
            }
            this.loading = false;
        },

        async unsubscribe() {
            try {
                const reg = await navigator.serviceWorker.ready;
                const subscription = await reg.pushManager.getSubscription();
                if (subscription) {
                    await fetch('/push/unsubscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ endpoint: subscription.endpoint }),
                    });
                    await subscription.unsubscribe();
                }
                this.subscribed = false;
                this.showBanner = false;
            } catch (e) {
                console.error('Push unsubscribe failed:', e);
            }
        },

        dismiss() {
            this.showBanner = false;
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
