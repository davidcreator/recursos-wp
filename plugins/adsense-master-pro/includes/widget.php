<?php
// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Widget de Anúncios do AdSense Master Pro
 */
class AMP_Ad_Widget extends WP_Widget {
    
    /**
     * Construtor do widget
     */
    public function __construct() {
        parent::__construct(
            'amp_ad_widget',
            __('AdSense Master Pro - Anúncio', 'adsense-master-pro'),
            array(
                'description' => __('Exibe um anúncio específico do AdSense Master Pro', 'adsense-master-pro'),
                'classname' => 'amp-ad-widget'
            )
        );
    }
    
    /**
     * Exibe o widget no frontend
     */
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $ad_id = isset($instance['ad_id']) ? intval($instance['ad_id']) : 0;
        $show_title = isset($instance['show_title']) ? $instance['show_title'] : false;
        
        echo $args['before_widget'];
        
        if (!empty($title) && $show_title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        if ($ad_id > 0) {
            // Usar a função global do plugin para exibir o anúncio
            if (function_exists('amp_display_ad')) {
                amp_display_ad($ad_id);
            }
        } else {
            if (current_user_can('manage_options')) {
                echo '<p style="padding: 10px; background: #f0f0f0; border: 1px dashed #ccc;">';
                echo __('Nenhum anúncio selecionado. Configure o widget no painel administrativo.', 'adsense-master-pro');
                echo '</p>';
            }
        }
        
        echo $args['after_widget'];
    }
    
