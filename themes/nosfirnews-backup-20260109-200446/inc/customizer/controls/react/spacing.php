<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Spacing extends WP_Customize_Control { public $type = 'nosfirnews_spacing'; public function render_content() { echo '<input type="number" class="nn-spacing-input" />'; } }
