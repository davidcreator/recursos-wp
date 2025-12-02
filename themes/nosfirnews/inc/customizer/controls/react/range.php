<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Range extends WP_Customize_Control { public $type = 'nosfirnews_range'; public function render_content() { echo '<input type="range" />'; } }
