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

function bdrposts_uninstall_cleanup() {
    global $wpdb;
    
    // Remove opções
    delete_option('bdrposts_version');
    delete_option('bdrposts_settings');
    
    // Remove todos os transients (usando prepared statements)
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            $wpdb->esc_like('_transient_bdrposts_') . '%',
            $wpdb->esc_like('_transient_timeout_bdrposts_') . '%'
        )
    );
    
    // Remove post meta
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
            $wpdb->esc_like('bdrposts_') . '%'
        )
    );
    
    // Remove user meta
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
            $wpdb->esc_like('bdrposts_') . '%'
        )
    );
    
    // Limpa cache
    wp_cache_flush();
    
    // Log de desinstalação (opcional)
    if (function_exists('error_log')) {
        error_log('BDRPosts: Plugin desinstalado e dados removidos');
    }
}

// Executa cleanup
bdrposts_uninstall_cleanup();
