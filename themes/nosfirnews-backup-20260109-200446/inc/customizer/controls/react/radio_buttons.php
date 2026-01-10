<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Radio_Buttons extends WP_Customize_Control { public $type = 'nosfirnews_radio_buttons'; public function render_content() { echo '<label><input type="radio" name="nn" /> A</label><label><input type="radio" name="nn" /> B</label>'; } }
