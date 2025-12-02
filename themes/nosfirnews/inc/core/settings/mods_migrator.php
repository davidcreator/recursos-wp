<?php
function nosfirnews_mods_migrate(){
    $map= ['old_header_bg'=>'header_bg'];
    foreach($map as $o=>$n){ $v= get_theme_mod($o,null); if($v!==null) set_theme_mod($n,$v); }
}
