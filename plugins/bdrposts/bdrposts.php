<?php
/**
 * Plugin Name: BDRPosts
 * Plugin URI: https://github.com/davidcreator/recursos-wp/bdrposts
 * Description: Plugin flexível para exibir posts, páginas e custom post types com múltiplos layouts usando o editor Gutenberg
 * Version: 1.0.1
 * Author: David L. Almeida
 * Author URI: https://davidcreator.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bdrposts
 * Domain Path: /languages
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Define constantes do plugin
define('BDRPOSTS_VERSION', '1.0.1');
define('BDRPOSTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BDRPOSTS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Classe principal do plugin BDRPosts
 */
class BDRPosts {
    
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
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Shortcode support
        add_shortcode('bdrposts', array($this, 'shortcode_render'));
        
        // Cria diretório build se não existir
        $this->ensure_build_directory();
    }
    
    /**
     * Garante que o diretório build existe
     */
    private function ensure_build_directory() {
        $build_dir = BDRPOSTS_PLUGIN_DIR . 'build';
        if (!file_exists($build_dir)) {
            wp_mkdir_p($build_dir);
        }
    }
    
    /**
     * Carrega tradução
     */
    public function load_textdomain() {
        load_plugin_textdomain('bdrposts', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Registra rotas REST API
     */
    public function register_rest_routes() {
        // Post types
        register_rest_route('bdrposts/v1', '/post-types', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_post_types'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
        
        // Taxonomias
        register_rest_route('bdrposts/v1', '/taxonomies/(?P<post_type>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_taxonomies'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
        
        // Categorias
        register_rest_route('bdrposts/v1', '/categories', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_categories'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
        
        // Tags
        register_rest_route('bdrposts/v1', '/tags', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_tags'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
        
        // Termos de taxonomia customizada
        register_rest_route('bdrposts/v1', '/terms/(?P<taxonomy>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_taxonomy_terms'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
    }
    
    /**
     * Retorna post types disponíveis
     */
    public function get_post_types() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $result = array();
        
        foreach ($post_types as $post_type) {
            $result[] = array(
                'value' => $post_type->name,
                'label' => $post_type->label
            );
        }
        
        return rest_ensure_response($result);
    }
    
    /**
     * Retorna taxonomias de um post type
     */
    public function get_taxonomies($request) {
        $post_type = $request->get_param('post_type');
        $taxonomies = get_object_taxonomies($post_type, 'objects');
        $result = array();
        
        foreach ($taxonomies as $taxonomy) {
            $result[] = array(
                'value' => $taxonomy->name,
                'label' => $taxonomy->label
            );
        }
        
        return rest_ensure_response($result);
    }
    
    /**
     * Retorna categorias
     */
    public function get_categories() {
        $categories = get_categories(array(
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ));
        
        $result = array();
        foreach ($categories as $category) {
            $result[] = array(
                'value' => $category->term_id,
                'label' => $category->name . ' (' . $category->count . ')'
            );
        }
        
        return rest_ensure_response($result);
    }
    
    /**
     * Retorna tags
     */
    public function get_tags() {
        $tags = get_tags(array(
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ));
        
        $result = array();
        foreach ($tags as $tag) {
            $result[] = array(
                'value' => $tag->term_id,
                'label' => $tag->name . ' (' . $tag->count . ')'
            );
        }
        
        return rest_ensure_response($result);
    }
    
    /**
     * Retorna termos de uma taxonomia
     */
    public function get_taxonomy_terms($request) {
        $taxonomy = $request->get_param('taxonomy');
        
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ));
        
        if (is_wp_error($terms)) {
            return rest_ensure_response(array());
        }
        
        $result = array();
        foreach ($terms as $term) {
            $result[] = array(
                'value' => $term->term_id,
                'label' => $term->name . ' (' . $term->count . ')'
            );
        }
        
        return rest_ensure_response($result);
    }
    
    /**
     * Registra o bloco Gutenberg
     */
    public function register_block() {
        register_block_type('bdrposts/post-block', array(
            'api_version' => 2,
            'editor_script' => 'bdrposts-block-editor',
            'editor_style' => 'bdrposts-block-editor-style',
            'style' => 'bdrposts-block-style',
            'render_callback' => array($this, 'render_block'),
            'attributes' => $this->get_block_attributes()
        ));
    }
    
    /**
     * Define os atributos do bloco
     */
    private function get_block_attributes() {
        return array(
            'layout' => array('type' => 'string', 'default' => 'grid'),
            'subLayout' => array('type' => 'string', 'default' => 'title-meta'),
            'postType' => array('type' => 'string', 'default' => 'post'),
            'postsPerPage' => array('type' => 'number', 'default' => 6),
            'columns' => array('type' => 'number', 'default' => 3),
            'order' => array('type' => 'string', 'default' => 'DESC'),
            'orderBy' => array('type' => 'string', 'default' => 'date'),
            'categories' => array('type' => 'array', 'default' => array(), 'items' => array('type' => 'number')),
            'tags' => array('type' => 'array', 'default' => array(), 'items' => array('type' => 'number')),
            'authors' => array('type' => 'array', 'default' => array(), 'items' => array('type' => 'number')),
            'offset' => array('type' => 'number', 'default' => 0),
            'includePosts' => array('type' => 'array', 'default' => array(), 'items' => array('type' => 'number')),
            'excludePosts' => array('type' => 'array', 'default' => array(), 'items' => array('type' => 'number')),
            'excludeCurrent' => array('type' => 'boolean', 'default' => false),
            'showImage' => array('type' => 'boolean', 'default' => true),
            'imageSize' => array('type' => 'string', 'default' => 'medium'),
            'linkImage' => array('type' => 'boolean', 'default' => true),
            'showTitle' => array('type' => 'boolean', 'default' => true),
            'linkTitle' => array('type' => 'boolean', 'default' => true),
            'showExcerpt' => array('type' => 'boolean', 'default' => true),
            'excerptLength' => array('type' => 'number', 'default' => 20),
            'showMeta' => array('type' => 'boolean', 'default' => true),
            'showDate' => array('type' => 'boolean', 'default' => true),
            'showAuthor' => array('type' => 'boolean', 'default' => true),
            'showCategories' => array('type' => 'boolean', 'default' => true),
            'showTags' => array('type' => 'boolean', 'default' => false),
            'linkAuthor' => array('type' => 'boolean', 'default' => true),
            'taxonomy' => array('type' => 'string', 'default' => ''),
            'taxonomyTerms' => array('type' => 'array', 'default' => array(), 'items' => array('type' => 'number')),
            'showReadMore' => array('type' => 'boolean', 'default' => true),
            'readMoreText' => array('type' => 'string', 'default' => 'Ler Mais'),
            'enablePagination' => array('type' => 'boolean', 'default' => false),
            'showReadingTime' => array('type' => 'boolean', 'default' => false)
        );
    }
    
    /**
     * Enfileira assets do editor
     */
    public function enqueue_block_editor_assets() {
        $js_file = BDRPOSTS_PLUGIN_DIR . 'build/index.js';
        $css_file = BDRPOSTS_PLUGIN_DIR . 'build/editor.css';
        
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'bdrposts-block-editor',
                BDRPOSTS_PLUGIN_URL . 'build/index.js',
                array('wp-blocks', 'wp-element', 'wp-editor', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-api-fetch', 'wp-i18n', 'wp-server-side-render'),
                BDRPOSTS_VERSION,
                true
            );
        }
        
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'bdrposts-block-editor-style',
                BDRPOSTS_PLUGIN_URL . 'build/editor.css',
                array('wp-edit-blocks'),
                BDRPOSTS_VERSION
            );
        }
        
        // Passa dados para o JavaScript
        wp_localize_script('bdrposts-block-editor', 'bdrpostsData', array(
            'restUrl' => rest_url('bdrposts/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => BDRPOSTS_PLUGIN_URL
        ));
    }
    
    /**
     * Enfileira assets do frontend
     */
    public function enqueue_block_assets() {
        // Swiper CSS/JS para slider
        wp_enqueue_style(
            'bdrposts-swiper-style',
            'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css',
            array(),
            '9.0.0'
        );
        
        wp_enqueue_script(
            'bdrposts-swiper',
            'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js',
            array(),
            '9.0.0',
            true
        );

        // Estilos do bloco
        $style_file = BDRPOSTS_PLUGIN_DIR . 'build/style.css';
        if (file_exists($style_file)) {
            wp_enqueue_style(
                'bdrposts-block-style',
                BDRPOSTS_PLUGIN_URL . 'build/style.css',
                array(),
                BDRPOSTS_VERSION
            );
        } else {
            wp_add_inline_style('wp-block-library', $this->get_inline_styles());
        }
        
        // JS do frontend
        $js_file = BDRPOSTS_PLUGIN_DIR . 'build/frontend.js';
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'bdrposts-frontend',
                BDRPOSTS_PLUGIN_URL . 'build/frontend.js',
                array('jquery', 'bdrposts-swiper'),
                BDRPOSTS_VERSION,
                true
            );
        } else {
            wp_add_inline_script('bdrposts-swiper', $this->get_inline_script());
        }
    }
    
    /**
     * Retorna CSS inline como fallback
     */
    private function get_inline_styles() {
        return '
        .bdrposts-wrapper{width:100%;margin:20px 0}
        .bdrposts-grid{display:grid;gap:20px}
        .bdrposts-columns-1 .bdrposts-grid{grid-template-columns:1fr}
        .bdrposts-columns-2 .bdrposts-grid{grid-template-columns:repeat(2,1fr)}
        .bdrposts-columns-3 .bdrposts-grid{grid-template-columns:repeat(3,1fr)}
        .bdrposts-columns-4 .bdrposts-grid{grid-template-columns:repeat(4,1fr)}
        .bdrposts-masonry{column-gap:20px}
        .bdrposts-columns-2 .bdrposts-masonry{column-count:2}
        .bdrposts-columns-3 .bdrposts-masonry{column-count:3}
        .bdrposts-columns-4 .bdrposts-masonry{column-count:4}
        .bdrposts-masonry .bdrposts-item{break-inside:avoid;margin-bottom:20px}
        .bdrposts-item{background:#fff;border:1px solid #e5e5e5;border-radius:6px;overflow:hidden;transition:transform .3s,box-shadow .3s}
        .bdrposts-item:hover{transform:translateY(-5px);box-shadow:0 10px 30px rgba(0,0,0,.1)}
        .bdrposts-thumbnail{width:100%;display:block;overflow:hidden}
        .bdrposts-thumbnail img{width:100%;height:auto;display:block;transition:transform .3s}
        .bdrposts-item:hover .bdrposts-thumbnail img{transform:scale(1.05)}
        .bdrposts-content{padding:16px}
        .bdrposts-title{margin:0 0 10px;font-size:20px;line-height:1.4}
        .bdrposts-title a{text-decoration:none;color:inherit;transition:color .3s}
        .bdrposts-title a:hover{color:#0073aa}
        .bdrposts-excerpt{color:#555;line-height:1.6;margin:10px 0}
        .bdrposts-meta{display:flex;flex-wrap:wrap;gap:12px;font-size:13px;color:#777;margin-bottom:10px}
        .bdrposts-meta a{color:inherit;text-decoration:none}
        .bdrposts-meta a:hover{color:#0073aa}
        .bdrposts-read-more{display:inline-block;margin-top:12px;padding:8px 12px;border-radius:4px;background:#0073aa;color:#fff;text-decoration:none;transition:background .3s}
        .bdrposts-read-more:hover{background:#005177}
        .bdrposts-ticker{overflow:hidden;background:#f5f5f5;padding:15px 0}
        .bdrposts-ticker-content{display:inline-block;white-space:nowrap;animation:ticker-scroll 30s linear infinite}
        .bdrposts-ticker-item{display:inline-block;margin-right:30px}
        @keyframes ticker-scroll{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
        @media(max-width:768px){.bdrposts-columns-3 .bdrposts-grid,.bdrposts-columns-4 .bdrposts-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:600px){.bdrposts-columns-2 .bdrposts-grid,.bdrposts-columns-3 .bdrposts-grid,.bdrposts-columns-4 .bdrposts-grid{grid-template-columns:1fr}}
        ';
    }
    
    /**
     * Retorna JS inline como fallback
     */
    private function get_inline_script() {
        return '
        (function(){
            function init(){
                if(typeof Swiper!=="undefined"){
                    document.querySelectorAll(".bdrposts-slider.swiper").forEach(function(el){
                        new Swiper(el,{loop:true,slidesPerView:1,pagination:{el:el.querySelector(".swiper-pagination"),clickable:true},navigation:{nextEl:el.querySelector(".swiper-button-next"),prevEl:el.querySelector(".swiper-button-prev")}});
                    });
                }
            }
            if(document.readyState==="complete"||document.readyState==="interactive"){init()}else{document.addEventListener("DOMContentLoaded",init)}
        })();
        ';
    }
    
    /**
     * Renderiza o bloco
     */
    public function render_block($attributes) {
        $args = $this->build_query_args($attributes);
        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return '<div class="bdrposts-wrapper"><p>' . __('Nenhum post encontrado.', 'bdrposts') . '</p></div>';
        }
        
        ob_start();
        
        $layout = isset($attributes['layout']) ? $attributes['layout'] : 'grid';
        $sub_layout = isset($attributes['subLayout']) ? $attributes['subLayout'] : 'title-meta';
        $columns = isset($attributes['columns']) ? $attributes['columns'] : 3;
        
        $wrapper_classes = array(
            'bdrposts-wrapper',
            'bdrposts-layout-' . esc_attr($layout),
            'bdrposts-sublayout-' . esc_attr($sub_layout),
            'bdrposts-columns-' . esc_attr($columns)
        );
        
        echo '<div class="' . implode(' ', $wrapper_classes) . '">';
        
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
            'posts_per_page' => isset($attributes['postsPerPage']) ? intval($attributes['postsPerPage']) : 6,
            'order' => isset($attributes['order']) ? $attributes['order'] : 'DESC',
            'orderby' => isset($attributes['orderBy']) ? $attributes['orderBy'] : 'date',
            'offset' => isset($attributes['offset']) ? intval($attributes['offset']) : 0,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        );
        
        // Paginação
        if (isset($attributes['enablePagination']) && $attributes['enablePagination']) {
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $args['paged'] = $paged;
            unset($args['offset']);
        }
        
        // Categorias
        if (!empty($attributes['categories']) && is_array($attributes['categories']) && count($attributes['categories']) > 0) {
            $args['cat'] = implode(',', array_map('intval', $attributes['categories']));
        }
        
        // Tags
        if (!empty($attributes['tags']) && is_array($attributes['tags']) && count($attributes['tags']) > 0) {
            $args['tag__in'] = array_map('intval', $attributes['tags']);
        }
        
        // Autores
        if (!empty($attributes['authors']) && is_array($attributes['authors']) && count($attributes['authors']) > 0) {
            $args['author__in'] = array_map('intval', $attributes['authors']);
        }
        
        // Include/Exclude posts
        if (!empty($attributes['includePosts']) && is_array($attributes['includePosts']) && count($attributes['includePosts']) > 0) {
            $args['post__in'] = array_map('intval', $attributes['includePosts']);
        }
        
        if (!empty($attributes['excludePosts']) && is_array($attributes['excludePosts']) && count($attributes['excludePosts']) > 0) {
            $args['post__not_in'] = array_map('intval', $attributes['excludePosts']);
        }
        
        // Excluir post atual
        if (isset($attributes['excludeCurrent']) && $attributes['excludeCurrent'] && is_singular()) {
            if (!isset($args['post__not_in'])) {
                $args['post__not_in'] = array();
            }
            $args['post__not_in'][] = get_the_ID();
        }

        // Taxonomy filtering
        if (!empty($attributes['taxonomy']) && !empty($attributes['taxonomyTerms']) && is_array($attributes['taxonomyTerms']) && count($attributes['taxonomyTerms']) > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => sanitize_text_field($attributes['taxonomy']),
                    'field'    => 'term_id',
                    'terms'    => array_map('intval', $attributes['taxonomyTerms']),
                    'operator' => 'IN',
                ),
            );
        }
        
        return apply_filters('bdrposts_query_args', $args, $attributes);
    }
    
    /**
     * Renderiza layout Grid
     */
    private function render_grid_layout($query, $attributes) {
        echo '<div class="bdrposts-grid">';
        
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
        echo '<div class="bdrposts-masonry">';
        
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
        $slider_id = 'bdrposts-slider-' . wp_rand(1000, 9999);
        echo '<div class="bdrposts-slider swiper" id="' . esc_attr($slider_id) . '">';
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
        echo '<div class="bdrposts-ticker">';
        echo '<div class="bdrposts-ticker-content">';
        
        $items = array();
        while ($query->have_posts()) {
            $query->the_post();
            $items[] = '<span class="bdrposts-ticker-item"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></span>';
        }
        
        // Duplica itens para loop infinito
        echo implode('', $items);
        echo implode('', $items);
        
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
        
        echo '<article class="bdrposts-item" id="post-' . get_the_ID() . '">';
        
        // Imagem destacada
        if ($show_image && has_post_thumbnail()) {
            $image_size = isset($attributes['imageSize']) ? $attributes['imageSize'] : 'medium';
            $link_image = isset($attributes['linkImage']) ? $attributes['linkImage'] : true;
            
            echo '<div class="bdrposts-thumbnail">';
            if ($link_image) {
                echo '<a href="' . esc_url(get_permalink()) . '" aria-label="' . esc_attr(get_the_title()) . '">';
            }
            the_post_thumbnail($image_size, array('loading' => 'lazy'));
            if ($link_image) {
                echo '</a>';
            }
            echo '</div>';
        }
        
        echo '<div class="bdrposts-content">';
        
        // Meta no topo (se sub-layout for meta-title)
        if ($show_meta && isset($attributes['subLayout']) && $attributes['subLayout'] === 'meta-title') {
            $this->render_post_meta($attributes);
        }
        
        // Título
        if ($show_title) {
            $link_title = isset($attributes['linkTitle']) ? $attributes['linkTitle'] : true;
            echo '<h3 class="bdrposts-title">';
            if ($link_title) {
                echo '<a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a>';
            } else {
                echo esc_html(get_the_title());
            }
            echo '</h3>';
        }
        
        // Meta (posição padrão)
        if ($show_meta && (!isset($attributes['subLayout']) || $attributes['subLayout'] !== 'meta-title')) {
            $this->render_post_meta($attributes);
        }
        
        // Excerpt
        if ($show_excerpt) {
            $excerpt_length = isset($attributes['excerptLength']) ? intval($attributes['excerptLength']) : 20;
            echo '<div class="bdrposts-excerpt">';
            echo wp_trim_words(get_the_excerpt(), $excerpt_length, '...');
            echo '</div>';
        }
        
        // Botão Ler Mais
        if ($show_read_more) {
            $read_more_text = isset($attributes['readMoreText']) ? $attributes['readMoreText'] : __('Ler Mais', 'bdrposts');
            echo '<a href="' . esc_url(get_permalink()) . '" class="bdrposts-read-more">' . esc_html($read_more_text) . '</a>';
        }
        
        echo '</div>'; // .bdrposts-content
        echo '</article>';
    }
    
    /**
     * Renderiza meta informações
     */
    private function render_post_meta($attributes) {
        $show_date = isset($attributes['showDate']) ? $attributes['showDate'] : true;
        $show_author = isset($attributes['showAuthor']) ? $attributes['showAuthor'] : true;
        $show_categories = isset($attributes['showCategories']) ? $attributes['showCategories'] : true;
        $show_tags = isset($attributes['showTags']) ? $attributes['showTags'] : false;
        $show_reading_time = isset($attributes['showReadingTime']) ? $attributes['showReadingTime'] : false;
        $link_author = isset($attributes['linkAuthor']) ? $attributes['linkAuthor'] : true;
        
        $meta_items = array();
        
        if ($show_date) {
            $meta_items[] = '<span class="bdrposts-meta-date"><time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time></span>';
        }
        
        if ($show_author) {
            $author_name = get_the_author();
            if ($link_author) {
                $meta_items[] = '<span class="bdrposts-meta-author"><a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html($author_name) . '</a></span>';
            } else {
                $meta_items[] = '<span class="bdrposts-meta-author">' . esc_html($author_name) . '</span>';
            }
        }
        
        if ($show_categories) {
            $categories = get_the_category();
            if (!empty($categories)) {
                $cat_links = array();
                foreach ($categories as $category) {
                    $cat_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                }
                $meta_items[] = '<span class="bdrposts-meta-categories">' . implode(', ', $cat_links) . '</span>';
            }
        }

        if ($show_tags) {
            $tags = get_the_tags();
            if (!empty($tags)) {
                $tag_links = array();
                foreach ($tags as $tag) {
                    $tag_links[] = '<a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a>';
                }
                $meta_items[] = '<span class="bdrposts-meta-tags">' . implode(', ', $tag_links) . '</span>';
            }
        }
        
        if ($show_reading_time) {
            $reading_time = $this->calculate_reading_time(get_post_field('post_content', get_the_ID()));
            $meta_items[] = '<span class="bdrposts-meta-reading-time">' . sprintf(__('%s min de leitura', 'bdrposts'), $reading_time) . '</span>';
        }
        
        if (!empty($meta_items)) {
            echo '<div class="bdrposts-meta">' . implode('<span class="bdrposts-meta-separator"> • </span>', $meta_items) . '</div>';
        }
    }
    
    /**
     * Calcula tempo de leitura
     */
    private function calculate_reading_time($content) {
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200);
        return max(1, $reading_time);
    }
    
    /**
     * Renderiza paginação
     */
    private function render_pagination($query) {
        if ($query->max_num_pages <= 1) {
            return '';
        }
        
        $output = '<nav class="bdrposts-pagination" role="navigation" aria-label="' . esc_attr__('Navegação de posts', 'bdrposts') . '">';
        $output .= paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => max(1, get_query_var('paged')),
            'prev_text' => '&laquo; ' . __('Anterior', 'bdrposts'),
            'next_text' => __('Próximo', 'bdrposts') . ' &raquo;',
            'type' => 'list'
        ));
        $output .= '</nav>';
        
        return $output;
    }
    
    /**
     * Suporte a shortcode
     */
    public function shortcode_render($atts) {
        $defaults = array();
        foreach ($this->get_block_attributes() as $key => $value) {
            $defaults[$key] = $value['default'];
        }
        
        $attributes = shortcode_atts($defaults, $atts);
        
        // Converte strings para arrays onde necessário
        $array_fields = array('categories', 'tags', 'authors', 'includePosts', 'excludePosts', 'taxonomyTerms');
        foreach ($array_fields as $field) {
            if (isset($attributes[$field]) && is_string($attributes[$field])) {
                $attributes[$field] = array_filter(array_map('intval', explode(',', $attributes[$field])));
            }
        }
        
        // Converte strings para booleanos
        $bool_fields = array('excludeCurrent', 'showImage', 'linkImage', 'showTitle', 'linkTitle', 'showExcerpt', 'showMeta', 'showDate', 'showAuthor', 'showCategories', 'showTags', 'linkAuthor', 'showReadMore', 'enablePagination', 'showReadingTime');
        foreach ($bool_fields as $field) {
            if (isset($attributes[$field])) {
                $attributes[$field] = filter_var($attributes[$field], FILTER_VALIDATE_BOOLEAN);
            }
        }
        
        return $this->render_block($attributes);
    }
}

/**
 * Inicializa o plugin
 */
function bdrposts_init() {
    return BDRPosts::get_instance();
}
add_action('plugins_loaded', 'bdrposts_init');

/**
 * Hook de ativação
 */
register_activation_hook(__FILE__, function() {
    $build_dir = BDRPOSTS_PLUGIN_DIR . 'build';
    if (!file_exists($build_dir)) {
        wp_mkdir_p($build_dir);
    }
    
    update_option('bdrposts_version', BDRPOSTS_VERSION);
    flush_rewrite_rules();
});

/**
 * Hook de desativação
 */
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});