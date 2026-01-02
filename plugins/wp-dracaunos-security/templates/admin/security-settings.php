<?php
if (!defined('ABSPATH')) exit;
if (isset($_POST['wpsp_save_security'])) {
    check_admin_referer('wpsp_security_settings');
    $checkboxes = [
        'wpsp_block_default_admin',
        'wpsp_block_wp_includes',
        'wpsp_block_wp_content',
        'wpsp_block_xmlrpc',
        'wpsp_security_headers',
        'wpsp_enable_csp',
        'wpsp_enable_hsts',
        'wpsp_enable_permissions_policy',
    ];
    foreach ($checkboxes as $cb) {
        update_option($cb, isset($_POST[$cb]) ? 1 : 0);
    }
    $fields = [
        'wpsp_x_frame_options',
        'wpsp_referrer_policy',
        'wpsp_csp_default_src',
        'wpsp_csp_script_src',
        'wpsp_csp_style_src',
        'wpsp_csp_img_src',
        'wpsp_csp_font_src',
        'wpsp_csp_connect_src',
        'wpsp_csp_frame_ancestors',
        'wpsp_hsts_max_age',
        'wpsp_hsts_include_subdomains',
        'wpsp_hsts_preload',
        'wpsp_permissions_geolocation',
        'wpsp_permissions_microphone',
        'wpsp_permissions_camera',
        'wpsp_permissions_payment',
        'wpsp_permissions_usb',
    ];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            update_option($f, sanitize_text_field($_POST[$f]));
        }
    }
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'wp-dracaunos-security') . '</p></div>';
}
?>
<div class="wrap">
    <h1><?php _e('Security Settings', 'wp-dracaunos-security'); ?></h1>
    <form method="post" action="">
        <?php wp_nonce_field('wpsp_security_settings'); ?>
        <h2><?php _e('Blocking', 'wp-dracaunos-security'); ?></h2>
        <table class="form-table"><tbody>
            <tr><th><?php _e('Block default admin/login', 'wp-dracaunos-security'); ?></th><td><input type="checkbox" name="wpsp_block_default_admin" value="1" <?php checked(1, get_option('wpsp_block_default_admin', 1)); ?> /></td></tr>
            <tr><th><?php _e('Block wp-includes direct access', 'wp-dracaunos-security'); ?></th><td><input type="checkbox" name="wpsp_block_wp_includes" value="1" <?php checked(1, get_option('wpsp_block_wp_includes', 1)); ?> /></td></tr>
            <tr><th><?php _e('Block wp-content PHP direct access', 'wp-dracaunos-security'); ?></th><td><input type="checkbox" name="wpsp_block_wp_content" value="1" <?php checked(1, get_option('wpsp_block_wp_content', 1)); ?> /></td></tr>
            <tr><th><?php _e('Block XML-RPC', 'wp-dracaunos-security'); ?></th><td><input type="checkbox" name="wpsp_block_xmlrpc" value="1" <?php checked(1, get_option('wpsp_block_xmlrpc', 1)); ?> /></td></tr>
        </tbody></table>
        <h2><?php _e('Security Headers', 'wp-dracaunos-security'); ?></h2>
        <table class="form-table"><tbody>
            <tr><th><?php _e('Enable headers', 'wp-dracaunos-security'); ?></th><td><input type="checkbox" name="wpsp_security_headers" value="1" <?php checked(1, get_option('wpsp_security_headers', 1)); ?> /></td></tr>
            <tr><th><?php _e('X-Frame-Options', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_x_frame_options" value="<?php echo esc_attr(get_option('wpsp_x_frame_options', 'SAMEORIGIN')); ?>" class="regular-text" /></td></tr>
            <tr><th><?php _e('Referrer-Policy', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_referrer_policy" value="<?php echo esc_attr(get_option('wpsp_referrer_policy', 'strict-origin-when-cross-origin')); ?>" class="regular-text" /></td></tr>
            <tr><th><?php _e('Enable CSP', 'wp-dracaunos-security'); ?></th><td><input type="checkbox" name="wpsp_enable_csp" value="1" <?php checked(1, get_option('wpsp_enable_csp', 0)); ?> /></td></tr>
            <tr><th><?php _e('CSP default-src', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_csp_default_src" value="<?php echo esc_attr(get_option('wpsp_csp_default_src', "'self'")); ?>" class="regular-text" /></td></tr>
            <tr><th><?php _e('CSP script-src', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_csp_script_src" value="<?php echo esc_attr(get_option('wpsp_csp_script_src', "'self'")); ?>" class="regular-text" /></td></tr>
            <tr><th><?php _e('CSP style-src', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_csp_style_src" value="<?php echo esc_attr(get_option('wpsp_csp_style_src', "'self' 'unsafe-inline'")); ?>" class="regular-text" /></td></tr>
            <tr><th><?php _e('CSP img-src', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_csp_img_src" value="<?php echo esc_attr(get_option('wpsp_csp_img_src', "'self' data:")); ?>" class="regular-text" /></td></tr>
            <tr><th><?php _e('CSP font-src', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_csp_font_src" value="<?php echo esc_attr(get_option('wpsp_csp_font_src', "'self' data:")); ?>" class="regular-text" /></td></tr>
            <tr><th><?php _e('CSP connect-src', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_csp_connect_src" value="<?php echo esc_attr(get_option('wpsp_csp_connect_src', "'self'")); ?>" class="regular-text" /></td></tr>
            <tr><th><?php _e('CSP frame-ancestors', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_csp_frame_ancestors" value="<?php echo esc_attr(get_option('wpsp_csp_frame_ancestors', "'self'")); ?>" class="regular-text" /></td></tr>
        </tbody></table>
        <h2><?php _e('HSTS', 'wp-dracaunos-security'); ?></h2>
        <table class="form-table"><tbody>
            <tr><th><?php _e('Enable HSTS', 'wp-dracaunos-security'); ?></th><td><input type="checkbox" name="wpsp_enable_hsts" value="1" <?php checked(1, get_option('wpsp_enable_hsts', 0)); ?> /></td></tr>
            <tr><th><?php _e('Max-Age', 'wp-dracaunos-security'); ?></th><td><input type="number" name="wpsp_hsts_max_age" value="<?php echo esc_attr(get_option('wpsp_hsts_max_age', 31536000)); ?>" /></td></tr>
            <tr><th><?php _e('Include Subdomains', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_hsts_include_subdomains" value="<?php echo esc_attr(get_option('wpsp_hsts_include_subdomains', 1)); ?>" /></td></tr>
            <tr><th><?php _e('Preload', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_hsts_preload" value="<?php echo esc_attr(get_option('wpsp_hsts_preload', 0)); ?>" /></td></tr>
        </tbody></table>
        <h2><?php _e('Permissions-Policy', 'wp-dracaunos-security'); ?></h2>
        <table class="form-table"><tbody>
            <tr><th><?php _e('Enable Permissions-Policy', 'wp-dracaunos-security'); ?></th><td><input type="checkbox" name="wpsp_enable_permissions_policy" value="1" <?php checked(1, get_option('wpsp_enable_permissions_policy', 0)); ?> /></td></tr>
            <tr><th><?php _e('Geolocation', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_permissions_geolocation" value="<?php echo esc_attr(get_option('wpsp_permissions_geolocation', '()')); ?>" /></td></tr>
            <tr><th><?php _e('Microphone', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_permissions_microphone" value="<?php echo esc_attr(get_option('wpsp_permissions_microphone', '()')); ?>" /></td></tr>
            <tr><th><?php _e('Camera', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_permissions_camera" value="<?php echo esc_attr(get_option('wpsp_permissions_camera', '()')); ?>" /></td></tr>
            <tr><th><?php _e('Payment', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_permissions_payment" value="<?php echo esc_attr(get_option('wpsp_permissions_payment', '()')); ?>" /></td></tr>
            <tr><th><?php _e('USB', 'wp-dracaunos-security'); ?></th><td><input type="text" name="wpsp_permissions_usb" value="<?php echo esc_attr(get_option('wpsp_permissions_usb', '()')); ?>" /></td></tr>
        </tbody></table>
        <?php submit_button(__('Save Security Settings', 'wp-dracaunos-security'), 'primary', 'wpsp_save_security'); ?>
    </form>
</div>
