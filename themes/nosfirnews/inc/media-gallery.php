<?php
/**
 * Advanced Media Gallery System
 * 
 * @package NosfirNews
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class NosfirNews_Media_Gallery {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_gallery_meta'));
        add_action('wp_ajax_upload_gallery_image', array($this, 'ajax_upload_gallery_image'));
        add_action('wp_ajax_delete_gallery_image', array($this, 'ajax_delete_gallery_image'));
        add_action('wp_ajax_reorder_gallery_images', array($this, 'ajax_reorder_gallery_images'));
        add_action('wp_ajax_get_gallery_images', array($this, 'ajax_get_gallery_images'));
        add_shortcode('nosfirnews_gallery', array($this, 'gallery_shortcode'));
        add_filter('post_gallery', array($this, 'custom_gallery_output'), 10, 2);
    }
    
    public function init() {
        // Register custom image sizes for gallery (proporções 16:9 e quadradas)
        add_image_size('gallery-thumb', 300, 300, true);        // Quadrado para thumbnails
        add_image_size('gallery-medium', 600, 338, true);       // 16:9 para visualização média
        add_image_size('gallery-large', 1200, 675, true);       // 16:9 para visualização grande
        add_image_size('gallery-full', 1920, 1080, false);      // 16:9 para tela cheia
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script(
            'nosfirnews-media-gallery',
            get_template_directory_uri() . '/assets/js/media-gallery.js',
            array('jquery', 'wp-util'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'nosfirnews-media-gallery',
            get_template_directory_uri() . '/assets/css/media-gallery.css',
            array(),
            '1.0.0'
        );
        
        // Localize script
        wp_localize_script('nosfirnews-media-gallery', 'mediaGalleryData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('media_gallery_nonce'),
            'strings' => array(
                'loading' => __('Carregando...', 'nosfirnews'),
                'error' => __('Erro ao carregar imagem', 'nosfirnews'),
                'deleteConfirm' => __('Tem certeza que deseja excluir esta imagem?', 'nosfirnews'),
                'uploadError' => __('Erro no upload da imagem', 'nosfirnews'),
                'maxFiles' => __('Número máximo de arquivos excedido', 'nosfirnews'),
                'invalidType' => __('Tipo de arquivo inválido', 'nosfirnews'),
                'fileTooLarge' => __('Arquivo muito grande', 'nosfirnews')
            )
        ));
    }
    
    public function admin_enqueue_scripts($hook) {
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            wp_enqueue_media();
            wp_enqueue_script('jquery-ui-sortable');
            
            wp_enqueue_script(
                'nosfirnews-gallery-admin',
                get_template_directory_uri() . '/assets/js/gallery-admin.js',
                array('jquery', 'jquery-ui-sortable', 'wp-util'),
                '1.0.0',
                true
            );
            
            wp_enqueue_style(
                'nosfirnews-gallery-admin',
                get_template_directory_uri() . '/assets/css/gallery-admin.css',
                array(),
                '1.0.0'
            );
        }
    }
    
    public function add_meta_boxes() {
        $post_types = array('post', 'page');
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'nosfirnews_gallery',
                __('Galeria de Mídia Avançada', 'nosfirnews'),
                array($this, 'gallery_meta_box_callback'),
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    public function gallery_meta_box_callback($post) {
        wp_nonce_field('nosfirnews_gallery_meta', 'nosfirnews_gallery_nonce');
        
        $gallery_images = get_post_meta($post->ID, '_nosfirnews_gallery_images', true);
        $gallery_settings = get_post_meta($post->ID, '_nosfirnews_gallery_settings', true);
        
        if (!is_array($gallery_images)) {
            $gallery_images = array();
        }
        
        if (!is_array($gallery_settings)) {
            $gallery_settings = array(
                'layout' => 'grid',
                'columns' => 3,
                'show_captions' => true,
                'lightbox' => true,
                'autoplay' => false,
                'autoplay_speed' => 3000,
                'show_thumbnails' => true,
                'lazy_load' => true,
                'filter_categories' => false
            );
        }
        ?>
        
        <div id="nosfirnews-gallery-admin">
            <div class="gallery-tabs">
                <ul class="gallery-tab-nav">
                    <li><a href="#gallery-images-tab" class="active">Imagens</a></li>
                    <li><a href="#gallery-settings-tab">Configurações</a></li>
                    <li><a href="#gallery-preview-tab">Preview</a></li>
                </ul>
                
                <div id="gallery-images-tab" class="gallery-tab-content active">
                    <div class="gallery-upload-area">
                        <button type="button" class="button button-primary" id="add-gallery-images">
                            Adicionar Imagens
                        </button>
                        <p class="description">
                            Selecione múltiplas imagens para adicionar à galeria. Você pode arrastar para reordenar.
                        </p>
                    </div>
                    
                    <div id="gallery-images-container" class="gallery-images-grid">
                        <?php foreach ($gallery_images as $index => $image_id): ?>
                            <?php $this->render_gallery_image_item($image_id, $index); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div id="gallery-settings-tab" class="gallery-tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Layout</th>
                            <td>
                                <select name="gallery_settings[layout]">
                                    <option value="grid" <?php selected($gallery_settings['layout'], 'grid'); ?>>Grid</option>
                                    <option value="masonry" <?php selected($gallery_settings['layout'], 'masonry'); ?>>Masonry</option>
                                    <option value="slider" <?php selected($gallery_settings['layout'], 'slider'); ?>>Slider</option>
                                    <option value="carousel" <?php selected($gallery_settings['layout'], 'carousel'); ?>>Carousel</option>
                                    <option value="justified" <?php selected($gallery_settings['layout'], 'justified'); ?>>Justified</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Colunas</th>
                            <td>
                                <select name="gallery_settings[columns]">
                                    <option value="1" <?php selected($gallery_settings['columns'], 1); ?>>1</option>
                                    <option value="2" <?php selected($gallery_settings['columns'], 2); ?>>2</option>
                                    <option value="3" <?php selected($gallery_settings['columns'], 3); ?>>3</option>
                                    <option value="4" <?php selected($gallery_settings['columns'], 4); ?>>4</option>
                                    <option value="5" <?php selected($gallery_settings['columns'], 5); ?>>5</option>
                                    <option value="6" <?php selected($gallery_settings['columns'], 6); ?>>6</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Mostrar Legendas</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="gallery_settings[show_captions]" value="1" 
                                           <?php checked($gallery_settings['show_captions']); ?>>
                                    Exibir legendas das imagens
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Lightbox</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="gallery_settings[lightbox]" value="1" 
                                           <?php checked($gallery_settings['lightbox']); ?>>
                                    Ativar lightbox para visualização em tela cheia
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Autoplay (Slider/Carousel)</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="gallery_settings[autoplay]" value="1" 
                                           <?php checked($gallery_settings['autoplay']); ?>>
                                    Reprodução automática
                                </label>
                                <br>
                                <label>
                                    Velocidade (ms): 
                                    <input type="number" name="gallery_settings[autoplay_speed]" 
                                           value="<?php echo esc_attr($gallery_settings['autoplay_speed']); ?>" 
                                           min="1000" max="10000" step="500">
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Miniaturas</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="gallery_settings[show_thumbnails]" value="1" 
                                           <?php checked($gallery_settings['show_thumbnails']); ?>>
                                    Mostrar miniaturas de navegação
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Lazy Loading</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="gallery_settings[lazy_load]" value="1" 
                                           <?php checked($gallery_settings['lazy_load']); ?>>
                                    Carregar imagens sob demanda
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Filtros por Categoria</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="gallery_settings[filter_categories]" value="1" 
                                           <?php checked($gallery_settings['filter_categories']); ?>>
                                    Permitir filtrar imagens por categoria
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div id="gallery-preview-tab" class="gallery-tab-content">
                    <div id="gallery-preview-container">
                        <p>Preview da galeria será exibido aqui...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
    }
    
    private function render_gallery_image_item($image_id, $index) {
        $image = wp_get_attachment_image_src($image_id, 'thumbnail');
        $image_meta = wp_get_attachment_metadata($image_id);
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        $image_caption = wp_get_attachment_caption($image_id);
        
        if (!$image) return;
        ?>
        
        <div class="gallery-image-item" data-image-id="<?php echo esc_attr($image_id); ?>">
            <div class="gallery-image-preview">
                <img src="<?php echo esc_url($image[0]); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                <div class="gallery-image-overlay">
                    <button type="button" class="button-link gallery-image-edit" title="Editar">
                        <span class="dashicons dashicons-edit"></span>
                    </button>
                    <button type="button" class="button-link gallery-image-delete" title="Remover">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                    <span class="gallery-image-handle" title="Arrastar para reordenar">
                        <span class="dashicons dashicons-move"></span>
                    </span>
                </div>
            </div>
            
            <div class="gallery-image-details">
                <input type="hidden" name="gallery_images[]" value="<?php echo esc_attr($image_id); ?>">
                
                <label>
                    Legenda:
                    <input type="text" name="gallery_captions[<?php echo esc_attr($image_id); ?>]" 
                           value="<?php echo esc_attr($image_caption); ?>" class="widefat">
                </label>
                
                <label>
                    Texto Alt:
                    <input type="text" name="gallery_alt_texts[<?php echo esc_attr($image_id); ?>]" 
                           value="<?php echo esc_attr($image_alt); ?>" class="widefat">
                </label>
                
                <label>
                    Categoria:
                    <select name="gallery_categories[<?php echo esc_attr($image_id); ?>]">
                        <option value="">Sem categoria</option>
                        <option value="featured">Destaque</option>
                        <option value="portfolio">Portfolio</option>
                        <option value="events">Eventos</option>
                        <option value="products">Produtos</option>
                    </select>
                </label>
            </div>
        </div>
        
        <?php
    }
    
    public function save_gallery_meta($post_id) {
        if (!isset($_POST['nosfirnews_gallery_nonce']) || 
            !wp_verify_nonce($_POST['nosfirnews_gallery_nonce'], 'nosfirnews_gallery_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save gallery images
        if (isset($_POST['gallery_images'])) {
            $gallery_images = array_map('intval', $_POST['gallery_images']);
            update_post_meta($post_id, '_nosfirnews_gallery_images', $gallery_images);
        } else {
            delete_post_meta($post_id, '_nosfirnews_gallery_images');
        }
        
        // Save gallery settings
        if (isset($_POST['gallery_settings'])) {
            $gallery_settings = array_map('sanitize_text_field', $_POST['gallery_settings']);
            update_post_meta($post_id, '_nosfirnews_gallery_settings', $gallery_settings);
        }
        
        // Save image captions
        if (isset($_POST['gallery_captions'])) {
            foreach ($_POST['gallery_captions'] as $image_id => $caption) {
                wp_update_post(array(
                    'ID' => intval($image_id),
                    'post_excerpt' => sanitize_text_field($caption)
                ));
            }
        }
        
        // Save alt texts
        if (isset($_POST['gallery_alt_texts'])) {
            foreach ($_POST['gallery_alt_texts'] as $image_id => $alt_text) {
                update_post_meta(intval($image_id), '_wp_attachment_image_alt', sanitize_text_field($alt_text));
            }
        }
        
        // Save categories
        if (isset($_POST['gallery_categories'])) {
            foreach ($_POST['gallery_categories'] as $image_id => $category) {
                update_post_meta(intval($image_id), '_gallery_category', sanitize_text_field($category));
            }
        }
    }
    
    public function ajax_upload_gallery_image() {
        check_ajax_referer('media_gallery_nonce', 'nonce');
        
        if (!current_user_can('upload_files')) {
            wp_die(__('Você não tem permissão para fazer upload de arquivos.', 'nosfirnews'));
        }
        
        $uploaded_file = $_FILES['file'];
        $upload_overrides = array('test_form' => false);
        
        $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            $attachment = array(
                'post_mime_type' => $movefile['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            
            $attach_id = wp_insert_attachment($attachment, $movefile['file']);
            
            if (!is_wp_error($attach_id)) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                
                wp_send_json_success(array(
                    'id' => $attach_id,
                    'url' => wp_get_attachment_url($attach_id),
                    'thumb' => wp_get_attachment_image_src($attach_id, 'thumbnail')[0]
                ));
            }
        }
        
        wp_send_json_error(__('Erro no upload da imagem', 'nosfirnews'));
    }
    
    public function ajax_delete_gallery_image() {
        check_ajax_referer('media_gallery_nonce', 'nonce');
        
        $image_id = intval($_POST['image_id']);
        
        if (!current_user_can('delete_posts')) {
            wp_send_json_error(__('Você não tem permissão para excluir imagens.', 'nosfirnews'));
        }
        
        if (wp_delete_attachment($image_id, true)) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Erro ao excluir imagem', 'nosfirnews'));
        }
    }
    
    public function ajax_reorder_gallery_images() {
        check_ajax_referer('media_gallery_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $image_ids = array_map('intval', $_POST['image_ids']);
        
        if (!current_user_can('edit_post', $post_id)) {
            wp_send_json_error(__('Você não tem permissão para editar este post.', 'nosfirnews'));
        }
        
        update_post_meta($post_id, '_nosfirnews_gallery_images', $image_ids);
        wp_send_json_success();
    }
    
    public function ajax_get_gallery_images() {
        check_ajax_referer('media_gallery_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $gallery_images = get_post_meta($post_id, '_nosfirnews_gallery_images', true);
        $gallery_settings = get_post_meta($post_id, '_nosfirnews_gallery_settings', true);
        
        if (!is_array($gallery_images)) {
            $gallery_images = array();
        }
        
        $images_data = array();
        foreach ($gallery_images as $image_id) {
            $image_data = wp_get_attachment_image_src($image_id, 'full');
            if ($image_data) {
                $images_data[] = array(
                    'id' => $image_id,
                    'url' => $image_data[0],
                    'width' => $image_data[1],
                    'height' => $image_data[2],
                    'caption' => wp_get_attachment_caption($image_id),
                    'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
                    'category' => get_post_meta($image_id, '_gallery_category', true)
                );
            }
        }
        
        wp_send_json_success(array(
            'images' => $images_data,
            'settings' => $gallery_settings
        ));
    }
    
    public function gallery_shortcode($atts) {
        $atts = shortcode_atts(array(
            'post_id' => get_the_ID(),
            'layout' => '',
            'columns' => '',
            'show_captions' => '',
            'lightbox' => '',
            'class' => ''
        ), $atts);
        
        $post_id = intval($atts['post_id']);
        $gallery_images = get_post_meta($post_id, '_nosfirnews_gallery_images', true);
        $gallery_settings = get_post_meta($post_id, '_nosfirnews_gallery_settings', true);
        
        if (!is_array($gallery_images) || empty($gallery_images)) {
            return '';
        }
        
        // Override settings with shortcode attributes
        if (!empty($atts['layout'])) {
            $gallery_settings['layout'] = $atts['layout'];
        }
        if (!empty($atts['columns'])) {
            $gallery_settings['columns'] = intval($atts['columns']);
        }
        if (!empty($atts['show_captions'])) {
            $gallery_settings['show_captions'] = $atts['show_captions'] === 'true';
        }
        if (!empty($atts['lightbox'])) {
            $gallery_settings['lightbox'] = $atts['lightbox'] === 'true';
        }
        
        return $this->render_gallery($gallery_images, $gallery_settings, $atts['class']);
    }
    
    public function custom_gallery_output($output, $attr) {
        global $post;
        
        // Check if this post has our custom gallery
        $gallery_images = get_post_meta($post->ID, '_nosfirnews_gallery_images', true);
        
        if (is_array($gallery_images) && !empty($gallery_images)) {
            $gallery_settings = get_post_meta($post->ID, '_nosfirnews_gallery_settings', true);
            return $this->render_gallery($gallery_images, $gallery_settings);
        }
        
        return $output;
    }
    
    private function render_gallery($gallery_images, $gallery_settings, $extra_class = '') {
        if (!is_array($gallery_settings)) {
            $gallery_settings = array(
                'layout' => 'grid',
                'columns' => 3,
                'show_captions' => true,
                'lightbox' => true,
                'lazy_load' => true
            );
        }
        
        $layout = $gallery_settings['layout'];
        $columns = intval($gallery_settings['columns']);
        $show_captions = !empty($gallery_settings['show_captions']);
        $lightbox = !empty($gallery_settings['lightbox']);
        $lazy_load = !empty($gallery_settings['lazy_load']);
        
        $gallery_class = "nosfirnews-gallery gallery-{$layout} columns-{$columns}";
        if ($lightbox) {
            $gallery_class .= ' gallery-lightbox';
        }
        if ($lazy_load) {
            $gallery_class .= ' gallery-lazy';
        }
        if ($extra_class) {
            $gallery_class .= ' ' . esc_attr($extra_class);
        }
        
        ob_start();
        ?>
        
        <div class="<?php echo esc_attr($gallery_class); ?>" 
             data-layout="<?php echo esc_attr($layout); ?>"
             data-columns="<?php echo esc_attr($columns); ?>"
             data-lightbox="<?php echo $lightbox ? 'true' : 'false'; ?>"
             data-lazy="<?php echo $lazy_load ? 'true' : 'false'; ?>">
            
            <?php if (!empty($gallery_settings['filter_categories'])): ?>
                <div class="gallery-filters">
                    <button class="filter-btn active" data-filter="*">Todos</button>
                    <button class="filter-btn" data-filter="featured">Destaque</button>
                    <button class="filter-btn" data-filter="portfolio">Portfolio</button>
                    <button class="filter-btn" data-filter="events">Eventos</button>
                    <button class="filter-btn" data-filter="products">Produtos</button>
                </div>
            <?php endif; ?>
            
            <div class="gallery-container">
                <?php foreach ($gallery_images as $image_id): ?>
                    <?php
                    $image_full = wp_get_attachment_image_src($image_id, 'gallery-full');
                    $image_large = wp_get_attachment_image_src($image_id, 'gallery-large');
                    $image_medium = wp_get_attachment_image_src($image_id, 'gallery-medium');
                    $image_thumb = wp_get_attachment_image_src($image_id, 'gallery-thumb');
                    
                    if (!$image_full) continue;
                    
                    $caption = wp_get_attachment_caption($image_id);
                    $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                    $category = get_post_meta($image_id, '_gallery_category', true);
                    
                    $item_class = 'gallery-item';
                    if ($category) {
                        $item_class .= ' category-' . esc_attr($category);
                    }
                    ?>
                    
                    <div class="<?php echo esc_attr($item_class); ?>" data-category="<?php echo esc_attr($category); ?>">
                        <div class="gallery-item-inner">
                            <?php if ($lightbox): ?>
                                <a href="<?php echo esc_url($image_full[0]); ?>" 
                                   class="gallery-lightbox-link"
                                   data-caption="<?php echo esc_attr($caption); ?>"
                                   data-alt="<?php echo esc_attr($alt_text); ?>">
                            <?php endif; ?>
                            
                            <img <?php if ($lazy_load): ?>data-<?php endif; ?>src="<?php echo esc_url($image_medium[0]); ?>"
                                 <?php if ($lazy_load): ?>src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E"<?php endif; ?>
                                 alt="<?php echo esc_attr($alt_text); ?>"
                                 width="<?php echo esc_attr($image_medium[1]); ?>"
                                 height="<?php echo esc_attr($image_medium[2]); ?>"
                                 class="gallery-image <?php echo $lazy_load ? 'lazy' : ''; ?>">
                            
                            <?php if ($show_captions && $caption): ?>
                                <div class="gallery-caption">
                                    <span class="caption-text"><?php echo esc_html($caption); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="gallery-overlay">
                                <span class="gallery-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21 21L16.514 16.506M19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                            
                            <?php if ($lightbox): ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php endforeach; ?>
            </div>
            
            <?php if ($layout === 'slider' && !empty($gallery_settings['show_thumbnails'])): ?>
                <div class="gallery-thumbnails">
                    <?php foreach ($gallery_images as $index => $image_id): ?>
                        <?php
                        $thumb = wp_get_attachment_image_src($image_id, 'gallery-thumb');
                        if ($thumb):
                        ?>
                            <button class="gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>" 
                                    data-slide="<?php echo esc_attr($index); ?>">
                                <img src="<?php echo esc_url($thumb[0]); ?>" 
                                     alt="<?php echo esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)); ?>">
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php
        return ob_get_clean();
    }
}

// Initialize the gallery system
new NosfirNews_Media_Gallery();