<?php
/**
 * AMP Settings Class
 * 
 * Handles plugin settings and configuration
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EasyAMPPro_Settings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'init_settings'));
        add_action('wp_ajax_easy_amp_pro_save_settings', array($this, 'save_settings'));
        add_action('wp_ajax_easy_amp_pro_reset_settings', array($this, 'reset_settings'));
        add_action('wp_ajax_easy_amp_pro_export_settings', array($this, 'export_settings'));
        add_action('wp_ajax_easy_amp_pro_import_settings', array($this, 'import_settings'));
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        // Register settings
        register_setting(
            'easy_amp_pro_settings_group',
            'easy_amp_pro_settings',
            array($this, 'sanitize_settings')
        );
        
        // General Settings Section
        add_settings_section(
            'easy_amp_pro_general_section',
            __('General Settings', 'easy-amp-pro'),
            array($this, 'general_section_callback'),
            'easy_amp_pro_general'
        );
        
        // Optimization Settings Section
        add_settings_section(
            'easy_amp_pro_optimization_section',
            __('Optimization Settings', 'easy-amp-pro'),
            array($this, 'optimization_section_callback'),
            'easy_amp_pro_optimization'
        );
        
        // Validation Settings Section
        add_settings_section(
            'easy_amp_pro_validation_section',
            __('Validation Settings', 'easy-amp-pro'),
            array($this, 'validation_section_callback'),
            'easy_amp_pro_validation'
        );
        
        // Analytics Settings Section
        add_settings_section(
            'easy_amp_pro_analytics_section',
            __('Analytics Settings', 'easy-amp-pro'),
            array($this, 'analytics_section_callback'),
            'easy_amp_pro_analytics'
        );
        
        // Advanced Settings Section
        add_settings_section(
            'easy_amp_pro_advanced_section',
            __('Advanced Settings', 'easy-amp-pro'),
            array($this, 'advanced_section_callback'),
            'easy_amp_pro_advanced'
        );
        
        // Add settings fields
        $this->add_settings_fields();
    }
    
    /**
     * Add settings fields
     */
    private function add_settings_fields() {
        $settings = get_option('easy_amp_pro_settings', array());
        
        // General Settings Fields
        add_settings_field(
            'enable_amp',
            __('Enable AMP', 'easy-amp-pro'),
            array($this, 'checkbox_field_callback'),
            'easy_amp_pro_general',
            'easy_amp_pro_general_section',
            array(
                'field' => 'enable_amp',
                'description' => __('Enable AMP functionality for your site', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'amp_mode',
            __('AMP Mode', 'easy-amp-pro'),
            array($this, 'select_field_callback'),
            'easy_amp_pro_general',
            'easy_amp_pro_general_section',
            array(
                'field' => 'amp_mode',
                'options' => array(
                    'paired' => __('Paired Mode (separate AMP URLs)', 'easy-amp-pro'),
                    'native' => __('Native Mode (AMP-first)', 'easy-amp-pro'),
                    'transitional' => __('Transitional Mode', 'easy-amp-pro')
                ),
                'description' => __('Choose how AMP should be implemented', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'supported_post_types',
            __('Supported Post Types', 'easy-amp-pro'),
            array($this, 'multi_checkbox_field_callback'),
            'easy_amp_pro_general',
            'easy_amp_pro_general_section',
            array(
                'field' => 'supported_post_types',
                'options' => $this->get_post_types(),
                'description' => __('Select which post types should support AMP', 'easy-amp-pro')
            )
        );
        
        // Optimization Settings Fields
        add_settings_field(
            'auto_optimize',
            __('Auto Optimization', 'easy-amp-pro'),
            array($this, 'checkbox_field_callback'),
            'easy_amp_pro_optimization',
            'easy_amp_pro_optimization_section',
            array(
                'field' => 'auto_optimize',
                'description' => __('Automatically optimize content for AMP compliance', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'remove_incompatible',
            __('Remove Incompatible Elements', 'easy-amp-pro'),
            array($this, 'checkbox_field_callback'),
            'easy_amp_pro_optimization',
            'easy_amp_pro_optimization_section',
            array(
                'field' => 'remove_incompatible',
                'description' => __('Automatically remove or replace incompatible HTML elements', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'image_optimization',
            __('Image Optimization', 'easy-amp-pro'),
            array($this, 'checkbox_field_callback'),
            'easy_amp_pro_optimization',
            'easy_amp_pro_optimization_section',
            array(
                'field' => 'image_optimization',
                'description' => __('Convert img tags to amp-img and optimize images', 'easy-amp-pro')
            )
        );
        
        // Validation Settings Fields
        add_settings_field(
            'enable_validation',
            __('Enable Validation', 'easy-amp-pro'),
            array($this, 'checkbox_field_callback'),
            'easy_amp_pro_validation',
            'easy_amp_pro_validation_section',
            array(
                'field' => 'enable_validation',
                'description' => __('Enable automatic AMP validation', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'validation_frequency',
            __('Validation Frequency', 'easy-amp-pro'),
            array($this, 'select_field_callback'),
            'easy_amp_pro_validation',
            'easy_amp_pro_validation_section',
            array(
                'field' => 'validation_frequency',
                'options' => array(
                    'daily' => __('Daily', 'easy-amp-pro'),
                    'weekly' => __('Weekly', 'easy-amp-pro'),
                    'monthly' => __('Monthly', 'easy-amp-pro')
                ),
                'description' => __('How often to run automatic validation checks', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'email_notifications',
            __('Email Notifications', 'easy-amp-pro'),
            array($this, 'checkbox_field_callback'),
            'easy_amp_pro_validation',
            'easy_amp_pro_validation_section',
            array(
                'field' => 'email_notifications',
                'description' => __('Send email notifications when validation errors are found', 'easy-amp-pro')
            )
        );
        
        // Analytics Settings Fields
        add_settings_field(
            'google_analytics_id',
            __('Google Analytics ID', 'easy-amp-pro'),
            array($this, 'text_field_callback'),
            'easy_amp_pro_analytics',
            'easy_amp_pro_analytics_section',
            array(
                'field' => 'google_analytics_id',
                'placeholder' => 'GA-XXXXXXXXX-X',
                'description' => __('Enter your Google Analytics tracking ID', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'custom_analytics',
            __('Custom Analytics Code', 'easy-amp-pro'),
            array($this, 'textarea_field_callback'),
            'easy_amp_pro_analytics',
            'easy_amp_pro_analytics_section',
            array(
                'field' => 'custom_analytics',
                'rows' => 10,
                'description' => __('Add custom AMP analytics code', 'easy-amp-pro')
            )
        );
        
        // Advanced Settings Fields
        add_settings_field(
            'custom_amp_head',
            __('Custom AMP Head Code', 'easy-amp-pro'),
            array($this, 'textarea_field_callback'),
            'easy_amp_pro_advanced',
            'easy_amp_pro_advanced_section',
            array(
                'field' => 'custom_amp_head',
                'rows' => 10,
                'description' => __('Add custom code to the AMP head section', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'amp_css',
            __('Custom AMP CSS', 'easy-amp-pro'),
            array($this, 'textarea_field_callback'),
            'easy_amp_pro_advanced',
            'easy_amp_pro_advanced_section',
            array(
                'field' => 'amp_css',
                'rows' => 15,
                'class' => 'large-text code',
                'description' => __('Add custom CSS for AMP pages (max 50KB)', 'easy-amp-pro')
            )
        );
        
        add_settings_field(
            'excluded_urls',
            __('Excluded URLs', 'easy-amp-pro'),
            array($this, 'textarea_field_callback'),
            'easy_amp_pro_advanced',
            'easy_amp_pro_advanced_section',
            array(
                'field' => 'excluded_urls',
                'rows' => 5,
                'description' => __('URLs to exclude from AMP (one per line, supports wildcards)', 'easy-amp-pro')
            )
        );
    }
    
    /**
     * Section callbacks
     */
    public function general_section_callback() {
        echo '<p>' . __('Configure general AMP settings for your site.', 'easy-amp-pro') . '</p>';
    }
    
    public function optimization_section_callback() {
        echo '<p>' . __('Configure automatic optimization settings.', 'easy-amp-pro') . '</p>';
    }
    
    public function validation_section_callback() {
        echo '<p>' . __('Configure AMP validation settings and monitoring.', 'easy-amp-pro') . '</p>';
    }
    
    public function analytics_section_callback() {
        echo '<p>' . __('Configure analytics tracking for AMP pages.', 'easy-amp-pro') . '</p>';
    }
    
    public function advanced_section_callback() {
        echo '<p>' . __('Advanced settings for experienced users.', 'easy-amp-pro') . '</p>';
    }
    
    /**
     * Field callbacks
     */
    public function checkbox_field_callback($args) {
        $settings = get_option('easy_amp_pro_settings', array());
        $field = $args['field'];
        $value = isset($settings[$field]) ? $settings[$field] : false;
        
        echo '<input type="checkbox" name="easy_amp_pro_settings[' . $field . ']" value="1" ' . checked(1, $value, false) . ' />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    public function text_field_callback($args) {
        $settings = get_option('easy_amp_pro_settings', array());
        $field = $args['field'];
        $value = isset($settings[$field]) ? $settings[$field] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $class = isset($args['class']) ? $args['class'] : 'regular-text';
        
        echo '<input type="text" name="easy_amp_pro_settings[' . $field . ']" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '" class="' . $class . '" />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    public function textarea_field_callback($args) {
        $settings = get_option('easy_amp_pro_settings', array());
        $field = $args['field'];
        $value = isset($settings[$field]) ? $settings[$field] : '';
        $rows = isset($args['rows']) ? $args['rows'] : 5;
        $class = isset($args['class']) ? $args['class'] : 'large-text';
        
        echo '<textarea name="easy_amp_pro_settings[' . $field . ']" rows="' . $rows . '" class="' . $class . '">' . esc_textarea($value) . '</textarea>';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    public function select_field_callback($args) {
        $settings = get_option('easy_amp_pro_settings', array());
        $field = $args['field'];
        $value = isset($settings[$field]) ? $settings[$field] : '';
        $options = $args['options'];
        
        echo '<select name="easy_amp_pro_settings[' . $field . ']">';
        foreach ($options as $option_value => $option_label) {
            echo '<option value="' . esc_attr($option_value) . '" ' . selected($value, $option_value, false) . '>' . esc_html($option_label) . '</option>';
        }
        echo '</select>';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    public function multi_checkbox_field_callback($args) {
        $settings = get_option('easy_amp_pro_settings', array());
        $field = $args['field'];
        $values = isset($settings[$field]) ? (array)$settings[$field] : array();
        $options = $args['options'];
        
        foreach ($options as $option_value => $option_label) {
            $checked = in_array($option_value, $values) ? 'checked="checked"' : '';
            echo '<label><input type="checkbox" name="easy_amp_pro_settings[' . $field . '][]" value="' . esc_attr($option_value) . '" ' . $checked . ' /> ' . esc_html($option_label) . '</label><br>';
        }
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Get post types
     */
    private function get_post_types() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $options = array();
        
        foreach ($post_types as $post_type) {
            $options[$post_type->name] = $post_type->labels->name;
        }
        
        return $options;
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        // Boolean fields
        $boolean_fields = array(
            'enable_amp',
            'auto_optimize',
            'remove_incompatible',
            'image_optimization',
            'enable_validation',
            'email_notifications'
        );
        
        foreach ($boolean_fields as $field) {
            $sanitized[$field] = !empty($input[$field]);
        }
        
        // Text fields
        $text_fields = array(
            'google_analytics_id' => 'sanitize_text_field',
            'amp_mode' => 'sanitize_text_field',
            'validation_frequency' => 'sanitize_text_field'
        );
        
        foreach ($text_fields as $field => $sanitize_function) {
            if (isset($input[$field])) {
                $sanitized[$field] = $sanitize_function($input[$field]);
            }
        }
        
        // Textarea fields
        $textarea_fields = array('custom_amp_head', 'custom_analytics', 'amp_css', 'excluded_urls');
        
        foreach ($textarea_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = wp_kses_post($input[$field]);
            }
        }
        
        // Array fields
        if (isset($input['supported_post_types']) && is_array($input['supported_post_types'])) {
            $sanitized['supported_post_types'] = array_map('sanitize_text_field', $input['supported_post_types']);
        }
        
        return $sanitized;
    }
    
    /**
     * Save settings via AJAX
     */
    public function save_settings() {
        check_ajax_referer('easy_amp_pro_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'easy-amp-pro'));
        }
        
        $settings = $_POST['settings'];
        $sanitized_settings = $this->sanitize_settings($settings);
        
        update_option('easy_amp_pro_settings', $sanitized_settings);
        
        wp_send_json_success(array(
            'message' => __('Settings saved successfully!', 'easy-amp-pro')
        ));
    }
    
    /**
     * Reset settings via AJAX
     */
    public function reset_settings() {
        check_ajax_referer('easy_amp_pro_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'easy-amp-pro'));
        }
        
        $default_settings = array(
            'enable_amp' => true,
            'amp_mode' => 'paired',
            'supported_post_types' => array('post', 'page'),
            'auto_optimize' => true,
            'remove_incompatible' => true,
            'image_optimization' => true,
            'enable_validation' => true,
            'validation_frequency' => 'weekly',
            'email_notifications' => false,
            'google_analytics_id' => '',
            'custom_analytics' => '',
            'custom_amp_head' => '',
            'amp_css' => '',
            'excluded_urls' => ''
        );
        
        update_option('easy_amp_pro_settings', $default_settings);
        
        wp_send_json_success(array(
            'message' => __('Settings reset to defaults!', 'easy-amp-pro'),
            'settings' => $default_settings
        ));
    }
    
    /**
     * Export settings via AJAX
     */
    public function export_settings() {
        check_ajax_referer('easy_amp_pro_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'easy-amp-pro'));
        }
        
        $settings = get_option('easy_amp_pro_settings', array());
        $export_data = array(
            'easy_amp_pro_settings' => $settings,
            'export_date' => current_time('mysql'),
            'site_url' => home_url(),
            'plugin_version' => EASY_AMP_PRO_VERSION
        );
        
        wp_send_json_success(array(
            'data' => base64_encode(wp_json_encode($export_data)),
            'filename' => 'easy-amp-pro-settings-' . date('Y-m-d') . '.json'
        ));
    }
    
    /**
     * Import settings via AJAX
     */
    public function import_settings() {
        check_ajax_referer('easy_amp_pro_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'easy-amp-pro'));
        }
        
        if (empty($_POST['import_data'])) {
            wp_send_json_error(array('message' => __('No import data provided', 'easy-amp-pro')));
        }
        
        $import_data = json_decode(base64_decode($_POST['import_data']), true);
        
        if (!$import_data || !isset($import_data['easy_amp_pro_settings'])) {
            wp_send_json_error(array('message' => __('Invalid import data', 'easy-amp-pro')));
        }
        
        $settings = $this->sanitize_settings($import_data['easy_amp_pro_settings']);
        update_option('easy_amp_pro_settings', $settings);
        
        wp_send_json_success(array(
            'message' => __('Settings imported successfully!', 'easy-amp-pro'),
            'settings' => $settings
        ));
    }
    
    /**
     * Get default settings
     */
    public static function get_default_settings() {
        return array(
            'enable_amp' => true,
            'amp_mode' => 'paired',
            'supported_post_types' => array('post', 'page'),
            'auto_optimize' => true,
            'remove_incompatible' => true,
            'image_optimization' => true,
            'enable_validation' => true,
            'validation_frequency' => 'weekly',
            'email_notifications' => false,
            'google_analytics_id' => '',
            'custom_analytics' => '',
            'custom_amp_head' => '',
            'amp_css' => '',
            'excluded_urls' => ''
        );
    }
    
    /**
     * Get setting value
     */
    public static function get_setting($key, $default = null) {
        $settings = get_option('easy_amp_pro_settings', self::get_default_settings());
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
    
    /**
     * Update setting value
     */
    public static function update_setting($key, $value) {
        $settings = get_option('easy_amp_pro_settings', self::get_default_settings());
        $settings[$key] = $value;
        return update_option('easy_amp_pro_settings', $settings);
    }
}