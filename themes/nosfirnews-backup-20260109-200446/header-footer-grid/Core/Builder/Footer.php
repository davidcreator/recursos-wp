<?php
namespace NosfirNews\HeaderFooterGrid\Core\Builder;
class Footer extends Abstract_Builder {
    public function output() {
        echo '<footer id="colophon" class="site-footer" role="contentinfo"><div class="container">';
        $this->render();
        echo '</div></footer>';
    }
}