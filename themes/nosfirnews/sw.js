/**
 * Service Worker Simplificado - NosfirNews
 * @package NosfirNews
 * @since 2.0.0
 */

const CACHE_VERSION = 'nosfirnews-v2.0.0';
const CACHE_PREFIX = 'nosfirnews';

const CACHES = {
    static: `${CACHE_PREFIX}-static-${CACHE_VERSION}`,
    dynamic: `${CACHE_PREFIX}-dynamic-${CACHE_VERSION}`,
    images: `${CACHE_PREFIX}-images-${CACHE_VERSION}`,
    fonts: `${CACHE_PREFIX}-fonts-${CACHE_VERSION}`
};

// Assets estáticos essenciais
const STATIC_ASSETS = [
    '/',
    '/offline/'
];

// Configurações
const CONFIG = {
    maxDynamicCacheSize: 50,
    maxImageCacheSize: 60,
    cacheDuration: {
        static: 30 * 24 * 60 * 60 * 1000, // 30 dias
        dynamic: 7 * 24 * 60 * 60 * 1000,  // 7 dias
        images: 30 * 24 * 60 * 60 * 1000   // 30 dias
    },
    networkTimeoutMs: 3000
};

// Install Event
self.addEventListener('install', event => {
    console.log('[SW] Installing...');
    
    event.waitUntil(
        (async () => {
            try {
                const cache = await caches.open(CACHES.static);
                await cache.addAll(STATIC_ASSETS);
                console.log('[SW] Static assets cached');
                await self.skipWaiting();
            } catch (error) {
                console.error('[SW] Install failed:', error);
            }
        })()
    );
});

// Activate Event
self.addEventListener('activate', event => {
    console.log('[SW] Activating...');
    
    event.waitUntil(
        (async () => {
            try {
                const cacheNames = await caches.keys();
                const cachesToDelete = cacheNames.filter(cacheName => {
                    return cacheName.startsWith(CACHE_PREFIX) && 
                           !Object.values(CACHES).includes(cacheName);
                });
                
                await Promise.all(
                    cachesToDelete.map(cacheName => {
                        console.log('[SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    })
                );
                
                await self.clients.claim();
                console.log('[SW] Activated successfully');
            } catch (error) {
                console.error('[SW] Activation failed:', error);
            }
        })()
    );
});

// Fetch Event
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    if (request.method !== 'GET') return;
    
    if (url.pathname.includes('/wp-admin/') || 
        url.pathname.includes('/wp-login.php')) {
        return;
    }
    
    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(request, CACHES.static));
    } else if (isImage(url)) {
        event.respondWith(cacheFirst(request, CACHES.images));
    } else if (isFont(url)) {
        event.respondWith(cacheFirst(request, CACHES.fonts));
    } else if (isDocument(request)) {
        event.respondWith(networkFirst(request, CACHES.dynamic));
    } else {
        event.respondWith(staleWhileRevalidate(request, CACHES.dynamic));
    }
});

// Cache First Strategy
async function cacheFirst(request, cacheName) {
    try {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            if (!hasExpired(cachedResponse, cacheName)) {
                return cachedResponse;
            }
        }
        
        const networkResponse = await fetchWithTimeout(request);
        
        if (networkResponse && networkResponse.status === 200) {
            await cacheResponse(cacheName, request, networkResponse.clone());
        }
        
        return networkResponse;
        
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        return getFallbackResponse(request);
    }
}

// Network First Strategy
async function networkFirst(request, cacheName) {
    try {
        const networkResponse = await fetchWithTimeout(request);
        
        if (networkResponse && networkResponse.status === 200) {
            await cacheResponse(cacheName, request, networkResponse.clone());
        }
        
        return networkResponse;
        
    } catch (error) {
        console.log('[SW] Network failed, trying cache:', request.url);
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        return getFallbackResponse(request);
    }
}

// Stale While Revalidate Strategy
async function staleWhileRevalidate(request, cacheName) {
    const cachedResponse = await caches.match(request);
    
    const fetchPromise = (async () => {
        try {
            const networkResponse = await fetch(request);
            
            if (networkResponse && networkResponse.status === 200) {
                await cacheResponse(cacheName, request, networkResponse.clone());
            }
            
            return networkResponse;
        } catch (error) {
            return cachedResponse;
        }
    })();
    
    return cachedResponse || fetchPromise;
}

