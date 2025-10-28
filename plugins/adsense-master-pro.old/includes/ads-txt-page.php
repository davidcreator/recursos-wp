<?php
// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Caminho para o arquivo ads.txt
$ads_txt_path = ABSPATH . 'ads.txt';
$ads_txt_content = '';
$is_writable = is_writable(ABSPATH);
$ads_txt_url = home_url('/ads.txt');

// Ler conteúdo existente do ads.txt
if (file_exists($ads_txt_path)) {
    $ads_txt_content = file_get_contents($ads_txt_path);
}

// Processar envio do formulário
if (isset($_POST['submit']) && wp_verify_nonce($_POST['ads_txt_nonce'], 'save_ads_txt')) {
    $new_content = sanitize_textarea_field($_POST['ads_txt_content']);
    
    if ($is_writable) {
        $result = file_put_contents($ads_txt_path, $new_content);
        if ($result !== false) {
            echo '<div class="notice notice-success"><p>' . __('Arquivo ads.txt salvo com sucesso!', 'adsense-master-pro') . '</p></div>';
            $ads_txt_content = $new_content;
        } else {
            echo '<div class="notice notice-error"><p>' . __('Erro ao salvar o arquivo ads.txt.', 'adsense-master-pro') . '</p></div>';
        }
    } else {
        echo '<div class="notice notice-error"><p>' . __('Não foi possível escrever no diretório raiz do WordPress.', 'adsense-master-pro') . '</p></div>';
    }
}

// Processar ação de adicionar linha AdSense
if (isset($_POST['add_adsense']) && wp_verify_nonce($_POST['ads_txt_nonce'], 'save_ads_txt')) {
    $adsense_id = sanitize_text_field($_POST['adsense_publisher_id']);
    if (!empty($adsense_id)) {
        $adsense_line = "google.com, {$adsense_id}, DIRECT, f08c47fec0942fa0\n";
        $ads_txt_content = $adsense_line . $ads_txt_content;
        
        if ($is_writable) {
            file_put_contents($ads_txt_path, $ads_txt_content);
            echo '<div class="notice notice-success"><p>' . __('Linha do AdSense adicionada com sucesso!', 'adsense-master-pro') . '</p></div>';
        }
    }
}

// Verificar se o arquivo é acessível via web
$ads_txt_accessible = false;
$response = wp_remote_get($ads_txt_url);
if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
    $ads_txt_accessible = true;
}

// Templates de ads.txt comuns
$templates = array(
    'adsense' => array(
        'name' => 'Google AdSense',
        'content' => "google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0"
    ),
    'media_net' => array(
        'name' => 'Media.net',
        'content' => "media.net, 8CU123456, DIRECT\ncontextlogic.com, 123456, RESELLER\nyahoo.com, 123456, RESELLER"
    ),
    'amazon' => array(
        'name' => 'Amazon Associates',
        'content' => "amazon-adsystem.com, 3456, DIRECT"
    ),
    'propeller' => array(
        'name' => 'PropellerAds',
        'content' => "propellerads.com, 123456, DIRECT"
    )
);
?>

