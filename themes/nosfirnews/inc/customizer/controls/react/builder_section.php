<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Builder_Section extends WP_Customize_Control { public $type = 'nosfirnews_builder_section'; public function render_content() { echo '<div class="nn-control"></div>'; } }
