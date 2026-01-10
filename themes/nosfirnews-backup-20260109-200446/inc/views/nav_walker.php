<?php
class NosfirNews_Nav_Walker extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? [] : (array) $item->classes;
        $class_names = join( ' ', array_map( 'esc_attr', $classes ) );
        $has_children = in_array( 'menu-item-has-children', $classes, true );
        $output .= '<li class="menu-item ' . $class_names . '">';
        $output .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
        if ( $has_children ) {
            $output .= '<button class="submenu-toggle" aria-expanded="false">\u25BC</button>';
        }
    }
    public function end_el( &$output, $item, $depth = 0, $args = null ) { $output .= '</li>'; }
}
