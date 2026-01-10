<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Textarea extends WP_Customize_Control { public $type = 'nosfirnews_textarea'; public function render_content() { echo '<textarea rows="4" cols="40"></textarea>'; } }
