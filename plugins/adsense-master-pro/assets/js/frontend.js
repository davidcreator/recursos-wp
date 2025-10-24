/* AdSense Master Pro - Frontend JavaScript */

(function($) {
    'use strict';
    
    // Objeto principal do plugin
    var AMP = {
        init: function() {
            this.setupAdBlockerDetection();
            this.setupLazyLoading();
            this.setupStickyAds();
            this.setupAdRotation();
            this.setupGDPRConsent();
            this.setupMobileDetection();
            this.setupAdTracking();
            this.bindEvents();
        },
        
        bindEvents: function() {
            $(document).ready(function() {
                AMP.onDocumentReady();
            });
            
            $(window).on('load', function() {
                AMP.onWindowLoad();
            });
            
            $(window).on('scroll', function() {
                AMP.onScroll();
            });
            
            $(window).on('resize', function() {
                AMP.onResize();
            });
        },
        
        onDocumentReady: function() {
            this.initializeAds();
            this.checkViewportAds();
        },
        
        onWindowLoad: function() {
            this.loadLazyAds();
            this.startAdRotation();
        },
        
        onScroll: function() {
            this.checkViewportAds();
            this.updateStickyAds();
        },
        
        onResize: function() {
            this.recalculateAdPositions();
        },
        
        // Inicializar anúncios
        initializeAds: function() {
            $('.amp-ad-container').each(function() {
                var $ad = $(this);
                var adId = $ad.data('ad-id');
                
                // Adicionar classe de animação
                $ad.addClass('amp-ad-fade-in');
                
                // Verificar condições de exibição
                if (!AMP.shouldDisplayAd($ad)) {
                    $ad.hide();
                    return;
                }
                
                // Aplicar configurações específicas
                AMP.applyAdSettings($ad, adId);
            });
        },
        
        shouldDisplayAd: function($ad) {
            // Verificar dispositivo
            var isMobile = AMP.isMobile();
            if (isMobile && $ad.hasClass('amp-hide-mobile')) {
                return false;
            }
            if (!isMobile && $ad.hasClass('amp-hide-desktop')) {
                return false;
            }
            
            // Verificar consentimento GDPR
            if (!AMP.hasGDPRConsent()) {
                return false;
            }
            
            // Verificar ad blocker
            if (AMP.isAdBlockerActive()) {
                return false;
            }
            
            return true;
        },
        
        applyAdSettings: function($ad, adId) {
            // Aplicar configurações baseadas nos dados do anúncio
            var settings = $ad.data('settings') || {};
            
            if (settings.sticky) {
                AMP.makeStickyAd($ad, settings.stickyPosition);
            }
            
            if (settings.lazy) {
                AMP.setupLazyAd($ad);
            }
            
            if (settings.rotation) {
                AMP.setupAdRotation($ad, settings.rotationInterval);
            }
        },
        
        // Detecção de Ad Blocker
        setupAdBlockerDetection: function() {
            var testAd = document.createElement('div');
            testAd.innerHTML = '&nbsp;';
            testAd.className = 'adsbox';
            testAd.style.position = 'absolute';
            testAd.style.left = '-1000px';
            testAd.style.top = '-1000px';
            document.body.appendChild(testAd);
            
            setTimeout(function() {
                if (testAd.offsetHeight === 0) {
                    AMP.adBlockerDetected = true;
                    AMP.handleAdBlocker();
                }
                document.body.removeChild(testAd);
            }, 100);
        },
        
        handleAdBlocker: function() {
            $('.amp-ad-container').each(function() {
                var $ad = $(this);
                var message = $ad.data('adblock-message') || 'Por favor, desative seu bloqueador de anúncios para apoiar nosso site.';
                
                $ad.html('<div class="amp-adblock-message">' + message + '</div>');
                $ad.attr('data-ad-blocked', 'true');
            });
            
            // Disparar evento personalizado
            $(document).trigger('amp-adblock-detected');
        },
        
        // Lazy Loading
        setupLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                this.lazyObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            AMP.loadAd($(entry.target));
                            AMP.lazyObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    rootMargin: '50px'
                });
            }
        },
        
        setupLazyAd: function($ad) {
            if (this.lazyObserver) {
                $ad.addClass('amp-ad-lazy');
                this.lazyObserver.observe($ad[0]);
            } else {
                // Fallback para navegadores sem IntersectionObserver
                this.loadAd($ad);
            }
        },
        
        loadLazyAds: function() {
            $('.amp-ad-lazy').each(function() {
                if (AMP.isInViewport($(this))) {
                    AMP.loadAd($(this));
                }
            });
        },
        
        loadAd: function($ad) {
            $ad.removeClass('amp-ad-lazy');
            
            // Carregar conteúdo do anúncio
            var adContent = $ad.data('lazy-content');
            if (adContent) {
                $ad.find('.amp-ad-content').html(adContent);
            }
            
            // Executar scripts do AdSense se necessário
            if ($ad.find('.adsbygoogle').length) {
                try {
                    (adsbygoogle = window.adsbygoogle || []).push({});
                } catch (e) {
                    console.log('Erro ao carregar anúncio AdSense:', e);
                }
            }
            
            $ad.addClass('amp-ad-loaded');
            $(document).trigger('amp-ad-loaded', $ad);
        },
        
        // Anúncios Sticky
        setupStickyAds: function() {
            $('.amp-ad-sticky').each(function() {
                AMP.makeStickyAd($(this));
            });
        },
        
        makeStickyAd: function($ad, position) {
            position = position || 'bottom';
            
            $ad.addClass('amp-ad-sticky amp-ad-sticky-' + position);
            
            // Adicionar botão de fechar
            if (!$ad.find('.amp-close-ad').length) {
                var closeBtn = $('<button class="amp-close-ad" title="Fechar anúncio">&times;</button>');
                $ad.append(closeBtn);
                
                closeBtn.on('click', function() {
                    $ad.fadeOut();
                    // Salvar preferência do usuário
                    AMP.setUserPreference('hide-sticky-ad', true, 24); // 24 horas
                });
            }
            
            // Verificar preferência do usuário
            if (AMP.getUserPreference('hide-sticky-ad')) {
                $ad.hide();
            }
        },
        
        updateStickyAds: function() {
            var scrollTop = $(window).scrollTop();
            var windowHeight = $(window).height();
            
            $('.amp-ad-sticky').each(function() {
                var $ad = $(this);
                var showAfter = $ad.data('show-after') || 0;
                
                if (scrollTop > showAfter) {
                    $ad.addClass('show');
                } else {
                    $ad.removeClass('show');
                }
            });
        },
        
        // Rotação de Anúncios
        setupAdRotation: function() {
            $('.amp-ad-rotator').each(function() {
                var $rotator = $(this);
                var interval = $rotator.data('rotation-interval') || 30000; // 30 segundos
                
                AMP.startRotation($rotator, interval);
            });
        },
        
        startRotation: function($rotator, interval) {
            var $ads = $rotator.find('.amp-ad-item');
            var currentIndex = 0;
            
            // Mostrar primeiro anúncio
            $ads.eq(currentIndex).addClass('active');
            
            setInterval(function() {
                $ads.eq(currentIndex).removeClass('active');
                currentIndex = (currentIndex + 1) % $ads.length;
                $ads.eq(currentIndex).addClass('active');
                
                // Disparar evento de rotação
                $(document).trigger('amp-ad-rotated', {
                    rotator: $rotator,
                    currentAd: $ads.eq(currentIndex),
                    index: currentIndex
                });
            }, interval);
        },
        
        startAdRotation: function() {
            // Iniciar rotações automáticas
            $('.amp-ad-rotator[data-auto-rotate="true"]').each(function() {
                if (!$(this).data('rotation-started')) {
                    AMP.startRotation($(this), $(this).data('rotation-interval') || 30000);
                    $(this).data('rotation-started', true);
                }
            });
        },
        
        // Consentimento GDPR
        setupGDPRConsent: function() {
            if (!AMP.hasGDPRConsent() && AMP.requiresGDPRConsent()) {
                AMP.showGDPRNotice();
            }
        },
        
        requiresGDPRConsent: function() {
            // Verificar se o usuário está na UE (simplificado)
            var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            var euTimezones = ['Europe/', 'Atlantic/'];
            
            return euTimezones.some(function(tz) {
                return timezone.startsWith(tz);
            });
        },
        
        hasGDPRConsent: function() {
            return AMP.getUserPreference('gdpr-consent') === 'accepted';
        },
        
        showGDPRNotice: function() {
            var notice = $('<div class="amp-gdpr-notice">' +
                '<p>Este site usa cookies e tecnologias de publicidade para melhorar sua experiência. ' +
                'Ao continuar navegando, você concorda com nossa política de cookies.</p>' +
                '<div class="amp-gdpr-buttons">' +
                    '<button class="amp-gdpr-button" data-action="accept">Aceitar</button>' +
                    '<button class="amp-gdpr-button" data-action="decline">Recusar</button>' +
                '</div>' +
            '</div>');
            
            $('body').append(notice);
            
            notice.on('click', '.amp-gdpr-button', function() {
                var action = $(this).data('action');
                AMP.setUserPreference('gdpr-consent', action, 365); // 1 ano
                notice.fadeOut();
                
                if (action === 'accepted') {
                    // Recarregar anúncios
                    AMP.initializeAds();
                }
            });
        },
        
        // Detecção de dispositivo móvel
        setupMobileDetection: function() {
            this.mobile = this.isMobile();
            
            if (this.mobile) {
                $('body').addClass('amp-mobile-device');
            } else {
                $('body').addClass('amp-desktop-device');
            }
        },
        
        isMobile: function() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                   window.innerWidth <= 768;
        },
        
        // Rastreamento de anúncios
        setupAdTracking: function() {
            // Rastrear impressões
            $('.amp-ad-container').each(function() {
                var $ad = $(this);
                if (AMP.isInViewport($ad)) {
                    AMP.trackAdImpression($ad);
                }
            });
        },
        
        trackAdImpression: function($ad) {
            if ($ad.data('impression-tracked')) {
                return;
            }
            
            var adId = $ad.data('ad-id');
            
            // Enviar dados de impressão apenas se amp_ajax estiver disponível
            if (typeof amp_ajax !== 'undefined') {
                $.post(amp_ajax.ajax_url, {
                    action: 'amp_track_impression',
                    ad_id: adId,
                    nonce: amp_ajax.nonce
                }).fail(function() {
                    console.log('Erro ao rastrear impressão do anúncio');
                });
            }
            
            $ad.data('impression-tracked', true);
        },
        
        trackAdClick: function($ad) {
            var adId = $ad.data('ad-id');
            
            if (typeof amp_ajax !== 'undefined') {
                $.post(amp_ajax.ajax_url, {
                    action: 'amp_track_click',
                    ad_id: adId,
                    nonce: amp_ajax.nonce
                }).fail(function() {
                    console.log('Erro ao rastrear clique do anúncio');
                });
            }
        },
        
        // Verificação de viewport
        checkViewportAds: function() {
            $('.amp-ad-container:not(.amp-ad-loaded)').each(function() {
                var $ad = $(this);
                if (AMP.isInViewport($ad)) {
                    if ($ad.hasClass('amp-ad-lazy')) {
                        AMP.loadAd($ad);
                    }
                    if (!$ad.data('impression-tracked')) {
                        AMP.trackAdImpression($ad);
                    }
                }
            });
        },
        
        isInViewport: function($element) {
            var elementTop = $element.offset().top;
            var elementBottom = elementTop + $element.outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            return elementBottom > viewportTop && elementTop < viewportBottom;
        },
        
        // Recalcular posições
        recalculateAdPositions: function() {
            $('.amp-ad-sticky').each(function() {
                var $ad = $(this);
                // Recalcular posições sticky se necessário
                // Implementar lógica específica conforme necessário
            });
        },
        
        // Gerenciamento de preferências do usuário
        setUserPreference: function(key, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = 'amp_' + key + '=' + value + expires + '; path=/';
        },
        
        getUserPreference: function(key) {
            var nameEQ = 'amp_' + key + '=';
            var cookies = document.cookie.split(';');
            
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i];
                while (cookie.charAt(0) === ' ') {
                    cookie = cookie.substring(1, cookie.length);
                }
                if (cookie.indexOf(nameEQ) === 0) {
                    return cookie.substring(nameEQ.length, cookie.length);
                }
            }
            return null;
        },
        
        // Funções utilitárias
        isAdBlockerActive: function() {
            return this.adBlockerDetected || false;
        },
        
        // API pública para desenvolvedores
        refreshAd: function(adId) {
            var $ad = $('#amp-ad-' + adId);
            if ($ad.length) {
                // Recarregar o anúncio
                var adContent = $ad.data('original-content');
                if (adContent) {
                    $ad.find('.amp-ad-content').html(adContent);
                    
                    // Executar scripts novamente
                    if ($ad.find('.adsbygoogle').length) {
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    }
                }
            }
        },
        
        hideAd: function(adId) {
            $('#amp-ad-' + adId).fadeOut();
        },
        
        showAd: function(adId) {
            $('#amp-ad-' + adId).fadeIn();
        },
        
        // Eventos personalizados
        on: function(event, callback) {
            $(document).on('amp-' + event, callback);
        },
        
        trigger: function(event, data) {
            $(document).trigger('amp-' + event, data);
        }
    };
    
    // Inicializar quando o DOM estiver pronto
    $(document).ready(function() {
        AMP.init();
    });
    
    // Expor API globalmente
    window.AMP = AMP;
    
    // Compatibilidade com temas
    $(document).on('click', '.amp-ad-container a', function() {
        AMP.trackAdClick($(this).closest('.amp-ad-container'));
    });
    
    // Suporte para Accelerated Mobile Pages (AMP)
    if (window.location.pathname.includes('/amp/')) {
        $('body').addClass('amp-page');
    }
    
    // Debug mode
    if (window.location.search.includes('amp_debug=1')) {
        AMP.debug = true;
        console.log('AdSense Master Pro Debug Mode Ativado');
        
        // Log eventos para debug
        $(document).on('amp-ad-loaded', function(event, $ad) {
            console.log('Anúncio carregado:', $ad.data('ad-id'));
        });
        
        $(document).on('amp-adblock-detected', function() {
            console.log('Ad Blocker detectado');
        });
    }

})(jQuery);

// Funções auxiliares globais
function ampDisplayAd(adId) {
    if (window.AMP) {
        window.AMP.showAd(adId);
    }
}

function ampHideAd(adId) {
    if (window.AMP) {
        window.AMP.hideAd(adId);
    }
}

function ampRefreshAd(adId) {
    if (window.AMP) {
        window.AMP.refreshAd(adId);
    }
}