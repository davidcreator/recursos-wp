<?php
namespace NosfirNews\HeaderFooterGrid\Core;
class Css_Generator {
    private $rules = [];
    public function add( $selector, $props ) { $this->rules[ $selector ][] = $props; }
    public function render() { $css=''; foreach($this->rules as $s=>$blocks){ $css.=$s.'{' ; foreach($blocks as $d){ foreach($d as $p=>$v){ $css.=$p.':'.$v.';'; } } $css.='}'; } return $css; }
}
