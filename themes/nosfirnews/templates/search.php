<?php
/**
 * Template Name: Search Results
 * Description: Template para resultados de busca com filtros avançados
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

get_header(); 

// Obter termo de busca
$search_query = get_search_query();
$search_results_count = $wp_query->found_posts;
$current_page = max(1, get_query_var('paged'));
$total_pages = $wp_query->max_num_pages;

// Obter filtros ativos
$selected_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$selected_date = isset($_GET['date_filter']) ? sanitize_text_field($_GET['date_filter']) : '';
$selected_author = isset($_GET['author']) ? intval($_GET['author']) : 0;
$sort_by = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'relevance';
?>

<style>
/* Estilos para o template search.php */
.search-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: var(--bg-color, #ffffff);
    color: var(--text-color, #333333);
}

.search-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 30px;
    border-radius: 20px;
    margin-bottom: 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.search-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="search-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23search-pattern)"/></svg>');
    pointer-events: none;
}

.search-title {
    font-size: 2.2rem;
    margin: 0 0 15px 0;
    font-weight: 700;
    position: relative;
    z-index: 2;
}

.search-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 25px;
    position: relative;
    z-index: 2;
}

.search-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 25px;
    position: relative;
    z-index: 2;
}

.stat-item {
    text-align: center;
    background: rgba(255,255,255,0.1);
    padding: 15px 20px;
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.stat-number {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 5px;
}

.search-form-container {
    position: relative;
    z-index: 2;
    max-width: 500px;
    margin: 0 auto;
}

.search-form {
    display: flex;
    background: white;
    border-radius: 50px;
    padding: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.search-input {
    flex: 1;
    border: none;
    padding: 12px 20px;
    font-size: 1rem;
    border-radius: 50px;
    outline: none;
    color: #333;
}

.search-input::placeholder {
    color: #999;
}

.search-submit {
    background: #667eea;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 50px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-submit:hover {
    background: #5a6fd8;
    transform: scale(1.05);
}

.search-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

.search-sidebar {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
    height: fit-content;
    position: sticky;
    top: 20px;
}

.filter-section {
    margin-bottom: 30px;
}

.filter-section:last-child {
    margin-bottom: 0;
}

.filter-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 8px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.filter-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-item:hover {
    background: #f8f9fa;
}

.filter-item input[type="checkbox"],
.filter-item input[type="radio"] {
    margin: 0;
    accent-color: #667eea;
}

.filter-item label {
    flex: 1;
    cursor: pointer;
    font-size: 0.95rem;
    color: #666;
}

.filter-count {
    background: #f0f0f0;
    color: #666;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.filter-select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #f0f0f0;
    border-radius: 8px;
    font-size: 0.95rem;
    background: white;
    color: #333;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #667eea;
}

.clear-filters {
    background: #f8f9fa;
    color: #666;
    border: 2px solid #f0f0f0;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 20px;
}

.clear-filters:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.search-main {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    border: 1px solid #f0f0f0;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.results-info {
    flex: 1;
}

.results-title {
    font-size: 1.8rem;
    margin: 0 0 8px 0;
    color: #333;
}

.results-meta {
    color: #666;
    font-size: 0.95rem;
}

.results-controls {
    display: flex;
    gap: 15px;
    align-items: center;
}

.sort-select {
    padding: 8px 12px;
    border: 2px solid #f0f0f0;
    border-radius: 8px;
    font-size: 0.9rem;
    background: white;
    color: #333;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.sort-select:focus {
    outline: none;
    border-color: #667eea;
}

.view-toggle {
    display: flex;
    gap: 5px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 5px;
}

.view-btn {
    padding: 8px 12px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.3s ease;
    color: #666;
}

.view-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.search-results {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.search-results.list-view {
    grid-template-columns: 1fr;
}

.result-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.result-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.list-view .result-card {
    display: flex;
    align-items: center;
}

.result-thumbnail {
    position: relative;
    overflow: hidden;
    height: 180px;
}

.list-view .result-thumbnail {
    width: 200px;
    height: 120px;
    flex-shrink: 0;
}

.result-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.result-card:hover .result-thumbnail img {
    transform: scale(1.05);
}

.result-category {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #667eea;
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    z-index: 2;
}

.result-content {
    padding: 20px;
}

.list-view .result-content {
    flex: 1;
    padding: 15px 20px;
}

.result-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    font-size: 0.85rem;
    color: #888;
}

.result-title {
    margin: 0 0 12px 0;
    font-size: 1.2rem;
    line-height: 1.4;
    font-weight: 600;
}

.list-view .result-title {
    font-size: 1.1rem;
}

.result-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.result-title a:hover {
    color: #667eea;
}

.search-highlight {
    background: #fff3cd;
    padding: 2px 4px;
    border-radius: 3px;
    font-weight: 600;
}

.result-excerpt {
    color: #666;
    line-height: 1.6;
    font-size: 0.95rem;
    margin-bottom: 15px;
}

.list-view .result-excerpt {
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.result-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.read-more {
    background: #667eea;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.read-more:hover {
    background: #5a6fd8;
    transform: translateX(3px);
}

.result-tags {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.result-tag {
    background: #f8f9fa;
    color: #666;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.result-tag:hover {
    background: #667eea;
    color: white;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 15px;
    margin: 40px 0;
}

.no-results h3 {
    font-size: 1.8rem;
    margin-bottom: 15px;
    color: #333;
}

.no-results p {
    color: #666;
    font-size: 1rem;
    margin-bottom: 25px;
}

.search-suggestions {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-width: 400px;
    margin: 0 auto;
}

.suggestion-item {
    background: white;
    padding: 15px 20px;
    border-radius: 10px;
    border: 2px solid #f0f0f0;
    transition: all 0.3s ease;
}

.suggestion-item:hover {
    border-color: #667eea;
    transform: translateY(-2px);
}

.suggestion-title {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.suggestion-desc {
    font-size: 0.9rem;
    color: #666;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin: 40px 0;
}

.pagination {
    display: flex;
    gap: 8px;
    align-items: center;
}

.pagination a,
.pagination span {
    padding: 10px 15px;
    border: 2px solid #ddd;
    color: #666;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination a:hover {
    border-color: #667eea;
    background: #667eea;
    color: white;
}

.pagination .current {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

/* Responsividade */
@media (max-width: 768px) {
    .search-page-container {
        padding: 15px;
    }
    
    .search-header {
        padding: 30px 20px;
    }
    
    .search-title {
        font-size: 1.8rem;
    }
    
    .search-stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: left;
    }
    
    .search-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .search-sidebar {
        position: static;
        order: 2;
    }
    
    .search-main {
        padding: 20px;
        order: 1;
    }
    
    .results-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .results-controls {
        justify-content: space-between;
    }
    
    .search-results {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .list-view .result-card {
        flex-direction: column;
    }
    
    .list-view .result-thumbnail {
        width: 100%;
        height: 180px;
    }
}

@media (max-width: 480px) {
    .search-header {
        padding: 25px 15px;
    }
    
    .search-title {
        font-size: 1.6rem;
    }
    
    .search-form {
        flex-direction: column;
        gap: 10px;
        padding: 15px;
        border-radius: 15px;
    }
    
    .search-input,
    .search-submit {
        border-radius: 10px;
    }
    
    .filter-section {
        margin-bottom: 25px;
    }
    
    .results-controls {
        flex-direction: column;
        gap: 10px;
    }
    
    .view-toggle {
        width: 100%;
        justify-content: center;
    }
}

/* Modo Escuro */
@media (prefers-color-scheme: dark) {
    .search-page-container {
        background: #1a1a1a;
        color: #e0e0e0;
    }
    
    .search-sidebar,
    .search-main {
        background: #2d2d2d;
        border-color: #404040;
    }
    
    .result-card {
        background: #2d2d2d;
        border-color: #404040;
    }
    
    .result-title a {
        color: #e0e0e0;
    }
    
    .result-excerpt {
        color: #b0b0b0;
    }
    
    .result-meta {
        color: #888;
    }
    
    .result-footer {
        border-color: #404040;
    }
    
    .filter-item:hover {
        background: #404040;
    }
    
    .no-results {
        background: #2d2d2d;
    }
    
    .suggestion-item {
        background: #404040;
        border-color: #555;
    }
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.result-card {
    animation: fadeInUp 0.6s ease forwards;
}

.result-card:nth-child(2) { animation-delay: 0.1s; }
.result-card:nth-child(3) { animation-delay: 0.2s; }
.result-card:nth-child(4) { animation-delay: 0.3s; }
.result-card:nth-child(5) { animation-delay: 0.4s; }
.result-card:nth-child(6) { animation-delay: 0.5s; }

/* Acessibilidade */
@media (prefers-reduced-motion: reduce) {
    .result-card,
    .result-thumbnail img,
    .read-more,
    .filter-item {
        animation: none;
        transition: none;
    }
    
    .result-card:hover {
        transform: none;
    }
}

/* Impressão */
@media print {
    .search-sidebar,
    .results-controls,
    .pagination-wrapper,
    .read-more {
        display: none;
    }
    
    .search-content {
        grid-template-columns: 1fr;
    }
    
    .result-card {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
        margin-bottom: 20px;
    }
}
</style>

<div class="search-page-container">
    <!-- Cabeçalho da Busca -->
    <section class="search-header">
        <h1 class="search-title">
            <?php if ($search_query) : ?>
                Resultados para "<?php echo esc_html($search_query); ?>"
            <?php else : ?>
                Buscar no Site
            <?php endif; ?>
        </h1>
        
        <?php if ($search_query) : ?>
            <div class="search-subtitle">
                Encontramos <?php echo $search_results_count; ?> resultado<?php echo $search_results_count != 1 ? 's' : ''; ?> para sua busca
            </div>
            
            <div class="search-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $search_results_count; ?></span>
                    <span class="stat-label">Resultados</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $current_page; ?></span>
                    <span class="stat-label">Página</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $total_pages; ?></span>
                    <span class="stat-label">Total</span>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Formulário de Busca -->
        <div class="search-form-container">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="search" 
                       class="search-input" 
                       placeholder="<?php esc_attr_e( 'Digite sua busca...', 'nosfirnews' ); ?>" 
                       value="<?php echo esc_attr($search_query); ?>" 
                       name="s" 
                       required>
                <button type="submit" class="search-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <?php esc_html_e( 'Buscar', 'nosfirnews' ); ?>
                </button>
            </form>
        </div>
    </section>

    <!-- Conteúdo Principal -->
    <div class="search-content">
        <!-- Sidebar com Filtros -->
        <aside class="search-sidebar">
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>" id="search-filters">
                <input type="hidden" name="s" value="<?php echo esc_attr($search_query); ?>">
                
                <!-- Filtro por Categoria -->
                <div class="filter-section">
                    <h3 class="filter-title"><?php esc_html_e( 'Categorias', 'nosfirnews' ); ?></h3>
                    <div class="filter-group">
                        <?php
                        $categories = get_categories(array(
                            'hide_empty' => true,
                            'number' => 8
                        ));
                        
                        foreach ($categories as $category) :
                            $post_count = $category->count;
                            $checked = ($selected_category == $category->slug) ? 'checked' : '';
                        ?>
                            <div class="filter-item">
                                <input type="radio" 
                                       id="cat-<?php echo $category->term_id; ?>" 
                                       name="category" 
                                       value="<?php echo $category->slug; ?>" 
                                       <?php echo $checked; ?>
                                       onchange="this.form.submit()">
                                <label for="cat-<?php echo $category->term_id; ?>">
                                    <?php echo $category->name; ?>
                                </label>
                                <span class="filter-count"><?php echo $post_count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Filtro por Data -->
                <div class="filter-section">
                    <h3 class="filter-title"><?php esc_html_e( 'Período', 'nosfirnews' ); ?></h3>
                    <select name="date_filter" class="filter-select" onchange="this.form.submit()">
                        <option value=""><?php esc_html_e( 'Qualquer período', 'nosfirnews' ); ?></option>
                        <option value="today" <?php selected($selected_date, 'today'); ?>><?php esc_html_e( 'Hoje', 'nosfirnews' ); ?></option>
                        <option value="week" <?php selected($selected_date, 'week'); ?>><?php esc_html_e( 'Esta semana', 'nosfirnews' ); ?></option>
                        <option value="month" <?php selected($selected_date, 'month'); ?>><?php esc_html_e( 'Este mês', 'nosfirnews' ); ?></option>
                        <option value="year" <?php selected($selected_date, 'year'); ?>><?php esc_html_e( 'Este ano', 'nosfirnews' ); ?></option>
                    </select>
                </div>

                <!-- Filtro por Autor -->
                <div class="filter-section">
                    <h3 class="filter-title"><?php esc_html_e( 'Autor', 'nosfirnews' ); ?></h3>
                    <select name="author" class="filter-select" onchange="this.form.submit()">
                        <option value=""><?php esc_html_e( 'Qualquer autor', 'nosfirnews' ); ?></option>
                        <?php
                        $authors = get_users(array(
                            'who' => 'authors',
                            'has_published_posts' => true,
                            'fields' => array('ID', 'display_name'),
                            'number' => 10
                        ));
                        
                        foreach ($authors as $author) :
                            $selected_attr = ($selected_author == $author->ID) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $author->ID; ?>" <?php echo $selected_attr; ?>>
                                <?php echo $author->display_name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botão Limpar Filtros -->
                <button type="button" class="clear-filters" onclick="clearFilters()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                    <?php esc_html_e( 'Limpar Filtros', 'nosfirnews' ); ?>
                </button>
            </form>
        </aside>

        <!-- Área Principal dos Resultados -->
        <main class="search-main">
            <?php if ($search_query && have_posts()) : ?>
                <!-- Cabeçalho dos Resultados -->
                <div class="results-header">
                    <div class="results-info">
                        <h2 class="results-title"><?php esc_html_e( 'Resultados da Busca', 'nosfirnews' ); ?></h2>
                        <div class="results-meta">
                            <?php printf( esc_html__( 'Mostrando %1$d-%2$d de %3$d resultados', 'nosfirnews' ), (($current_page - 1) * get_option('posts_per_page')) + 1, min($current_page * get_option('posts_per_page'), $search_results_count), $search_results_count ); ?>
                        </div>
                    </div>
                    
                    <div class="results-controls">
                        <!-- Ordenação -->
                        <select name="sort" class="sort-select" onchange="updateSort(this.value)">
                            <option value="relevance" <?php selected($sort_by, 'relevance'); ?>>Relevância</option>
                            <option value="date" <?php selected($sort_by, 'date'); ?>>Mais recentes</option>
                            <option value="title" <?php selected($sort_by, 'title'); ?>>Título A-Z</option>
                            <option value="popularity" <?php selected($sort_by, 'popularity'); ?>>Popularidade</option>
                        </select>
                        
                        <!-- Toggle de Visualização -->
                        <div class="view-toggle">
                            <button class="view-btn active" data-view="grid" title="Visualização em Grade">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                                </svg>
                            </button>
                            <button class="view-btn" data-view="list" title="Visualização em Lista">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de Resultados -->
                <div id="search-results" class="search-results">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="result-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="result-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                                    </a>
                                    
                                    <?php 
                                    $categories = get_the_category();
                                    if (!empty($categories)) :
                                        $primary_category = $categories[0];
                                    ?>
                                        <a href="<?php echo get_category_link($primary_category->term_id); ?>" 
                                           class="result-category">
                                            <?php echo $primary_category->name; ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="result-content">
                                <div class="result-meta">
                                    <time datetime="<?php echo get_the_date('c'); ?>">
                                        <?php echo get_the_date(); ?>
                                    </time>
                                    <span class="author">
                                        por <?php the_author(); ?>
                                    </span>
                                    <span class="reading-time">
                                        <?php echo ceil(str_word_count(get_the_content()) / 200); ?> min
                                    </span>
                                </div>
                                
                                <h3 class="result-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php 
                                        $title = get_the_title();
                                        if ($search_query) {
                                            $title = str_ireplace($search_query, '<span class="search-highlight">' . $search_query . '</span>', $title);
                                        }
                                        echo $title;
                                        ?>
                                    </a>
                                </h3>
                                
                                <div class="result-excerpt">
                                    <?php 
                                    $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 25, '...');
                                    if ($search_query) {
                                        $excerpt = str_ireplace($search_query, '<span class="search-highlight">' . $search_query . '</span>', $excerpt);
                                    }
                                    echo $excerpt;
                                    ?>
                                </div>
                                
                                <div class="result-footer">
                                    <a href="<?php the_permalink(); ?>" class="read-more">
                                        Ler mais
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                                        </svg>
                                    </a>
                                    
                                    <?php if (has_tag()) : ?>
                                        <div class="result-tags">
                                            <?php 
                                            $tags = get_the_tags();
                                            $tag_count = 0;
                                            foreach ($tags as $tag) :
                                                if ($tag_count >= 2) break;
                                            ?>
                                                <a href="<?php echo get_tag_link($tag->term_id); ?>" 
                                                   class="result-tag">#<?php echo $tag->name; ?></a>
                                            <?php 
                                                $tag_count++;
                                            endforeach; 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Paginação -->
                <div class="pagination-wrapper">
                    <?php
                    echo paginate_links(array(
                        'prev_text' => '&laquo; Anterior',
                        'next_text' => 'Próximo &raquo;',
                        'mid_size' => 2,
                        'end_size' => 1,
                        'type' => 'list',
                        'class' => 'pagination'
                    ));
                    ?>
                </div>

            <?php elseif ($search_query) : ?>
                <!-- Nenhum Resultado Encontrado -->
                <div class="no-results">
                    <h3><?php esc_html_e( 'Nenhum resultado encontrado', 'nosfirnews' ); ?></h3>
                    <p><?php printf( esc_html__( 'Não encontramos nenhum conteúdo para "%s". Tente refinar sua busca ou explore nossas sugestões abaixo.', 'nosfirnews' ), esc_html($search_query) ); ?></p>
                    
                    <div class="search-suggestions">
                        <div class="suggestion-item">
                            <div class="suggestion-title"><?php esc_html_e( 'Verifique a ortografia', 'nosfirnews' ); ?></div>
                            <div class="suggestion-desc"><?php esc_html_e( 'Certifique-se de que todas as palavras estão escritas corretamente', 'nosfirnews' ); ?></div>
                        </div>
                        
                        <div class="suggestion-item">
                            <div class="suggestion-title"><?php esc_html_e( 'Use palavras-chave diferentes', 'nosfirnews' ); ?></div>
                            <div class="suggestion-desc"><?php esc_html_e( 'Tente usar sinônimos ou termos relacionados', 'nosfirnews' ); ?></div>
                        </div>
                        
                        <div class="suggestion-item">
                            <div class="suggestion-title"><?php esc_html_e( 'Seja mais específico', 'nosfirnews' ); ?></div>
                            <div class="suggestion-desc"><?php esc_html_e( 'Adicione mais detalhes para refinar sua busca', 'nosfirnews' ); ?></div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- Página de Busca Inicial -->
                <div class="no-results">
                    <h3><?php esc_html_e( 'Busque por conteúdo', 'nosfirnews' ); ?></h3>
                    <p><?php esc_html_e( 'Use o formulário acima para encontrar artigos, notícias e conteúdos do nosso site.', 'nosfirnews' ); ?></p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Controles de visualização
    const viewButtons = document.querySelectorAll('.view-btn');
    const resultsContainer = document.getElementById('search-results');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            viewButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Change container class
            const view = this.dataset.view;
            if (view === 'list') {
                resultsContainer.classList.add('list-view');
            } else {
                resultsContainer.classList.remove('list-view');
            }
            
            // Save preference
            localStorage.setItem('search-view-preference', view);
        });
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('search-view-preference');
    if (savedView) {
        const targetButton = document.querySelector(`[data-view="${savedView}"]`);
        if (targetButton) {
            targetButton.click();
        }
    }
    
    // Auto-submit form on filter change
    const filterInputs = document.querySelectorAll('#search-filters input, #search-filters select');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.type !== 'radio' || this.checked) {
                this.form.submit();
            }
        });
    });
    
    // Smooth scroll para links internos
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
    
    // Lazy loading para imagens
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// Função para limpar filtros
function clearFilters() {
    const form = document.getElementById('search-filters');
    const inputs = form.querySelectorAll('input[type="radio"], input[type="checkbox"]');
    const selects = form.querySelectorAll('select');
    
    inputs.forEach(input => {
        input.checked = false;
    });
    
    selects.forEach(select => {
        select.selectedIndex = 0;
    });
    
    form.submit();
}

// Função para atualizar ordenação
function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}

// Destacar termos de busca
function highlightSearchTerms() {
    const searchTerm = '<?php echo esc_js($search_query); ?>';
    if (!searchTerm) return;
    
    const content = document.querySelectorAll('.result-title, .result-excerpt');
    content.forEach(element => {
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        element.innerHTML = element.innerHTML.replace(regex, '<span class="search-highlight">$1</span>');
    });
}

// Executar highlight após carregamento
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', highlightSearchTerms);
} else {
    highlightSearchTerms();
}
</script>

<?php get_footer(); ?>