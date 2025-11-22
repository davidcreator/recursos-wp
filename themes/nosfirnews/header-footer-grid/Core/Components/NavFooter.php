<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class NavFooter extends Abstract_Component {
    public function render() { wp_nav_menu( [ 'theme_location' => 'footer', 'menu_class' => 'nav-menu', 'container' => false ] ); }
}