// Fetch with timeout
function fetchWithTimeout(request, timeout = CONFIG.networkTimeoutMs) {
    return new Promise((resolve, reject) => {
        const timeoutId = setTimeout(() => {
            reject(new Error('Network timeout'));
        }, timeout);
        
        fetch(request)
            .then(response => {
                clearTimeout(timeoutId);
                resolve(response);
            })
            .catch(error => {
                clearTimeout(timeoutId);
                reject(error);
            });
    });
}

// Cache response
async function cacheResponse(cacheName, request, response) {
    try {
        const cache = await caches.open(cacheName);
        
        const clonedResponse = response.clone();
        const responseBody = await clonedResponse.blob();
        
        const headers = new Headers(clonedResponse.headers);
        headers.set('sw-cached-date', Date.now().toString());
        
        const cachedResponse = new Response(responseBody, {
            status: clonedResponse.status,
            statusText: clonedResponse.statusText,
            headers: headers
        });
        
        await cache.put(request, cachedResponse);
        await limitCacheSize(cacheName);
        
    } catch (error) {
        console.error('[SW] Error caching response:', error);
    }
}

// Limit cache size
async function limitCacheSize(cacheName) {
    const maxSize = getMaxCacheSize(cacheName);
    if (!maxSize) return;
    
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    
    if (keys.length > maxSize) {
        const toDelete = keys.length - maxSize;
        for (let i = 0; i < toDelete; i++) {
            await cache.delete(keys[i]);
        }
        console.log(`[SW] Removed ${toDelete} old entries from ${cacheName}`);
    }
}

// Get max cache size
function getMaxCacheSize(cacheName) {
    if (cacheName === CACHES.dynamic) return CONFIG.maxDynamicCacheSize;
    if (cacheName === CACHES.images) return CONFIG.maxImageCacheSize;
    return null;
}

// Check if response expired
function hasExpired(response, cacheName) {
    const cachedDate = response.headers.get('sw-cached-date');
    if (!cachedDate) return false;
    
    const age = Date.now() - parseInt(cachedDate);
    const maxAge = getCacheDuration(cacheName);
    
    return age > maxAge;
}

// Get cache duration
function getCacheDuration(cacheName) {
    if (cacheName === CACHES.static) return CONFIG.cacheDuration.static;
    if (cacheName === CACHES.dynamic) return CONFIG.cacheDuration.dynamic;
    if (cacheName === CACHES.images) return CONFIG.cacheDuration.images;
    return CONFIG.cacheDuration.dynamic;
}

// Fallback response
async function getFallbackResponse(request) {
    if (isDocument(request)) {
        const offlineResponse = await caches.match('/offline/');
        if (offlineResponse) return offlineResponse;
    }
    
    return new Response('Offline', {
        status: 503,
        statusText: 'Service Unavailable',
        headers: new Headers({
            'Content-Type': 'text/plain'
        })
    });
}

// Resource type checks
function isStaticAsset(url) {
    return url.pathname.match(/\.(css|js|json)$/);
}

function isImage(url) {
    return url.pathname.match(/\.(jpg|jpeg|png|gif|svg|webp|ico)$/);
}

function isFont(url) {
    return url.pathname.match(/\.(woff|woff2|ttf|eot)$/);
}

function isDocument(request) {
    return request.headers.get('Accept').includes('text/html') ||
           request.mode === 'navigate';
}

// Message Handler
self.addEventListener('message', event => {
    console.log('[SW] Message received:', event.data);
    
    if (event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data.type === 'CACHE_URLS') {
        event.waitUntil(cacheUrls(event.data.urls));
    }
    
    if (event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(clearAllCaches());
    }
});

// Cache specific URLs
async function cacheUrls(urls) {
    try {
        const cache = await caches.open(CACHES.dynamic);
        await cache.addAll(urls);
        console.log('[SW] URLs cached successfully');
    } catch (error) {
        console.error('[SW] Error caching URLs:', error);
    }
}

// Clear all caches
async function clearAllCaches() {
    try {
        const cacheNames = await caches.keys();
        await Promise.all(
            cacheNames.map(cacheName => caches.delete(cacheName))
        );
        console.log('[SW] All caches cleared');
    } catch (error) {
        console.error('[SW] Error clearing caches:', error);
    }
}

console.log('[SW] Service Worker loaded successfully');
console.log('[SW] Cache version:', CACHE_VERSION);