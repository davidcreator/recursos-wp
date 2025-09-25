<?php
/**
 * Template Name: Minimal Clean
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div class="minimal-layout">
    <div class="container">
        
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('minimal-article'); ?>>
                
                <!-- Article Header -->
                <header class="minimal-header">
                    <?php if (is_single()): ?>
                        <div class="minimal-meta">
                            <span class="minimal-category"><?php the_category(' / '); ?></span>
                            <time class="minimal-date" datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                        </div>
                        
                        <h1 class="minimal-title"><?php the_title(); ?></h1>
                        
                        <div class="minimal-author">
                            <div class="author-avatar">
                                <?php echo get_avatar(get_the_author_meta('ID'), 60); ?>
                            </div>
                            <div class="author-info">
                                <span class="author-name"><?php the_author(); ?></span>
                                <span class="author-bio"><?php the_author_meta('description'); ?></span>
                            </div>
                        </div>
                        
                        <?php if (has_post_thumbnail()): ?>
                            <div class="minimal-featured-image">
                                <?php the_post_thumbnail('large'); ?>
                                <?php if (get_post(get_post_thumbnail_id())->post_excerpt): ?>
                                    <figcaption class="image-caption">
                                        <?php echo get_post(get_post_thumbnail_id())->post_excerpt; ?>
                                    </figcaption>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="minimal-meta">
                            <span class="minimal-category"><?php the_category(' / '); ?></span>
                            <time class="minimal-date" datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                        </div>
                        
                        <h2 class="minimal-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <?php if (has_post_thumbnail()): ?>
                            <div class="minimal-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </header>
                
                <!-- Article Content -->
                <div class="minimal-content">
                    <?php if (is_single()): ?>
                        <?php the_content(); ?>
                        
                        <?php
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . __('Pages:', 'nosfirnews'),
                            'after' => '</div>',
                        ));
                        ?>
                        
                    <?php else: ?>
                        <div class="minimal-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="minimal-read-more">
                            <?php _e('Continue Reading', 'nosfirnews'); ?>
                            <span class="read-more-arrow">→</span>
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if (is_single()): ?>
                    <!-- Article Footer -->
                    <footer class="minimal-footer">
                        
                        <!-- Tags -->
                        <?php if (has_tag()): ?>
                            <div class="minimal-tags">
                                <span class="tags-label"><?php _e('Tags:', 'nosfirnews'); ?></span>
                                <?php the_tags('', ', ', ''); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Share Buttons -->
                        <div class="minimal-share">
                            <span class="share-label"><?php _e('Share:', 'nosfirnews'); ?></span>
                            <div class="share-buttons">
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                                   target="_blank" class="share-twitter">
                                    <?php _e('Twitter', 'nosfirnews'); ?>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                                   target="_blank" class="share-facebook">
                                    <?php _e('Facebook', 'nosfirnews'); ?>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" 
                                   target="_blank" class="share-linkedin">
                                    <?php _e('LinkedIn', 'nosfirnews'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Navigation -->
                        <div class="minimal-navigation">
                            <?php
                            $prev_post = get_previous_post();
                            $next_post = get_next_post();
                            ?>
                            
                            <?php if ($prev_post): ?>
                                <div class="nav-previous">
                                    <span class="nav-label"><?php _e('Previous Article', 'nosfirnews'); ?></span>
                                    <a href="<?php echo get_permalink($prev_post); ?>" class="nav-link">
                                        <?php echo get_the_title($prev_post); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($next_post): ?>
                                <div class="nav-next">
                                    <span class="nav-label"><?php _e('Next Article', 'nosfirnews'); ?></span>
                                    <a href="<?php echo get_permalink($next_post); ?>" class="nav-link">
                                        <?php echo get_the_title($next_post); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    </footer>
                    
                    <!-- Related Posts -->
                    <?php
                    $related_posts = get_posts(array(
                        'numberposts' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'category__in' => wp_get_post_categories(get_the_ID())
                    ));
                    
                    if ($related_posts): ?>
                        <section class="minimal-related">
                            <h3 class="related-title"><?php _e('Related Articles', 'nosfirnews'); ?></h3>
                            <div class="related-grid">
                                <?php foreach ($related_posts as $post): setup_postdata($post); ?>
                                    <article class="related-post">
                                        <?php if (has_post_thumbnail()): ?>
                                            <div class="related-image">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('medium'); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="related-content">
                                            <h4 class="related-post-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            <time class="related-date"><?php echo get_the_date(); ?></time>
                                        </div>
                                    </article>
                                <?php endforeach; wp_reset_postdata(); ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <!-- Comments -->
                    <?php if (comments_open() || get_comments_number()): ?>
                        <section class="minimal-comments">
                            <?php comments_template(); ?>
                        </section>
                    <?php endif; ?>
                    
                <?php endif; ?>
                
            </article>
            
        <?php endwhile; endif; ?>
        
        <?php if (!is_single()): ?>
            <!-- Pagination -->
            <div class="minimal-pagination">
                <?php
                the_posts_pagination(array(
                    'prev_text' => __('← Previous', 'nosfirnews'),
                    'next_text' => __('Next →', 'nosfirnews'),
                ));
                ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<?php get_footer(); ?>