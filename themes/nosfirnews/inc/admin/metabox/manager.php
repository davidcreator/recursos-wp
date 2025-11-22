<?php
function nosfirnews_save_metabox($post_id){
    if(isset($_POST['_nosfirnews_option'])){
        update_post_meta($post_id,'_nosfirnews_option',sanitize_text_field($_POST['_nosfirnews_option']));
    }
}
add_action('save_post','nosfirnews_save_metabox');