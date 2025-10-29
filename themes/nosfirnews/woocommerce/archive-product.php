<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * @package NosfirNews
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); ?>

<div class="woocommerce-shop-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-8">
                <div class="shop-content">
                    <?php
                    /**
                     * Hook: woocommerce_before_main_content.
                     *
                     * @hooked woocommerce_output_content_wrapper - 10 (removed)
                     * @hooked woocommerce_breadcrumb - 20
                     */
                    do_action( 'woocommerce_before_main_content' );
                    ?>

                    <header class="woocommerce-products-header">
                        <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
                            <h1 class="woocommerce-products-header__title page-title">
                                <?php woocommerce_page_title(); ?>
                            </h1>
                        <?php endif; ?>

                        <?php
                        /**
                         * Hook: woocommerce_archive_description.
                         *
                         * @hooked woocommerce_taxonomy_archive_description - 10
                         * @hooked woocommerce_product_archive_description - 10
                         */
                        do_action( 'woocommerce_archive_description' );
                        ?>
                    </header>

                    <div class="shop-toolbar">
                        <div class="shop-toolbar-left">
                            <?php
                            /**
                             * Hook: woocommerce_before_shop_loop.
                             *
                             * @hooked woocommerce_output_all_notices - 10
                             * @hooked woocommerce_result_count - 20
                             * @hooked woocommerce_catalog_ordering - 30
                             */
                            do_action( 'woocommerce_before_shop_loop' );
                            ?>
                        </div>
                        
                        <div class="shop-view-toggle">
                            <button class="view-grid active" data-view="grid" title="<?php esc_attr_e( 'Grid View', 'nosfirnews' ); ?>">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-list" data-view="list" title="<?php esc_attr_e( 'List View', 'nosfirnews' ); ?>">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    <?php
                    if ( woocommerce_product_loop() ) {
                        /**
                         * Hook: woocommerce_before_shop_loop.
                         *
                         * @hooked woocommerce_output_all_notices - 10
                         * @hooked woocommerce_result_count - 20
                         * @hooked woocommerce_catalog_ordering - 30
                         */
                        // do_action( 'woocommerce_before_shop_loop' ); // Already called above

                        woocommerce_product_loop_start();

                        if ( wc_get_loop_prop( 'is_shortcode' ) ) {
                            $columns = absint( wc_get_loop_prop( 'columns' ) );
                        } else {
                            $columns = wc_get_default_products_per_row();
                        }

                        while ( have_posts() ) {
                            the_post();

                            /**
                             * Hook: woocommerce_shop_loop.
                             */
                            do_action( 'woocommerce_shop_loop' );

                            wc_get_template_part( 'content', 'product' );
                        }

                        woocommerce_product_loop_end();

                        /**
                         * Hook: woocommerce_after_shop_loop.
                         *
                         * @hooked woocommerce_pagination - 10
                         */
                        do_action( 'woocommerce_after_shop_loop' );
                    } else {
                        /**
                         * Hook: woocommerce_no_products_found.
                         *
                         * @hooked wc_no_products_found - 10
                         */
                        do_action( 'woocommerce_no_products_found' );
                    }

                    /**
                     * Hook: woocommerce_after_main_content.
                     *
                     * @hooked woocommerce_output_content_wrapper_end - 10 (removed)
                     */
                    do_action( 'woocommerce_after_main_content' );
                    ?>
                </div>
            </div>

            <div class="col-lg-3 col-md-4">
                <aside class="shop-sidebar">
                    <?php
                    /**
                     * Hook: woocommerce_sidebar.
                     *
                     * @hooked woocommerce_get_sidebar - 10
                     */
                    do_action( 'woocommerce_sidebar' );
                    ?>
                </aside>
            </div>
        </div>
    </div>
</div>

<style>
.woocommerce-shop-page {
    padding: 2rem 0;
    background: #f8f9fa;
}

.shop-content {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.woocommerce-products-header {
    margin-bottom: 2rem;
    text-align: center;
}

.woocommerce-products-header__title {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 1rem;
}

.shop-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.shop-view-toggle {
    display: flex;
    gap: 0.5rem;
}

.shop-view-toggle button {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    background: #fff;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.shop-view-toggle button.active,
.shop-view-toggle button:hover {
    background: #1a73e8;
    color: #fff;
    border-color: #1a73e8;
}

.shop-sidebar {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: fit-content;
}

/* Product Grid Styles */
.products {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.product {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.product:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.product img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product:hover img {
    transform: scale(1.05);
}

.product .woocommerce-loop-product__title {
    font-size: 1.1rem;
    margin: 1rem;
    color: #333;
}

.product .price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #1a73e8;
    margin: 0 1rem 1rem;
}

.product .button {
    width: calc(100% - 2rem);
    margin: 0 1rem 1rem;
    background: #1a73e8;
    color: #fff;
    border: none;
    padding: 0.75rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.product .button:hover {
    background: #1557b0;
}

/* List View Styles */
.products.list-view {
    grid-template-columns: 1fr;
}

.products.list-view .product {
    display: flex;
    align-items: center;
    padding: 1rem;
}

.products.list-view .product img {
    width: 150px;
    height: 150px;
    margin-right: 2rem;
    flex-shrink: 0;
}

.products.list-view .product-info {
    flex: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .shop-toolbar {
        flex-direction: column;
        gap: 1rem;
    }
    
    .products {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .products.list-view .product {
        flex-direction: column;
        text-align: center;
    }
    
    .products.list-view .product img {
        margin-right: 0;
        margin-bottom: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.shop-view-toggle button');
    const productsContainer = document.querySelector('.products');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update active button
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Update products container
            if (productsContainer) {
                productsContainer.classList.toggle('list-view', view === 'list');
            }
            
            // Store preference
            localStorage.setItem('shop_view', view);
        });
    });
    
    // Restore saved view preference
    const savedView = localStorage.getItem('shop_view');
    if (savedView && productsContainer) {
        const button = document.querySelector(`[data-view="${savedView}"]`);
        if (button) {
            button.click();
        }
    }
});
</script>

<?php
get_footer( 'shop' );