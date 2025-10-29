<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * @package NosfirNews
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); ?>

<div class="single-product-page">
    <div class="container">
        <?php
        /**
         * woocommerce_before_main_content hook.
         *
         * @hooked woocommerce_output_content_wrapper - 10 (removed)
         * @hooked woocommerce_breadcrumb - 20
         */
        do_action( 'woocommerce_before_main_content' );
        ?>

        <?php while ( have_posts() ) : ?>
            <?php the_post(); ?>

            <div class="product-container">
                <?php wc_get_template_part( 'content', 'single-product' ); ?>
            </div>

        <?php endwhile; // end of the loop. ?>

        <?php
        /**
         * woocommerce_after_main_content hook.
         *
         * @hooked woocommerce_output_content_wrapper_end - 10 (removed)
         */
        do_action( 'woocommerce_after_main_content' );
        ?>

        <?php
        /**
         * woocommerce_sidebar hook.
         *
         * @hooked woocommerce_get_sidebar - 10
         */
        // do_action( 'woocommerce_sidebar' );
        ?>
    </div>
</div>

<style>
.single-product-page {
    padding: 2rem 0;
    background: #f8f9fa;
}

.product-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* Product Summary Styles */
.single-product div.product {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    padding: 3rem;
    align-items: start;
}

.single-product .woocommerce-product-gallery {
    position: relative;
}

.single-product .woocommerce-product-gallery__wrapper {
    border-radius: 8px;
    overflow: hidden;
}

.single-product .woocommerce-product-gallery__image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.single-product .summary {
    padding-left: 2rem;
}

.single-product .product_title {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.single-product .price {
    font-size: 2rem;
    font-weight: bold;
    color: #1a73e8;
    margin-bottom: 1.5rem;
}

.single-product .price del {
    color: #999;
    font-size: 1.5rem;
}

.single-product .woocommerce-product-details__short-description {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #666;
    margin-bottom: 2rem;
}

.single-product .cart {
    margin-bottom: 2rem;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #eee;
}

.single-product .quantity {
    display: inline-flex;
    align-items: center;
    margin-right: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}

.single-product .quantity input {
    width: 60px;
    text-align: center;
    border: none;
    padding: 0.75rem 0.5rem;
    font-size: 1rem;
}

.single-product .quantity .qty-btn {
    background: #f8f9fa;
    border: none;
    padding: 0.75rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.single-product .quantity .qty-btn:hover {
    background: #e9ecef;
}

.single-product .single_add_to_cart_button {
    background: #1a73e8;
    color: #fff;
    border: none;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 0.5px;
}

.single-product .single_add_to_cart_button:hover {
    background: #1557b0;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(26, 115, 232, 0.3);
}

.single-product .product_meta {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.single-product .product_meta span {
    display: block;
    margin-bottom: 0.5rem;
    color: #666;
}

.single-product .product_meta a {
    color: #1a73e8;
    text-decoration: none;
}

.single-product .product_meta a:hover {
    text-decoration: underline;
}

/* Product Tabs */
.woocommerce-tabs {
    margin-top: 3rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.woocommerce-tabs .wc-tabs {
    display: flex;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
    margin: 0;
    padding: 0;
    list-style: none;
}

.woocommerce-tabs .wc-tabs li {
    flex: 1;
}

.woocommerce-tabs .wc-tabs li a {
    display: block;
    padding: 1.5rem;
    text-align: center;
    color: #666;
    text-decoration: none;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.woocommerce-tabs .wc-tabs li.active a,
.woocommerce-tabs .wc-tabs li a:hover {
    color: #1a73e8;
    border-bottom-color: #1a73e8;
    background: #fff;
}

.woocommerce-tabs .wc-tab {
    padding: 2rem;
    display: none;
}

.woocommerce-tabs .wc-tab.active {
    display: block;
}

/* Related Products */
.related.products {
    margin-top: 4rem;
    padding: 3rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.related.products h2 {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2rem;
    color: #333;
}

.related.products .products {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.related.products .product {
    text-align: center;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.related.products .product:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.related.products .product img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .single-product div.product {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 2rem;
    }
    
    .single-product .summary {
        padding-left: 0;
    }
    
    .single-product .product_title {
        font-size: 2rem;
    }
    
    .single-product .price {
        font-size: 1.5rem;
    }
    
    .woocommerce-tabs .wc-tabs {
        flex-direction: column;
    }
    
    .related.products .products {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
}

/* Product Gallery Enhancements */
.woocommerce-product-gallery__trigger {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(0,0,0,0.7);
    color: #fff;
    border: none;
    padding: 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
}

.woocommerce-product-gallery__trigger:hover {
    background: rgba(0,0,0,0.9);
}

/* Stock Status */
.stock {
    font-weight: bold;
    margin-bottom: 1rem;
}

.stock.in-stock {
    color: #28a745;
}

.stock.out-of-stock {
    color: #dc3545;
}

/* Rating Stars */
.woocommerce-product-rating {
    margin-bottom: 1rem;
}

.star-rating {
    color: #ffc107;
}

/* Variations */
.variations {
    margin-bottom: 2rem;
}

.variations td {
    padding: 0.5rem 0;
    vertical-align: top;
}

.variations label {
    font-weight: bold;
    color: #333;
}

.variations select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons
    const quantityInputs = document.querySelectorAll('.quantity input[type="number"]');
    
    quantityInputs.forEach(input => {
        const wrapper = input.closest('.quantity');
        if (!wrapper) return;
        
        // Create + and - buttons
        const minusBtn = document.createElement('button');
        minusBtn.type = 'button';
        minusBtn.className = 'qty-btn minus';
        minusBtn.innerHTML = '-';
        
        const plusBtn = document.createElement('button');
        plusBtn.type = 'button';
        plusBtn.className = 'qty-btn plus';
        plusBtn.innerHTML = '+';
        
        // Insert buttons
        wrapper.insertBefore(minusBtn, input);
        wrapper.appendChild(plusBtn);
        
        // Add event listeners
        minusBtn.addEventListener('click', function() {
            const currentValue = parseInt(input.value) || 1;
            const minValue = parseInt(input.min) || 1;
            if (currentValue > minValue) {
                input.value = currentValue - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        
        plusBtn.addEventListener('click', function() {
            const currentValue = parseInt(input.value) || 1;
            const maxValue = parseInt(input.max) || 999;
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
    
    // Product tabs
    const tabLinks = document.querySelectorAll('.wc-tabs li a');
    const tabPanels = document.querySelectorAll('.wc-tab');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            
            // Remove active class from all tabs and panels
            tabLinks.forEach(l => l.parentElement.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding panel
            this.parentElement.classList.add('active');
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });
    
    // Initialize first tab as active
    if (tabLinks.length > 0) {
        tabLinks[0].click();
    }
});
</script>

<?php
get_footer( 'shop' );