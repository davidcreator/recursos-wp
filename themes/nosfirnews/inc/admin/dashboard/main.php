<?php
function nosfirnews_admin_menu(){
    add_theme_page('NosfirNews','NosfirNews','manage_options','nosfirnews-admin','nosfirnews_admin_page');
}
add_action('admin_menu','nosfirnews_admin_menu');
function nosfirnews_admin_page(){
    $changelog = function_exists('nosfirnews_get_changelog') ? nosfirnews_get_changelog() : [];
    echo '<div class="wrap"><h1>NosfirNews</h1><h2>Changelog</h2><pre>'.esc_html(json_encode($changelog)).'</pre></div>';
}