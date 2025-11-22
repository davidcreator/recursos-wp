<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class Abstract_FooterWidget extends Abstract_Component {
    protected $index = 1;
    public function render() {
        $id = 'footer-' . $this->index;
        if ( is_active_sidebar( $id ) ) { echo '<div class="footer-widget">'; dynamic_sidebar( $id ); echo '</div>'; }
    }
}