<?php
/**
 * Template Name: Página Demonstrativa NosfirNews
 * Description: Template personalizado para demonstrar todos os recursos do tema NosfirNews
 * 
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<?php // Estilos da página demo foram movidos para assets/css/page-demo.css e serão enfileirados via inc/page-templates.php ?>

<div class="site-content-wrapper">
    <div class="demo-page-container">
        
        <?php while ( have_posts() ) : the_post(); ?>
            <?php
            $post_id = get_the_ID();
            $hero_section = get_post_meta( $post_id, '_nosfirnews_hero_section', true );
            $call_to_action = get_post_meta( $post_id, '_nosfirnews_call_to_action', true );
            $testimonials = get_post_meta( $post_id, '_nosfirnews_testimonials', true );
            $features = get_post_meta( $post_id, '_nosfirnews_features', true );
            $layout_sections = get_post_meta( $post_id, '_nosfirnews_layout_sections', true );
            $gallery_images = get_post_meta( $post_id, '_nosfirnews_gallery_images', true );
            $gallery_type = get_post_meta( $post_id, '_nosfirnews_gallery_type', true );
            $gallery_columns = get_post_meta( $post_id, '_nosfirnews_gallery_columns', true );
            $custom_css = get_post_meta( $post_id, '_nosfirnews_custom_css', true );
            $custom_js = get_post_meta( $post_id, '_nosfirnews_custom_js', true );
            ?>
            
            <!-- Breadcrumbs -->
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Início</a> &raquo; 
                <span><?php the_title(); ?></span>
            </nav>

            <?php if ( ! empty( $hero_section ) && ! empty( $hero_section['enabled'] ) ) : ?>
                <section class="demo-hero-section" aria-label="Hero">
                    <div class="demo-hero-bg" style="<?php echo ! empty( $hero_section['background_image'] ) ? 'background-image: url(' . esc_url( $hero_section['background_image'] ) . ');' : ''; ?>">
                        <div class="demo-hero-overlay" style="opacity: <?php echo isset( $hero_section['overlay_opacity'] ) ? esc_attr( $hero_section['overlay_opacity'] ) : 0.4; ?>;"></div>
                        <div class="demo-hero-content">
                            <?php if ( ! empty( $hero_section['subtitle'] ) ) : ?>
                                <p class="demo-hero-subtitle"><?php echo esc_html( $hero_section['subtitle'] ); ?></p>
                            <?php endif; ?>
                            <h1 class="demo-hero-title"><?php echo ! empty( $hero_section['title'] ) ? esc_html( $hero_section['title'] ) : get_the_title(); ?></h1>
                            <?php if ( ! empty( $hero_section['button_text'] ) && ! empty( $hero_section['button_url'] ) ) : ?>
                                <div class="demo-hero-cta">
                                    <a class="btn btn-primary btn-lg" href="<?php echo esc_url( $hero_section['button_url'] ); ?>"><?php echo esc_html( $hero_section['button_text'] ); ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Demo Introduction -->
            <div class="demo-section">
                <h1 class="demo-title">
                    <i class="fas fa-star"></i>
                    <?php the_title(); ?>
                    <span class="amp-badge">AMP Ready</span>
                </h1>
                <div class="demo-description">
                    <?php if ( has_excerpt() ) : ?>
                        <?php the_excerpt(); ?>
                    <?php else : ?>
                        Esta página demonstra todos os recursos e componentes do tema NosfirNews v2.0.0, 
                        incluindo layouts responsivos, acessibilidade, SEO otimizado e suporte completo ao AMP.
                    <?php endif; ?>
                </div>
                
                <!-- Accessibility Features -->
                <div class="accessibility-features">
                    <h3><i class="fas fa-universal-access"></i> Recursos de Acessibilidade</h3>
                    <ul>
                        <li>Skip links para navegação por teclado</li>
                        <li>Marcação semântica HTML5</li>
                        <li>Suporte a leitores de tela</li>
                        <li>Contraste adequado de cores</li>
                        <li>Navegação por teclado otimizada</li>
                    </ul>
                </div>
            </div>

            <?php if ( ! empty( $call_to_action ) && ! empty( $call_to_action['enabled'] ) ) : ?>
                <section class="demo-cta-section" aria-label="Call to Action" style="<?php echo ! empty( $call_to_action['background_color'] ) ? 'background-color:' . esc_attr( $call_to_action['background_color'] ) . ';' : ''; ?><?php echo ! empty( $call_to_action['text_color'] ) ? 'color:' . esc_attr( $call_to_action['text_color'] ) . ';' : ''; ?>">
                    <div class="demo-cta-container">
                        <?php if ( ! empty( $call_to_action['title'] ) ) : ?>
                            <h2 class="demo-cta-title"><?php echo esc_html( $call_to_action['title'] ); ?></h2>
                        <?php endif; ?>
                        <?php if ( ! empty( $call_to_action['description'] ) ) : ?>
                            <p class="demo-cta-description"><?php echo esc_html( $call_to_action['description'] ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $call_to_action['button_text'] ) && ! empty( $call_to_action['button_url'] ) ) : ?>
                            <a class="btn btn-outline" href="<?php echo esc_url( $call_to_action['button_url'] ); ?>" style="<?php echo ! empty( $call_to_action['text_color'] ) ? 'border-color:' . esc_attr( $call_to_action['text_color'] ) . ';color:' . esc_attr( $call_to_action['text_color'] ) . ';' : ''; ?>">
                                <?php echo esc_html( $call_to_action['button_text'] ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Featured News Section -->
            <section class="demo-section">
                <h2 class="demo-title">
                    <i class="fas fa-fire"></i>
                    Notícias em Destaque
                </h2>
                <p class="demo-description">Layout de cards responsivo para notícias principais</p>
                
                <div class="news-grid">
                    <?php
                    // Buscar posts recentes para demonstração
                    $demo_posts = get_posts(array(
                        'numberposts' => 3,
                        'post_status' => 'publish'
                    ));
                    
                    if (!empty($demo_posts)) :
                        foreach ($demo_posts as $demo_post) :
                            setup_postdata($demo_post);
                            $categories = get_the_category($demo_post->ID);
                            $category_name = !empty($categories) ? $categories[0]->name : 'Geral';
                            ?>
                            <article class="news-card">
                                <div class="news-image">
                                    <?php if (has_post_thumbnail($demo_post->ID)) : ?>
                                        <?php echo get_the_post_thumbnail($demo_post->ID, 'medium', array('style' => 'width: 100%; height: 200px; object-fit: cover;')); ?>
                                    <?php else : ?>
                                        <i class="fas fa-newspaper"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="news-content">
                                    <span class="news-category"><?php echo esc_html($category_name); ?></span>
                                    <h3 class="news-title">
                                        <a href="<?php echo get_permalink($demo_post->ID); ?>">
                                            <?php echo get_the_title($demo_post->ID); ?>
                                        </a>
                                    </h3>
                                    <p class="news-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt($demo_post->ID), 20, '...'); ?>
                                    </p>
                                    <div class="news-meta">
                                        <span><i class="fas fa-user"></i> <?php echo get_the_author_meta('display_name', $demo_post->post_author); ?></span>
                                        <span><i class="fas fa-calendar"></i> <?php echo get_the_date('j M Y', $demo_post->ID); ?></span>
                                    </div>
                                </div>
                            </article>
                            <?php
                        endforeach;
                        wp_reset_postdata();
                    else :
                        // Posts de exemplo se não houver posts reais
                        ?>
                        <article class="news-card">
                            <div class="news-image">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div class="news-content">
                                <span class="news-category">Tecnologia</span>
                                <h3 class="news-title">Nova Era da Inteligência Artificial Revoluciona o Mercado</h3>
                                <p class="news-excerpt">
                                    Descobertas recentes em IA prometem transformar completamente a forma como 
                                    interagimos com a tecnologia no dia a dia...
                                </p>
                                <div class="news-meta">
                                    <span><i class="fas fa-user"></i> David Almeida</span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('j M Y'); ?></span>
                                </div>
                            </div>
                        </article>
                        
                        <article class="news-card">
                            <div class="news-image">
                                <i class="fas fa-futbol"></i>
                            </div>
                            <div class="news-content">
                                <span class="news-category">Esportes</span>
                                <h3 class="news-title">Campeonato Mundial Bate Recordes de Audiência</h3>
                                <p class="news-excerpt">
                                    O evento esportivo mais aguardado do ano superou todas as expectativas 
                                    de público e engajamento nas redes sociais...
                                </p>
                                <div class="news-meta">
                                    <span><i class="fas fa-user"></i> Ana Silva</span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('j M Y', strtotime('-1 day')); ?></span>
                                </div>
                            </div>
                        </article>
                        
                        <article class="news-card">
                            <div class="news-image">
                                <i class="fas fa-palette"></i>
                            </div>
                            <div class="news-content">
                                <span class="news-category">Cultura</span>
                                <h3 class="news-title">Festival de Arte Digital Atrai Milhares de Visitantes</h3>
                                <p class="news-excerpt">
                                    A exposição inovadora combina arte tradicional com tecnologias emergentes, 
                                    criando uma experiência única para os visitantes...
                                </p>
                                <div class="news-meta">
                                    <span><i class="fas fa-user"></i> Carlos Santos</span>
                                    <span><i class="fas fa-calendar"></i> <?php echo date('j M Y', strtotime('-2 days')); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
            </section>

            <?php if ( ! empty( $features ) && is_array( $features ) ) : ?>
                <section class="demo-section" id="advanced-features">
                    <h2 class="demo-title">
                        <i class="fas fa-tools"></i>
                        Recursos Personalizados (Advanced Fields)
                    </h2>
                    <p class="demo-description">Recursos configurados via metabox Advanced Content Fields</p>
                    <div class="feature-grid">
                        <?php foreach ( $features as $feature ) : ?>
                            <div class="feature-card">
                                <?php if ( ! empty( $feature['icon'] ) ) : ?>
                                    <div class="feature-icon"><i class="<?php echo esc_attr( $feature['icon'] ); ?>"></i></div>
                                <?php endif; ?>
                                <?php if ( ! empty( $feature['title'] ) ) : ?>
                                    <h3><?php echo esc_html( $feature['title'] ); ?></h3>
                                <?php endif; ?>
                                <?php if ( ! empty( $feature['description'] ) ) : ?>
                                    <p><?php echo esc_html( $feature['description'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $feature['link'] ) ) : ?>
                                    <p><a href="<?php echo esc_url( $feature['link'] ); ?>" class="btn btn-sm">Saiba mais</a></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ( ! empty( $testimonials ) && is_array( $testimonials ) ) : ?>
                <section class="demo-section" id="advanced-testimonials">
                    <h2 class="demo-title">
                        <i class="fas fa-comments"></i>
                        Depoimentos
                    </h2>
                    <p class="demo-description">Feedbacks adicionados via metabox Advanced Content Fields</p>
                    <div class="testimonials-grid">
                        <?php foreach ( $testimonials as $testimonial ) : ?>
                            <div class="testimonial-card">
                                <?php if ( ! empty( $testimonial['avatar'] ) ) : ?>
                                    <div class="testimonial-avatar">
                                        <img src="<?php echo esc_url( $testimonial['avatar'] ); ?>" alt="<?php echo esc_attr( $testimonial['author'] ); ?>" />
                                    </div>
                                <?php endif; ?>
                                <?php if ( ! empty( $testimonial['quote'] ) ) : ?>
                                    <blockquote class="testimonial-quote">“<?php echo esc_html( $testimonial['quote'] ); ?>”</blockquote>
                                <?php endif; ?>
                                <div class="testimonial-author-info">
                                    <?php if ( ! empty( $testimonial['author'] ) ) : ?>
                                        <h4 class="testimonial-author"><?php echo esc_html( $testimonial['author'] ); ?></h4>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $testimonial['position'] ) ) : ?>
                                        <p class="testimonial-position"><?php echo esc_html( $testimonial['position'] ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ( ! empty( $layout_sections ) && is_array( $layout_sections ) ) : ?>
                <section class="demo-section" id="advanced-layout">
                    <h2 class="demo-title">
                        <i class="fas fa-layer-group"></i>
                        Layout Personalizado
                    </h2>
                    <p class="demo-description">Seções dinâmicas criadas no construtor de layout</p>
                    <div class="advanced-layout-output">
                        <?php foreach ( $layout_sections as $section ) : ?>
                            <?php $type = isset( $section['type'] ) ? $section['type'] : ''; ?>
                            <?php if ( 'text' === $type && ! empty( $section['content'] ) ) : ?>
                                <div class="layout-text">
                                    <?php echo wp_kses_post( $section['content'] ); ?>
                                </div>
                            <?php elseif ( 'image' === $type && ! empty( $section['image'] ) ) : ?>
                                <figure class="layout-image align-<?php echo esc_attr( isset( $section['alignment'] ) ? $section['alignment'] : 'center' ); ?>">
                                    <img src="<?php echo esc_url( $section['image'] ); ?>" alt="" />
                                    <?php if ( ! empty( $section['caption'] ) ) : ?>
                                        <figcaption><?php echo esc_html( $section['caption'] ); ?></figcaption>
                                    <?php endif; ?>
                                </figure>
                            <?php elseif ( 'columns' === $type && ! empty( $section['columns'] ) ) : ?>
                                <?php $cols = max( 2, min( 4, intval( $section['columns'] ) ) ); ?>
                                <div class="layout-columns" style="grid-template-columns: repeat(<?php echo $cols; ?>, 1fr);">
                                    <?php for ( $i = 1; $i <= $cols; $i++ ) : ?>
                                        <div class="layout-column">
                                            <?php echo isset( $section['column_' . $i] ) ? wp_kses_post( $section['column_' . $i] ) : ''; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php elseif ( 'spacer' === $type ) : ?>
                                <div class="layout-spacer" style="height: <?php echo isset( $section['height'] ) ? intval( $section['height'] ) : 50; ?>px;"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ( ! empty( $gallery_images ) && is_array( $gallery_images ) ) : ?>
                <section class="demo-section" id="advanced-gallery">
                    <h2 class="demo-title">
                        <i class="fas fa-images"></i>
                        Galeria de Mídia
                    </h2>
                    <p class="demo-description">Imagens gerenciadas via metabox Media Gallery (tipo: <?php echo esc_html( $gallery_type ?: 'grid' ); ?>)</p>
                    <?php $cols = ! empty( $gallery_columns ) ? intval( $gallery_columns ) : 3; ?>
                    <div class="demo-gallery grid-cols-<?php echo $cols; ?> gallery-type-<?php echo esc_attr( $gallery_type ?: 'grid' ); ?>" style="grid-template-columns: repeat(<?php echo $cols; ?>, 1fr);">
                        <?php foreach ( $gallery_images as $image ) : ?>
                            <?php $img_url = wp_get_attachment_image_url( intval( $image['id'] ), 'large' ); ?>
                            <figure class="demo-gallery-item">
                                <img src="<?php echo esc_url( $img_url ); ?>" alt="" />
                                <?php if ( ! empty( $image['caption'] ) ) : ?>
                                    <figcaption><?php echo esc_html( $image['caption'] ); ?></figcaption>
                                <?php endif; ?>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Theme Features -->
            <section class="demo-section">
                <h2 class="demo-title">
                    <i class="fas fa-cogs"></i>
                    Recursos do Tema
                </h2>
                <p class="demo-description">Principais funcionalidades e características técnicas</p>
                
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Design Responsivo</h3>
                        <p>Layout que se adapta perfeitamente a todos os dispositivos, desde smartphones até desktops.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>SEO Otimizado</h3>
                        <p>Estrutura otimizada para mecanismos de busca com marcação semântica e meta tags.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>AMP Ready</h3>
                        <p>Suporte completo ao AMP (Accelerated Mobile Pages) para carregamento ultra-rápido.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-universal-access"></i>
                        </div>
                        <h3>Acessibilidade</h3>
                        <p>Desenvolvido seguindo as diretrizes WCAG para máxima acessibilidade.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3>Customizável</h3>
                        <p>Múltiplas opções de personalização através do WordPress Customizer.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3>Performance</h3>
                        <p>Otimizado para velocidade com carregamento assíncrono e cache inteligente.</p>
                    </div>
                </div>
            </section>

            <!-- Content from WordPress Editor -->
            <?php if (get_the_content()) : ?>
                <section class="demo-section">
                    <h2 class="demo-title">
                        <i class="fas fa-edit"></i>
                        Conteúdo Personalizado
                    </h2>
                    <div class="demo-content">
                        <?php the_content(); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Pagination -->
            <nav class="pagination" aria-label="Navegação de páginas">
                <a href="#" aria-label="Página anterior">&laquo; Anterior</a>
                <span class="current" aria-current="page">1</span>
                <a href="#" aria-label="Página 2">2</a>
                <a href="#" aria-label="Página 3">3</a>
                <a href="#" aria-label="Próxima página">Próxima &raquo;</a>
            </nav>

        <?php endwhile; ?>
        
</div>
</div>

<?php // JavaScript da página demo foi movido para assets/js/page-demo.js e será enfileirado via inc/page-templates.php ?>

<?php if ( ! empty( $custom_css ) ) : ?>
    <style id="nosfirnews-custom-css">
        <?php echo esc_html( $custom_css ); ?>
    </style>
<?php endif; ?>

<?php if ( ! empty( $custom_js ) ) : ?>
    <script id="nosfirnews-custom-js">
        <?php echo esc_js( $custom_js ); ?>
    </script>
<?php endif; ?>

<?php get_footer(); ?>