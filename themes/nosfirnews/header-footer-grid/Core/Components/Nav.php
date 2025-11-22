<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class Nav extends Abstract_Component {
    public function render() { wp_nav_menu( [ 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container' => false ] ); }
}