<div class="wrap">
    <h1><?php _e('AdSense Master Pro - Gerenciar ads.txt', 'adsense-master-pro'); ?></h1>
    
    <div class="amp-ads-txt-container">
        <!-- Status do arquivo ads.txt -->
        <div class="amp-status-section">
            <h2><?php _e('Status do Arquivo ads.txt', 'adsense-master-pro'); ?></h2>
            
            <div class="amp-status-grid">
                <div class="amp-status-card <?php echo file_exists($ads_txt_path) ? 'status-success' : 'status-warning'; ?>">
                    <div class="amp-status-icon">
                        <span class="dashicons <?php echo file_exists($ads_txt_path) ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
                    </div>
                    <div class="amp-status-info">
                        <h3><?php _e('Arquivo Existe', 'adsense-master-pro'); ?></h3>
                        <p><?php echo file_exists($ads_txt_path) ? __('Arquivo ads.txt encontrado', 'adsense-master-pro') : __('Arquivo ads.txt não encontrado', 'adsense-master-pro'); ?></p>
                    </div>
                </div>
                
                <div class="amp-status-card <?php echo $is_writable ? 'status-success' : 'status-error'; ?>">
                    <div class="amp-status-icon">
                        <span class="dashicons <?php echo $is_writable ? 'dashicons-yes-alt' : 'dashicons-no-alt'; ?>"></span>
                    </div>
                    <div class="amp-status-info">
                        <h3><?php _e('Permissões de Escrita', 'adsense-master-pro'); ?></h3>
                        <p><?php echo $is_writable ? __('Diretório gravável', 'adsense-master-pro') : __('Sem permissão de escrita', 'adsense-master-pro'); ?></p>
                    </div>
                </div>
                
                <div class="amp-status-card <?php echo $ads_txt_accessible ? 'status-success' : 'status-warning'; ?>">
                    <div class="amp-status-icon">
                        <span class="dashicons <?php echo $ads_txt_accessible ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
                    </div>
                    <div class="amp-status-info">
                        <h3><?php _e('Acessibilidade Web', 'adsense-master-pro'); ?></h3>
                        <p><?php echo $ads_txt_accessible ? __('Arquivo acessível via web', 'adsense-master-pro') : __('Arquivo não acessível', 'adsense-master-pro'); ?></p>
                    </div>
                </div>
                
                <div class="amp-status-card">
                    <div class="amp-status-icon">
                        <span class="dashicons dashicons-admin-links"></span>
                    </div>
                    <div class="amp-status-info">
                        <h3><?php _e('URL do Arquivo', 'adsense-master-pro'); ?></h3>
                        <p><a href="<?php echo esc_url($ads_txt_url); ?>" target="_blank"><?php echo esc_url($ads_txt_url); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Assistente rápido para AdSense -->
        <div class="amp-quick-setup">
            <h2><?php _e('Configuração Rápida do AdSense', 'adsense-master-pro'); ?></h2>
            <p><?php _e('Adicione rapidamente sua linha do Google AdSense ao arquivo ads.txt:', 'adsense-master-pro'); ?></p>
            
            <form method="post" action="" class="amp-quick-form">
                <?php wp_nonce_field('save_ads_txt', 'ads_txt_nonce'); ?>
                <div class="amp-form-row">
                    <label for="adsense_publisher_id"><?php _e('ID do Editor AdSense:', 'adsense-master-pro'); ?></label>
                    <input type="text" id="adsense_publisher_id" name="adsense_publisher_id" placeholder="pub-0000000000000000" class="regular-text">
                    <button type="submit" name="add_adsense" class="button button-primary"><?php _e('Adicionar ao ads.txt', 'adsense-master-pro'); ?></button>
                </div>
                <small><?php _e('Exemplo: pub-1234567890123456 (encontre seu ID no painel do AdSense)', 'adsense-master-pro'); ?></small>
            </form>
        </div>
        
        <!-- Templates -->
        <div class="amp-templates-section">
            <h2><?php _e('Templates Prontos', 'adsense-master-pro'); ?></h2>
            <p><?php _e('Use um dos templates abaixo para começar rapidamente:', 'adsense-master-pro'); ?></p>
            
            <div class="amp-templates-grid">
                <?php foreach ($templates as $key => $template): ?>
                <div class="amp-template-card">
                    <h3><?php echo esc_html($template['name']); ?></h3>
                    <pre class="amp-template-code"><?php echo esc_html($template['content']); ?></pre>
                    <button type="button" class="button use-template" data-template="<?php echo esc_attr($template['content']); ?>">
                        <?php _e('Usar Template', 'adsense-master-pro'); ?>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Editor do arquivo ads.txt -->
        <div class="amp-editor-section">
            <h2><?php _e('Editor do Arquivo ads.txt', 'adsense-master-pro'); ?></h2>
            
            <?php if (!$is_writable): ?>
                <div class="notice notice-warning">
                    <p><?php _e('Aviso: Não é possível escrever no diretório raiz do WordPress. Você precisará criar/editar o arquivo ads.txt manualmente via FTP.', 'adsense-master-pro'); ?></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <?php wp_nonce_field('save_ads_txt', 'ads_txt_nonce'); ?>
                
                <div class="amp-editor-toolbar">
                    <button type="button" class="button" id="validate-ads-txt"><?php _e('Validar Sintaxe', 'adsense-master-pro'); ?></button>
                    <button type="button" class="button" id="clear-ads-txt"><?php _e('Limpar Tudo', 'adsense-master-pro'); ?></button>
                    <button type="button" class="button" id="download-ads-txt"><?php _e('Baixar Arquivo', 'adsense-master-pro'); ?></button>
                </div>
                
                <div class="amp-editor-container">
                    <textarea id="ads-txt-editor" name="ads_txt_content" rows="20" class="large-text code" <?php echo !$is_writable ? 'readonly' : ''; ?>><?php echo esc_textarea($ads_txt_content); ?></textarea>
                    
                    <div class="amp-editor-info">
                        <h4><?php _e('Formato do ads.txt:', 'adsense-master-pro'); ?></h4>
                        <p><?php _e('Cada linha deve seguir o formato:', 'adsense-master-pro'); ?></p>
                        <code>domain.com, publisher_id, DIRECT|RESELLER, [certification_authority_id]</code>
                        
                        <h4><?php _e('Exemplos:', 'adsense-master-pro'); ?></h4>
                        <ul>
                            <li><code>google.com, pub-1234567890123456, DIRECT, f08c47fec0942fa0</code></li>
                            <li><code>media.net, 8CU123456, DIRECT</code></li>
                            <li><code>amazon-adsystem.com, 3456, DIRECT</code></li>
                        </ul>
                    </div>
                </div>
                
                <?php if ($is_writable): ?>
                    <p class="submit">
                        <input type="submit" name="submit" class="button-primary" value="<?php _e('Salvar ads.txt', 'adsense-master-pro'); ?>">
                        <button type="button" class="button" id="preview-ads-txt"><?php _e('Visualizar', 'adsense-master-pro'); ?></button>
                    </p>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Validação e dicas -->
        <div class="amp-validation-section">
            <h2><?php _e('Validação e Dicas', 'adsense-master-pro'); ?></h2>
            
            <div class="amp-tips-grid">
                <div class="amp-tip-card">
                    <h3><?php _e('Verificação Online', 'adsense-master-pro'); ?></h3>
                    <p><?php _e('Verifique se seu ads.txt está funcionando corretamente:', 'adsense-master-pro'); ?></p>
                    <a href="https://www.google.com/webmasters/tools/ads-txt" target="_blank" class="button">
                        <?php _e('Verificar no Google', 'adsense-master-pro'); ?>
                    </a>
                </div>
                
                <div class="amp-tip-card">
                    <h3><?php _e('Documentação', 'adsense-master-pro'); ?></h3>
                    <p><?php _e('Saiba mais sobre o padrão ads.txt:', 'adsense-master-pro'); ?></p>
                    <a href="https://iabtechlab.com/ads-txt/" target="_blank" class="button">
                        <?php _e('Documentação IAB', 'adsense-master-pro'); ?>
                    </a>
                </div>
                
                <div class="amp-tip-card">
                    <h3><?php _e('Backup', 'adsense-master-pro'); ?></h3>
                    <p><?php _e('Sempre faça backup antes de fazer alterações:', 'adsense-master-pro'); ?></p>
                    <button type="button" class="button" id="backup-ads-txt">
                        <?php _e('Criar Backup', 'adsense-master-pro'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.amp-ads-txt-container { max-width: 1200px; }
.amp-status-section { margin-bottom: 30px; }
.amp-status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
.amp-status-card { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; }
.amp-status-card.status-success { border-left: 4px solid #46b450; }
.amp-status-card.status-warning { border-left: 4px solid #ffb900; }
.amp-status-card.status-error { border-left: 4px solid #dc3232; }
.amp-status-icon { margin-right: 15px; font-size: 24px; }
.amp-status-icon .dashicons-yes-alt { color: #46b450; }
.amp-status-icon .dashicons-warning { color: #ffb900; }
.amp-status-icon .dashicons-no-alt { color: #dc3232; }
.amp-status-info h3 { margin: 0 0 5px; font-size: 14px; }
.amp-status-info p { margin: 0; color: #666; }
.amp-quick-setup { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 30px; }
.amp-quick-form { margin-top: 15px; }
.amp-form-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.amp-form-row label { min-width: 150px; }
.amp-templates-section { margin-bottom: 30px; }
.amp-templates-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
.amp-template-card { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; }
.amp-template-card h3 { margin: 0 0 10px; }
.amp-template-code { background: #f5f5f5; padding: 10px; border-radius: 3px; font-size: 12px; margin: 10px 0; }
.amp-editor-section { margin-bottom: 30px; }
.amp-editor-toolbar { margin-bottom: 10px; }
.amp-editor-toolbar .button { margin-right: 10px; }
.amp-editor-container { display: flex; gap: 20px; }
.amp-editor-container textarea { flex: 2; }
.amp-editor-info { flex: 1; background: #f9f9f9; padding: 15px; border-radius: 4px; }
.amp-editor-info h4 { margin: 0 0 10px; }
.amp-editor-info ul { margin: 10px 0; padding-left: 20px; }
.amp-tips-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
.amp-tip-card { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; text-align: center; }
.amp-tip-card h3 { margin: 0 0 10px; }
.amp-tip-card p { margin: 0 0 15px; color: #666; }
</style>

<script>
jQuery(document).ready(function($) {
    // Usar template
    $('.use-template').click(function() {
        var template = $(this).data('template');
        var currentContent = $('#ads-txt-editor').val();
        var newContent = currentContent ? currentContent + '\n' + template : template;
        $('#ads-txt-editor').val(newContent);
    });
    
    // Validar sintaxe
    $('#validate-ads-txt').click(function() {
        var content = $('#ads-txt-editor').val();
        var lines = content.split('\n');
        var errors = [];
        
        lines.forEach(function(line, index) {
            line = line.trim();
            if (line && !line.startsWith('#')) {
                var parts = line.split(',');
                if (parts.length < 3) {
                    errors.push('Linha ' + (index + 1) + ': Formato inválido');
                }
            }
        });
        
        if (errors.length > 0) {
            alert('Erros encontrados:\n' + errors.join('\n'));
        } else {
            alert('<?php _e('Sintaxe válida!', 'adsense-master-pro'); ?>');
        }
    });
    
    // Limpar tudo
    $('#clear-ads-txt').click(function() {
        if (confirm('<?php _e('Tem certeza que deseja limpar todo o conteúdo?', 'adsense-master-pro'); ?>')) {
            $('#ads-txt-editor').val('');
        }
    });
    
    // Download do arquivo
    $('#download-ads-txt').click(function() {
        var content = $('#ads-txt-editor').val();
        var blob = new Blob([content], { type: 'text/plain' });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'ads.txt';
        a.click();
        window.URL.revokeObjectURL(url);
    });
    
    // Backup
    $('#backup-ads-txt').click(function() {
        var content = $('#ads-txt-editor').val();
        if (content) {
            var timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
            var blob = new Blob([content], { type: 'text/plain' });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'ads-txt-backup-' + timestamp + '.txt';
            a.click();
            window.URL.revokeObjectURL(url);
        } else {
            alert('<?php _e('Nenhum conteúdo para fazer backup.', 'adsense-master-pro'); ?>');
        }
    });
});
</script>