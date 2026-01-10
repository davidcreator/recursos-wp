<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function nosfirnews_admin_upsell_notice() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    if ( isset( $_GET['dismiss_nosfirnews_upsell'] ) ) {
        update_user_meta( get_current_user_id(), 'nosfirnews_upsell_dismissed', 1 );
        return;
    }

    $dismissed = get_user_meta( get_current_user_id(), 'nosfirnews_upsell_dismissed', true );
    if ( $dismissed ) return;

    $url = esc_url( admin_url( 'themes.php?page=nosfirnews-admin' ) );
    echo '<div class="notice notice-info is-dismissible">'
       . '<p>' . esc_html__( 'Explore recursos avançados do tema NosfirNews.', 'nosfirnews' )
       . ' <a href="' . $url . '">' . esc_html__( 'Ver detalhes', 'nosfirnews' ) . '</a>'
       . ' <a style="margin-left:8px" href="' . esc_url( add_query_arg( 'dismiss_nosfirnews_upsell', '1' ) ) . '">' . esc_html__( 'Dispensar', 'nosfirnews' ) . '</a>'
       . '</p></div>';
}
add_action( 'admin_notices', 'nosfirnews_admin_upsell_notice' );

