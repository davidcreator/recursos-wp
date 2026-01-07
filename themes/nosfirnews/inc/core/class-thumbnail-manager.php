<?php
/**
 * Sistema de Thumbnails Simplificado
 * Substitui a lógica complexa por uma classe gerenciadora
 * 
 * Usar em: inc/core/class-thumbnail-manager.php
 */

if (!defined('ABSPATH')) exit;

class NosfirNews_Thumbnail_Manager {
    
    private $post_id;
    private $context; // 'archive' ou 'single'
    
    public function __construct($post_id, $context = 'archive') {
        $this->post_id = $post_id;
        $this->context = $context;
    }
    
    /**
     * Renderiza thumbnail com todas as configurações aplicadas
     */
    public function render() {
        if (!$this->should_display()) {
            return;
        }
        
        $classes = $this->get_wrapper_classes();
        $styles = $this->get_inline_styles();
        $size = $this->get_image_size();
        
        printf(
            '<div class="entry-thumb %s"%s>%s</div>',
            esc_attr(implode(' ', $classes)),
            $styles ? ' style="' . esc_attr($styles) . '"' : '',
            get_the_post_thumbnail($this->post_id, $size)
        );
    }
    
    /**
     * Verifica se deve exibir thumbnail
     */
    private function should_display() {
        // Post meta sobrescreve theme mod
        $meta_hide = get_post_meta($this->post_id, 'nn_meta_hide_thumb', true);
        if ($meta_hide) {
            return false;
        }
        
        // Verifica theme mod baseado no contexto
        $theme_mod_key = $this->context === 'single' 
            ? 'nn_single_thumb_show' 
            : 'nn_post_thumb_show';
            
        return (bool) get_theme_mod($theme_mod_key, true) && has_post_thumbnail($this->post_id);
    }
    
    /**
     * Retorna classes CSS da wrapper
     */
    private function get_wrapper_classes() {
        $classes = [];
        
        // Hover effect
        $hover = $this->get_setting('nn_meta_thumb_hover', 'nn_thumb_hover_effect', 'none');
        if ($hover !== 'none' && $hover !== 'default') {
            $classes[] = 'thumb-effect-' . $hover;
        }
        
        // Fit mode
        $fit = $this->get_setting('nn_meta_thumb_fit', 'nn_thumb_fit', 'contain');
        if ($fit !== 'default') {
            $classes[] = 'thumb-fit-' . $fit;
        }
        
        return $classes;
    }
    
    /**
     * Retorna inline styles como string
     */
    private function get_inline_styles() {
        $styles = [];
        
        // Border radius
        $br = get_post_meta($this->post_id, 'nn_meta_thumb_border_radius', true);
        if ($br !== '') {
            $styles[] = '--nn-thumb-br:' . intval($br) . 'px';
        }
        
        // Shadow
        $shadow = $this->get_setting('nn_meta_thumb_shadow', 'nn_thumb_shadow', 'none');
        if ($shadow !== 'none' && $shadow !== 'default') {
            $shadow_map = [
                'soft' => '0 4px 12px rgba(0,0,0,.08)',
                'medium' => '0 8px 24px rgba(0,0,0,.12)',
                'hard' => '0 12px 32px rgba(0,0,0,.18)'
            ];
            if (isset($shadow_map[$shadow])) {
                $styles[] = '--nn-thumb-shadow:' . $shadow_map[$shadow];
            }
        }
        
        // Filter
        $filter = $this->get_setting('nn_meta_thumb_filter', 'nn_thumb_filter', 'none');
        if ($filter !== 'none' && $filter !== 'default') {
            $filter_map = [
                'grayscale' => 'grayscale(1)',
                'sepia' => 'sepia(1)',
                'saturate' => 'saturate(1.6)',
                'contrast' => 'contrast(1.2)',
                'brightness' => 'brightness(1.1)',
                'blur' => 'blur(2px)'
            ];
            if (isset($filter_map[$filter])) {
                $styles[] = '--nn-thumb-filter:' . $filter_map[$filter];
            }
        }
        
        return implode(';', $styles);
    }
    
    /**
     * Retorna tamanho da imagem
     */
    private function get_image_size() {
        if ($this->context === 'single') {
            $use_cover = (bool) get_theme_mod('nn_single_use_cover', true);
            return $use_cover ? 'nn_single_cover' : get_theme_mod('nn_thumb_size', 'large');
        }
        
        $use_std = (bool) get_theme_mod('nn_use_standard_thumb', true);
        return $use_std ? 'nn_thumb_standard' : get_theme_mod('nn_thumb_size', 'large');
    }
    
    /**
     * Helper: pega configuração de post meta ou theme mod
     */
    private function get_setting($meta_key, $theme_mod_key, $default) {
        $meta_value = get_post_meta($this->post_id, $meta_key, true);
        
        if ($meta_value && $meta_value !== 'default') {
            return $meta_value;
        }
        
        return get_theme_mod($theme_mod_key, $default);
    }
}

/**
 * USO NOS TEMPLATES:
 * 
 * // Em template-parts/content.php
 * $thumb = new NosfirNews_Thumbnail_Manager(get_the_ID(), 'archive');
 * $thumb->render();
 * 
 * // Em page-templates/content-single.php
 * $thumb = new NosfirNews_Thumbnail_Manager(get_the_ID(), 'single');
 * $thumb->render();
 */