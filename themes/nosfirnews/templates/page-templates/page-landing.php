<?php
/**
 * Template Name: Landing Page
 * 
 * Advanced landing page template with customizable sections
 *
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area landing-page">
    <main id="main" class="site-main">

        <?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'landing-page-content' ); ?>>
                
                <!-- Hero Section -->
                <section class="hero-section" id="hero">
                    <div class="hero-background">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="hero-image">
                                <?php the_post_thumbnail( 'full', array( 'class' => 'hero-bg-image' ) ); ?>
                            </div>
                        <?php endif; ?>
                        <div class="hero-overlay"></div>
                    </div>
                    
                    <div class="container">
                        <div class="hero-content">
                            <div class="hero-text">
                                <?php 
                                $hero_subtitle = get_post_meta( get_the_ID(), '_landing_hero_subtitle', true );
                                if ( $hero_subtitle ) : ?>
                                    <p class="hero-subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
                                <?php endif; ?>
                                
                                <h1 class="hero-title"><?php the_title(); ?></h1>
                                
                                <?php if ( get_the_content() ) : ?>
                                    <div class="hero-description">
                                        <?php the_content(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php 
                                $cta_text = get_post_meta( get_the_ID(), '_landing_cta_text', true );
                                $cta_url = get_post_meta( get_the_ID(), '_landing_cta_url', true );
                                if ( $cta_text && $cta_url ) : ?>
                                    <div class="hero-cta">
                                        <a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn-primary btn-lg">
                                            <?php echo esc_html( $cta_text ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="scroll-indicator">
                        <a href="#features" class="scroll-down">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </section>

                <!-- Features Section -->
                <?php 
                $features = get_post_meta( get_the_ID(), '_landing_features', true );
                if ( $features && is_array( $features ) ) : ?>
                    <section class="features-section" id="features">
                        <div class="container">
                            <div class="section-header">
                                <?php 
                                $features_title = get_post_meta( get_the_ID(), '_landing_features_title', true );
                                if ( $features_title ) : ?>
                                    <h2 class="section-title"><?php echo esc_html( $features_title ); ?></h2>
                                <?php endif; ?>
                                
                                <?php 
                                $features_subtitle = get_post_meta( get_the_ID(), '_landing_features_subtitle', true );
                                if ( $features_subtitle ) : ?>
                                    <p class="section-subtitle"><?php echo esc_html( $features_subtitle ); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="features-grid">
                                <?php foreach ( $features as $feature ) : ?>
                                    <div class="feature-item">
                                        <?php if ( ! empty( $feature['icon'] ) ) : ?>
                                            <div class="feature-icon">
                                                <i class="<?php echo esc_attr( $feature['icon'] ); ?>"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ( ! empty( $feature['title'] ) ) : ?>
                                            <h3 class="feature-title"><?php echo esc_html( $feature['title'] ); ?></h3>
                                        <?php endif; ?>
                                        
                                        <?php if ( ! empty( $feature['description'] ) ) : ?>
                                            <p class="feature-description"><?php echo esc_html( $feature['description'] ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- About Section -->
                <?php 
                $about_content = get_post_meta( get_the_ID(), '_landing_about_content', true );
                if ( $about_content ) : ?>
                    <section class="about-section" id="about">
                        <div class="container">
                            <div class="about-content">
                                <div class="about-text">
                                    <?php 
                                    $about_title = get_post_meta( get_the_ID(), '_landing_about_title', true );
                                    if ( $about_title ) : ?>
                                        <h2 class="about-title"><?php echo esc_html( $about_title ); ?></h2>
                                    <?php endif; ?>
                                    
                                    <div class="about-description">
                                        <?php echo wp_kses_post( $about_content ); ?>
                                    </div>
                                    
                                    <?php 
                                    $about_cta_text = get_post_meta( get_the_ID(), '_landing_about_cta_text', true );
                                    $about_cta_url = get_post_meta( get_the_ID(), '_landing_about_cta_url', true );
                                    if ( $about_cta_text && $about_cta_url ) : ?>
                                        <div class="about-cta">
                                            <a href="<?php echo esc_url( $about_cta_url ); ?>" class="btn btn-outline">
                                                <?php echo esc_html( $about_cta_text ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php 
                                $about_image = get_post_meta( get_the_ID(), '_landing_about_image', true );
                                if ( $about_image ) : ?>
                                    <div class="about-image">
                                        <?php echo wp_get_attachment_image( $about_image, 'large', false, array( 'class' => 'about-img' ) ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Testimonials Section -->
                <?php 
                $testimonials = get_post_meta( get_the_ID(), '_landing_testimonials', true );
                if ( $testimonials && is_array( $testimonials ) ) : ?>
                    <section class="testimonials-section" id="testimonials">
                        <div class="container">
                            <div class="section-header">
                                <?php 
                                $testimonials_title = get_post_meta( get_the_ID(), '_landing_testimonials_title', true );
                                if ( $testimonials_title ) : ?>
                                    <h2 class="section-title"><?php echo esc_html( $testimonials_title ); ?></h2>
                                <?php endif; ?>
                            </div>
                            
                            <div class="testimonials-slider">
                                <?php foreach ( $testimonials as $testimonial ) : ?>
                                    <div class="testimonial-item">
                                        <?php if ( ! empty( $testimonial['content'] ) ) : ?>
                                            <blockquote class="testimonial-content">
                                                "<?php echo esc_html( $testimonial['content'] ); ?>"
                                            </blockquote>
                                        <?php endif; ?>
                                        
                                        <div class="testimonial-author">
                                            <?php if ( ! empty( $testimonial['avatar'] ) ) : ?>
                                                <div class="author-avatar">
                                                    <?php echo wp_get_attachment_image( $testimonial['avatar'], 'thumbnail', false, array( 'class' => 'avatar' ) ); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="author-info">
                                                <?php if ( ! empty( $testimonial['name'] ) ) : ?>
                                                    <h4 class="author-name"><?php echo esc_html( $testimonial['name'] ); ?></h4>
                                                <?php endif; ?>
                                                
                                                <?php if ( ! empty( $testimonial['position'] ) ) : ?>
                                                    <p class="author-position"><?php echo esc_html( $testimonial['position'] ); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Contact Section -->
                <?php 
                $contact_form_shortcode = get_post_meta( get_the_ID(), '_landing_contact_form', true );
                if ( $contact_form_shortcode ) : ?>
                    <section class="contact-section" id="contact">
                        <div class="container">
                            <div class="section-header">
                                <?php 
                                $contact_title = get_post_meta( get_the_ID(), '_landing_contact_title', true );
                                if ( $contact_title ) : ?>
                                    <h2 class="section-title"><?php echo esc_html( $contact_title ); ?></h2>
                                <?php endif; ?>
                                
                                <?php 
                                $contact_subtitle = get_post_meta( get_the_ID(), '_landing_contact_subtitle', true );
                                if ( $contact_subtitle ) : ?>
                                    <p class="section-subtitle"><?php echo esc_html( $contact_subtitle ); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="contact-form">
                                <?php echo do_shortcode( $contact_form_shortcode ); ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php
get_footer();
?>