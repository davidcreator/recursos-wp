/**
 * AdSense Master Pro v3.0
 * JavaScript para Posicionamento Avançado de Anúncios
 * 
 * @package AdSense Master Pro
 * @version 3.0.0
 * @since 3.0.0
 */

(function() {
    'use strict';

    /**
     * Controlador de Anúncios Flutuantes
     */
    const AMP_FloatingAds = {
        ads: [],
        
        init: function() {
            this.ads = document.querySelectorAll('.amp-floating-ad');
            
            if (this.ads.length === 0) {
                return;
            }
            
            this.ads.forEach((ad) => {
                this.setupAd(ad);
            });
        },

        setupAd: function(ad) {
            const showAfter = parseInt(ad.dataset.showAfter) || 0;
            const position = ad.className.match(/amp-floating-(\w+)/)?.[1] || 'bottom';
            
            // Mostrar anúncio após X pixels de scroll
            if (showAfter > 0) {
                let hasShown = false;
                window.addEventListener('scroll', () => {
                    if (window.pageYOffset > showAfter && !hasShown) {
                        this.showAd(ad);
                        hasShown = true;
                    }
                });
            } else {
                this.showAd(ad);
            }

            // Botão fechar
            const closeBtn = ad.querySelector('.amp-floating-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.hideAd(ad);
                    // Salvar preferência por 24 horas
                    this.setPreference(`hide-floating-${position}`, true, 24);
                });
            }

            // Verificar se deve ser mostrado (baseado em preferências salvas)
            if (this.getPreference(`hide-floating-${position}`)) {
                this.hideAd(ad);
            }
        },

        showAd: function(ad) {
            ad.classList.remove('hidden');
            ad.classList.add('visible');
            
            // Rastrear impressão
            this.trackImpression(ad.dataset.adId);
        },

        hideAd: function(ad) {
            ad.classList.add('hidden');
            ad.classList.remove('visible');
        },

        trackImpression: function(adId) {
            if (!adId || window.amp_ajax === undefined) {
                return;
            }

            fetch(window.amp_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=amp_track_impression&ad_id=${adId}&nonce=${window.amp_ajax.nonce}`
            }).catch((error) => {
                console.log('Erro ao rastrear impressão:', error);
            });
        },

        setPreference: function(key, value, days) {
            let expires = '';
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = `amp_${key}=${value}${expires}; path=/`;
        },

        getPreference: function(key) {
            const nameEQ = `amp_${key}=`;
            const cookies = document.cookie.split(';');

            for (let i = 0; i < cookies.length; i++) {
                let cookie = cookies[i].trim();
                if (cookie.indexOf(nameEQ) === 0) {
                    return cookie.substring(nameEQ.length);
                }
            }
            return null;
        }
    };

    /**
     * Controlador de Pop-ups
     */
    const AMP_PopupAds = {
        popup: null,
        timeout: null,
        scrollListener: null,
        shownCount: 0,

        init: function() {
            this.popup = document.getElementById('amp-popup-ad');
            if (!this.popup) {
                return;
            }

            const trigger = this.popup.dataset.trigger || 'time';
            const triggerValue = parseInt(this.popup.dataset.triggerValue) || 3;
            const frequency = this.popup.dataset.frequency || 'once_per_session';
            const maxShows = parseInt(this.popup.dataset.maxShows) || 1;

            // Verificar frequência
            if (!this.shouldShow(frequency)) {
                return;
            }

            // Setup gatilhos
            if (trigger === 'time') {
                this.setupTimeTrigg (triggerValue);
            } else if (trigger === 'scroll') {
                this.setupScrollTrigger(triggerValue);
            } else if (trigger === 'exit') {
                this.setupExitTrigger();
            }

            this.setupCloseButtons();
        },

        setupTimeTrigger: function(seconds) {
            this.timeout = setTimeout(() => {
                if (this.shownCount < 1) {
                    this.show();
                }
            }, seconds * 1000);
        },

        setupScrollTrigger: function(pixels) {
            this.scrollListener = () => {
                if (window.pageYOffset > pixels && this.shownCount === 0) {
                    this.show();
                    window.removeEventListener('scroll', this.scrollListener);
                }
            };
            window.addEventListener('scroll', this.scrollListener);
        },

        setupExitTrigger: function() {
            document.addEventListener('mouseleave', () => {
                if (this.shownCount === 0) {
                    this.show();
                }
            }, { once: true });
        },

        setupCloseButtons: function() {
            const closeBtn = this.popup.querySelector('.amp-popup-close');
            const overlay = this.popup.querySelector('.amp-popup-overlay');

            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.hide());
            }

            if (overlay) {
                overlay.addEventListener('click', () => this.hide());
            }

            // ESC para fechar
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.popup.style.display !== 'none') {
                    this.hide();
                }
            });
        },

        show: function() {
            if (!this.popup) return;

            this.popup.style.display = 'flex';
            this.shownCount++;

            // Rastrear impressão
            const adId = this.popup.dataset.adId;
            if (adId) {
                this.trackImpression(adId);
            }

            // Salvar que foi mostrado
            this.saveShown();
        },

        hide: function() {
            if (!this.popup) return;

            this.popup.style.display = 'none';
            
            // Limpar timeout/listener se ainda ativo
            if (this.timeout) clearTimeout(this.timeout);
            if (this.scrollListener) {
                window.removeEventListener('scroll', this.scrollListener);
            }
        },

        shouldShow: function(frequency) {
            const key = `popup_shown_${frequency}`;
            const shown = sessionStorage.getItem(key);

            if (frequency === 'once_per_session') {
                return !shown;
            } else if (frequency === 'once_per_day') {
                const lastShown = localStorage.getItem(key);
                if (!lastShown) return true;

                const lastShownTime = parseInt(lastShown);
                const now = Date.now();
                const dayMs = 24 * 60 * 60 * 1000;

                return (now - lastShownTime) > dayMs;
            }

            return true;
        },

        saveShown: function() {
            const frequency = this.popup.dataset.frequency || 'once_per_session';

            if (frequency === 'once_per_session') {
                sessionStorage.setItem(`popup_shown_${frequency}`, 'true');
            } else if (frequency === 'once_per_day') {
                localStorage.setItem(`popup_shown_${frequency}`, Date.now().toString());
            }
        },

        trackImpression: function(adId) {
            if (!window.amp_ajax) return;

            fetch(window.amp_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=amp_track_impression&ad_id=${adId}&nonce=${window.amp_ajax.nonce}`
            }).catch((error) => {
                console.log('Erro ao rastrear pop-up:', error);
            });
        }
    };

    /**
     * Controlador de Filtros
     */
    const AMP_FilterBar = {
        init: function() {
            const filterButtons = document.querySelectorAll('.amp-filter-button');
            
            filterButtons.forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.handleFilter(btn);
                });
            });
        },

        handleFilter: function(button) {
            const termId = button.dataset.termId;
            const taxonomy = button.dataset.taxonomy;

            // Toggle ativo
            button.classList.toggle('active');

            // Atualizar lista de posts
            this.updatePosts(taxonomy, this.getActiveTerms(taxonomy));
        },

        getActiveTerms: function(taxonomy) {
            return Array.from(
                document.querySelectorAll(`.amp-filter-button[data-taxonomy="${taxonomy}"].active`)
            ).map((btn) => btn.dataset.termId);
        },

        updatePosts: function(taxonomy, termIds) {
            const container = document.querySelector('[data-filter-taxonomy="' + taxonomy + '"]');
            if (!container) return;

            // Mostrar loading
            container.style.opacity = '0.5';

            // Fazer requisição REST
            const url = new URL(`${window.location.origin}/wp-json/bdrposts/v1/posts`);
            url.searchParams.append('taxonomy', taxonomy);
            url.searchParams.append('terms', termIds.join(','));

            fetch(url)
                .then((response) => response.json())
                .then((data) => {
                    // Atualizar HTML
                    container.innerHTML = data.html;
                    container.style.opacity = '1';
                })
                .catch((error) => {
                    console.error('Erro ao atualizar posts:', error);
                    container.style.opacity = '1';
                });
        }
    };

    /**
     * Rastreamento de Cliques
     */
    const AMP_ClickTracking = {
        init: function() {
            document.addEventListener('click', (e) => {
                const adContainer = e.target.closest('[data-ad-id]');
                
                if (adContainer) {
                    const adId = adContainer.dataset.adId;
                    this.trackClick(adId);
                }
            });
        },

        trackClick: function(adId) {
            if (!adId || !window.amp_ajax) return;

            fetch(window.amp_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=amp_track_click&ad_id=${adId}&nonce=${window.amp_ajax.nonce}`
            }).catch((error) => {
                console.log('Erro ao rastrear clique:', error);
            });
        }
    };

    /**
     * Inicialização
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            AMP_FloatingAds.init();
            AMP_PopupAds.init();
            AMP_FilterBar.init();
            AMP_ClickTracking.init();
        });
    } else {
        AMP_FloatingAds.init();
        AMP_PopupAds.init();
        AMP_FilterBar.init();
        AMP_ClickTracking.init();
    }

    // Expor globalmente
    window.AMP_FloatingAds = AMP_FloatingAds;
    window.AMP_PopupAds = AMP_PopupAds;
    window.AMP_FilterBar = AMP_FilterBar;
})();