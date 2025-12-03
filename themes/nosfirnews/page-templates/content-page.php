<?php
while ( have_posts() ) { the_post();
    $hover = get_theme_mod( 'nn_thumb_hover_effect', 'none' ); $meta_hover = get_post_meta( get_the_ID(), 'nn_meta_thumb_hover', true ); $hover_class = ( $meta_hover ? $meta_hover : $hover );
    echo '<article id="post-' . get_the_ID() . '" class="page ' . ( $hover_class !== 'none' ? 'thumb-effect-' . esc_attr( $hover_class ) : '' ) . '">';
    $show_page = (bool) get_theme_mod( 'nn_page_featured_show', true ); $size = get_theme_mod( 'nn_thumb_size', 'large' );
    $meta_hide = (bool) get_post_meta( get_the_ID(), 'nn_meta_hide_thumb', false );
    $br = get_post_meta( get_the_ID(), 'nn_meta_thumb_border_radius', true );
    $shadow = get_post_meta( get_the_ID(), 'nn_meta_thumb_shadow', true );
    $filter = get_post_meta( get_the_ID(), 'nn_meta_thumb_filter', true );
    $style = '';
    if ( $br !== '' ) { $style .= '--nn-thumb-br:' . intval( $br ) . 'px;'; }
    if ( $shadow ) { $map = [ 'none' => '0 0 0 rgba(0,0,0,0)', 'soft' => '0 4px 12px rgba(0,0,0,.08)', 'medium' => '0 8px 24px rgba(0,0,0,.12)', 'hard' => '0 12px 32px rgba(0,0,0,.18)' ]; if ( isset( $map[ $shadow ] ) ) $style .= '--nn-thumb-shadow:' . $map[ $shadow ] . ';'; }
    if ( $filter ) { $fmap = [ 'none' => 'none', 'grayscale' => 'grayscale(1)', 'sepia' => 'sepia(1)', 'saturate' => 'saturate(1.6)', 'contrast' => 'contrast(1.2)', 'brightness' => 'brightness(1.1)', 'blur' => 'blur(2px)' ]; if ( isset( $fmap[ $filter ] ) ) $style .= '--nn-thumb-filter:' . $fmap[ $filter ] . ';'; }
    if ( $show_page && ! $meta_hide && has_post_thumbnail() ) { echo '<div class="page-featured"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>'; the_post_thumbnail( $size ); echo '</div>'; }
    the_content();
    echo '</article>';
}
