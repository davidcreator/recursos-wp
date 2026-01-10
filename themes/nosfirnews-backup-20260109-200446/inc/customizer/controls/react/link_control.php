<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Link_Control extends WP_Customize_Control { public $type = 'nosfirnews_link_control'; public function render_content() { echo '<input type="url" />'; } }
