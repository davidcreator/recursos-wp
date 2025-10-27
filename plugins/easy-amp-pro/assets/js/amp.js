<?php
/**
 * Default AMP Template
 * 
 * This is the default AMP template used when no theme-specific AMP template is found
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html amp lang="<?php echo get_locale(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    
    <!-- AMP Runtime -->
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    
    <!-- AMP Boilerplate -->
    <style amp-boilerplate>
        body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
    </style>
    <noscript>
        <style amp-boilerplate>
            body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}
        </style>
    </noscript>
    
    <!-- Custom AMP CSS -->
    <style amp-custom>
        <?php echo file_get_contents(EASY_AMP_PRO_PLUGIN_PATH . 'assets/css/amp.css'); ?>
        
        <?php
        // Add custom CSS from settings
        $settings = get_option('easy_amp_pro_settings', array());
        if (!empty($settings['amp_css'])) {
            echo $settings['amp_css'];
        }
        ?>
    </style>
    
    <!-- SEO Meta Tags -->
    <?php if (is_single() || is_page()): ?>
    <meta name="description" content="<?php echo esc_attr(wp_strip_all_tags(get_the_excerpt())); ?>">
    <?php endif; ?>
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo esc_attr(get_the_title()); ?>">
    <meta property="og:description" content="<?php echo esc_attr(wp_strip_all_tags(get_the_excerpt())); ?>">
    <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
    <meta property="og:type" content="<?php echo is_single() ? 'article' : 'website'; ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    
    <?php if (has_post_thumbnail()): ?>
    <meta property="og:image" content="<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>">
    <?php endif; ?>
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr(get_the_title()); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr(wp_strip_all_tags(get_the_excerpt())); ?>">
    
    <?php if (has_post_thumbnail()): ?>
    <meta name="twitter:image" content="<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>">
    <?php endif; ?>
    
    <!-- Canonical Link -->
    <link rel="canonical" href="<?php echo esc_url(get_permalink()); ?>">
    
    <!-- Schema.org JSON-LD -->
    <?php if (is_single() || is_page()): ?>
    <script type="application/ld+json">
    <?php
    $structured_data = array(
        '@context' => 'https://schema.org',
        '@type' => is_page() ? 'WebPage' : 'Article',
        'headline' => get_the_title(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author()
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_site_icon_url(60) ?: get_template_directory_uri() . '/assets/logo.png',
                'width' => 60,
                'height' => 60
            )
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink()
        ),
        'url' => get_permalink(),
        'description' => wp_strip_all_tags(get_the_excerpt())
    );
    
    if (has_post_thumbnail()) {
        $image_data = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        $structured_data['image'] = array(
            '@type' => 'ImageObject',
            'url' => $image_data[0],
            'width' => $image_data[1],
            'height' => $image_data[2]
        );
    }
    
    echo wp_json_encode($structured_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    ?>
    </script>
    <?php endif; ?>
    
    <?php
    // Add required AMP component scripts
    $content = get_the_content();
    
    // AMP Analytics
    if (!empty($settings['google_analytics_id'])):
    ?>
    <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
    <?php endif; ?>
    
    <?php
    // Check for other AMP components needed
    $amp_components = array();
    
    // Check for amp-video
    if (preg_match('/<amp-video/i', $content) || preg_match('/<video/i', $content)) {
        $amp_components[] = 'amp-video';
    }
    
    // Check for amp-iframe
    if (preg_match('/<amp-iframe/i', $content) || preg_match('/<iframe/i', $content)) {
        $amp_components[] = 'amp-iframe';
    }
    
    // Check for amp-youtube
    if (preg_match('/<amp-youtube/i', $content) || preg_match('/youtube\.com\/watch/i', $content) || preg_match('/youtu\.be\//i', $content)) {
        $amp_components[] = 'amp-youtube';
    }
    
    // Check for amp-vimeo
    if (preg_match('/<amp-vimeo/i', $content) || preg_match('/vimeo\.com/i', $content)) {
        $amp_components[] = 'amp-vimeo';
    }
    
    // Check for amp-twitter
    if (preg_match('/<amp-twitter/i', $content) || preg_match('/twitter\.com\/.*\/status/i', $content)) {
        $amp_components[] = 'amp-twitter';
    }
    
    // Check for amp-instagram
    if (preg_match('/<amp-instagram/i', $content) || preg_match('/instagram\.com\/p\//i', $content)) {
        $amp_components[] = 'amp-instagram';
    }
    
    // Output component scripts
    foreach (array_unique($amp_components) as $component) {
        echo '<script async custom-element="' . $component . '" src="https://cdn.ampproject.org/v0/' . $component . '-0.1.js"></script>' . "\n";
    }
    ?>
    
    <!-- Custom AMP Head Content -->
    <?php
    if (!empty($settings['custom_amp_head'])) {
        echo $settings['custom_amp_head'];
    }
    ?>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class('amp-wp'); ?>>

    <!-- Header -->
    <header class="amp-header">
        <div class="amp-container">
            <h1 class="amp-site-title">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php bloginfo('name'); ?>
                </a>
            </h1>
            
            <?php if (get_bloginfo('description')): ?>
                <p class="amp-site-description"><?php bloginfo('description'); ?></p>
            <?php endif; ?>
            
            <?php if (is_single() || is_page()): ?>
                <nav class="amp-breadcrumbs" aria-label="<?php _e('Breadcrumb', 'easy-amp-pro'); ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'easy-amp-pro'); ?></a>
                    
                    <?php if (is_single()): ?>
                        <?php
                        $categories = get_the_category();
                        if ($categories) {
                            $category = $categories[0];
                            echo '<span>&rsaquo;</span>';
                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '?amp=1">' . esc_html($category->name) . '</a>';
                        }
                        ?>
                        <span>&rsaquo;</span>
                        <span><?php the_title(); ?></span>
                    <?php elseif (is_page()): ?>
                        <?php
                        $parents = array();
                        $parent_id = wp_get_post_parent_id(get_the_ID());
                        
                        while ($parent_id) {
                            $parents[] = $parent_id;
                            $parent_id = wp_get_post_parent_id($parent_id);
                        }
                        
                        $parents = array_reverse($parents);
                        
                        foreach ($parents as $parent) {
                            echo '<span>&rsaquo;</span>';
                            echo '<a href="' . esc_url(get_permalink($parent)) . '?amp=1">' . esc_html(get_the_title($parent)) . '</a>';
                        }
                        ?>
                        <span>&rsaquo;</span>
                        <span><?php the_title(); ?></span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content -->
    <main class="amp-main" role="main">
        <div class="amp-container">
            
            <?php if (have_posts()): ?>
                
                <?php while (have_posts()): the_post(); ?>
                    
                    <article class="amp-article" itemscope itemtype="https://schema.org/Article">
                        
                        <?php if (is_single() || is_page()): ?>
                            <!-- Single Post/Page View -->
                            <header class="amp-article-header">
                                <h1 class="amp-article-title" itemprop="headline"><?php the_title(); ?></h1>
                                
                                <?php if (is_single()): ?>
                                    <div class="amp-article-meta">
                                        <time datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                        
                                        <span class="amp-article-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                            <?php _e('By', 'easy-amp-pro'); ?> 
                                            <span itemprop="name"><?php the_author(); ?></span>
                                        </span>
                                        
                                        <?php if (get_the_modified_date() !== get_the_date()): ?>
                                            <span class="amp-article-updated">
                                                <?php _e('Updated:', 'easy-amp-pro'); ?>
                                                <time datetime="<?php echo get_the_modified_date('c'); ?>" itemprop="dateModified">
                                                    <?php echo get_the_modified_date(); ?>
                                                </time>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php
                                        $reading_time = ceil(str_word_count(strip_tags(get_the_content())) / 200);
                                        ?>
                                        <span class="amp-reading-time">
                                            <?php printf(_n('%s min read', '%s mins read', $reading_time, 'easy-amp-pro'), $reading_time); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </header>
                            
                            <?php if (has_post_thumbnail()): ?>
                                <div class="amp-article-featured-image">
                                    <?php
                                    $image_id = get_post_thumbnail_id();
                                    $image_src = wp_get_attachment_image_src($image_id, 'large');
                                    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                                    ?>
                                    <amp-img 
                                        src="<?php echo esc_url($image_src[0]); ?>" 
                                        width="<?php echo intval($image_src[1]); ?>" 
                                        height="<?php echo intval($image_src[2]); ?>"
                                        alt="<?php echo esc_attr($image_alt ?: get_the_title()); ?>"
                                        layout="responsive"
                                        itemprop="image">
                                    </amp-img>
                                    
                                    <?php
                                    $caption = wp_get_attachment_caption($image_id);
                                    if ($caption):
                                    ?>
                                        <figcaption class="amp-image-caption"><?php echo esc_html($caption); ?></figcaption>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="amp-article-content" itemprop="articleBody">
                                <?php
                                $content = get_the_content();
                                
                                // Apply content filters but ensure AMP compatibility
                                $content = apply_filters('the_content', $content);
                                
                                // Additional AMP-specific content processing
                                echo $content;
                                ?>
                            </div>
                            
                            <?php if (is_single()): ?>
                                <footer class="amp-article-footer">
                                    <?php
                                    $categories = get_the_category();
                                    if ($categories && !is_wp_error($categories)):
                                    ?>
                                        <div class="amp-article-categories">
                                            <span><?php _e('Categories:', 'easy-amp-pro'); ?></span>
                                            <?php foreach ($categories as $category): ?>
                                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>?amp=1" 
                                                   rel="category tag"
                                                   itemprop="about">
                                                    <?php echo esc_html($category->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $tags = get_the_tags();
                                    if ($tags && !is_wp_error($tags)):
                                    ?>
                                        <div class="amp-article-tags">
                                            <span><?php _e('Tags:', 'easy-amp-pro'); ?></span>
                                            <?php foreach ($tags as $tag): ?>
                                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>?amp=1" 
                                                   rel="tag"
                                                   itemprop="keywords">
                                                    <?php echo esc_html($tag->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Social Sharing -->
                                    <div class="amp-social-sharing">
                                        <h4><?php _e('Share this article:', 'easy-amp-pro'); ?></h4>
                                        <div class="amp-social-buttons">
                                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                                               target="_blank" 
                                               rel="noopener"
                                               class="amp-social-button twitter">
                                                <?php _e('Twitter', 'easy-amp-pro'); ?>
                                            </a>
                                            
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                                               target="_blank" 
                                               rel="noopener"
                                               class="amp-social-button facebook">
                                                <?php _e('Facebook', 'easy-amp-pro'); ?>
                                            </a>
                                            
                                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" 
                                               target="_blank" 
                                               rel="noopener"
                                               class="amp-social-button linkedin">
                                                <?php _e('LinkedIn', 'easy-amp-pro'); ?>
                                            </a>
                                            
                                            <a href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" 
                                               class="amp-social-button email">
                                                <?php _e('Email', 'easy-amp-pro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </footer>
                                
                                <!-- Related Posts -->
                                <?php
                                $related_posts = get_posts(array(
                                    'category__in' => wp_get_post_categories(get_the_ID()),
                                    'numberposts' => 3,
                                    'post__not_in' => array(get_the_ID()),
                                    'post_status' => 'publish'
                                ));
                                
                                if ($related_posts):
                                ?>
                                    <section class="amp-related-posts">
                                        <h3><?php _e('Related Articles', 'easy-amp-pro'); ?></h3>
                                        <div class="amp-related-posts-grid">
                                            <?php foreach ($related_posts as $related): ?>
                                                <article class="amp-related-post">
                                                    <?php if (has_post_thumbnail($related->ID)): ?>
                                                        <div class="amp-related-thumbnail">
                                                            <a href="<?php echo esc_url(get_permalink($related->ID)); ?>?amp=1">
                                                                <?php
                                                                $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($related->ID), 'medium');
                                                                ?>
                                                                <amp-img 
                                                                    src="<?php echo esc_url($thumb_src[0]); ?>"
                                                                    width="<?php echo intval($thumb_src[1]); ?>"
                                                                    height="<?php echo intval($thumb_src[2]); ?>"
                                                                    alt="<?php echo esc_attr($related->post_title); ?>"
                                                                    layout="responsive">
                                                                </amp-img>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="amp-related-content">
                                                        <h4>
                                                            <a href="<?php echo esc_url(get_permalink($related->ID)); ?>?amp=1">
                                                                <?php echo esc_html($related->post_title); ?>
                                                            </a>
                                                        </h4>
                                                        <time datetime="<?php echo get_the_date('c', $related->ID); ?>">
                                                            <?php echo get_the_date('', $related->ID); ?>
                                                        </time>
                                                    </div>
                                                </article>
                                            <?php endforeach; ?>
                                        </div>
                                    </section>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                            
                        <?php else: ?>
                            
                            <!-- Archive/List View -->
                            <header class="amp-article-header">
                                <h2 class="amp-article-title">
                                    <a href="<?php echo esc_url(get_permalink()); ?>?amp=1" itemprop="url">
                                        <span itemprop="headline"><?php the_title(); ?></span>
                                    </a>
                                </h2>
                                
                                <div class="amp-article-meta">
                                    <time datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                                        <?php echo get_the_date(); ?>
                                    </time>
                                    
                                    <span class="amp-article-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                        <?php _e('By', 'easy-amp-pro'); ?> 
                                        <span itemprop="name"><?php the_author(); ?></span>
                                    </span>
                                </div>
                            </header>
                            
                            <?php if (has_post_thumbnail()): ?>
                                <div class="amp-article-thumbnail">
                                    <a href="<?php echo esc_url(get_permalink()); ?>?amp=1">
                                        <?php
                                        $image_id = get_post_thumbnail_id();
                                        $image_src = wp_get_attachment_image_src($image_id, 'medium');
                                        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                                        ?>
                                        <amp-img 
                                            src="<?php echo esc_url($image_src[0]); ?>" 
                                            width="<?php echo intval($image_src[1]); ?>" 
                                            height="<?php echo intval($image_src[2]); ?>"
                                            alt="<?php echo esc_attr($image_alt ?: get_the_title()); ?>"
                                            layout="responsive">
                                        </amp-img>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="amp-article-excerpt" itemprop="description">
                                <?php
                                if (has_excerpt()) {
                                    the_excerpt();
                                } else {
                                    echo wp_trim_words(get_the_content(), 30, '...');
                                }
                                ?>
                                
                                <a href="<?php echo esc_url(get_permalink()); ?>?amp=1" class="amp-read-more">
                                    <?php _e('Continue reading', 'easy-amp-pro'); ?> &rarr;
                                </a>
                            </div>
                            
                        <?php endif; ?>
                        
                    </article>
                    
                <?php endwhile; ?>
                
                <?php if (!is_single() && !is_page()): ?>
                    <!-- Pagination -->
                    <nav class="amp-pagination" aria-label="<?php _e('Posts navigation', 'easy-amp-pro'); ?>">
                        <?php
                        $pagination = paginate_links(array(
                            'type' => 'array',
                            'prev_text' => '&lsaquo; ' . __('Previous', 'easy-amp-pro'),
                            'next_text' => __('Next', 'easy-amp-pro') . ' &rsaquo;',
                            'mid_size' => 2
                        ));
                        
                        if ($pagination) {
                            foreach ($pagination as $link) {
                                // Add AMP parameter to pagination links
                                if (strpos($link, 'href="') !== false) {
                                    if (strpos($link, '?') !== false) {
                                        $link = str_replace('">', '&amp=1">', $link);
                                    } else {
                                        $link = str_replace('">', '?amp=1">', $link);
                                    }
                                }
                                echo $link;
                            }
                        }
                        ?>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                
                <!-- No Posts Found -->
                <div class="amp-no-posts">
                    <h2><?php _e('Nothing found', 'easy-amp-pro'); ?></h2>
                    
                    <?php if (is_search()): ?>
                        <p><?php printf(__('Sorry, no results were found for "%s".', 'easy-amp-pro'), get_search_query()); ?></p>
                        <p><?php _e('Try searching with different keywords or browse our categories.', 'easy-amp-pro'); ?></p>
                    <?php else: ?>
                        <p><?php _e('It looks like nothing was found at this location.', 'easy-amp-pro'); ?></p>
                        <p><?php _e('Maybe try a search or check out our latest posts.', 'easy-amp-pro'); ?></p>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url(home_url('/')); ?>?amp=1" class="button">
                        <?php _e('Go to Homepage', 'easy-amp-pro'); ?>
                    </a>
                </div>
                
            <?php endif; ?>
            
        </div>
    </main>

    <!-- Footer -->
    <footer class="amp-footer" role="contentinfo">
        <div class="amp-container">
            <div class="amp-footer-content">
                <div class="amp-footer-info">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'easy-amp-pro'); ?></p>
                    
                    <?php
                    $privacy_page = get_option('wp_page_for_privacy_policy');
                    if ($privacy_page):
                    ?>
                        <p>
                            <a href="<?php echo esc_url(get_permalink($privacy_page)); ?>?amp=1">
                                <?php _e('Privacy Policy', 'easy-amp-pro'); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="amp-footer-navigation">
                    <p>
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="nofollow">
                            <?php _e('Visit full site', 'easy-amp-pro'); ?>
                        </a>
                    </p>
                    
                    <?php if (easy_amp_pro_is_amp_endpoint()): ?>
                        <p class="amp-powered">
                            <a href="https://amp.dev" target="_blank" rel="noopener">
                                <?php _e('Powered by AMP', 'easy-amp-pro'); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- AMP Analytics -->
    <?php if (!empty($settings['google_analytics_id'])): ?>
        <amp-analytics type="googleanalytics" id="analytics-ga">
            <script type="application/json">
            {
                "vars": {
                    "account": "<?php echo esc_js($settings['google_analytics_id']); ?>"
                },
                "triggers": {
                    "trackPageview": {
                        "on": "visible",
                        "request": "pageview"
                    },
                    "trackScroll": {
                        "on": "scroll",
                        "scrollSpec": {
                            "verticalBoundaries": [25, 50, 75, 90]
                        },
                        "request": "event",
                        "vars": {
                            "eventCategory": "Scroll",
                            "eventAction": "scroll"
                        }
                    }
                }
            }
            </script>
        </amp-analytics>
    <?php endif; ?>
    
    <!-- Custom Analytics -->
    <?php if (!empty($settings['custom_analytics'])): ?>
        <?php echo $settings['custom_analytics']; ?>
    <?php endif; ?>

    <?php wp_footer(); ?>
    
</body>
</html>