const CACHE_NAME = 'datacollect-v1';
const STATIC_CACHE_URLS = ['/', '/offline', '/manifest.json'];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_CACHE_URLS))
  );
  self.skipWaiting();
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(cached => {
      if (cached) return cached;
      return fetch(event.request).catch(() => {
        if (event.request.mode === 'navigate') return caches.match('/offline');
        return new Response('Hors-ligne', { status: 503 });
      });
    })
  );
});
