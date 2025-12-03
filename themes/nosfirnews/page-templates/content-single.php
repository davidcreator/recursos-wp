<?php
while ( have_posts() ) { the_post();
    $hover = get_theme_mod( 'nn_thumb_hover_effect', 'none' ); $meta_hover = get_post_meta( get_the_ID(), 'nn_meta_thumb_hover', true ); $hover_class = ( $meta_hover ? $meta_hover : $hover );
    echo '<article id="post-' . get_the_ID() . '" class="single container ' . ( $hover_class !== 'none' ? 'thumb-effect-' . esc_attr( $hover_class ) : '' ) . '">';
    echo '<header class="single-header">';
    echo '<h1 class="entry-title">' . esc_html( get_the_title() ) . '</h1>';
    $show_single = (bool) get_theme_mod( 'nn_single_thumb_show', true ); $size = get_theme_mod( 'nn_thumb_size', 'large' );
    $meta_hide = (bool) get_post_meta( get_the_ID(), 'nn_meta_hide_thumb', false );
    $br = get_post_meta( get_the_ID(), 'nn_meta_thumb_border_radius', true );
    $shadow = get_post_meta( get_the_ID(), 'nn_meta_thumb_shadow', true );
    $filter = get_post_meta( get_the_ID(), 'nn_meta_thumb_filter', true );
    $style = '';
    if ( $br !== '' ) { $style .= '--nn-thumb-br:' . intval( $br ) . 'px;'; }
    if ( $shadow ) { $map = [ 'none' => '0 0 0 rgba(0,0,0,0)', 'soft' => '0 4px 12px rgba(0,0,0,.08)', 'medium' => '0 8px 24px rgba(0,0,0,.12)', 'hard' => '0 12px 32px rgba(0,0,0,.18)' ]; if ( isset( $map[ $shadow ] ) ) $style .= '--nn-thumb-shadow:' . $map[ $shadow ] . ';'; }
    if ( $filter ) { $fmap = [ 'none' => 'none', 'grayscale' => 'grayscale(1)', 'sepia' => 'sepia(1)', 'saturate' => 'saturate(1.6)', 'contrast' => 'contrast(1.2)', 'brightness' => 'brightness(1.1)', 'blur' => 'blur(2px)' ]; if ( isset( $fmap[ $filter ] ) ) $style .= '--nn-thumb-filter:' . $fmap[ $filter ] . ';'; }
    if ( $show_single && ! $meta_hide && has_post_thumbnail() ) { echo '<div class="single-featured"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>'; the_post_thumbnail( $size ); echo '</div>'; }
    echo '</header>';
    if ( function_exists( 'nosfirnews_partial_post_meta' ) ) { nosfirnews_partial_post_meta(); } else { get_template_part( 'inc/views/partials/post_meta' ); if ( function_exists( 'nosfirnews_partial_post_meta' ) ) nosfirnews_partial_post_meta(); }
    echo '<div class="entry-content">';
    the_content();
    echo '</div>';
    $tags = get_the_tag_list( '', ' ' ); if ( $tags ) { echo '<div class="post-tags">' . $tags . '</div>'; }
    echo '<nav class="post-nav">'; previous_post_link( '<div class="prev">%link</div>', '&larr; ' . esc_html__( 'Anterior', 'nosfirnews' ) ); next_post_link( '<div class="next">%link</div>', esc_html__( 'Próximo', 'nosfirnews' ) . ' &rarr;' ); echo '</nav>';
    echo '<section class="author-box">' . get_avatar( get_the_author_meta( 'ID' ), 64, '', '', [ 'class' => 'author-avatar' ] ) . '<div class="author-info"><h3 class="author-name">' . esc_html( get_the_author() ) . '</h3><p class="author-bio">' . esc_html( get_the_author_meta( 'description' ) ) . '</p></div></section>';
    nosfirnews_pagination();
    echo '<section class="comments-area">'; nosfirnews_partial_comments(); echo '</section>';
    echo '</article>';
}
