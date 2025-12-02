<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Multiselect extends WP_Customize_Control { public $type = 'nosfirnews_multiselect'; public function render_content() { echo '<select multiple><option>A</option><option>B</option></select>'; } }
