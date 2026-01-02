<?php
/**
 * Classe Principal do AI Post Generator
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AI_Post_Generator {
    
    private static $instance = null;
    private $content_generator;
    private $image_generator;
    
    /**
     * Singleton
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
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Carrega dependências
     */
    private function load_dependencies() {
        $this->content_generator = new AIPG_Content_Generator();
        $this->image_generator = new AIPG_Image_Generator();
    }
    
    /**
     * Inicializa hooks do WordPress
     */
    private function init_hooks() {
        // Menu admin
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handlers - Posts
        add_action('wp_ajax_aipg_generate_post', array($this, 'ajax_generate_post'));
        add_action('wp_ajax_aipg_generate_content_only', array($this, 'ajax_generate_content_only'));
        
        // AJAX handlers - Imagens
        add_action('wp_ajax_aipg_generate_image', array($this->image_generator, 'ajax_generate_image'));
        add_action('wp_ajax_aipg_get_recent_images', array($this->image_generator, 'ajax_get_recent_images'));
        add_action('wp_ajax_aipg_get_image_stats', array($this->image_generator, 'ajax_get_image_stats'));
        add_action('wp_ajax_aipg_test_image_provider', array($this->image_generator, 'ajax_test_image_provider'));
        
        // AJAX handlers - Templates
        add_action('wp_ajax_aipg_save_template', array($this, 'ajax_save_template'));
        add_action('wp_ajax_aipg_delete_template', array($this, 'ajax_delete_template'));
        add_action('wp_ajax_aipg_get_template', array($this, 'ajax_get_template'));
        
        // Settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Agendamento
        add_action('aipg_scheduled_post', array($this, 'process_scheduled_post'), 10, 1);
        
        // Editor integration
        add_action('add_meta_boxes', array($this, 'add_editor_meta_box'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_gutenberg_assets'));
        
        // Coluna de posts
        add_filter('manage_posts_columns', array($this, 'add_posts_column'));
        add_action('manage_posts_custom_column', array($this, 'render_posts_column'), 10, 2);
    }
    
    /**
     * Adiciona menus do admin
     */
    public function add_admin_menu() {
        add_menu_page(
            __('AI Post Generator', 'ai-post-generator'),
            __('AI Posts', 'ai-post-generator'),
            'manage_options',
            'ai-post-generator',
            array($this, 'render_admin_page'),
            'dashicons-edit-large',
            30
        );
        
        add_submenu_page(
            'ai-post-generator',
            __('Gerar Post', 'ai-post-generator'),
            __('Gerar Post', 'ai-post-generator'),
            'publish_posts',
            'ai-post-generator',
            array($this, 'render_admin_page')
        );

        add_submenu_page(
            'ai-post-generator',
            __('Gerenciar Imagens', 'ai-post-generator'),
            __('Imagens IA', 'ai-post-generator'),
            'upload_files',
            'ai-post-generator-images',
            array($this, 'render_image_manager_page')
        );
        
        add_submenu_page(
            'ai-post-generator',
            __('Configurações', 'ai-post-generator'),
            __('Configurações', 'ai-post-generator'),
            'manage_options',
            'ai-post-generator-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Enfileira scripts e estilos
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'ai-post-generator') !== false) {
            wp_enqueue_media();
            wp_enqueue_style('aipg-admin-style', AIPG_PLUGIN_URL . 'assets/admin-style.css', array(), AIPG_VERSION);
            wp_enqueue_script('aipg-admin-script', AIPG_PLUGIN_URL . 'assets/admin-script.js', array('jquery'), AIPG_VERSION, true);
            
            wp_localize_script('aipg-admin-script', 'aipgAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aipg_generate_post'),
                'admin_url' => admin_url(),
                'strings' => array(
                    'generating' => __('Gerando post...', 'ai-post-generator'),
                    'generating_image' => __('Gerando imagem...', 'ai-post-generator'),
                    'success' => __('Post gerado com sucesso!', 'ai-post-generator'),
                    'error' => __('Erro ao gerar post.', 'ai-post-generator'),
                    'confirm_delete' => __('Tem certeza que deseja excluir este template?', 'ai-post-generator')
                )
            ));
        }
        
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            wp_enqueue_style('aipg-editor-style', AIPG_PLUGIN_URL . 'assets/editor-style.css', array(), AIPG_VERSION);
            wp_enqueue_script('aipg-editor-script', AIPG_PLUGIN_URL . 'assets/editor-script.js', array('jquery'), AIPG_VERSION, true);
            
            wp_localize_script('aipg-editor-script', 'aipgEditor', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aipg_generate_post'),
                'post_id' => get_the_ID(),
                'strings' => array(
                    'generating' => __('Gerando conteúdo...', 'ai-post-generator'),
                    'success' => __('Conteúdo gerado!', 'ai-post-generator'),
                    'error' => __('Erro ao gerar conteúdo.', 'ai-post-generator'),
                    'fill_topic' => __('Por favor, preencha o tópico.', 'ai-post-generator')
                )
            ));
        }
    }
    
    /**
     * Registra configurações
     */
    public function register_settings() {
        $settings = array(
            'aipg_api_provider', 'aipg_groq_model', 'aipg_openai_key', 'aipg_anthropic_key',
            'aipg_groq_key', 'aipg_cohere_key', 'aipg_huggingface_key', 'aipg_mistral_key',
            'aipg_unsplash_key', 'aipg_image_provider', 'aipg_pexels_key', 'aipg_pixabay_key',
            'aipg_stability_key', 'aipg_image_width', 'aipg_image_height', 'aipg_default_category',
            'aipg_post_status', 'aipg_default_author', 'aipg_auto_tags', 'aipg_seo_optimization',
            'aipg_auto_featured_image', 'aipg_add_internal_links', 'aipg_disable_ssl_verify'
        );
        
        foreach ($settings as $setting) {
            register_setting('aipg_settings', $setting);
        }
    }
    
    /**
     * Renderiza páginas
     */
    public function render_admin_page() {
        include AIPG_INCLUDES_DIR . 'pages/admin-page.php';
    }
    
    public function render_settings_page() {
        include AIPG_INCLUDES_DIR . 'pages/settings-page.php';
    }
    
    public function render_image_manager_page() {
        include AIPG_INCLUDES_DIR . 'pages/image-manager-page.php';
    }
    
    /**
     * AJAX: Gera post completo
     */
    public function ajax_generate_post() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        if (!current_user_can('publish_posts')) {
            wp_send_json_error(array('message' => __('Permissão negada', 'ai-post-generator')));
        }
        
        $data = $this->sanitize_post_data($_POST);
        
        if ($data['schedule_post'] && !empty($data['schedule_date'])) {
            $this->schedule_post_generation($data);
            wp_send_json_success(array('message' => __('Post agendado com sucesso!', 'ai-post-generator')));
            return;
        }
        
        $result = $this->content_generator->generate($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        $post_id = $this->create_post($result, $data);
        
        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => $post_id->get_error_message()));
        }
        
        if ($data['generate_image']) {
            $image_id = $this->image_generator->generate($data['topic'], $post_id);
            if ($image_id && !is_wp_error($image_id)) {
                set_post_thumbnail($post_id, $image_id);
            }
        }
        
        wp_send_json_success(array(
            'message' => __('Post gerado com sucesso!', 'ai-post-generator'),
            'post_id' => $post_id,
            'edit_url' => get_edit_post_link($post_id, 'raw'),
            'view_url' => get_permalink($post_id),
            'title' => $result['title'],
            'content' => $result['content']
        ));
    }
    
    /**
     * AJAX: Gera apenas conteúdo (sem criar post)
     */
    public function ajax_generate_content_only() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permissão negada', 'ai-post-generator')));
        }
        
        $data = $this->sanitize_post_data($_POST);
        $result = $this->content_generator->generate($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success($result);
    }
    
    /**
     * AJAX: Salva template
     */
    public function ajax_save_template() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        $template = array(
            'name' => sanitize_text_field($_POST['template_name']),
            'tone' => sanitize_text_field($_POST['tone']),
            'length' => sanitize_text_field($_POST['length']),
            'keywords' => sanitize_text_field($_POST['keywords']),
            'category' => intval($_POST['category']),
            'language' => sanitize_text_field($_POST['language'])
        );
        
        $templates = get_option('aipg_templates', array());
        $template_id = 'tpl_' . time();
        $templates[$template_id] = $template;
        
        update_option('aipg_templates', $templates);
        
        wp_send_json_success(array('message' => __('Template salvo!', 'ai-post-generator')));
    }
    
    /**
     * AJAX: Deleta template
     */
    public function ajax_delete_template() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        $template_id = sanitize_text_field($_POST['template_id']);
        $templates = get_option('aipg_templates', array());
        
        if (isset($templates[$template_id])) {
            unset($templates[$template_id]);
            update_option('aipg_templates', $templates);
            wp_send_json_success(array('message' => __('Template excluído!', 'ai-post-generator')));
        } else {
            wp_send_json_error(array('message' => __('Template não encontrado', 'ai-post-generator')));
        }
    }
    
    /**
     * AJAX: Busca template
     */
    public function ajax_get_template() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        $template_id = sanitize_text_field($_POST['template_id']);
        $templates = get_option('aipg_templates', array());
        
        if (isset($templates[$template_id])) {
            wp_send_json_success($templates[$template_id]);
        } else {
            wp_send_json_error(array('message' => __('Template não encontrado', 'ai-post-generator')));
        }
    }
    
    /**
     * Meta box do editor
     */
    public function add_editor_meta_box() {
        add_meta_box(
            'aipg_editor_meta_box',
            '✨ ' . __('Gerar Conteúdo com IA', 'ai-post-generator'),
            array($this, 'render_editor_meta_box'),
            'post',
            'side',
            'high'
        );
    }
    
    public function render_editor_meta_box($post) {
        $templates = get_option('aipg_templates', array());
        include AIPG_INCLUDES_DIR . 'pages/editor-meta-box.php';
    }
    
    /**
     * Gutenberg assets
     */
    public function enqueue_gutenberg_assets() {
        wp_enqueue_script(
            'aipg-gutenberg',
            AIPG_PLUGIN_URL . 'assets/gutenberg-plugin.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'),
            AIPG_VERSION,
            true
        );
        
        wp_localize_script('aipg-gutenberg', 'aipgGutenberg', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aipg_generate_post')
        ));
    }
    
    /**
     * Coluna na lista de posts
     */
    public function add_posts_column($columns) {
        $columns['aipg_generated'] = __('Gerado por IA', 'ai-post-generator');
        return $columns;
    }
    
    public function render_posts_column($column, $post_id) {
        if ($column === 'aipg_generated') {
            $is_generated = get_post_meta($post_id, '_aipg_generated', true);
            if ($is_generated) {
                echo '<span class="dashicons dashicons-yes-alt" style="color:#46b450"></span>';
            }
        }
    }
    
    /**
     * Helpers privados
     */
    private function sanitize_post_data($post_data) {
        return array(
            'topic' => sanitize_text_field($post_data['topic'] ?? ''),
            'keywords' => sanitize_text_field($post_data['keywords'] ?? ''),
            'tone' => sanitize_text_field($post_data['tone'] ?? 'professional'),
            'length' => sanitize_text_field($post_data['length'] ?? 'medium'),
            'language' => sanitize_text_field($post_data['language'] ?? 'pt-br'),
            'category' => intval($post_data['category'] ?? 0),
            'generate_image' => isset($post_data['generate_image']),
            'auto_tags' => isset($post_data['auto_tags']),
            'seo_optimization' => isset($post_data['seo_optimization']),
            'add_internal_links' => isset($post_data['add_internal_links']),
            'schedule_post' => isset($post_data['schedule_post']),
            'schedule_date' => sanitize_text_field($post_data['schedule_date'] ?? '')
        );
    }
    
    private function create_post($result, $data) {
        $post_data = array(
            'post_title' => $result['title'],
            'post_content' => $result['content'],
            'post_status' => get_option('aipg_post_status', 'draft'),
            'post_author' => get_option('aipg_default_author', get_current_user_id()),
            'post_category' => $data['category'] ? array($data['category']) : array()
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_aipg_generated', '1');
            update_post_meta($post_id, '_aipg_generation_date', current_time('mysql'));
            
            if ($data['auto_tags'] && !empty($result['tags'])) {
                wp_set_post_tags($post_id, $result['tags'], false);
            }
            
            if ($data['seo_optimization'] && !empty($result['seo'])) {
                update_post_meta($post_id, '_yoast_wpseo_metadesc', $result['seo']['description']);
                update_post_meta($post_id, '_yoast_wpseo_title', $result['seo']['title']);
            }
        }
        
        return $post_id;
    }
    
    private function schedule_post_generation($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'aipg_scheduled';
        
        $wpdb->insert($table, array(
            'topic' => $data['topic'],
            'config' => json_encode($data),
            'schedule_date' => date('Y-m-d H:i:s', strtotime($data['schedule_date'])),
            'status' => 'pending'
        ));
        
        wp_schedule_single_event(
            strtotime($data['schedule_date']), 
            'aipg_scheduled_post', 
            array($wpdb->insert_id)
        );
    }
    
    public function process_scheduled_post($scheduled_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'aipg_scheduled';
        
        $scheduled = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $scheduled_id));
        
        if (!$scheduled) {
            return;
        }
        
        $data = json_decode($scheduled->config, true);
        $result = $this->content_generator->generate($data);
        
        if (is_wp_error($result)) {
            $wpdb->update($table, array('status' => 'failed'), array('id' => $scheduled_id));
            return;
        }
        
        $post_id = $this->create_post($result, $data);
        
        if (!is_wp_error($post_id)) {
            $wpdb->update($table, array(
                'status' => 'completed', 
                'post_id' => $post_id
            ), array('id' => $scheduled_id));
        }
    }
}
