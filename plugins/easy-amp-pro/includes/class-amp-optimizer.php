<?php
/**
 * AMP Optimizer Class
 * 
 * Handles AMP optimization, performance improvements and content processing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EasyAMPPro_Optimizer {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init_optimizer'));
        add_filter('the_content', array($this, 'optimize_content'), 999);
        add_filter('wp_get_attachment_image', array($this, 'convert_img_to_amp'), 10, 5);
        add_action('wp_head', array($this, 'add_amp_discovery'), 5);
        add_filter('embed_oembed_html', array($this, 'convert_embeds_to_amp'), 10, 4);
    }
    
    /**
     * Initialize optimizer
     */
    public function init_optimizer() {
        if (easy_amp_pro_is_amp_endpoint()) {
            // Remove actions that are not AMP compatible
            $this->remove_incompatible_actions();
            
            // Add AMP-specific optimizations
            add_filter('wp_resource_hints', array($this, 'add_amp_resource_hints'), 10, 2);
            add_action('wp_head', array($this, 'add_amp_optimizations'));
            add_filter('script_loader_tag', array($this, 'optimize_amp_scripts'), 10, 3);
            add_filter('style_loader_tag', array($this, 'optimize_amp_styles'), 10, 4);
        }
    }
    
    /**
     * Remove incompatible actions
     */
    private function remove_incompatible_actions() {
        $settings = get_option('easy_amp_pro_settings', array());
        
        if (!empty($settings['remove_incompatible'])) {
            // Common incompatible plugins/themes actions
            $incompatible_actions = array(
                'wp_head' => array(
                    'wp_generator',
                    'wlwmanifest_link',
                    'rsd_link',
                    'wp_shortlink_wp_head',
                    'adjacent_posts_rel_link_wp_head',
                    'wp_oembed_add_discovery_links'
                ),
                'wp_footer' => array(
                    'wp_print_footer_scripts'
                )
            );
            
            // Allow filtering
            $incompatible_actions = apply_filters('easy_amp_pro_incompatible_actions', $incompatible_actions);
            
            foreach ($incompatible_actions as $hook => $functions) {
                foreach ($functions as $function) {
                    remove_action($hook, $function);
                }
            }
            
            // Remove widgets that might not be AMP compatible
            add_action('widgets_init', array($this, 'remove_incompatible_widgets'), 11);
        }
    }
    
    /**
     * Remove incompatible widgets
     */
    public function remove_incompatible_widgets() {
        // List of potentially incompatible widgets
        $incompatible_widgets = array(
            'WP_Widget_Media_Video',
            'WP_Widget_Media_Audio',
            'WP_Widget_Custom_HTML'  // May contain non-AMP HTML
        );
        
        $incompatible_widgets = apply_filters('easy_amp_pro_incompatible_widgets', $incompatible_widgets);
        
        foreach ($incompatible_widgets as $widget_class) {
            if (class_exists($widget_class)) {
                unregister_widget($widget_class);
            }
        }
    }
    
    /**
     * Optimize content for AMP
     */
    public function optimize_content($content) {
        if (!easy_amp_pro_is_amp_endpoint()) {
            return $content;
        }
        
        // Convert images to amp-img
        $content = $this->convert_images_to_amp($content);
        
        // Convert videos to amp-video
        $content = $this->convert_videos_to_amp($content);
        
        // Convert iframes to amp-iframe
        $content = $this->convert_iframes_to_amp($content);
        
        // Remove prohibited attributes
        $content = $this->remove_prohibited_attributes($content);
        
        // Optimize inline styles
        $content = $this->optimize_inline_styles($content);
        
        // Clean up empty tags
        $content = $this->clean_empty_tags($content);
        
        return $content;
    }
    
    /**
     * Convert images to AMP format
     */
    private function convert_images_to_amp($content) {
        // Match img tags
        $pattern = '/<img([^>]+)>/i';
        
        return preg_replace_callback($pattern, function($matches) {
            $attributes = $matches[1];
            
            // Extract src, width, height, alt, class
            $src = $this->extract_attribute($attributes, 'src');
            $width = $this->extract_attribute($attributes, 'width');
            $height = $this->extract_attribute($attributes, 'height');
            $alt = $this->extract_attribute($attributes, 'alt');
            $class = $this->extract_attribute($attributes, 'class');
            $srcset = $this->extract_attribute($attributes, 'srcset');
            
            if (!$src) {
                return $matches[0]; // Return original if no src
            }
            
            // Get dimensions if not provided
            if (!$width || !$height) {
                $attachment_id = attachment_url_to_postid($src);
                if ($attachment_id) {
                    $image_meta = wp_get_attachment_metadata($attachment_id);
                    if ($image_meta) {
                        $width = $width ?: $image_meta['width'];
                        $height = $height ?: $image_meta['height'];
                    }
                }
                
                // Default dimensions if still not available
                if (!$width) $width = '600';
                if (!$height) $height = '400';
            }
            
            // Build amp-img tag
            $amp_img = '<amp-img';
            $amp_img .= ' src="' . esc_url($src) . '"';
            $amp_img .= ' width="' . intval($width) . '"';
            $amp_img .= ' height="' . intval($height) . '"';
            
            if ($alt) {
                $amp_img .= ' alt="' . esc_attr($alt) . '"';
            }
            
            if ($class) {
                $amp_img .= ' class="' . esc_attr($class) . '"';
            }
            
            $amp_img .= ' layout="responsive"';
            
            $amp_img .= '>';
            
            // Add fallback img for srcset
            if ($srcset) {
                $amp_img .= '<img src="' . esc_url($src) . '" srcset="' . esc_attr($srcset) . '" alt="' . esc_attr($alt) . '" fallback>';
            }
            
            $amp_img .= '</amp-img>';
            
            return $amp_img;
        }, $content);
    }
    
    /**
     * Convert videos to AMP format
     */
    private function convert_videos_to_amp($content) {
        // Match video tags
        $pattern = '/<video([^>]*)>(.*?)<\/video>/is';
        
        return preg_replace_callback($pattern, function($matches) {
            $attributes = $matches[1];
            $inner_content = $matches[2];
            
            $src = $this->extract_attribute($attributes, 'src');
            $width = $this->extract_attribute($attributes, 'width') ?: '640';
            $height = $this->extract_attribute($attributes, 'height') ?: '360';
            $poster = $this->extract_attribute($attributes, 'poster');
            $controls = strpos($attributes, 'controls') !== false;
            $autoplay = strpos($attributes, 'autoplay') !== false;
            $loop = strpos($attributes, 'loop') !== false;
            $muted = strpos($attributes, 'muted') !== false;
            
            // Extract source tags if no src attribute
            if (!$src && preg_match('/<source[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $inner_content, $source_match)) {
                $src = $source_match[1];
            }
            
            if (!$src) {
                return $matches[0]; // Return original if no src
            }
            
            $amp_video = '<amp-video';
            $amp_video .= ' src="' . esc_url($src) . '"';
            $amp_video .= ' width="' . intval($width) . '"';
            $amp_video .= ' height="' . intval($height) . '"';
            $amp_video .= ' layout="responsive"';
            
            if ($poster) {
                $amp_video .= ' poster="' . esc_url($poster) . '"';
            }
            
            if ($controls) $amp_video .= ' controls';
            if ($autoplay) $amp_video .= ' autoplay';
            if ($loop) $amp_video .= ' loop';
            if ($muted) $amp_video .= ' muted';
            
            $amp_video .= '>';
            
            // Add fallback
            $amp_video .= '<div fallback><p>' . __('Your browser does not support AMP video.', 'easy-amp-pro') . '</p></div>';
            
            $amp_video .= '</amp-video>';
            
            return $amp_video;
        }, $content);
    }
    
    /**
     * Convert iframes to AMP format
     */
    private function convert_iframes_to_amp($content) {
        $pattern = '/<iframe([^>]+)><\/iframe>/i';
        
        return preg_replace_callback($pattern, function($matches) {
            $attributes = $matches[1];
            
            $src = $this->extract_attribute($attributes, 'src');
            $width = $this->extract_attribute($attributes, 'width') ?: '640';
            $height = $this->extract_attribute($attributes, 'height') ?: '360';
            $frameborder = $this->extract_attribute($attributes, 'frameborder');
            $allowfullscreen = strpos($attributes, 'allowfullscreen') !== false;
            
            if (!$src) {
                return ''; // Remove iframe if no src
            }
            
            // Check for known video embeds and convert to specific AMP components
            if (preg_match('/youtube\.com\/embed\/([^\/\?&]+)/i', $src, $youtube_match)) {
                return '<amp-youtube data-videoid="' . $youtube_match[1] . '" width="' . intval($width) . '" height="' . intval($height) . '" layout="responsive"></amp-youtube>';
            }
            
            if (preg_match('/vimeo\.com\/video\/(\d+)/i', $src, $vimeo_match)) {
                return '<amp-vimeo data-videoid="' . $vimeo_match[1] . '" width="' . intval($width) . '" height="' . intval($height) . '" layout="responsive"></amp-vimeo>';
            }
            
            // Convert to amp-iframe
            $amp_iframe = '<amp-iframe';
            $amp_iframe .= ' src="' . esc_url($src) . '"';
            $amp_iframe .= ' width="' . intval($width) . '"';
            $amp_iframe .= ' height="' . intval($height) . '"';
            $amp_iframe .= ' layout="responsive"';
            $amp_iframe .= ' sandbox="allow-scripts allow-same-origin"';
            
            if ($frameborder === '0') {
                $amp_iframe .= ' frameborder="0"';
            }
            
            if ($allowfullscreen) {
                $amp_iframe .= ' allowfullscreen';
            }
            
            $amp_iframe .= '>';
            $amp_iframe .= '<div fallback><p>' . __('Your browser does not support AMP iframe.', 'easy-amp-pro') . '</p></div>';
            $amp_iframe .= '</amp-iframe>';
            
            return $amp_iframe;
        }, $content);
    }
    
    /**
     * Remove prohibited attributes
     */
    private function remove_prohibited_attributes($content) {
        // Remove JavaScript event attributes
        $content = preg_replace('/\son[a-z]+\s*=\s*["\'][^"\']*["\']?/i', '', $content);
        
        // Remove style attributes (will be handled separately)
        $content = preg_replace('/\sstyle\s*=\s*["\'][^"\']*["\']?/i', '', $content);
        
        return $content;
    }
    
    /**
     * Optimize inline styles
     */
    private function optimize_inline_styles($content) {
        // For now, just remove inline styles
        // In a more advanced version, these could be extracted and added to the AMP custom CSS
        return preg_replace('/\sstyle\s*=\s*["\'][^"\']*["\']?/i', '', $content);
    }
    
    /**
     * Clean empty tags
     */
    private function clean_empty_tags($content) {
        // Remove empty paragraphs and divs
        $content = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $content);
        $content = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $content);
        
        return $content;
    }
    
    /**
     * Extract attribute from HTML attributes string
     */
    private function extract_attribute($attributes, $name) {
        if (preg_match('/' . $name . '\s*=\s*["\']([^"\']*)["\']?/i', $attributes, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    /**
     * Convert attachment images to AMP
     */
    public function convert_img_to_amp($html, $attachment_id, $size, $icon, $attr) {
        if (!easy_amp_pro_is_amp_endpoint()) {
            return $html;
        }
        
        // Get image metadata
        $image_meta = wp_get_attachment_metadata($attachment_id);
        if (!$image_meta) {
            return $html;
        }
        
        // Extract src from HTML
        if (preg_match('/src=["\']([^"\']+)["\']/', $html, $matches)) {
            $src = $matches[1];
        } else {
            return $html;
        }
        
        // Get dimensions
        if (is_array($size)) {
            $width = $size[0];
            $height = $size[1];
        } else {
            $size_data = wp_get_attachment_image_src($attachment_id, $size);
            $width = $size_data[1];
            $height = $size_data[2];
        }
        
        // Build amp-img
        $amp_img = '<amp-img';
        $amp_img .= ' src="' . esc_url($src) . '"';
        $amp_img .= ' width="' . intval($width) . '"';
        $amp_img .= ' height="' . intval($height) . '"';
        $amp_img .= ' layout="responsive"';
        
        if (!empty($attr['alt'])) {
            $amp_img .= ' alt="' . esc_attr($attr['alt']) . '"';
        }
        
        if (!empty($attr['class'])) {
            $amp_img .= ' class="' . esc_attr($attr['class']) . '"';
        }
        
        $amp_img .= '></amp-img>';
        
        return $amp_img;
    }
    
    /**
     * Add AMP discovery link
     */
    public function add_amp_discovery() {
        if (!easy_amp_pro_is_amp_endpoint() && (is_single() || is_page())) {
            $amp_url = get_permalink() . '?amp=1';
            echo '<link rel="amphtml" href="' . esc_url($amp_url) . '">';
        }
    }
    
    /**
     * Convert embeds to AMP
     */
    public function convert_embeds_to_amp($html, $url, $attr, $post_id) {
        if (!easy_amp_pro_is_amp_endpoint()) {
            return $html;
        }
        
        // YouTube
        if (preg_match('/youtube\.com\/watch\?v=([^&\s]+)/', $url, $matches)) {
            $video_id = $matches[1];
            return '<amp-youtube data-videoid="' . esc_attr($video_id) . '" width="640" height="360" layout="responsive"></amp-youtube>';
        }
        
        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            $video_id = $matches[1];
            return '<amp-vimeo data-videoid="' . esc_attr($video_id) . '" width="640" height="360" layout="responsive"></amp-vimeo>';
        }
        
        // Twitter
        if (preg_match('/twitter\.com\/\w+\/status\/(\d+)/', $url, $matches)) {
            $tweet_id = $matches[1];
            return '<amp-twitter data-tweetid="' . esc_attr($tweet_id) . '" width="375" height="472" layout="responsive"></amp-twitter>';
        }
        
        // Instagram
        if (preg_match('/instagram\.com\/p\/([^\/\?]+)/', $url, $matches)) {
            $shortcode = $matches[1];
            return '<amp-instagram data-shortcode="' . esc_attr($shortcode) . '" width="400" height="400" layout="responsive"></amp-instagram>';
        }
        
        return $html;
    }
    
    /**
     * Add AMP resource hints
     */
    public function add_amp_resource_hints($hints, $relation_type) {
        if ($relation_type === 'dns-prefetch') {
            $hints[] = 'https://cdn.ampproject.org';
        }
        
        return $hints;
    }
    
    /**
     * Add AMP optimizations to head
     */
    public function add_amp_optimizations() {
        // Add preconnect for AMP CDN
        echo '<link rel="preconnect" href="https://cdn.ampproject.org">';
        
        // Add resource hints for fonts if used
        $settings = get_option('easy_amp_pro_settings', array());
        if (!empty($settings['google_fonts'])) {
            echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        }
    }
    
    /**
     * Optimize AMP scripts
     */
    public function optimize_amp_scripts($tag, $handle, $src) {
        // Ensure AMP scripts are async
        if (strpos($src, 'cdn.ampproject.org') !== false) {
            if (strpos($tag, 'async') === false) {
                $tag = str_replace('<script ', '<script async ', $tag);
            }
        }
        
        return $tag;
    }
    
    /**
     * Optimize AMP styles
     */
    public function optimize_amp_styles($html, $handle, $href, $media) {
        // Remove non-AMP compatible stylesheets on AMP pages
        if (easy_amp_pro_is_amp_endpoint()) {
            // List of handles to remove
            $remove_handles = array(
                'wp-block-library',
                'wp-block-library-theme',
                'contact-form-7'
            );
            
            $remove_handles = apply_filters('easy_amp_pro_remove_style_handles', $remove_handles);
            
            if (in_array($handle, $remove_handles)) {
                return '';
            }
        }
        
        return $html;
    }
}