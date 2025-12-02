<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Presets_Selector extends WP_Customize_Control { public $type = 'nosfirnews_presets_selector'; public function render_content() { echo '<select><option>Padrão</option></select>'; } }
