<?php
function nosfirnews_core_theme_info(){
    $t= wp_get_theme();
    return ['name'=>$t->get('Name'),'version'=>$t->get('Version')];
}
