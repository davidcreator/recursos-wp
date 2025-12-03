<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php $hover = get_theme_mod( 'nn_thumb_hover_effect', 'none' ); $meta_hover = get_post_meta( get_the_ID(), 'nn_meta_thumb_hover', true ); $hover_class = ( $meta_hover && $meta_hover !== 'default' ? $meta_hover : $hover ); $fit = get_theme_mod( 'nn_thumb_fit', 'contain' ); $meta_fit = get_post_meta( get_the_ID(), 'nn_meta_thumb_fit', true ); $fit_class = ( $meta_fit && $meta_fit !== 'default' ? $meta_fit : $fit ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card card card-body mb-4 ' . ( $hover_class !== 'none' ? 'thumb-effect-' . $hover_class : '' ) . ' thumb-fit-' . esc_attr( $fit_class ) ); ?>>
    <header class="entry-header">
        <?php 
        $show = (bool) get_theme_mod( 'nn_post_thumb_show', true ); 
        $size = get_theme_mod( 'nn_thumb_size', 'large' ); 
        $meta_hide = (bool) get_post_meta( get_the_ID(), 'nn_meta_hide_thumb', false );
        $br = get_post_meta( get_the_ID(), 'nn_meta_thumb_border_radius', true );
        $shadow = get_post_meta( get_the_ID(), 'nn_meta_thumb_shadow', true );
        $filter = get_post_meta( get_the_ID(), 'nn_meta_thumb_filter', true );
        $style = '';
        if ( $br !== '' ) { $style .= '--nn-thumb-br:' . intval( $br ) . 'px;'; }
        if ( $shadow ) { 
            $map = [ 'none' => '0 0 0 rgba(0,0,0,0)', 'soft' => '0 4px 12px rgba(0,0,0,.08)', 'medium' => '0 8px 24px rgba(0,0,0,.12)', 'hard' => '0 12px 32px rgba(0,0,0,.18)' ]; 
            if ( isset( $map[ $shadow ] ) ) $style .= '--nn-thumb-shadow:' . $map[ $shadow ] . ';'; 
        }
        if ( $filter ) { 
            $fmap = [ 'none' => 'none', 'grayscale' => 'grayscale(1)', 'sepia' => 'sepia(1)', 'saturate' => 'saturate(1.6)', 'contrast' => 'contrast(1.2)', 'brightness' => 'brightness(1.1)', 'blur' => 'blur(2px)' ]; 
            if ( isset( $fmap[ $filter ] ) ) $style .= '--nn-thumb-filter:' . $fmap[ $filter ] . ';'; 
        }
        if ( $show && ! $meta_hide && has_post_thumbnail() ) { echo '<div class="entry-thumb mb-3"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>'; the_post_thumbnail( $size ); echo '</div>'; } ?>
        <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
    </header>
    <?php 
    $show_ex = (bool) get_theme_mod( 'nn_archive_show_excerpt', true );
    $meta_hide_ex = (bool) get_post_meta( get_the_ID(), 'nn_meta_hide_excerpt', false );
    if ( $show_ex && ! $meta_hide_ex ) { echo '<div class="entry-summary">'; the_excerpt(); echo '</div>'; }
    $show_rm = (bool) get_theme_mod( 'nn_archive_show_read_more', true );
    $meta_hide_rm = (bool) get_post_meta( get_the_ID(), 'nn_meta_hide_read_more', false );
    if ( $show_rm && ! $meta_hide_rm ) { $rm_text = get_post_meta( get_the_ID(), 'nn_meta_read_more_text', true ); if ( ! $rm_text ) $rm_text = get_theme_mod( 'nn_read_more_text', __( 'Leia mais', 'nosfirnews' ) ); $style = get_theme_mod( 'nn_read_more_style', 'primary' ); echo '<a class="nn-read-more ' . esc_attr( $style ) . '" href="' . esc_url( get_permalink() ) . '">' . esc_html( $rm_text ) . '</a>'; }
    ?>
</article>
