<?php
/**
 * Plugin Name: AI Post Generator Pro
 * Plugin URI: https://github.com/davidcreator/ai-post-generator
 * Description: Gera posts automaticamente usando APIs de IA com recursos avançados: agendamento, imagens, SEO e mais
 * Version: 2.0.0
 * Author: David Creator
 * Author URI: https://davidcreator.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-post-generator
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AIPG_VERSION', '2.0.0');
define('AIPG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AIPG_PLUGIN_URL', plugin_dir_url(__FILE__));

class AI_Post_Generator {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_aipg_generate_post', array($this, 'ajax_generate_post'));
        add_action('wp_ajax_aipg_generate_image', array($this, 'ajax_generate_image'));
        add_action('wp_ajax_aipg_save_template', array($this, 'ajax_save_template'));
        add_action('wp_ajax_aipg_delete_template', array($this, 'ajax_delete_template'));
        add_action('wp_ajax_aipg_get_template', array($this, 'ajax_get_template'));
        add_action('wp_ajax_aipg_generate_content_only', array($this, 'ajax_generate_content_only'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('aipg_scheduled_post', array($this, 'process_scheduled_post'), 10, 1);
        
        // Integração com editor de posts
        add_action('add_meta_boxes', array($this, 'add_editor_meta_box'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_gutenberg_assets'));
        
        // Adiciona coluna na lista de posts
        add_filter('manage_posts_columns', array($this, 'add_posts_column'));
        add_action('manage_posts_custom_column', array($this, 'render_posts_column'), 10, 2);
    }
    
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
            __('Posts Agendados', 'ai-post-generator'),
            __('Agendamentos', 'ai-post-generator'),
            'publish_posts',
            'ai-post-generator-schedule',
            array($this, 'render_schedule_page')
        );
        
        add_submenu_page(
            'ai-post-generator',
            __('Templates', 'ai-post-generator'),
            __('Templates', 'ai-post-generator'),
            'publish_posts',
            'ai-post-generator-templates',
            array($this, 'render_templates_page')
        );
        
        add_submenu_page(
            'ai-post-generator',
            __('Histórico', 'ai-post-generator'),
            __('Histórico', 'ai-post-generator'),
            'publish_posts',
            'ai-post-generator-history',
            array($this, 'render_history_page')
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
    
    public function enqueue_admin_scripts($hook) {
        // Enfileira em páginas do plugin
        if (strpos($hook, 'ai-post-generator') !== false) {
            wp_enqueue_media();
            wp_enqueue_style('aipg-admin-style', AIPG_PLUGIN_URL . 'assets/admin-style.css', array(), AIPG_VERSION);
            wp_enqueue_script('aipg-admin-script', AIPG_PLUGIN_URL . 'assets/admin-script.js', array('jquery'), AIPG_VERSION, true);
            
            wp_localize_script('aipg-admin-script', 'aipgAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aipg_generate_post'),
                'strings' => array(
                    'generating' => __('Gerando post...', 'ai-post-generator'),
                    'generating_image' => __('Gerando imagem...', 'ai-post-generator'),
                    'success' => __('Post gerado com sucesso!', 'ai-post-generator'),
                    'error' => __('Erro ao gerar post.', 'ai-post-generator'),
                    'confirm_delete' => __('Tem certeza que deseja excluir este template?', 'ai-post-generator')
                )
            ));
        }
        
        // Enfileira no editor de posts
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
    
    public function register_settings() {
        register_setting('aipg_settings', 'aipg_api_provider');
        register_setting('aipg_settings', 'aipg_groq_model');
        register_setting('aipg_settings', 'aipg_openai_key');
        register_setting('aipg_settings', 'aipg_anthropic_key');
        register_setting('aipg_settings', 'aipg_groq_key');
        register_setting('aipg_settings', 'aipg_cohere_key');
        register_setting('aipg_settings', 'aipg_huggingface_key');
        register_setting('aipg_settings', 'aipg_mistral_key');
        register_setting('aipg_settings', 'aipg_unsplash_key');
        register_setting('aipg_settings', 'aipg_default_category');
        register_setting('aipg_settings', 'aipg_post_status');
        register_setting('aipg_settings', 'aipg_default_author');
        register_setting('aipg_settings', 'aipg_auto_tags');
        register_setting('aipg_settings', 'aipg_seo_optimization');
        register_setting('aipg_settings', 'aipg_auto_featured_image');
        register_setting('aipg_settings', 'aipg_add_internal_links');
    }
    
    public function render_admin_page() {
        $templates = get_option('aipg_templates', array());
        ?>
        <div class="wrap aipg-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="aipg-container">
                <div class="aipg-form-card">
                    <h2><?php _e('Gerar Novo Post com IA', 'ai-post-generator'); ?></h2>
                    
                    <form id="aipg-generate-form">
                        <?php wp_nonce_field('aipg_generate_post', 'aipg_nonce'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="aipg_template"><?php _e('Template', 'ai-post-generator'); ?></label>
                                </th>
                                <td>
                                    <select id="aipg_template" name="template">
                                        <option value=""><?php _e('Nenhum (configuração manual)', 'ai-post-generator'); ?></option>
                                        <?php foreach ($templates as $id => $template): ?>
                                            <option value="<?php echo esc_attr($id); ?>">
                                                <?php echo esc_html($template['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description">
                                        <?php _e('Use um template pré-configurado ou crie manualmente', 'ai-post-generator'); ?>
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="aipg_topic"><?php _e('Tópico/Assunto *', 'ai-post-generator'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="aipg_topic" name="topic" class="regular-text" required
                                           placeholder="<?php _e('Ex: Como criar um site WordPress', 'ai-post-generator'); ?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="aipg_keywords"><?php _e('Palavras-chave', 'ai-post-generator'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="aipg_keywords" name="keywords" class="regular-text"
                                           placeholder="<?php _e('WordPress, desenvolvimento, tutorial', 'ai-post-generator'); ?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="aipg_tone"><?php _e('Tom do Post', 'ai-post-generator'); ?></label>
                                </th>
                                <td>
                                    <select id="aipg_tone" name="tone">
                                        <option value="professional"><?php _e('Profissional', 'ai-post-generator'); ?></option>
                                        <option value="casual"><?php _e('Casual', 'ai-post-generator'); ?></option>
                                        <option value="technical"><?php _e('Técnico', 'ai-post-generator'); ?></option>
                                        <option value="friendly"><?php _e('Amigável', 'ai-post-generator'); ?></option>
                                        <option value="educational"><?php _e('Educacional', 'ai-post-generator'); ?></option>
                                        <option value="persuasive"><?php _e('Persuasivo', 'ai-post-generator'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="aipg_length"><?php _e('Tamanho', 'ai-post-generator'); ?></label>
                                </th>
                                <td>
                                    <select id="aipg_length" name="length">
                                        <option value="short"><?php _e('Curto (300-500 palavras)', 'ai-post-generator'); ?></option>
                                        <option value="medium" selected><?php _e('Médio (500-800 palavras)', 'ai-post-generator'); ?></option>
                                        <option value="long"><?php _e('Longo (800-1200 palavras)', 'ai-post-generator'); ?></option>
                                        <option value="verylong"><?php _e('Muito Longo (1200-2000 palavras)', 'ai-post-generator'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="aipg_language"><?php _e('Idioma', 'ai-post-generator'); ?></label>
                                </th>
                                <td>
                                    <select id="aipg_language" name="language">
                                        <option value="pt-br" selected><?php _e('Português Brasileiro', 'ai-post-generator'); ?></option>
                                        <option value="en"><?php _e('Inglês', 'ai-post-generator'); ?></option>
                                        <option value="es"><?php _e('Espanhol', 'ai-post-generator'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="aipg_category"><?php _e('Categoria', 'ai-post-generator'); ?></label>
                                </th>
                                <td>
                                    <?php
                                    wp_dropdown_categories(array(
                                        'id' => 'aipg_category',
                                        'name' => 'category',
                                        'selected' => get_option('aipg_default_category', 1),
                                        'show_option_none' => __('Sem categoria', 'ai-post-generator'),
                                        'option_none_value' => '0',
                                        'hide_empty' => false
                                    ));
                                    ?>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Recursos Avançados', 'ai-post-generator'); ?></th>
                                <td>
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" name="generate_image" id="aipg_generate_image" value="1" 
                                                   <?php checked(get_option('aipg_auto_featured_image'), '1'); ?>>
                                            <?php _e('Gerar imagem destacada automaticamente', 'ai-post-generator'); ?>
                                        </label><br>
                                        
                                        <label>
                                            <input type="checkbox" name="auto_tags" id="aipg_auto_tags" value="1"
                                                   <?php checked(get_option('aipg_auto_tags'), '1'); ?>>
                                            <?php _e('Gerar tags automaticamente', 'ai-post-generator'); ?>
                                        </label><br>
                                        
                                        <label>
                                            <input type="checkbox" name="seo_optimization" id="aipg_seo" value="1"
                                                   <?php checked(get_option('aipg_seo_optimization'), '1'); ?>>
                                            <?php _e('Otimizar para SEO (meta description, título SEO)', 'ai-post-generator'); ?>
                                        </label><br>
                                        
                                        <label>
                                            <input type="checkbox" name="add_internal_links" id="aipg_links" value="1"
                                                   <?php checked(get_option('aipg_add_internal_links'), '1'); ?>>
                                            <?php _e('Adicionar links internos para posts relacionados', 'ai-post-generator'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Agendamento', 'ai-post-generator'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="schedule_post" id="aipg_schedule" value="1">
                                        <?php _e('Agendar publicação', 'ai-post-generator'); ?>
                                    </label>
                                    
                                    <div id="aipg_schedule_options" style="display:none; margin-top:10px;">
                                        <input type="datetime-local" name="schedule_date" id="aipg_schedule_date">
                                        <p class="description">
                                            <?php _e('Selecione data e hora para publicação', 'ai-post-generator'); ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="submit" class="button button-primary button-large">
                                <?php _e('Gerar Post', 'ai-post-generator'); ?>
                            </button>
                            
                            <button type="button" id="aipg-save-template" class="button button-secondary">
                                <?php _e('Salvar como Template', 'ai-post-generator'); ?>
                            </button>
                        </p>
                    </form>
                    
                    <div id="aipg-result" style="display:none;">
                        <h3><?php _e('Post Gerado:', 'ai-post-generator'); ?></h3>
                        <div id="aipg-result-content"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function render_schedule_page() {
        global $wpdb;
        $table = $wpdb->prefix . 'aipg_scheduled';
        
        $scheduled = $wpdb->get_results("SELECT * FROM $table ORDER BY schedule_date ASC");
        ?>
        <div class="wrap">
            <h1><?php _e('Posts Agendados', 'ai-post-generator'); ?></h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Tópico', 'ai-post-generator'); ?></th>
                        <th><?php _e('Data Agendada', 'ai-post-generator'); ?></th>
                        <th><?php _e('Status', 'ai-post-generator'); ?></th>
                        <th><?php _e('Ações', 'ai-post-generator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($scheduled)): ?>
                        <tr>
                            <td colspan="4"><?php _e('Nenhum post agendado', 'ai-post-generator'); ?></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($scheduled as $item): ?>
                            <tr>
                                <td><?php echo esc_html($item->topic); ?></td>
                                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item->schedule_date)); ?></td>
                                <td><?php echo esc_html($item->status); ?></td>
                                <td>
                                    <a href="#" class="button button-small"><?php _e('Cancelar', 'ai-post-generator'); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    public function render_templates_page() {
        $templates = get_option('aipg_templates', array());
        ?>
        <div class="wrap">
            <h1><?php _e('Templates Salvos', 'ai-post-generator'); ?></h1>
            
            <div class="aipg-templates-grid">
                <?php if (empty($templates)): ?>
                    <p><?php _e('Nenhum template salvo ainda. Crie um template na página de geração de posts.', 'ai-post-generator'); ?></p>
                <?php else: ?>
                    <?php foreach ($templates as $id => $template): ?>
                        <div class="aipg-template-card" data-template-id="<?php echo esc_attr($id); ?>">
                            <h3><?php echo esc_html($template['name']); ?></h3>
                            <p><strong><?php _e('Tom:', 'ai-post-generator'); ?></strong> <?php echo esc_html($template['tone']); ?></p>
                            <p><strong><?php _e('Tamanho:', 'ai-post-generator'); ?></strong> <?php echo esc_html($template['length']); ?></p>
                            <p><strong><?php _e('Palavras-chave:', 'ai-post-generator'); ?></strong> <?php echo esc_html($template['keywords']); ?></p>
                            
                            <div class="aipg-template-actions">
                                <a href="<?php echo admin_url('admin.php?page=ai-post-generator&template=' . $id); ?>" 
                                   class="button button-primary"><?php _e('Usar', 'ai-post-generator'); ?></a>
                                <button class="button button-danger aipg-delete-template" data-template-id="<?php echo esc_attr($id); ?>">
                                    <?php _e('Excluir', 'ai-post-generator'); ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    public function render_history_page() {
        global $wpdb;
        
        $history = $wpdb->get_results("
            SELECT p.*, pm.meta_value as ai_generated
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_aipg_generated'
            WHERE pm.meta_value = '1'
            ORDER BY p.post_date DESC
            LIMIT 50
        ");
        ?>
        <div class="wrap">
            <h1><?php _e('Histórico de Posts Gerados', 'ai-post-generator'); ?></h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Título', 'ai-post-generator'); ?></th>
                        <th><?php _e('Data', 'ai-post-generator'); ?></th>
                        <th><?php _e('Status', 'ai-post-generator'); ?></th>
                        <th><?php _e('Ações', 'ai-post-generator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="4"><?php _e('Nenhum post gerado ainda', 'ai-post-generator'); ?></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history as $post): ?>
                            <tr>
                                <td><strong><?php echo esc_html($post->post_title); ?></strong></td>
                                <td><?php echo date_i18n(get_option('date_format'), strtotime($post->post_date)); ?></td>
                                <td><?php echo esc_html($post->post_status); ?></td>
                                <td>
                                    <a href="<?php echo get_edit_post_link($post->ID); ?>" class="button button-small">
                                        <?php _e('Editar', 'ai-post-generator'); ?>
                                    </a>
                                    <a href="<?php echo get_permalink($post->ID); ?>" target="_blank" class="button button-small">
                                        <?php _e('Ver', 'ai-post-generator'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('aipg_settings');
                do_settings_sections('aipg_settings');
                ?>
                
                <h2 class="title"><?php _e('Configurações de API', 'ai-post-generator'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="aipg_api_provider"><?php _e('Provedor de IA', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <select id="aipg_api_provider" name="aipg_api_provider" class="aipg-provider-select">
                                <optgroup label="<?php _e('APIs Gratuitas Generosas', 'ai-post-generator'); ?>">
                                    <option value="groq" <?php selected(get_option('aipg_api_provider'), 'groq'); ?>>
                                        🚀 Groq (GRATUITO - Llama 3.3 70B - Ultra Rápido)
                                    </option>
                                    <option value="huggingface" <?php selected(get_option('aipg_api_provider'), 'huggingface'); ?>>
                                        🤗 Hugging Face (GRATUITO - Vários modelos)
                                    </option>
                                    <option value="cohere" <?php selected(get_option('aipg_api_provider'), 'cohere'); ?>>
                                        💎 Cohere (GRATUITO até 1000 req/mês)
                                    </option>
                                    <option value="mistral" <?php selected(get_option('aipg_api_provider'), 'mistral'); ?>>
                                        ⚡ Mistral AI (GRATUITO - Mistral 7B)
                                    </option>
                                </optgroup>
                                <optgroup label="<?php _e('APIs Pagas (Alta Qualidade)', 'ai-post-generator'); ?>">
                                    <option value="openai" <?php selected(get_option('aipg_api_provider'), 'openai'); ?>>
                                        🤖 OpenAI (ChatGPT-4o-mini)
                                    </option>
                                    <option value="anthropic" <?php selected(get_option('aipg_api_provider'), 'anthropic'); ?>>
                                        🧠 Anthropic (Claude 3.5 Sonnet)
                                    </option>
                                </optgroup>
                            </select>
                            <p class="description" id="provider-description"></p>
                        </td>
                    </tr>
                    
                    <tr class="api-key-row" data-provider="groq">
                        <th scope="row">
                            <label for="aipg_groq_key"><?php _e('Chave API Groq', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="aipg_groq_key" name="aipg_groq_key" 
                                   value="<?php echo esc_attr(get_option('aipg_groq_key')); ?>" class="regular-text">
                            <p class="description">
                                ✅ <strong>100% GRATUITO</strong> - Obtenha em: <a href="https://console.groq.com" target="_blank">console.groq.com</a><br>
                                🚀 <strong>Ultra Rápido:</strong> 600+ tokens/segundo<br>
                                📊 <strong>Limite:</strong> 14.400 requisições/dia GRÁTIS
                            </p>
                            
                            <div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border-left: 4px solid #2271b1; border-radius: 4px;">
                                <label for="aipg_groq_model" style="font-weight: 600; display: block; margin-bottom: 10px;">
                                    <?php _e('Modelo Groq:', 'ai-post-generator'); ?>
                                </label>
                                <select id="aipg_groq_model" name="aipg_groq_model" class="regular-text" style="max-width: 100%;">
                                    <option value="llama-3.3-70b-versatile" <?php selected(get_option('aipg_groq_model', 'llama-3.3-70b-versatile'), 'llama-3.3-70b-versatile'); ?>>
                                        🚀 Llama 3.3 70B Versatile (Recomendado) - Mais Recente
                                    </option>
                                    <option value="llama-3.1-70b-versatile" <?php selected(get_option('aipg_groq_model'), 'llama-3.1-70b-versatile'); ?>>
                                        ⚡ Llama 3.1 70B Versatile - Versão Anterior
                                    </option>
                                    <option value="meta-llama/llama-4-scout-17b-16e-instruct" <?php selected(get_option('aipg_groq_model'), 'meta-llama/llama-4-scout-17b-16e-instruct'); ?>>
                                        🔬 Llama 4 Scout 17B - Experimental (Mais Rápido)
                                    </option>
                                    <option value="mixtral-8x7b-32768" <?php selected(get_option('aipg_groq_model'), 'mixtral-8x7b-32768'); ?>>
                                        🎯 Mixtral 8x7B - Contexto Longo (32K tokens)
                                    </option>
                                </select>
                                
                                <div id="groq-model-info" style="margin-top: 10px; padding: 10px; background: #fff; border-radius: 4px; font-size: 13px;">
                                    <strong>ℹ️ Informações do Modelo:</strong>
                                    <div id="groq-model-description"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="api-key-row" data-provider="huggingface">
                        <th scope="row">
                            <label for="aipg_huggingface_key"><?php _e('Token Hugging Face', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="aipg_huggingface_key" name="aipg_huggingface_key" 
                                   value="<?php echo esc_attr(get_option('aipg_huggingface_key')); ?>" class="regular-text">
                            <p class="description">
                                ✅ <strong>100% GRATUITO</strong> - Obtenha em: <a href="https://huggingface.co/settings/tokens" target="_blank">huggingface.co</a><br>
                                🎯 <strong>Sem Limites:</strong> Uso ilimitado grátis<br>
                                🤖 <strong>Modelos:</strong> Mixtral, Falcon, Llama, Zephyr
                            </p>
                        </td>
                    </tr>
                    
                    <tr class="api-key-row" data-provider="cohere">
                        <th scope="row">
                            <label for="aipg_cohere_key"><?php _e('Chave API Cohere', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="aipg_cohere_key" name="aipg_cohere_key" 
                                   value="<?php echo esc_attr(get_option('aipg_cohere_key')); ?>" class="regular-text">
                            <p class="description">
                                ✅ <strong>Plano Grátis Generoso</strong> - Obtenha em: <a href="https://dashboard.cohere.com/api-keys" target="_blank">dashboard.cohere.com</a><br>
                                📊 <strong>Limite:</strong> 1000 requisições/mês GRÁTIS<br>
                                🤖 <strong>Modelo:</strong> Command-R+ (otimizado para texto)
                            </p>
                        </td>
                    </tr>
                    
                    <tr class="api-key-row" data-provider="mistral">
                        <th scope="row">
                            <label for="aipg_mistral_key"><?php _e('Chave API Mistral', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="aipg_mistral_key" name="aipg_mistral_key" 
                                   value="<?php echo esc_attr(get_option('aipg_mistral_key')); ?>" class="regular-text">
                            <p class="description">
                                ✅ <strong>Créditos Gratuitos</strong> - Obtenha em: <a href="https://console.mistral.ai" target="_blank">console.mistral.ai</a><br>
                                💰 <strong>5€ Grátis</strong> para novos usuários<br>
                                🤖 <strong>Modelo:</strong> Mistral 7B / Mixtral 8x7B
                            </p>
                        </td>
                    </tr>
                    
                    <tr class="api-key-row" data-provider="openai">
                        <th scope="row">
                            <label for="aipg_openai_key"><?php _e('Chave API OpenAI', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="aipg_openai_key" name="aipg_openai_key" 
                                   value="<?php echo esc_attr(get_option('aipg_openai_key')); ?>" class="regular-text">
                            <p class="description">
                                💳 <strong>Pago</strong> - Obtenha em: <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com</a><br>
                                💰 <strong>Custo:</strong> ~$0.002/post<br>
                                🤖 <strong>Modelo:</strong> GPT-4o-mini
                            </p>
                        </td>
                    </tr>
                    
                    <tr class="api-key-row" data-provider="anthropic">
                        <th scope="row">
                            <label for="aipg_anthropic_key"><?php _e('Chave API Anthropic', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="aipg_anthropic_key" name="aipg_anthropic_key" 
                                   value="<?php echo esc_attr(get_option('aipg_anthropic_key')); ?>" class="regular-text">
                            <p class="description">
                                💳 <strong>Pago</strong> - Obtenha em: <a href="https://console.anthropic.com" target="_blank">console.anthropic.com</a><br>
                                💰 <strong>Custo:</strong> ~$0.015/post<br>
                                🤖 <strong>Modelo:</strong> Claude 3.5 Sonnet
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="aipg_unsplash_key"><?php _e('Chave API Unsplash', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="aipg_unsplash_key" name="aipg_unsplash_key" 
                                   value="<?php echo esc_attr(get_option('aipg_unsplash_key')); ?>" class="regular-text">
                            <p class="description"><?php _e('Para gerar imagens automaticamente', 'ai-post-generator'); ?> - https://unsplash.com/developers</p>
                        </td>
                    </tr>
                </table>
                
                <h2 class="title"><?php _e('Configurações Padrão', 'ai-post-generator'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="aipg_post_status"><?php _e('Status Padrão do Post', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <select id="aipg_post_status" name="aipg_post_status">
                                <option value="draft" <?php selected(get_option('aipg_post_status', 'draft'), 'draft'); ?>>
                                    <?php _e('Rascunho', 'ai-post-generator'); ?>
                                </option>
                                <option value="publish" <?php selected(get_option('aipg_post_status'), 'publish'); ?>>
                                    <?php _e('Publicado', 'ai-post-generator'); ?>
                                </option>
                                <option value="pending" <?php selected(get_option('aipg_post_status'), 'pending'); ?>>
                                    <?php _e('Pendente', 'ai-post-generator'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="aipg_default_author"><?php _e('Autor Padrão', 'ai-post-generator'); ?></label>
                        </th>
                        <td>
                            <?php
                            wp_dropdown_users(array(
                                'id' => 'aipg_default_author',
                                'name' => 'aipg_default_author',
                                'selected' => get_option('aipg_default_author', get_current_user_id())
                            ));
                            ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Recursos Automáticos', 'ai-post-generator'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="aipg_auto_tags" value="1" 
                                           <?php checked(get_option('aipg_auto_tags'), '1'); ?>>
                                    <?php _e('Gerar tags automaticamente', 'ai-post-generator'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="aipg_seo_optimization" value="1" 
                                           <?php checked(get_option('aipg_seo_optimization'), '1'); ?>>
                                    <?php _e('Otimização SEO automática', 'ai-post-generator'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="aipg_auto_featured_image" value="1" 
                                           <?php checked(get_option('aipg_auto_featured_image'), '1'); ?>>
                                    <?php _e('Gerar imagem destacada automaticamente', 'ai-post-generator'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="aipg_add_internal_links" value="1" 
                                           <?php checked(get_option('aipg_add_internal_links'), '1'); ?>>
                                    <?php _e('Adicionar links internos automaticamente', 'ai-post-generator'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    public function ajax_generate_post() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        if (!current_user_can('publish_posts')) {
            wp_send_json_error(array('message' => __('Permissão negada', 'ai-post-generator')));
        }
        
        $data = array(
            'topic' => sanitize_text_field($_POST['topic']),
            'keywords' => sanitize_text_field($_POST['keywords']),
            'tone' => sanitize_text_field($_POST['tone']),
            'length' => sanitize_text_field($_POST['length']),
            'language' => sanitize_text_field($_POST['language']),
            'category' => intval($_POST['category']),
            'generate_image' => isset($_POST['generate_image']),
            'auto_tags' => isset($_POST['auto_tags']),
            'seo_optimization' => isset($_POST['seo_optimization']),
            'add_internal_links' => isset($_POST['add_internal_links']),
            'schedule_post' => isset($_POST['schedule_post']),
            'schedule_date' => sanitize_text_field($_POST['schedule_date'])
        );
        
        // Se agendado, salva para processar depois
        if ($data['schedule_post'] && !empty($data['schedule_date'])) {
            $this->schedule_post_generation($data);
            wp_send_json_success(array('message' => __('Post agendado com sucesso!', 'ai-post-generator')));
            return;
        }
        
        // Gera conteúdo
        $result = $this->generate_content($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        // Cria post
        $post_data = array(
            'post_title' => $result['title'],
            'post_content' => $result['content'],
            'post_status' => get_option('aipg_post_status', 'draft'),
            'post_author' => get_option('aipg_default_author', get_current_user_id()),
            'post_category' => $data['category'] ? array($data['category']) : array()
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => $post_id->get_error_message()));
        }
        
        // Marca como gerado por IA
        update_post_meta($post_id, '_aipg_generated', '1');
        update_post_meta($post_id, '_aipg_generation_date', current_time('mysql'));
        
        // Gera tags
        if ($data['auto_tags'] && !empty($result['tags'])) {
            wp_set_post_tags($post_id, $result['tags'], false);
        }
        
        // Adiciona meta SEO
        if ($data['seo_optimization'] && !empty($result['seo'])) {
            update_post_meta($post_id, '_yoast_wpseo_metadesc', $result['seo']['description']);
            update_post_meta($post_id, '_yoast_wpseo_title', $result['seo']['title']);
        }
        
        // Gera imagem destacada
        if ($data['generate_image']) {
            $image_id = $this->generate_featured_image($data['topic'], $post_id);
            if ($image_id) {
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
     * Adiciona meta box no editor de posts
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
    
    /**
     * Renderiza meta box no editor
     */
    public function render_editor_meta_box($post) {
        $templates = get_option('aipg_templates', array());
        ?>
        <div class="aipg-editor-box">
            <p class="description">
                <?php _e('Preencha o título do post acima e clique em gerar para criar o conteúdo automaticamente.', 'ai-post-generator'); ?>
            </p>
            
            <div class="aipg-editor-field">
                <label for="aipg_editor_topic">
                    <strong><?php _e('Tópico/Tema:', 'ai-post-generator'); ?></strong>
                </label>
                <input type="text" 
                       id="aipg_editor_topic" 
                       class="widefat" 
                       placeholder="<?php _e('Deixe em branco para usar o título', 'ai-post-generator'); ?>">
                <p class="description"><?php _e('Ou use o título do post automaticamente', 'ai-post-generator'); ?></p>
            </div>
            
            <div class="aipg-editor-field">
                <label for="aipg_editor_template">
                    <strong><?php _e('Template:', 'ai-post-generator'); ?></strong>
                </label>
                <select id="aipg_editor_template" class="widefat">
                    <option value=""><?php _e('Configuração padrão', 'ai-post-generator'); ?></option>
                    <?php foreach ($templates as $id => $template): ?>
                        <option value="<?php echo esc_attr($id); ?>">
                            <?php echo esc_html($template['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="aipg-editor-field">
                <label for="aipg_editor_length">
                    <strong><?php _e('Tamanho:', 'ai-post-generator'); ?></strong>
                </label>
                <select id="aipg_editor_length" class="widefat">
                    <option value="short"><?php _e('Curto (300-500)', 'ai-post-generator'); ?></option>
                    <option value="medium" selected><?php _e('Médio (500-800)', 'ai-post-generator'); ?></option>
                    <option value="long"><?php _e('Longo (800-1200)', 'ai-post-generator'); ?></option>
                    <option value="verylong"><?php _e('Muito Longo (1200-2000)', 'ai-post-generator'); ?></option>
                </select>
            </div>
            
            <div class="aipg-editor-field">
                <label for="aipg_editor_tone">
                    <strong><?php _e('Tom:', 'ai-post-generator'); ?></strong>
                </label>
                <select id="aipg_editor_tone" class="widefat">
                    <option value="professional"><?php _e('Profissional', 'ai-post-generator'); ?></option>
                    <option value="casual"><?php _e('Casual', 'ai-post-generator'); ?></option>
                    <option value="technical"><?php _e('Técnico', 'ai-post-generator'); ?></option>
                    <option value="friendly"><?php _e('Amigável', 'ai-post-generator'); ?></option>
                    <option value="educational"><?php _e('Educacional', 'ai-post-generator'); ?></option>
                </select>
            </div>
            
            <div class="aipg-editor-field">
                <label>
                    <input type="checkbox" id="aipg_editor_image" value="1">
                    <?php _e('Gerar imagem destacada', 'ai-post-generator'); ?>
                </label>
            </div>
            
            <div class="aipg-editor-actions">
                <button type="button" id="aipg_generate_content" class="button button-primary button-large">
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e('Gerar Conteúdo', 'ai-post-generator'); ?>
                </button>
                
                <button type="button" id="aipg_improve_content" class="button button-secondary" style="display:none;">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('Melhorar Texto', 'ai-post-generator'); ?>
                </button>
            </div>
            
            <div id="aipg_editor_status" class="aipg-status" style="display:none;"></div>
        </div>
        <?php
    }
    
    /**
     * Enfileira assets para Gutenberg
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
     * Gera apenas o conteúdo (sem criar post)
     */
    public function ajax_generate_content_only() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permissão negada', 'ai-post-generator')));
        }
        
        $data = array(
            'topic' => sanitize_text_field($_POST['topic']),
            'keywords' => sanitize_text_field($_POST['keywords'] ?? ''),
            'tone' => sanitize_text_field($_POST['tone'] ?? 'professional'),
            'length' => sanitize_text_field($_POST['length'] ?? 'medium'),
            'language' => sanitize_text_field($_POST['language'] ?? 'pt-br'),
            'category' => 0,
            'generate_image' => false,
            'auto_tags' => false,
            'seo_optimization' => false,
            'add_internal_links' => false,
            'schedule_post' => false,
            'schedule_date' => ''
        );
        
        // Gera conteúdo
        $result = $this->generate_content($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success(array(
            'title' => $result['title'],
            'content' => $result['content'],
            'tags' => $result['tags'] ?? array(),
            'seo' => $result['seo'] ?? array()
        ));
    }
    
    private function generate_content($data) {
        $provider = get_option('aipg_api_provider', 'groq');
        
        $length_words = array(
            'short' => '300-500',
            'medium' => '500-800',
            'long' => '800-1200',
            'verylong' => '1200-2000'
        );
        
        $language_map = array(
            'pt-br' => 'português brasileiro',
            'en' => 'inglês',
            'es' => 'espanhol'
        );
        
        $prompt = sprintf(
            "Crie um post de blog em %s sobre '%s'.\n\n",
            $language_map[$data['language']],
            $data['topic']
        );
        
        if (!empty($data['keywords'])) {
            $prompt .= "Palavras-chave: " . $data['keywords'] . "\n";
        }
        
        $prompt .= sprintf(
            "Tom: %s\nTamanho: %s palavras\n\n",
            $data['tone'],
            $length_words[$data['length']]
        );
        
        if ($data['seo_optimization']) {
            $prompt .= "Inclua:\n- Título SEO otimizado (máx 60 caracteres)\n- Meta description (máx 160 caracteres)\n\n";
        }
        
        if ($data['auto_tags']) {
            $prompt .= "Sugira 5-8 tags relevantes.\n\n";
        }
        
        $prompt .= "Retorne em formato JSON:\n{\n  \"title\": \"título do post\",\n  \"content\": \"conteúdo HTML\",\n  \"tags\": [\"tag1\", \"tag2\"],\n  \"seo\": {\"title\": \"título seo\", \"description\": \"meta description\"}\n}";
        
        // Roteamento para o provedor correto
        switch ($provider) {
            case 'groq':
                return $this->generate_with_groq($prompt);
            case 'huggingface':
                return $this->generate_with_huggingface($prompt);
            case 'cohere':
                return $this->generate_with_cohere($prompt);
            case 'mistral':
                return $this->generate_with_mistral($prompt);
            case 'openai':
                return $this->generate_with_openai($prompt);
            case 'anthropic':
                return $this->generate_with_anthropic($prompt);
            default:
                return new WP_Error('no_provider', __('Provedor não configurado', 'ai-post-generator'));
        }
    }
    
    private function generate_with_openai($prompt) {
        $api_key = get_option('aipg_openai_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API OpenAI não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'gpt-4o-mini',
                'messages' => array(
                    array('role' => 'system', 'content' => 'Você é um especialista em criar conteúdo de blog otimizado. Sempre retorne JSON válido.'),
                    array('role' => 'user', 'content' => $prompt)
                ),
                'temperature' => 0.7,
                'response_format' => array('type' => 'json_object')
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message']);
        }
        
        $content = json_decode($body['choices'][0]['message']['content'], true);
        
        return $content;
    }
    
    private function generate_with_anthropic($prompt) {
        $api_key = get_option('aipg_anthropic_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Anthropic não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
            'headers' => array(
                'x-api-key' => $api_key,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 4096,
                'messages' => array(
                    array('role' => 'user', 'content' => $prompt)
                )
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message']);
        }
        
        $content_text = $body['content'][0]['text'];
        $content = json_decode($content_text, true);
        
        return $content;
    }
    
    private function generate_with_groq($prompt) {
        $api_key = get_option('aipg_groq_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Groq não configurada', 'ai-post-generator'));
        }
        
        // Obtém o modelo selecionado (padrão: Llama 3.3 70B)
        $model = get_option('aipg_groq_model', 'llama-3.3-70b-versatile');
        
        // Configurações específicas por modelo
        $model_configs = array(
            'llama-3.3-70b-versatile' => array(
                'max_tokens' => 8000,
                'temperature' => 0.7,
            ),
            'llama-3.1-70b-versatile' => array(
                'max_tokens' => 8000,
                'temperature' => 0.7,
            ),
            'meta-llama/llama-4-scout-17b-16e-instruct' => array(
                'max_tokens' => 4096,
                'temperature' => 0.6, // Mais determinístico para modelo experimental
            ),
            'mixtral-8x7b-32768' => array(
                'max_tokens' => 16000,
                'temperature' => 0.7,
            ),
        );
        
        $config = isset($model_configs[$model]) ? $model_configs[$model] : $model_configs['llama-3.3-70b-versatile'];
        
        $response = wp_remote_post('https://api.groq.com/openai/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'system', 'content' => 'Você é um especialista em criar conteúdo de blog. Sempre retorne JSON válido.'),
                    array('role' => 'user', 'content' => $prompt)
                ),
                'temperature' => $config['temperature'],
                'max_tokens' => $config['max_tokens'],
                'response_format' => array('type' => 'json_object')
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message']);
        }
        
        $content = json_decode($body['choices'][0]['message']['content'], true);
        return $content;
    }
    
    private function generate_with_huggingface($prompt) {
        $api_key = get_option('aipg_huggingface_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Token Hugging Face não configurado', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api-inference.huggingface.co/models/mistralai/Mixtral-8x7B-Instruct-v0.1', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'inputs' => $prompt,
                'parameters' => array(
                    'max_new_tokens' => 2000,
                    'temperature' => 0.7,
                    'return_full_text' => false
                )
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']);
        }
        
        // Extrai JSON do texto gerado
        $generated_text = $body[0]['generated_text'];
        preg_match('/\{.*\}/s', $generated_text, $matches);
        
        if (empty($matches)) {
            // Fallback: cria estrutura básica
            return array(
                'title' => 'Post Gerado',
                'content' => $generated_text,
                'tags' => array(),
                'seo' => array('title' => '', 'description' => '')
            );
        }
        
        $content = json_decode($matches[0], true);
        return $content;
    }
    
    private function generate_with_cohere($prompt) {
        $api_key = get_option('aipg_cohere_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Cohere não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api.cohere.ai/v1/generate', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'command-r-plus',
                'prompt' => $prompt,
                'max_tokens' => 2000,
                'temperature' => 0.7
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['message'])) {
            return new WP_Error('api_error', $body['message']);
        }
        
        $generated_text = $body['generations'][0]['text'];
        preg_match('/\{.*\}/s', $generated_text, $matches);
        
        if (empty($matches)) {
            return array(
                'title' => 'Post Gerado',
                'content' => $generated_text,
                'tags' => array(),
                'seo' => array('title' => '', 'description' => '')
            );
        }
        
        $content = json_decode($matches[0], true);
        return $content;
    }
    
    private function generate_with_mistral($prompt) {
        $api_key = get_option('aipg_mistral_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Mistral não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api.mistral.ai/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'mistral-small-latest',
                'messages' => array(
                    array('role' => 'system', 'content' => 'Você é um especialista em criar conteúdo. Retorne JSON válido.'),
                    array('role' => 'user', 'content' => $prompt)
                ),
                'temperature' => 0.7,
                'response_format' => array('type' => 'json_object')
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message']);
        }
        
        $content = json_decode($body['choices'][0]['message']['content'], true);
        return $content;
    }
    
    private function generate_featured_image($topic, $post_id) {
        $unsplash_key = get_option('aipg_unsplash_key');
        
        if (empty($unsplash_key)) {
            return false;
        }
        
        $response = wp_remote_get(
            'https://api.unsplash.com/search/photos?query=' . urlencode($topic) . '&per_page=1',
            array(
                'headers' => array(
                    'Authorization' => 'Client-ID ' . $unsplash_key
                )
            )
        );
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (empty($body['results'])) {
            return false;
        }
        
        $image_url = $body['results'][0]['urls']['regular'];
        
        // Download e salva imagem
        $tmp = download_url($image_url);
        
        if (is_wp_error($tmp)) {
            return false;
        }
        
        $file_array = array(
            'name' => basename($image_url) . '.jpg',
            'tmp_name' => $tmp
        );
        
        $id = media_handle_sideload($file_array, $post_id);
        
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return false;
        }
        
        return $id;
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
        
        wp_schedule_single_event(strtotime($data['schedule_date']), 'aipg_scheduled_post', array($wpdb->insert_id));
    }
    
    public function process_scheduled_post($scheduled_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'aipg_scheduled';
        
        $scheduled = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $scheduled_id));
        
        if (!$scheduled) {
            return;
        }
        
        $data = json_decode($scheduled->config, true);
        $result = $this->generate_content($data);
        
        if (is_wp_error($result)) {
            $wpdb->update($table, array('status' => 'failed'), array('id' => $scheduled_id));
            return;
        }
        
        $post_id = wp_insert_post(array(
            'post_title' => $result['title'],
            'post_content' => $result['content'],
            'post_status' => 'publish',
            'post_author' => get_option('aipg_default_author', 1),
            'post_category' => $data['category'] ? array($data['category']) : array()
        ));
        
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_aipg_generated', '1');
            $wpdb->update($table, array('status' => 'completed', 'post_id' => $post_id), array('id' => $scheduled_id));
        }
    }
    
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
}

function aipg_init() {
    return AI_Post_Generator::get_instance();
}
add_action('plugins_loaded', 'aipg_init');

register_activation_hook(__FILE__, 'aipg_activate');
function aipg_activate() {
    global $wpdb;
    
    $table = $wpdb->prefix . 'aipg_scheduled';
    $charset = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        topic varchar(255) NOT NULL,
        config text NOT NULL,
        schedule_date datetime NOT NULL,
        status varchar(20) DEFAULT 'pending',
        post_id bigint(20) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    add_option('aipg_post_status', 'draft');
    add_option('aipg_default_author', get_current_user_id());
    add_option('aipg_auto_tags', '0');
    add_option('aipg_seo_optimization', '0');
    add_option('aipg_auto_featured_image', '0');
    add_option('aipg_add_internal_links', '0');
}

register_deactivation_hook(__FILE__, 'aipg_deactivate');
function aipg_deactivate() {
    wp_clear_scheduled_hook('aipg_scheduled_post');
}