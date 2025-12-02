<?php
function nosfirnews_css_vars($vars){ $out=':root{'; foreach((array)$vars as $k=>$v){ $out.='--'.$k.':'.$v.';'; } return $out.'}'; }
