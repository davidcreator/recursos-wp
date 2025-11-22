<?php
namespace NosfirNews\HeaderFooterGrid\Core\Customizer;
class SpacingControl extends \WP_Customize_Control {
    public $type = 'nosfirnews_responsive_spacing';
    public function enqueue() {
        $css_path = get_template_directory() . '/header-footer-grid/Core/Customizer/responsive-spacing.css';
        $js_path  = get_template_directory() . '/header-footer-grid/Core/Customizer/responsive-spacing.js';
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style( 'nosfirnews-responsive-spacing', get_template_directory_uri() . '/header-footer-grid/Core/Customizer/responsive-spacing.css', [], filemtime( $css_path ) );
        }
        if ( file_exists( $js_path ) ) {
            wp_enqueue_script( 'nosfirnews-responsive-spacing', get_template_directory_uri() . '/header-footer-grid/Core/Customizer/responsive-spacing.js', [ 'customize-controls' ], filemtime( $js_path ), true );
        }
    }
    public function render_content() {
        echo '<label><span class="customize-control-title">' . esc_html( $this->label ) . '</span></label>';
        echo '<div class="nn-spacing-control"><input type="number" class="nn-spacing-input" value="' . esc_attr( $this->value() ) . '" ';
        $this->link();
        echo ' /></div>';
    }
}