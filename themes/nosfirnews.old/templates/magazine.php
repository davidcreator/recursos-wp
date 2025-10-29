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
                $featured_posts = get_posts(array(
                    'numberposts' => 1,
                    'meta_key' => '_nosfirnews_featured',
                    'meta_value' => '1'
                ));
                
                if ($featured_posts) {
                    foreach ($featured_posts as $post) {
                        setup_postdata($post);
                        ?>
                        <article class="featured-post">
                            <?php if (has_post_thumbnail()): ?>
                                <div class="featured-image">
                                    <?php the_post_thumbnail('large'); ?>
                                    <div class="featured-overlay">
                                        <div class="featured-content">
                                            <span class="featured-category"><?php the_category(', '); ?></span>
                                            <h2 class="featured-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            <div class="featured-excerpt">
                                                <?php the_excerpt(); ?>
                                            </div>
                                            <div class="featured-meta">
                                                <span class="author"><?php the_author(); ?></span>
                                                <span class="date"><?php the_date(); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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
                $secondary_posts = get_posts(array(
                    'numberposts' => 4,
                    'offset' => 1
                ));
                
                if ($secondary_posts) {
                    foreach ($secondary_posts as $post) {
                        setup_postdata($post);
                        ?>
                        <article class="secondary-post">
                            <?php if (has_post_thumbnail()): ?>
                                <div class="secondary-image">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="secondary-content">
                                <span class="secondary-category"><?php the_category(', '); ?></span>
                                <h3 class="secondary-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="secondary-meta">
                                    <span class="date"><?php the_date(); ?></span>
                                </div>
                            </div>
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
                    $trending_posts = get_posts(array(
                        'numberposts' => 5,
                        'meta_key' => '_nosfirnews_views',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC'
                    ));
                    
                    if ($trending_posts) {
                        echo '<ul class="trending-list">';
                        foreach ($trending_posts as $post) {
                            setup_postdata($post);
                            ?>
                            <li class="trending-item">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()): ?>
                                        <div class="trending-thumb">
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="trending-content">
                                        <h4><?php the_title(); ?></h4>
                                        <span class="trending-date"><?php the_date(); ?></span>
                                    </div>
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
                $latest_posts = get_posts(array(
                    'numberposts' => 6,
                    'offset' => 5
                ));
                
                if ($latest_posts) {
                    foreach ($latest_posts as $post) {
                        setup_postdata($post);
                        ?>
                        <article class="grid-post">
                            <?php if (has_post_thumbnail()): ?>
                                <div class="grid-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="grid-content">
                                <span class="grid-category"><?php the_category(', '); ?></span>
                                <h3 class="grid-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="grid-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                </div>
                                <div class="grid-meta">
                                    <span class="author"><?php the_author(); ?></span>
                                    <span class="date"><?php the_date(); ?></span>
                                </div>
                            </div>
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