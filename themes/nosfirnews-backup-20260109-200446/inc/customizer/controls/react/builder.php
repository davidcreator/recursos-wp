<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Builder extends WP_Customize_Control { public $type = 'nosfirnews_builder'; public function render_content() { echo '<div class="nn-control"></div>'; } }
