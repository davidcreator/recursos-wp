<?php
function nosfirnews_core_load(){
    $base= get_template_directory().'/inc/core';
    foreach(['front_end.php','dynamic_css.php','theme_info.php','supported_post_types.php'] as $f){ $p=$base.'/'.$f; if(file_exists($p)) require_once $p; }
}
add_action('after_setup_theme','nosfirnews_core_load');
