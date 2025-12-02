<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Color extends WP_Customize_Control { public $type = 'nosfirnews_color'; public function render_content() { echo '<input type="color" />'; } }
