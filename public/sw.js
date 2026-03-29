const CACHE_VERSION = 'v3.0';
const CACHE_NAME = `thw-trainer-${CACHE_VERSION}`;

// Assets to precache
const PRECACHE_ASSETS = [
  '/offline',
  '/logo-thwtrainer.png',
  '/logo-thwtrainer_w.png',
  '/manifest.json',
  '/favicon.ico'
];

// Install event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(PRECACHE_ASSETS))
      .then(() => self.skipWaiting())
  );
});

// Activate event - cleanup old caches + claim clients
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(names => Promise.all(
        names
          .filter(n => n.startsWith('thw-trainer-') && n !== CACHE_NAME)
          .map(n => caches.delete(n))
      ))
      .then(() => self.clients.claim())
  );
});

// Fetch event - nur Navigation offline-fallback, kein Runtime-Caching
self.addEventListener('fetch', event => {
  if (event.request.mode !== 'navigate') return;

  event.respondWith(
    fetch(event.request).catch(() =>
      caches.match(event.request)
        .then(r => r || caches.match('/offline'))
    )
  );
});

// Push — IMMER sichtbare Notification, IMMER in waitUntil
self.addEventListener('push', event => {
  let data = {};
  try {
    data = event.data?.json() ?? {};
  } catch (e) {
    data = { title: 'THW Trainer', body: event.data?.text() ?? '' };
  }

  const promise = self.registration.showNotification(
    data.title || 'THW Trainer',
    {
      body: data.body || 'Neue Mitteilung',
      data: { url: data.url || '/notifications' },
    }
  );

  event.waitUntil(promise);
});

// Notification click
self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow(event.notification.data?.url || '/notifications')
  );
});

// Subscription-Wechsel (iOS kann Endpoint nach Reload aendern)
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
