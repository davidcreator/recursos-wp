<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
abstract class Abstract_Component {
    protected $args = [];
    public function __construct( array $args = [] ) { $this->args = $args; }
    abstract public function render();
}