<?php
/**
 * The template for displaying archive pages
 *
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); ?>

<div class="archive-page">
    <div class="container">
        
        <!-- Archive Header -->
        <div class="archive-header">
            <div class="row">
                <div class="col-12">
                    <div class="archive-title-section">
                        <?php if ( have_posts() ) : ?>
                            <h1 class="archive-title">
                                <?php
                                if ( is_category() ) {
                                    printf( esc_html__( 'Categoria: %s', 'nosfirnews' ), '<span>' . single_cat_title( '', false ) . '</span>' );
                                } elseif ( is_tag() ) {
                                    printf( esc_html__( 'Tag: %s', 'nosfirnews' ), '<span>' . single_tag_title( '', false ) . '</span>' );
                                } elseif ( is_author() ) {
                                    printf( esc_html__( 'Autor: %s', 'nosfirnews' ), '<span>' . get_the_author() . '</span>' );
                                } elseif ( is_year() ) {
                                    printf( esc_html__( 'Ano: %s', 'nosfirnews' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'nosfirnews' ) ) . '</span>' );
                                } elseif ( is_month() ) {
                                    printf( esc_html__( 'Mês: %s', 'nosfirnews' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'nosfirnews' ) ) . '</span>' );
                                } elseif ( is_day() ) {
                                    printf( esc_html__( 'Dia: %s', 'nosfirnews' ), '<span>' . get_the_date( _x( 'F j, Y', 'daily archives date format', 'nosfirnews' ) ) . '</span>' );
                                } else {
                                    esc_html_e( 'Arquivo', 'nosfirnews' );
                                }
                                ?>
                            </h1>
                            
                            <?php
                            // Archive description
                            $archive_description = get_the_archive_description();
                            if ( $archive_description ) : ?>
                                <div class="archive-description">
                                    <?php echo wp_kses_post( $archive_description ); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Post count -->
                            <div class="archive-meta">
                                <span class="post-count">
                                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                                    <?php
                                    global $wp_query;
                                    $total_posts = $wp_query->found_posts;
                                    printf( 
                                        esc_html( _n( '%s post encontrado', '%s posts encontrados', $total_posts, 'nosfirnews' ) ), 
                                        number_format_i18n( $total_posts ) 
                                    );
                                    ?>
                                </span>
                            </div>
                            
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Archive Content -->
        <div class="archive-content">
            <div class="row">
                
                <!-- Main Content -->
                <div class="col-lg-8 col-md-12">
                    <div class="posts-container">
                        
                        <!-- Filter and Sort Options -->
                        <div class="archive-filters">
                            <div class="filter-options">
                                <div class="sort-options">
                                    <label for="sort-posts"><?php esc_html_e( 'Ordenar por:', 'nosfirnews' ); ?></label>
                                    <select id="sort-posts" class="sort-select">
                                        <option value="date-desc" <?php selected( get_query_var( 'orderby' ), 'date' ); ?>>
                                            <?php esc_html_e( 'Mais recentes', 'nosfirnews' ); ?>
                                        </option>
                                        <option value="date-asc">
                                            <?php esc_html_e( 'Mais antigos', 'nosfirnews' ); ?>
                                        </option>
                                        <option value="title-asc">
                                            <?php esc_html_e( 'Título A-Z', 'nosfirnews' ); ?>
                                        </option>
                                        <option value="title-desc">
                                            <?php esc_html_e( 'Título Z-A', 'nosfirnews' ); ?>
                                        </option>
                                        <option value="comment-count">
                                            <?php esc_html_e( 'Mais comentados', 'nosfirnews' ); ?>
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="view-options">
                                    <button class="view-toggle active" data-view="grid" aria-label="<?php esc_attr_e( 'Visualização em grade', 'nosfirnews' ); ?>">
                                        <i class="fas fa-th" aria-hidden="true"></i>
                                    </button>
                                    <button class="view-toggle" data-view="list" aria-label="<?php esc_attr_e( 'Visualização em lista', 'nosfirnews' ); ?>">
                                        <i class="fas fa-list" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Posts Grid -->
                        <?php if ( have_posts() ) : ?>
                            <div class="posts-grid" id="posts-container">
                                <?php while ( have_posts() ) : the_post(); ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-item' ); ?>>
                                        
                                        <!-- Post Thumbnail -->
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="post-thumbnail">
                                                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                                    <?php the_post_thumbnail( 'medium_large', array( 'class' => 'img-fluid' ) ); ?>
                                                </a>
                                                
                                                <!-- Post Format Icon -->
                                                <?php if ( get_post_format() ) : ?>
                                                    <div class="post-format-icon">
                                                        <?php
                                                        $format = get_post_format();
                                                        switch ( $format ) {
                                                            case 'video':
                                                                echo '<i class="fas fa-play" aria-hidden="true"></i>';
                                                                break;
                                                            case 'audio':
                                                                echo '<i class="fas fa-music" aria-hidden="true"></i>';
                                                                break;
                                                            case 'gallery':
                                                                echo '<i class="fas fa-images" aria-hidden="true"></i>';
                                                                break;
                                                            case 'quote':
                                                                echo '<i class="fas fa-quote-left" aria-hidden="true"></i>';
                                                                break;
                                                            default:
                                                                echo '<i class="fas fa-file-alt" aria-hidden="true"></i>';
                                                        }
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Post Content -->
                                        <div class="post-content">
                                            
                                            <!-- Post Meta -->
                                            <div class="post-meta">
                                                <span class="post-category">
                                                    <?php
                                                    $categories = get_the_category();
                                                    if ( $categories ) {
                                                        echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '" class="category-link">';
                                                        echo esc_html( $categories[0]->name );
                                                        echo '</a>';
                                                    }
                                                    ?>
                                                </span>
                                                <span class="post-date">
                                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                                    <time datetime="<?php echo get_the_date( 'c' ); ?>">
                                                        <?php echo get_the_date(); ?>
                                                    </time>
                                                </span>
                                            </div>
                                            
                                            <!-- Post Title -->
                                            <h2 class="post-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            
                                            <!-- Post Excerpt -->
                                            <div class="post-excerpt">
                                                <?php echo wp_trim_words( get_the_excerpt(), 25, '...' ); ?>
                                            </div>
                                            
                                            <!-- Post Footer -->
                                            <div class="post-footer">
                                                <div class="post-stats">
                                                    <span class="post-author">
                                                        <i class="fas fa-user" aria-hidden="true"></i>
                                                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                                                            <?php the_author(); ?>
                                                        </a>
                                                    </span>
                                                    
                                                    <?php if ( comments_open() || get_comments_number() ) : ?>
                                                        <span class="post-comments">
                                                            <i class="fas fa-comments" aria-hidden="true"></i>
                                                            <a href="<?php comments_link(); ?>">
                                                                <?php
                                                                printf( 
                                                                    esc_html( _n( '%s comentário', '%s comentários', get_comments_number(), 'nosfirnews' ) ), 
                                                                    number_format_i18n( get_comments_number() ) 
                                                                );
                                                                ?>
                                                            </a>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                                    <?php esc_html_e( 'Ler mais', 'nosfirnews' ); ?>
                                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                            
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="archive-pagination">
                                <?php
                                the_posts_pagination( array(
                                    'mid_size'  => 2,
                                    'prev_text' => '<i class="fas fa-chevron-left" aria-hidden="true"></i> ' . esc_html__( 'Anterior', 'nosfirnews' ),
                                    'next_text' => esc_html__( 'Próximo', 'nosfirnews' ) . ' <i class="fas fa-chevron-right" aria-hidden="true"></i>',
                                    'screen_reader_text' => esc_html__( 'Navegação de posts', 'nosfirnews' ),
                                ) );
                                ?>
                            </div>
                            
                        <?php else : ?>
                            
                            <!-- No Posts Found -->
                            <div class="no-posts-found">
                                <div class="no-posts-icon">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                </div>
                                <h2><?php esc_html_e( 'Nenhum post encontrado', 'nosfirnews' ); ?></h2>
                                <p><?php esc_html_e( 'Não foi possível encontrar posts para esta categoria ou período.', 'nosfirnews' ); ?></p>
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                                    <?php esc_html_e( 'Voltar ao início', 'nosfirnews' ); ?>
                                </a>
                            </div>
                            
                        <?php endif; ?>
                        
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <aside class="archive-sidebar">
                        <?php get_sidebar(); ?>
                    </aside>
                </div>
                
            </div>
        </div>
        
    </div>
</div>

<style>
/* Archive Page Styles */
.archive-page {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 70vh;
}

