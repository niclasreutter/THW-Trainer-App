const CACHE_VERSION = 'v1.2';
const CACHE_NAME = `thw-trainer-${CACHE_VERSION}`;
const RUNTIME_CACHE = `thw-trainer-runtime-${CACHE_VERSION}`;
const OFFLINE_CACHE = `thw-trainer-offline-${CACHE_VERSION}`;

// Assets to precache (wichtig für Offline-Funktionalität)
const PRECACHE_ASSETS = [
  '/',
  '/dashboard',
  '/practice',
  '/exam',
  '/offline',
  '/logo-thwtrainer.png',
  '/logo-thwtrainer_w.png',
  '/manifest.json',
  '/favicon.ico'
];

// Install event - precache critical assets
self.addEventListener('install', event => {
  console.log('[SW] Installing service worker...');
  event.waitUntil(
    Promise.all([
      caches.open(CACHE_NAME).then(cache => {
        console.log('[SW] Precaching assets');
        return cache.addAll(PRECACHE_ASSETS.map(url => new Request(url, {
          cache: 'reload'
        })));
      }),
      caches.open(OFFLINE_CACHE).then(cache => {
        // Cache a simple offline page
        return cache.put('/offline', new Response(
          createOfflinePage(),
          { headers: { 'Content-Type': 'text/html' } }
        ));
      })
    ]).then(() => {
      console.log('[SW] Installation complete');
      return self.skipWaiting();
    }).catch(error => {
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

// Fetch event - Network first, fallback to cache
self.addEventListener('fetch', event => {
  const { request } = event;
  
  // Skip non-GET requests
  if (request.method !== 'GET') return;
  
  // Skip chrome extensions and other non-http(s) requests
  if (!request.url.startsWith('http')) return;
  
  // Skip API calls (they need fresh data)
  if (request.url.includes('/api/')) return;

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
          // Try cache first
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

  // For all other requests (CSS, JS, images)
  event.respondWith(
    caches.match(request).then(cachedResponse => {
      // Return cached version if available
      if (cachedResponse) {
        // Update cache in background
        fetch(request).then(response => {
          if (response.status === 200) {
            caches.open(RUNTIME_CACHE).then(cache => {
              cache.put(request, response);
            });
          }
        }).catch(() => {});
        
        return cachedResponse;
      }
      
      // Fetch from network
      return fetch(request).then(response => {
        // Cache successful responses
        if (response.status === 200 && !request.url.includes('/sanctum/')) {
          const responseToCache = response.clone();
          caches.open(RUNTIME_CACHE).then(cache => {
            cache.put(request, responseToCache);
          });
        }
        return response;
      });
    })
  );
});

// Create offline page HTML
function createOfflinePage() {
  return `
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Offline - THW Trainer</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #003d7a 0%, #0052a3 100%);
                color: white;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                text-align: center;
                max-width: 500px;
            }
            .icon {
                font-size: 80px;
                margin-bottom: 20px;
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
            h1 {
                font-size: 32px;
                margin-bottom: 16px;
            }
            p {
                font-size: 18px;
                margin-bottom: 24px;
                opacity: 0.9;
            }
            .btn {
                background: white;
                color: #003d7a;
                padding: 12px 32px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: bold;
                display: inline-block;
                transition: transform 0.2s;
                border: none;
                cursor: pointer;
                font-size: 16px;
            }
            .btn:hover {
                transform: scale(1.05);
            }
            .info {
                margin-top: 40px;
                font-size: 14px;
                opacity: 0.8;
            }
            .status {
                margin-top: 20px;
                padding: 12px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                font-size: 14px;
            }
            .online { color: #4ade80; }
            .offline { color: #fbbf24; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="icon">📴</div>
            <h1>Du bist offline</h1>
            <p>Diese Seite ist nicht im Cache verfügbar. Bitte stelle eine Internetverbindung her, um fortzufahren.</p>
            <button class="btn" onclick="location.reload()">Erneut versuchen</button>
            <div class="status">
                Status: <span id="connectionStatus" class="offline">Offline</span>
            </div>
            <div class="info">
                💡 Tipp: Besuche Seiten online, damit sie offline verfügbar werden.
            </div>
        </div>
        <script>
            function updateStatus() {
                const statusEl = document.getElementById('connectionStatus');
                if (navigator.onLine) {
                    statusEl.textContent = 'Online';
                    statusEl.className = 'online';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    statusEl.textContent = 'Offline';
                    statusEl.className = 'offline';
                }
            }
            
            // Check connection status
            updateStatus();
            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);
        </script>
    </body>
    </html>
  `;
}

// Background Sync (optional - für später)
self.addEventListener('sync', event => {
  if (event.tag === 'sync-answers') {
    event.waitUntil(syncPendingData());
  }
});

async function syncPendingData() {
  console.log('[SW] Syncing pending data...');
  // Implementierung für später
}
