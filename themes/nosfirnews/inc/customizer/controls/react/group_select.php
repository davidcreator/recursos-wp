<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NosfirNews_Customizer_Group_Select extends WP_Customize_Control { public $type = 'nosfirnews_group_select'; public function render_content() { echo '<select><option>Opção</option></select>'; } }
