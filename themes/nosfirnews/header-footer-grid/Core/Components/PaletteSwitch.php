<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class PaletteSwitch extends Abstract_Component {
    public function render() { echo '<button class="nn-btn" id="palette-switch">'.esc_html__('Tema','nosfirnews').'</button>'; }
}