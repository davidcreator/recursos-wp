<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class Nav extends Abstract_Component {
    public function render() {
        $location = get_theme_mod( 'nosfirnews_primary_menu_location', 'primary' );
        wp_nav_menu( [ 'theme_location' => $location, 'menu_class' => 'nav-menu', 'container' => false, 'walker' => new \NosfirNews_Nav_Walker() ] );
    }
}
