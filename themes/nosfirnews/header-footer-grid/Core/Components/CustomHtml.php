<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class CustomHtml extends Abstract_Component {
    public function render() { echo isset($this->args['html']) ? $this->args['html'] : ''; }
}