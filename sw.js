const CACHE_NAME = 'ironclick-v1';
const ASSETS = [
    '/assets/css/app.css',
    '/assets/js/app.js',
    '/assets/js/search.js',
    '/assets/js/mensajes.js',
    '/assets/js/charts.js',
    '/assets/img/logo/logo-full.svg',
    '/assets/img/logo/logo-icon.svg',
];

// Install
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
    );
    self.skipWaiting();
});

// Activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Fetch - Network first, fallback to cache
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    // Skip API calls
    if (event.request.url.includes('/api/')) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                return response;
            })
            .catch(() => caches.match(event.request))
    );
});

// Background sync for offline actions
self.addEventListener('sync', event => {
    if (event.tag === 'sync-actions') {
        event.waitUntil(syncPendingActions());
    }
});

async function syncPendingActions() {
    // TODO: implement offline action queue sync
}