.archive-header {
    background: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.archive-title-section {
    text-align: center;
}

.archive-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1rem;
}

.archive-title span {
    color: var(--color-primary, #007cba);
}

.archive-description {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 1rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.archive-meta {
    color: #888;
    font-size: 1rem;
}

.post-count i {
    margin-right: 0.5rem;
    color: var(--color-primary, #007cba);
}

/* Archive Filters */
.archive-filters {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.filter-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.sort-options {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sort-options label {
    font-weight: 500;
    color: #333;
}

.sort-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    font-size: 0.9rem;
}

.view-options {
    display: flex;
    gap: 0.5rem;
}

.view-toggle {
    padding: 8px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.view-toggle.active,
.view-toggle:hover {
    background: var(--color-primary, #007cba);
    color: white;
    border-color: var(--color-primary, #007cba);
}

/* Posts Grid */
.posts-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.posts-grid.list-view {
    grid-template-columns: 1fr;
}

.post-item {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.posts-grid.list-view .post-item {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
    height: 250px;
}

.posts-grid.list-view .post-thumbnail {
    width: 200px;
    height: 150px;
    flex-shrink: 0;
}

.post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-thumbnail:hover img {
    transform: scale(1.05);
}

.post-format-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px;
    border-radius: 50%;
    font-size: 0.9rem;
}

.post-content {
    padding: 1.5rem;
}

.posts-grid.list-view .post-content {
    padding: 1rem;
    flex: 1;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.post-category a {
    background: var(--color-primary, #007cba);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
}

.post-date {
    color: #666;
}

.post-date i {
    margin-right: 0.25rem;
}

.post-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.posts-grid.list-view .post-title {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.post-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-title a:hover {
    color: var(--color-primary, #007cba);
}

.post-excerpt {
    color: #555;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.posts-grid.list-view .post-excerpt {
    margin-bottom: 0.5rem;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.post-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.post-stats a {
    color: inherit;
    text-decoration: none;
}

.post-stats a:hover {
    color: var(--color-primary, #007cba);
}

.post-stats i {
    margin-right: 0.25rem;
}

.read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--color-primary, #007cba);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.read-more-btn:hover {
    color: var(--color-primary-dark, #005a87);
}

/* No Posts Found */
.no-posts-found {
    text-align: center;
    padding: 3rem 2rem;
    color: #666;
}

.no-posts-icon {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.no-posts-found h2 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: #333;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: var(--color-primary, #007cba);
    color: white;
}

.btn-primary:hover {
    background: var(--color-primary-dark, #005a87);
    color: white;
    transform: translateY(-2px);
}

/* Pagination */
.archive-pagination {
    margin-top: 2rem;
    text-align: center;
}

.archive-pagination .nav-links {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.archive-pagination a,
.archive-pagination span {
    display: inline-flex;
    align-items: center;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.archive-pagination a:hover,
.archive-pagination .current {
    background: var(--color-primary, #007cba);
    color: white;
    border-color: var(--color-primary, #007cba);
}

/* Sidebar */
.archive-sidebar {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    height: fit-content;
    position: sticky;
    top: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .archive-title {
        font-size: 2rem;
    }
    
    .filter-options {
        flex-direction: column;
        align-items: stretch;
    }
    
    .view-options {
        justify-content: center;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
    }
    
    .posts-grid.list-view .post-item {
        flex-direction: column;
    }
    
    .posts-grid.list-view .post-thumbnail {
        width: 100%;
        height: 200px;
    }
    
    .post-footer {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    
    .archive-pagination .nav-links {
        gap: 0.25rem;
    }
    
    .archive-pagination a,
    .archive-pagination span {
        padding: 8px 12px;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .archive-page {
        padding: 1rem 0;
    }
    
    .archive-header,
    .posts-container,
    .archive-sidebar {
        padding: 1rem;
    }
    
    .post-content {
        padding: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const viewToggles = document.querySelectorAll('.view-toggle');
    const postsContainer = document.getElementById('posts-container');
    
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            viewToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            if (view === 'list') {
                postsContainer.classList.add('list-view');
            } else {
                postsContainer.classList.remove('list-view');
            }
        });
    });
    
    // Sort functionality (basic implementation)
    const sortSelect = document.getElementById('sort-posts');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const url = new URL(window.location);
            
            switch(sortValue) {
                case 'date-desc':
                    url.searchParams.set('orderby', 'date');
                    url.searchParams.set('order', 'DESC');
                    break;
                case 'date-asc':
                    url.searchParams.set('orderby', 'date');
                    url.searchParams.set('order', 'ASC');
                    break;
                case 'title-asc':
                    url.searchParams.set('orderby', 'title');
                    url.searchParams.set('order', 'ASC');
                    break;
                case 'title-desc':
                    url.searchParams.set('orderby', 'title');
                    url.searchParams.set('order', 'DESC');
                    break;
                case 'comment-count':
                    url.searchParams.set('orderby', 'comment_count');
                    url.searchParams.set('order', 'DESC');
                    break;
            }
            
            window.location.href = url.toString();
        });
    }
});
</script>

<?php get_footer(); ?>