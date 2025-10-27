(function() {
    'use strict';

    /**
     * Inicializa Sliders Swiper
     */
    function initSliders() {
        if (typeof Swiper === 'undefined') {
            console.warn('BDRPosts: Swiper não encontrado');
            return;
        }

        const sliders = document.querySelectorAll('.bdrposts-slider.swiper');
        
        sliders.forEach(function(slider) {
            // Verifica se já foi inicializado
            if (slider.swiper) {
                return;
            }

            try {
                new Swiper(slider, {
                    loop: true,
                    slidesPerView: 1,
                    spaceBetween: 20,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: slider.querySelector('.swiper-pagination'),
                        clickable: true,
                        dynamicBullets: true,
                    },
                    navigation: {
                        nextEl: slider.querySelector('.swiper-button-next'),
                        prevEl: slider.querySelector('.swiper-button-prev'),
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 1,
                        },
                        768: {
                            slidesPerView: 2,
                        },
                        1024: {
                            slidesPerView: 3,
                        },
                    }
                });
            } catch (error) {
                console.error('BDRPosts: Erro ao inicializar slider', error);
            }
        });
    }

    /**
     * Inicializa Tickers (efeito marquee)
     */
    function initTickers() {
        const tickers = document.querySelectorAll('.bdrposts-ticker .bdrposts-ticker-content');
        
        tickers.forEach(function(ticker) {
            // Calcula largura total
            let totalWidth = 0;
            const items = ticker.querySelectorAll('.bdrposts-ticker-item');
            
            items.forEach(function(item) {
                totalWidth += item.offsetWidth + 30; // 30px = margin-right
            });

            // Define largura
            ticker.style.width = (totalWidth + 100) + 'px';

            // Animação
            let offset = 0;
            const speed = 1; // pixels por frame
            
            function animate() {
                offset -= speed;
                
                // Reset quando chegar no fim
                if (Math.abs(offset) >= totalWidth / 2) {
                    offset = 0;
                }
                
                ticker.style.transform = 'translateX(' + offset + 'px)';
                ticker.rafId = requestAnimationFrame(animate);
            }

            // Pausa ao passar o mouse
            ticker.parentElement.addEventListener('mouseenter', function() {
                if (ticker.rafId) {
                    cancelAnimationFrame(ticker.rafId);
                }
            });

            ticker.parentElement.addEventListener('mouseleave', function() {
                ticker.rafId = requestAnimationFrame(animate);
            });

            // Inicia animação
            ticker.rafId = requestAnimationFrame(animate);
        });
    }

    /**
     * Inicializa Masonry (usando CSS columns como fallback)
     */
    function initMasonry() {
        const masonryContainers = document.querySelectorAll('.bdrposts-masonry');
        
        masonryContainers.forEach(function(container) {
            // Adiciona classe para indicar que foi inicializado
            container.classList.add('bdrposts-masonry-initialized');
            
            // Força reflow para melhor renderização
            const items = container.querySelectorAll('.bdrposts-item');
            items.forEach(function(item, index) {
                item.style.opacity = '0';
                setTimeout(function() {
                    item.style.transition = 'opacity 0.3s ease';
                    item.style.opacity = '1';
                }, index * 50);
            });
        });
    }

    /**
     * Adiciona efeitos de hover e animações
     */
    function addInteractivity() {
        const items = document.querySelectorAll('.bdrposts-item');
        
        items.forEach(function(item) {
            // Lazy loading para imagens
            const images = item.querySelectorAll('img[loading="lazy"]');
            images.forEach(function(img) {
                if ('loading' in HTMLImageElement.prototype) {
                    // Browser suporta lazy loading nativo
                    img.loading = 'lazy';
                } else {
                    // Fallback para browsers antigos
                    if ('IntersectionObserver' in window) {
                        const imageObserver = new IntersectionObserver(function(entries) {
                            entries.forEach(function(entry) {
                                if (entry.isIntersecting) {
                                    const img = entry.target;
                                    if (img.dataset.src) {
                                        img.src = img.dataset.src;
                                        img.removeAttribute('data-src');
                                    }
                                    imageObserver.unobserve(img);
                                }
                            });
                        });
                        imageObserver.observe(img);
                    }
                }
            });
        });
    }

    /**
     * Função principal de inicialização
     */
    function init() {
        try {
            initSliders();
            initTickers();
            initMasonry();
            addInteractivity();
        } catch (error) {
            console.error('BDRPosts: Erro na inicialização', error);
        }
    }

    /**
     * Aguarda DOM estar pronto
     */
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(init, 1);
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }

    /**
     * Reinicializa quando blocos dinâmicos são carregados
     */
    if (window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.classList && node.classList.contains('bdrposts-wrapper')) {
                            init();
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Expõe API pública para desenvolvedores
     */
    window.BDRPosts = {
        init: init,
        initSliders: initSliders,
        initTickers: initTickers,
        initMasonry: initMasonry,
        version: '1.0.0'
    };

})();