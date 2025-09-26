<?php
// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Processar formulário de configurações
if (isset($_POST['submit']) && wp_verify_nonce($_POST['amp_settings_nonce'], 'amp_settings_action')) {
    $options = array(
        'adsense_client_id' => sanitize_text_field($_POST['adsense_client_id']),
        'adsense_auto_ads' => isset($_POST['adsense_auto_ads']) ? 1 : 0,
        'enable_lazy_loading' => isset($_POST['enable_lazy_loading']) ? 1 : 0,
        'ad_blocker_detection' => isset($_POST['ad_blocker_detection']) ? 1 : 0,
        'gdpr_compliance' => isset($_POST['gdpr_compliance']) ? 1 : 0,
        'custom_css' => wp_kses_post($_POST['custom_css']),
        'exclude_user_roles' => isset($_POST['exclude_user_roles']) ? array_map('sanitize_text_field', $_POST['exclude_user_roles']) : array(),
        'exclude_pages' => sanitize_textarea_field($_POST['exclude_pages']),
        'mobile_optimization' => isset($_POST['mobile_optimization']) ? 1 : 0,
        'cache_compatibility' => isset($_POST['cache_compatibility']) ? 1 : 0,
        'analytics_tracking' => isset($_POST['analytics_tracking']) ? 1 : 0,
        'ad_refresh_interval' => intval($_POST['ad_refresh_interval']),
        'max_ads_per_page' => intval($_POST['max_ads_per_page']),
        'ad_blocker_message' => wp_kses_post($_POST['ad_blocker_message'])
    );
    
    update_option('amp_settings', $options);
    echo '<div class="notice notice-success"><p>' . __('Configurações salvas com sucesso!', 'adsense-master-pro') . '</p></div>';
}

// Carregar configurações atuais
$settings = get_option('amp_settings', array());
$defaults = array(
    'adsense_client_id' => '',
    'adsense_auto_ads' => 0,
    'enable_lazy_loading' => 1,
    'ad_blocker_detection' => 1,
    'gdpr_compliance' => 1,
    'custom_css' => '',
    'exclude_user_roles' => array(),
    'exclude_pages' => '',
    'mobile_optimization' => 1,
    'cache_compatibility' => 1,
    'analytics_tracking' => 0,
    'ad_refresh_interval' => 0,
    'max_ads_per_page' => 10,
    'ad_blocker_message' => __('Por favor, desative seu bloqueador de anúncios para apoiar nosso site.', 'adsense-master-pro')
);
$settings = wp_parse_args($settings, $defaults);
?>

