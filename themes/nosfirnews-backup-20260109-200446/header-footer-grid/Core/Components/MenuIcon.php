<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class MenuIcon extends Abstract_Component {
    public function render() { echo '<button class="menu-toggle" aria-expanded="false" aria-controls="primary-menu"><span></span><span></span><span></span></button>'; }
}