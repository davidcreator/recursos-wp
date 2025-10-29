<?php
/**
 * Custom Walker for Navigation Menus
 * 
 * Extends the default WordPress Walker_Nav_Menu class to provide
 * enhanced menu functionality with modern HTML structure and
 * support for mega menus, icons, and responsive design.
 *
 * @package NosfirNews
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * NosfirNews Custom Walker Nav Menu Class
 */
class NosfirNews_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * What the class handles.
     *
     * @var string
     */
    public $tree_type = array( 'post_type', 'taxonomy', 'custom' );

    /**
     * Database fields to use.
     *
     * @var array
     */
    public $db_fields = array(
        'parent' => 'menu_item_parent',
        'id'     => 'db_id'
    );

    /**
     * Start Level - Start the list before the CHILD elements are added.
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );

        // Determine the submenu classes based on depth
        $classes = array( 'sub-menu' );
        if ( $depth === 0 ) {
            $classes[] = 'dropdown-menu';
        } elseif ( $depth === 1 ) {
            $classes[] = 'sub-dropdown-menu';
        } else {
            $classes[] = 'nested-dropdown';
        }

        $output .= "\n$indent<ul class=\"" . esc_attr( implode( ' ', $classes ) ) . "\">\n";
    }

    /**
     * End Level - End the list after the CHILD elements are added.
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }

    /**
     * Start Element - Start the element output.
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments.
     * @param int    $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Add custom classes based on menu item properties
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $classes[] = 'has-dropdown';
            if ( $depth === 0 ) {
                $classes[] = 'dropdown';
            }
        }

        // Add current item classes
        if ( in_array( 'current-menu-item', $classes ) ) {
            $classes[] = 'active';
        }

        if ( in_array( 'current-menu-parent', $classes ) || in_array( 'current-menu-ancestor', $classes ) ) {
            $classes[] = 'active-parent';
        }

        // Check for mega menu
        $is_mega_menu = get_post_meta( $item->ID, '_menu_item_mega_menu', true );
        if ( $is_mega_menu && $depth === 0 ) {
            // Align with theme styles: use consistent class names
            $classes[] = 'menu-item-mega-menu';
            $classes[] = 'mega-menu-item';
        }

        // Check for menu icon
        $menu_icon = get_post_meta( $item->ID, '_menu_item_icon', true );
        if ( $menu_icon ) {
            $classes[] = 'has-icon';
        }

        // Check for featured item
        $is_featured = get_post_meta( $item->ID, '_menu_item_featured', true );
        if ( $is_featured ) {
            $classes[] = 'featured-item';
        }

        // Check for button style
        $is_button = get_post_meta( $item->ID, '_menu_item_button', true );
        if ( $is_button ) {
            $classes[] = 'menu-button';
        }

        // Check for badge/label
        $menu_badge = get_post_meta( $item->ID, '_menu_item_badge', true );
        if ( $menu_badge ) {
            $classes[] = 'has-badge';
        }

        // Check for custom color
        $menu_color = get_post_meta( $item->ID, '_menu_item_color', true );
        if ( $menu_color ) {
            $classes[] = 'has-custom-color';
        }

        // Check for hide on mobile
        $hide_mobile = get_post_meta( $item->ID, '_menu_item_hide_mobile', true );
        if ( $hide_mobile ) {
            $classes[] = 'hide-mobile';
        }

        // Check for hide on desktop
        $hide_desktop = get_post_meta( $item->ID, '_menu_item_hide_desktop', true );
        if ( $hide_desktop ) {
            $classes[] = 'hide-desktop';
        }

        /**
         * Filter the CSS class(es) applied to a menu item's list item element.
         *
         * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param object $item    The current menu item.
         * @param array  $args    An array of wp_nav_menu() arguments.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filter the ID applied to a menu item's list item element.
         *
         * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param object $item    The current menu item.
         * @param array  $args    An array of wp_nav_menu() arguments.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        // Build link attributes
        $attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
        $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
        $attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
        $attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

        // Add dropdown toggle attributes
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $attributes .= ' class="dropdown-toggle"';
            $attributes .= ' aria-haspopup="true"';
            $attributes .= ' aria-expanded="false"';
        }

        // Build the link content
        $item_output = isset( $args->before ) ? $args->before : '';
        
        // Add custom color style if exists
        $link_style = '';
        if ( $menu_color ) {
            $link_style = ' style="color: ' . esc_attr( $menu_color ) . ';"';
        }
        
        $item_output .= '<a' . $attributes . $link_style . '>';

        // Add menu icon if exists
        if ( $menu_icon ) {
            $item_output .= '<i class="menu-icon ' . esc_attr( $menu_icon ) . '" aria-hidden="true"></i>';
        }

        // Add menu item title wrapper
        $item_output .= '<span class="menu-item-text">';
        $item_output .= isset( $args->link_before ) ? $args->link_before : '';
        $item_output .= apply_filters( 'the_title', $item->title, $item->ID );
        $item_output .= isset( $args->link_after ) ? $args->link_after : '';
        $item_output .= '</span>';

        // Add badge if exists
        if ( $menu_badge ) {
            $badge_color = get_post_meta( $item->ID, '_menu_item_badge_color', true );
            $badge_style = $badge_color ? ' style="background-color: ' . esc_attr( $badge_color ) . ';"' : '';
            $item_output .= '<span class="menu-badge"' . $badge_style . '>' . esc_html( $menu_badge ) . '</span>';
        }

        // Add dropdown indicator for parent items
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $item_output .= '<span class="dropdown-indicator" aria-hidden="true">';
            if ( $depth === 0 ) {
                $item_output .= '<svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">';
                $item_output .= '<path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
                $item_output .= '</svg>';
            } else {
                $item_output .= '<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">';
                $item_output .= '<path d="M1.5 1L6.5 6L1.5 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
                $item_output .= '</svg>';
            }
            $item_output .= '</span>';
        }

        $item_output .= '</a>';

        // Add description if exists
        if ( ! empty( $item->description ) ) {
            $item_output .= '<span class="menu-item-description">' . esc_html( $item->description ) . '</span>';
        }

        $item_output .= isset( $args->after ) ? $args->after : '';

        /**
         * Filter a menu item's starting output.
         *
         * @param string $item_output The menu item's starting HTML output.
         * @param object $item        Menu item data object.
         * @param int    $depth       Depth of menu item. Used for padding.
         * @param array  $args        An array of wp_nav_menu() arguments.
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * End Element - End the element output.
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}

/**
 * NosfirNews Mobile Walker Nav Menu Class
 * Specialized walker for mobile navigation
 */
