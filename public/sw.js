const CACHE_NAME = 'thw-trainer-v1';
const RUNTIME_CACHE = 'thw-trainer-runtime-v1';

// Assets to cache on install
const PRECACHE_ASSETS = [
  '/dashboard',
  '/practice',
  '/exam',
  '/logo-thwtrainer.png',
  '/logo-thwtrainer_w.png',
  '/manifest.json'
];

// Install event - precache assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(PRECACHE_ASSETS))
      .then(() => self.skipWaiting())
  );
});

// Activate event - cleanup old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames
          .filter(cacheName => cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE)
          .map(cacheName => caches.delete(cacheName))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event - network first, fallback to cache
self.addEventListener('fetch', event => {
  const { request } = event;
  
  // Skip non-GET requests
  if (request.method !== 'GET') return;
  
  // Skip chrome extensions and other non-http(s) requests
  if (!request.url.startsWith('http')) return;

  event.respondWith(
    fetch(request)
      .then(response => {
        // Clone the response
        const responseToCache = response.clone();
        
        // Cache successful responses
        if (response.status === 200) {
          caches.open(RUNTIME_CACHE).then(cache => {
            cache.put(request, responseToCache);
          });
        }
        
        return response;
      })
      .catch(() => {
        // Network failed, try cache
        return caches.match(request).then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          
          // Return offline page for navigation requests
          if (request.mode === 'navigate') {
            return caches.match('/dashboard');
          }
          
          return new Response('Offline', {
            status: 503,
            statusText: 'Service Unavailable'
          });
        });
      })
  );
});

// Background Sync for failed requests
self.addEventListener('sync', event => {
  if (event.tag === 'sync-answers') {
    event.waitUntil(syncAnswers());
  }
});

async function syncAnswers() {
  try {
    // Get pending answers from IndexedDB
    const db = await openDB();
    const tx = db.transaction('pending-answers', 'readonly');
    const store = tx.objectStore('pending-answers');
    const pendingAnswers = await store.getAll();
    
    // Send each answer to the server
    for (const answer of pendingAnswers) {
      try {
        const response = await fetch('/api/sync-answer', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': answer.csrf
          },
          body: JSON.stringify(answer.data)
        });
        
        if (response.ok) {
          // Remove from IndexedDB if successful
          const deleteTx = db.transaction('pending-answers', 'readwrite');
          await deleteTx.objectStore('pending-answers').delete(answer.id);
        }
      } catch (error) {
        console.error('Failed to sync answer:', error);
      }
    }
  } catch (error) {
    console.error('Background sync failed:', error);
  }
}

function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('thw-trainer-db', 1);
    
    request.onerror = () => reject(request.error);
    request.onsuccess = () => resolve(request.result);
    
    request.onupgradeneeded = event => {
      const db = event.target.result;
      if (!db.objectStoreNames.contains('pending-answers')) {
        db.createObjectStore('pending-answers', { keyPath: 'id', autoIncrement: true });
      }
    };
  });
}
