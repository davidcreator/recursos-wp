<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$option_keys = [
    'wpsp_custom_admin_url',
    'wpsp_custom_login_url',
    'wpsp_custom_theme_url',
    'wpsp_custom_plugins_url',
    'wpsp_custom_uploads_url',
    'wpsp_custom_xmlrpc_path',
    'wpsp_block_default_admin',
    'wpsp_block_wp_includes',
    'wpsp_block_wp_content',
    'wpsp_block_xmlrpc',
    'wpsp_security_headers',
    'wpsp_x_frame_options',
    'wpsp_referrer_policy',
    'wpsp_enable_csp',
    'wpsp_csp_default_src',
    'wpsp_csp_script_src',
    'wpsp_csp_style_src',
    'wpsp_csp_img_src',
    'wpsp_csp_font_src',
    'wpsp_csp_connect_src',
    'wpsp_csp_frame_ancestors',
    'wpsp_enable_hsts',
    'wpsp_hsts_max_age',
    'wpsp_hsts_include_subdomains',
    'wpsp_hsts_preload',
    'wpsp_enable_permissions_policy',
    'wpsp_permissions_geolocation',
    'wpsp_permissions_microphone',
    'wpsp_permissions_camera',
    'wpsp_permissions_payment',
    'wpsp_permissions_usb',
    'wpsp_2fa_enabled',
    'wpsp_2fa_methods',
    'wpsp_captcha_enabled',
    'wpsp_captcha_site_key',
    'wpsp_captcha_secret_key',
    'wpsp_captcha_comments',
    'wpsp_remove_wp_version',
    'wpsp_remove_meta_generator',
    'wpsp_disable_emojis',
    'wpsp_minify_html',
    'wpsp_minify_css',
    'wpsp_minify_js',
    'wpsp_remove_feed_links',
    'wpsp_remove_rest_api_links',
    'wpsp_remove_oembed',
    'wpsp_remove_canonical',
];

foreach ($option_keys as $key) {
    delete_option($key);
}

global $wpdb;
$tables = [
    $wpdb->prefix . 'wpsp_two_factor',
    $wpdb->prefix . 'wpsp_security_logs',
    $wpdb->prefix . 'wpsp_sessions',
    $wpdb->prefix . 'wpsp_blocked_ips',
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS {$table}");
}

delete_transient('wpsp_2fa_user_*');
