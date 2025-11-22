<?php
namespace NosfirNews\HeaderFooterGrid\Core\Builder;
class Header extends Abstract_Builder {
    public function output() {
        echo '<header class="site-header" role="banner"><div class="container">';
        $this->render();
        echo '</div></header>';
    }
}