<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components\Utility;
use NosfirNews\HeaderFooterGrid\Core\Components\Abstract_Component;
class SearchIconButton extends Abstract_Component {
    public function render() {
        echo '<button class="search-toggle" aria-expanded="false" aria-controls="search">'.esc_html__( 'Buscar', 'nosfirnews' ).'</button>';
    }
}