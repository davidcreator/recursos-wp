<?php
/**
 * Template Name: Magazine Layout
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div class="magazine-layout">
    <div class="container">
        <div class="magazine-grid">
            
            <!-- Featured Article -->
            <div class="featured-article">
                <?php
                $featured_posts = nosfirnews_get_featured_posts(1);
                
                if ($featured_posts->have_posts()) {
                    while ($featured_posts->have_posts()) {
                        $featured_posts->the_post();
                        ?>
                        <article class="featured-post">
                            <?php echo nosfirnews_get_post_card(get_the_ID(), 'featured', array(
                                'show_category' => true,
                                'show_date' => true,
                                'show_excerpt' => true,
                                'show_author' => true,
                                'excerpt_length' => 30,
                                'thumbnail_size' => 'large'
                            )); ?>
                        </article>
                        <?php
                    }
                    wp_reset_postdata();
                }
                ?>
            </div>
            
            <!-- Secondary Articles -->
            <div class="secondary-articles">
                <?php
                $secondary_posts = nosfirnews_get_featured_posts(4, true);
                
                if ($secondary_posts->have_posts()) {
                    while ($secondary_posts->have_posts()) {
                        $secondary_posts->the_post();
                        ?>
                        <article class="secondary-post">
                            <?php echo nosfirnews_get_post_card(get_the_ID(), 'compact', array(
                                'show_category' => true,
                                'show_date' => true,
                                'show_excerpt' => false,
                                'thumbnail_size' => 'medium'
                            )); ?>
                        </article>
                        <?php
                    }
                    wp_reset_postdata();
                }
                ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="magazine-sidebar">
                <div class="sidebar-widget">
                    <h3><?php _e('Trending Now', 'nosfirnews'); ?></h3>
                    <?php
                    $trending_posts = nosfirnews_get_trending_posts(5);
                    
                    if ($trending_posts->have_posts()) {
                        echo '<ul class="trending-list">';
                        while ($trending_posts->have_posts()) {
                            $trending_posts->the_post();
                            ?>
                            <li class="trending-item">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo nosfirnews_get_post_card(get_the_ID(), 'compact', array(
                                        'show_date' => true,
                                        'show_excerpt' => false,
                                        'show_category' => false,
                                        'thumbnail_size' => 'thumbnail'
                                    )); ?>
                                </a>
                            </li>
                            <?php
                        }
                        echo '</ul>';
                        wp_reset_postdata();
                    }
                    ?>
                </div>
                
                <?php if (is_active_sidebar('sidebar-1')): ?>
                    <?php dynamic_sidebar('sidebar-1'); ?>
                <?php endif; ?>
            </aside>
            
        </div>
        
        <!-- Latest Articles Grid -->
        <div class="latest-articles">
            <h2 class="section-title"><?php _e('Latest Articles', 'nosfirnews'); ?></h2>
            <div class="articles-grid">
                <?php
                $latest_posts = new WP_Query(array(
                    'posts_per_page' => 6,
                    'offset' => 5
                ));
                
                if ($latest_posts->have_posts()) {
                    while ($latest_posts->have_posts()) {
                        $latest_posts->the_post();
                        ?>
                        <article class="grid-post">
                            <?php echo nosfirnews_get_post_card(get_the_ID(), 'grid', array(
                                'show_category' => true,
                                'show_date' => true,
                                'show_excerpt' => true,
                                'show_author' => true,
                                'excerpt_length' => 15,
                                'thumbnail_size' => 'medium'
                            )); ?>
                        </article>
                        <?php
                    }
                    wp_reset_postdata();
                }
                ?>
            </div>
        </div>
        
    </div>
</div>

<?php get_footer(); ?>