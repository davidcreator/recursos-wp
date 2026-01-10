<?php
function nosfirnews_register_metabox(){
    add_meta_box('nosfirnews_meta','NosfirNews','nosfirnews_metabox_render',['post','page'],'side');
}
add_action('add_meta_boxes','nosfirnews_register_metabox');
function nosfirnews_metabox_render($post){
    $value=get_post_meta($post->ID,'_nosfirnews_option',true);
    echo '<label for="_nosfirnews_option">Opção</label>';
    echo '<input type="text" id="_nosfirnews_option" name="_nosfirnews_option" value="'.esc_attr($value).'" />';
}