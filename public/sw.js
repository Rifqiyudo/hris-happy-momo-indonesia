const CACHE_NAME = "happy-momo-v1";
const ASSETS = [
    "/",
    "/login",
    "/manifest.json",
    "/build/assets/app.css",
    "/build/assets/app.js",
];

// Instalasi & Caching Assets
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS)),
    );
});

// Fetching: Mengambil dari cache jika offline
self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        }),
    );
});