    /**
     * Formulário de configuração do widget no admin
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $ad_id = isset($instance['ad_id']) ? intval($instance['ad_id']) : 0;
        $show_title = isset($instance['show_title']) ? $instance['show_title'] : false;
        
        // Buscar anúncios disponíveis
        global $wpdb;
        $ads = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}amp_ads WHERE status = 'active' ORDER BY name");
        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'adsense-master-pro'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_title); ?> id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" />
            <label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Exibir título', 'adsense-master-pro'); ?></label>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('ad_id'); ?>"><?php _e('Selecionar Anúncio:', 'adsense-master-pro'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('ad_id'); ?>" name="<?php echo $this->get_field_name('ad_id'); ?>">
                <option value="0"><?php _e('-- Selecione um anúncio --', 'adsense-master-pro'); ?></option>
                <?php foreach ($ads as $ad): ?>
                    <option value="<?php echo $ad->id; ?>" <?php selected($ad_id, $ad->id); ?>>
                        <?php echo esc_html($ad->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        
        <?php if (empty($ads)): ?>
            <p style="color: #d54e21;">
                <strong><?php _e('Nenhum anúncio ativo encontrado.', 'adsense-master-pro'); ?></strong><br>
                <a href="<?php echo admin_url('admin.php?page=adsense-master-pro'); ?>" target="_blank">
                    <?php _e('Criar um novo anúncio', 'adsense-master-pro'); ?>
                </a>
            </p>
        <?php endif; ?>
        
        <p>
            <small><?php _e('Este widget exibirá o anúncio selecionado respeitando todas as configurações de exibição definidas no plugin.', 'adsense-master-pro'); ?></small>
        </p>
        
        <?php
    }
    
    /**
     * Salva as configurações do widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['ad_id'] = (!empty($new_instance['ad_id'])) ? intval($new_instance['ad_id']) : 0;
        $instance['show_title'] = isset($new_instance['show_title']) ? (bool) $new_instance['show_title'] : false;
        
        return $instance;
    }
}

/**
 * Widget Avançado de Anúncios com Múltiplas Opções
 */
class AMP_Advanced_Ad_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'amp_advanced_ad_widget',
            __('AdSense Master Pro - Anúncio Avançado', 'adsense-master-pro'),
            array(
                'description' => __('Widget avançado com múltiplas opções de exibição de anúncios', 'adsense-master-pro'),
                'classname' => 'amp-advanced-ad-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $display_type = isset($instance['display_type']) ? $instance['display_type'] : 'single';
        $ad_ids = isset($instance['ad_ids']) ? $instance['ad_ids'] : array();
        $rotation_type = isset($instance['rotation_type']) ? $instance['rotation_type'] : 'random';
        $show_title = isset($instance['show_title']) ? $instance['show_title'] : false;
        $custom_class = isset($instance['custom_class']) ? $instance['custom_class'] : '';
        
        echo $args['before_widget'];
        
        if (!empty($title) && $show_title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $widget_class = 'amp-advanced-widget';
        if (!empty($custom_class)) {
            $widget_class .= ' ' . sanitize_html_class($custom_class);
        }
        
        echo '<div class="' . esc_attr($widget_class) . '">';
        
        if ($display_type === 'single' && !empty($ad_ids)) {
            $ad_id = $rotation_type === 'random' ? $ad_ids[array_rand($ad_ids)] : $ad_ids[0];
            if (function_exists('amp_display_ad')) {
                amp_display_ad($ad_id);
            }
        } elseif ($display_type === 'multiple' && !empty($ad_ids)) {
            foreach ($ad_ids as $ad_id) {
                if (function_exists('amp_display_ad')) {
                    echo '<div class="amp-widget-ad-item">';
                    amp_display_ad($ad_id);
                    echo '</div>';
                }
            }
        } else {
            if (current_user_can('manage_options')) {
                echo '<p style="padding: 10px; background: #f0f0f0; border: 1px dashed #ccc;">';
                echo __('Configure o widget para exibir anúncios.', 'adsense-master-pro');
                echo '</p>';
            }
        }
        
        echo '</div>';
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $display_type = isset($instance['display_type']) ? $instance['display_type'] : 'single';
        $ad_ids = isset($instance['ad_ids']) ? $instance['ad_ids'] : array();
        $rotation_type = isset($instance['rotation_type']) ? $instance['rotation_type'] : 'random';
        $show_title = isset($instance['show_title']) ? $instance['show_title'] : false;
        $custom_class = isset($instance['custom_class']) ? $instance['custom_class'] : '';
        
        // Buscar anúncios disponíveis
        global $wpdb;
        $ads = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}amp_ads WHERE status = 'active' ORDER BY name");
        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'adsense-master-pro'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_title); ?> id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" />
            <label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Exibir título', 'adsense-master-pro'); ?></label>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('display_type'); ?>"><?php _e('Tipo de Exibição:', 'adsense-master-pro'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('display_type'); ?>" name="<?php echo $this->get_field_name('display_type'); ?>">
                <option value="single" <?php selected($display_type, 'single'); ?>><?php _e('Anúncio Único', 'adsense-master-pro'); ?></option>
                <option value="multiple" <?php selected($display_type, 'multiple'); ?>><?php _e('Múltiplos Anúncios', 'adsense-master-pro'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('rotation_type'); ?>"><?php _e('Tipo de Rotação:', 'adsense-master-pro'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('rotation_type'); ?>" name="<?php echo $this->get_field_name('rotation_type'); ?>">
                <option value="random" <?php selected($rotation_type, 'random'); ?>><?php _e('Aleatório', 'adsense-master-pro'); ?></option>
                <option value="sequential" <?php selected($rotation_type, 'sequential'); ?>><?php _e('Sequencial', 'adsense-master-pro'); ?></option>
            </select>
        </p>
        
        <p>
            <label><?php _e('Selecionar Anúncios:', 'adsense-master-pro'); ?></label><br>
            <?php foreach ($ads as $ad): ?>
                <label style="display: block; margin: 5px 0;">
                    <input type="checkbox" name="<?php echo $this->get_field_name('ad_ids'); ?>[]" value="<?php echo $ad->id; ?>" <?php checked(in_array($ad->id, $ad_ids)); ?> />
                    <?php echo esc_html($ad->name); ?>
                </label>
            <?php endforeach; ?>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('custom_class'); ?>"><?php _e('Classe CSS Personalizada:', 'adsense-master-pro'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('custom_class'); ?>" name="<?php echo $this->get_field_name('custom_class'); ?>" type="text" value="<?php echo esc_attr($custom_class); ?>" />
            <small><?php _e('Adicione classes CSS personalizadas para estilização', 'adsense-master-pro'); ?></small>
        </p>
        
        <?php if (empty($ads)): ?>
            <p style="color: #d54e21;">
                <strong><?php _e('Nenhum anúncio ativo encontrado.', 'adsense-master-pro'); ?></strong><br>
                <a href="<?php echo admin_url('admin.php?page=adsense-master-pro'); ?>" target="_blank">
                    <?php _e('Criar um novo anúncio', 'adsense-master-pro'); ?>
                </a>
            </p>
        <?php endif; ?>
        
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['display_type'] = (!empty($new_instance['display_type'])) ? $new_instance['display_type'] : 'single';
        $instance['ad_ids'] = isset($new_instance['ad_ids']) ? array_map('intval', $new_instance['ad_ids']) : array();
        $instance['rotation_type'] = (!empty($new_instance['rotation_type'])) ? $new_instance['rotation_type'] : 'random';
        $instance['show_title'] = isset($new_instance['show_title']) ? (bool) $new_instance['show_title'] : false;
        $instance['custom_class'] = (!empty($new_instance['custom_class'])) ? sanitize_html_class($new_instance['custom_class']) : '';
        
        return $instance;
    }
}

/**
 * Registrar os widgets
 */
function amp_register_widgets() {
    register_widget('AMP_Ad_Widget');
    register_widget('AMP_Advanced_Ad_Widget');
}
add_action('widgets_init', 'amp_register_widgets');

/**
 * Adicionar estilos CSS para os widgets
 */
function amp_widget_styles() {
    if (is_active_widget(false, false, 'amp_ad_widget') || is_active_widget(false, false, 'amp_advanced_ad_widget')) {
        ?>
        <style>
        .amp-ad-widget, .amp-advanced-ad-widget {
            margin-bottom: 20px;
        }
        .amp-advanced-widget .amp-widget-ad-item {
            margin-bottom: 15px;
        }
        .amp-advanced-widget .amp-widget-ad-item:last-child {
            margin-bottom: 0;
        }
        .widget .amp-ad-container {
            text-align: center;
        }
        .widget .amp-ad-label {
            font-size: 11px;
            color: #999;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'amp_widget_styles');