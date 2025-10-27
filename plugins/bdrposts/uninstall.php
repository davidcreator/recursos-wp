<?php
/**
 * BDRPosts Uninstall
 * 
 * Script executado quando o plugin é desinstalado
 * Remove todas as opções e dados do plugin
 * 
 * @package BRDPosts
 */

// Se o uninstall não foi chamado pelo WordPress, sai
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Remove opções do plugin
 */
function brdposts_uninstall_cleanup() {
    global $wpdb;
    
    // Remove opções do plugin (se houver)
    delete_option('brdposts_version');
    delete_option('brdposts_settings');
    
    // Remove transients (se houver)
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_brdposts_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_brdposts_%'");
    
    // Remove post meta (se houver)
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'brdposts_%'");
    
    // Remove user meta (se houver)
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'brdposts_%'");
    
    // Limpa cache
    wp_cache_flush();
}

// Executa cleanup
brdposts_uninstall_cleanup();