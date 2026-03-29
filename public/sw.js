const CACHE_VERSION = 'v2.4';
const CACHE_NAME = `thw-trainer-${CACHE_VERSION}`;
const RUNTIME_CACHE = `thw-trainer-runtime-${CACHE_VERSION}`;

// Assets to precache
const PRECACHE_ASSETS = [
  '/offline',
  '/logo-thwtrainer.png',
  '/logo-thwtrainer_w.png',
  '/manifest.json',
  '/favicon.ico'
];

// Install event - precache offline page
self.addEventListener('install', event => {
  console.log('[SW] Installing service worker...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[SW] Precaching offline assets');
        return cache.addAll(PRECACHE_ASSETS);
      })
      .then(() => {
        console.log('[SW] Installation complete');
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('[SW] Installation failed:', error);
      })
  );
});

// Activate event - cleanup old caches
self.addEventListener('activate', event => {
  console.log('[SW] Activating service worker...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames
          .filter(cacheName => 
            cacheName.startsWith('thw-trainer-') && 
            !cacheName.includes(CACHE_VERSION)
          )
          .map(cacheName => {
            console.log('[SW] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          })
      );
    }).then(() => {
      console.log('[SW] Activation complete');
      return self.clients.claim();
    })
  );
});

// Fetch event - Network first, offline page as fallback
self.addEventListener('fetch', event => {
  const { request } = event;
  
  // Skip non-GET requests
  if (request.method !== 'GET') return;
  
  // Skip chrome extensions and other non-http(s) requests
  if (!request.url.startsWith('http')) return;

  // For navigation requests (HTML pages)
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then(response => {
          // Clone and cache successful responses
          if (response.status === 200) {
            const responseToCache = response.clone();
            caches.open(RUNTIME_CACHE).then(cache => {
              cache.put(request, responseToCache);
            });
          }
          return response;
        })
        .catch(() => {
          // Network failed - try cache
          return caches.match(request).then(cachedResponse => {
            if (cachedResponse) {
              return cachedResponse;
            }
            
            // Fallback to offline page
            return caches.match('/offline');
          });
        })
    );
    return;
  }

  // For all other requests (CSS, JS, images) - cache first
  event.respondWith(
    caches.match(request).then(cachedResponse => {
      if (cachedResponse) {
        return cachedResponse;
      }
      
      return fetch(request).then(response => {
        // Cache successful responses
        if (response.status === 200) {
          const responseToCache = response.clone();
          caches.open(RUNTIME_CACHE).then(cache => {
            cache.put(request, responseToCache);
          });
        }
        return response;
      }).catch(() => {
        // Return nothing on error for assets
        return new Response('', {
          status: 408,
          statusText: 'Request Timeout'
        });
      });
    })
  );
});

// Push notification received
self.addEventListener('push', event => {
  console.log('[SW] Push received');

  let data = { title: 'THW Trainer', body: 'Neue Mitteilung', url: '/notifications' };

  if (event.data) {
    try {
      data = Object.assign(data, event.data.json());
    } catch (e) {
      data.body = event.data.text() || data.body;
    }
  }

  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      data: { url: data.url || '/notifications' },
    })
  );
});

// Handle subscription loss (iOS kills subscription on SW update)
self.addEventListener('pushsubscriptionchange', event => {
  console.log('[SW] Push subscription changed, re-subscribing...');
  event.waitUntil(
    self.registration.pushManager.subscribe(event.oldSubscription.options)
      .then(newSubscription => {
        const sub = newSubscription.toJSON();
        return fetch('/push/subscribe', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            endpoint: sub.endpoint,
            keys: {
              p256dh: sub.keys.p256dh,
              auth: sub.keys.auth,
            },
            contentEncoding: (PushManager.supportedContentEncodings || ['aes128gcm'])[0],
            old_endpoint: event.oldSubscription ? event.oldSubscription.endpoint : null,
          }),
        });
      })
      .then(() => console.log('[SW] Re-subscription successful'))
      .catch(err => console.error('[SW] Re-subscription failed:', err))
  );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
  console.log('[SW] Notification clicked');
  event.notification.close();

  const url = event.notification.data?.url || '/notifications';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
      for (const client of windowClients) {
        if (client.url.includes(url) && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(url);
      }
    })
  );
});
