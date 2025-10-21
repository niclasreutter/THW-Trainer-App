// PWA Detection & Service Worker Management
class PWAManager {
    constructor() {
        this.isPWA = this.detectPWA();
        this.serviceWorkerRegistration = null;
        this.vapidPublicKey = null;
    }

    // Prüfe ob App als PWA läuft
    detectPWA() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true ||
               document.referrer.includes('android-app://');
    }

    // Registriere Service Worker
    async registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            console.log('Service Worker not supported');
            return false;
        }

        try {
            this.serviceWorkerRegistration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });

            console.log('Service Worker registered:', this.serviceWorkerRegistration);

            // Warte auf aktivierung
            await navigator.serviceWorker.ready;
            console.log('Service Worker ready');

            return true;
        } catch (error) {
            console.error('Service Worker registration failed:', error);
            return false;
        }
    }

    // Lade VAPID Public Key
    async loadVapidPublicKey() {
        try {
            const response = await fetch('/push/public-key');
            const data = await response.json();
            
            if (data.success) {
                this.vapidPublicKey = data.publicKey;
                return true;
            } else {
                console.error('Failed to load VAPID public key:', data.message);
                return false;
            }
        } catch (error) {
            console.error('Error loading VAPID public key:', error);
            return false;
        }
    }

    // Prüfe Push-Berechtigung
    async checkPushPermission() {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        return Notification.permission;
    }

    // Zeige Push-Consent-Dialog (nur in PWA)
    async showPushConsentDialog() {
        // Nur in PWA zeigen
        if (!this.isPWA) {
            console.log('Not in PWA mode, skipping push consent dialog');
            return;
        }

        // Prüfe ob bereits entschieden wurde
        const consentDecision = localStorage.getItem('push_consent_decision');
        if (consentDecision) {
            console.log('Push consent already decided:', consentDecision);
            return;
        }

        // Prüfe Browser-Support
        const permission = await this.checkPushPermission();
        if (permission === 'unsupported') {
            console.log('Push notifications not supported');
            return;
        }

        // Zeige Dialog nach 5 Sekunden (damit User sich eingewöhnt)
        setTimeout(() => {
            this.displayPushConsentDialog();
        }, 5000);
    }

    displayPushConsentDialog() {
        const dialog = document.createElement('div');
        dialog.className = 'fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50';
        dialog.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-thw-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Benachrichtigungen aktivieren?
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Erhalte Push-Benachrichtigungen für:
                        </p>
                        <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                            <li>Tägliche Lern-Erinnerungen</li>
                            <li>Streak-Benachrichtigungen</li>
                            <li>Neue Features und Updates</li>
                            <li>Motivations-Tipps</li>
                        </ul>
                        <p class="mt-3 text-xs text-gray-500">
                            Du kannst dies jederzeit in den Einstellungen ändern.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button id="push-consent-accept" class="flex-1 bg-thw-blue text-white px-4 py-2 rounded-lg hover:bg-thw-blue-dark transition-colors">
                        Aktivieren
                    </button>
                    <button id="push-consent-decline" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Nicht jetzt
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(dialog);

        // Event Handlers
        document.getElementById('push-consent-accept').addEventListener('click', async () => {
            await this.enablePushNotifications();
            localStorage.setItem('push_consent_decision', 'accepted');
            dialog.remove();
        });

        document.getElementById('push-consent-decline').addEventListener('click', () => {
            localStorage.setItem('push_consent_decision', 'declined');
            dialog.remove();
        });
    }

    // Aktiviere Push-Benachrichtigungen
    async enablePushNotifications() {
        try {
            // Request permission
            const permission = await Notification.requestPermission();
            
            if (permission !== 'granted') {
                console.log('Push permission denied');
                return false;
            }

            // Lade VAPID key falls noch nicht geladen
            if (!this.vapidPublicKey) {
                const keyLoaded = await this.loadVapidPublicKey();
                if (!keyLoaded) {
                    throw new Error('Failed to load VAPID public key');
                }
            }

            // Subscribe to push
            const subscription = await this.serviceWorkerRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            // Sende Subscription an Server
            const response = await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(subscription.toJSON())
            });

            const data = await response.json();
            
            if (data.success) {
                console.log('Push subscription successful');
                this.showNotification('Benachrichtigungen aktiviert', 'Du erhältst ab jetzt Push-Benachrichtigungen!', 'success');
                return true;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Failed to enable push notifications:', error);
            this.showNotification('Fehler', 'Push-Benachrichtigungen konnten nicht aktiviert werden.', 'error');
            return false;
        }
    }

    // Deaktiviere Push-Benachrichtigungen
    async disablePushNotifications() {
        try {
            const subscription = await this.serviceWorkerRegistration.pushManager.getSubscription();
            
            if (subscription) {
                // Unsubscribe vom Browser
                await subscription.unsubscribe();

                // Sende Unsubscribe an Server
                const response = await fetch('/push/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ endpoint: subscription.endpoint })
                });

                const data = await response.json();
                
                if (data.success) {
                    console.log('Push unsubscribe successful');
                    localStorage.removeItem('push_consent_decision');
                    this.showNotification('Benachrichtigungen deaktiviert', 'Du erhältst keine Push-Benachrichtigungen mehr.', 'success');
                    return true;
                }
            }
        } catch (error) {
            console.error('Failed to disable push notifications:', error);
            this.showNotification('Fehler', 'Push-Benachrichtigungen konnten nicht deaktiviert werden.', 'error');
            return false;
        }
    }

    // Hilfsfunktion: Base64 zu Uint8Array
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // Zeige Notification
    showNotification(title, message, type = 'info') {
        // Erstelle temporäre Notification im UI
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        
        notification.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-md`;
        notification.innerHTML = `
            <div class="font-semibold">${title}</div>
            <div class="text-sm mt-1">${message}</div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Initialisierung
    async init() {
        console.log('PWA Manager initializing...');
        console.log('Is PWA:', this.isPWA);

        // Registriere Service Worker
        const swRegistered = await this.registerServiceWorker();
        
        if (!swRegistered) {
            console.error('Service Worker registration failed');
            return;
        }

        // Zeige Push-Dialog nur in PWA
        if (this.isPWA) {
            await this.showPushConsentDialog();
        }
    }
}

// Globale Instanz
window.pwaManager = new PWAManager();

// Auto-Init bei Page-Load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.pwaManager.init());
} else {
    window.pwaManager.init();
}
