<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Responsive_Toggle extends WP_Customize_Control { public $type = 'nosfirnews_responsive_toggle'; public function render_content() { echo '<label><input type="checkbox" /> Toggle</label>'; } }
