<?php
if (!defined('ABSPATH')) exit;
if (isset($_POST['wpsp_save_2fa'])) {
    check_admin_referer('wpsp_2fa_settings');
    $enabled = isset($_POST['wpsp_2fa_enabled']) ? 1 : 0;
    $methods = isset($_POST['wpsp_2fa_methods']) && is_array($_POST['wpsp_2fa_methods']) ? array_map('sanitize_text_field', $_POST['wpsp_2fa_methods']) : [];
    update_option('wpsp_2fa_enabled', $enabled);
    update_option('wpsp_2fa_methods', $methods);
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'wp-dracaunos-security') . '</p></div>';
}
$methods_opt = get_option('wpsp_2fa_methods', ['email']);
?>
<div class="wrap">
    <h1><?php _e('Two-Factor Settings', 'wp-dracaunos-security'); ?></h1>
    <form method="post" action="">
        <?php wp_nonce_field('wpsp_2fa_settings'); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><?php _e('Enable 2FA', 'wp-dracaunos-security'); ?></th>
                    <td><input type="checkbox" name="wpsp_2fa_enabled" value="1" <?php checked(1, get_option('wpsp_2fa_enabled', 0)); ?> /></td>
                </tr>
                <tr>
                    <th><?php _e('Available Methods', 'wp-dracaunos-security'); ?></th>
                    <td>
                        <label><input type="checkbox" name="wpsp_2fa_methods[]" value="email" <?php checked(in_array('email', $methods_opt)); ?> /> <?php _e('Email', 'wp-dracaunos-security'); ?></label><br />
                        <label><input type="checkbox" name="wpsp_2fa_methods[]" value="authenticator" <?php checked(in_array('authenticator', $methods_opt)); ?> /> <?php _e('Authenticator App', 'wp-dracaunos-security'); ?></label><br />
                        <label><input type="checkbox" name="wpsp_2fa_methods[]" value="backup" <?php checked(in_array('backup', $methods_opt)); ?> /> <?php _e('Backup Codes', 'wp-dracaunos-security'); ?></label>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(__('Save 2FA Settings', 'wp-dracaunos-security'), 'primary', 'wpsp_save_2fa'); ?>
    </form>
</div>
