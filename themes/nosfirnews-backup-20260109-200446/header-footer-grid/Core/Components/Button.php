<?php
namespace NosfirNews\HeaderFooterGrid\Core\Components;
class Button extends Abstract_Component {
    public function render() {
        $url = isset($this->args['url']) ? esc_url($this->args['url']) : '#';
        $text = isset($this->args['text']) ? esc_html($this->args['text']) : __('Saiba mais','nosfirnews');
        echo '<a class="nn-btn" href="'.$url.'">'.$text.'</a>';
    }
}