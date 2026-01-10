<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class SecondNav extends Abstract_Component {
    public function render() { wp_nav_menu( [ 'theme_location' => 'secondary', 'fallback_cb' => function(){ wp_nav_menu( [ 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container' => false ] ); }, 'menu_class' => 'nav-menu', 'container' => false ] ); }
}