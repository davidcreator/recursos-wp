<?php
/**
 * Template Name: Página Demonstrativa NosfirNews
 * Description: Template personalizado para demonstrar todos os recursos do tema NosfirNews
 * 
 * @package NosfirNews
 * @since 2.0.0
 */

get_header(); ?>

<style>
/* Estilos específicos para a página demonstrativa */
.demo-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.demo-section {
    margin: 2rem 0;
    padding: 2rem;
    border: 2px dashed #e0e0e0;
    border-radius: 8px;
    background: #f8f9fa;
}

.demo-title {
    color: #1a73e8;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    text-align: center;
}

.demo-description {
    color: #666;
    text-align: center;
    margin-bottom: 1.5rem;
    font-style: italic;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.feature-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    font-size: 2rem;
    color: #1a73e8;
    margin-bottom: 1rem;
}

.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.news-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.news-image {
    width: 100%;
    height: 200px;
    background: linear-gradient(45deg, #1a73e8, #34a853);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.news-content {
    padding: 1.5rem;
}

.news-category {
    background: #ea4335;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
    margin-bottom: 1rem;
}

.news-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.news-excerpt {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.news-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.9rem;
    color: #888;
}

.amp-badge {
    background: #00c851;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
    margin-left: 1rem;
}

.accessibility-features {
    background: #e8f5e8;
    padding: 1rem;
    border-radius: 5px;
    margin: 1rem 0;
}

.breadcrumbs {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 2rem;
}

.breadcrumbs a {
    color: #1a73e8;
    text-decoration: none;
}

.breadcrumbs a:hover {
    text-decoration: underline;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin: 2rem 0;
}

.pagination a,
.pagination span {
    padding: 0.75rem 1rem;
    border: 1px solid #e0e0e0;
    text-decoration: none;
    color: #333;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.pagination a:hover,
.pagination .current {
    background: #1a73e8;
    color: white;
    border-color: #1a73e8;
}

@media (max-width: 768px) {
    .demo-section {
        padding: 1rem;
    }
    
    .feature-grid,
    .news-grid {
        grid-template-columns: 1fr;
    }
    
    .demo-page-container {
        padding: 0 1rem;
    }
}
</style>

<div class="site-content-wrapper">
    <div class="demo-page-container">
        
        <?php while ( have_posts() ) : the_post(); ?>
            
            <!-- Breadcrumbs -->
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Início</a> &raquo; 
                <span><?php the_title(); ?></span>
            </nav>

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

<script>
// JavaScript para funcionalidades da demo
document.addEventListener('DOMContentLoaded', function() {
    // Animação dos cards
    const cards = document.querySelectorAll('.feature-card, .news-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.style.animation = 'fadeInUp 0.6s ease forwards';
    });
    
    // Adicionar CSS de animação
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .feature-card,
        .news-card {
            opacity: 0;
        }
    `;
    document.head.appendChild(style);
    
    // Smooth scrolling para links internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php get_footer(); ?>