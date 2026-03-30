const CACHE_VERSION = 'v3.0';
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
  if (!event.data) return;

  let data;
  try {
    data = event.data.json();
  } catch (e) {
    data = {
      title: 'THW Trainer',
      body: event.data.text()
    };
  }

  const title = data.title || 'THW Trainer';
  const options = {
    body: data.body || '',
    icon: data.icon || '/logo-thwtrainer.png',
    badge: data.badge || '/logo-thwtrainer.png',
    tag: data.tag || 'thw-trainer-default',
    data: data.data || {},
    vibrate: [200, 100, 200]
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Notification clicked
self.addEventListener('notificationclick', event => {
  event.notification.close();

  const url = event.notification.data?.url || '/dashboard';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true })
      .then(windowClients => {
        // Focus existing window if available
        for (const client of windowClients) {
          if (client.url.includes(self.location.origin) && 'focus' in client) {
            client.navigate(url);
            return client.focus();
          }
        }
        // Open new window
        return clients.openWindow(url);
      })
  );
});