<div class="wrap">
    <h1><?php _e('AdSense Master Pro - Configurações', 'adsense-master-pro'); ?></h1>
    
    <div class="amp-settings-container">
        <form method="post" action="">
            <?php wp_nonce_field('amp_settings_action', 'amp_settings_nonce'); ?>
            
            <div class="amp-settings-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#general" class="nav-tab nav-tab-active"><?php _e('Geral', 'adsense-master-pro'); ?></a>
                    <a href="#adsense" class="nav-tab"><?php _e('AdSense', 'adsense-master-pro'); ?></a>
                    <a href="#display" class="nav-tab"><?php _e('Exibição', 'adsense-master-pro'); ?></a>
                    <a href="#advanced" class="nav-tab"><?php _e('Avançado', 'adsense-master-pro'); ?></a>
                    <a href="#performance" class="nav-tab"><?php _e('Performance', 'adsense-master-pro'); ?></a>
                </nav>
                
                <!-- Aba Geral -->
                <div id="general" class="amp-tab-content active">
                    <h2><?php _e('Configurações Gerais', 'adsense-master-pro'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Lazy Loading de Anúncios', 'adsense-master-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="enable_lazy_loading" value="1" <?php checked($settings['enable_lazy_loading'], 1); ?>>
                                    <?php _e('Ativar carregamento sob demanda para melhorar a velocidade', 'adsense-master-pro'); ?>
                                </label>
                                <p class="description"><?php _e('Os anúncios serão carregados apenas quando estiverem visíveis na tela.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Detecção de Ad Blocker', 'adsense-master-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="ad_blocker_detection" value="1" <?php checked($settings['ad_blocker_detection'], 1); ?>>
                                    <?php _e('Detectar bloqueadores de anúncios', 'adsense-master-pro'); ?>
                                </label>
                                <p class="description"><?php _e('Exibe uma mensagem quando um bloqueador de anúncios é detectado.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Conformidade GDPR', 'adsense-master-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="gdpr_compliance" value="1" <?php checked($settings['gdpr_compliance'], 1); ?>>
                                    <?php _e('Ativar recursos de conformidade GDPR', 'adsense-master-pro'); ?>
                                </label>
                                <p class="description"><?php _e('Adiciona código de consentimento para cookies e dados pessoais.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Máximo de Anúncios por Página', 'adsense-master-pro'); ?></th>
                            <td>
                                <input type="number" name="max_ads_per_page" value="<?php echo esc_attr($settings['max_ads_per_page']); ?>" min="1" max="20" class="small-text">
                                <p class="description"><?php _e('Limite o número de anúncios exibidos por página para melhor experiência do usuário.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Aba AdSense -->
                <div id="adsense" class="amp-tab-content">
                    <h2><?php _e('Configurações do Google AdSense', 'adsense-master-pro'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('ID do Cliente AdSense', 'adsense-master-pro'); ?></th>
                            <td>
                                <input type="text" name="adsense_client_id" value="<?php echo esc_attr($settings['adsense_client_id']); ?>" class="regular-text" placeholder="ca-pub-1234567890123456">
                                <p class="description"><?php _e('Seu ID de cliente do Google AdSense (formato: ca-pub-xxxxxxxxxxxxxxxx)', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Auto Ads do AdSense', 'adsense-master-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="adsense_auto_ads" value="1" <?php checked($settings['adsense_auto_ads'], 1); ?>>
                                    <?php _e('Ativar Auto Ads do Google AdSense', 'adsense-master-pro'); ?>
                                </label>
                                <p class="description"><?php _e('Permite que o Google coloque anúncios automaticamente em seu site.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Aba Exibição -->
                <div id="display" class="amp-tab-content">
                    <h2><?php _e('Configurações de Exibição', 'adsense-master-pro'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Otimização Mobile', 'adsense-master-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="mobile_optimization" value="1" <?php checked($settings['mobile_optimization'], 1); ?>>
                                    <?php _e('Otimizar anúncios para dispositivos móveis', 'adsense-master-pro'); ?>
                                </label>
                                <p class="description"><?php _e('Ajusta automaticamente o tamanho e posição dos anúncios em telas menores.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Excluir Funções de Usuário', 'adsense-master-pro'); ?></th>
                            <td>
                                <?php
                                $roles = wp_roles()->get_names();
                                foreach ($roles as $role_key => $role_name) {
                                    $checked = in_array($role_key, $settings['exclude_user_roles']) ? 'checked' : '';
                                    echo '<label><input type="checkbox" name="exclude_user_roles[]" value="' . esc_attr($role_key) . '" ' . $checked . '> ' . esc_html($role_name) . '</label><br>';
                                }
                                ?>
                                <p class="description"><?php _e('Usuários com essas funções não verão anúncios.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Excluir Páginas/Posts', 'adsense-master-pro'); ?></th>
                            <td>
                                <textarea name="exclude_pages" rows="5" class="large-text"><?php echo esc_textarea($settings['exclude_pages']); ?></textarea>
                                <p class="description"><?php _e('IDs de páginas/posts onde os anúncios não devem aparecer (um por linha).', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('CSS Personalizado', 'adsense-master-pro'); ?></th>
                            <td>
                                <textarea name="custom_css" rows="10" class="large-text code"><?php echo esc_textarea($settings['custom_css']); ?></textarea>
                                <p class="description"><?php _e('CSS personalizado para estilizar os anúncios.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Aba Avançado -->
                <div id="advanced" class="amp-tab-content">
                    <h2><?php _e('Configurações Avançadas', 'adsense-master-pro'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Compatibilidade com Cache', 'adsense-master-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="cache_compatibility" value="1" <?php checked($settings['cache_compatibility'], 1); ?>>
                                    <?php _e('Ativar modo de compatibilidade com plugins de cache', 'adsense-master-pro'); ?>
                                </label>
                                <p class="description"><?php _e('Melhora a compatibilidade com WP Rocket, W3 Total Cache, etc.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Intervalo de Atualização', 'adsense-master-pro'); ?></th>
                            <td>
                                <select name="ad_refresh_interval">
                                    <option value="0" <?php selected($settings['ad_refresh_interval'], 0); ?>><?php _e('Desabilitado', 'adsense-master-pro'); ?></option>
                                    <option value="30" <?php selected($settings['ad_refresh_interval'], 30); ?>><?php _e('30 segundos', 'adsense-master-pro'); ?></option>
                                    <option value="60" <?php selected($settings['ad_refresh_interval'], 60); ?>><?php _e('1 minuto', 'adsense-master-pro'); ?></option>
                                    <option value="120" <?php selected($settings['ad_refresh_interval'], 120); ?>><?php _e('2 minutos', 'adsense-master-pro'); ?></option>
                                    <option value="300" <?php selected($settings['ad_refresh_interval'], 300); ?>><?php _e('5 minutos', 'adsense-master-pro'); ?></option>
                                </select>
                                <p class="description"><?php _e('Intervalo para atualizar anúncios automaticamente (use com cuidado).', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Rastreamento de Analytics', 'adsense-master-pro'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="analytics_tracking" value="1" <?php checked($settings['analytics_tracking'], 1); ?>>
                                    <?php _e('Ativar rastreamento de impressões e cliques', 'adsense-master-pro'); ?>
                                </label>
                                <p class="description"><?php _e('Coleta dados sobre performance dos anúncios (armazenados localmente).', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Mensagem do Ad Blocker', 'adsense-master-pro'); ?></th>
                            <td>
                                <textarea name="ad_blocker_message" rows="3" class="large-text"><?php echo esc_textarea($settings['ad_blocker_message']); ?></textarea>
                                <p class="description"><?php _e('Mensagem exibida quando um bloqueador de anúncios é detectado.', 'adsense-master-pro'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Aba Performance -->
                <div id="performance" class="amp-tab-content">
                    <h2><?php _e('Relatórios de Performance', 'adsense-master-pro'); ?></h2>
                    
                    <div class="amp-performance-stats">
                        <div class="amp-stat-card">
                            <h3><?php _e('Anúncios Ativos', 'adsense-master-pro'); ?></h3>
                            <div class="amp-stat-number">
                                <?php
                                global $wpdb;
                                $active_ads = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}amp_ads WHERE status = 'active'");
                                echo $active_ads;
                                ?>
                            </div>
                        </div>
                        
                        <div class="amp-stat-card">
                            <h3><?php _e('Impressões (30 dias)', 'adsense-master-pro'); ?></h3>
                            <div class="amp-stat-number">0</div>
                            <small><?php _e('Em breve', 'adsense-master-pro'); ?></small>
                        </div>
                        
                        <div class="amp-stat-card">
                            <h3><?php _e('Cliques (30 dias)', 'adsense-master-pro'); ?></h3>
                            <div class="amp-stat-number">0</div>
                            <small><?php _e('Em breve', 'adsense-master-pro'); ?></small>
                        </div>
                        
                        <div class="amp-stat-card">
                            <h3><?php _e('CTR Médio', 'adsense-master-pro'); ?></h3>
                            <div class="amp-stat-number">0%</div>
                            <small><?php _e('Em breve', 'adsense-master-pro'); ?></small>
                        </div>
                    </div>
                    
                    <div class="amp-performance-tools">
                        <h3><?php _e('Ferramentas de Performance', 'adsense-master-pro'); ?></h3>
                        <p><?php _e('Ferramentas para otimizar a performance dos seus anúncios:', 'adsense-master-pro'); ?></p>
                        
                        <div class="amp-tools-grid">
                            <div class="amp-tool-card">
                                <h4><?php _e('Teste A/B', 'adsense-master-pro'); ?></h4>
                                <p><?php _e('Compare diferentes posições e formatos de anúncios.', 'adsense-master-pro'); ?></p>
                                <button class="button" disabled><?php _e('Em breve', 'adsense-master-pro'); ?></button>
                            </div>
                            
                            <div class="amp-tool-card">
                                <h4><?php _e('Heatmap de Cliques', 'adsense-master-pro'); ?></h4>
                                <p><?php _e('Visualize onde os usuários mais clicam em seus anúncios.', 'adsense-master-pro'); ?></p>
                                <button class="button" disabled><?php _e('Em breve', 'adsense-master-pro'); ?></button>
                            </div>
                            
                            <div class="amp-tool-card">
                                <h4><?php _e('Relatórios Detalhados', 'adsense-master-pro'); ?></h4>
                                <p><?php _e('Análises detalhadas de performance por período.', 'adsense-master-pro'); ?></p>
                                <button class="button" disabled><?php _e('Em breve', 'adsense-master-pro'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <p class="submit">
                <input type="submit" name="submit" class="button-primary" value="<?php _e('Salvar Configurações', 'adsense-master-pro'); ?>">
                <button type="button" class="button" id="reset-settings"><?php _e('Restaurar Padrões', 'adsense-master-pro'); ?></button>
            </p>
        </form>
    </div>
</div>

<style>
.amp-settings-container { max-width: 1000px; }
.amp-settings-tabs { margin-top: 20px; }
.amp-tab-content { display: none; background: #fff; padding: 20px; border: 1px solid #ddd; border-top: none; }
.amp-tab-content.active { display: block; }
.amp-performance-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
.amp-stat-card { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; text-align: center; }
.amp-stat-card h3 { margin: 0 0 10px; font-size: 14px; color: #666; }
.amp-stat-number { font-size: 2.5em; font-weight: bold; color: #0073aa; margin: 10px 0; }
.amp-tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
.amp-tool-card { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 4px; }
.amp-tool-card h4 { margin: 0 0 10px; }
.amp-tool-card p { margin: 0 0 15px; color: #666; }
</style>

<script>
jQuery(document).ready(function($) {
    // Navegação por abas
    $('.nav-tab').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.amp-tab-content').removeClass('active');
        $(target).addClass('active');
    });
    
    // Reset configurações
    $('#reset-settings').click(function() {
        if (confirm('<?php _e('Tem certeza que deseja restaurar as configurações padrão?', 'adsense-master-pro'); ?>')) {
            // Implementar reset
            location.reload();
        }
    });
});
</script>