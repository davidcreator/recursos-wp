<?php
/**
 * AMP Validator Class
 * 
 * Handles AMP validation, error checking and compliance verification
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EasyAMPPro_Validator {
    
    /**
     * Validation errors
     */
    private $validation_errors = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init_validator'));
        add_action('wp_ajax_easy_amp_pro_validate_page', array($this, 'ajax_validate_page'));
        add_action('wp_ajax_nopriv_easy_amp_pro_validate_page', array($this, 'ajax_validate_page'));
        add_filter('the_content', array($this, 'validate_content'), 1000);
        add_action('wp_head', array($this, 'add_validation_meta'), 1);
    }
    
    /**
     * Initialize validator
     */
    public function init_validator() {
        if (easy_amp_pro_is_amp_endpoint()) {
            add_action('wp_footer', array($this, 'output_validation_results'));
            add_filter('amp_post_template_data', array($this, 'add_validation_data'));
        }
        
        // Admin validation hooks
        if (is_admin()) {
            add_action('admin_notices', array($this, 'show_validation_notices'));
            add_action('save_post', array($this, 'validate_post_on_save'));
        }
    }
    
    /**
     * Validate AMP content
     */
    public function validate_content($content) {
        if (!easy_amp_pro_is_amp_endpoint()) {
            return $content;
        }
        
        $this->validation_errors = array();
        
        // Validate HTML structure
        $this->validate_html_structure($content);
        
        // Validate AMP components
        $this->validate_amp_components($content);
        
        // Validate CSS
        $this->validate_css($content);
        
        // Validate JavaScript
        $this->validate_javascript($content);
        
        // Validate images
        $this->validate_images($content);
        
        // Validate forms
        $this->validate_forms($content);
        
        return $content;
    }
    
    /**
     * Validate HTML structure
     */
    private function validate_html_structure($content) {
        // Check for invalid HTML tags
        $invalid_tags = array('script', 'style', 'frame', 'frameset', 'object', 'param', 'applet', 'embed');
        
        foreach ($invalid_tags as $tag) {
            if (preg_match('/<' . $tag . '[^>]*>/i', $content)) {
                $this->add_validation_error(
                    'invalid_html_tag',
                    sprintf(__('Invalid HTML tag found: %s', 'easy-amp-pro'), $tag),
                    'error'
                );
            }
        }
        
        // Check for inline styles
        if (preg_match('/style\s*=\s*["\'][^"\']*["\']/', $content)) {
            $this->add_validation_error(
                'inline_styles',
                __('Inline styles are not allowed in AMP', 'easy-amp-pro'),
                'warning'
            );
        }
        
        // Check for onclick and other event handlers
        $event_handlers = array('onclick', 'onload', 'onmouseover', 'onmouseout', 'onfocus', 'onblur');
        foreach ($event_handlers as $handler) {
            if (preg_match('/' . $handler . '\s*=/', $content)) {
                $this->add_validation_error(
                    'event_handlers',
                    sprintf(__('Event handler not allowed: %s', 'easy-amp-pro'), $handler),
                    'error'
                );
            }
        }
    }
    
    /**
     * Validate AMP components
     */
    private function validate_amp_components($content) {
        // Check for proper amp-img usage
        if (preg_match_all('/<img[^>]*>/i', $content, $matches)) {
            foreach ($matches[0] as $img_tag) {
                if (!preg_match('/width\s*=/', $img_tag) || !preg_match('/height\s*=/', $img_tag)) {
                    $this->add_validation_error(
                        'img_dimensions',
                        __('Images must have width and height attributes in AMP', 'easy-amp-pro'),
                        'error'
                    );
                }
            }
        }
        
        // Check for amp-video requirements
        if (preg_match_all('/<amp-video[^>]*>/i', $content, $matches)) {
            foreach ($matches[0] as $video_tag) {
                if (!preg_match('/width\s*=/', $video_tag) || !preg_match('/height\s*=/', $video_tag)) {
                    $this->add_validation_error(
                        'video_dimensions',
                        __('AMP videos must have width and height attributes', 'easy-amp-pro'),
                        'error'
                    );
                }
            }
        }
        
        // Check for amp-iframe requirements
        if (preg_match_all('/<amp-iframe[^>]*>/i', $content, $matches)) {
            foreach ($matches[0] as $iframe_tag) {
                if (!preg_match('/sandbox\s*=/', $iframe_tag)) {
                    $this->add_validation_error(
                        'iframe_sandbox',
                        __('AMP iframes must have sandbox attribute', 'easy-amp-pro'),
                        'error'
                    );
                }
            }
        }
    }
    
    /**
     * Validate CSS
     */
    private function validate_css($content) {
        // Check for external stylesheets
        if (preg_match('/<link[^>]*rel\s*=\s*["\']stylesheet["\'][^>]*>/i', $content)) {
            $this->add_validation_error(
                'external_css',
                __('External stylesheets are not allowed in AMP (except for fonts)', 'easy-amp-pro'),
                'warning'
            );
        }
        
        // Check CSS size limit (50KB)
        if (preg_match('/<style[^>]*>(.*?)<\/style>/is', $content, $matches)) {
            $css_content = $matches[1];
            if (strlen($css_content) > 50000) {
                $this->add_validation_error(
                    'css_size_limit',
                    __('CSS size exceeds 50KB limit for AMP', 'easy-amp-pro'),
                    'error'
                );
            }
        }
        
        // Check for !important usage
        if (preg_match('/!important/', $content)) {
            $this->add_validation_error(
                'css_important',
                __('Excessive use of !important in CSS may cause AMP validation issues', 'easy-amp-pro'),
                'warning'
            );
        }
    }
    
    /**
     * Validate JavaScript
     */
    private function validate_javascript($content) {
        // Check for inline JavaScript
        if (preg_match('/<script[^>]*>(?!.*application\/ld\+json).*?<\/script>/is', $content)) {
            $this->add_validation_error(
                'inline_javascript',
                __('Inline JavaScript is not allowed in AMP (except JSON-LD)', 'easy-amp-pro'),
                'error'
            );
        }
        
        // Check for external JavaScript
        if (preg_match('/<script[^>]*src\s*=\s*["\'][^"\']*["\'][^>]*>/i', $content)) {
            $this->add_validation_error(
                'external_javascript',
                __('External JavaScript is not allowed in AMP (except AMP components)', 'easy-amp-pro'),
                'error'
            );
        }
    }
    
    /**
     * Validate images
     */
    private function validate_images($content) {
        // Check for img tags that should be amp-img
        if (preg_match_all('/<img[^>]*>/i', $content, $matches)) {
            $this->add_validation_error(
                'img_to_amp_img',
                sprintf(__('Found %d img tags that should be converted to amp-img', 'easy-amp-pro'), count($matches[0])),
                'warning'
            );
        }
        
        // Check for missing alt attributes
        if (preg_match_all('/<(?:amp-)?img(?![^>]*alt\s*=)[^>]*>/i', $content, $matches)) {
            $this->add_validation_error(
                'missing_alt',
                sprintf(__('Found %d images without alt attributes', 'easy-amp-pro'), count($matches[0])),
                'warning'
            );
        }
    }
    
    /**
     * Validate forms
     */
    private function validate_forms($content) {
        // Check for form method and action
        if (preg_match_all('/<form[^>]*>/i', $content, $matches)) {
            foreach ($matches[0] as $form_tag) {
                if (!preg_match('/method\s*=\s*["\']post["\']/', $form_tag)) {
                    $this->add_validation_error(
                        'form_method',
                        __('AMP forms should use POST method', 'easy-amp-pro'),
                        'warning'
                    );
                }
                
                if (!preg_match('/action-xhr\s*=/', $form_tag)) {
                    $this->add_validation_error(
                        'form_action_xhr',
                        __('AMP forms should have action-xhr attribute for AJAX submission', 'easy-amp-pro'),
                        'warning'
                    );
                }
            }
        }
    }
    
    /**
     * Add validation error
     */
    private function add_validation_error($code, $message, $type = 'error') {
        $this->validation_errors[] = array(
            'code' => $code,
            'message' => $message,
            'type' => $type,
            'timestamp' => current_time('timestamp')
        );
    }
    
    /**
     * Get validation errors
     */
    public function get_validation_errors() {
        return $this->validation_errors;
    }
    
    /**
     * Validate page via AJAX
     */
    public function ajax_validate_page() {
        check_ajax_referer('easy_amp_pro_nonce', 'nonce');
        
        $url = sanitize_url($_POST['url']);
        $validation_result = $this->validate_url($url);
        
        wp_send_json_success($validation_result);
    }
    
    /**
     * Validate URL
     */
    public function validate_url($url) {
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return array(
                'valid' => false,
                'errors' => array(array(
                    'code' => 'request_failed',
                    'message' => $response->get_error_message(),
                    'type' => 'error'
                ))
            );
        }
        
        $content = wp_remote_retrieve_body($response);
        $this->validate_content($content);
        
        return array(
            'valid' => empty($this->validation_errors),
            'errors' => $this->validation_errors,
            'url' => $url
        );
    }
    
    /**
     * Output validation results
     */
    public function output_validation_results() {
        if (!current_user_can('manage_options') || empty($this->validation_errors)) {
            return;
        }
        
        echo '<!-- AMP Validation Results -->';
        echo '<script type="application/json" id="amp-validation-results">';
        echo json_encode($this->validation_errors);
        echo '</script>';
    }
    
    /**
     * Add validation data to AMP template
     */
    public function add_validation_data($data) {
        $data['validation_errors'] = $this->validation_errors;
        return $data;
    }
    
    /**
     * Show validation notices in admin
     */
    public function show_validation_notices() {
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->id, array('post', 'page', 'edit-post', 'edit-page'))) {
            return;
        }
        
        $post_id = get_the_ID();
        if (!$post_id) {
            return;
        }
        
        $validation_errors = get_post_meta($post_id, '_amp_validation_errors', true);
        if (!empty($validation_errors)) {
            echo '<div class="notice notice-warning">';
            echo '<p><strong>' . __('AMP Validation Issues:', 'easy-amp-pro') . '</strong></p>';
            echo '<ul>';
            foreach ($validation_errors as $error) {
                echo '<li>' . esc_html($error['message']) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }
    
    /**
     * Validate post on save
     */
    public function validate_post_on_save($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $post = get_post($post_id);
        if (!$post) {
            return;
        }
        
        // Validate post content
        $this->validate_content($post->post_content);
        
        // Save validation results
        if (!empty($this->validation_errors)) {
            update_post_meta($post_id, '_amp_validation_errors', $this->validation_errors);
        } else {
            delete_post_meta($post_id, '_amp_validation_errors');
        }
    }
    
    /**
     * Add validation meta to head
     */
    public function add_validation_meta() {
        if (!easy_amp_pro_is_amp_endpoint()) {
            return;
        }
        
        echo '<meta name="amp-validator" content="easy-amp-pro">' . "\n";
        
        if (current_user_can('manage_options')) {
            echo '<meta name="amp-validation-mode" content="development">' . "\n";
        }
    }
    
    /**
     * Check if current page is AMP endpoint
     */
    private function check_amp_endpoint() {
        // Check for AMP query parameter
        if (isset($_GET['amp']) || isset($_GET['amp_preview'])) {
            return true;
        }
        
        // Check for AMP URL structure
        if (function_exists('amp_is_request')) {
            return amp_is_request();
        }
        
        // Fallback check
        return false;
    }
}