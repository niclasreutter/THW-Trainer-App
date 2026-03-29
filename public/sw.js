// v3.1 — Nur Push, kein Caching/Fetch
// iOS kann Push blockieren wenn der SW mit Fetch-Events beschaeftigt ist

self.addEventListener('install', event => {
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  // Alte Caches aufraeumen
  event.waitUntil(
    caches.keys()
      .then(names => Promise.all(names.map(n => caches.delete(n))))
      .then(() => self.clients.claim())
  );
});

// KEIN fetch handler — Push-only SW

// Push — IMMER sichtbare Notification in waitUntil
self.addEventListener('push', event => {
  let data = {};
  try {
    data = event.data?.json() ?? {};
  } catch (e) {
    data = { title: 'THW Trainer', body: event.data?.text() ?? '' };
  }

  event.waitUntil(
    self.registration.showNotification(
      data.title || 'THW Trainer',
      {
        body: data.body || 'Neue Mitteilung',
        data: { url: data.url || '/notifications' },
      }
    )
  );
});

// Notification click
self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow(event.notification.data?.url || '/notifications')
  );
});

// Subscription-Wechsel
self.addEventListener('pushsubscriptionchange', event => {
  event.waitUntil(
    self.registration.pushManager.subscribe(event.oldSubscription?.options || { userVisibleOnly: true })
      .then(sub => {
        const json = sub.toJSON();
        return fetch('/push/subscribe', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            endpoint: json.endpoint,
            keys: { p256dh: json.keys.p256dh, auth: json.keys.auth },
            contentEncoding: (PushManager.supportedContentEncodings || ['aes128gcm'])[0],
            old_endpoint: event.oldSubscription?.endpoint || null,
          }),
        });
      })
  );
});
