// PWA Detection and Push Notification Handler

/**
 * Check if app is running as PWA (standalone mode)
 */
function isPWA() {
    return window.matchMedia('(display-mode: standalone)').matches ||
           window.navigator.standalone === true ||
           document.referrer.includes('android-app://');
}

/**
 * Check if push notifications are supported
 */
function isPushSupported() {
    return 'serviceWorker' in navigator && 
           'PushManager' in window &&
           'Notification' in window;
}

/**
 * Get current push permission status
 */
function getPushPermission() {
    if (!isPushSupported()) return 'unsupported';
    return Notification.permission;
}

/**
 * Request push notification permission and subscribe
 */
async function requestPushPermission() {
    try {
        // Check if already granted
        if (Notification.permission === 'granted') {
            return await subscribeToPush();
        }

        // Request permission
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            return await subscribeToPush();
        } else if (permission === 'denied') {
            console.warn('Push notification permission denied');
            return { success: false, message: 'Benachrichtigungen wurden blockiert' };
        } else {
            return { success: false, message: 'Benachrichtigungen wurden abgelehnt' };
        }
    } catch (error) {
        console.error('Error requesting push permission:', error);
        return { success: false, message: error.message };
    }
}

/**
 * Subscribe to push notifications
 */
async function subscribeToPush() {
    try {
        // Get service worker registration
        const registration = await navigator.serviceWorker.ready;
        
        // Get VAPID public key from backend
        const response = await fetch('/push/vapid-public-key');
        const { publicKey } = await response.json();
        
        // Subscribe to push
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(publicKey)
        });

        // Send subscription to backend
        const saveResponse = await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                keys: {
                    p256dh: arrayBufferToBase64(subscription.getKey('p256dh')),
                    auth: arrayBufferToBase64(subscription.getKey('auth'))
                }
            })
        });

        const result = await saveResponse.json();
        
        if (result.success) {
            console.log('Push subscription successful');
            return { success: true, message: 'Push-Benachrichtigungen aktiviert!' };
        } else {
            throw new Error(result.message || 'Subscription failed');
        }
    } catch (error) {
        console.error('Error subscribing to push:', error);
        return { success: false, message: error.message };
    }
}

/**
 * Unsubscribe from push notifications
 */
async function unsubscribeFromPush() {
    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        
        if (subscription) {
            // Unsubscribe from push manager
            await subscription.unsubscribe();
            
            // Remove from backend
            await fetch('/push/unsubscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint
                })
            });
            
            return { success: true, message: 'Push-Benachrichtigungen deaktiviert' };
        }
        
        return { success: false, message: 'Keine aktive Subscription gefunden' };
    } catch (error) {
        console.error('Error unsubscribing from push:', error);
        return { success: false, message: error.message };
    }
}

/**
 * Check if user is already subscribed
 */
async function isSubscribedToPush() {
    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        return subscription !== null;
    } catch (error) {
        console.error('Error checking subscription:', error);
        return false;
    }
}

/**
 * Send a test push notification
 */
async function sendTestPushNotification() {
    try {
        const response = await fetch('/push/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error sending test notification:', error);
        return { success: false, message: error.message };
    }
}

/**
 * Show push notification prompt (only in PWA mode)
 */
function showPushPromptIfPWA() {
    // Only show in PWA mode
    if (!isPWA()) {
        console.log('Not running as PWA, skipping push prompt');
        return;
    }

    // Check if push is supported
    if (!isPushSupported()) {
        console.log('Push notifications not supported');
        return;
    }

    // Check if already granted or denied
    if (Notification.permission === 'granted') {
        console.log('Push already granted');
        // Check if subscribed, if not subscribe
        isSubscribedToPush().then(subscribed => {
            if (!subscribed) {
                subscribeToPush();
            }
        });
        return;
    }

    if (Notification.permission === 'denied') {
        console.log('Push permission denied');
        return;
    }

    // Check if user dismissed prompt recently
    const dismissedAt = localStorage.getItem('push_prompt_dismissed_at');
    if (dismissedAt) {
        const daysSinceDismissed = (Date.now() - parseInt(dismissedAt)) / (1000 * 60 * 60 * 24);
        if (daysSinceDismissed < 7) {
            console.log('Push prompt dismissed recently');
            return;
        }
    }

    // Show custom prompt
    setTimeout(() => {
        showPushPermissionBanner();
    }, 3000); // Show after 3 seconds
}

/**
 * Show custom push permission banner
 */
function showPushPermissionBanner() {
    // Check if banner already exists
    if (document.getElementById('push-permission-banner')) {
        return;
    }

    const banner = document.createElement('div');
    banner.id = 'push-permission-banner';
    banner.className = 'fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:max-w-md bg-white rounded-lg shadow-2xl p-6 border-2 border-blue-500 z-50 animate-slide-up';
    banner.style.animation = 'slideUp 0.3s ease-out';
    
    banner.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0 text-4xl mr-4">🔔</div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Push-Benachrichtigungen aktivieren?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Verpasse keine Updates! Erhalte Benachrichtigungen über deinen Lernfortschritt und neue Features direkt auf dein Gerät.
                </p>
                <div class="flex gap-2">
                    <button id="push-allow-btn" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Aktivieren
                    </button>
                    <button id="push-dismiss-btn" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Später
                    </button>
                </div>
            </div>
            <button id="push-close-btn" class="ml-2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(banner);

    // Add event listeners
    document.getElementById('push-allow-btn').addEventListener('click', async () => {
        const btn = document.getElementById('push-allow-btn');
        btn.disabled = true;
        btn.textContent = 'Aktiviere...';
        
        const result = await requestPushPermission();
        
        if (result.success) {
            showToast(result.message || 'Push-Benachrichtigungen aktiviert! 🎉', 'success');
            banner.remove();
        } else {
            showToast(result.message || 'Fehler beim Aktivieren', 'error');
            btn.disabled = false;
            btn.textContent = 'Aktivieren';
        }
    });

    document.getElementById('push-dismiss-btn').addEventListener('click', () => {
        localStorage.setItem('push_prompt_dismissed_at', Date.now().toString());
        banner.remove();
    });

    document.getElementById('push-close-btn').addEventListener('click', () => {
        localStorage.setItem('push_prompt_dismissed_at', Date.now().toString());
        banner.remove();
    });
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 animate-slide-down ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    toast.style.animation = 'slideDown 0.3s ease-out';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Helper: Convert VAPID public key to Uint8Array
 */
function urlBase64ToUint8Array(base64String) {
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

/**
 * Helper: Convert ArrayBuffer to Base64
 */
function arrayBufferToBase64(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.byteLength; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return window.btoa(binary);
}

// CSS Animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', showPushPromptIfPWA);
} else {
    showPushPromptIfPWA();
}

// Export functions for global use
window.pushNotifications = {
    isPWA,
    isPushSupported,
    getPushPermission,
    requestPushPermission,
    subscribeToPush,
    unsubscribeFromPush,
    isSubscribedToPush,
    sendTestPushNotification,
    showPushPromptIfPWA
};
