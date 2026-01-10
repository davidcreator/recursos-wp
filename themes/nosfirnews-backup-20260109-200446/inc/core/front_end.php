<?php
function nosfirnews_core_enqueue_front_end(){
    $path= get_template_directory().'/header-footer-grid/assets/js/theme.js';
    if(file_exists($path)){
        wp_enqueue_script('nosfirnews-hfg-theme', get_template_directory_uri().'/header-footer-grid/assets/js/theme.js', [], filemtime($path), true);
    }
}
add_action('wp_enqueue_scripts','nosfirnews_core_enqueue_front_end');
