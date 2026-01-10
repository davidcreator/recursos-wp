<?php
function nosfirnews_settings_set($key,$value){ set_theme_mod($key,$value); }
function nosfirnews_settings_get($key,$default=null){ return get_theme_mod($key,$default); }