class NosfirNews_Mobile_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Start Level - Start the list before the CHILD elements are added.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<ul class=\"mobile-sub-menu\">\n";
    }

    /**
     * End Level - End the list after the CHILD elements are added.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }

    /**
     * Start Element - Start the element output.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'mobile-menu-item';
        $classes[] = 'menu-item-' . $item->ID;

        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $classes[] = 'has-children';
        }

        if ( in_array( 'current-menu-item', $classes ) ) {
            $classes[] = 'active';
        }

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'mobile-menu-item-' . $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
        $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
        $attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
        $attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

        $item_output = isset( $args->before ) ? $args->before : '';
        
        // Add toggle button for parent items
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $item_output .= '<button class="mobile-submenu-toggle" aria-expanded="false">';
            $item_output .= '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">';
            $item_output .= '<path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
            $item_output .= '</svg>';
            $item_output .= '</button>';
        }

        $item_output .= '<a' . $attributes . '>';

        // Add menu icon if exists
        $menu_icon = get_post_meta( $item->ID, '_menu_item_icon', true );
        if ( $menu_icon ) {
            $item_output .= '<i class="menu-icon ' . esc_attr( $menu_icon ) . '" aria-hidden="true"></i>';
        }

        $item_output .= isset( $args->link_before ) ? $args->link_before : '';
        $item_output .= apply_filters( 'the_title', $item->title, $item->ID );
        $item_output .= isset( $args->link_after ) ? $args->link_after : '';
        $item_output .= '</a>';
        $item_output .= isset( $args->after ) ? $args->after : '';

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * End Element - End the element output.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}

/**
 * Helper function to get menu walker based on context
 *
 * @param string $context The context for the walker (default, mobile, mega)
 * @return object Walker instance
 */
function nosfirnews_get_nav_walker( $context = 'default' ) {
    switch ( $context ) {
        case 'mobile':
            return new NosfirNews_Mobile_Walker_Nav_Menu();
        default:
            return new NosfirNews_Walker_Nav_Menu();
    }
}

/* Menu custom fields are handled centrally in inc/menu-custom-fields.php */