<?php
/**
 * Classe de Configurações Avançadas do AdSense Master Pro
 * 
 * @package AdSenseMasterPro
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AMP_Settings {
    
    private $options;
    private $tabs;
    
    public function __construct() {
        $this->options = get_option('amp_options', array());
        $this->init_tabs();
        
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    private function init_tabs() {
        $this->tabs = array(
            'general' => __('Geral', 'adsense-master-pro'),
            'adsense' => __('AdSense', 'adsense-master-pro'),
            'positioning' => __('Posicionamento', 'adsense-master-pro'),
            'optimization' => __('Otimização', 'adsense-master-pro'),
            'analytics' => __('Analytics', 'adsense-master-pro'),
            'ab_testing' => __('A/B Testing', 'adsense-master-pro'),
            'amp' => __('AMP', 'adsense-master-pro'),
            'gdpr' => __('GDPR', 'adsense-master-pro'),
            'advanced' => __('Avançado', 'adsense-master-pro')
        );
    }
    
    public function register_settings() {
        register_setting('amp_settings', 'amp_options', array($this, 'sanitize_options'));
        
        // Seções de configuração
        $this->register_general_settings();
        $this->register_adsense_settings();
        $this->register_positioning_settings();
        $this->register_optimization_settings();
        $this->register_analytics_settings();
        $this->register_ab_testing_settings();
        $this->register_amp_settings();
        $this->register_gdpr_settings();
        $this->register_advanced_settings();
    }
    
    private function register_general_settings() {
        add_settings_section(
            'amp_general_section',
            __('Configurações Gerais', 'adsense-master-pro'),
            array($this, 'general_section_callback'),
            'amp_general'
        );
        
        add_settings_field(
            'enable_adsense',
            __('Habilitar AdSense', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_general',
            'amp_general_section',
            array('name' => 'enable_adsense', 'description' => 'Ativar/desativar o sistema de anúncios')
        );
        
        add_settings_field(
            'max_ads_per_page',
            __('Máximo de Anúncios por Página', 'adsense-master-pro'),
            array($this, 'number_field'),
            'amp_general',
            'amp_general_section',
            array('name' => 'max_ads_per_page', 'min' => 1, 'max' => 20, 'description' => 'Limite de anúncios por página')
        );
        
        add_settings_field(
            'exclude_user_roles',
            __('Excluir Funções de Usuário', 'adsense-master-pro'),
            array($this, 'multiselect_field'),
            'amp_general',
            'amp_general_section',
            array('name' => 'exclude_user_roles', 'options' => $this->get_user_roles(), 'description' => 'Usuários com essas funções não verão anúncios')
        );
    }
    
    private function register_adsense_settings() {
        add_settings_section(
            'amp_adsense_section',
            __('Configurações do Google AdSense', 'adsense-master-pro'),
            array($this, 'adsense_section_callback'),
            'amp_adsense'
        );
        
        add_settings_field(
            'adsense_publisher_id',
            __('Publisher ID', 'adsense-master-pro'),
            array($this, 'text_field'),
            'amp_adsense',
            'amp_adsense_section',
            array('name' => 'adsense_publisher_id', 'placeholder' => 'pub-1234567890123456', 'description' => 'Seu ID de editor do AdSense')
        );
        
        add_settings_field(
            'adsense_client_id',
            __('Client ID', 'adsense-master-pro'),
            array($this, 'text_field'),
            'amp_adsense',
            'amp_adsense_section',
            array('name' => 'adsense_client_id', 'placeholder' => 'ca-pub-1234567890123456', 'description' => 'Seu Client ID do AdSense')
        );
        
        add_settings_field(
            'auto_ads',
            __('Auto Ads', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_adsense',
            'amp_adsense_section',
            array('name' => 'auto_ads', 'description' => 'Habilitar Auto Ads do Google')
        );
    }
    
    private function register_positioning_settings() {
        add_settings_section(
            'amp_positioning_section',
            __('Configurações de Posicionamento', 'adsense-master-pro'),
            array($this, 'positioning_section_callback'),
            'amp_positioning'
        );
        
        add_settings_field(
            'smart_positioning',
            __('Posicionamento Inteligente', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_positioning',
            'amp_positioning_section',
            array('name' => 'smart_positioning', 'description' => 'Usar IA para otimizar posições dos anúncios')
        );
        
        add_settings_field(
            'position_weights',
            __('Pesos das Posições', 'adsense-master-pro'),
            array($this, 'position_weights_field'),
            'amp_positioning',
            'amp_positioning_section',
            array('name' => 'position_weights')
        );
    }
    
    private function register_optimization_settings() {
        add_settings_section(
            'amp_optimization_section',
            __('Configurações de Otimização', 'adsense-master-pro'),
            array($this, 'optimization_section_callback'),
            'amp_optimization'
        );
        
        add_settings_field(
            'auto_optimization',
            __('Otimização Automática', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_optimization',
            'amp_optimization_section',
            array('name' => 'auto_optimization', 'description' => 'Otimizar automaticamente baseado na performance')
        );
        
        add_settings_field(
            'lazy_loading',
            __('Lazy Loading', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_optimization',
            'amp_optimization_section',
            array('name' => 'lazy_loading', 'description' => 'Carregar anúncios apenas quando visíveis')
        );
        
        add_settings_field(
            'cache_ads',
            __('Cache de Anúncios', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_optimization',
            'amp_optimization_section',
            array('name' => 'cache_ads', 'description' => 'Cachear anúncios para melhor performance')
        );
        
        add_settings_field(
            'performance_mode',
            __('Modo de Performance', 'adsense-master-pro'),
            array($this, 'select_field'),
            'amp_optimization',
            'amp_optimization_section',
            array(
                'name' => 'performance_mode',
                'options' => array(
                    'conservative' => 'Conservador',
                    'balanced' => 'Balanceado',
                    'aggressive' => 'Agressivo'
                ),
                'description' => 'Nível de otimização de performance'
            )
        );
    }
    
    private function register_analytics_settings() {
        add_settings_section(
            'amp_analytics_section',
            __('Configurações de Analytics', 'adsense-master-pro'),
            array($this, 'analytics_section_callback'),
            'amp_analytics'
        );
        
        add_settings_field(
            'analytics_tracking',
            __('Rastreamento de Analytics', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_analytics',
            'amp_analytics_section',
            array('name' => 'analytics_tracking', 'description' => 'Rastrear impressões e cliques')
        );
        
        add_settings_field(
            'google_analytics_id',
            __('Google Analytics ID', 'adsense-master-pro'),
            array($this, 'text_field'),
            'amp_analytics',
            'amp_analytics_section',
            array('name' => 'google_analytics_id', 'placeholder' => 'UA-XXXXXXXXX-X', 'description' => 'ID do Google Analytics')
        );
        
        add_settings_field(
            'data_retention',
            __('Retenção de Dados (dias)', 'adsense-master-pro'),
            array($this, 'number_field'),
            'amp_analytics',
            'amp_analytics_section',
            array('name' => 'data_retention', 'min' => 30, 'max' => 365, 'description' => 'Tempo para manter dados de analytics')
        );
    }
    
    private function register_ab_testing_settings() {
        add_settings_section(
            'amp_ab_testing_section',
            __('Configurações de A/B Testing', 'adsense-master-pro'),
            array($this, 'ab_testing_section_callback'),
            'amp_ab_testing'
        );
        
        add_settings_field(
            'ab_testing',
            __('Habilitar A/B Testing', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_ab_testing',
            'amp_ab_testing_section',
            array('name' => 'ab_testing', 'description' => 'Ativar testes A/B para anúncios')
        );
        
        add_settings_field(
            'ab_confidence_level',
            __('Nível de Confiança (%)', 'adsense-master-pro'),
            array($this, 'number_field'),
            'amp_ab_testing',
            'amp_ab_testing_section',
            array('name' => 'ab_confidence_level', 'min' => 80, 'max' => 99, 'description' => 'Nível de confiança estatística')
        );
        
        add_settings_field(
            'ab_min_sample_size',
            __('Tamanho Mínimo da Amostra', 'adsense-master-pro'),
            array($this, 'number_field'),
            'amp_ab_testing',
            'amp_ab_testing_section',
            array('name' => 'ab_min_sample_size', 'min' => 100, 'max' => 10000, 'description' => 'Mínimo de impressões para validar teste')
        );
    }
    
    private function register_amp_settings() {
        add_settings_section(
            'amp_amp_section',
            __('Configurações AMP', 'adsense-master-pro'),
            array($this, 'amp_section_callback'),
            'amp_amp'
        );
        
        add_settings_field(
            'enable_amp',
            __('Habilitar Suporte AMP', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_amp',
            'amp_amp_section',
            array('name' => 'enable_amp', 'description' => 'Suporte para páginas AMP')
        );
        
        add_settings_field(
            'amp_auto_ads',
            __('AMP Auto Ads', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_amp',
            'amp_amp_section',
            array('name' => 'amp_auto_ads', 'description' => 'Auto Ads em páginas AMP')
        );
    }
    
    private function register_gdpr_settings() {
        add_settings_section(
            'amp_gdpr_section',
            __('Configurações GDPR', 'adsense-master-pro'),
            array($this, 'gdpr_section_callback'),
            'amp_gdpr'
        );
        
        add_settings_field(
            'gdpr_consent',
            __('Consentimento GDPR', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_gdpr',
            'amp_gdpr_section',
            array('name' => 'gdpr_consent', 'description' => 'Exigir consentimento para cookies')
        );
        
        add_settings_field(
            'gdpr_message',
            __('Mensagem GDPR', 'adsense-master-pro'),
            array($this, 'textarea_field'),
            'amp_gdpr',
            'amp_gdpr_section',
            array('name' => 'gdpr_message', 'description' => 'Mensagem de consentimento de cookies')
        );
    }
    
    private function register_advanced_settings() {
        add_settings_section(
            'amp_advanced_section',
            __('Configurações Avançadas', 'adsense-master-pro'),
            array($this, 'advanced_section_callback'),
            'amp_advanced'
        );
        
        add_settings_field(
            'custom_css',
            __('CSS Personalizado', 'adsense-master-pro'),
            array($this, 'textarea_field'),
            'amp_advanced',
            'amp_advanced_section',
            array('name' => 'custom_css', 'description' => 'CSS personalizado para anúncios')
        );
        
        add_settings_field(
            'debug_mode',
            __('Modo Debug', 'adsense-master-pro'),
            array($this, 'checkbox_field'),
            'amp_advanced',
            'amp_advanced_section',
            array('name' => 'debug_mode', 'description' => 'Ativar logs de debug')
        );
    }
    
    // Callbacks das seções
    public function general_section_callback() {
        echo '<p>' . __('Configurações gerais do plugin.', 'adsense-master-pro') . '</p>';
    }
    
    public function adsense_section_callback() {
        echo '<p>' . __('Configure sua conta do Google AdSense.', 'adsense-master-pro') . '</p>';
    }
    
    public function positioning_section_callback() {
        echo '<p>' . __('Configure como e onde os anúncios serão posicionados.', 'adsense-master-pro') . '</p>';
    }
    
    public function optimization_section_callback() {
        echo '<p>' . __('Configurações para otimizar a performance dos anúncios.', 'adsense-master-pro') . '</p>';
    }
    
    public function analytics_section_callback() {
        echo '<p>' . __('Configure o rastreamento e análise de dados.', 'adsense-master-pro') . '</p>';
    }
    
    public function ab_testing_section_callback() {
        echo '<p>' . __('Configure testes A/B para otimizar seus anúncios.', 'adsense-master-pro') . '</p>';
    }
    
    public function amp_section_callback() {
        echo '<p>' . __('Configurações para páginas AMP (Accelerated Mobile Pages).', 'adsense-master-pro') . '</p>';
    }
    
    public function gdpr_section_callback() {
        echo '<p>' . __('Configurações de conformidade com GDPR.', 'adsense-master-pro') . '</p>';
    }
    
    public function advanced_section_callback() {
        echo '<p>' . __('Configurações avançadas para usuários experientes.', 'adsense-master-pro') . '</p>';
    }
    
    // Campos de formulário
    public function checkbox_field($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : 0;
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<label>';
        echo '<input type="checkbox" name="amp_options[' . $name . ']" value="1" ' . checked(1, $value, false) . ' />';
        echo ' ' . $description;
        echo '</label>';
    }
    
    public function text_field($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="text" name="amp_options[' . $name . ']" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '" class="regular-text" />';
        if ($description) {
            echo '<p class="description">' . $description . '</p>';
        }
    }
    
    public function number_field($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : '';
        $min = isset($args['min']) ? $args['min'] : '';
        $max = isset($args['max']) ? $args['max'] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<input type="number" name="amp_options[' . $name . ']" value="' . esc_attr($value) . '" min="' . $min . '" max="' . $max . '" class="small-text" />';
        if ($description) {
            echo '<p class="description">' . $description . '</p>';
        }
    }
    
    public function textarea_field($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<textarea name="amp_options[' . $name . ']" rows="5" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
        if ($description) {
            echo '<p class="description">' . $description . '</p>';
        }
    }
    
    public function select_field($args) {
        $name = $args['name'];
        $value = isset($this->options[$name]) ? $this->options[$name] : '';
        $options = $args['options'];
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<select name="amp_options[' . $name . ']">';
        foreach ($options as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        if ($description) {
            echo '<p class="description">' . $description . '</p>';
        }
    }
    
    public function multiselect_field($args) {
        $name = $args['name'];
        $values = isset($this->options[$name]) ? (array) $this->options[$name] : array();
        $options = $args['options'];
        $description = isset($args['description']) ? $args['description'] : '';
        
        echo '<select name="amp_options[' . $name . '][]" multiple="multiple" size="5">';
        foreach ($options as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . (in_array($key, $values) ? 'selected="selected"' : '') . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        if ($description) {
            echo '<p class="description">' . $description . '</p>';
        }
    }
    
    public function position_weights_field($args) {
        $name = $args['name'];
        $values = isset($this->options[$name]) ? $this->options[$name] : array();
        
        $positions = array(
            'before_content' => 'Antes do Conteúdo',
            'after_first_paragraph' => 'Após Primeiro Parágrafo',
            'middle_content' => 'Meio do Conteúdo',
            'after_content' => 'Após Conteúdo',
            'sidebar' => 'Sidebar',
            'footer' => 'Rodapé'
        );
        
        echo '<table class="form-table">';
        foreach ($positions as $key => $label) {
            $weight = isset($values[$key]) ? $values[$key] : 50;
            echo '<tr>';
            echo '<td>' . $label . '</td>';
            echo '<td><input type="range" name="amp_options[' . $name . '][' . $key . ']" value="' . $weight . '" min="0" max="100" oninput="this.nextElementSibling.value = this.value" /></td>';
            echo '<td><output>' . $weight . '</output>%</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    
    private function get_user_roles() {
        global $wp_roles;
        $roles = array();
        
        foreach ($wp_roles->roles as $key => $role) {
            $roles[$key] = $role['name'];
        }
        
        return $roles;
    }
    
    public function sanitize_options($input) {
        $sanitized = array();
        
        // Sanitizar cada campo baseado no tipo
        $text_fields = array('adsense_publisher_id', 'adsense_client_id', 'google_analytics_id', 'gdpr_message', 'custom_css');
        $number_fields = array('max_ads_per_page', 'data_retention', 'ab_confidence_level', 'ab_min_sample_size');
        $checkbox_fields = array('enable_adsense', 'auto_ads', 'smart_positioning', 'auto_optimization', 'lazy_loading', 'cache_ads', 'analytics_tracking', 'ab_testing', 'enable_amp', 'amp_auto_ads', 'gdpr_consent', 'debug_mode');
        $select_fields = array('performance_mode');
        $array_fields = array('exclude_user_roles', 'position_weights');
        
        foreach ($text_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_text_field($input[$field]);
            }
        }
        
        foreach ($number_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = intval($input[$field]);
            }
        }
        
        foreach ($checkbox_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? 1 : 0;
        }
        
        foreach ($select_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = sanitize_text_field($input[$field]);
            }
        }
        
        foreach ($array_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = array_map('sanitize_text_field', (array) $input[$field]);
            }
        }
        
        return $sanitized;
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'adsense-master-pro') === false) return;
        
        wp_enqueue_script('amp-admin-settings', AMP_PLUGIN_URL . 'assets/js/admin-settings.js', array('jquery'), AMP_VERSION, true);
        wp_enqueue_style('amp-admin-settings', AMP_PLUGIN_URL . 'assets/css/admin-settings.css', array(), AMP_VERSION);
    }
    
    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <nav class="nav-tab-wrapper">
                <?php foreach ($this->tabs as $tab_key => $tab_label): ?>
                    <a href="?page=adsense-master-pro-settings&tab=<?php echo $tab_key; ?>" 
                       class="nav-tab <?php echo $active_tab == $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo $tab_label; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('amp_settings');
                do_settings_sections('amp_' . $active_tab);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    public function get_tabs() {
        return $this->tabs;
    }
}