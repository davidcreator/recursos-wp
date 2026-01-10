<?php
function nosfirnews_run_migrations() {
    $current = get_option( 'nosfirnews_migrations_version', '0' );
    if ( version_compare( $current, '1.0.0', '<' ) ) {
        $map = [ 'old_option_container_width' => 'nosfirnews_container_width' ];
        foreach ( $map as $old => $new ) {
            $val = get_option( $old, null );
            if ( $val !== null ) update_option( $new, $val );
        }
        update_option( 'nosfirnews_migrations_version', '1.0.0' );
    }
}