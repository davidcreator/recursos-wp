<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @package NosfirNews
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'nosfirnews' ) ) );
    return;
}
?>

<div class="checkout-page-wrapper">
    <div class="container">
        <div class="checkout-header">
            <h1 class="checkout-title"><?php esc_html_e( 'Checkout', 'nosfirnews' ); ?></h1>
            <div class="checkout-steps">
                <div class="step completed">
                    <span class="step-number">1</span>
                    <span class="step-label"><?php esc_html_e( 'Cart', 'nosfirnews' ); ?></span>
                </div>
                <div class="step active">
                    <span class="step-number">2</span>
                    <span class="step-label"><?php esc_html_e( 'Checkout', 'nosfirnews' ); ?></span>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span class="step-label"><?php esc_html_e( 'Complete', 'nosfirnews' ); ?></span>
                </div>
            </div>
        </div>

        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

            <div class="checkout-content">
                <div class="checkout-form">
                    <?php if ( $checkout->get_checkout_fields() ) : ?>

                        <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                        <div class="checkout-sections">
                            <div class="billing-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user"></i>
                                    <?php esc_html_e( 'Billing Details', 'nosfirnews' ); ?>
                                </h3>
                                <div class="section-content">
                                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                                </div>
                            </div>

                            <div class="shipping-section">
                                <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                            </div>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                    <?php endif; ?>
                    
                    <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
                    
                    <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                    <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                </div>

                <div class="checkout-sidebar">
                    <div class="order-review-section">
                        <h3 class="section-title">
                            <i class="fas fa-shopping-bag"></i>
                            <?php esc_html_e( 'Your Order', 'nosfirnews' ); ?>
                        </h3>
                        
                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                        </div>
                    </div>

                    <div class="checkout-security">
                        <div class="security-badges">
                            <div class="security-item">
                                <i class="fas fa-shield-alt"></i>
                                <span><?php esc_html_e( 'SSL Secure Checkout', 'nosfirnews' ); ?></span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-lock"></i>
                                <span><?php esc_html_e( 'Your data is protected', 'nosfirnews' ); ?></span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-credit-card"></i>
                                <span><?php esc_html_e( 'Secure payments', 'nosfirnews' ); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="payment-methods-info">
                        <h4><?php esc_html_e( 'We Accept', 'nosfirnews' ); ?></h4>
                        <div class="payment-icons">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-amex"></i>
                            <i class="fab fa-paypal"></i>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
    </div>
</div>

<style>
.checkout-page-wrapper {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 70vh;
}

.checkout-header {
    text-align: center;
    margin-bottom: 3rem;
}

.checkout-title {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 2rem;
}

