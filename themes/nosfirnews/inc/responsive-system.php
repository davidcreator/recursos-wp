<?php
/**
 * Responsive System for NosfirNews Theme
 * 
 * @package NosfirNews
 */

if (!defined('ABSPATH')) {
    exit;
}

class NosfirNews_Responsive_System {
    
    private $breakpoints;
    private $options;
    
    public function __construct() {
        $this->init();
    }
    
    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_responsive_styles'));
        add_action('wp_head', array($this, 'output_responsive_css'));
        add_action('customize_register', array($this, 'add_customizer_controls'));
        add_action('wp_footer', array($this, 'output_responsive_js'));
        
        $this->set_default_breakpoints();
        $this->load_options();
    }
    
    /**
     * Set default breakpoints
     */
    private function set_default_breakpoints() {
        $this->breakpoints = array(
            'mobile' => array(
                'label' => __('Mobile', 'nosfirnews'),
                'max_width' => 767,
                'min_width' => 0,
                'container_width' => '100%',
                'columns' => 1,
                'gutter' => 15
            ),
            'tablet' => array(
                'label' => __('Tablet', 'nosfirnews'),
                'max_width' => 1024,
                'min_width' => 768,
                'container_width' => '750px',
                'columns' => 2,
                'gutter' => 20
            ),
            'desktop' => array(
                'label' => __('Desktop', 'nosfirnews'),
                'max_width' => 1199,
                'min_width' => 1025,
                'container_width' => '970px',
                'columns' => 3,
                'gutter' => 30
            ),
            'large' => array(
                'label' => __('Large Desktop', 'nosfirnews'),
                'max_width' => 9999,
                'min_width' => 1200,
                'container_width' => '1170px',
                'columns' => 4,
                'gutter' => 30
            )
        );
    }
    
    /**
     * Load responsive options
     */
    private function load_options() {
        $defaults = array(
            'enable_responsive' => true,
            'mobile_menu_breakpoint' => 768,
            'container_max_width' => 1200,
            'enable_fluid_images' => true,
            'enable_responsive_embeds' => true,
            'enable_touch_navigation' => true,
            'retina_support' => true,
            'lazy_load_images' => true,
            'responsive_typography' => true,
            'mobile_sidebar_behavior' => 'below_content',
            'tablet_sidebar_behavior' => 'sidebar',
            'hide_elements_mobile' => array(),
            'hide_elements_tablet' => array(),
            'custom_breakpoints' => array()
        );
        
        $this->options = wp_parse_args(get_option('nosfirnews_responsive_options', array()), $defaults);
        
        // Merge custom breakpoints
        if (!empty($this->options['custom_breakpoints'])) {
            $this->breakpoints = array_merge($this->breakpoints, $this->options['custom_breakpoints']);
        }
    }
    
    /**
     * Add customizer controls
     */
    public function add_customizer_controls($wp_customize) {
        // Responsive Panel
        $wp_customize->add_panel('nosfirnews_responsive', array(
            'title' => __('Responsividade', 'nosfirnews'),
            'description' => __('Configurações de responsividade e breakpoints.', 'nosfirnews'),
            'priority' => 160
        ));
        
        // General Responsive Section
        $wp_customize->add_section('nosfirnews_responsive_general', array(
            'title' => __('Configurações Gerais', 'nosfirnews'),
            'panel' => 'nosfirnews_responsive',
            'priority' => 10
        ));
        
        // Enable Responsive
        $wp_customize->add_setting('nosfirnews_responsive_options[enable_responsive]', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean'
        ));
        
        $wp_customize->add_control('nosfirnews_responsive_options[enable_responsive]', array(
            'label' => __('Ativar Design Responsivo', 'nosfirnews'),
            'section' => 'nosfirnews_responsive_general',
            'type' => 'checkbox'
        ));
        
        // Mobile Menu Breakpoint
        $wp_customize->add_setting('nosfirnews_responsive_options[mobile_menu_breakpoint]', array(
            'default' => 768,
            'sanitize_callback' => 'absint'
        ));
        
        $wp_customize->add_control('nosfirnews_responsive_options[mobile_menu_breakpoint]', array(
            'label' => __('Breakpoint do Menu Mobile (px)', 'nosfirnews'),
            'section' => 'nosfirnews_responsive_general',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 320,
                'max' => 1200,
                'step' => 1
            )
        ));
        
        // Container Max Width
        $wp_customize->add_setting('nosfirnews_responsive_options[container_max_width]', array(
            'default' => 1200,
            'sanitize_callback' => 'absint'
        ));
        
        $wp_customize->add_control('nosfirnews_responsive_options[container_max_width]', array(
            'label' => __('Largura Máxima do Container (px)', 'nosfirnews'),
            'section' => 'nosfirnews_responsive_general',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 960,
                'max' => 1920,
                'step' => 10
            )
        ));
        
        // Breakpoints Section
        $wp_customize->add_section('nosfirnews_responsive_breakpoints', array(
            'title' => __('Breakpoints', 'nosfirnews'),
            'panel' => 'nosfirnews_responsive',
            'priority' => 20
        ));
        
        // Add breakpoint controls
        foreach ($this->breakpoints as $key => $breakpoint) {
            $wp_customize->add_setting("nosfirnews_responsive_breakpoints[{$key}_max_width]", array(
                'default' => $breakpoint['max_width'],
                'sanitize_callback' => 'absint'
            ));
            
            $wp_customize->add_control("nosfirnews_responsive_breakpoints[{$key}_max_width]", array(
                'label' => sprintf(__('%s - Largura Máxima (px)', 'nosfirnews'), $breakpoint['label']),
                'section' => 'nosfirnews_responsive_breakpoints',
                'type' => 'number'
            ));
        }
        
        // Mobile Behavior Section
        $wp_customize->add_section('nosfirnews_responsive_mobile', array(
            'title' => __('Comportamento Mobile', 'nosfirnews'),
            'panel' => 'nosfirnews_responsive',
            'priority' => 30
        ));
        
        // Mobile Sidebar Behavior
        $wp_customize->add_setting('nosfirnews_responsive_options[mobile_sidebar_behavior]', array(
            'default' => 'below_content',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        $wp_customize->add_control('nosfirnews_responsive_options[mobile_sidebar_behavior]', array(
            'label' => __('Comportamento da Sidebar no Mobile', 'nosfirnews'),
            'section' => 'nosfirnews_responsive_mobile',
            'type' => 'select',
            'choices' => array(
                'below_content' => __('Abaixo do Conteúdo', 'nosfirnews'),
                'above_content' => __('Acima do Conteúdo', 'nosfirnews'),
                'hidden' => __('Ocultar', 'nosfirnews'),
                'offcanvas' => __('Menu Off-Canvas', 'nosfirnews')
            )
        ));
        
        // Touch Navigation
        $wp_customize->add_setting('nosfirnews_responsive_options[enable_touch_navigation]', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean'
        ));
        
        $wp_customize->add_control('nosfirnews_responsive_options[enable_touch_navigation]', array(
            'label' => __('Ativar Navegação Touch', 'nosfirnews'),
            'section' => 'nosfirnews_responsive_mobile',
            'type' => 'checkbox'
        ));
    }
    
    /**
     * Enqueue responsive styles
     */
    public function enqueue_responsive_styles() {
        if (!$this->options['enable_responsive']) {
            return;
        }
        
        wp_enqueue_style(
            'nosfirnews-responsive',
            get_template_directory_uri() . '/assets/css/responsive.css',
            array(),
            wp_get_theme()->get('Version')
        );
        
        // Enqueue touch navigation script for mobile
        if ($this->options['enable_touch_navigation']) {
            wp_enqueue_script(
                'nosfirnews-touch-navigation',
                get_template_directory_uri() . '/assets/js/touch-navigation.js',
                array('jquery'),
                wp_get_theme()->get('Version'),
                true
            );
        }
    }
    
    /**
     * Output responsive CSS
     */
    public function output_responsive_css() {
        if (!$this->options['enable_responsive']) {
            return;
        }
        
        $css = $this->generate_responsive_css();
        
        if (!empty($css)) {
            echo "<style id='nosfirnews-responsive-css'>\n" . $css . "\n</style>\n";
        }
    }
    
    /**
     * Generate responsive CSS
     */
    private function generate_responsive_css() {
        $css = '';
        
        // Container max width
        $css .= ".container, .site-content { max-width: {$this->options['container_max_width']}px; }\n";
        
        // Generate breakpoint CSS
        foreach ($this->breakpoints as $key => $breakpoint) {
            $media_query = $this->get_media_query($breakpoint);
            
            $css .= "@media {$media_query} {\n";
            $css .= $this->get_breakpoint_css($key, $breakpoint);
            $css .= "}\n";
        }
        
        // Fluid images
        if ($this->options['enable_fluid_images']) {
            $css .= "img { max-width: 100%; height: auto; }\n";
        }
        
        // Responsive embeds
        if ($this->options['enable_responsive_embeds']) {
            $css .= $this->get_responsive_embeds_css();
        }
        
        // Responsive typography
        if ($this->options['responsive_typography']) {
            $css .= $this->get_responsive_typography_css();
        }
        
        return $css;
    }
    
    /**
     * Get media query for breakpoint
     */
    private function get_media_query($breakpoint) {
        $query = '';
        
        if ($breakpoint['min_width'] > 0) {
            $query .= "(min-width: {$breakpoint['min_width']}px)";
        }
        
        if ($breakpoint['max_width'] < 9999) {
            if (!empty($query)) {
                $query .= " and ";
            }
            $query .= "(max-width: {$breakpoint['max_width']}px)";
        }
        
        return $query;
    }
    
    /**
     * Get CSS for specific breakpoint
     */
    private function get_breakpoint_css($key, $breakpoint) {
        $css = '';
        
        // Container width
        if (isset($breakpoint['container_width'])) {
            $css .= ".container { width: {$breakpoint['container_width']}; }\n";
        }
        
        // Grid columns
        if (isset($breakpoint['columns'])) {
            $css .= ".grid-item { width: " . (100 / $breakpoint['columns']) . "%; }\n";
        }
        
        // Gutter
        if (isset($breakpoint['gutter'])) {
            $css .= ".grid-item { padding: 0 " . ($breakpoint['gutter'] / 2) . "px; }\n";
        }
        
        // Mobile specific styles
        if ($key === 'mobile') {
            $css .= $this->get_mobile_css();
        }
        
        // Tablet specific styles
        if ($key === 'tablet') {
            $css .= $this->get_tablet_css();
        }
        
        return $css;
    }
    
    /**
     * Get mobile specific CSS
     */
    private function get_mobile_css() {
        $css = '';
        
        // Mobile menu
        $css .= ".main-navigation { display: none; }\n";
        $css .= ".mobile-menu-toggle { display: block; }\n";
        
        // Sidebar behavior
        switch ($this->options['mobile_sidebar_behavior']) {
            case 'hidden':
                $css .= ".sidebar { display: none; }\n";
                break;
            case 'above_content':
                $css .= ".content-area { order: 2; }\n";
                $css .= ".sidebar { order: 1; }\n";
                break;
            case 'offcanvas':
                $css .= ".sidebar { position: fixed; top: 0; right: -300px; width: 300px; height: 100vh; background: #fff; transition: right 0.3s ease; z-index: 9999; }\n";
                $css .= ".sidebar.open { right: 0; }\n";
                break;
        }
        
        // Typography adjustments
        $css .= "h1 { font-size: 1.8em; }\n";
        $css .= "h2 { font-size: 1.5em; }\n";
        $css .= "h3 { font-size: 1.3em; }\n";
        
        // Button adjustments
        $css .= ".btn { padding: 12px 20px; font-size: 16px; }\n";
        
        // Form adjustments
        $css .= "input, textarea, select { font-size: 16px; }\n";
        
        return $css;
    }
    
    /**
     * Get tablet specific CSS
     */
    private function get_tablet_css() {
        $css = '';
        
        // Sidebar behavior
        if ($this->options['tablet_sidebar_behavior'] === 'hidden') {
            $css .= ".sidebar { display: none; }\n";
        }
        
        // Typography adjustments
        $css .= "h1 { font-size: 2.2em; }\n";
        $css .= "h2 { font-size: 1.8em; }\n";
        $css .= "h3 { font-size: 1.5em; }\n";
        
        return $css;
    }
    
    /**
     * Get responsive embeds CSS
     */
    private function get_responsive_embeds_css() {
        return "
        .embed-responsive {
            position: relative;
            display: block;
            width: 100%;
            padding: 0;
            overflow: hidden;
        }
        
        .embed-responsive::before {
            display: block;
            content: '';
        }
        
        .embed-responsive .embed-responsive-item,
        .embed-responsive iframe,
        .embed-responsive embed,
        .embed-responsive object,
        .embed-responsive video {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        
        .embed-responsive-21by9::before { padding-top: 42.857143%; }
        .embed-responsive-16by9::before { padding-top: 56.25%; }
        .embed-responsive-4by3::before { padding-top: 75%; }
        .embed-responsive-1by1::before { padding-top: 100%; }
        ";
    }
    
    /**
     * Get responsive typography CSS
     */
    private function get_responsive_typography_css() {
        return "
        @media (max-width: 767px) {
            body { font-size: 14px; line-height: 1.5; }
            h1 { font-size: 1.8em; }
            h2 { font-size: 1.5em; }
            h3 { font-size: 1.3em; }
            h4 { font-size: 1.1em; }
            h5 { font-size: 1em; }
            h6 { font-size: 0.9em; }
        }
        
        @media (min-width: 768px) and (max-width: 1024px) {
            body { font-size: 15px; line-height: 1.6; }
            h1 { font-size: 2.2em; }
            h2 { font-size: 1.8em; }
            h3 { font-size: 1.5em; }
            h4 { font-size: 1.2em; }
            h5 { font-size: 1.1em; }
            h6 { font-size: 1em; }
        }
        
        @media (min-width: 1025px) {
            body { font-size: 16px; line-height: 1.7; }
            h1 { font-size: 2.5em; }
            h2 { font-size: 2em; }
            h3 { font-size: 1.7em; }
            h4 { font-size: 1.4em; }
            h5 { font-size: 1.2em; }
            h6 { font-size: 1.1em; }
        }
        ";
    }
    
    /**
     * Output responsive JavaScript
     */
    public function output_responsive_js() {
        if (!$this->options['enable_responsive']) {
            return;
        }
        
        $breakpoints_json = json_encode($this->breakpoints);
        $options_json = json_encode($this->options);
        
        echo "<script id='nosfirnews-responsive-js'>
        window.NosfirNewsResponsive = {
            breakpoints: {$breakpoints_json},
            options: {$options_json},
            currentBreakpoint: null,
            
            init: function() {
                this.detectBreakpoint();
                this.bindEvents();
                this.initMobileMenu();
            },
            
            detectBreakpoint: function() {
                var width = window.innerWidth;
                var breakpoint = 'desktop';
                
                for (var key in this.breakpoints) {
                    var bp = this.breakpoints[key];
                    if (width >= bp.min_width && width <= bp.max_width) {
                        breakpoint = key;
                        break;
                    }
                }
                
                if (this.currentBreakpoint !== breakpoint) {
                    this.currentBreakpoint = breakpoint;
                    this.onBreakpointChange(breakpoint);
                }
            },
            
            onBreakpointChange: function(breakpoint) {
                document.body.className = document.body.className.replace(/breakpoint-\w+/g, '');
                document.body.classList.add('breakpoint-' + breakpoint);
                
                // Trigger custom event
                var event = new CustomEvent('breakpointChange', { detail: { breakpoint: breakpoint } });
                window.dispatchEvent(event);
            },
            
            bindEvents: function() {
                var self = this;
                window.addEventListener('resize', function() {
                    self.detectBreakpoint();
                });
            },
            
            initMobileMenu: function() {
                var toggle = document.querySelector('.mobile-menu-toggle');
                var menu = document.querySelector('.main-navigation');
                
                if (toggle && menu) {
                    toggle.addEventListener('click', function() {
                        menu.classList.toggle('open');
                        toggle.classList.toggle('active');
                    });
                }
            }
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            NosfirNewsResponsive.init();
        });
        </script>";
    }
    
    /**
     * Get current breakpoint
     */
    public function get_current_breakpoint() {
        return $this->current_breakpoint;
    }
    
    /**
     * Check if current breakpoint is mobile
     */
    public function is_mobile() {
        return $this->get_current_breakpoint() === 'mobile';
    }
    
    /**
     * Check if current breakpoint is tablet
     */
    public function is_tablet() {
        return $this->get_current_breakpoint() === 'tablet';
    }
    
    /**
     * Check if current breakpoint is desktop
     */
    public function is_desktop() {
        return in_array($this->get_current_breakpoint(), array('desktop', 'large'));
    }
    
    /**
     * Get breakpoint data
     */
    public function get_breakpoint($key) {
        return isset($this->breakpoints[$key]) ? $this->breakpoints[$key] : null;
    }
    
    /**
     * Get all breakpoints
     */
    public function get_breakpoints() {
        return $this->breakpoints;
    }
}

// Initialize the responsive system
new NosfirNews_Responsive_System();