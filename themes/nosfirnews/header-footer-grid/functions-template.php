<?php
function nosfirnews_hfg_render_header() {
    if (has_action('nosfirnews_hfg_header')) { do_action('nosfirnews_hfg_header'); return; }
    get_header();
}
function nosfirnews_hfg_render_footer() {
    if (has_action('nosfirnews_hfg_footer')) { do_action('nosfirnews_hfg_footer'); return; }
    get_footer();
}