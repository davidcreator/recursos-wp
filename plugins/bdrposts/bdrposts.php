<?php
/**
 * Plugin Name: BDRPosts
 * Plugin URI: https://github.com/seu-usuario/brdposts
 * Description: Plugin flexível para exibir posts, páginas e custom post types com múltiplos layouts usando o editor Gutenberg
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://seusite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: brdposts
 * Domain Path: /languages
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Define constantes do plugin
define('BRDPOSTS_VERSION', '1.0.0');
define('BRDPOSTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BRDPOSTS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Classe principal do plugin BRDPosts
 */
class BRDPosts {
    
    private static $instance = null;
    
    /**
     * Singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Construtor
     */
    private function __construct() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        add_action('enqueue_block_assets', array($this, 'enqueue_block_assets'));
        add_action('init', array($this, 'register_block'));
        
        // Shortcode support
        add_shortcode('brdposts', array($this, 'shortcode_render'));
    }
    
    /**
     * Carrega tradução
     */
    public function load_textdomain() {
        load_plugin_textdomain('brdposts', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Registra o bloco Gutenberg
     */
    public function register_block() {
        register_block_type('brdposts/post-block', array(
            'editor_script' => 'brdposts-block-editor',
            'editor_style' => 'brdposts-block-editor-style',
            'style' => 'brdposts-block-style',
            'render_callback' => array($this, 'render_block'),
            'attributes' => $this->get_block_attributes()
        ));
    }
    
    /**
     * Define os atributos do bloco
     */
    private function get_block_attributes() {
        return array(
            'layout' => array(
                'type' => 'string',
                'default' => 'grid'
            ),
            'subLayout' => array(
                'type' => 'string',
                'default' => 'title-meta'
            ),
            'postType' => array(
                'type' => 'string',
                'default' => 'post'
            ),
            'postsPerPage' => array(
                'type' => 'number',
                'default' => 6
            ),
            'columns' => array(
                'type' => 'number',
                'default' => 3
            ),
            'order' => array(
                'type' => 'string',
                'default' => 'DESC'
            ),
            'orderBy' => array(
                'type' => 'string',
                'default' => 'date'
            ),
            'categories' => array(
                'type' => 'array',
                'default' => array()
            ),
            'tags' => array(
                'type' => 'array',
                'default' => array()
            ),
            'authors' => array(
                'type' => 'array',
                'default' => array()
            ),
            'offset' => array(
                'type' => 'number',
                'default' => 0
            ),
            'includePosts' => array(
                'type' => 'array',
                'default' => array()
            ),
            'excludePosts' => array(
                'type' => 'array',
                'default' => array()
            ),
            'excludeCurrent' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'showImage' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'imageSize' => array(
                'type' => 'string',
                'default' => 'medium'
            ),
            'linkImage' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showTitle' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'linkTitle' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showExcerpt' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'excerptLength' => array(
                'type' => 'number',
                'default' => 20
            ),
            'showMeta' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showDate' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showAuthor' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showCategories' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showTags' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'linkAuthor' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'taxonomy' => array(
                'type' => 'string',
                'default' => ''
            ),
            'taxonomyTerms' => array(
                'type' => 'array',
                'default' => array()
            ),
            'showReadMore' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'readMoreText' => array(
                'type' => 'string',
                'default' => 'Ler Mais'
            ),
            'enablePagination' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'showReadingTime' => array(
                'type' => 'boolean',
                'default' => false
            )
        );
    }
    
    /**
     * Enfileira assets do editor
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'brdposts-block-editor',
            BRDPOSTS_PLUGIN_URL . 'build/index.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'),
            BRDPOSTS_VERSION
        );
        
        wp_enqueue_style(
            'brdposts-block-editor-style',
            BRDPOSTS_PLUGIN_URL . 'build/editor.css',
            array('wp-edit-blocks'),
            BRDPOSTS_VERSION
        );
    }
    
    /**
     * Enfileira assets do frontend
     */
    public function enqueue_block_assets() {
        // Swiper CSS/JS para slider
        wp_enqueue_style(
            'brdposts-swiper-style',
            'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css',
            array(),
            BRDPOSTS_VERSION
        );
        wp_enqueue_script(
            'brdposts-swiper',
            'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js',
            array(),
            BRDPOSTS_VERSION,
            true
        );

        // Estilos do bloco
        wp_enqueue_style(
            'brdposts-block-style',
            BRDPOSTS_PLUGIN_URL . 'build/style.css',
            array(),
            BRDPOSTS_VERSION
        );
        
        // JS do frontend (depende do Swiper)
        wp_enqueue_script(
            'brdposts-frontend',
            BRDPOSTS_PLUGIN_URL . 'build/frontend.js',
            array('jquery', 'brdposts-swiper'),
            BRDPOSTS_VERSION,
            true
        );
    }
    
    /**
     * Renderiza o bloco
     */
    public function render_block($attributes) {
        $args = $this->build_query_args($attributes);
        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return '<p>' . __('Nenhum post encontrado.', 'brdposts') . '</p>';
        }
        
        ob_start();
        
        $layout = isset($attributes['layout']) ? $attributes['layout'] : 'grid';
        $columns = isset($attributes['columns']) ? $attributes['columns'] : 3;
        
        echo '<div class="brdposts-wrapper brdposts-layout-' . esc_attr($layout) . ' brdposts-columns-' . esc_attr($columns) . '">';
        
        switch ($layout) {
            case 'masonry':
                $this->render_masonry_layout($query, $attributes);
                break;
            case 'slider':
                $this->render_slider_layout($query, $attributes);
                break;
            case 'ticker':
                $this->render_ticker_layout($query, $attributes);
                break;
            default:
                $this->render_grid_layout($query, $attributes);
        }
        
        echo '</div>';
        
        // Paginação
        if (isset($attributes['enablePagination']) && $attributes['enablePagination']) {
            echo $this->render_pagination($query);
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Constrói argumentos da query
     */
    private function build_query_args($attributes) {
        $args = array(
            'post_type' => isset($attributes['postType']) ? $attributes['postType'] : 'post',
            'posts_per_page' => isset($attributes['postsPerPage']) ? $attributes['postsPerPage'] : 6,
            'order' => isset($attributes['order']) ? $attributes['order'] : 'DESC',
            'orderby' => isset($attributes['orderBy']) ? $attributes['orderBy'] : 'date',
            'offset' => isset($attributes['offset']) ? $attributes['offset'] : 0,
            'post_status' => 'publish'
        );
        
        // Categorias
        if (!empty($attributes['categories'])) {
            $args['cat'] = implode(',', $attributes['categories']);
        }
        
        // Tags
        if (!empty($attributes['tags'])) {
            $args['tag__in'] = $attributes['tags'];
        }
        
        // Autores
        if (!empty($attributes['authors'])) {
            $args['author__in'] = $attributes['authors'];
        }
        
        // Include/Exclude posts
        if (!empty($attributes['includePosts'])) {
            $args['post__in'] = $attributes['includePosts'];
        }
        
        if (!empty($attributes['excludePosts'])) {
            $args['post__not_in'] = $attributes['excludePosts'];
        }
        
        // Excluir post atual
        if (isset($attributes['excludeCurrent']) && $attributes['excludeCurrent'] && is_singular()) {
            $args['post__not_in'][] = get_the_ID();
        }

        // Taxonomy filtering (custom taxonomies)
        if (!empty($attributes['taxonomy']) && !empty($attributes['taxonomyTerms'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $attributes['taxonomy'],
                    'field'    => 'term_id',
                    'terms'    => $attributes['taxonomyTerms'],
                    'operator' => 'IN',
                ),
            );
        }
        
        // Hook customizado para desenvolvedores
        return apply_filters('brdposts_query_args', $args, $attributes);
    }
    
    /**
     * Renderiza layout Grid
     */
    private function render_grid_layout($query, $attributes) {
        echo '<div class="brdposts-grid">';
        
        while ($query->have_posts()) {
            $query->the_post();
            $this->render_post_item($attributes);
        }
        
        echo '</div>';
    }
    
    /**
     * Renderiza layout Masonry
     */
    private function render_masonry_layout($query, $attributes) {
        echo '<div class="brdposts-masonry">';
        
        while ($query->have_posts()) {
            $query->the_post();
            $this->render_post_item($attributes);
        }
        
        echo '</div>';
    }
    
    /**
     * Renderiza layout Slider
     */
    private function render_slider_layout($query, $attributes) {
        echo '<div class="brdposts-slider swiper">';
        echo '<div class="swiper-wrapper">';
        
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="swiper-slide">';
            $this->render_post_item($attributes);
            echo '</div>';
        }
        
        echo '</div>';
        echo '<div class="swiper-pagination"></div>';
        echo '<div class="swiper-button-prev"></div>';
        echo '<div class="swiper-button-next"></div>';
        echo '</div>';
    }
    
    /**
     * Renderiza layout Ticker
     */
    private function render_ticker_layout($query, $attributes) {
        echo '<div class="brdposts-ticker">';
        echo '<div class="brdposts-ticker-content">';
        
        while ($query->have_posts()) {
            $query->the_post();
            echo '<span class="brdposts-ticker-item">';
            echo '<a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a>';
            echo '</span>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Renderiza item individual do post
     */
    private function render_post_item($attributes) {
        $show_image = isset($attributes['showImage']) ? $attributes['showImage'] : true;
        $show_title = isset($attributes['showTitle']) ? $attributes['showTitle'] : true;
        $show_excerpt = isset($attributes['showExcerpt']) ? $attributes['showExcerpt'] : true;
        $show_meta = isset($attributes['showMeta']) ? $attributes['showMeta'] : true;
        $show_read_more = isset($attributes['showReadMore']) ? $attributes['showReadMore'] : true;
        
        echo '<article class="brdposts-item">';
        
        // Imagem destacada
        if ($show_image && has_post_thumbnail()) {
            $image_size = isset($attributes['imageSize']) ? $attributes['imageSize'] : 'medium';
            $link_image = isset($attributes['linkImage']) ? $attributes['linkImage'] : true;
            
            echo '<div class="brdposts-thumbnail">';
            if ($link_image) {
                echo '<a href="' . esc_url(get_permalink()) . '">';
            }
            the_post_thumbnail($image_size);
            if ($link_image) {
                echo '</a>';
            }
            echo '</div>';
        }
        
        echo '<div class="brdposts-content">';
        
        // Título
        if ($show_title) {
            $link_title = isset($attributes['linkTitle']) ? $attributes['linkTitle'] : true;
            echo '<h3 class="brdposts-title">';
            if ($link_title) {
                echo '<a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a>';
            } else {
                echo get_the_title();
            }
            echo '</h3>';
        }
        
        // Meta informações
        if ($show_meta) {
            $this->render_post_meta($attributes);
        }
        
        // Excerpt
        if ($show_excerpt) {
            $excerpt_length = isset($attributes['excerptLength']) ? $attributes['excerptLength'] : 20;
            echo '<div class="brdposts-excerpt">';
            echo wp_trim_words(get_the_excerpt(), $excerpt_length, '...');
            echo '</div>';
        }
        
        // Botão Ler Mais
        if ($show_read_more) {
            $read_more_text = isset($attributes['readMoreText']) ? $attributes['readMoreText'] : 'Ler Mais';
            echo '<a href="' . esc_url(get_permalink()) . '" class="brdposts-read-more">' . esc_html($read_more_text) . '</a>';
        }
        
        echo '</div>'; // .brdposts-content
        echo '</article>';
    }
    
    /**
     * Renderiza meta informações
     */
    private function render_post_meta($attributes) {
        $show_date = isset($attributes['showDate']) ? $attributes['showDate'] : true;
        $show_author = isset($attributes['showAuthor']) ? $attributes['showAuthor'] : true;
        $show_categories = isset($attributes['showCategories']) ? $attributes['showCategories'] : true;
        $show_reading_time = isset($attributes['showReadingTime']) ? $attributes['showReadingTime'] : false;
        
        echo '<div class="brdposts-meta">';
        
        if ($show_date) {
            echo '<span class="brdposts-meta-date">';
            echo '<i class="dashicons dashicons-calendar"></i> ';
            echo get_the_date();
            echo '</span>';
        }
        
        if ($show_author) {
            echo '<span class="brdposts-meta-author">';
            echo '<i class="dashicons dashicons-admin-users"></i> ';
            $author_name = get_the_author();
            if (!isset($attributes['linkAuthor']) || $attributes['linkAuthor']) {
                echo '<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html($author_name) . '</a>';
            } else {
                echo esc_html($author_name);
            }
            echo '</span>';
        }
        
        if ($show_categories) {
            $categories = get_the_category();
            if (!empty($categories)) {
                echo '<span class="brdposts-meta-categories">';
                echo '<i class="dashicons dashicons-category"></i> ';
                foreach ($categories as $i => $category) {
                    if ($i > 0) echo ', ';
                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                }
                echo '</span>';
            }
        }

        if (!empty($attributes['showTags'])) {
            $tags = get_the_tags();
            if (!empty($tags)) {
                echo '<span class="brdposts-meta-tags">';
                echo '<i class="dashicons dashicons-tag"></i> ';
                foreach ($tags as $i => $tag) {
                    if ($i > 0) echo ', ';
                    echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a>';
                }
                echo '</span>';
            }
        }
        
        if ($show_reading_time) {
            $reading_time = $this->calculate_reading_time(get_post_field('post_content', get_the_ID()));
            echo '<span class="brdposts-meta-reading-time">';
            echo '<i class="dashicons dashicons-clock"></i> ';
            echo $reading_time . ' min';
            echo '</span>';
        }
        
        echo '</div>';
    }
    
    /**
     * Calcula tempo de leitura
     */
    private function calculate_reading_time($content) {
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // 200 palavras por minuto
        return max(1, $reading_time);
    }
    
    /**
     * Renderiza paginação
     */
    private function render_pagination($query) {
        if ($query->max_num_pages <= 1) {
            return '';
        }
        
        $output = '<div class="brdposts-pagination">';
        $output .= paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => max(1, get_query_var('paged')),
            'prev_text' => '&laquo; ' . __('Anterior', 'brdposts'),
            'next_text' => __('Próximo', 'brdposts') . ' &raquo;'
        ));
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Suporte a shortcode
     */
    public function shortcode_render($atts) {
        $attributes = shortcode_atts($this->get_block_attributes(), $atts);
        return $this->render_block($attributes);
    }
}

// Inicializa o plugin
function brdposts_init() {
    return BRDPosts::get_instance();
}
add_action('plugins_loaded', 'brdposts_init');