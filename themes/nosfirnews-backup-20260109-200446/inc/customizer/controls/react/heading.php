<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Heading extends WP_Customize_Control { public $type = 'nosfirnews_heading'; public function render_content() { echo '<h3>Título</h3>'; } }
