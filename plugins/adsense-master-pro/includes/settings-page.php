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
                    <a href="#abtest" class="nav-tab"><?php _e('Teste A/B', 'adsense-master-pro'); ?></a>
                    <a href="#heatmap" class="nav-tab"><?php _e('Heatmap de Cliques', 'adsense-master-pro'); ?></a>
                    <a href="#reports" class="nav-tab"><?php _e('Relatórios', 'adsense-master-pro'); ?></a>
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
                
                <div id="heatmap" class="amp-tab-content">
                    <h2><?php _e('Heatmap de Cliques', 'adsense-master-pro'); ?></h2>
                    <?php
                    global $wpdb;
                    $ads_all = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}amp_ads ORDER BY name ASC", ARRAY_A);
                    ?>
                    <div class="amp-form-row amp-form-columns">
                        <div class="amp-form-group">
                            <label for="heatmap-ad"><?php _e('Selecione o Anúncio', 'adsense-master-pro'); ?></label>
                            <select id="heatmap-ad" class="regular-text">
                                <option value=""><?php _e('Selecione', 'adsense-master-pro'); ?></option>
                                <?php foreach ($ads_all as $ad): ?>
                                    <option value="<?php echo intval($ad['id']); ?>"><?php echo '#' . intval($ad['id']) . ' - ' . esc_html($ad['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="amp-form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="button button-primary" id="load-heatmap"><?php _e('Carregar Heatmap', 'adsense-master-pro'); ?></button>
                        </div>
                    </div>
                    <div id="heatmap-container" style="position:relative; width:728px; height:250px; border:1px solid #ddd; background:#fafafa;">
                        <canvas id="heatmap-canvas" width="728" height="250"></canvas>
                    </div>
                    <p class="description"><?php _e('Visualização aproximada com base nos cliques registrados. Tamanho ajustável conforme dados coletados.', 'adsense-master-pro'); ?></p>
                </div>
                
                <div id="reports" class="amp-tab-content">
                    <h2><?php _e('Relatórios Detalhados', 'adsense-master-pro'); ?></h2>
                    <?php
                    global $wpdb;
                    $ads_list = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}amp_ads ORDER BY name ASC", ARRAY_A);
                    ?>
                    <div class="amp-form-row amp-form-columns">
                        <div class="amp-form-group">
                            <label for="report-start"><?php _e('Data Inicial', 'adsense-master-pro'); ?></label>
                            <input type="date" id="report-start" class="regular-text">
                        </div>
                        <div class="amp-form-group">
                            <label for="report-end"><?php _e('Data Final', 'adsense-master-pro'); ?></label>
                            <input type="date" id="report-end" class="regular-text">
                        </div>
                        <div class="amp-form-group">
                            <label for="report-ad"><?php _e('Anúncio', 'adsense-master-pro'); ?></label>
                            <select id="report-ad" class="regular-text">
                                <option value=""><?php _e('Todos', 'adsense-master-pro'); ?></option>
                                <?php foreach ($ads_list as $ad): ?>
                                    <option value="<?php echo intval($ad['id']); ?>"><?php echo '#' . intval($ad['id']) . ' - ' . esc_html($ad['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="amp-form-group">
                            <label for="report-device"><?php _e('Dispositivo', 'adsense-master-pro'); ?></label>
                            <select id="report-device" class="regular-text">
                                <option value=""><?php _e('Todos', 'adsense-master-pro'); ?></option>
                                <option value="mobile"><?php _e('Mobile', 'adsense-master-pro'); ?></option>
                                <option value="desktop"><?php _e('Desktop', 'adsense-master-pro'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="amp-form-row">
                        <button type="button" class="button button-primary" id="generate-report"><?php _e('Gerar Relatório', 'adsense-master-pro'); ?></button>
                    </div>
                    <div class="amp-form-row">
                        <table class="widefat fixed striped" id="report-table">
                            <thead>
                                <tr>
                                    <th><?php _e('Data', 'adsense-master-pro'); ?></th>
                                    <th><?php _e('Impressões', 'adsense-master-pro'); ?></th>
                                    <th><?php _e('Cliques', 'adsense-master-pro'); ?></th>
                                    <th><?php _e('CTR', 'adsense-master-pro'); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th><?php _e('Totais', 'adsense-master-pro'); ?></th>
                                    <th id="report-total-impr">0</th>
                                    <th id="report-total-clicks">0</th>
                                    <th id="report-total-ctr">0%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="amp-form-row">
                        <div style="position:relative; width:100%; max-width:900px;">
                            <canvas id="report-chart" width="900" height="260"></canvas>
                        </div>
                    </div>
                </div>
                
                <div id="abtest" class="amp-tab-content">
                    <h2><?php _e('Teste A/B de Anúncios', 'adsense-master-pro'); ?></h2>
                    <?php
                    global $wpdb;
                    $ads = $wpdb->get_results("SELECT id, name, position, code, status FROM {$wpdb->prefix}amp_ads WHERE status IN ('active','inactive') ORDER BY name ASC", ARRAY_A);
                    $tests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amp_ab_tests ORDER BY created_at DESC", ARRAY_A);
                    if (!function_exists('detect_ad_type')) {
                        function detect_ad_type($code) {
                            if (strpos($code, 'googlesyndication.com') !== false) return 'adsense';
                            if (strpos($code, '<img') !== false) return 'banner';
                            if (strpos($code, '<script') !== false) return 'script';
                            return 'html';
                        }
                    }
                    if (!function_exists('get_position_label')) {
                        function get_position_label($position) {
                            $labels = array(
                                'header' => __('Cabeçalho', 'adsense-master-pro'),
                                'footer' => __('Rodapé', 'adsense-master-pro'),
                                'before_content' => __('Antes do Conteúdo', 'adsense-master-pro'),
                                'after_content' => __('Depois do Conteúdo', 'adsense-master-pro'),
                                'before_paragraph' => __('Antes do 1º Parágrafo', 'adsense-master-pro'),
                                'after_paragraph' => __('Depois do 2º Parágrafo', 'adsense-master-pro'),
                                'sidebar' => __('Sidebar', 'adsense-master-pro'),
                                'custom' => __('Personalizada', 'adsense-master-pro')
                            );
                            return isset($labels[$position]) ? $labels[$position] : $position;
                        }
                    }
                    ?>
                    <div class="amp-form-row amp-form-columns">
                        <div class="amp-form-group">
                            <label for="abtest-name"><?php _e('Nome do Teste', 'adsense-master-pro'); ?></label>
                            <input type="text" id="abtest-name" class="regular-text">
                        </div>
                        <div class="amp-form-group">
                            <label for="abtest-split"><?php _e('Divisão de Tráfego (%)', 'adsense-master-pro'); ?></label>
                            <input type="number" id="abtest-split" class="small-text" min="0" max="100" value="50">
                        </div>
                        <div class="amp-form-group">
                            <label for="abtest-status"><?php _e('Status', 'adsense-master-pro'); ?></label>
                            <select id="abtest-status">
                                <option value="active"><?php _e('Ativo', 'adsense-master-pro'); ?></option>
                                <option value="inactive"><?php _e('Inativo', 'adsense-master-pro'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="amp-form-row amp-form-columns">
                        <div class="amp-form-group">
                            <label for="abtest-ad-a"><?php _e('Anúncio A', 'adsense-master-pro'); ?></label>
                            <select id="abtest-ad-a" class="regular-text">
                                <option value=""><?php _e('Selecione', 'adsense-master-pro'); ?></option>
                                <?php foreach ($ads as $ad): 
                                    $type = detect_ad_type($ad['code']);
                                    $pos = get_position_label($ad['position']);
                                ?>
                                    <option value="<?php echo intval($ad['id']); ?>">
                                        <?php echo '#' . intval($ad['id']) . ' - ' . esc_html($ad['name']) . ' (' . esc_html($pos) . ', ' . esc_html($type) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="amp-form-group">
                            <label for="abtest-ad-b"><?php _e('Anúncio B', 'adsense-master-pro'); ?></label>
                            <select id="abtest-ad-b" class="regular-text">
                                <option value=""><?php _e('Selecione', 'adsense-master-pro'); ?></option>
                                <?php foreach ($ads as $ad): 
                                    $type = detect_ad_type($ad['code']);
                                    $pos = get_position_label($ad['position']);
                                ?>
                                    <option value="<?php echo intval($ad['id']); ?>">
                                        <?php echo '#' . intval($ad['id']) . ' - ' . esc_html($ad['name']) . ' (' . esc_html($pos) . ', ' . esc_html($type) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="amp-form-row">
                        <div class="amp-form-group">
                            <label for="abtest-desc"><?php _e('Descrição', 'adsense-master-pro'); ?></label>
                            <textarea id="abtest-desc" rows="4" class="large-text"></textarea>
                        </div>
                    </div>
                    <div class="amp-form-row">
                        <button type="button" class="button button-primary" id="save-abtest"><?php _e('Salvar Teste A/B', 'adsense-master-pro'); ?></button>
                        <span id="abtest-shortcode" style="margin-left:10px;"></span>
                    </div>
                    <hr>
                    <h3><?php _e('Testes A/B Criados', 'adsense-master-pro'); ?></h3>
                    <table class="widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'adsense-master-pro'); ?></th>
                                <th><?php _e('Nome', 'adsense-master-pro'); ?></th>
                                <th><?php _e('Anúncio A', 'adsense-master-pro'); ?></th>
                                <th><?php _e('Anúncio B', 'adsense-master-pro'); ?></th>
                                <th><?php _e('Split', 'adsense-master-pro'); ?></th>
                                <th><?php _e('Status', 'adsense-master-pro'); ?></th>
                                <th><?php _e('Shortcode', 'adsense-master-pro'); ?></th>
                                <th><?php _e('Ações', 'adsense-master-pro'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tests)): ?>
                                <tr><td colspan="8"><?php _e('Nenhum teste criado.', 'adsense-master-pro'); ?></td></tr>
                            <?php else: foreach ($tests as $t):
                                $a = null; $b = null;
                                foreach ($ads as $ad) {
                                    if ($ad['id'] == $t['ad_a_id']) $a = $ad;
                                    if ($ad['id'] == $t['ad_b_id']) $b = $ad;
                                }
                            ?>
                                <tr>
                                    <td>#<?php echo intval($t['id']); ?></td>
                                    <td><?php echo esc_html($t['name']); ?></td>
                                    <td><?php echo $a ? esc_html($a['name']) . ' (' . esc_html(get_position_label($a['position'])) . ')' : '-'; ?></td>
                                    <td><?php echo $b ? esc_html($b['name']) . ' (' . esc_html(get_position_label($b['position'])) . ')' : '-'; ?></td>
                                    <td><?php echo intval($t['traffic_split']); ?>%</td>
                                    <td><?php echo esc_html($t['status']); ?></td>
                                    <td>[amp_ab_test id="<?php echo intval($t['id']); ?>"]</td>
                                    <td>
                                        <button class="button copy-abtest-shortcode" data-id="<?php echo intval($t['id']); ?>"><?php _e('Copiar', 'adsense-master-pro'); ?></button>
                                        <button class="button delete-abtest" data-id="<?php echo intval($t['id']); ?>" style="color:#a00;"><?php _e('Excluir', 'adsense-master-pro'); ?></button>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
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
    
    $('#save-abtest').on('click', function() {
        var name = $('#abtest-name').val().trim();
        var split = parseInt($('#abtest-split').val(), 10);
        var status = $('#abtest-status').val();
        var adA = parseInt($('#abtest-ad-a').val(), 10);
        var adB = parseInt($('#abtest-ad-b').val(), 10);
        var desc = $('#abtest-desc').val().trim();
        if (!name || !adA || !adB || adA === adB) {
            alert('<?php _e('Preencha o nome e selecione anúncios diferentes para A e B.', 'adsense-master-pro'); ?>');
            return;
        }
        $.ajax({
            url: amp_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'amp_save_ab_test',
                nonce: amp_ajax.nonce,
                name: name,
                traffic_split: isNaN(split) ? 50 : Math.max(0, Math.min(100, split)),
                status: status,
                ad_a_id: adA,
                ad_b_id: adB,
                description: desc
            },
            success: function(resp) {
                if (resp.success && resp.data && resp.data.shortcode) {
                    $('#abtest-shortcode').text(resp.data.shortcode);
                    alert('<?php _e('Teste A/B salvo com sucesso!', 'adsense-master-pro'); ?>');
                    setTimeout(function(){ location.reload(); }, 800);
                } else {
                    alert('❌ ' + (resp.data || '<?php _e('Erro ao salvar Teste A/B.', 'adsense-master-pro'); ?>'));
                }
            },
            error: function() {
                alert('❌ <?php _e('Erro na comunicação com o servidor.', 'adsense-master-pro'); ?>');
            }
        });
    });
    
    $(document).on('click', '.delete-abtest', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (!confirm('<?php _e('Deseja excluir este Teste A/B?', 'adsense-master-pro'); ?>')) return;
        $.ajax({
            url: amp_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'amp_delete_ab_test',
                nonce: amp_ajax.nonce,
                id: id
            },
            success: function(resp) {
                if (resp.success) {
                    alert('<?php _e('Teste A/B excluído.', 'adsense-master-pro'); ?>');
                    location.reload();
                } else {
                    alert('❌ ' + (resp.data || '<?php _e('Erro ao excluir Teste A/B.', 'adsense-master-pro'); ?>'));
                }
            }
        });
    });
    
    $(document).on('click', '.copy-abtest-shortcode', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var text = '[amp_ab_test id="' + id + '"]';
        var ta = $('<textarea>').val(text).appendTo('body').select();
        document.execCommand('copy');
        ta.remove();
        alert('<?php _e('Shortcode copiado.', 'adsense-master-pro'); ?>');
    });
    
    $('#load-heatmap').on('click', function() {
        var adId = parseInt($('#heatmap-ad').val(), 10);
        if (!adId) {
            alert('<?php _e('Selecione um anúncio.', 'adsense-master-pro'); ?>');
            return;
        }
        $.ajax({
            url: amp_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'amp_get_heatmap',
                nonce: amp_ajax.nonce,
                ad_id: adId,
                limit: 5000
            },
            success: function(resp) {
                if (resp.success && resp.data && resp.data.points) {
                    var canvas = document.getElementById('heatmap-canvas');
                    var ctx = canvas.getContext('2d');
                    ctx.clearRect(0,0,canvas.width, canvas.height);
                    var pts = resp.data.points;
                    for (var i = 0; i < pts.length; i++) {
                        var px = Math.round(pts[i].x * canvas.width);
                        var py = Math.round(pts[i].y * canvas.height);
                        var grd = ctx.createRadialGradient(px, py, 0, px, py, 25);
                        grd.addColorStop(0, 'rgba(255,0,0,0.6)');
                        grd.addColorStop(1, 'rgba(255,0,0,0)');
                        ctx.fillStyle = grd;
                        ctx.beginPath();
                        ctx.arc(px, py, 25, 0, Math.PI*2);
                        ctx.fill();
                    }
                    if (!pts.length) {
                        alert('<?php _e('Sem dados de cliques para este anúncio.', 'adsense-master-pro'); ?>');
                    }
                } else {
                    alert('❌ ' + (resp.data || '<?php _e('Erro ao carregar heatmap.', 'adsense-master-pro'); ?>'));
                }
            }
        });
    });
    
    $('#generate-report').on('click', function() {
        var start = $('#report-start').val();
        var end = $('#report-end').val();
        var adId = parseInt($('#report-ad').val(), 10) || '';
        var device = $('#report-device').val();
        $.ajax({
            url: amp_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'amp_get_analytics',
                nonce: amp_ajax.nonce,
                start_date: start,
                end_date: end,
                ad_id: adId,
                device: device
            },
            success: function(resp) {
                if (resp.success && resp.data && resp.data.rows) {
                    var rows = resp.data.rows;
                    var $tbody = $('#report-table tbody');
                    $tbody.empty();
                    var totalImpr = 0, totalClicks = 0;
                    for (var i = 0; i < rows.length; i++) {
                        var r = rows[i];
                        var impr = parseInt(r.impressions, 10) || 0;
                        var clk = parseInt(r.clicks, 10) || 0;
                        var ctr = impr ? ((clk / impr) * 100).toFixed(2) + '%' : '0%';
                        totalImpr += impr;
                        totalClicks += clk;
                        var tr = $('<tr>');
                        tr.append($('<td>').text(r.day));
                        tr.append($('<td>').text(impr));
                        tr.append($('<td>').text(clk));
                        tr.append($('<td>').text(ctr));
                        $tbody.append(tr);
                    }
                    var totalCtr = totalImpr ? ((totalClicks / totalImpr) * 100).toFixed(2) + '%' : '0%';
                    $('#report-total-impr').text(totalImpr);
                    $('#report-total-clicks').text(totalClicks);
                    $('#report-total-ctr').text(totalCtr);
                    
                    var canvas = document.getElementById('report-chart');
                    var ctx = canvas.getContext('2d');
                    ctx.clearRect(0,0,canvas.width,canvas.height);
                    var maxVal = 0;
                    var imprData = rows.map(function(r){ return parseInt(r.impressions,10)||0; });
                    var clickData = rows.map(function(r){ return parseInt(r.clicks,10)||0; });
                    for (var j=0;j<imprData.length;j++) { maxVal = Math.max(maxVal, imprData[j], clickData[j]); }
                    maxVal = maxVal || 1;
                    var w = canvas.width, h = canvas.height;
                    var n = rows.length || 1;
                    function yScale(v){ return h - Math.round((v / maxVal) * (h - 20)); }
                    function xPos(i){ return Math.round((i / Math.max(1,n-1)) * (w - 20)) + 10; }
                    ctx.strokeStyle = '#0073aa';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    for (var i1=0;i1<n;i1++) {
                        var x = xPos(i1), y = yScale(imprData[i1]);
                        if (i1===0) ctx.moveTo(x,y); else ctx.lineTo(x,y);
                    }
                    ctx.stroke();
                    ctx.strokeStyle = '#d54e21';
                    ctx.beginPath();
                    for (var i2=0;i2<n;i2++) {
                        var x2 = xPos(i2), y2 = yScale(clickData[i2]);
                        if (i2===0) ctx.moveTo(x2,y2); else ctx.lineTo(x2,y2);
                    }
                    ctx.stroke();
                } else {
                    alert('❌ ' + (resp.data || '<?php _e('Erro ao gerar relatório.', 'adsense-master-pro'); ?>'));
                }
            }
        });
    });
});
</script>
