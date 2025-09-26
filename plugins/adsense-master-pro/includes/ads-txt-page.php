<?php
// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

$ads_txt_file = ABSPATH . 'ads.txt';
$ads_txt_content = '';
$file_writable = is_writable(ABSPATH);

if (file_exists($ads_txt_file)) {
    $ads_txt_content = file_get_contents($ads_txt_file);
    $file_writable = is_writable($ads_txt_file);
}

if (isset($_POST['submit']) && wp_verify_nonce($_POST['amp_ads_txt_nonce'], 'amp_ads_txt')) {
    if ($file_writable)