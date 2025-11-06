<?php
/**
 * Page Templates Management
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Page Templates Class
 */
class NosfirNews_Page_Templates {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_filter('theme_page_templates', array($this, 'add_page_templates'));
        add_filter('template_include', array($this, 'redirect_page_template'));
    }

    /**
     * Initialize
     */
    public function init() {
        // Register custom post meta for page templates
        $this->register_page_meta();
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Garantir ID correto da página atual
        if (!is_page()) {
            return;
        }
        $page_id = get_queried_object_id();
        if (empty($page_id)) {
            return;
        }
        $template = get_page_template_slug($page_id);
        
        // Enqueue page template styles
        wp_enqueue_style(
            'nosfirnews-page-templates',
            get_template_directory_uri() . '/assets/css/page-templates.css',
            array(),
            NOSFIRNEWS_VERSION
        );

        // Enqueue page template scripts
        wp_enqueue_script(
            'nosfirnews-page-templates',
            get_template_directory_uri() . '/assets/js/page-templates.js',
            array('jquery'),
            NOSFIRNEWS_VERSION,
            true
        );

        // Enqueue specific scripts based on template
        switch ($template) {
            case 'templates/page-templates/page-demo.php':
                // Demo page specific assets
                wp_enqueue_style(
                    'nosfirnews-page-demo',
                    get_template_directory_uri() . '/assets/css/page-demo.css',
                    array('nosfirnews-page-templates'),
                    NOSFIRNEWS_VERSION
                );
                wp_enqueue_script(
                    'nosfirnews-page-demo',
                    get_template_directory_uri() . '/assets/js/page-demo.js',
                    array('jquery', 'nosfirnews-page-templates'),
                    NOSFIRNEWS_VERSION,
                    true
                );
                break;
            case 'templates/page-templates/page-portfolio.php':
                // Masonry for portfolio
                wp_enqueue_script('masonry');
                wp_enqueue_script('imagesloaded');
                
                // Magnific Popup for lightbox
                wp_enqueue_style(
                    'magnific-popup',
                    'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css',
                    array(),
                    '1.1.0'
                );
                wp_enqueue_script(
                    'magnific-popup',
                    'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js',
                    array('jquery'),
                    '1.1.0',
                    true
                );
                break;

            case 'templates/page-templates/page-blog-grid.php':
                // Masonry for blog grid
                wp_enqueue_script('masonry');
                wp_enqueue_script('imagesloaded');
                break;

            case 'templates/page-templates/page-magazine.php':
                // Slick slider for featured stories
                wp_enqueue_style(
                    'slick-css',
                    'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css',
                    array(),
                    '1.8.1'
                );
                wp_enqueue_style(
                    'slick-theme',
                    'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css',
                    array(),
                    '1.8.1'
                );
                wp_enqueue_script(
                    'slick-js',
                    'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
                    array('jquery'),
                    '1.8.1',
                    true
                );
                break;
        }

        // Localize script for AJAX
        wp_localize_script('nosfirnews-page-templates', 'nosfirnews_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nosfirnews_ajax_nonce'),
            'loading_text' => __('Carregando...', 'nosfirnews'),
            'load_more_text' => __('Carregar Mais', 'nosfirnews'),
            'no_more_text' => __('Não há mais itens', 'nosfirnews')
        ));
    }

    /**
     * Add page templates to the dropdown
     */
    public function add_page_templates($templates) {
        // Ensure the demo template appears in the dropdown
        $templates['templates/page-templates/page-demo.php'] = __('Página Demonstrativa NosfirNews', 'nosfirnews');
        $templates['templates/page-templates/page-landing.php'] = __('Landing Page', 'nosfirnews');
        $templates['templates/page-templates/page-portfolio.php'] = __('Portfolio', 'nosfirnews');
        $templates['templates/page-templates/page-blog-grid.php'] = __('Blog Grid', 'nosfirnews');
        $templates['templates/page-templates/page-magazine.php'] = __('Magazine Layout', 'nosfirnews');
        
        return $templates;
    }

    /**
     * Redirect page template
     */
    public function redirect_page_template($template) {
        global $post;
        
        if (!is_page()) {
            return $template;
        }

        $page_template = get_page_template_slug($post->ID);
        
        if ($page_template) {
            $template_file = get_template_directory() . '/' . $page_template;
            
            if (file_exists($template_file)) {
                return $template_file;
            }
        }
        
        return $template;
    }

    /**
     * Register page meta fields
     */
    public function register_page_meta() {
        // Landing Page Meta
        register_post_meta('page', '_landing_hero_title', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));

        register_post_meta('page', '_landing_hero_subtitle', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_textarea_field'
        ));

        register_post_meta('page', '_landing_hero_button_text', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));

        register_post_meta('page', '_landing_hero_button_url', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'esc_url_raw'
        ));

        register_post_meta('page', '_landing_hero_background', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'esc_url_raw'
        ));

        // Portfolio Meta
        register_post_meta('page', '_portfolio_layout', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));

        register_post_meta('page', '_portfolio_columns', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));

        register_post_meta('page', '_portfolio_categories', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));

        // Blog Grid Meta
        register_post_meta('page', '_blog_layout', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));

        register_post_meta('page', '_blog_posts_per_page', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));

        register_post_meta('page', '_blog_show_filters', array(
            'type' => 'boolean',
            'single' => true
        ));

        // Magazine Meta
        register_post_meta('page', '_magazine_breaking_news', array(
            'type' => 'boolean',
            'single' => true
        ));

        register_post_meta('page', '_magazine_featured_categories', array(
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'nosfirnews_page_template_options',
            __('Opções do Template', 'nosfirnews'),
            array($this, 'render_meta_box'),
            'page',
            'normal',
            'high'
        );
    }

    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('nosfirnews_page_template_meta', 'nosfirnews_page_template_nonce');
        
        $template = get_page_template_slug($post->ID);
        
        echo '<div id="page-template-options">';
        
        switch ($template) {
            case 'templates/page-templates/page-landing.php':
                $this->render_landing_options($post);
                break;
                
            case 'templates/page-templates/page-portfolio.php':
                $this->render_portfolio_options($post);
                break;
                
            case 'templates/page-templates/page-blog-grid.php':
                $this->render_blog_grid_options($post);
                break;
                
            case 'templates/page-templates/page-magazine.php':
                $this->render_magazine_options($post);
                break;
                
            default:
                echo '<p>' . __('Selecione um template de página para ver as opções disponíveis.', 'nosfirnews') . '</p>';
                break;
        }
        
        echo '</div>';
        
        // Add JavaScript for dynamic options
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('#page_template').on('change', function() {
                var template = $(this).val();
                $('#page-template-options').load(ajaxurl, {
                    action: 'nosfirnews_load_template_options',
                    template: template,
                    post_id: <?php echo $post->ID; ?>,
                    nonce: '<?php echo wp_create_nonce('nosfirnews_template_options'); ?>'
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Render landing page options
     */
    private function render_landing_options($post) {
        $hero_title = get_post_meta($post->ID, '_landing_hero_title', true);
        $hero_subtitle = get_post_meta($post->ID, '_landing_hero_subtitle', true);
        $hero_button_text = get_post_meta($post->ID, '_landing_hero_button_text', true);
        $hero_button_url = get_post_meta($post->ID, '_landing_hero_button_url', true);
        $hero_background = get_post_meta($post->ID, '_landing_hero_background', true);
        ?>
        <h4><?php _e('Configurações do Hero', 'nosfirnews'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="landing_hero_title"><?php _e('Título do Hero', 'nosfirnews'); ?></label></th>
                <td><input type="text" id="landing_hero_title" name="landing_hero_title" value="<?php echo esc_attr($hero_title); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="landing_hero_subtitle"><?php _e('Subtítulo do Hero', 'nosfirnews'); ?></label></th>
                <td><textarea id="landing_hero_subtitle" name="landing_hero_subtitle" rows="3" class="large-text"><?php echo esc_textarea($hero_subtitle); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="landing_hero_button_text"><?php _e('Texto do Botão', 'nosfirnews'); ?></label></th>
                <td><input type="text" id="landing_hero_button_text" name="landing_hero_button_text" value="<?php echo esc_attr($hero_button_text); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="landing_hero_button_url"><?php _e('URL do Botão', 'nosfirnews'); ?></label></th>
                <td><input type="url" id="landing_hero_button_url" name="landing_hero_button_url" value="<?php echo esc_url($hero_button_url); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="landing_hero_background"><?php _e('Imagem de Fundo', 'nosfirnews'); ?></label></th>
                <td>
                    <input type="url" id="landing_hero_background" name="landing_hero_background" value="<?php echo esc_url($hero_background); ?>" class="regular-text" />
                    <button type="button" class="button" onclick="openMediaUploader('landing_hero_background')"><?php _e('Selecionar Imagem', 'nosfirnews'); ?></button>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render portfolio options
     */
    private function render_portfolio_options($post) {
        $layout = get_post_meta($post->ID, '_portfolio_layout', true) ?: 'grid';
        $columns = get_post_meta($post->ID, '_portfolio_columns', true) ?: '3';
        $categories = get_post_meta($post->ID, '_portfolio_categories', true);
        ?>
        <h4><?php _e('Configurações do Portfolio', 'nosfirnews'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="portfolio_layout"><?php _e('Layout', 'nosfirnews'); ?></label></th>
                <td>
                    <select id="portfolio_layout" name="portfolio_layout">
                        <option value="grid" <?php selected($layout, 'grid'); ?>><?php _e('Grade', 'nosfirnews'); ?></option>
                        <option value="masonry" <?php selected($layout, 'masonry'); ?>><?php _e('Masonry', 'nosfirnews'); ?></option>
                        <option value="list" <?php selected($layout, 'list'); ?>><?php _e('Lista', 'nosfirnews'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="portfolio_columns"><?php _e('Colunas', 'nosfirnews'); ?></label></th>
                <td>
                    <select id="portfolio_columns" name="portfolio_columns">
                        <option value="2" <?php selected($columns, '2'); ?>>2</option>
                        <option value="3" <?php selected($columns, '3'); ?>>3</option>
                        <option value="4" <?php selected($columns, '4'); ?>>4</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="portfolio_categories"><?php _e('Categorias (IDs separados por vírgula)', 'nosfirnews'); ?></label></th>
                <td><input type="text" id="portfolio_categories" name="portfolio_categories" value="<?php echo esc_attr($categories); ?>" class="regular-text" /></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render blog grid options
     */
    private function render_blog_grid_options($post) {
        $layout = get_post_meta($post->ID, '_blog_layout', true) ?: 'grid';
        $posts_per_page = get_post_meta($post->ID, '_blog_posts_per_page', true) ?: '12';
        $show_filters = get_post_meta($post->ID, '_blog_show_filters', true);
        ?>
        <h4><?php _e('Configurações do Blog Grid', 'nosfirnews'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="blog_layout"><?php _e('Layout', 'nosfirnews'); ?></label></th>
                <td>
                    <select id="blog_layout" name="blog_layout">
                        <option value="grid" <?php selected($layout, 'grid'); ?>><?php _e('Grade', 'nosfirnews'); ?></option>
                        <option value="masonry" <?php selected($layout, 'masonry'); ?>><?php _e('Masonry', 'nosfirnews'); ?></option>
                        <option value="list" <?php selected($layout, 'list'); ?>><?php _e('Lista', 'nosfirnews'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="blog_posts_per_page"><?php _e('Posts por Página', 'nosfirnews'); ?></label></th>
                <td><input type="number" id="blog_posts_per_page" name="blog_posts_per_page" value="<?php echo esc_attr($posts_per_page); ?>" min="1" max="50" /></td>
            </tr>
            <tr>
                <th><label for="blog_show_filters"><?php _e('Mostrar Filtros', 'nosfirnews'); ?></label></th>
                <td><input type="checkbox" id="blog_show_filters" name="blog_show_filters" value="1" <?php checked($show_filters, 1); ?> /></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render magazine options
     */
    private function render_magazine_options($post) {
        $breaking_news = get_post_meta($post->ID, '_magazine_breaking_news', true);
        $featured_categories = get_post_meta($post->ID, '_magazine_featured_categories', true);
        ?>
        <h4><?php _e('Configurações do Magazine', 'nosfirnews'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="magazine_breaking_news"><?php _e('Mostrar Breaking News', 'nosfirnews'); ?></label></th>
                <td><input type="checkbox" id="magazine_breaking_news" name="magazine_breaking_news" value="1" <?php checked($breaking_news, 1); ?> /></td>
            </tr>
            <tr>
                <th><label for="magazine_featured_categories"><?php _e('Categorias em Destaque (IDs separados por vírgula)', 'nosfirnews'); ?></label></th>
                <td><input type="text" id="magazine_featured_categories" name="magazine_featured_categories" value="<?php echo esc_attr($featured_categories); ?>" class="regular-text" /></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save meta boxes
     */
    public function save_meta_boxes($post_id) {
        if (!isset($_POST['nosfirnews_page_template_nonce']) || 
            !wp_verify_nonce($_POST['nosfirnews_page_template_nonce'], 'nosfirnews_page_template_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_page', $post_id)) {
            return;
        }

        // Save landing page meta
        if (isset($_POST['landing_hero_title'])) {
            update_post_meta($post_id, '_landing_hero_title', sanitize_text_field($_POST['landing_hero_title']));
        }
        if (isset($_POST['landing_hero_subtitle'])) {
            update_post_meta($post_id, '_landing_hero_subtitle', sanitize_textarea_field($_POST['landing_hero_subtitle']));
        }
        if (isset($_POST['landing_hero_button_text'])) {
            update_post_meta($post_id, '_landing_hero_button_text', sanitize_text_field($_POST['landing_hero_button_text']));
        }
        if (isset($_POST['landing_hero_button_url'])) {
            update_post_meta($post_id, '_landing_hero_button_url', esc_url_raw($_POST['landing_hero_button_url']));
        }
        if (isset($_POST['landing_hero_background'])) {
            update_post_meta($post_id, '_landing_hero_background', esc_url_raw($_POST['landing_hero_background']));
        }

        // Save portfolio meta
        if (isset($_POST['portfolio_layout'])) {
            update_post_meta($post_id, '_portfolio_layout', sanitize_text_field($_POST['portfolio_layout']));
        }
        if (isset($_POST['portfolio_columns'])) {
            update_post_meta($post_id, '_portfolio_columns', sanitize_text_field($_POST['portfolio_columns']));
        }
        if (isset($_POST['portfolio_categories'])) {
            update_post_meta($post_id, '_portfolio_categories', sanitize_text_field($_POST['portfolio_categories']));
        }

        // Save blog grid meta
        if (isset($_POST['blog_layout'])) {
            update_post_meta($post_id, '_blog_layout', sanitize_text_field($_POST['blog_layout']));
        }
        if (isset($_POST['blog_posts_per_page'])) {
            update_post_meta($post_id, '_blog_posts_per_page', sanitize_text_field($_POST['blog_posts_per_page']));
        }
        update_post_meta($post_id, '_blog_show_filters', isset($_POST['blog_show_filters']) ? 1 : 0);

        // Save magazine meta
        update_post_meta($post_id, '_magazine_breaking_news', isset($_POST['magazine_breaking_news']) ? 1 : 0);
        if (isset($_POST['magazine_featured_categories'])) {
            update_post_meta($post_id, '_magazine_featured_categories', sanitize_text_field($_POST['magazine_featured_categories']));
        }
    }
}

// Initialize the class
new NosfirNews_Page_Templates();

/**
 * Helper functions for templates
 */

/**
 * Get portfolio items
 */
function nosfirnews_get_portfolio_items($args = array()) {
    $defaults = array(
        'post_type' => 'portfolio',
        'posts_per_page' => 12,
        'post_status' => 'publish'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    return new WP_Query($args);
}

/**
 * Get blog posts for grid
 */
function nosfirnews_get_blog_posts($args = array()) {
    $defaults = array(
        'post_type' => 'post',
        'posts_per_page' => 12,
        'post_status' => 'publish'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    return new WP_Query($args);
}







/**
 * Add media uploader script
 */
function nosfirnews_add_media_uploader_script() {
    global $pagenow;
    
    if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
        wp_enqueue_media();
        ?>
        <script>
        function openMediaUploader(inputId) {
            var mediaUploader = wp.media({
                title: 'Selecionar Imagem',
                button: {
                    text: 'Usar esta imagem'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                document.getElementById(inputId).value = attachment.url;
            });
            
            mediaUploader.open();
        }
        </script>
        <?php
    }
}
add_action('admin_footer', 'nosfirnews_add_media_uploader_script');