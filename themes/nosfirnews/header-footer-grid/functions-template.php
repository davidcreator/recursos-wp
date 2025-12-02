<?php
namespace NosfirNews\HeaderFooterGrid;
function render( $template, $args = [] ) { $f = get_template_directory() . '/header-footer-grid/templates/' . $template . '.php'; if ( file_exists( $f ) ) { if ( is_array( $args ) ) extract( $args, EXTR_SKIP ); include $f; } }
