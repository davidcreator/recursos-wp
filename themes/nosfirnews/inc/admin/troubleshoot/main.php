<?php
function nosfirnews_troubleshoot_menu(){
    add_theme_page('NosfirNews Troubleshoot','NosfirNews Troubleshoot','manage_options','nosfirnews-troubleshoot','nosfirnews_troubleshoot_page');
}
add_action('admin_menu','nosfirnews_troubleshoot_menu');
function nosfirnews_troubleshoot_page(){
    echo '<div class="wrap"><h1>NosfirNews Troubleshoot</h1>';
    echo '<p>WP '.esc_html(get_bloginfo('version')).'</p>';
    echo '<p>PHP '.esc_html(phpversion()).'</p>';
    echo '</div>';
}