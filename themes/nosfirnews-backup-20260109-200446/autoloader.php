<?php
spl_autoload_register(function($class){
    if (strpos($class,'NosfirNews\\')!==0) return;
    $relative = str_replace('NosfirNews\\','',$class);
    $path = get_template_directory().'/inc/'.str_replace('\\','/',$relative).'.php';
    if (file_exists($path)) require $path;
});