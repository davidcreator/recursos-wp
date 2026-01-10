<?php
function nosfirnews_dynamic_selector($selector,$props){ $css=$selector.'{'; foreach($props as $k=>$v){ $css.= $k.':'.$v.';'; } return $css.'}'; }
