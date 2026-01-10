<?php
function nosfirnews_comp_amp_active() { return function_exists('amp_is_available') || class_exists('AMP'); }
