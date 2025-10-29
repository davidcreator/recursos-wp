<?php
if (!defined('ABSPATH')) exit;

// Processar formulário
if (isset($_POST['wpsp_save_url_settings'])) {
    check_admin_referer('wpsp_url_settings');
    
    $fields = [
        'wpsp_custom_admin_url',
        'wpsp_custom_login_url',
        'wpsp_custom_theme_url',
        'wpsp_custom_plugins_url',
        'wpsp_custom_uploads_url',
        'wpsp_custom_xmlrpc_path'
    ];
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_option($field, sanitize_text_field($_POST[$field]));
        }
    }
    
    flush_rewrite_rules();
    
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'wp-security-pro') . '</p></div>';
}
?>

<div class="wrap">
    <h1><?php _e('URL Customization Settings', 'wp-security-pro'); ?></h1>
    
    <div class="notice notice-warning">
        <p><strong><?php _e('Important:', 'wp-security-pro'); ?></strong> <?php _e('After changing URLs, make sure to save your new URLs in a safe place. If you forget them, you may lose access to your site!', 'wp-security-pro'); ?></p>
    </div>
    
    <form method="post" action="">
        <?php wp_nonce_field('wpsp_url_settings'); ?>
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wpsp_custom_admin_url"><?php _e('Custom Admin URL', 'wp-security-pro'); ?></label>
                    </th>
                    <td>
                        <?php echo home_url('/'); ?><input type="text" name="wpsp_custom_admin_url" id="wpsp_custom_admin_url" value="<?php echo esc_attr(get_option('wpsp_custom_admin_url')); ?>" class="regular-text" />
                        <p class="description"><?php _e('Replace wp-admin with a custom URL (e.g., "my-admin")', 'wp-security-pro'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="wpsp_custom_login_url"><?php _e('Custom Login URL', 'wp-security-pro'); ?></label>
                    </th>
                    <td>
                        <?php echo home_url('/'); ?><input type="text" name="wpsp_custom_login_url" id="wpsp_custom_login_url" value="<?php echo esc_attr(get_option('wpsp_custom_login_url')); ?>" class="regular-text" />
                        <p class="description"><?php _e('Replace wp-login.php with a custom URL (e.g., "login")', 'wp-security-pro'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="wpsp_custom_theme_url"><?php _e('Custom Theme URL', 'wp-security-pro'); ?></label>
                    </th>
                    <td>
                        <?php echo home_url('/'); ?><input type="text" name="wpsp_custom_theme_url" id="wpsp_custom_theme_url" value="<?php echo esc_attr(get_option('wpsp_custom_theme_url')); ?>" class="regular-text" />
                        <p class="description"><?php _e('Replace wp-content/themes with a custom path (e.g., "assets")', 'wp-security-pro'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="wpsp_custom_plugins_url"><?php _e('Custom Plugins URL', 'wp-security-pro'); ?></label>
                    </th>
                    <td>
                        <?php echo home_url('/'); ?><input type="text" name="wpsp_custom_plugins_url" id="wpsp_custom_plugins_url" value="<?php echo esc_attr(get_option('wpsp_custom_plugins_url')); ?>" class="regular-text" />
                        <p class="description"><?php _e('Replace wp-content/plugins with a custom path (e.g., "modules")', 'wp-security-pro'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="wpsp_custom_uploads_url"><?php _e('Custom Uploads URL', 'wp-security-pro'); ?></label>
                    </th>
                    <td>
                        <?php echo home_url('/'); ?><input type="text" name="wpsp_custom_uploads_url" id="wpsp_custom_uploads_url" value="<?php echo esc_attr(get_option('wpsp_custom_uploads_url')); ?>" class="regular-text" />
                        <p class="description"><?php _e('Replace wp-content/uploads with a custom path (e.g., "files")', 'wp-security-pro'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="wpsp_custom_xmlrpc_path"><?php _e('Custom XML-RPC Path', 'wp-security-pro'); ?></label>
                    </th>
                    <td>
                        <?php echo home_url('/'); ?><input type="text" name="wpsp_custom_xmlrpc_path" id="wpsp_custom_xmlrpc_path" value="<?php echo esc_attr(get_option('wpsp_custom_xmlrpc_path')); ?>" class="regular-text" />
                        <p class="description"><?php _e('Replace xmlrpc.php with a custom path (e.g., "api")', 'wp-security-pro'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php submit_button(__('Save URL Settings', 'wp-security-pro'), 'primary', 'wpsp_save_url_settings'); ?>
    </form>
</div>