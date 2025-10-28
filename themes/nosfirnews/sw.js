/**
 * Service Worker Otimizado - NosfirNews PWA
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
    '/offline/',
    '/manifest.json'
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

// Install Event - Cachear assets estáticos
self.addEventListener('install', event => {
    console.log('[SW] Installing...');
    
    event.waitUntil(
        (async () => {
            try {
                const cache = await caches.open(CACHES.static);
                await cache.addAll(STATIC_ASSETS);
                console.log('[SW] Static assets cached');
                
                // Ativar imediatamente
                await self.skipWaiting();
            } catch (error) {
                console.error('[SW] Install failed:', error);
            }
        })()
    );
});

// Activate Event - Limpar caches antigos
self.addEventListener('activate', event => {
    console.log('[SW] Activating...');
    
    event.waitUntil(
        (async () => {
            try {
                // Limpar caches antigos
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
                
                // Tomar controle imediato
                await self.clients.claim();
                
                console.log('[SW] Activated successfully');
            } catch (error) {
                console.error('[SW] Activation failed:', error);
            }
        })()
    );
});

// Fetch Event - Estratégias de cache
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Ignorar requisições não-GET
    if (request.method !== 'GET') return;
    
    // Ignorar admin e login
    if (url.pathname.includes('/wp-admin/') || 
        url.pathname.includes('/wp-login.php')) {
        return;
    }
    
    // Escolher estratégia baseada no tipo de recurso
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

// Estratégia: Cache First
async function cacheFirst(request, cacheName) {
    try {
        // Tentar buscar do cache
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            // Verificar se não expirou
            if (!hasExpired(cachedResponse, cacheName)) {
                return cachedResponse;
            }
        }
        
        // Buscar da rede
        const networkResponse = await fetchWithTimeout(request);
        
        // Cachear se sucesso
        if (networkResponse && networkResponse.status === 200) {
            await cacheResponse(cacheName, request, networkResponse.clone());
        }
        
        return networkResponse;
        
    } catch (error) {
        // Se falhar, tentar cache mesmo expirado
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Retornar fallback
        return getFallbackResponse(request);
    }
}

// Estratégia: Network First
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

// Estratégia: Stale While Revalidate
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

// Fetch com timeout
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

// Cachear resposta com gerenciamento de tamanho
async function cacheResponse(cacheName, request, response) {
    try {
        const cache = await caches.open(cacheName);
        
        // Adicionar timestamp para controle de expiração
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
        
        // Limitar tamanho do cache
        await limitCacheSize(cacheName);
        
    } catch (error) {
        console.error('[SW] Error caching response:', error);
    }
}

// Limitar tamanho do cache
async function limitCacheSize(cacheName) {
    const maxSize = getMaxCacheSize(cacheName);
    if (!maxSize) return;
    
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    
    if (keys.length > maxSize) {
        // Remover os mais antigos
        const toDelete = keys.length - maxSize;
        for (let i = 0; i < toDelete; i++) {
            await cache.delete(keys[i]);
        }
        console.log(`[SW] Removed ${toDelete} old entries from ${cacheName}`);
    }
}

// Obter tamanho máximo do cache
function getMaxCacheSize(cacheName) {
    if (cacheName === CACHES.dynamic) return CONFIG.maxDynamicCacheSize;
    if (cacheName === CACHES.images) return CONFIG.maxImageCacheSize;
    return null;
}

// Verificar se resposta expirou
function hasExpired(response, cacheName) {
    const cachedDate = response.headers.get('sw-cached-date');
    if (!cachedDate) return false;
    
    const age = Date.now() - parseInt(cachedDate);
    const maxAge = getCacheDuration(cacheName);
    
    return age > maxAge;
}

// Obter duração do cache
function getCacheDuration(cacheName) {
    if (cacheName === CACHES.static) return CONFIG.cacheDuration.static;
    if (cacheName === CACHES.dynamic) return CONFIG.cacheDuration.dynamic;
    if (cacheName === CACHES.images) return CONFIG.cacheDuration.images;
    return CONFIG.cacheDuration.dynamic;
}

// Resposta fallback
async function getFallbackResponse(request) {
    if (isDocument(request)) {
        const offlineResponse = await caches.match('/offline/');
        if (offlineResponse) return offlineResponse;
    }
    
    // Resposta genérica de erro
    return new Response('Offline', {
        status: 503,
        statusText: 'Service Unavailable',
        headers: new Headers({
            'Content-Type': 'text/plain'
        })
    });
}

// Verificar tipos de recursos
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

// Background Sync
self.addEventListener('sync', event => {
    console.log('[SW] Background sync:', event.tag);
    
    if (event.tag === 'sync-data') {
        event.waitUntil(syncData());
    }
});

async function syncData() {
    try {
        // Sincronizar dados pendentes
        const pendingData = await getPendingData();
        
        for (const data of pendingData) {
            try {
                await fetch(data.url, {
                    method: data.method,
                    body: JSON.stringify(data.payload),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                await removePendingData(data.id);
            } catch (error) {
                console.error('[SW] Failed to sync data:', error);
            }
        }
    } catch (error) {
        console.error('[SW] Background sync failed:', error);
    }
}

// Push Notifications
self.addEventListener('push', event => {
    console.log('[SW] Push notification received');
    
    let notificationData = {
        title: 'NosfirNews',
        body: 'Nova notícia disponível!',
        icon: '/wp-content/themes/nosfirnews/assets/images/icons/icon-192x192.png',
        badge: '/wp-content/themes/nosfirnews/assets/images/icons/badge-72x72.png',
        data: {
            url: '/'
        }
    };
    
    if (event.data) {
        try {
            const data = event.data.json();
            notificationData = { ...notificationData, ...data };
        } catch (error) {
            console.error('[SW] Error parsing push data:', error);
        }
    }
    
    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            vibrate: [200, 100, 200],
            data: notificationData.data,
            actions: [
                {
                    action: 'open',
                    title: 'Abrir',
                    icon: notificationData.icon
                },
                {
                    action: 'close',
                    title: 'Fechar'
                }
            ]
        })
    );
});

// Notification Click
self.addEventListener('notificationclick', event => {
    console.log('[SW] Notification clicked:', event.action);
    
    event.notification.close();
    
    if (event.action === 'close') {
        return;
    }
    
    const urlToOpen = event.notification.data?.url || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(windowClients => {
                // Verificar se já existe uma janela aberta
                for (const client of windowClients) {
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Abrir nova janela
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Message Handler
self.addEventListener('message', event => {
    console.log('[SW] Message received:', event.data);
    
    if (event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data.type === 'CACHE_URLS') {
        event.waitUntil(
            cacheUrls(event.data.urls)
        );
    }
    
    if (event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            clearAllCaches()
        );
    }
    
    if (event.data.type === 'GET_CACHE_SIZE') {
        event.waitUntil(
            getCacheSize().then(size => {
                event.ports[0].postMessage({ size });
            })
        );
    }
});

// Cachear URLs específicas
async function cacheUrls(urls) {
    try {
        const cache = await caches.open(CACHES.dynamic);
        await cache.addAll(urls);
        console.log('[SW] URLs cached successfully');
    } catch (error) {
        console.error('[SW] Error caching URLs:', error);
    }
}

// Limpar todos os caches
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

// Obter tamanho total do cache
async function getCacheSize() {
    let totalSize = 0;
    
    try {
        const cacheNames = await caches.keys();
        
        for (const cacheName of cacheNames) {
            const cache = await caches.open(cacheName);
            const keys = await cache.keys();
            
            for (const request of keys) {
                const response = await cache.match(request);
                if (response) {
                    const blob = await response.blob();
                    totalSize += blob.size;
                }
            }
        }
    } catch (error) {
        console.error('[SW] Error calculating cache size:', error);
    }
    
    return totalSize;
}

// Periodic Background Sync (experimental)
self.addEventListener('periodicsync', event => {
    console.log('[SW] Periodic sync:', event.tag);
    
    if (event.tag === 'content-sync') {
        event.waitUntil(syncLatestContent());
    }
});

async function syncLatestContent() {
    try {
        const response = await fetch('/wp-json/wp/v2/posts?per_page=5');
        
        if (!response.ok) {
            throw new Error('Failed to fetch latest posts');
        }
        
        const posts = await response.json();
        const cache = await caches.open(CACHES.dynamic);
        
        // Cachear os últimos posts
        for (const post of posts) {
            try {
                await cache.add(post.link);
            } catch (error) {
                console.error('[SW] Error caching post:', error);
            }
        }
        
        console.log('[SW] Latest content synced');
    } catch (error) {
        console.error('[SW] Content sync failed:', error);
    }
}

// Helpers para IndexedDB (simplificado)
async function getPendingData() {
    // Implementação simplificada - em produção usar IndexedDB
    return [];
}

async function removePendingData(id) {
    // Implementação simplificada - em produção usar IndexedDB
    console.log('[SW] Removing pending data:', id);
}

// Analytics offline (opcional)
function trackOfflineUsage(request) {
    // Implementar tracking de uso offline se necessário
    console.log('[SW] Offline request:', request.url);
}

// Log de performance
console.log('[SW] Service Worker loaded successfully');
console.log('[SW] Cache version:', CACHE_VERSION);
console.log('[SW] Available caches:', Object.keys(CACHES));