<?php
/**
 * Suporte AMP para AdSense Master Pro
 * 
 * @package AdSenseMasterPro
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AMP_Support {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option('amp_options', array());
        
        if ($this->is_amp_enabled()) {
            $this->init_amp_hooks();
        }
    }
    
    private function is_amp_enabled() {
        return isset($this->options['enable_amp']) && $this->options['enable_amp'];
    }
    
    private function init_amp_hooks() {
        // Hooks para diferentes plugins AMP
        add_action('amp_post_template_head', array($this, 'add_amp_adsense_script'));
        add_action('amp_post_template_css', array($this, 'add_amp_css'));
        add_filter('amp_post_template_data', array($this, 'add_amp_analytics'));
        
        // Hook para AMP plugin oficial do WordPress
        add_action('wp_head', array($this, 'add_amp_head_code'));
        add_filter('the_content', array($this, 'insert_amp_ads'));
        
        // Hooks para Yoast SEO AMP
        add_action('amp_post_template_head', array($this, 'yoast_amp_head'));
        
        // Hooks para AMP for WP
        add_action('ampforwp_add_custom_amp_script', array($this, 'ampforwp_add_scripts'));
    }
    
    public function add_amp_adsense_script() {
        if (!$this->is_amp_page()) return;
        
        echo '<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>' . "\n";
        
        // Auto Ads para AMP
        if (isset($this->options['amp_auto_ads']) && $this->options['amp_auto_ads']) {
            $publisher_id = $this->options['adsense_publisher_id'];
            if ($publisher_id) {
                echo '<script async custom-element="amp-auto-ads" src="https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js"></script>' . "\n";
            }
        }
        
        // Analytics para AMP
        if (isset($this->options['analytics_tracking']) && $this->options['analytics_tracking']) {
            echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>' . "\n";
        }
    }
    
    public function add_amp_css() {
        if (!$this->is_amp_page()) return;
        
        ?>
        <style amp-custom>
        .amp-ad-container {
            margin: 20px 0;
            text-align: center;
            clear: both;
        }
        
        .amp-ad-responsive {
            width: 100%;
            height: auto;
        }
        
        .amp-ad-banner {
            max-width: 728px;
            margin: 0 auto;
        }
        
        .amp-ad-square {
            max-width: 300px;
            margin: 0 auto;
        }
        
        .amp-ad-mobile {
            max-width: 320px;
            margin: 0 auto;
        }
        
        .amp-ad-leaderboard {
            max-width: 728px;
            margin: 0 auto;
        }
        
        .amp-ad-skyscraper {
            max-width: 160px;
            margin: 0 auto;
        }
        
        @media (max-width: 768px) {
            .amp-ad-banner,
            .amp-ad-leaderboard {
                max-width: 320px;
            }
        }
        
        /* Estilos personalizados */
        <?php
        if (!empty($this->options['custom_css'])) {
            echo $this->options['custom_css'];
        }
        ?>
        </style>
        <?php
    }
    
    public function add_amp_analytics($data) {
        if (!$this->is_amp_page()) return $data;
        
        if (isset($this->options['analytics_tracking']) && $this->options['analytics_tracking']) {
            $analytics_config = array(
                'vars' => array(
                    'account' => $this->options['google_analytics_id'] ?? ''
                ),
                'triggers' => array(
                    'trackPageview' => array(
                        'on' => 'visible',
                        'request' => 'pageview'
                    )
                )
            );
            
            $data['amp_analytics'] = $analytics_config;
        }
        
        return $data;
    }
    
    public function add_amp_head_code() {
        if (!$this->is_amp_page()) return;
        
        // Auto Ads para AMP
        if (isset($this->options['amp_auto_ads']) && $this->options['amp_auto_ads']) {
            $publisher_id = $this->options['adsense_publisher_id'];
            if ($publisher_id) {
                echo '<amp-auto-ads type="adsense" data-ad-client="' . esc_attr($publisher_id) . '"></amp-auto-ads>' . "\n";
            }
        }
    }
    
    public function insert_amp_ads($content) {
        if (!$this->is_amp_page() || !is_single()) return $content;
        
        $ads = $this->get_amp_ads();
        if (empty($ads)) return $content;
        
        return $this->insert_amp_ads_in_content($content, $ads);
    }
    
    private function insert_amp_ads_in_content($content, $ads) {
        $paragraphs = explode('</p>', $content);
        $total_paragraphs = count($paragraphs);
        
        if ($total_paragraphs < 2) return $content;
        
        $max_ads = min($this->options['max_ads_per_page'] ?? 3, count($ads));
        $inserted_ads = 0;
        
        // Inserir anúncios em posições estratégicas
        $positions = $this->calculate_amp_ad_positions($total_paragraphs, $max_ads);
        
        foreach ($positions as $position) {
            if ($inserted_ads >= $max_ads) break;
            
            $ad = $ads[$inserted_ads];
            $amp_ad_html = $this->render_amp_ad($ad);
            
            if ($position < count($paragraphs)) {
                $paragraphs[$position] .= '</p>' . $amp_ad_html;
            }
            
            $inserted_ads++;
        }
        
        return implode('</p>', $paragraphs);
    }
    
    private function calculate_amp_ad_positions($total_paragraphs, $max_ads) {
        $positions = array();
        
        if ($max_ads == 1) {
            // Um anúncio no meio do conteúdo
            $positions[] = intval($total_paragraphs / 2);
        } else {
            // Distribuir uniformemente
            $interval = intval($total_paragraphs / ($max_ads + 1));
            
            for ($i = 1; $i <= $max_ads; $i++) {
                $position = $interval * $i;
                if ($position < $total_paragraphs - 1) {
                    $positions[] = $position;
                }
            }
        }
        
        return $positions;
    }
    
    private function render_amp_ad($ad) {
        $ad_options = json_decode($ad->options, true) ?: array();
        
        // Determinar tipo e tamanho do anúncio
        $ad_type = $ad_options['amp_type'] ?? 'responsive';
        $width = $ad_options['width'] ?? 300;
        $height = $ad_options['height'] ?? 250;
        
        $html = '<div class="amp-ad-container">';
        
        switch ($ad_type) {
            case 'responsive':
                $html .= $this->render_responsive_amp_ad($ad, $width, $height);
                break;
            case 'banner':
                $html .= $this->render_banner_amp_ad($ad, 728, 90);
                break;
            case 'square':
                $html .= $this->render_square_amp_ad($ad, 300, 300);
                break;
            case 'mobile':
                $html .= $this->render_mobile_amp_ad($ad, 320, 50);
                break;
            default:
                $html .= $this->render_standard_amp_ad($ad, $width, $height);
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    private function render_responsive_amp_ad($ad, $width, $height) {
        $publisher_id = $this->options['adsense_client_id'] ?? $this->options['adsense_publisher_id'];
        $ad_slot = $this->extract_ad_slot($ad->code);
        
        return sprintf(
            '<amp-ad width="%d" height="%d" type="adsense" data-ad-client="%s" data-ad-slot="%s" data-auto-format="rspv" data-full-width="">
                <div overflow=""></div>
            </amp-ad>',
            $width,
            $height,
            esc_attr($publisher_id),
            esc_attr($ad_slot)
        );
    }
    
    private function render_banner_amp_ad($ad, $width, $height) {
        $publisher_id = $this->options['adsense_client_id'] ?? $this->options['adsense_publisher_id'];
        $ad_slot = $this->extract_ad_slot($ad->code);
        
        return sprintf(
            '<amp-ad class="amp-ad-banner" width="%d" height="%d" type="adsense" data-ad-client="%s" data-ad-slot="%s">
                <div overflow=""></div>
            </amp-ad>',
            $width,
            $height,
            esc_attr($publisher_id),
            esc_attr($ad_slot)
        );
    }
    
    private function render_square_amp_ad($ad, $width, $height) {
        $publisher_id = $this->options['adsense_client_id'] ?? $this->options['adsense_publisher_id'];
        $ad_slot = $this->extract_ad_slot($ad->code);
        
        return sprintf(
            '<amp-ad class="amp-ad-square" width="%d" height="%d" type="adsense" data-ad-client="%s" data-ad-slot="%s">
                <div overflow=""></div>
            </amp-ad>',
            $width,
            $height,
            esc_attr($publisher_id),
            esc_attr($ad_slot)
        );
    }
    
    private function render_mobile_amp_ad($ad, $width, $height) {
        $publisher_id = $this->options['adsense_client_id'] ?? $this->options['adsense_publisher_id'];
        $ad_slot = $this->extract_ad_slot($ad->code);
        
        return sprintf(
            '<amp-ad class="amp-ad-mobile" width="%d" height="%d" type="adsense" data-ad-client="%s" data-ad-slot="%s">
                <div overflow=""></div>
            </amp-ad>',
            $width,
            $height,
            esc_attr($publisher_id),
            esc_attr($ad_slot)
        );
    }
    
    private function render_standard_amp_ad($ad, $width, $height) {
        $publisher_id = $this->options['adsense_client_id'] ?? $this->options['adsense_publisher_id'];
        $ad_slot = $this->extract_ad_slot($ad->code);
        
        return sprintf(
            '<amp-ad width="%d" height="%d" type="adsense" data-ad-client="%s" data-ad-slot="%s">
                <div overflow=""></div>
            </amp-ad>',
            $width,
            $height,
            esc_attr($publisher_id),
            esc_attr($ad_slot)
        );
    }
    
    private function extract_ad_slot($ad_code) {
        // Extrair slot do código AdSense
        if (preg_match('/data-ad-slot=["\']([^"\']+)["\']/', $ad_code, $matches)) {
            return $matches[1];
        }
        
        // Fallback para formato antigo
        if (preg_match('/google_ad_slot\s*=\s*["\']([^"\']+)["\']/', $ad_code, $matches)) {
            return $matches[1];
        }
        
        return '';
    }
    
    private function get_amp_ads() {
        global $wpdb;
        
        return $wpdb->get_results("
            SELECT * FROM {$wpdb->prefix}amp_ads 
            WHERE status = 'active' 
            AND (device_targeting = 'all' OR device_targeting = 'mobile')
            ORDER BY priority DESC, created_at ASC
        ");
    }
    
    private function is_amp_page() {
        // Verificar diferentes plugins AMP
        
        // AMP plugin oficial
        if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
            return true;
        }
        
        // AMP for WP
        if (function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()) {
            return true;
        }
        
        // Yoast SEO AMP
        if (function_exists('is_amp') && is_amp()) {
            return true;
        }
        
        // Verificação manual por URL
        if (isset($_GET['amp']) || strpos($_SERVER['REQUEST_URI'], '/amp/') !== false) {
            return true;
        }
        
        return false;
    }
    
    public function yoast_amp_head() {
        $this->add_amp_adsense_script();
    }
    
    public function ampforwp_add_scripts() {
        $this->add_amp_adsense_script();
    }
    
    // Shortcode para anúncios AMP
    public function amp_ad_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
            'type' => 'responsive',
            'width' => 300,
            'height' => 250
        ), $atts);
        
        if (empty($atts['id'])) return '';
        
        global $wpdb;
        $ad = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}amp_ads WHERE id = %d AND status = 'active'",
            $atts['id']
        ));
        
        if (!$ad) return '';
        
        // Simular opções do anúncio
        $ad->options = json_encode(array(
            'amp_type' => $atts['type'],
            'width' => $atts['width'],
            'height' => $atts['height']
        ));
        
        return $this->render_amp_ad($ad);
    }
    
    // Analytics específicos para AMP
    public function track_amp_impression($ad_id) {
        if (!$this->is_amp_page()) return;
        
        // Usar amp-analytics para tracking
        $analytics_data = array(
            'requests' => array(
                'impression' => admin_url('admin-ajax.php') . '?action=amp_track_impression&ad_id=' . $ad_id
            ),
            'triggers' => array(
                'trackImpression' => array(
                    'on' => 'visible',
                    'request' => 'impression'
                )
            )
        );
        
        return '<amp-analytics type="googleanalytics"><script type="application/json">' . 
               json_encode($analytics_data) . '</script></amp-analytics>';
    }
    
    // Validação AMP
    public function validate_amp_ad($ad_code) {
        $errors = array();
        
        // Verificar se contém JavaScript não permitido
        if (preg_match('/<script(?![^>]*type=["\']application\/json["\'])[^>]*>/', $ad_code)) {
            $errors[] = 'JavaScript não é permitido em páginas AMP';
        }
        
        // Verificar se usa tags não permitidas
        $forbidden_tags = array('iframe', 'object', 'embed', 'form');
        foreach ($forbidden_tags as $tag) {
            if (strpos($ad_code, '<' . $tag) !== false) {
                $errors[] = "Tag <{$tag}> não é permitida em AMP";
            }
        }
        
        // Verificar se usa estilos inline
        if (preg_match('/style\s*=/', $ad_code)) {
            $errors[] = 'Estilos inline não são permitidos em AMP';
        }
        
        return $errors;
    }
    
    // Converter anúncio regular para AMP
    public function convert_to_amp_ad($ad_code) {
        // Extrair informações do código AdSense
        $client_id = '';
        $ad_slot = '';
        
        if (preg_match('/data-ad-client=["\']([^"\']+)["\']/', $ad_code, $matches)) {
            $client_id = $matches[1];
        }
        
        if (preg_match('/data-ad-slot=["\']([^"\']+)["\']/', $ad_code, $matches)) {
            $ad_slot = $matches[1];
        }
        
        if ($client_id && $ad_slot) {
            return sprintf(
                '<amp-ad width="300" height="250" type="adsense" data-ad-client="%s" data-ad-slot="%s"></amp-ad>',
                esc_attr($client_id),
                esc_attr($ad_slot)
            );
        }
        
        return $ad_code; // Retornar original se não conseguir converter
    }
}