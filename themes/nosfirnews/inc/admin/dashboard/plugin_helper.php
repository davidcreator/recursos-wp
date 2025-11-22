<?php
function nosfirnews_is_plugin_active($plugin){
    if(!function_exists('is_plugin_active')){include_once ABSPATH.'wp-admin/includes/plugin.php';}
    return is_plugin_active($plugin);
}