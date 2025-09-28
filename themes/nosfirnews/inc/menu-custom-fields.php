<?php
/**
 * Menu Custom Fields
 * 
 * Adds custom fields to menu items for enhanced navigation functionality
 *
 * @package NosfirNews
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * NosfirNews Menu Custom Fields Class
 */
class NosfirNews_Menu_Custom_Fields {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_custom_fields' ), 10, 4 );
        add_action( 'wp_update_nav_menu_item', array( $this, 'save_custom_fields' ), 10, 3 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    /**
     * Add custom fields to menu items
     */
    public function add_custom_fields( $item_id, $item, $depth, $args ) {
        $menu_icon = get_post_meta( $item_id, '_menu_item_icon', true );
        $menu_badge = get_post_meta( $item_id, '_menu_item_badge', true );
        $menu_badge_color = get_post_meta( $item_id, '_menu_item_badge_color', true );
        $menu_color = get_post_meta( $item_id, '_menu_item_color', true );
        $is_mega_menu = get_post_meta( $item_id, '_menu_item_mega_menu', true );
        $is_featured = get_post_meta( $item_id, '_menu_item_featured', true );
        $is_button = get_post_meta( $item_id, '_menu_item_button', true );
        $hide_mobile = get_post_meta( $item_id, '_menu_item_hide_mobile', true );
        $hide_desktop = get_post_meta( $item_id, '_menu_item_hide_desktop', true );
        ?>
        
        <div class="nosfirnews-menu-custom-fields" style="margin-top: 10px; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">
            <h4 style="margin: 0 0 10px 0; font-size: 14px;"><?php esc_html_e( 'Opções Avançadas', 'nosfirnews' ); ?></h4>
            
            <!-- Menu Icon -->
            <p class="field-icon description description-wide">
                <label for="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Ícone do Menu', 'nosfirnews' ); ?><br />
                    <input type="text" id="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-icon" name="menu-item-icon[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $menu_icon ); ?>" placeholder="fas fa-home" />
                    <span class="description"><?php esc_html_e( 'Classe do ícone (ex: fas fa-home, bi bi-house)', 'nosfirnews' ); ?></span>
                </label>
            </p>

            <!-- Menu Badge -->
            <p class="field-badge description description-wide">
                <label for="edit-menu-item-badge-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Badge/Etiqueta', 'nosfirnews' ); ?><br />
                    <input type="text" id="edit-menu-item-badge-<?php echo esc_attr( $item_id ); ?>" class="widefat edit-menu-item-badge" name="menu-item-badge[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $menu_badge ); ?>" placeholder="Novo" />
                    <span class="description"><?php esc_html_e( 'Texto do badge (ex: Novo, Hot, Sale)', 'nosfirnews' ); ?></span>
                </label>
            </p>

            <!-- Badge Color -->
            <p class="field-badge-color description description-wide">
                <label for="edit-menu-item-badge-color-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Cor do Badge', 'nosfirnews' ); ?><br />
                    <input type="color" id="edit-menu-item-badge-color-<?php echo esc_attr( $item_id ); ?>" class="edit-menu-item-badge-color" name="menu-item-badge-color[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $menu_badge_color ?: '#ff0000' ); ?>" />
                </label>
            </p>

            <!-- Menu Color -->
            <p class="field-color description description-wide">
                <label for="edit-menu-item-color-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Cor do Texto', 'nosfirnews' ); ?><br />
                    <input type="color" id="edit-menu-item-color-<?php echo esc_attr( $item_id ); ?>" class="edit-menu-item-color" name="menu-item-color[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $menu_color ); ?>" />
                </label>
            </p>

            <!-- Checkboxes Row 1 -->
            <div style="display: flex; gap: 15px; margin: 10px 0;">
                <label>
                    <input type="checkbox" id="edit-menu-item-mega-menu-<?php echo esc_attr( $item_id ); ?>" name="menu-item-mega-menu[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $is_mega_menu, 1 ); ?> />
                    <?php esc_html_e( 'Mega Menu', 'nosfirnews' ); ?>
                </label>

                <label>
                    <input type="checkbox" id="edit-menu-item-featured-<?php echo esc_attr( $item_id ); ?>" name="menu-item-featured[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $is_featured, 1 ); ?> />
                    <?php esc_html_e( 'Item Destacado', 'nosfirnews' ); ?>
                </label>

                <label>
                    <input type="checkbox" id="edit-menu-item-button-<?php echo esc_attr( $item_id ); ?>" name="menu-item-button[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $is_button, 1 ); ?> />
                    <?php esc_html_e( 'Estilo Botão', 'nosfirnews' ); ?>
                </label>
            </div>

            <!-- Checkboxes Row 2 -->
            <div style="display: flex; gap: 15px; margin: 10px 0;">
                <label>
                    <input type="checkbox" id="edit-menu-item-hide-mobile-<?php echo esc_attr( $item_id ); ?>" name="menu-item-hide-mobile[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $hide_mobile, 1 ); ?> />
                    <?php esc_html_e( 'Ocultar no Mobile', 'nosfirnews' ); ?>
                </label>

                <label>
                    <input type="checkbox" id="edit-menu-item-hide-desktop-<?php echo esc_attr( $item_id ); ?>" name="menu-item-hide-desktop[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $hide_desktop, 1 ); ?> />
                    <?php esc_html_e( 'Ocultar no Desktop', 'nosfirnews' ); ?>
                </label>
            </div>

            <!-- Icon Preview -->
            <div class="icon-preview" style="margin-top: 10px;">
                <strong><?php esc_html_e( 'Preview do Ícone:', 'nosfirnews' ); ?></strong>
                <span id="icon-preview-<?php echo esc_attr( $item_id ); ?>" style="margin-left: 10px; font-size: 16px;"></span>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Icon preview
            function updateIconPreview(itemId) {
                var iconClass = $('#edit-menu-item-icon-' + itemId).val();
                var preview = $('#icon-preview-' + itemId);
                
                if (iconClass) {
                    preview.html('<i class="' + iconClass + '"></i>');
                } else {
                    preview.html('<?php esc_html_e( 'Nenhum ícone', 'nosfirnews' ); ?>');
                }
            }

            $('#edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>').on('input', function() {
                updateIconPreview(<?php echo esc_attr( $item_id ); ?>);
            });

            // Initial preview
            updateIconPreview(<?php echo esc_attr( $item_id ); ?>);
        });
        </script>

