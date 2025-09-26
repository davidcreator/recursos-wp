<?php
// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php _e('AdSense Master Pro - Gerenciar Anúncios', 'adsense-master-pro'); ?></h1>
    
    <div class="amp-admin-container">
        <div class="amp-ads-list">
            <h2><?php _e('Anúncios Ativos', 'adsense-master-pro'); ?></h2>
            <button class="button button-primary" id="add-new-ad"><?php _e('Adicionar Novo Anúncio', 'adsense-master-pro'); ?></button>
            
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Nome', 'adsense-master-pro'); ?></th>
                        <th><?php _e('Posição', 'adsense-master-pro'); ?></th>
                        <th><?php _e('Status', 'adsense-master-pro'); ?></th>
                        <th><?php _e('Ações', 'adsense-master-pro'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->ads as $ad): ?>
                    <tr>
                        <td><?php echo esc_html($ad['name']); ?></t