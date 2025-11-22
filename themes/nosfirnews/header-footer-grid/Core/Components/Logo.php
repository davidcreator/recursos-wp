<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class Logo extends Abstract_Component {
    public function render() {
        if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) { the_custom_logo(); return; }
        echo '<a class="site-title" href="'.esc_url( home_url('/') ).'">'.esc_html( get_bloginfo('name') ).'</a>';
    }
}