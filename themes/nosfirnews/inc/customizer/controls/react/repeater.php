<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Repeater extends WP_Customize_Control { public $type = 'nosfirnews_repeater'; public function render_content() { echo '<div class="nn-control"></div>'; } }
