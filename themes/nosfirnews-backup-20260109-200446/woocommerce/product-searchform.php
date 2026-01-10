<?php
echo '<form role="search" method="get" class="woocommerce-product-search" action="' . esc_url( home_url( '/' ) ) . '">';
echo '<label class="screen-reader-text" for="woocommerce-product-search-field">' . esc_html__( 'Search for:', 'nosfirnews' ) . '</label>';
echo '<input type="search" id="woocommerce-product-search-field" class="search-field" placeholder="' . esc_attr__( 'Search products…', 'nosfirnews' ) . '" value="' . esc_attr( get_search_query() ) . '" name="s" />';
echo '<button type="submit" value="' . esc_attr__( 'Search', 'nosfirnews' ) . '">' . esc_html__( 'Search', 'nosfirnews' ) . '</button>';
echo '<input type="hidden" name="post_type" value="product" />';
echo '</form>';
