<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Button_Appearance extends WP_Customize_Control { public $type = 'nosfirnews_button_appearance'; public function render_content() { echo '<button class="nn-btn">Botão</button>'; } }
