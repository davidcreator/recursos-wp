<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Rich_Text extends WP_Customize_Control { public $type = 'nosfirnews_rich_text'; public function render_content() { echo '<textarea></textarea>'; } }
