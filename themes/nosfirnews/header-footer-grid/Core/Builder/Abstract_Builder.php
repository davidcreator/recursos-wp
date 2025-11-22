<?php
namespace NosfirNews\HeaderFooterGrid\Core\Builder;
class Abstract_Builder {
    protected $components = [];
    public function add( $component ) { $this->components[] = $component; return $this; }
    public function render() { foreach ( $this->components as $c ) { if ( is_object( $c ) && method_exists( $c, 'render' ) ) { $c->render(); } } }
}