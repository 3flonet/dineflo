const CACHE_NAME = 'dineflo-v2';
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/logo.png',
    '/pwa-192x192.png',
    '/pwa-512x512.png'
];

// Pre-load essential assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[SW] Pre-caching static assets');
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate: clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clientsClaim();
});

// Fetch Strategy
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // 1. Skip non-GET and internal Laravel/Livewire paths that need live connection
    if (request.method !== 'GET' || url.pathname.includes('/livewire/') || url.pathname.includes('/filament/')) {
        return;
    }

    // 2. Specialized Strategy for Static Assets (Images, Fonts, CSS, JS)
    // Strategy for JS/CSS: Stale-While-Revalidate
    if (url.pathname.endsWith('.js') || url.pathname.endsWith('.css') || url.pathname.includes('/build/assets/')) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse.clone()));
                    return networkResponse;
                });
                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // Strategy for Images/Fonts: Cache First, Fallback to Network
    if (request.destination === 'image' || request.destination === 'font') {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) return cachedResponse;
                
                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const cacheCopy = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, cacheCopy));
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // 3. Strategy for Navigation (POS Page, etc.)
    // Strategy: Network First, Fallback to Cache, then Offline Page
    event.respondWith(
        fetch(request)
            .then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    const cacheCopy = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, cacheCopy));
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(request).then((cachedResponse) => {
                    if (cachedResponse) return cachedResponse;
                    if (request.mode === 'navigate') {
                        return caches.match('/offline.html');
                    }
                    return Response.error();
                });
            })
    );
});

// WebPush Notifications (Existing)
self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    if (e.data) {
        try {
            var msg = e.data.json();
            e.waitUntil(self.registration.showNotification(msg.title, {
                body: msg.body,
                icon: msg.icon || '/logo.png',
                badge: msg.badge || '/logo.png',
                data: msg.data || null,
                actions: msg.actions || []
            }));
        } catch (err) {
            console.error('Push error:', err);
        }
    }
});

self.addEventListener('notificationclick', function (e) {
    e.notification.close();
    
    if (e.notification.data && e.notification.data.url) {
        e.waitUntil(clients.openWindow(e.notification.data.url));
    } else {
        e.waitUntil(clients.matchAll({ type: 'window' }).then(windowClients => {
            for (var i = 0; i < windowClients.length; i++) {
                var client = windowClients[i];
                if (client.url === '/' && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow('/');
            }
        }));
    }
});
