/**
 * NosfirNews PWA Manager - Otimizado
 * @package NosfirNews
 * @since 2.0.0
 */

(function() {
    'use strict';

    class PWAManager {
        constructor(config = {}) {
            this.config = {
                swUrl: config.swUrl || '/sw.js',
                vapidPublicKey: config.vapidPublicKey || '',
                showInstallButton: config.showInstallButton !== false,
                autoUpdate: config.autoUpdate !== false,
                updateInterval: config.updateInterval || 3600000, // 1 hora
                offlineUrl: config.offlineUrl || '/offline/',
                ...config
            };

            this.state = {
                isInstalled: false,
                isUpdateAvailable: false,
                registration: null,
                deferredPrompt: null,
                isOnline: navigator.onLine
            };

            this.init();
        }

        async init() {
            if (!this.isSupported()) {
                console.warn('PWA: Service Workers não suportados neste navegador');
                return;
            }

            await this.registerServiceWorker();
            this.setupInstallPrompt();
            this.monitorConnection();
            this.setupUpdateCheck();
            this.setupNotifications();
        }

        isSupported() {
            return 'serviceWorker' in navigator && 
                   'PushManager' in window && 
                   'Notification' in window;
        }

        async registerServiceWorker() {
            try {
                // Aguardar página carregar completamente
                if (document.readyState !== 'complete') {
                    await new Promise(resolve => {
                        window.addEventListener('load', resolve, { once: true });
                    });
                }

                const registration = await navigator.serviceWorker.register(
                    this.config.swUrl,
                    { scope: '/' }
                );

                this.state.registration = registration;

                console.log('PWA: Service Worker registrado com sucesso', registration);

                // Verificar se há update disponível
                registration.addEventListener('updatefound', () => {
                    this.handleUpdateFound(registration);
                });

                // Verificar updates periodicamente
                if (this.config.autoUpdate) {
                    this.scheduleUpdateCheck();
                }

                this.emit('swRegistered', { registration });

            } catch (error) {
                console.error('PWA: Erro ao registrar Service Worker', error);
                this.emit('swError', { error });
            }
        }

        handleUpdateFound(registration) {
            const newWorker = registration.installing;
            
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    this.state.isUpdateAvailable = true;
                    this.showUpdateNotification();
                }
            });
        }

        showUpdateNotification() {
            const notification = this.createNotification(
                'Atualização disponível',
                'Uma nova versão está disponível. Recarregue a página para atualizar.',
                [
                    {
                        text: 'Atualizar',
                        callback: () => this.applyUpdate()
                    },
                    {
                        text: 'Depois',
                        callback: () => notification.remove()
                    }
                ]
            );

            this.emit('updateAvailable');
        }

        async applyUpdate() {
            if (!this.state.registration) return;

            const waiting = this.state.registration.waiting;
            if (waiting) {
                // Enviar mensagem para o SW skipWaiting
                waiting.postMessage({ type: 'SKIP_WAITING' });

                // Aguardar o novo SW estar ativo
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    window.location.reload();
                });
            }
        }

        scheduleUpdateCheck() {
            setInterval(() => {
                if (this.state.registration) {
                    this.state.registration.update();
                }
            }, this.config.updateInterval);
        }

        setupInstallPrompt() {
            // Verificar se já está instalado
            if (window.matchMedia('(display-mode: standalone)').matches) {
                this.state.isInstalled = true;
                this.emit('alreadyInstalled');
                return;
            }

            // Capturar evento beforeinstallprompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.state.deferredPrompt = e;
                
                if (this.config.showInstallButton) {
                    this.showInstallButton();
                }

                this.emit('installAvailable');
            });

            // Detectar quando app foi instalado
            window.addEventListener('appinstalled', () => {
                this.state.isInstalled = true;
                this.state.deferredPrompt = null;
                this.hideInstallButton();
                this.emit('appInstalled');
            });
        }

        async promptInstall() {
            if (!this.state.deferredPrompt) {
                console.warn('PWA: Prompt de instalação não disponível');
                return;
            }

            try {
                this.state.deferredPrompt.prompt();
                const { outcome } = await this.state.deferredPrompt.userChoice;
                
                console.log(`PWA: Usuário ${outcome} a instalação`);
                this.emit('installPromptResponse', { outcome });

                if (outcome === 'accepted') {
                    this.hideInstallButton();
                }

                this.state.deferredPrompt = null;

            } catch (error) {
                console.error('PWA: Erro no prompt de instalação', error);
            }
        }

        showInstallButton() {
            let button = document.getElementById('pwa-install-button');
            
            if (!button) {
                button = document.createElement('button');
                button.id = 'pwa-install-button';
                button.className = 'pwa-install-button';
                button.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Instalar App</span>
                `;
                button.setAttribute('aria-label', 'Instalar aplicativo');
                
                button.addEventListener('click', () => this.promptInstall());
                
                // Adicionar ao DOM
                document.body.appendChild(button);

                // Animar entrada
                requestAnimationFrame(() => {
                    button.classList.add('visible');
                });
            }

            button.style.display = 'flex';
        }

        hideInstallButton() {
            const button = document.getElementById('pwa-install-button');
            if (button) {
                button.classList.remove('visible');
                setTimeout(() => {
                    button.style.display = 'none';
                }, 300);
            }
        }

        monitorConnection() {
            // Status inicial
            this.updateConnectionStatus();

            // Monitorar mudanças
            window.addEventListener('online', () => {
                this.state.isOnline = true;
                this.updateConnectionStatus();
                this.emit('online');
                this.syncWhenOnline();
            });

            window.addEventListener('offline', () => {
                this.state.isOnline = false;
                this.updateConnectionStatus();
                this.emit('offline');
            });
        }

        updateConnectionStatus() {
            let indicator = document.getElementById('connection-status');
            
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'connection-status';
                indicator.className = 'connection-status';
                document.body.appendChild(indicator);
            }

            if (this.state.isOnline) {
                indicator.textContent = 'Conectado';
                indicator.className = 'connection-status online';
                
                // Auto-ocultar após 3 segundos
                setTimeout(() => {
                    indicator.classList.add('hidden');
                }, 3000);
            } else {
                indicator.textContent = 'Sem conexão';
                indicator.className = 'connection-status offline';
                indicator.classList.remove('hidden');
            }
        }

        async syncWhenOnline() {
            if (!this.state.registration || !this.state.registration.sync) {
                return;
            }

            try {
                await this.state.registration.sync.register('background-sync');
                console.log('PWA: Background sync registrado');
            } catch (error) {
                console.error('PWA: Erro ao registrar background sync', error);
            }
        }

        async setupNotifications() {
            if (!('Notification' in window)) {
                console.warn('PWA: Notificações não suportadas');
                return;
            }

            // Verificar permissão atual
            if (Notification.permission === 'granted') {
                await this.subscribeToPush();
            }
        }

        async requestNotificationPermission() {
            try {
                const permission = await Notification.requestPermission();
                
                if (permission === 'granted') {
                    await this.subscribeToPush();
                    this.emit('notificationPermissionGranted');
                    return true;
                }

                this.emit('notificationPermissionDenied');
                return false;

            } catch (error) {
                console.error('PWA: Erro ao solicitar permissão de notificação', error);
                return false;
            }
        }

        async subscribeToPush() {
            if (!this.state.registration || !this.config.vapidPublicKey) {
                return;
            }

            try {
                const subscription = await this.state.registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(this.config.vapidPublicKey)
                });

                // Enviar subscription para o servidor
                await this.sendSubscriptionToServer(subscription);

                this.emit('pushSubscribed', { subscription });
                
            } catch (error) {
                console.error('PWA: Erro ao fazer subscribe de push', error);
            }
        }

        async sendSubscriptionToServer(subscription) {
            try {
                const response = await fetch('/wp-json/nosfirnews/v1/push-subscription', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(subscription)
                });

                if (!response.ok) {
                    throw new Error('Falha ao enviar subscription');
                }

                console.log('PWA: Subscription enviada com sucesso');

            } catch (error) {
                console.error('PWA: Erro ao enviar subscription', error);
            }
        }

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        async cacheUrl(url) {
            if (!this.state.registration) return;

            try {
                const cache = await caches.open('nosfirnews-v2.0.0');
                await cache.add(url);
                console.log(`PWA: URL cacheada: ${url}`);
            } catch (error) {
                console.error('PWA: Erro ao cachear URL', error);
            }
        }

        async clearCache() {
            try {
                const cacheNames = await caches.keys();
                await Promise.all(
                    cacheNames.map(cacheName => caches.delete(cacheName))
                );
                console.log('PWA: Cache limpo com sucesso');
                this.emit('cacheCleared');
            } catch (error) {
                console.error('PWA: Erro ao limpar cache', error);
            }
        }

        createNotification(title, message, actions = []) {
            const notification = document.createElement('div');
            notification.className = 'pwa-notification';
            notification.innerHTML = `
                <div class="pwa-notification-content">
                    <h3 class="pwa-notification-title">${title}</h3>
                    <p class="pwa-notification-message">${message}</p>
                    <div class="pwa-notification-actions">
                        ${actions.map((action, index) => `
                            <button class="pwa-notification-button" data-action="${index}">
                                ${action.text}
                            </button>
                        `).join('')}
                    </div>
                </div>
            `;

            // Event listeners para ações
            actions.forEach((action, index) => {
                const button = notification.querySelector(`[data-action="${index}"]`);
                button.addEventListener('click', () => {
                    if (action.callback) action.callback();
                });
            });

            document.body.appendChild(notification);

            // Animar entrada
            requestAnimationFrame(() => {
                notification.classList.add('visible');
            });

            // Método para remover
            notification.remove = () => {
                notification.classList.remove('visible');
                setTimeout(() => notification.remove(), 300);
            };

            return notification;
        }

        setupUpdateCheck() {
            // Verificar updates quando página ganhar foco
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden && this.state.registration) {
                    this.state.registration.update();
                }
            });
        }

        emit(eventName, detail = {}) {
            const event = new CustomEvent(`nosfirnews:pwa:${eventName}`, { 
                detail,
                bubbles: true
            });
            document.dispatchEvent(event);
        }

        // Métodos públicos úteis
        getState() {
            return { ...this.state };
        }

        isInstalled() {
            return this.state.isInstalled;
        }

        isOnline() {
            return this.state.isOnline;
        }
    }

    // Estilos CSS inline para componentes PWA
    const styles = document.createElement('style');
    styles.textContent = `
        .pwa-install-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            cursor: pointer;
            z-index: 9999;
            opacity: 0;
            transform: translateY(100px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pwa-install-button.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .pwa-install-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .pwa-install-button svg {
            flex-shrink: 0;
        }

        .connection-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            z-index: 9998;
            transition: all 0.3s ease;
            opacity: 1;
        }

        .connection-status.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .connection-status.online {
            background: #4caf50;
            color: white;
        }

        .connection-status.offline {
            background: #f44336;
            color: white;
        }

        .pwa-notification {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            max-width: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            opacity: 0;
            transform: translateY(100px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pwa-notification.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .pwa-notification-content {
            padding: 20px;
        }

        .pwa-notification-title {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .pwa-notification-message {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .pwa-notification-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .pwa-notification-button {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pwa-notification-button:first-child {
            background: #667eea;
            color: white;
        }

        .pwa-notification-button:first-child:hover {
            background: #5568d3;
        }

        .pwa-notification-button:last-child {
            background: #e0e0e0;
            color: #333;
        }

        .pwa-notification-button:last-child:hover {
            background: #d0d0d0;
        }

        @media (max-width: 768px) {
            .pwa-install-button {
                bottom: 10px;
                right: 10px;
                padding: 10px 16px;
                font-size: 14px;
            }

            .pwa-notification {
                left: 10px;
                right: 10px;
            }
        }
    `;
    document.head.appendChild(styles);

    // Inicializar PWA Manager com configuração do WordPress
    function init() {
        const config = window.nosfirNewsConfig || {};
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                window.NosfirNewsPWA = new PWAManager(config);
            });
        } else {
            window.NosfirNewsPWA = new PWAManager(config);
        }
    }

    init();

    // Expor classe para uso global
    window.PWAManager = PWAManager;

})();