/**
 * WooCommerce JavaScript for NosfirNews Theme
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        initWooCommerce();
    });

    /**
     * Initialize WooCommerce functionality
     */
    function initWooCommerce() {
        initProductGallery();
        initQuantityButtons();
        initWishlistButtons();
        initProductFilters();
        initCartUpdates();
        initCheckoutValidation();
        initProductQuickView();
        initProductComparison();
    }

    /**
     * Enhanced Product Gallery
     */
    function initProductGallery() {
        // Zoom on hover for product images
        $('.woocommerce-product-gallery__image img').hover(
            function() {
                $(this).addClass('zoomed');
            },
            function() {
                $(this).removeClass('zoomed');
            }
        );

        // Lightbox for product gallery
        if (typeof $.fn.magnificPopup !== 'undefined') {
            $('.woocommerce-product-gallery').magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: {
                    enabled: true,
                    navigateByImgClick: true,
                    preload: [0, 1]
                },
                image: {
                    titleSrc: function(item) {
                        return item.el.attr('title') || '';
                    }
                }
            });
        }
    }

    /**
     * Custom Quantity Buttons
     */
    function initQuantityButtons() {
        // Add quantity buttons if they don't exist
        $('.quantity:not(.buttons_added)').addClass('buttons_added').append('<input type="button" value="+" class="plus" />').prepend('<input type="button" value="-" class="minus" />');

        // Handle quantity changes
        $(document).on('click', '.plus, .minus', function() {
            var $qty = $(this).closest('.quantity').find('.qty'),
                currentVal = parseFloat($qty.val()) || 0,
                max = parseFloat($qty.attr('max')),
                min = parseFloat($qty.attr('min')),
                step = parseFloat($qty.attr('step')) || 1;

            if ($(this).is('.plus')) {
                if (max && (currentVal >= max)) {
                    $qty.val(max);
                } else {
                    $qty.val(currentVal + step);
                }
            } else {
                if (min && (currentVal <= min)) {
                    $qty.val(min);
                } else if (currentVal > 0) {
                    $qty.val(currentVal - step);
                }
            }

            $qty.trigger('change');
        });
    }

    /**
     * Wishlist Functionality
     */
    function initWishlistButtons() {
        $(document).on('click', '.wishlist-button', function(e) {
            e.preventDefault();
            
            var $button = $(this),
                productId = $button.data('product-id'),
                $icon = $button.find('i');

            // Toggle wishlist state
            if ($button.hasClass('added')) {
                $button.removeClass('added');
                $icon.removeClass('fas').addClass('far');
                $button.find('span').text('Adicionar aos Favoritos');
                showNotification('Produto removido dos favoritos', 'info');
            } else {
                $button.addClass('added');
                $icon.removeClass('far').addClass('fas');
                $button.find('span').text('Remover dos Favoritos');
                showNotification('Produto adicionado aos favoritos', 'success');
            }

            // Store in localStorage (in a real implementation, this would be an AJAX call)
            var wishlist = JSON.parse(localStorage.getItem('nosfirnews_wishlist') || '[]');
            var index = wishlist.indexOf(productId);
            
            if (index > -1) {
                wishlist.splice(index, 1);
            } else {
                wishlist.push(productId);
            }
            
            localStorage.setItem('nosfirnews_wishlist', JSON.stringify(wishlist));
        });

        // Load wishlist state on page load
        var wishlist = JSON.parse(localStorage.getItem('nosfirnews_wishlist') || '[]');
        $('.wishlist-button').each(function() {
            var productId = $(this).data('product-id');
            if (wishlist.includes(productId)) {
                $(this).addClass('added');
                $(this).find('i').removeClass('far').addClass('fas');
                $(this).find('span').text('Remover dos Favoritos');
            }
        });
    }

    /**
     * Product Filters
     */
    function initProductFilters() {
        // Price range filter
        if (typeof $.fn.slider !== 'undefined') {
            $('.price-slider').slider({
                range: true,
                min: 0,
                max: 1000,
                values: [0, 1000],
                slide: function(event, ui) {
                    $('.price-range-display').text('R$ ' + ui.values[0] + ' - R$ ' + ui.values[1]);
                }
            });
        }

        // Category filter
        $('.product-categories input[type="checkbox"]').on('change', function() {
            filterProducts();
        });

        // Sort by filter
        $('.orderby').on('change', function() {
            var sortBy = $(this).val();
            sortProducts(sortBy);
        });
    }

    /**
     * Filter products based on selected criteria
     */
    function filterProducts() {
        var selectedCategories = [];
        $('.product-categories input[type="checkbox"]:checked').each(function() {
            selectedCategories.push($(this).val());
        });

        $('.products .product').each(function() {
            var $product = $(this),
                productCategories = $product.data('categories') || [];

            if (selectedCategories.length === 0 || selectedCategories.some(cat => productCategories.includes(cat))) {
                $product.show().addClass('filtered-in');
            } else {
                $product.hide().removeClass('filtered-in');
            }
        });

        // Update results count
        var visibleProducts = $('.products .product:visible').length;
        $('.results-count').text(visibleProducts + ' produtos encontrados');
    }

    /**
     * Sort products
     */
    function sortProducts(sortBy) {
        var $products = $('.products'),
            $productItems = $products.find('.product');

        $productItems.sort(function(a, b) {
            var aVal, bVal;

            switch (sortBy) {
                case 'price':
                    aVal = parseFloat($(a).find('.price').text().replace(/[^\d.]/g, ''));
                    bVal = parseFloat($(b).find('.price').text().replace(/[^\d.]/g, ''));
                    return aVal - bVal;
                case 'price-desc':
                    aVal = parseFloat($(a).find('.price').text().replace(/[^\d.]/g, ''));
                    bVal = parseFloat($(b).find('.price').text().replace(/[^\d.]/g, ''));
                    return bVal - aVal;
                case 'popularity':
                    aVal = parseInt($(a).data('popularity') || 0);
                    bVal = parseInt($(b).data('popularity') || 0);
                    return bVal - aVal;
                case 'rating':
                    aVal = parseFloat($(a).data('rating') || 0);
                    bVal = parseFloat($(b).data('rating') || 0);
                    return bVal - aVal;
                case 'date':
                    aVal = new Date($(a).data('date'));
                    bVal = new Date($(b).data('date'));
                    return bVal - aVal;
                default:
                    return 0;
            }
        });

        $products.html($productItems);
    }

    /**
     * Cart Updates
     */
    function initCartUpdates() {
        // Auto-update cart on quantity change
        $(document).on('change', '.cart .qty', function() {
            var $form = $(this).closest('form');
            $form.find('[name="update_cart"]').prop('disabled', false).trigger('click');
        });

        // Remove item confirmation
        $(document).on('click', '.remove', function(e) {
            if (!confirm('Tem certeza que deseja remover este item do carrinho?')) {
                e.preventDefault();
            }
        });

        // Continue shopping button
        $(document).on('click', '.continue-shopping', function(e) {
            e.preventDefault();
            window.history.back();
        });
    }

    /**
     * Checkout Validation
     */
    function initCheckoutValidation() {
        // Real-time validation
        $('.checkout .input-text').on('blur', function() {
            validateField($(this));
        });

        // Form submission validation
        $('.checkout form').on('submit', function(e) {
            var isValid = true;
            
            $(this).find('.input-text[required]').each(function() {
                if (!validateField($(this))) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                showNotification('Por favor, corrija os erros no formulário', 'error');
            }
        });
    }

    /**
     * Validate individual field
     */
    function validateField($field) {
        var value = $field.val().trim(),
            fieldType = $field.attr('type'),
            isValid = true,
            errorMessage = '';

        // Required field validation
        if ($field.prop('required') && !value) {
            isValid = false;
            errorMessage = 'Este campo é obrigatório';
        }

        // Email validation
        if (fieldType === 'email' && value && !isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Por favor, insira um email válido';
        }

        // Phone validation
        if ($field.attr('name') === 'billing_phone' && value && !isValidPhone(value)) {
            isValid = false;
            errorMessage = 'Por favor, insira um telefone válido';
        }

        // CPF validation (Brazilian tax ID)
        if ($field.attr('name') === 'billing_cpf' && value && !isValidCPF(value)) {
            isValid = false;
            errorMessage = 'Por favor, insira um CPF válido';
        }

        // Update field state
        if (isValid) {
            $field.removeClass('error').addClass('valid');
            $field.next('.error-message').remove();
        } else {
            $field.removeClass('valid').addClass('error');
            $field.next('.error-message').remove();
            $field.after('<span class="error-message">' + errorMessage + '</span>');
        }

        return isValid;
    }

    /**
     * Product Quick View
     */
    function initProductQuickView() {
        $(document).on('click', '.quick-view-button', function(e) {
            e.preventDefault();
            
            var productId = $(this).data('product-id');
            openQuickView(productId);
        });
    }

    /**
     * Open quick view modal
     */
    function openQuickView(productId) {
        // Create modal if it doesn't exist
        if (!$('#quick-view-modal').length) {
            $('body').append('<div id="quick-view-modal" class="modal"><div class="modal-content"><span class="close">&times;</span><div class="modal-body"></div></div></div>');
        }

        var $modal = $('#quick-view-modal');
        
        // Show loading
        $modal.find('.modal-body').html('<div class="loading">Carregando...</div>');
        $modal.show();

        // In a real implementation, this would be an AJAX call to get product data
        setTimeout(function() {
            $modal.find('.modal-body').html(`
                <div class="quick-view-content">
                    <div class="product-image">
                        <img src="https://via.placeholder.com/400x400" alt="Produto">
                    </div>
                    <div class="product-details">
                        <h3>Nome do Produto</h3>
                        <div class="price">R$ 99,90</div>
                        <div class="description">Descrição breve do produto...</div>
                        <form class="cart">
                            <div class="quantity">
                                <input type="number" class="qty" value="1" min="1">
                            </div>
                            <button type="submit" class="button">Adicionar ao Carrinho</button>
                        </form>
                    </div>
                </div>
            `);
        }, 500);

        // Close modal
        $modal.find('.close').on('click', function() {
            $modal.hide();
        });

        $(window).on('click', function(e) {
            if (e.target === $modal[0]) {
                $modal.hide();
            }
        });
    }

    /**
     * Product Comparison
     */
    function initProductComparison() {
        $(document).on('click', '.compare-button', function(e) {
            e.preventDefault();
            
            var productId = $(this).data('product-id');
            toggleProductComparison(productId);
        });

        // Show comparison table
        $(document).on('click', '.view-comparison', function(e) {
            e.preventDefault();
            showComparisonTable();
        });
    }

    /**
     * Toggle product in comparison
     */
    function toggleProductComparison(productId) {
        var comparison = JSON.parse(localStorage.getItem('nosfirnews_comparison') || '[]');
        var index = comparison.indexOf(productId);
        
        if (index > -1) {
            comparison.splice(index, 1);
            showNotification('Produto removido da comparação', 'info');
        } else {
            if (comparison.length >= 4) {
                showNotification('Máximo de 4 produtos para comparação', 'warning');
                return;
            }
            comparison.push(productId);
            showNotification('Produto adicionado à comparação', 'success');
        }
        
        localStorage.setItem('nosfirnews_comparison', JSON.stringify(comparison));
        updateComparisonCounter();
    }

    /**
     * Update comparison counter
     */
    function updateComparisonCounter() {
        var comparison = JSON.parse(localStorage.getItem('nosfirnews_comparison') || '[]');
        $('.comparison-counter').text(comparison.length);
        
        if (comparison.length > 0) {
            $('.view-comparison').show();
        } else {
            $('.view-comparison').hide();
        }
    }

    /**
     * Show comparison table
     */
    function showComparisonTable() {
        // In a real implementation, this would fetch product data and show a comparison table
        alert('Funcionalidade de comparação será implementada em breve!');
    }

    /**
     * Utility Functions
     */

    /**
     * Validate email format
     */
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Validate phone format
     */
    function isValidPhone(phone) {
        var phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(phone.replace(/\D/g, ''));
    }

    /**
     * Validate CPF (Brazilian tax ID)
     */
    function isValidCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
            return false;
        }
        
        var sum = 0;
        for (var i = 0; i < 9; i++) {
            sum += parseInt(cpf.charAt(i)) * (10 - i);
        }
        
        var remainder = 11 - (sum % 11);
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpf.charAt(9))) return false;
        
        sum = 0;
        for (var i = 0; i < 10; i++) {
            sum += parseInt(cpf.charAt(i)) * (11 - i);
        }
        
        remainder = 11 - (sum % 11);
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpf.charAt(10))) return false;
        
        return true;
    }

    /**
     * Show notification
     */
    function showNotification(message, type) {
        type = type || 'info';
        
        var $notification = $('<div class="notification notification-' + type + '">' + message + '</div>');
        
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.addClass('show');
        }, 100);
        
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }

    // Initialize comparison counter on page load
    updateComparisonCounter();

})(jQuery);