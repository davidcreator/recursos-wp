/**
 * PWA functionality for NosfirNews
 * 
 * @package NosfirNews
 * @since 2.0.0
 */

(function($) {
    'use strict';

    // PWA Manager
    const PWAManager = {
        deferredPrompt: null,
        isInstalled: false,
        swRegistration: null,

        init: function() {
            this.registerServiceWorker();
            this.setupInstallPrompt();
            this.setupPushNotifications();
            this.handleOfflineStatus();
            this.setupUpdateNotification();
            this.initInstallButton();
        },

        // Register Service Worker
        registerServiceWorker: function() {
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/wp-content/themes/nosfirnews/sw.js')
                        .then(registration => {
                            console.log('Service Worker registered successfully:', registration);
                            this.swRegistration = registration;
                            
                            // Check for updates
                            registration.addEventListener('updatefound', () => {
                                this.handleServiceWorkerUpdate(registration);
                            });
                        })
                        .catch(error => {
                            console.error('Service Worker registration failed:', error);
                        });
                });
            }
        },

        // Handle Service Worker updates
        handleServiceWorkerUpdate: function(registration) {
            const newWorker = registration.installing;
            
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    this.showUpdateNotification();
                }
            });
        },

        // Show update notification
        showUpdateNotification: function() {
            const updateBanner = $(`
                <div class="pwa-update-banner" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    background: #2196F3;
                    color: white;
                    padding: 15px;
                    text-align: center;
                    z-index: 9999;
                    transform: translateY(-100%);
                    transition: transform 0.3s ease;
                ">
                    <span>Nova versão disponível!</span>
                    <button class="update-btn" style="
                        background: white;
                        color: #2196F3;
                        border: none;
                        padding: 8px 16px;
                        margin-left: 15px;
                        border-radius: 4px;
                        cursor: pointer;
                    ">Atualizar</button>
                    <button class="dismiss-btn" style="
                        background: transparent;
                        color: white;
                        border: 1px solid white;
                        padding: 8px 16px;
                        margin-left: 10px;
                        border-radius: 4px;
                        cursor: pointer;
                    ">Depois</button>
                </div>
            `);

            $('body').prepend(updateBanner);
            
            setTimeout(() => {
                updateBanner.css('transform', 'translateY(0)');
            }, 100);

            updateBanner.find('.update-btn').on('click', () => {
                this.updateServiceWorker();
                updateBanner.remove();
            });

            updateBanner.find('.dismiss-btn').on('click', () => {
                updateBanner.css('transform', 'translateY(-100%)');
                setTimeout(() => updateBanner.remove(), 300);
            });
        },

        // Update Service Worker
        updateServiceWorker: function() {
            if (this.swRegistration && this.swRegistration.waiting) {
                this.swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
                window.location.reload();
            }
        },

        // Setup install prompt
        setupInstallPrompt: function() {
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                this.showInstallButton();
            });

            window.addEventListener('appinstalled', () => {
                console.log('PWA was installed');
                this.isInstalled = true;
                this.hideInstallButton();
                this.trackInstallation();
            });
        },

        // Initialize install button
        initInstallButton: function() {
            // Check if already installed
            if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
                this.isInstalled = true;
                return;
            }

            // Check if running as PWA
            if (window.navigator.standalone === true) {
                this.isInstalled = true;
                return;
            }

            // Add install button to header if not installed
            if (!this.isInstalled) {
                this.addInstallButtonToHeader();
            }
        },

        // Add install button to header
        addInstallButtonToHeader: function() {
            const installButton = $(`
                <button class="pwa-install-btn" style="display: none;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                    </svg>
                    <span>Instalar App</span>
                </button>
            `);

            // Try to add to navigation or header
            const nav = $('.main-navigation .nav-menu');
            const header = $('.site-header');
            
            if (nav.length) {
                nav.append(`<li class="menu-item pwa-install-item">${installButton[0].outerHTML}</li>`);
            } else if (header.length) {
                header.append(installButton);
            }

            // Style the button
            this.styleInstallButton();
        },

        // Style install button
        styleInstallButton: function() {
            const styles = `
                <style>
                .pwa-install-btn {
                    background: var(--primary-color, #2196F3);
                    color: white;
                    border: none;
                    padding: 10px 15px;
                    border-radius: 25px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 14px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
                }
                
                .pwa-install-btn:hover {
                    background: var(--primary-dark, #1976D2);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
                }
                
                .pwa-install-item {
                    margin-left: auto;
                }
                
                @media (max-width: 768px) {
                    .pwa-install-btn {
                        padding: 8px 12px;
                        font-size: 12px;
                    }
                }
                </style>
            `;
            
            if (!$('#pwa-install-styles').length) {
                $('head').append(styles);
            }
        },

        // Show install button
        showInstallButton: function() {
            $('.pwa-install-btn').show().on('click', (e) => {
                e.preventDefault();
                this.promptInstall();
            });
        },

        // Hide install button
        hideInstallButton: function() {
            $('.pwa-install-btn').hide();
        },

        // Prompt installation
        promptInstall: function() {
            if (this.deferredPrompt) {
                this.deferredPrompt.prompt();
                
                this.deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                        this.trackInstallPromptAccepted();
                    } else {
                        console.log('User dismissed the install prompt');
                        this.trackInstallPromptDismissed();
                    }
                    this.deferredPrompt = null;
                });
            }
        },

        // Setup push notifications
        setupPushNotifications: function() {
            if ('Notification' in window && 'serviceWorker' in navigator) {
                this.addNotificationButton();
            }
        },

        // Add notification permission button
        addNotificationButton: function() {
            if (Notification.permission === 'default') {
                const notifyButton = $(`
                    <div class="pwa-notification-prompt" style="
                        position: fixed;
                        bottom: 20px;
                        right: 20px;
                        background: white;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                        max-width: 300px;
                        z-index: 1000;
                        display: none;
                    ">
                        <h4 style="margin: 0 0 10px 0; font-size: 16px;">Receber Notificações</h4>
                        <p style="margin: 0 0 15px 0; font-size: 14px; color: #666;">
                            Quer ser notificado sobre as últimas notícias?
                        </p>
                        <div style="display: flex; gap: 10px;">
                            <button class="allow-notifications" style="
                                background: #4CAF50;
                                color: white;
                                border: none;
                                padding: 8px 16px;
                                border-radius: 4px;
                                cursor: pointer;
                                flex: 1;
                            ">Permitir</button>
                            <button class="deny-notifications" style="
                                background: #f44336;
                                color: white;
                                border: none;
                                padding: 8px 16px;
                                border-radius: 4px;
                                cursor: pointer;
                                flex: 1;
                            ">Não</button>
                        </div>
                    </div>
                `);

                $('body').append(notifyButton);

                // Show after 5 seconds
                setTimeout(() => {
                    notifyButton.fadeIn();
                }, 5000);

                notifyButton.find('.allow-notifications').on('click', () => {
                    this.requestNotificationPermission();
                    notifyButton.fadeOut();
                });

                notifyButton.find('.deny-notifications').on('click', () => {
                    notifyButton.fadeOut();
                    localStorage.setItem('pwa-notifications-denied', 'true');
                });
            }
        },

        // Request notification permission
        requestNotificationPermission: function() {
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    console.log('Notification permission granted');
                    this.subscribeToPushNotifications();
                }
            });
        },

        // Subscribe to push notifications
        subscribeToPushNotifications: function() {
            if (this.swRegistration) {
                // Check if VAPID key is configured
                const vapidKey = window.nosfirNewsConfig?.vapidPublicKey;
                if (!vapidKey) {
                    console.warn('VAPID public key not configured. Push notifications disabled.');
                    return;
                }

                this.swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(vapidKey)
                }).then((subscription) => {
                    console.log('Push subscription successful:', subscription);
                    this.sendSubscriptionToServer(subscription);
                }).catch((error) => {
                    console.error('Push subscription failed:', error);
                });
            }
        },

        // Send subscription to server
        sendSubscriptionToServer: function(subscription) {
            fetch('/wp-json/nosfirnews/v1/push-subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(subscription)
            }).then(response => {
                if (response.ok) {
                    console.log('Subscription sent to server successfully');
                }
            }).catch(error => {
                console.error('Failed to send subscription to server:', error);
            });
        },

        // Handle offline status
        handleOfflineStatus: function() {
            const showOfflineMessage = () => {
                if (!$('.offline-message').length) {
                    const offlineMsg = $(`
                        <div class="offline-message" style="
                            position: fixed;
                            top: 0;
                            left: 0;
                            right: 0;
                            background: #ff9800;
                            color: white;
                            padding: 10px;
                            text-align: center;
                            z-index: 9999;
                        ">
                            <span>Você está offline. Algumas funcionalidades podem estar limitadas.</span>
                        </div>
                    `);
                    $('body').prepend(offlineMsg);
                }
            };

            const hideOfflineMessage = () => {
                $('.offline-message').remove();
            };

            window.addEventListener('online', hideOfflineMessage);
            window.addEventListener('offline', showOfflineMessage);

            // Check initial status
            if (!navigator.onLine) {
                showOfflineMessage();
            }
        },

        // Utility function for VAPID key conversion
        urlBase64ToUint8Array: function(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        },

        // Analytics tracking
        trackInstallation: function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'pwa_installed', {
                    event_category: 'PWA',
                    event_label: 'App Installed'
                });
            }
        },

        trackInstallPromptAccepted: function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'pwa_install_prompt_accepted', {
                    event_category: 'PWA',
                    event_label: 'Install Prompt Accepted'
                });
            }
        },

        trackInstallPromptDismissed: function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'pwa_install_prompt_dismissed', {
                    event_category: 'PWA',
                    event_label: 'Install Prompt Dismissed'
                });
            }
        }
    };

    // Initialize PWA when document is ready
    $(document).ready(function() {
        PWAManager.init();
    });

    // Expose PWAManager globally for debugging
    window.NosfirNewsPWA = PWAManager;

})(jQuery);