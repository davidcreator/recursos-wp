/**
 * Service Worker for NosfirNews PWA
 * 
 * @package NosfirNews
 * @since 2.0.0
 */

const CACHE_NAME = 'nosfirnews-v2.0.0';
const OFFLINE_URL = '/offline/';

// Assets to cache on install
const STATIC_CACHE_URLS = [
    '/',
    '/offline/',
    '/wp-content/themes/nosfirnews/assets/css/main.css',
    '/wp-content/themes/nosfirnews/assets/css/responsive.css',
    '/wp-content/themes/nosfirnews/assets/css/navigation-enhanced.css',
    '/wp-content/themes/nosfirnews/assets/js/main.js',
    '/wp-content/themes/nosfirnews/assets/js/pwa.js',
    '/wp-content/themes/nosfirnews/assets/images/icons/icon-192x192.svg',
    '/wp-content/themes/nosfirnews/assets/images/icons/icon-512x512.svg',
    '/wp-content/themes/nosfirnews/assets/images/icons/icon-144x144.svg',
    '/wp-content/themes/nosfirnews/assets/images/icons/badge-72x72.svg',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('Service Worker installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Caching static assets');
                return cache.addAll(STATIC_CACHE_URLS);
            })
            .then(() => {
                console.log('Static assets cached successfully');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('Failed to cache static assets:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker activating...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== CACHE_NAME) {
                            console.log('Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Skip requests to wp-admin and wp-login
    if (event.request.url.includes('/wp-admin/') || 
        event.request.url.includes('/wp-login.php')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                // Return cached version if available
                if (cachedResponse) {
                    return cachedResponse;
                }

                // Clone the request for fetch
                const fetchRequest = event.request.clone();

                return fetch(fetchRequest)
                    .then(response => {
                        // Check if valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Clone the response for caching
                        const responseToCache = response.clone();

                        // Cache strategy based on request type
                        if (shouldCache(event.request)) {
                            caches.open(CACHE_NAME)
                                .then(cache => {
                                    cache.put(event.request, responseToCache);
                                });
                        }

                        return response;
                    })
                    .catch(error => {
                        console.log('Fetch failed, serving offline page:', error);
                        
                        // Serve offline page for navigation requests
                        if (event.request.mode === 'navigate') {
                            return caches.match(OFFLINE_URL);
                        }
                        
                        // For other requests, try to serve a cached version
                        return caches.match(event.request);
                    });
            })
    );
});

// Determine if request should be cached
function shouldCache(request) {
    const url = new URL(request.url);
    
    // Cache images
    if (request.destination === 'image') {
        return true;
    }
    
    // Cache CSS and JS files
    if (url.pathname.endsWith('.css') || 
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.woff') ||
        url.pathname.endsWith('.woff2')) {
        return true;
    }
    
    // Cache HTML pages (but limit to avoid filling storage)
    if (request.mode === 'navigate' && !url.pathname.includes('/wp-admin/')) {
        return true;
    }
    
    return false;
}

// Background sync for offline actions
self.addEventListener('sync', event => {
    console.log('Background sync triggered:', event.tag);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

async function doBackgroundSync() {
    try {
        // Get pending actions from IndexedDB
        const pendingActions = await getPendingActions();
        
        for (const action of pendingActions) {
            try {
                await processAction(action);
                await removePendingAction(action.id);
            } catch (error) {
                console.error('Failed to process action:', error);
            }
        }
    } catch (error) {
        console.error('Background sync failed:', error);
    }
}

// Push notification handling
self.addEventListener('push', event => {
    console.log('Push notification received');
    
    const options = {
        body: 'Nova notícia disponível no NosfirNews!',
        icon: '/wp-content/themes/nosfirnews/assets/images/icons/icon-192x192.png',
        badge: '/wp-content/themes/nosfirnews/assets/images/icons/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Ver Notícia',
                icon: '/wp-content/themes/nosfirnews/assets/images/icons/view-icon.png'
            },
            {
                action: 'close',
                title: 'Fechar',
                icon: '/wp-content/themes/nosfirnews/assets/images/icons/close-icon.png'
            }
        ]
    };

    if (event.data) {
        const data = event.data.json();
        options.body = data.body || options.body;
        options.data.url = data.url || '/';
    }

    event.waitUntil(
        self.registration.showNotification('NosfirNews', options)
    );
});

// Notification click handling
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event.action);
    
    event.notification.close();

    if (event.action === 'explore') {
        const url = event.notification.data.url || '/';
        event.waitUntil(
            clients.openWindow(url)
        );
    } else if (event.action === 'close') {
        // Just close the notification
        return;
    } else {
        // Default action - open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message handling from main thread
self.addEventListener('message', event => {
    console.log('Service Worker received message:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CACHE_URLS') {
        event.waitUntil(
            caches.open(CACHE_NAME)
                .then(cache => {
                    return cache.addAll(event.data.urls);
                })
        );
    }
});

// Utility functions for IndexedDB operations
async function getPendingActions() {
    // Implementation would depend on your IndexedDB structure
    return [];
}

async function processAction(action) {
    // Process the pending action (e.g., submit form, send comment)
    console.log('Processing action:', action);
}

async function removePendingAction(actionId) {
    // Remove the action from IndexedDB after successful processing
    console.log('Removing action:', actionId);
}

// Cache management
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        return caches.delete(cacheName);
                    })
                );
            })
        );
    }
});

// Periodic background sync (if supported)
self.addEventListener('periodicsync', event => {
    if (event.tag === 'content-sync') {
        event.waitUntil(syncContent());
    }
});

async function syncContent() {
    try {
        // Sync latest content in background
        const response = await fetch('/wp-json/wp/v2/posts?per_page=5');
        if (response.ok) {
            const posts = await response.json();
            // Cache the latest posts
            const cache = await caches.open(CACHE_NAME);
            posts.forEach(post => {
                cache.add(post.link);
            });
        }
    } catch (error) {
        console.error('Content sync failed:', error);
    }
}