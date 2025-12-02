<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Form_Token_Field extends WP_Customize_Control { public $type = 'nosfirnews_form_token_field'; public function render_content() { echo '<input type="text" />'; } }
