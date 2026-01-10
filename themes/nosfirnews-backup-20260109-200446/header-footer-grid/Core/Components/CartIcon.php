<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class CartIcon extends Abstract_Component {
    public function render() {
        if ( class_exists( '\\WooCommerce' ) && function_exists('wc_get_cart_url') ) {
            $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
            echo '<a class="nn-btn" href="'.esc_url( wc_get_cart_url() ).'">'.esc_html__('Carrinho','nosfirnews').' ('.intval($count).')</a>';
        }
    }
}