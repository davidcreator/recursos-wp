<?php
function nosfirnews_core_dynamic_css(){
    $color= get_theme_mod('header_bg','#ffffff');
    $css= '.site-header{background-color:'.$color.';}';
    wp_add_inline_style('nosfirnews-style',$css);
}
add_action('wp_enqueue_scripts','nosfirnews_core_dynamic_css');
