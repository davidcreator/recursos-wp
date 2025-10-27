<?php
namespace WPSP\Security;

if (!defined('ABSPATH')) exit;

class HeadersCleaner {
    
    public function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Remover versão do WordPress
        if (get_option('wpsp_remove_wp_version', 1)) {
            remove_action('wp_head', 'wp_generator');
            add_filter('the_generator', '__return_empty_string');
        }
        
        // Remover Meta Generator
        if (get_option('wpsp_remove_meta_generator', 1)) {
            remove_action('wp_head', 'wp_generator');
            add_filter('the_generator', '__return_false');
        }
        
        // Desabilitar emojis
        if (get_option('wpsp_disable_emojis', 1)) {
            $this->disable_emojis();
        }
        
        // Remover tags desnecessárias
        $this->remove_unnecessary_headers();
        
        // Limpar informações de versão de scripts e estilos
        add_filter('style_loader_src', [$this, 'remove_version_from_assets'], 9999);
        add_filter('script_loader_src', [$this, 'remove_version_from_assets'], 9999);
    }
    
    /**
     * Desabilitar emojis
     */
    private function disable_emojis() {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        
        add_filter('tiny_mce_plugins', [$this, 'disable_emojis_tinymce']);
        add_filter('wp_resource_hints', [$this, 'disable_emojis_dns_prefetch'], 10, 2);
        add_filter('emoji_svg_url', '__return_false');
    }
    
    /**
     * Desabilitar emojis no TinyMCE
     */
    public function disable_emojis_tinymce($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, ['wpemoji']);
        }
        return [];
    }
    
    /**
     * Desabilitar DNS prefetch de emojis
     */
    public function disable_emojis_dns_prefetch($urls, $relation_type) {
        if ('dns-prefetch' === $relation_type) {
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/');
            $urls = array_diff($urls, [$emoji_svg_url]);
        }
        return $urls;
    }
    
    /**
     * Remover headers desnecessários
     */
    private function remove_unnecessary_headers() {
        // Remover RSD link
        remove_action('wp_head', 'rsd_link');
        
        // Remover wlwmanifest link
        remove_action('wp_head', 'wlwmanifest_link');
        
        // Remover short link
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        
        // Remover feed links
        if (get_option('wpsp_remove_feed_links', 0)) {
            remove_action('wp_head', 'feed_links', 2);
            remove_action('wp_head', 'feed_links_extra', 3);
        }
        
        // Remover REST API links
        if (get_option('wpsp_remove_rest_api_links', 0)) {
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            remove_action('template_redirect', 'rest_output_link_header', 11);
        }
        
        // Remover oEmbed
        if (get_option('wpsp_remove_oembed', 0)) {
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
        }
        
        // Remover canonical
        if (get_option('wpsp_remove_canonical', 0)) {
            remove_action('wp_head', 'rel_canonical');
        }
    }
    
    /**
     * Remover versão dos assets
     */
    public function remove_version_from_assets($src) {
        if (strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
}