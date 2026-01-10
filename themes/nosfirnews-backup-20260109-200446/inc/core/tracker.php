<?php
function nosfirnews_core_tracker(){
    $info= nosfirnews_core_theme_info();
    echo '<meta name="nosfirnews" content="'.esc_attr($info['name'].' '.$info['version']).'">';
}
add_action('wp_head','nosfirnews_core_tracker');
