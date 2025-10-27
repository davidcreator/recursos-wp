<?php
// Load WordPress
require_once('../wp-config.php');
require_once('../wp-load.php');

// Clear WordPress object cache
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo 'WordPress object cache cleared' . PHP_EOL;
}

// Clear all transients
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");
echo 'WordPress transients cleared' . PHP_EOL;

// Clear any plugin-specific caches
delete_option('amp_cache_version');
delete_transient('amp_plugin_cache');
echo 'Plugin-specific caches cleared' . PHP_EOL;

echo 'All caches cleared successfully!' . PHP_EOL;
?>