<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class EddCartIcon extends Abstract_Component {
    public function render() {
        if ( function_exists('edd_get_checkout_uri') && function_exists('edd_get_cart_quantity') ) {
            $count = edd_get_cart_quantity();
            echo '<a class="nn-btn" href="'.esc_url( edd_get_checkout_uri() ).'">'.esc_html__('Carrinho','nosfirnews').' ('.intval($count).')</a>';
        }
    }
}