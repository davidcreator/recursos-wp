<?php
/**
 * Custom Templates System
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Templates Class
 */
class NosfirNews_Custom_Templates {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    /**
     * Initialize
     */
    public function init() {
        add_action('add_meta_boxes', array($this, 'add_template_metabox'));
        add_action('save_post', array($this, 'save_template_meta'));
        add_filter('template_include', array($this, 'load_custom_template'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_template_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_save_custom_template', array($this, 'ajax_save_template'));
        add_action('wp_ajax_load_template_preview', array($this, 'ajax_load_preview'));
        add_action('wp_ajax_duplicate_template', array($this, 'ajax_duplicate_template'));
    }
    
    /**
     * Add template selection metabox
     */
    public function add_template_metabox() {
        $post_types = array('post', 'page');
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'nosfirnews_custom_template',
                __('Template Personalizado', 'nosfirnews'),
                array($this, 'template_metabox_callback'),
                $post_type,
                'side',
                'high'
            );
        }
    }
    
    /**
     * Template metabox callback
     */
    public function template_metabox_callback($post) {
        wp_nonce_field('nosfirnews_template_nonce', 'nosfirnews_template_nonce');
        
        $current_template = get_post_meta($post->ID, '_nosfirnews_custom_template', true);
        $templates = $this->get_available_templates();
        
        ?>
        <div class="nosfirnews-template-selector">
            <p>
                <label for="nosfirnews_template_select">
                    <strong><?php _e('Selecionar Template:', 'nosfirnews'); ?></strong>
                </label>
            </p>
            
            <select id="nosfirnews_template_select" name="nosfirnews_custom_template" style="width: 100%;">
                <option value=""><?php _e('Template Padrão', 'nosfirnews'); ?></option>
                <?php foreach ($templates as $key => $template): ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($current_template, $key); ?>>
                        <?php echo esc_html($template['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <div class="template-preview" id="template-preview" style="margin-top: 15px;">
                <!-- Preview will be loaded here -->
            </div>
            
            <div class="template-actions" style="margin-top: 15px;">
                <button type="button" class="button" id="create-custom-template">
                    <?php _e('Criar Template Personalizado', 'nosfirnews'); ?>
                </button>
                <button type="button" class="button" id="edit-template" style="display: none;">
                    <?php _e('Editar Template', 'nosfirnews'); ?>
                </button>
            </div>
        </div>
        
        <!-- Custom Template Editor Modal -->
        <div id="template-editor-modal" class="template-modal" style="display: none;">
            <div class="template-modal-content">
                <div class="template-modal-header">
                    <h3><?php _e('Editor de Template', 'nosfirnews'); ?></h3>
                    <span class="template-modal-close">&times;</span>
                </div>
                
                <div class="template-modal-body">
                    <div class="template-editor-tabs">
                        <button type="button" class="tab-button active" data-tab="structure">
                            <?php _e('Estrutura', 'nosfirnews'); ?>
                        </button>
                        <button type="button" class="tab-button" data-tab="styling">
                            <?php _e('Estilo', 'nosfirnews'); ?>
                        </button>
                        <button type="button" class="tab-button" data-tab="settings">
                            <?php _e('Configurações', 'nosfirnews'); ?>
                        </button>
                    </div>
                    
                    <div class="template-editor-content">
                        <!-- Structure Tab -->
                        <div class="tab-content active" data-tab="structure">
                            <div class="template-builder">
                                <div class="template-components">
                                    <h4><?php _e('Componentes Disponíveis', 'nosfirnews'); ?></h4>
                                    <div class="component-list">
                                        <div class="component-item" data-component="header">
                                            <span class="dashicons dashicons-admin-appearance"></span>
                                            <?php _e('Cabeçalho', 'nosfirnews'); ?>
                                        </div>
                                        <div class="component-item" data-component="content">
                                            <span class="dashicons dashicons-edit-page"></span>
                                            <?php _e('Conteúdo', 'nosfirnews'); ?>
                                        </div>
                                        <div class="component-item" data-component="sidebar">
                                            <span class="dashicons dashicons-layout"></span>
                                            <?php _e('Sidebar', 'nosfirnews'); ?>
                                        </div>
                                        <div class="component-item" data-component="footer">
                                            <span class="dashicons dashicons-admin-generic"></span>
                                            <?php _e('Rodapé', 'nosfirnews'); ?>
                                        </div>
                                        <div class="component-item" data-component="custom">
                                            <span class="dashicons dashicons-plus-alt"></span>
                                            <?php _e('Seção Personalizada', 'nosfirnews'); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="template-canvas">
                                    <h4><?php _e('Estrutura do Template', 'nosfirnews'); ?></h4>
                                    <div class="canvas-area" id="template-canvas">
                                        <!-- Template structure will be built here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Styling Tab -->
                        <div class="tab-content" data-tab="styling">
                            <div class="styling-options">
                                <h4><?php _e('Opções de Estilo', 'nosfirnews'); ?></h4>
                                
                                <div class="style-group">
                                    <label><?php _e('Layout:', 'nosfirnews'); ?></label>
                                    <select name="template_layout">
                                        <option value="full-width"><?php _e('Largura Total', 'nosfirnews'); ?></option>
                                        <option value="boxed"><?php _e('Boxed', 'nosfirnews'); ?></option>
                                        <option value="centered"><?php _e('Centralizado', 'nosfirnews'); ?></option>
                                    </select>
                                </div>
                                
                                <div class="style-group">
                                    <label><?php _e('Cor de Fundo:', 'nosfirnews'); ?></label>
                                    <input type="color" name="template_bg_color" value="#ffffff">
                                </div>
                                
                                <div class="style-group">
                                    <label><?php _e('Espaçamento:', 'nosfirnews'); ?></label>
                                    <input type="range" name="template_spacing" min="0" max="100" value="20">
                                    <span class="range-value">20px</span>
                                </div>
                                
                                <div class="style-group">
                                    <label><?php _e('CSS Personalizado:', 'nosfirnews'); ?></label>
                                    <textarea name="template_custom_css" rows="10" placeholder="/* Adicione seu CSS personalizado aqui */"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Settings Tab -->
                        <div class="tab-content" data-tab="settings">
                            <div class="template-settings">
                                <h4><?php _e('Configurações do Template', 'nosfirnews'); ?></h4>
                                
                                <div class="setting-group">
                                    <label>
                                        <input type="text" name="template_name" placeholder="<?php _e('Nome do Template', 'nosfirnews'); ?>">
                                    </label>
                                </div>
                                
                                <div class="setting-group">
                                    <label>
                                        <textarea name="template_description" placeholder="<?php _e('Descrição do Template', 'nosfirnews'); ?>"></textarea>
                                    </label>
                                </div>
                                
                                <div class="setting-group">
                                    <label>
                                        <input type="checkbox" name="template_responsive" checked>
                                        <?php _e('Design Responsivo', 'nosfirnews'); ?>
                                    </label>
                                </div>
                                
                                <div class="setting-group">
                                    <label>
                                        <input type="checkbox" name="template_seo_optimized" checked>
                                        <?php _e('Otimizado para SEO', 'nosfirnews'); ?>
                                    </label>
                                </div>
                                
                                <div class="setting-group">
                                    <label><?php _e('Aplicar a:', 'nosfirnews'); ?></label>
                                    <select name="template_apply_to" multiple>
                                        <option value="post"><?php _e('Posts', 'nosfirnews'); ?></option>
                                        <option value="page"><?php _e('Páginas', 'nosfirnews'); ?></option>
                                        <option value="archive"><?php _e('Arquivos', 'nosfirnews'); ?></option>
                                        <option value="single"><?php _e('Posts Individuais', 'nosfirnews'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="template-modal-footer">
                    <button type="button" class="button button-secondary" id="cancel-template">
                        <?php _e('Cancelar', 'nosfirnews'); ?>
                    </button>
                    <button type="button" class="button button-primary" id="save-template">
                        <?php _e('Salvar Template', 'nosfirnews'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save template meta
     */
    public function save_template_meta($post_id) {
        if (!isset($_POST['nosfirnews_template_nonce']) || 
            !wp_verify_nonce($_POST['nosfirnews_template_nonce'], 'nosfirnews_template_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $template = sanitize_text_field($_POST['nosfirnews_custom_template'] ?? '');
        
        if ($template) {
            update_post_meta($post_id, '_nosfirnews_custom_template', $template);
        } else {
            delete_post_meta($post_id, '_nosfirnews_custom_template');
        }
    }
    
    /**
     * Load custom template
     */
    public function load_custom_template($template) {
        global $post;
        
        if (!$post) {
            return $template;
        }
        
        $custom_template = get_post_meta($post->ID, '_nosfirnews_custom_template', true);
        
        if ($custom_template) {
            $template_file = $this->get_template_file($custom_template);
            
            if ($template_file && file_exists($template_file)) {
                return $template_file;
            }
        }
        
        return $template;
    }
    
    /**
     * Get available templates
     */
    public function get_available_templates() {
        $templates = array(
            'magazine' => array(
                'name' => __('Magazine Layout', 'nosfirnews'),
                'description' => __('Layout estilo revista com múltiplas colunas', 'nosfirnews'),
                'file' => 'magazine.php',
                'preview' => 'magazine-preview.jpg'
            ),
            'minimal' => array(
                'name' => __('Minimal Clean', 'nosfirnews'),
                'description' => __('Design minimalista e limpo', 'nosfirnews'),
                'file' => 'minimal.php',
                'preview' => 'minimal-preview.jpg'
            ),
            'grid' => array(
                'name' => __('Grid Layout', 'nosfirnews'),
                'description' => __('Layout em grade para posts', 'nosfirnews'),
                'file' => 'grid.php',
                'preview' => 'grid-preview.jpg'
            ),
            'fullwidth' => array(
                'name' => __('Full Width', 'nosfirnews'),
                'description' => __('Layout de largura total', 'nosfirnews'),
                'file' => 'fullwidth.php',
                'preview' => 'fullwidth-preview.jpg'
            ),
            'sidebar-left' => array(
                'name' => __('Sidebar Esquerda', 'nosfirnews'),
                'description' => __('Layout com sidebar à esquerda', 'nosfirnews'),
                'file' => 'sidebar-left.php',
                'preview' => 'sidebar-left-preview.jpg'
            ),
            'landing' => array(
                'name' => __('Landing Page', 'nosfirnews'),
                'description' => __('Template para páginas de destino', 'nosfirnews'),
                'file' => 'landing.php',
                'preview' => 'landing-preview.jpg'
            )
        );
        
        return apply_filters('nosfirnews_custom_templates', $templates);
    }
    
    /**
     * Get template file path
     */
    public function get_template_file($template_key) {
        $templates = $this->get_available_templates();
        
        if (isset($templates[$template_key])) {
            $template_dir = get_template_directory() . '/templates/';
            return $template_dir . $templates[$template_key]['file'];
        }
        
        return false;
    }
    
    /**
     * Enqueue template assets
     */
    public function enqueue_template_assets() {
        global $post;
        
        if (!$post) {
            return;
        }
        
        $custom_template = get_post_meta($post->ID, '_nosfirnews_custom_template', true);
        
        if ($custom_template) {
            $template_css = get_template_directory_uri() . '/assets/css/templates/' . $custom_template . '.css';
            $template_js = get_template_directory_uri() . '/assets/js/templates/' . $custom_template . '.js';
            
            if (file_exists(get_template_directory() . '/assets/css/templates/' . $custom_template . '.css')) {
                wp_enqueue_style('nosfirnews-template-' . $custom_template, $template_css, array(), '1.0.0');
            }
            
            if (file_exists(get_template_directory() . '/assets/js/templates/' . $custom_template . '.js')) {
                wp_enqueue_script('nosfirnews-template-' . $custom_template, $template_js, array('jquery'), '1.0.0', true);
            }
        }
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'nosfirnews-custom-templates',
            get_template_directory_uri() . '/assets/css/custom-templates.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'nosfirnews-custom-templates',
            get_template_directory_uri() . '/assets/js/custom-templates.js',
            array('jquery', 'jquery-ui-sortable'),
            '1.0.0',
            true
        );
        
        wp_localize_script('nosfirnews-custom-templates', 'nosfirnews_templates', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nosfirnews_templates_nonce'),
            'strings' => array(
                'save_success' => __('Template salvo com sucesso!', 'nosfirnews'),
                'save_error' => __('Erro ao salvar template.', 'nosfirnews'),
                'confirm_delete' => __('Tem certeza que deseja excluir este template?', 'nosfirnews')
            )
        ));
    }
    
    /**
     * AJAX: Save custom template
     */
    public function ajax_save_template() {
        check_ajax_referer('nosfirnews_templates_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_die(__('Permissão negada.', 'nosfirnews'));
        }
        
        $template_data = $_POST['template_data'] ?? array();
        $template_name = sanitize_text_field($template_data['name'] ?? '');
        
        if (empty($template_name)) {
            wp_send_json_error(__('Nome do template é obrigatório.', 'nosfirnews'));
        }
        
        // Save template to database or file system
        $template_id = $this->save_custom_template_data($template_data);
        
        if ($template_id) {
            wp_send_json_success(array(
                'message' => __('Template salvo com sucesso!', 'nosfirnews'),
                'template_id' => $template_id
            ));
        } else {
            wp_send_json_error(__('Erro ao salvar template.', 'nosfirnews'));
        }
    }
    
    /**
     * AJAX: Load template preview
     */
    public function ajax_load_preview() {
        check_ajax_referer('nosfirnews_templates_nonce', 'nonce');
        
        $template_key = sanitize_text_field($_POST['template'] ?? '');
        $templates = $this->get_available_templates();
        
        if (isset($templates[$template_key])) {
            $preview_url = get_template_directory_uri() . '/assets/images/template-previews/' . $templates[$template_key]['preview'];
            
            ob_start();
            ?>
            <div class="template-preview-content">
                <img src="<?php echo esc_url($preview_url); ?>" alt="<?php echo esc_attr($templates[$template_key]['name']); ?>">
                <h4><?php echo esc_html($templates[$template_key]['name']); ?></h4>
                <p><?php echo esc_html($templates[$template_key]['description']); ?></p>
            </div>
            <?php
            $preview_html = ob_get_clean();
            
            wp_send_json_success($preview_html);
        } else {
            wp_send_json_error(__('Template não encontrado.', 'nosfirnews'));
        }
    }
    
    /**
     * AJAX: Duplicate template
     */
    public function ajax_duplicate_template() {
        check_ajax_referer('nosfirnews_templates_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_die(__('Permissão negada.', 'nosfirnews'));
        }
        
        $template_id = intval($_POST['template_id'] ?? 0);
        
        if ($template_id) {
            $new_template_id = $this->duplicate_template($template_id);
            
            if ($new_template_id) {
                wp_send_json_success(array(
                    'message' => __('Template duplicado com sucesso!', 'nosfirnews'),
                    'new_template_id' => $new_template_id
                ));
            }
        }
        
        wp_send_json_error(__('Erro ao duplicar template.', 'nosfirnews'));
    }
    
    /**
     * Save custom template data
     */
    private function save_custom_template_data($data) {
        // Implementation for saving template data
        // This could save to database, file system, or both
        return true; // Return template ID on success
    }
    
    /**
     * Duplicate template
     */
    private function duplicate_template($template_id) {
        // Implementation for duplicating template
        return true; // Return new template ID on success
    }
    
    /**
     * Generate template file
     */
    public function generate_template_file($template_data) {
        $template_content = $this->build_template_content($template_data);
        $template_file = get_template_directory() . '/templates/' . sanitize_file_name($template_data['name']) . '.php';
        
        return file_put_contents($template_file, $template_content);
    }
    
    /**
     * Build template content
     */
    private function build_template_content($data) {
        ob_start();
        ?>
<?php
/**
 * Custom Template: <?php echo esc_html($data['name']); ?>
 * 
 * @package NosfirNews
 */

get_header(); ?>

<div class="custom-template-wrapper <?php echo esc_attr($data['layout'] ?? 'full-width'); ?>">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('custom-template-content'); ?>>
            <?php
            // Render template components based on structure
            if (isset($data['structure'])) {
                foreach ($data['structure'] as $component) {
                    $this->render_template_component($component);
                }
            }
            ?>
        </article>
        
    <?php endwhile; endif; ?>
</div>

<?php if (isset($data['custom_css']) && !empty($data['custom_css'])): ?>
<style>
<?php echo wp_strip_all_tags($data['custom_css']); ?>
</style>
<?php endif; ?>

<?php get_footer(); ?>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render template component
     */
    private function render_template_component($component) {
        switch ($component['type']) {
            case 'header':
                echo '<header class="template-header">';
                the_title('<h1 class="entry-title">', '</h1>');
                echo '</header>';
                break;
                
            case 'content':
                echo '<div class="template-content">';
                the_content();
                echo '</div>';
                break;
                
            case 'sidebar':
                echo '<aside class="template-sidebar">';
                dynamic_sidebar('sidebar-1');
                echo '</aside>';
                break;
                
            case 'footer':
                echo '<footer class="template-footer">';
                echo '<p>&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '</p>';
                echo '</footer>';
                break;
                
            case 'custom':
                if (isset($component['content'])) {
                    echo '<div class="template-custom-section">';
                    echo wp_kses_post($component['content']);
                    echo '</div>';
                }
                break;
        }
    }
}

// Initialize
new NosfirNews_Custom_Templates();