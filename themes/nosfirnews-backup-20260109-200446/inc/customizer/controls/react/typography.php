<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Typography extends WP_Customize_Control { public $type = 'nosfirnews_typography'; public function render_content() { echo '<div><label>Tamanho <input type="number" /></label> <label>Peso <select><option>400</option><option>500</option><option>600</option><option>700</option></select></label></div>'; } }
