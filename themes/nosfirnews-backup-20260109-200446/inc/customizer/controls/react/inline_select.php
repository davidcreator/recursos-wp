<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Inline_Select extends WP_Customize_Control { public $type = 'nosfirnews_inline_select'; public function render_content() { echo '<select><option>1</option><option>2</option></select>'; } }
