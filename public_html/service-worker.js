const CACHE_NAME = 'rbxams-cache-v1';
const urlsToCache = [
    '/',
    '/index.php',
    '/css/app.css',
    '/css/styles.css',
    '/scripts/index.js',
    '/scripts/dashboard.js',
    '/scripts/accounts.js',
    '/scripts/summary.js',
    '/assets/android-chrome-192x192.png',
    '/assets/android-chrome-512x512.png',
    '/assets/apple-touch-icon.png',
    '/assets/favicon-16x16.png',
    '/assets/favicon-32x32.png',
    '/assets/favicon.ico'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Cache hit - return response
                if (response) {
                    return response;
                }
                return fetch(event.request);
            })
    );
});

self.addEventListener('activate', (event) => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});