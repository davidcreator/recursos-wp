<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Responsive_Range extends WP_Customize_Control { public $type = 'nosfirnews_responsive_range'; public function render_content() { echo '<input type="range" />'; } }
