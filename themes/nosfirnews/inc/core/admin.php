<?php
function nosfirnews_core_admin_boot(){
    add_theme_page('NosfirNews Core','NosfirNews Core','manage_options','nosfirnews-core','nosfirnews_core_admin_page');
}
add_action('admin_menu','nosfirnews_core_admin_boot');
function nosfirnews_core_admin_page(){
    echo '<div class="wrap"><h1>NosfirNews Core</h1></div>';
}
