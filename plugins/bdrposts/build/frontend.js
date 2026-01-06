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
            // Respeita preferências de movimento reduzido
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                ticker.style.transform = 'none';
                ticker.style.animation = 'none';
                return;
            }
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
     * Inicializa Masonry (MELHORADO)
     * Suporta CSS columns com melhorias de renderização
     */
    function initMasonry() {
        const masonryContainers = document.querySelectorAll('.bdrposts-masonry');
        
        masonryContainers.forEach(function(container) {
            // Adiciona classe para indicar que foi inicializado
            container.classList.add('bdrposts-masonry-initialized');
            
            // Força reflow para melhor renderização com CSS columns
            const items = container.querySelectorAll('.bdrposts-item');
            items.forEach(function(item, index) {
                item.style.opacity = '0';
                setTimeout(function() {
                    item.style.transition = 'opacity 0.3s ease';
                    item.style.opacity = '1';
                }, index * 50);
            });
            
            // Observa mudanças de tamanho para reorganizar
            if (window.ResizeObserver) {
                const resizeObserver = new ResizeObserver(function(entries) {
                    entries.forEach(function(entry) {
                        // Força recalculo de colunas quando resize acontecer
                        const currentGap = getComputedStyle(entry.target).columnGap;
                        entry.target.style.columnGap = currentGap;
                    });
                });
                resizeObserver.observe(container);
            }
            
            // Observa quando imagens carregam para reajustar layout
            const images = container.querySelectorAll('img');
            let loadedImages = 0;
            const totalImages = images.length;
            
            if (totalImages > 0) {
                images.forEach(function(img) {
                    function imageLoaded() {
                        loadedImages++;
                        if (loadedImages === totalImages) {
                            // Todas as imagens carregadas, força recalculo
                            const currentGap = getComputedStyle(container).columnGap;
                            container.style.columnGap = currentGap;
                        }
                    }
                    
                    if (img.complete) {
                        imageLoaded();
                    } else {
                        img.addEventListener('load', imageLoaded);
                        img.addEventListener('error', imageLoaded);
                    }
                });
            }
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
     * Inicializa filtros de categoria/tag
     */
    function initFilters() {
        const wrappers = document.querySelectorAll('.bdrposts-wrapper');
        wrappers.forEach(function(wrapper) {
            const filter = wrapper.querySelector('.bdrposts-filter');
            const buttons = filter ? filter.querySelectorAll('.bdrposts-filter-item') : [];
            const attrsRaw = wrapper.getAttribute('data-bdrposts-attrs');
            let attrs = {};
            try { attrs = attrsRaw ? JSON.parse(attrsRaw) : {}; } catch (e) {}
            buttons.forEach(function(btn) {
                btn.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    const taxonomy = btn.getAttribute('data-taxonomy');
                    const termId = btn.getAttribute('data-term');
                    const payload = {
                        attributes: { ...attrs, enablePagination: false, page: 1 },
                        taxonomy: taxonomy || '',
                        term: termId ? parseInt(termId, 10) : 0
                    };
                    if (payload.taxonomy === 'category') {
                        payload.attributes.categories = payload.term ? [payload.term] : [];
                        payload.attributes.tags = [];
                    } else if (payload.taxonomy === 'post_tag') {
                        payload.attributes.tags = payload.term ? [payload.term] : [];
                        payload.attributes.categories = [];
                    }
                    if (window.wp && wp.apiFetch) {
                        wp.apiFetch({
                            path: '/bdrposts/v1/render',
                            method: 'POST',
                            data: payload
                        }).then(function(html) {
                            const container = document.createElement('div');
                            container.innerHTML = html;
                            const newWrapper = container.firstElementChild;
                            if (newWrapper && newWrapper.classList.contains('bdrposts-wrapper')) {
                                wrapper.replaceWith(newWrapper);
                                init();
                            }
                        }).catch(function(err) {
                            console.error('BDRPosts: erro ao filtrar', err);
                        });
                    }
                });
            });
            const tools = wrapper.querySelector('.bdrposts-tools');
            if (tools) {
                const search = tools.querySelector('.bdrposts-search');
                const sortBy = tools.querySelector('.bdrposts-sort-by');
                const sortOrderBtn = tools.querySelector('.bdrposts-sort-order');
                let searchTimer = null;
                if (search) {
                    search.addEventListener('input', function() {
                        if (searchTimer) { clearTimeout(searchTimer); }
                        searchTimer = setTimeout(function() {
                            const payload = { attributes: { ...attrs, searchTerm: search.value || '', page: 1, enablePagination: false } };
                            if (window.wp && wp.apiFetch) {
                                wp.apiFetch({ path: '/bdrposts/v1/render', method: 'POST', data: payload }).then(function(html) {
                                    const container = document.createElement('div');
                                    container.innerHTML = html;
                                    const newWrapper = container.firstElementChild;
                                    if (newWrapper && newWrapper.classList.contains('bdrposts-wrapper')) {
                                        wrapper.replaceWith(newWrapper);
                                        init();
                                    }
                                }).catch(function(err) { console.error('BDRPosts: erro na busca', err); });
                            }
                        }, 300);
                    });
                }
                if (sortBy) {
                    sortBy.addEventListener('change', function() {
                        const payload = { attributes: { ...attrs, orderBy: sortBy.value, page: 1, enablePagination: false } };
                        if (window.wp && wp.apiFetch) {
                            wp.apiFetch({ path: '/bdrposts/v1/render', method: 'POST', data: payload }).then(function(html) {
                                const container = document.createElement('div');
                                container.innerHTML = html;
                                const newWrapper = container.firstElementChild;
                                if (newWrapper && newWrapper.classList.contains('bdrposts-wrapper')) {
                                    wrapper.replaceWith(newWrapper);
                                    init();
                                }
                            }).catch(function(err) { console.error('BDRPosts: erro ao ordenar', err); });
                        }
                    });
                }
                if (sortOrderBtn) {
                    sortOrderBtn.addEventListener('click', function() {
                        const nextOrder = sortOrderBtn.getAttribute('data-order') === 'ASC' ? 'DESC' : 'ASC';
                        const payload = { attributes: { ...attrs, order: nextOrder, page: 1, enablePagination: false } };
                        if (window.wp && wp.apiFetch) {
                            wp.apiFetch({ path: '/bdrposts/v1/render', method: 'POST', data: payload }).then(function(html) {
                                sortOrderBtn.setAttribute('data-order', nextOrder);
                                sortOrderBtn.textContent = nextOrder;
                                const container = document.createElement('div');
                                container.innerHTML = html;
                                const newWrapper = container.firstElementChild;
                                if (newWrapper && newWrapper.classList.contains('bdrposts-wrapper')) {
                                    wrapper.replaceWith(newWrapper);
                                    init();
                                }
                            }).catch(function(err) { console.error('BDRPosts: erro ao alternar ordem', err); });
                        }
                    });
                }
            }
            const loadMoreBtn = wrapper.querySelector('.bdrposts-load-more');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    const nextPage = parseInt(loadMoreBtn.getAttribute('data-next-page'), 10) || 2;
                    const total = parseInt(wrapper.getAttribute('data-bdrposts-total'), 10) || 1;
                    const payload = { attributes: { ...attrs, page: nextPage, enablePagination: true } };
                    if (window.wp && wp.apiFetch) {
                        wp.apiFetch({ path: '/bdrposts/v1/render', method: 'POST', data: payload }).then(function(html) {
                            const container = document.createElement('div');
                            container.innerHTML = html;
                            const newWrapper = container.firstElementChild;
                            if (!newWrapper) return;
                            const layoutGrid = newWrapper.querySelector('.bdrposts-grid');
                            const layoutMasonry = newWrapper.querySelector('.bdrposts-masonry');
                            const layoutSlider = newWrapper.querySelector('.swiper-wrapper');
                            if (layoutGrid) {
                                const target = wrapper.querySelector('.bdrposts-grid');
                                layoutGrid.childNodes.forEach(function(node) { if (node.nodeType === 1) { target.appendChild(node); } });
                            } else if (layoutMasonry) {
                                const target = wrapper.querySelector('.bdrposts-masonry');
                                layoutMasonry.childNodes.forEach(function(node) { if (node.nodeType === 1) { target.appendChild(node); } });
                                // Reinicializa masonry após adicionar novos items
                                initMasonry();
                            } else if (layoutSlider) {
                                const target = wrapper.querySelector('.swiper-wrapper');
                                layoutSlider.childNodes.forEach(function(node) { if (node.nodeType === 1) { target.appendChild(node); } });
                                initSliders();
                            }
                            const next = nextPage + 1;
                            if (nextPage >= total) {
                                loadMoreBtn.disabled = true;
                                loadMoreBtn.textContent = 'Fim';
                            } else {
                                loadMoreBtn.setAttribute('data-next-page', String(next));
                            }
                        }).catch(function(err) { console.error('BDRPosts: erro ao carregar mais', err); });
                    }
                });
            }
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
            initFilters();
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
        initFilters: initFilters,
        version: '1.0.1'
    };

})();