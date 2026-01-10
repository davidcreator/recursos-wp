<?php
namespace NosfirNews\HeaderFooterGrid\Trails;
class Core {
    public static function render( $template, $args = [] ) {
        $file = get_template_directory() . '/header-footer-grid/templates/' . $template . '.php';
        if ( file_exists( $file ) ) { if ( is_array( $args ) ) extract( $args, EXTR_SKIP ); include $file; }
    }
}