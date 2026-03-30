// v5.0 — Push-only mit iOS-Fixes
// iOS: Kein Fetch-Handler, besseres notificationclick, robuster push handler
// KEIN Cache — Push-only SW

self.addEventListener('install', event => {
  console.log('[SW v5] Installing push-only service worker');
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(names => Promise.all(names.map(n => caches.delete(n))))
      .then(() => self.clients.claim())
      .then(() => console.log('[SW v5] Activated, old caches cleared'))
  );
});

// KEIN fetch handler — Push-only SW

// Push — IMMER sichtbare Notification in waitUntil (iOS-Pflicht)
self.addEventListener('push', event => {
  let data = {};
  try {
    data = event.data?.json() ?? {};
  } catch (e) {
    data = { title: 'THW Trainer', body: event.data?.text() ?? '' };
  }

  const options = {
    body: data.body || 'Neue Mitteilung',
    icon: '/icon-192.png',
    badge: '/icon-192.png',
    data: { url: data.url || '/notifications' },
    // iOS braucht tag um doppelte Notifications zu vermeiden
    tag: data.tag || 'thw-' + Date.now(),
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'THW Trainer', options)
  );
});

// Notification click — existierendes Fenster fokussieren statt neues oeffnen
self.addEventListener('notificationclick', event => {
  event.notification.close();
  const targetUrl = event.notification.data?.url || '/notifications';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true })
      .then(windowClients => {
        // Existierendes Fenster mit gleicher Origin suchen und fokussieren
        for (const client of windowClients) {
          if (client.url.startsWith(self.location.origin) && 'focus' in client) {
            return client.focus().then(c => c.navigate(targetUrl));
          }
        }
        // Kein Fenster offen — neues oeffnen
        return clients.openWindow(targetUrl);
      })
  );
});

// Subscription-Wechsel (feuert nicht zuverlaessig auf iOS,
// Client-seitiger Re-Subscribe in app.blade.php uebernimmt das)
self.addEventListener('pushsubscriptionchange', event => {
  event.waitUntil(
    self.registration.pushManager.subscribe(event.oldSubscription?.options || { userVisibleOnly: true })
      .then(sub => {
        // Client informieren damit er den Server updaten kann
        return self.clients.matchAll().then(cls => {
          cls.forEach(c => c.postMessage({
            type: 'pushsubscriptionchange',
            subscription: sub.toJSON(),
            oldEndpoint: event.oldSubscription?.endpoint || null,
          }));
        });
      })
      .catch(err => console.log('[SW] Re-subscribe failed:', err))
  );
});
