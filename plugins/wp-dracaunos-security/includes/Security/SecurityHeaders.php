<?php
namespace WPSP\Security;

if (!defined('ABSPATH')) exit;

class SecurityHeaders {
    
    public function __construct() {
        if (get_option('wpsp_security_headers', 0)) {
            add_action('send_headers', [$this, 'add_security_headers']);
        }
    }
    
    /**
     * Adicionar headers de segurança
     */
    public function add_security_headers() {
        // X-Content-Type-Options
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            
            // X-Frame-Options
            $x_frame = get_option('wpsp_x_frame_options', 'SAMEORIGIN');
            header('X-Frame-Options: ' . $x_frame);
            
            // X-XSS-Protection
            header('X-XSS-Protection: 1; mode=block');
            
            // Referrer-Policy
            $referrer_policy = get_option('wpsp_referrer_policy', 'strict-origin-when-cross-origin');
            header('Referrer-Policy: ' . $referrer_policy);
            
            // Content-Security-Policy
            if (get_option('wpsp_enable_csp', 0)) {
                $csp = $this->get_csp_header();
                if (!empty($csp)) {
                    header('Content-Security-Policy: ' . $csp);
                }
            }
            
            // Strict-Transport-Security (HSTS)
            if (is_ssl() && get_option('wpsp_enable_hsts', 0)) {
                $max_age = get_option('wpsp_hsts_max_age', 31536000);
                $hsts = 'max-age=' . $max_age;
                
                if (get_option('wpsp_hsts_include_subdomains', 0)) {
                    $hsts .= '; includeSubDomains';
                }
                
                if (get_option('wpsp_hsts_preload', 0)) {
                    $hsts .= '; preload';
                }
                
                header('Strict-Transport-Security: ' . $hsts);
            }
            
            // Permissions-Policy
            if (get_option('wpsp_enable_permissions_policy', 0)) {
                $permissions = $this->get_permissions_policy();
                if (!empty($permissions)) {
                    header('Permissions-Policy: ' . $permissions);
                }
            }
        }
    }
    
    /**
     * Gerar Content-Security-Policy
     */
    private function get_csp_header() {
        $directives = [];
        
        // Default src
        $default_src = get_option('wpsp_csp_default_src', "'self'");
        if (!empty($default_src)) {
            $directives[] = "default-src {$default_src}";
        }
        
        // Script src
        $script_src = get_option('wpsp_csp_script_src', "'self' 'unsafe-inline' 'unsafe-eval'");
        if (get_option('wpsp_captcha_enabled', 0)) {
            $extra = " https://www.gstatic.com/recaptcha/ https://www.google.com/recaptcha/";
            if (strpos($script_src, 'recaptcha') === false) {
                $script_src .= $extra;
            }
        }
        if (!empty($script_src)) {
            $directives[] = "script-src {$script_src}";
        }
        
        // Style src
        $style_src = get_option('wpsp_csp_style_src', "'self' 'unsafe-inline'");
        if (!empty($style_src)) {
            $directives[] = "style-src {$style_src}";
        }
        
        // Img src
        $img_src = get_option('wpsp_csp_img_src', "'self' data: https:");
        if (!empty($img_src)) {
            $directives[] = "img-src {$img_src}";
        }
        
        // Font src
        $font_src = get_option('wpsp_csp_font_src', "'self' data:");
        if (!empty($font_src)) {
            $directives[] = "font-src {$font_src}";
        }
        
        // Connect src
        $connect_src = get_option('wpsp_csp_connect_src', "'self'");
        if (!empty($connect_src)) {
            $directives[] = "connect-src {$connect_src}";
        }
        
        // Frame ancestors
        $frame_ancestors = get_option('wpsp_csp_frame_ancestors', "'self'");
        if (!empty($frame_ancestors)) {
            $directives[] = "frame-ancestors {$frame_ancestors}";
        }
        
        if (get_option('wpsp_captcha_enabled', 0)) {
            $directives[] = "frame-src https://www.google.com/";
        }
        
        return implode('; ', $directives);
    }
    
    /**
     * Gerar Permissions-Policy
     */
    private function get_permissions_policy() {
        $policies = [];
        
        // Geolocation
        if (get_option('wpsp_permissions_geolocation', 'self') !== 'allow') {
            $policies[] = 'geolocation=()';
        }
        
        // Microphone
        if (get_option('wpsp_permissions_microphone', 'self') !== 'allow') {
            $policies[] = 'microphone=()';
        }
        
        // Camera
        if (get_option('wpsp_permissions_camera', 'self') !== 'allow') {
            $policies[] = 'camera=()';
        }
        
        // Payment
        if (get_option('wpsp_permissions_payment', 'self') !== 'allow') {
            $policies[] = 'payment=()';
        }
        
        // USB
        if (get_option('wpsp_permissions_usb', 'self') !== 'allow') {
            $policies[] = 'usb=()';
        }
        
        // Interest Cohort (FLoC)
        $policies[] = 'interest-cohort=()';
        
        return implode(', ', $policies);
    }
}
