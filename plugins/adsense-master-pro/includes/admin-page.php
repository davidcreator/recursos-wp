<?php
// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Buscar anúncios do banco de dados
global $wpdb;
$table_name = $wpdb->prefix . 'amp_ads';
$ads = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);
?>
<div class="wrap">
    <h1><?php _e('AdSense Master Pro - Gerenciar Anúncios', 'adsense-master-pro'); ?></h1>
    
    <div class="amp-admin-container">
        <div class="amp-header-actions">
            <button class="button button-primary" id="add-new-ad">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php _e('Adicionar Novo Anúncio', 'adsense-master-pro'); ?>
            </button>
            <button class="button" id="import-ads">
                <span class="dashicons dashicons-upload"></span>
                <?php _e('Importar Anúncios', 'adsense-master-pro'); ?>
            </button>
            <button class="button" id="export-ads">
                <span class="dashicons dashicons-download"></span>
                <?php _e('Exportar Anúncios', 'adsense-master-pro'); ?>
            </button>
        </div>

        <div class="amp-stats-overview">
            <div class="amp-stat-box">
                <h3><?php echo count($ads); ?></h3>
                <p><?php _e('Total de Anúncios', 'adsense-master-pro'); ?></p>
            </div>
            <div class="amp-stat-box">
                <h3><?php echo count(array_filter($ads, function($ad) { return $ad['status'] === 'active'; })); ?></h3>
                <p><?php _e('Anúncios Ativos', 'adsense-master-pro'); ?></p>
            </div>
            <div class="amp-stat-box">
                <h3>0</h3>
                <p><?php _e('Impressões Hoje', 'adsense-master-pro'); ?></p>
            </div>
            <div class="amp-stat-box">
                <h3>0</h3>
                <p><?php _e('Cliques Hoje', 'adsense-master-pro'); ?></p>
            </div>
        </div>
        
        <div class="amp-ads-list">
            <h2><?php _e('Lista de Anúncios', 'adsense-master-pro'); ?></h2>
            
            <?php if (empty($ads)): ?>
                <div class="amp-empty-state">
                    <div class="amp-empty-icon">
                        <span class="dashicons dashicons-admin-media"></span>
                    </div>
                    <h3><?php _e('Nenhum anúncio encontrado', 'adsense-master-pro'); ?></h3>
                    <p><?php _e('Comece criando seu primeiro anúncio para começar a monetizar seu site.', 'adsense-master-pro'); ?></p>
                    <button class="button button-primary" id="create-first-ad">
                        <?php _e('Criar Primeiro Anúncio', 'adsense-master-pro'); ?>
                    </button>
                </div>
            <?php else: ?>
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php _e('ID', 'adsense-master-pro'); ?></th>
                            <th><?php _e('Nome', 'adsense-master-pro'); ?></th>
                            <th><?php _e('Posição', 'adsense-master-pro'); ?></th>
                            <th><?php _e('Tipo', 'adsense-master-pro'); ?></th>
                            <th><?php _e('Status', 'adsense-master-pro'); ?></th>
                            <th><?php _e('Criado em', 'adsense-master-pro'); ?></th>
                            <th style="width: 150px;"><?php _e('Ações', 'adsense-master-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ads as $ad): 
                            $options = unserialize($ad['options']);
                            $ad_type = $this->detect_ad_type($ad['code']);
                        ?>
                        <tr data-ad-id="<?php echo $ad['id']; ?>">
                            <td><strong>#<?php echo $ad['id']; ?></strong></td>
                            <td>
                                <strong><?php echo esc_html($ad['name']); ?></strong>
                                <div class="row-actions">
                                    <span class="edit">
                                        <a href="#" class="edit-ad" data-id="<?php echo $ad['id']; ?>">
                                            <?php _e('Editar', 'adsense-master-pro'); ?>
                                        </a> |
                                    </span>
                                    <span class="duplicate">
                                        <a href="#" class="duplicate-ad" data-id="<?php echo $ad['id']; ?>">
                                            <?php _e('Duplicar', 'adsense-master-pro'); ?>
                                        </a> |
                                    </span>
                                    <span class="trash">
                                        <a href="#" class="delete-ad" data-id="<?php echo $ad['id']; ?>" style="color: #a00;">
                                            <?php _e('Excluir', 'adsense-master-pro'); ?>
                                        </a>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="amp-position-badge amp-position-<?php echo esc_attr($ad['position']); ?>">
                                    <?php echo $this->get_position_label($ad['position']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="amp-type-badge amp-type-<?php echo esc_attr($ad_type); ?>">
                                    <?php echo $this->get_ad_type_label($ad_type); ?>
                                </span>
                            </td>
                            <td>
                                <label class="amp-toggle">
                                    <input type="checkbox" class="toggle-ad-status" data-id="<?php echo $ad['id']; ?>" <?php checked($ad['status'], 'active'); ?>>
                                    <span class="amp-toggle-slider"></span>
                                </label>
                            </td>
                            <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($ad['created_at'])); ?></td>
                            <td>
                                <div class="amp-action-buttons">
                                    <button class="button button-small preview-ad" data-id="<?php echo $ad['id']; ?>" title="<?php _e('Visualizar', 'adsense-master-pro'); ?>">
                                        <span class="dashicons dashicons-visibility"></span>
                                    </button>
                                    <button class="button button-small edit-ad" data-id="<?php echo $ad['id']; ?>" title="<?php _e('Editar', 'adsense-master-pro'); ?>">
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                    <button class="button button-small copy-shortcode" data-shortcode="[amp_ad id=&quot;<?php echo $ad['id']; ?>&quot;]" title="<?php _e('Copiar Shortcode', 'adsense-master-pro'); ?>">
                                        <span class="dashicons dashicons-admin-page"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para adicionar/editar anúncio -->
<div id="amp-ad-modal" class="amp-modal" style="display: none;">
    <div class="amp-modal-content">
        <div class="amp-modal-header">
            <h2 id="amp-modal-title"><?php _e('Adicionar Novo Anúncio', 'adsense-master-pro'); ?></h2>
            <button class="amp-modal-close">&times;</button>
        </div>
        
        <form id="amp-ad-form">
            <div class="amp-modal-body">
                <div class="amp-form-row">
                    <div class="amp-form-group">
                        <label for="ad-name"><?php _e('Nome do Anúncio', 'adsense-master-pro'); ?> *</label>
                        <input type="text" id="ad-name" name="name" required>
                        <small><?php _e('Nome identificador para este anúncio', 'adsense-master-pro'); ?></small>
                    </div>
                </div>
                
                <div class="amp-form-row">
                    <div class="amp-form-group">
                        <label for="ad-position"><?php _e('Posição do Anúncio', 'adsense-master-pro'); ?> *</label>
                        <select id="ad-position" name="position" required>
                            <option value=""><?php _e('Selecione uma posição', 'adsense-master-pro'); ?></option>
                            <option value="header"><?php _e('Cabeçalho', 'adsense-master-pro'); ?></option>
                            <option value="footer"><?php _e('Rodapé', 'adsense-master-pro'); ?></option>
                            <option value="before_content"><?php _e('Antes do Conteúdo', 'adsense-master-pro'); ?></option>
                            <option value="after_content"><?php _e('Depois do Conteúdo', 'adsense-master-pro'); ?></option>
                            <option value="before_paragraph"><?php _e('Antes do 1º Parágrafo', 'adsense-master-pro'); ?></option>
                            <option value="after_paragraph"><?php _e('Depois do 2º Parágrafo', 'adsense-master-pro'); ?></option>
                            <option value="sidebar"><?php _e('Sidebar (via widget)', 'adsense-master-pro'); ?></option>
                            <option value="custom"><?php _e('Posição Personalizada', 'adsense-master-pro'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="amp-form-row">
                    <div class="amp-form-group">
                        <label for="ad-code"><?php _e('Código do Anúncio', 'adsense-master-pro'); ?> *</label>
                        <textarea id="ad-code" name="code" rows="8" required placeholder="<?php _e('Cole aqui o código do seu anúncio (AdSense, HTML, JavaScript, etc.)', 'adsense-master-pro'); ?>"></textarea>
                        <div class="amp-code-templates">
                            <button type="button" class="button" data-template="adsense"><?php _e('Template AdSense', 'adsense-master-pro'); ?></button>
                            <button type="button" class="button" data-template="banner"><?php _e('Template Banner', 'adsense-master-pro'); ?></button>
                            <button type="button" class="button" data-template="responsive"><?php _e('Template Responsivo', 'adsense-master-pro'); ?></button>
                        </div>
                    </div>
                </div>
                
                <div class="amp-form-row amp-form-columns">
                    <div class="amp-form-group">
                        <label for="ad-alignment"><?php _e('Alinhamento', 'adsense-master-pro'); ?></label>
                        <select id="ad-alignment" name="alignment">
                            <option value="left"><?php _e('Esquerda', 'adsense-master-pro'); ?></option>
                            <option value="center" selected><?php _e('Centro', 'adsense-master-pro'); ?></option>
                            <option value="right"><?php _e('Direita', 'adsense-master-pro'); ?></option>
                        </select>
                    </div>
                    
                    <div class="amp-form-group">
                        <label for="css-selector"><?php _e('Seletor CSS (Opcional)', 'adsense-master-pro'); ?></label>
                        <input type="text" id="css-selector" name="css_selector" placeholder=".my-custom-class">
                        <small><?php _e('Para posicionamento personalizado', 'adsense-master-pro'); ?></small>
                    </div>
                </div>
                
                <div class="amp-form-section">
                    <h3><?php _e('Configurações de Exibição', 'adsense-master-pro'); ?></h3>
                    
                    <div class="amp-form-row amp-form-columns">
                        <div class="amp-form-group">
                            <h4><?php _e('Dispositivos', 'adsense-master-pro'); ?></h4>
                            <label class="amp-checkbox">
                                <input type="checkbox" name="show_on_desktop" value="1" checked>
                                <?php _e('Exibir no Desktop', 'adsense-master-pro'); ?>
                            </label>
                            <label class="amp-checkbox">
                                <input type="checkbox" name="show_on_mobile" value="1" checked>
                                <?php _e('Exibir no Mobile', 'adsense-master-pro'); ?>
                            </label>
                        </div>
                        
                        <div class="amp-form-group">
                            <h4><?php _e('Páginas', 'adsense-master-pro'); ?></h4>
                            <label class="amp-checkbox">
                                <input type="checkbox" name="show_on_homepage" value="1" checked>
                                <?php _e('Página Inicial', 'adsense-master-pro'); ?>
                            </label>
                            <label class="amp-checkbox">
                                <input type="checkbox" name="show_on_posts" value="1" checked>
                                <?php _e('Posts', 'adsense-master-pro'); ?>
                            </label>
                            <label class="amp-checkbox">
                                <input type="checkbox" name="show_on_pages" value="1" checked>
                                <?php _e('Páginas', 'adsense-master-pro'); ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="amp-modal-footer">
                <button type="button" class="button" id="amp-cancel-ad"><?php _e('Cancelar', 'adsense-master-pro'); ?></button>
                <button type="submit" class="button button-primary" id="amp-save-ad"><?php _e('Salvar Anúncio', 'adsense-master-pro'); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de preview -->
<div id="amp-preview-modal" class="amp-modal" style="display: none;">
    <div class="amp-modal-content">
        <div class="amp-modal-header">
            <h2><?php _e('Visualizar Anúncio', 'adsense-master-pro'); ?></h2>
            <button class="amp-modal-close">&times;</button>
        </div>
        <div class="amp-modal-body">
            <div id="amp-preview-content"></div>
        </div>
    </div>
</div>

<style>
.amp-admin-container { max-width: 1200px; }
.amp-header-actions { margin: 20px 0; }
.amp-header-actions .button { margin-right: 10px; }
.amp-stats-overview { display: flex; gap: 20px; margin: 20px 0; }
.amp-stat-box { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; text-align: center; flex: 1; }
.amp-stat-box h3 { font-size: 2em; margin: 0; color: #0073aa; }
.amp-stat-box p { margin: 5px 0 0; color: #666; }
.amp-empty-state { text-align: center; padding: 60px 20px; background: #fff; border: 1px solid #ddd; border-radius: 4px; }
.amp-empty-icon { font-size: 4em; color: #ddd; margin-bottom: 20px; }
.amp-position-badge, .amp-type-badge { padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
.amp-position-badge { background: #e1f5fe; color: #0277bd; }
.amp-type-badge { background: #f3e5f5; color: #7b1fa2; }
.amp-toggle { position: relative; display: inline-block; width: 50px; height: 24px; }
.amp-toggle input { opacity: 0; width: 0; height: 0; }
.amp-toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
.amp-toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
.amp-toggle input:checked + .amp-toggle-slider { background-color: #0073aa; }
.amp-toggle input:checked + .amp-toggle-slider:before { transform: translateX(26px); }
.amp-action-buttons { display: flex; gap: 5px; }
.amp-modal { position: fixed; z-index: 100000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
.amp-modal-content { background-color: #fff; margin: 5% auto; width: 80%; max-width: 800px; border-radius: 4px; max-height: 90vh; overflow-y: auto; }
.amp-modal-header { padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
.amp-modal-close { background: none; border: none; font-size: 24px; cursor: pointer; }
.amp-modal-body { padding: 20px; }
.amp-modal-footer { padding: 20px; border-top: 1px solid #ddd; text-align: right; }
.amp-form-row { margin-bottom: 20px; }
.amp-form-columns { display: flex; gap: 20px; }
.amp-form-group { flex: 1; }
.amp-form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
.amp-form-group input, .amp-form-group select, .amp-form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
.amp-form-group small { color: #666; font-style: italic; }
.amp-form-section { border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px; }
.amp-checkbox { display: block; margin-bottom: 10px; }
.amp-checkbox input { width: auto; margin-right: 8px; }
.amp-code-templates { margin-top: 10px; }
.amp-code-templates .button { margin-right: 10px; }
</style>

<?php
// Funções auxiliares para a página
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

if (!function_exists('get_ad_type_label')) {
    function get_ad_type_label($type) {
        $labels = array(
            'adsense' => __('AdSense', 'adsense-master-pro'),
            'banner' => __('Banner', 'adsense-master-pro'),
            'script' => __('Script', 'adsense-master-pro'),
            'html' => __('HTML', 'adsense-master-pro')
        );
        return isset($labels[$type]) ? $labels[$type] : $type;
    }
}
?>