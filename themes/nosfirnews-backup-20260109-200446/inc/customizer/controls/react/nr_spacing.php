<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Nr_Spacing extends WP_Customize_Control { public $type = 'nosfirnews_nr_spacing'; public function render_content() { echo '<input type="number" class="nn-spacing-input" />'; } }
