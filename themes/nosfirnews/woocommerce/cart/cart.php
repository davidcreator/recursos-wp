<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * @package NosfirNews
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="cart-page-wrapper">
    <div class="container">
        <div class="cart-header">
            <h1 class="cart-title"><?php esc_html_e( 'Shopping Cart', 'nosfirnews' ); ?></h1>
            <div class="cart-steps">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span class="step-label"><?php esc_html_e( 'Cart', 'nosfirnews' ); ?></span>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <span class="step-label"><?php esc_html_e( 'Checkout', 'nosfirnews' ); ?></span>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span class="step-label"><?php esc_html_e( 'Complete', 'nosfirnews' ); ?></span>
                </div>
            </div>
        </div>

        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <div class="cart-content">
                <div class="cart-items">
                    <table class="shop_table shop_table_responsive cart woocommerce-cart-table__table" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="product-thumbnail"><?php esc_html_e( 'Product', 'nosfirnews' ); ?></th>
                                <th class="product-name">&nbsp;</th>
                                <th class="product-price"><?php esc_html_e( 'Price', 'nosfirnews' ); ?></th>
                                <th class="product-quantity"><?php esc_html_e( 'Quantity', 'nosfirnews' ); ?></th>
                                <th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'nosfirnews' ); ?></th>
                                <th class="product-remove">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                            <?php
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                                $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                    ?>
                                    <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                        <td class="product-thumbnail">
                                            <?php
                                            $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                            if ( ! $product_permalink ) {
                                                echo $thumbnail; // PHPCS: XSS ok.
                                            } else {
                                                printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                            }
                                            ?>
                                        </td>

                                        <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'nosfirnews' ); ?>">
                                            <?php
                                            if ( ! $product_permalink ) {
                                                echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                            } else {
                                                echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                            }

                                            do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                            // Meta data.
                                            echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

                                            // Backorder notification.
                                            if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                                echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'nosfirnews' ) . '</p>', $product_id ) );
                                            }
                                            ?>
                                        </td>

                                        <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'nosfirnews' ); ?>">
                                            <?php
                                                echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                            ?>
                                        </td>

                                        <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'nosfirnews' ); ?>">
                                            <?php
                                            if ( $_product->is_sold_individually() ) {
                                                $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                            } else {
                                                $product_quantity = woocommerce_quantity_input(
                                                    array(
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => $cart_item['quantity'],
                                                        'max_value'    => $_product->get_max_purchase_quantity(),
                                                        'min_value'    => '0',
                                                        'product_name' => $_product->get_name(),
                                                    ),
                                                    $_product,
                                                    false
                                                );
                                            }

                                            echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
                                            ?>
                                        </td>

                                        <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'nosfirnews' ); ?>">
                                            <?php
                                                echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                            ?>
                                        </td>

                                        <td class="product-remove">
                                            <?php
                                                echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                    'woocommerce_cart_item_remove_link',
                                                    sprintf(
                                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fas fa-times"></i></a>',
                                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                        esc_html__( 'Remove this item', 'nosfirnews' ),
                                                        esc_attr( $product_id ),
                                                        esc_attr( $_product->get_sku() )
                                                    ),
                                                    $cart_item_key
                                                );
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                            <?php do_action( 'woocommerce_cart_contents' ); ?>

                            <tr>
                                <td colspan="6" class="actions">
                                    <div class="cart-actions">
                                        <?php if ( wc_coupons_enabled() ) { ?>
                                            <div class="coupon">
                                                <label for="coupon_code"><?php esc_html_e( 'Coupon:', 'nosfirnews' ); ?></label>
                                                <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'nosfirnews' ); ?>" />
                                                <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'nosfirnews' ); ?>"><?php esc_html_e( 'Apply coupon', 'nosfirnews' ); ?></button>
                                                <?php do_action( 'woocommerce_cart_coupon' ); ?>
                                            </div>
                                        <?php } ?>

                                        <div class="cart-buttons">
                                            <button type="submit" class="button update-cart" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'nosfirnews' ); ?>"><?php esc_html_e( 'Update cart', 'nosfirnews' ); ?></button>
                                            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="button continue-shopping"><?php esc_html_e( 'Continue Shopping', 'nosfirnews' ); ?></a>
                                        </div>
                                    </div>

                                    <?php do_action( 'woocommerce_cart_actions' ); ?>

                                    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                                </td>
                            </tr>

                            <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-sidebar">
                    <div class="cart-totals">
                        <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

                        <div class="cart-collaterals">
                            <?php
                                /**
                                 * Cart collaterals hook.
                                 *
                                 * @hooked woocommerce_cart_totals - 10
                                 * @hooked woocommerce_shipping_calculator - 20
                                 */
                                do_action( 'woocommerce_cart_collaterals' );
                            ?>
                        </div>
                    </div>

                    <div class="cart-security">
                        <div class="security-badges">
                            <div class="security-item">
                                <i class="fas fa-shield-alt"></i>
                                <span><?php esc_html_e( 'Secure Checkout', 'nosfirnews' ); ?></span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-truck"></i>
                                <span><?php esc_html_e( 'Free Shipping', 'nosfirnews' ); ?></span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-undo"></i>
                                <span><?php esc_html_e( 'Easy Returns', 'nosfirnews' ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </form>

        <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
    </div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>

<style>
.cart-page-wrapper {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 70vh;
}

.cart-header {
    text-align: center;
    margin-bottom: 3rem;
}

.cart-title {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 2rem;
}

.cart-steps {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.step {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #999;
    position: relative;
}

.step.active {
    color: #1a73e8;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.step.active .step-number {
    background: #1a73e8;
    color: #fff;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -1.25rem;
    top: 50%;
    transform: translateY(-50%);
    width: 0.5rem;
    height: 2px;
    background: #ddd;
}

.cart-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    align-items: start;
}

.cart-items {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.shop_table {
    width: 100%;
    border-collapse: collapse;
}

.shop_table thead {
    background: #f8f9fa;
}

.shop_table th {
    padding: 1.5rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #eee;
}

.shop_table td {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.product-thumbnail img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.product-name a {
    color: #333;
    text-decoration: none;
    font-weight: 500;
}

.product-name a:hover {
    color: #1a73e8;
}

.product-price,
.product-subtotal {
    font-weight: 600;
    color: #1a73e8;
    font-size: 1.1rem;
}

.product-quantity .quantity {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    width: 120px;
}

.product-quantity .quantity input {
    width: 60px;
    text-align: center;
    border: none;
    padding: 0.5rem;
    font-size: 1rem;
}

.product-quantity .qty-btn {
    background: #f8f9fa;
    border: none;
    padding: 0.5rem;
    cursor: pointer;
    transition: background 0.3s ease;
    width: 30px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-quantity .qty-btn:hover {
    background: #e9ecef;
}

.product-remove .remove {
    color: #dc3545;
    font-size: 1.2rem;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.product-remove .remove:hover {
    background: #dc3545;
    color: #fff;
}

.actions {
    background: #f8f9fa;
    padding: 2rem !important;
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.coupon {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.coupon input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
}

.coupon button,
.cart-buttons .button {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.coupon button,
.update-cart {
    background: #1a73e8;
    color: #fff;
}

.coupon button:hover,
.update-cart:hover {
    background: #1557b0;
}

.continue-shopping {
    background: #6c757d;
    color: #fff;
}

.continue-shopping:hover {
    background: #545b62;
}

.cart-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.cart-totals {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
}

.cart_totals h2 {
    margin-bottom: 1.5rem;
    color: #333;
    font-size: 1.5rem;
}

.cart_totals table {
    width: 100%;
}

.cart_totals th,
.cart_totals td {
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.cart_totals th {
    text-align: left;
    font-weight: 500;
    color: #666;
}

.cart_totals td {
    text-align: right;
    font-weight: 600;
    color: #333;
}

.order-total th,
.order-total td {
    font-size: 1.2rem;
    color: #1a73e8;
    border-bottom: none;
    padding-top: 1.5rem;
}

.wc-proceed-to-checkout {
    margin-top: 1.5rem;
}

.checkout-button {
    width: 100%;
    background: #28a745;
    color: #fff;
    border: none;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 0.5px;
}

.checkout-button:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.cart-security {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
}

.security-badges {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.security-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #666;
}

.security-item i {
    color: #28a745;
    font-size: 1.2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .cart-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .cart-steps {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    
    .step:not(:last-child)::after {
        display: none;
    }
    
    .shop_table,
    .shop_table thead,
    .shop_table tbody,
    .shop_table th,
    .shop_table td,
    .shop_table tr {
        display: block;
    }
    
    .shop_table thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    
    .shop_table tr {
        border: 1px solid #ccc;
        margin-bottom: 1rem;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .shop_table td {
        border: none;
        position: relative;
        padding-left: 50%;
    }
    
    .shop_table td:before {
        content: attr(data-title) ": ";
        position: absolute;
        left: 1rem;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        font-weight: bold;
        color: #333;
    }
    
    .cart-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .coupon {
        flex-direction: column;
        align-items: stretch;
    }
    
    .cart-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
}

/* Empty Cart Styles */
.cart-empty {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.cart-empty .wc-empty-cart-message {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 2rem;
}

.cart-empty .return-to-shop {
    background: #1a73e8;
    color: #fff;
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.cart-empty .return-to-shop:hover {
    background: #1557b0;
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons for cart
    const quantityInputs = document.querySelectorAll('.product-quantity .quantity input[type="number"]');
    
    quantityInputs.forEach(input => {
        const wrapper = input.closest('.quantity');
        if (!wrapper || wrapper.querySelector('.qty-btn')) return;
        
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
            const minValue = parseInt(input.min) || 0;
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
    
    // Auto-update cart on quantity change
    const cartForm = document.querySelector('.woocommerce-cart-form');
    if (cartForm) {
        let updateTimeout;
        
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
                    const updateButton = cartForm.querySelector('[name="update_cart"]');
                    if (updateButton) {
                        updateButton.click();
                    }
                }, 1000);
            });
        });
    }
    
    // Smooth scroll to cart after update
    if (window.location.hash === '#cart-updated') {
        setTimeout(() => {
            document.querySelector('.cart-page-wrapper').scrollIntoView({
                behavior: 'smooth'
            });
        }, 100);
    }
});
</script>