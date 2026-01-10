<?php
function nosfirnews_enqueue_google_fonts(){
    $handle='nosfirnews-fonts';
    $href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap';
    wp_enqueue_style($handle,$href,[],null);
}
