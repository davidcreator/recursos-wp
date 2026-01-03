<?php
/**
 * AdSense Master Pro v3.0
 * AJAX Hooks para Adicionar, Importar e Exportar Anúncios
 * 
 * Adicione isto ao método init_hooks() da classe AdSenseMasterPro
 */

// Adicionar estes hooks dentro de init_hooks():
add_action('wp_ajax_amp_save_ad', array($this, 'save_ad_enhanced'));
add_action('wp_ajax_amp_import_ad', array($this, 'import_ad'));
add_action('wp_ajax_amp_export_ads', array($this, 'export_ads'));

// E enfileirar o novo script na função frontend_scripts():
wp_enqueue_script(
    'amp-admin-actions',
    AMP_PLUGIN_URL . 'assets/js/admin-actions.js',
    array('jquery'),
    AMP_VERSION,
    true
);