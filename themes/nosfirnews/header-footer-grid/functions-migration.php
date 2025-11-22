<?php
function nosfirnews_hfg_run_migrations() {
    $v = get_option('nosfirnews_hfg_version', '0');
    if (version_compare($v, '1.0.0', '<')) {
        update_option('nosfirnews_hfg_version', '1.0.0');
    }
}