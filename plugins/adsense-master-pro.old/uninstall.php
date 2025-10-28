<?php
/**
 * AdSense Master Pro - Uninstall
 * 
 * Executed when the plugin is uninstalled
 * 
 * @package AdSenseMasterPro
 * @version 1.0.0
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('amp_options');

// Delete any transients
delete_transient('amp_ad_cache');
delete_transient('amp_settings_cache');

// Drop custom tables
global $wpdb;

$table_name = $wpdb->prefix . 'amp_ads';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Remove ads.txt file (opcional - comentado para preservar)
// $ads_txt_file = ABSPATH . 'ads.txt';
// if (file_exists($ads_txt_file)) {
//     unlink($ads_txt_file);
// }

// Clean up post meta
$wpdb->delete($wpdb->postmeta, array('meta_key' => '_amp_disable_ads'));

// Clean up any scheduled hooks
wp_clear_scheduled_hook('amp_rotate_ads');
wp_clear_scheduled_hook('amp_cleanup_stats');

// Remove user preferences cookies (via JavaScript)
?>
<script>
// Remove cookies set by the plugin
document.cookie = 'amp_gdpr-consent=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
document.cookie = 'amp_hide-sticky-ad=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
</script>
<?php

// Clear any cached data
wp_cache_flush();

// Log uninstallation (optional)
error_log('AdSense Master Pro plugin uninstalled successfully');
?>