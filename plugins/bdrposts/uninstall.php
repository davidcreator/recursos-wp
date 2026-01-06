<?php
/**
 * BDRPosts Uninstall
 * 
 * Script executado quando o plugin é desinstalado
 * Remove todas as opções e dados do plugin
 * 
 * @package bdrposts
 */

// Se o uninstall não foi chamado pelo WordPress, sai
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Remove opções do plugin
 */
function bdrposts_uninstall_cleanup() {
    global $wpdb;
    
    // Remove opções do plugin (se houver)
    delete_option('bdrposts_version');
    delete_option('bdrposts_settings');
    
    // Remove transients (se houver)
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_bdrposts_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_bdrposts_%'");
    
    // Remove post meta (se houver)
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'bdrposts_%'");
    
    // Remove user meta (se houver)
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'bdrposts_%'");
    
    // Limpa cache
    wp_cache_flush();
}

// Executa cleanup
bdrposts_uninstall_cleanup();
