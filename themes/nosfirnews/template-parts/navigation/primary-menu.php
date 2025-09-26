<?php
/**
 * Primary Navigation Template
 * Responsive navigation with hamburger menu for mobile
 *
 * @package NosfirNews
 * @since 2.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary Menu', 'nosfirnews'); ?>" itemscope itemtype="https://schema.org/SiteNavigationElement">
    <div class="navigation-wrapper">
        
        <!-- Desktop Navigation -->
        <div class="desktop-navigation">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'menu_class'     => 'nav-menu',
                    'container'      => false,
                    'depth'          => 3,
                    'walker'         => class_exists('NosfirNews_Walker_Nav_Menu') ? new NosfirNews_Walker_Nav_Menu() : '',
                    'fallback_cb'    => 'nosfirnews_fallback_menu',
                ));
            } else {
                nosfirnews_fallback_menu();
            }
            ?>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" aria-controls="mobile-menu" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle mobile menu', 'nosfirnews'); ?>" type="button">
            <span class="hamburger" aria-hidden="true">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </span>
            <span class="menu-text"><?php esc_html_e('Menu', 'nosfirnews'); ?></span>
        </button>

    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div id="mobile-menu-overlay" class="mobile-menu-overlay" aria-hidden="true"></div>

<!-- Mobile Navigation -->
<div id="mobile-menu" class="mobile-menu" role="navigation" aria-label="<?php esc_attr_e('Mobile Menu', 'nosfirnews'); ?>" aria-hidden="true">
    <div class="mobile-menu-header">
        <h2 class="mobile-menu-title"><?php esc_html_e('Menu', 'nosfirnews'); ?></h2>
        <button class="mobile-menu-close" aria-label="<?php esc_attr_e('Close mobile menu', 'nosfirnews'); ?>" type="button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="screen-reader-text"><?php esc_html_e('Close menu', 'nosfirnews'); ?></span>
        </button>
    </div>
    
    <div class="mobile-menu-content">
        <?php
        if (has_nav_menu('primary')) {
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id'        => 'mobile-primary-menu',
                'menu_class'     => 'mobile-nav-menu',
                'container'      => false,
                'depth'          => 3,
                'walker'         => class_exists('NosfirNews_Mobile_Walker_Nav_Menu') ? new NosfirNews_Mobile_Walker_Nav_Menu() : '',
                'fallback_cb'    => 'nosfirnews_mobile_fallback_menu',
            ));
        } else {
            nosfirnews_mobile_fallback_menu();
        }
        ?>
    </div>
    
    <!-- Mobile Menu Footer -->
    <div class="mobile-menu-footer">
        <?php if (has_nav_menu('social')) : ?>
            <div class="mobile-social-menu">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'social',
                    'menu_class'     => 'social-links-menu',
                    'container'      => false,
                    'depth'          => 1,
                    'link_before'    => '<span class="screen-reader-text">',
                    'link_after'     => '</span>',
                ));
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
/**
 * Fallback function for primary menu
 */
function nosfirnews_fallback_menu() {
    ?>
    <ul id="primary-menu" class="nav-menu">
        <li class="menu-item">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'nosfirnews'); ?></a>
        </li>
        <?php if (current_user_can('manage_options')) : ?>
        <li class="menu-item">
            <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>"><?php esc_html_e('Add a menu', 'nosfirnews'); ?></a>
        </li>
        <?php endif; ?>
    </ul>
    <?php
}

/**
 * Fallback function for mobile menu
 */
function nosfirnews_mobile_fallback_menu() {
    ?>
    <ul id="mobile-primary-menu" class="mobile-nav-menu">
        <li class="mobile-menu-item">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'nosfirnews'); ?></a>
        </li>
        <?php if (current_user_can('manage_options')) : ?>
        <li class="mobile-menu-item">
            <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>"><?php esc_html_e('Add a menu', 'nosfirnews'); ?></a>
        </li>
        <?php endif; ?>
    </ul>
    <?php
}
?>