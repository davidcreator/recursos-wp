<?php
namespace NosfirNews\HeaderFooterGrid\Core;
class Css_Generator {
    private $rules = [];
    public function add( $selector, array $declarations ) { $this->rules[ $selector ][] = $declarations; }
    public function render() {
        $css = '';
        foreach ( $this->rules as $selector => $blocks ) {
            $css .= $selector . '{';
            foreach ( $blocks as $declarations ) {
                foreach ( $declarations as $prop => $val ) { $css .= $prop . ':' . $val . ';'; }
            }
            $css .= '}';
        }
        return $css;
    }
    public function enqueue( $handle = 'nosfirnews-style' ) { $css = $this->render(); if ( $css ) wp_add_inline_style( $handle, $css ); }
}