.checkout-steps {
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

.step.completed {
    color: #28a745;
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

.step.completed .step-number {
    background: #28a745;
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

.step.completed:not(:last-child)::after {
    background: #28a745;
}

.checkout-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    align-items: start;
}

.checkout-form {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
}

.checkout-sections {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.3rem;
    color: #333;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #eee;
}

.section-title i {
    color: #1a73e8;
    font-size: 1.1rem;
}

.section-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.woocommerce-input-wrapper {
    position: relative;
}

.checkout-form input[type="text"],
.checkout-form input[type="email"],
.checkout-form input[type="tel"],
.checkout-form input[type="password"],
.checkout-form select,
.checkout-form textarea {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.checkout-form input:focus,
.checkout-form select:focus,
.checkout-form textarea:focus {
    outline: none;
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
}

.checkout-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.checkout-form .required::after {
    content: ' *';
    color: #dc3545;
}

.checkout-form .form-row {
    margin-bottom: 1.5rem;
}

.checkout-form .form-row-wide {
    grid-column: 1 / -1;
}

.checkout-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.order-review-section {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
}

.woocommerce-checkout-review-order-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}

.woocommerce-checkout-review-order-table th,
.woocommerce-checkout-review-order-table td {
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
    text-align: left;
}

.woocommerce-checkout-review-order-table .product-name {
    font-weight: 500;
    color: #333;
}

.woocommerce-checkout-review-order-table .product-total {
    text-align: right;
    font-weight: 600;
    color: #1a73e8;
}

.order-total th,
.order-total td {
    font-size: 1.2rem;
    font-weight: bold;
    color: #1a73e8;
    border-bottom: none;
    padding-top: 1.5rem;
}

.order-total td {
    text-align: right;
}

.wc_payment_methods {
    list-style: none;
    padding: 0;
    margin: 1.5rem 0;
}

.wc_payment_method {
    margin-bottom: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.wc_payment_method:hover {
    border-color: #1a73e8;
}

.wc_payment_method input[type="radio"] {
    display: none;
}

.wc_payment_method label {
    display: block;
    padding: 1rem;
    cursor: pointer;
    position: relative;
    background: #fff;
    transition: all 0.3s ease;
}

.wc_payment_method input[type="radio"]:checked + label {
    background: #f8f9ff;
    border-left: 4px solid #1a73e8;
}

.wc_payment_method label::before {
    content: '';
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    border: 2px solid #ddd;
    border-radius: 50%;
    background: #fff;
}

.wc_payment_method input[type="radio"]:checked + label::before {
    border-color: #1a73e8;
    background: #1a73e8;
    box-shadow: inset 0 0 0 4px #fff;
}

.payment_box {
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    color: #666;
    font-size: 0.9rem;
}

#place_order {
    width: 100%;
    background: #28a745;
    color: #fff;
    border: none;
    padding: 1.25rem 2rem;
    font-size: 1.1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 0.5px;
    margin-top: 1rem;
}

#place_order:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.checkout-security {
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

.payment-methods-info {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
    text-align: center;
}

.payment-methods-info h4 {
    margin-bottom: 1rem;
    color: #333;
}

.payment-icons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.payment-icons i {
    font-size: 2rem;
    color: #666;
    transition: color 0.3s ease;
}

.payment-icons i:hover {
    color: #1a73e8;
}

/* Shipping Section */
.shipping-section {
    display: none;
}

.shipping-section.show {
    display: block;
}

.ship-to-different-address {
    margin-bottom: 1.5rem;
}

.ship-to-different-address input[type="checkbox"] {
    margin-right: 0.5rem;
}

.ship-to-different-address label {
    cursor: pointer;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .checkout-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .checkout-steps {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    
    .step:not(:last-child)::after {
        display: none;
    }
    
    .section-content {
        grid-template-columns: 1fr;
    }
    
    .checkout-form,
    .order-review-section,
    .checkout-security,
    .payment-methods-info {
        padding: 1.5rem;
    }
    
    .payment-icons {
        gap: 0.5rem;
    }
    
    .payment-icons i {
        font-size: 1.5rem;
    }
}

/* Loading State */
.processing {
    opacity: 0.6;
    pointer-events: none;
}

.processing #place_order::after {
    content: '';
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #fff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
    margin-left: 0.5rem;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Error Messages */
.woocommerce-error,
.woocommerce-message,
.woocommerce-info {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid;
}

.woocommerce-error {
    background: #f8d7da;
    border-left-color: #dc3545;
    color: #721c24;
}

.woocommerce-message {
    background: #d4edda;
    border-left-color: #28a745;
    color: #155724;
}

.woocommerce-info {
    background: #cce7ff;
    border-left-color: #1a73e8;
    color: #004085;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle shipping to different address
    const shipToDifferentAddress = document.getElementById('ship-to-different-address-checkbox');
    const shippingSection = document.querySelector('.shipping-section');
    
    if (shipToDifferentAddress && shippingSection) {
        function toggleShippingSection() {
            if (shipToDifferentAddress.checked) {
                shippingSection.classList.add('show');
            } else {
                shippingSection.classList.remove('show');
            }
        }
        
        shipToDifferentAddress.addEventListener('change', toggleShippingSection);
        toggleShippingSection(); // Initial state
    }
    
    // Form validation enhancement
    const checkoutForm = document.querySelector('.checkout');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const requiredFields = checkoutForm.querySelectorAll('[required]');
            let hasErrors = false;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    hasErrors = true;
                } else {
                    field.style.borderColor = '#e9ecef';
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                document.querySelector('.checkout-form').scrollIntoView({
                    behavior: 'smooth'
                });
            } else {
                // Add loading state
                checkoutForm.classList.add('processing');
            }
        });
    }
    
    // Real-time field validation
    const formFields = document.querySelectorAll('.checkout input, .checkout select, .checkout textarea');
    formFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#e9ecef';
            }
        });
        
        field.addEventListener('input', function() {
            if (this.style.borderColor === 'rgb(220, 53, 69)' && this.value.trim()) {
                this.style.borderColor = '#e9ecef';
            }
        });
    });
    
    // Payment method selection enhancement
    const paymentMethods = document.querySelectorAll('.wc_payment_method input[type="radio"]');
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Hide all payment boxes
            document.querySelectorAll('.payment_box').forEach(box => {
                box.style.display = 'none';
            });
            
            // Show selected payment box
            const paymentBox = this.closest('.wc_payment_method').querySelector('.payment_box');
            if (paymentBox) {
                paymentBox.style.display = 'block';
            }
        });
    });
    
    // Initialize first payment method
    if (paymentMethods.length > 0) {
        paymentMethods[0].checked = true;
        paymentMethods[0].dispatchEvent(new Event('change'));
    }
});
</script>