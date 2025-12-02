<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Font_Family extends WP_Customize_Control { public $type = 'nosfirnews_font_family'; public function render_content() { echo '<select><option>System</option><option>Inter</option><option>Roboto</option></select>'; } }