        <?php
    }

    /**
     * Save custom fields
     */
    public function save_custom_fields( $menu_id, $menu_item_db_id, $args ) {
        $fields = array(
            'menu-item-icon',
            'menu-item-badge',
            'menu-item-badge-color',
            'menu-item-color',
            'menu-item-mega-menu',
            'menu-item-featured',
            'menu-item-button',
            'menu-item-hide-mobile',
            'menu-item-hide-desktop'
        );

        foreach ( $fields as $field ) {
            $key = str_replace( '-', '_', $field );
            
            if ( isset( $_POST[$field][$menu_item_db_id] ) ) {
                $value = $_POST[$field][$menu_item_db_id];
                
                // Sanitize based on field type
                switch ( $field ) {
                    case 'menu-item-icon':
                        $value = sanitize_text_field( $value );
                        break;
                    case 'menu-item-badge':
                        $value = sanitize_text_field( $value );
                        break;
                    case 'menu-item-badge-color':
                    case 'menu-item-color':
                        $value = sanitize_hex_color( $value );
                        break;
                    default:
                        $value = $value ? 1 : 0;
                }
                
                update_post_meta( $menu_item_db_id, '_' . $key, $value );
            } else {
                delete_post_meta( $menu_item_db_id, '_' . $key );
            }
        }
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( 'nav-menus.php' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'nosfirnews-menu-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), NOSFIRNEWS_VERSION );
        wp_enqueue_script( 'nosfirnews-menu-admin', get_template_directory_uri() . '/assets/js/admin.js', array( 'jquery' ), NOSFIRNEWS_VERSION, true );
    }
}

// Initialize the class
new NosfirNews_Menu_Custom_Fields();