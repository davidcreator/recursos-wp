<?php
/**
 * Advanced Theme Options Panel
 *
 * @package NosfirNews
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Advanced Theme Options Class
 */
class NosfirNews_Advanced_Theme_Options {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_theme_options_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_reset_theme_options', array($this, 'ajax_reset_options'));
        add_action('wp_ajax_export_theme_options', array($this, 'ajax_export_options'));
        add_action('wp_ajax_import_theme_options', array($this, 'ajax_import_options'));
        add_action('wp_ajax_clear_theme_cache', array($this, 'ajax_clear_cache'));
    }

    /**
     * Add theme options page to admin menu
     */
    public function add_theme_options_page() {
        add_theme_page(
            __('Configurações Avançadas - NosfirNews', 'nosfirnews'),
            __('Configurações Avançadas', 'nosfirnews'),
            'manage_options',
            'nosfirnews-advanced-options',
            array($this, 'render_options_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // Performance Settings
        register_setting('nosfirnews_performance_group', 'nosfirnews_performance_options', array($this, 'sanitize_performance_options'));
        
        // SEO Settings
        register_setting('nosfirnews_seo_group', 'nosfirnews_seo_options', array($this, 'sanitize_seo_options'));
        
        // Social Media Settings
        register_setting('nosfirnews_social_group', 'nosfirnews_social_options', array($this, 'sanitize_social_options'));
        
        // Security Settings
        register_setting('nosfirnews_security_group', 'nosfirnews_security_options', array($this, 'sanitize_security_options'));
        
        // Advanced Settings
        register_setting('nosfirnews_advanced_group', 'nosfirnews_advanced_options', array($this, 'sanitize_advanced_options'));
    }

    /**
     * Render options page
     */
    public function render_options_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'performance';
        ?>
        <div class="wrap nosfirnews-advanced-options">
            <h1><?php _e('Configurações Avançadas do NosfirNews', 'nosfirnews'); ?></h1>
            
            <nav class="nav-tab-wrapper">
                <a href="?page=nosfirnews-advanced-options&tab=performance" class="nav-tab <?php echo $active_tab == 'performance' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-performance"></span>
                    <?php _e('Performance', 'nosfirnews'); ?>
                </a>
                <a href="?page=nosfirnews-advanced-options&tab=seo" class="nav-tab <?php echo $active_tab == 'seo' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-search"></span>
                    <?php _e('SEO', 'nosfirnews'); ?>
                </a>
                <a href="?page=nosfirnews-advanced-options&tab=social" class="nav-tab <?php echo $active_tab == 'social' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-share"></span>
                    <?php _e('Redes Sociais', 'nosfirnews'); ?>
                </a>
                <a href="?page=nosfirnews-advanced-options&tab=security" class="nav-tab <?php echo $active_tab == 'security' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-shield"></span>
                    <?php _e('Segurança', 'nosfirnews'); ?>
                </a>
                <a href="?page=nosfirnews-advanced-options&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-admin-tools"></span>
                    <?php _e('Avançado', 'nosfirnews'); ?>
                </a>
                <a href="?page=nosfirnews-advanced-options&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php _e('Ferramentas', 'nosfirnews'); ?>
                </a>
            </nav>

            <div class="tab-content">
                <?php
                switch ($active_tab) {
                    case 'performance':
                        $this->render_performance_tab();
                        break;
                    case 'seo':
                        $this->render_seo_tab();
                        break;
                    case 'social':
                        $this->render_social_tab();
                        break;
                    case 'security':
                        $this->render_security_tab();
                        break;
                    case 'advanced':
                        $this->render_advanced_tab();
                        break;
                    case 'tools':
                        $this->render_tools_tab();
                        break;
                    default:
                        $this->render_performance_tab();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render Performance tab
     */
    private function render_performance_tab() {
        $options = get_option('nosfirnews_performance_options', $this->get_default_performance_options());
        ?>
        <div class="nosfirnews-tab-content">
            <div class="nosfirnews-section">
                <h2><?php _e('Otimização de Performance', 'nosfirnews'); ?></h2>
                <p class="description"><?php _e('Configure as opções de performance para acelerar seu site.', 'nosfirnews'); ?></p>
                
                <form method="post" action="options.php">
                    <?php settings_fields('nosfirnews_performance_group'); ?>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Otimização de Recursos', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[minify_css]" value="1" <?php checked($options['minify_css'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Minificar CSS', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Remove espaços em branco e comentários do CSS para reduzir o tamanho dos arquivos.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[minify_js]" value="1" <?php checked($options['minify_js'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Minificar JavaScript', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Remove espaços em branco e comentários do JavaScript.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[combine_css]" value="1" <?php checked($options['combine_css'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Combinar Arquivos CSS', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Combina múltiplos arquivos CSS em um único arquivo.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[combine_js]" value="1" <?php checked($options['combine_js'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Combinar Arquivos JavaScript', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Combina múltiplos arquivos JavaScript em um único arquivo.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Otimização de Imagens', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[lazy_load_images]" value="1" <?php checked($options['lazy_load_images'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Lazy Loading de Imagens', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Carrega imagens apenas quando elas estão prestes a aparecer na tela.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[webp_conversion]" value="1" <?php checked($options['webp_conversion'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Conversão para WebP', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Converte automaticamente imagens para o formato WebP quando suportado.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="image_quality"><?php _e('Qualidade de Imagem', 'nosfirnews'); ?></label>
                            <div class="nosfirnews-range-input">
                                <input type="range" id="image_quality" name="nosfirnews_performance_options[image_quality]" 
                                       value="<?php echo esc_attr($options['image_quality'] ?? 85); ?>" 
                                       min="50" max="100" step="5" 
                                       oninput="this.nextElementSibling.textContent = this.value + '%'" />
                                <span class="range-value"><?php echo esc_attr($options['image_quality'] ?? 85); ?>%</span>
                            </div>
                            <p class="description"><?php _e('Qualidade de compressão das imagens (menor = arquivo menor).', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Cache e Armazenamento', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[enable_cache]" value="1" <?php checked($options['enable_cache'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Cache de Página', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Armazena páginas em cache para carregamento mais rápido.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="cache_duration"><?php _e('Duração do Cache (horas)', 'nosfirnews'); ?></label>
                            <input type="number" id="cache_duration" name="nosfirnews_performance_options[cache_duration]" 
                                   value="<?php echo esc_attr($options['cache_duration'] ?? 24); ?>" 
                                   min="1" max="168" step="1" class="small-text" />
                            <p class="description"><?php _e('Por quanto tempo manter os arquivos em cache.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[gzip_compression]" value="1" <?php checked($options['gzip_compression'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Compressão GZIP', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Comprime arquivos antes de enviá-los ao navegador.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Otimização de Banco de Dados', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[optimize_db]" value="1" <?php checked($options['optimize_db'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Otimização Automática do Banco', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Otimiza automaticamente o banco de dados semanalmente.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_performance_options[limit_revisions]" value="1" <?php checked($options['limit_revisions'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Limitar Revisões de Posts', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Limita o número de revisões salvas por post.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="max_revisions"><?php _e('Máximo de Revisões', 'nosfirnews'); ?></label>
                            <input type="number" id="max_revisions" name="nosfirnews_performance_options[max_revisions]" 
                                   value="<?php echo esc_attr($options['max_revisions'] ?? 5); ?>" 
                                   min="1" max="20" step="1" class="small-text" />
                            <p class="description"><?php _e('Número máximo de revisões a manter por post.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <?php submit_button(__('Salvar Configurações de Performance', 'nosfirnews')); ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render SEO tab
     */
    private function render_seo_tab() {
        $options = get_option('nosfirnews_seo_options', $this->get_default_seo_options());
        ?>
        <div class="nosfirnews-tab-content">
            <div class="nosfirnews-section">
                <h2><?php _e('Configurações de SEO', 'nosfirnews'); ?></h2>
                <p class="description"><?php _e('Otimize seu site para motores de busca.', 'nosfirnews'); ?></p>
                
                <form method="post" action="options.php">
                    <?php settings_fields('nosfirnews_seo_group'); ?>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Meta Tags', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label for="default_meta_description"><?php _e('Meta Description Padrão', 'nosfirnews'); ?></label>
                            <textarea id="default_meta_description" name="nosfirnews_seo_options[default_meta_description]" 
                                      rows="3" class="large-text"><?php echo esc_textarea($options['default_meta_description'] ?? ''); ?></textarea>
                            <p class="description"><?php _e('Descrição padrão para páginas sem meta description específica.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_seo_options[auto_meta_description]" value="1" <?php checked($options['auto_meta_description'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Gerar Meta Description Automaticamente', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Gera automaticamente meta descriptions baseadas no conteúdo.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Open Graph e Twitter Cards', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_seo_options[enable_og]" value="1" <?php checked($options['enable_og'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Open Graph Tags', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Melhora o compartilhamento em redes sociais como Facebook.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_seo_options[enable_twitter_cards]" value="1" <?php checked($options['enable_twitter_cards'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Twitter Cards', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Melhora o compartilhamento no Twitter.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="twitter_username"><?php _e('Nome de Usuário do Twitter', 'nosfirnews'); ?></label>
                            <input type="text" id="twitter_username" name="nosfirnews_seo_options[twitter_username]" 
                                   value="<?php echo esc_attr($options['twitter_username'] ?? ''); ?>" 
                                   class="regular-text" placeholder="@seuusuario" />
                            <p class="description"><?php _e('Seu nome de usuário do Twitter (com @).', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Schema.org e Dados Estruturados', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_seo_options[enable_schema]" value="1" <?php checked($options['enable_schema'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Schema.org Markup', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Adiciona marcação estruturada para melhor compreensão pelos motores de busca.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="organization_name"><?php _e('Nome da Organização', 'nosfirnews'); ?></label>
                            <input type="text" id="organization_name" name="nosfirnews_seo_options[organization_name]" 
                                   value="<?php echo esc_attr($options['organization_name'] ?? get_bloginfo('name')); ?>" 
                                   class="regular-text" />
                            <p class="description"><?php _e('Nome da sua organização para Schema.org.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="organization_logo"><?php _e('Logo da Organização', 'nosfirnews'); ?></label>
                            <input type="url" id="organization_logo" name="nosfirnews_seo_options[organization_logo]" 
                                   value="<?php echo esc_url($options['organization_logo'] ?? ''); ?>" 
                                   class="regular-text" />
                            <button type="button" class="button upload-logo-btn"><?php _e('Escolher Logo', 'nosfirnews'); ?></button>
                            <p class="description"><?php _e('URL do logo da organização para Schema.org.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Sitemap e Indexação', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_seo_options[enable_sitemap]" value="1" <?php checked($options['enable_sitemap'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Sitemap XML', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Gera automaticamente um sitemap XML para facilitar a indexação.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_seo_options[ping_search_engines]" value="1" <?php checked($options['ping_search_engines'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Notificar Motores de Busca', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Notifica automaticamente Google e Bing sobre atualizações.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="robots_txt"><?php _e('Robots.txt Personalizado', 'nosfirnews'); ?></label>
                            <textarea id="robots_txt" name="nosfirnews_seo_options[robots_txt]" 
                                      rows="6" class="large-text code"><?php echo esc_textarea($options['robots_txt'] ?? ''); ?></textarea>
                            <p class="description"><?php _e('Conteúdo personalizado para robots.txt. Deixe em branco para usar o padrão.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Breadcrumbs e Navegação', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_seo_options[enable_breadcrumbs]" value="1" <?php checked($options['enable_breadcrumbs'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Breadcrumbs', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Adiciona breadcrumbs para melhor navegação e SEO.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="breadcrumb_separator"><?php _e('Separador de Breadcrumb', 'nosfirnews'); ?></label>
                            <input type="text" id="breadcrumb_separator" name="nosfirnews_seo_options[breadcrumb_separator]" 
                                   value="<?php echo esc_attr($options['breadcrumb_separator'] ?? ' > '); ?>" 
                                   class="small-text" />
                            <p class="description"><?php _e('Caractere usado para separar itens do breadcrumb.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <?php submit_button(__('Salvar Configurações de SEO', 'nosfirnews')); ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render Social tab
     */
    private function render_social_tab() {
        $options = get_option('nosfirnews_social_options', $this->get_default_social_options());
        $social_networks = array(
            'facebook' => array('name' => 'Facebook', 'icon' => 'dashicons-facebook'),
            'twitter' => array('name' => 'Twitter', 'icon' => 'dashicons-twitter'),
            'instagram' => array('name' => 'Instagram', 'icon' => 'dashicons-instagram'),
            'youtube' => array('name' => 'YouTube', 'icon' => 'dashicons-youtube'),
            'linkedin' => array('name' => 'LinkedIn', 'icon' => 'dashicons-linkedin'),
            'pinterest' => array('name' => 'Pinterest', 'icon' => 'dashicons-pinterest'),
            'tiktok' => array('name' => 'TikTok', 'icon' => 'dashicons-video-alt3'),
            'whatsapp' => array('name' => 'WhatsApp', 'icon' => 'dashicons-whatsapp'),
            'telegram' => array('name' => 'Telegram', 'icon' => 'dashicons-email'),
            'discord' => array('name' => 'Discord', 'icon' => 'dashicons-groups')
        );
        ?>
        <div class="nosfirnews-tab-content">
            <div class="nosfirnews-section">
                <h2><?php _e('Configurações de Redes Sociais', 'nosfirnews'); ?></h2>
                <p class="description"><?php _e('Configure links e opções de compartilhamento para redes sociais.', 'nosfirnews'); ?></p>
                
                <form method="post" action="options.php">
                    <?php settings_fields('nosfirnews_social_group'); ?>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Links das Redes Sociais', 'nosfirnews'); ?></h3>
                        <p class="description"><?php _e('Adicione os links dos seus perfis nas redes sociais.', 'nosfirnews'); ?></p>
                        
                        <div class="nosfirnews-social-grid">
                            <?php foreach ($social_networks as $key => $network): ?>
                            <div class="nosfirnews-social-item">
                                <label for="social_<?php echo $key; ?>">
                                    <span class="dashicons <?php echo $network['icon']; ?>"></span>
                                    <?php echo $network['name']; ?>
                                </label>
                                <input type="url" id="social_<?php echo $key; ?>" 
                                       name="nosfirnews_social_options[<?php echo $key; ?>]" 
                                       value="<?php echo esc_url($options[$key] ?? ''); ?>" 
                                       class="regular-text" placeholder="https://" />
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Configurações de Compartilhamento', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_social_options[enable_sharing]" value="1" <?php checked($options['enable_sharing'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Botões de Compartilhamento', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Mostra botões de compartilhamento nos posts.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="sharing_position"><?php _e('Posição dos Botões', 'nosfirnews'); ?></label>
                            <select id="sharing_position" name="nosfirnews_social_options[sharing_position]">
                                <option value="top" <?php selected($options['sharing_position'] ?? 'bottom', 'top'); ?>><?php _e('Antes do Conteúdo', 'nosfirnews'); ?></option>
                                <option value="bottom" <?php selected($options['sharing_position'] ?? 'bottom', 'bottom'); ?>><?php _e('Depois do Conteúdo', 'nosfirnews'); ?></option>
                                <option value="both" <?php selected($options['sharing_position'] ?? 'bottom', 'both'); ?>><?php _e('Antes e Depois', 'nosfirnews'); ?></option>
                                <option value="floating" <?php selected($options['sharing_position'] ?? 'bottom', 'floating'); ?>><?php _e('Flutuante', 'nosfirnews'); ?></option>
                            </select>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="sharing_style"><?php _e('Estilo dos Botões', 'nosfirnews'); ?></label>
                            <select id="sharing_style" name="nosfirnews_social_options[sharing_style]">
                                <option value="default" <?php selected($options['sharing_style'] ?? 'default', 'default'); ?>><?php _e('Padrão', 'nosfirnews'); ?></option>
                                <option value="minimal" <?php selected($options['sharing_style'] ?? 'default', 'minimal'); ?>><?php _e('Minimalista', 'nosfirnews'); ?></option>
                                <option value="rounded" <?php selected($options['sharing_style'] ?? 'default', 'rounded'); ?>><?php _e('Arredondado', 'nosfirnews'); ?></option>
                                <option value="square" <?php selected($options['sharing_style'] ?? 'default', 'square'); ?>><?php _e('Quadrado', 'nosfirnews'); ?></option>
                                <option value="icon-only" <?php selected($options['sharing_style'] ?? 'default', 'icon-only'); ?>><?php _e('Apenas Ícones', 'nosfirnews'); ?></option>
                            </select>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label><?php _e('Redes para Compartilhamento', 'nosfirnews'); ?></label>
                            <div class="nosfirnews-checkbox-grid">
                                <?php
                                $sharing_networks = array('facebook', 'twitter', 'linkedin', 'pinterest', 'whatsapp', 'telegram');
                                $selected_networks = $options['sharing_networks'] ?? array('facebook', 'twitter', 'linkedin');
                                
                                foreach ($sharing_networks as $network):
                                ?>
                                <label class="nosfirnews-checkbox-item">
                                    <input type="checkbox" name="nosfirnews_social_options[sharing_networks][]" 
                                           value="<?php echo $network; ?>" 
                                           <?php checked(in_array($network, $selected_networks), true); ?> />
                                    <span class="dashicons <?php echo $social_networks[$network]['icon']; ?>"></span>
                                    <?php echo $social_networks[$network]['name']; ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_social_options[show_share_count]" value="1" <?php checked($options['show_share_count'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Mostrar Contador de Compartilhamentos', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Exibe o número de compartilhamentos para cada rede social.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Configurações Avançadas', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_social_options[open_new_window]" value="1" <?php checked($options['open_new_window'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Abrir em Nova Janela', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Abre links de redes sociais em uma nova janela/aba.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_social_options[nofollow_links]" value="1" <?php checked($options['nofollow_links'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Adicionar rel="nofollow"', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Adiciona rel="nofollow" aos links de redes sociais.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="custom_sharing_text"><?php _e('Texto Personalizado de Compartilhamento', 'nosfirnews'); ?></label>
                            <input type="text" id="custom_sharing_text" name="nosfirnews_social_options[custom_sharing_text]" 
                                   value="<?php echo esc_attr($options['custom_sharing_text'] ?? __('Compartilhar:', 'nosfirnews')); ?>" 
                                   class="regular-text" />
                            <p class="description"><?php _e('Texto exibido antes dos botões de compartilhamento.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <?php submit_button(__('Salvar Configurações Sociais', 'nosfirnews')); ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render Security tab
     */
    private function render_security_tab() {
        $options = get_option('nosfirnews_security_options', $this->get_default_security_options());
        ?>
        <div class="nosfirnews-tab-content">
            <div class="nosfirnews-section">
                <h2><?php _e('Configurações de Segurança', 'nosfirnews'); ?></h2>
                <p class="description"><?php _e('Configure opções de segurança para proteger seu site.', 'nosfirnews'); ?></p>
                
                <form method="post" action="options.php">
                    <?php settings_fields('nosfirnews_security_group'); ?>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Proteção de Login', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[limit_login_attempts]" value="1" <?php checked($options['limit_login_attempts'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Limitar Tentativas de Login', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Bloqueia IPs após várias tentativas de login falhadas.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="max_login_attempts"><?php _e('Máximo de Tentativas', 'nosfirnews'); ?></label>
                            <input type="number" id="max_login_attempts" name="nosfirnews_security_options[max_login_attempts]" 
                                   value="<?php echo esc_attr($options['max_login_attempts'] ?? 5); ?>" 
                                   min="3" max="20" step="1" class="small-text" />
                            <p class="description"><?php _e('Número máximo de tentativas antes do bloqueio.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="lockout_duration"><?php _e('Tempo de Bloqueio (minutos)', 'nosfirnews'); ?></label>
                            <input type="number" id="lockout_duration" name="nosfirnews_security_options[lockout_duration]" 
                                   value="<?php echo esc_attr($options['lockout_duration'] ?? 30); ?>" 
                                   min="5" max="1440" step="5" class="small-text" />
                            <p class="description"><?php _e('Duração do bloqueio em minutos.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[hide_login_errors]" value="1" <?php checked($options['hide_login_errors'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Ocultar Erros de Login', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Não revela se o usuário ou senha está incorreto.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Proteção de Cabeçalhos', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[remove_wp_version]" value="1" <?php checked($options['remove_wp_version'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Remover Versão do WordPress', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Remove informações de versão do WordPress do código fonte.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[disable_xmlrpc]" value="1" <?php checked($options['disable_xmlrpc'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Desativar XML-RPC', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Desativa XML-RPC se não for necessário para sua aplicação.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[disable_file_editing]" value="1" <?php checked($options['disable_file_editing'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Desativar Editor de Arquivos', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Impede a edição de arquivos através do painel administrativo.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[force_ssl_admin]" value="1" <?php checked($options['force_ssl_admin'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Forçar SSL no Admin', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Força o uso de HTTPS na área administrativa.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Proteção de Conteúdo', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[disable_right_click]" value="1" <?php checked($options['disable_right_click'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Desativar Clique Direito', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Desativa o menu de contexto do clique direito (proteção básica).', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[disable_text_selection]" value="1" <?php checked($options['disable_text_selection'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Desativar Seleção de Texto', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Impede a seleção de texto no site (proteção básica).', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[disable_image_hotlinking]" value="1" <?php checked($options['disable_image_hotlinking'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Prevenir Hotlinking de Imagens', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Impede que outros sites usem suas imagens diretamente.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Monitoramento e Logs', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[enable_security_logs]" value="1" <?php checked($options['enable_security_logs'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Logs de Segurança', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Registra tentativas de login e atividades suspeitas.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_security_options[email_notifications]" value="1" <?php checked($options['email_notifications'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Notificações por Email', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Envia emails sobre atividades de segurança importantes.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="notification_email"><?php _e('Email para Notificações', 'nosfirnews'); ?></label>
                            <input type="email" id="notification_email" name="nosfirnews_security_options[notification_email]" 
                                   value="<?php echo esc_attr($options['notification_email'] ?? get_option('admin_email')); ?>" 
                                   class="regular-text" />
                            <p class="description"><?php _e('Email que receberá as notificações de segurança.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <?php submit_button(__('Salvar Configurações de Segurança', 'nosfirnews')); ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render Advanced tab
     */
    private function render_advanced_tab() {
        $options = get_option('nosfirnews_advanced_options', $this->get_default_advanced_options());
        ?>
        <div class="nosfirnews-tab-content">
            <div class="nosfirnews-section">
                <h2><?php _e('Configurações Avançadas', 'nosfirnews'); ?></h2>
                <p class="description"><?php _e('Configurações avançadas para desenvolvedores e usuários experientes.', 'nosfirnews'); ?></p>
                
                <form method="post" action="options.php">
                    <?php settings_fields('nosfirnews_advanced_group'); ?>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Código Personalizado', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label for="custom_css"><?php _e('CSS Personalizado', 'nosfirnews'); ?></label>
                            <textarea id="custom_css" name="nosfirnews_advanced_options[custom_css]" 
                                      rows="10" class="large-text code"><?php echo esc_textarea($options['custom_css'] ?? ''); ?></textarea>
                            <p class="description"><?php _e('CSS personalizado que será adicionado ao tema.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="custom_js"><?php _e('JavaScript Personalizado', 'nosfirnews'); ?></label>
                            <textarea id="custom_js" name="nosfirnews_advanced_options[custom_js]" 
                                      rows="10" class="large-text code"><?php echo esc_textarea($options['custom_js'] ?? ''); ?></textarea>
                            <p class="description"><?php _e('JavaScript personalizado (sem tags &lt;script&gt;).', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="custom_functions"><?php _e('Funções PHP Personalizadas', 'nosfirnews'); ?></label>
                            <textarea id="custom_functions" name="nosfirnews_advanced_options[custom_functions]" 
                                      rows="10" class="large-text code"><?php echo esc_textarea($options['custom_functions'] ?? ''); ?></textarea>
                            <p class="description"><?php _e('Código PHP personalizado (sem tags &lt;?php). <strong>Use com cuidado!</strong>', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Modo de Desenvolvimento', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_advanced_options[dev_mode]" value="1" <?php checked($options['dev_mode'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Modo de Desenvolvimento', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Desativa cache e minificação para desenvolvimento.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_advanced_options[debug_mode]" value="1" <?php checked($options['debug_mode'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Modo Debug', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Ativa logs detalhados e informações de debug.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_advanced_options[query_debug]" value="1" <?php checked($options['query_debug'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Debug de Consultas', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Mostra informações de consultas do banco (apenas para administradores).', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Configurações de API', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_advanced_options[enable_rest_api]" value="1" <?php checked($options['enable_rest_api'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('WordPress REST API', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Permite acesso à API REST do WordPress.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="api_rate_limit"><?php _e('Limite de Taxa da API (por minuto)', 'nosfirnews'); ?></label>
                            <input type="number" id="api_rate_limit" name="nosfirnews_advanced_options[api_rate_limit]" 
                                   value="<?php echo esc_attr($options['api_rate_limit'] ?? 60); ?>" 
                                   min="10" max="1000" step="10" class="small-text" />
                            <p class="description"><?php _e('Número máximo de requisições à API por minuto.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="api_key"><?php _e('Chave da API Personalizada', 'nosfirnews'); ?></label>
                            <input type="text" id="api_key" name="nosfirnews_advanced_options[api_key]" 
                                   value="<?php echo esc_attr($options['api_key'] ?? ''); ?>" 
                                   class="regular-text" />
                            <button type="button" class="button" onclick="generateApiKey()"><?php _e('Gerar Nova Chave', 'nosfirnews'); ?></button>
                            <p class="description"><?php _e('Chave para autenticação em APIs personalizadas.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Configurações de Banco de Dados', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_advanced_options[auto_optimize_db]" value="1" <?php checked($options['auto_optimize_db'] ?? 0, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Otimização Automática do Banco', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Otimiza automaticamente o banco de dados semanalmente.', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="db_cleanup_frequency"><?php _e('Frequência de Limpeza', 'nosfirnews'); ?></label>
                            <select id="db_cleanup_frequency" name="nosfirnews_advanced_options[db_cleanup_frequency]">
                                <option value="daily" <?php selected($options['db_cleanup_frequency'] ?? 'weekly', 'daily'); ?>><?php _e('Diário', 'nosfirnews'); ?></option>
                                <option value="weekly" <?php selected($options['db_cleanup_frequency'] ?? 'weekly', 'weekly'); ?>><?php _e('Semanal', 'nosfirnews'); ?></option>
                                <option value="monthly" <?php selected($options['db_cleanup_frequency'] ?? 'weekly', 'monthly'); ?>><?php _e('Mensal', 'nosfirnews'); ?></option>
                            </select>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label class="nosfirnews-toggle">
                                <input type="checkbox" name="nosfirnews_advanced_options[backup_before_optimize]" value="1" <?php checked($options['backup_before_optimize'] ?? 1, 1); ?> />
                                <span class="nosfirnews-toggle-slider"></span>
                                <span class="nosfirnews-toggle-label"><?php _e('Backup Antes da Otimização', 'nosfirnews'); ?></span>
                            </label>
                            <p class="description"><?php _e('Cria backup automático antes de otimizar o banco.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-option-group">
                        <h3><?php _e('Configurações de Memória', 'nosfirnews'); ?></h3>
                        
                        <div class="nosfirnews-option">
                            <label for="memory_limit"><?php _e('Limite de Memória PHP', 'nosfirnews'); ?></label>
                            <select id="memory_limit" name="nosfirnews_advanced_options[memory_limit]">
                                <option value="128M" <?php selected($options['memory_limit'] ?? '256M', '128M'); ?>>128MB</option>
                                <option value="256M" <?php selected($options['memory_limit'] ?? '256M', '256M'); ?>>256MB</option>
                                <option value="512M" <?php selected($options['memory_limit'] ?? '256M', '512M'); ?>>512MB</option>
                                <option value="1024M" <?php selected($options['memory_limit'] ?? '256M', '1024M'); ?>>1GB</option>
                            </select>
                            <p class="description"><?php _e('Limite de memória para scripts PHP (requer permissões do servidor).', 'nosfirnews'); ?></p>
                        </div>
                        
                        <div class="nosfirnews-option">
                            <label for="max_execution_time"><?php _e('Tempo Máximo de Execução (segundos)', 'nosfirnews'); ?></label>
                            <input type="number" id="max_execution_time" name="nosfirnews_advanced_options[max_execution_time]" 
                                   value="<?php echo esc_attr($options['max_execution_time'] ?? 30); ?>" 
                                   min="30" max="300" step="30" class="small-text" />
                            <p class="description"><?php _e('Tempo máximo de execução para scripts PHP.', 'nosfirnews'); ?></p>
                        </div>
                    </div>
                    
                    <?php submit_button(__('Salvar Configurações Avançadas', 'nosfirnews')); ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render Tools tab
     */
    private function render_tools_tab() {
        ?>
        <div class="nosfirnews-tab-content">
            <div class="nosfirnews-section">
                <h2><?php _e('Ferramentas do Sistema', 'nosfirnews'); ?></h2>
                <p class="description"><?php _e('Ferramentas para manutenção e gerenciamento do tema.', 'nosfirnews'); ?></p>
                
                <div class="nosfirnews-tools-grid">
                    <div class="nosfirnews-tool-card">
                        <h3><?php _e('Cache e Performance', 'nosfirnews'); ?></h3>
                        <p><?php _e('Limpe o cache e otimize a performance do site.', 'nosfirnews'); ?></p>
                        <div class="nosfirnews-tool-actions">
                            <button type="button" class="button button-primary" onclick="clearThemeCache()">
                                <?php _e('Limpar Cache', 'nosfirnews'); ?>
                            </button>
                            <button type="button" class="button" onclick="optimizeDatabase()">
                                <?php _e('Otimizar Banco', 'nosfirnews'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-tool-card">
                        <h3><?php _e('Backup e Restauração', 'nosfirnews'); ?></h3>
                        <p><?php _e('Faça backup e restaure as configurações do tema.', 'nosfirnews'); ?></p>
                        <div class="nosfirnews-tool-actions">
                            <button type="button" class="button button-primary" onclick="exportSettings()">
                                <?php _e('Exportar Configurações', 'nosfirnews'); ?>
                            </button>
                            <button type="button" class="button" onclick="document.getElementById('import-file').click()">
                                <?php _e('Importar Configurações', 'nosfirnews'); ?>
                            </button>
                            <input type="file" id="import-file" style="display: none;" accept=".json" onchange="importSettings(this.files[0])">
                        </div>
                    </div>
                    
                    <div class="nosfirnews-tool-card">
                        <h3><?php _e('Reset e Limpeza', 'nosfirnews'); ?></h3>
                        <p><?php _e('Restaure configurações padrão ou limpe dados desnecessários.', 'nosfirnews'); ?></p>
                        <div class="nosfirnews-tool-actions">
                            <button type="button" class="button button-secondary" onclick="resetThemeOptions()">
                                <?php _e('Restaurar Padrões', 'nosfirnews'); ?>
                            </button>
                            <button type="button" class="button" onclick="cleanupOrphanedData()">
                                <?php _e('Limpar Dados Órfãos', 'nosfirnews'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="nosfirnews-tool-card">
                        <h3><?php _e('Informações do Sistema', 'nosfirnews'); ?></h3>
                        <p><?php _e('Visualize informações técnicas do sistema e tema.', 'nosfirnews'); ?></p>
                        <div class="nosfirnews-tool-actions">
                            <button type="button" class="button" onclick="showSystemInfo()">
                                <?php _e('Ver Informações', 'nosfirnews'); ?>
                            </button>
                            <button type="button" class="button" onclick="downloadSystemReport()">
                                <?php _e('Baixar Relatório', 'nosfirnews'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="system-info-modal" class="nosfirnews-modal" style="display: none;">
                    <div class="nosfirnews-modal-content">
                        <span class="nosfirnews-modal-close" onclick="closeModal()">&times;</span>
                        <h3><?php _e('Informações do Sistema', 'nosfirnews'); ?></h3>
                        <div id="system-info-content">
                            <!-- Conteúdo será carregado via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get default performance options
     */
    private function get_default_performance_options() {
        return array(
            'minify_css' => 0,
            'minify_js' => 0,
            'combine_css' => 0,
            'combine_js' => 0,
            'lazy_load_images' => 1,
            'webp_conversion' => 0,
            'image_quality' => 85,
            'enable_cache' => 1,
            'cache_duration' => 24,
            'gzip_compression' => 1,
            'optimize_db' => 0,
            'limit_revisions' => 0,
            'max_revisions' => 5
        );
    }

    /**
     * Get default SEO options
     */
    private function get_default_seo_options() {
        return array(
            'default_meta_description' => '',
            'auto_meta_description' => 1,
            'enable_og' => 1,
            'enable_twitter_cards' => 1,
            'twitter_username' => '',
            'enable_schema' => 1,
            'organization_name' => get_bloginfo('name'),
            'organization_logo' => '',
            'enable_sitemap' => 1,
            'ping_search_engines' => 1,
            'robots_txt' => '',
            'enable_breadcrumbs' => 1,
            'breadcrumb_separator' => ' > '
        );
    }

    /**
     * Get default social options
     */
    private function get_default_social_options() {
        return array(
            'facebook' => '',
            'twitter' => '',
            'instagram' => '',
            'youtube' => '',
            'linkedin' => '',
            'pinterest' => '',
            'tiktok' => '',
            'whatsapp' => '',
            'telegram' => '',
            'discord' => '',
            'enable_sharing' => 1,
            'sharing_position' => 'bottom',
            'sharing_style' => 'default',
            'sharing_networks' => array('facebook', 'twitter', 'linkedin'),
            'show_share_count' => 0,
            'open_new_window' => 1,
            'nofollow_links' => 1,
            'custom_sharing_text' => __('Compartilhar:', 'nosfirnews')
        );
    }

    /**
     * Get default security options
     */
    private function get_default_security_options() {
        return array(
            'limit_login_attempts' => 1,
            'max_login_attempts' => 5,
            'lockout_duration' => 30,
            'hide_login_errors' => 1,
            'remove_wp_version' => 1,
            'disable_xmlrpc' => 1,
            'disable_file_editing' => 1,
            'force_ssl_admin' => 0,
            'disable_right_click' => 0,
            'disable_text_selection' => 0,
            'disable_image_hotlinking' => 1,
            'enable_security_logs' => 1,
            'email_notifications' => 0,
            'notification_email' => get_option('admin_email')
        );
    }

    /**
     * Get default advanced options
     */
    private function get_default_advanced_options() {
        return array(
            'custom_css' => '',
            'custom_js' => '',
            'custom_functions' => '',
            'dev_mode' => 0,
            'debug_mode' => 0,
            'query_debug' => 0,
            'enable_rest_api' => 1,
            'api_rate_limit' => 60,
            'api_key' => '',
            'auto_optimize_db' => 0,
            'db_cleanup_frequency' => 'weekly',
            'backup_before_optimize' => 1,
            'memory_limit' => '256M',
            'max_execution_time' => 30
        );
    }

    /**
     * Sanitize performance options
     */
    public function sanitize_performance_options($input) {
        $sanitized = array();
        
        $sanitized['minify_css'] = isset($input['minify_css']) ? 1 : 0;
        $sanitized['minify_js'] = isset($input['minify_js']) ? 1 : 0;
        $sanitized['combine_css'] = isset($input['combine_css']) ? 1 : 0;
        $sanitized['combine_js'] = isset($input['combine_js']) ? 1 : 0;
        $sanitized['lazy_load_images'] = isset($input['lazy_load_images']) ? 1 : 0;
        $sanitized['webp_conversion'] = isset($input['webp_conversion']) ? 1 : 0;
        $sanitized['image_quality'] = absint($input['image_quality'] ?? 85);
        $sanitized['enable_cache'] = isset($input['enable_cache']) ? 1 : 0;
        $sanitized['cache_duration'] = absint($input['cache_duration'] ?? 24);
        $sanitized['gzip_compression'] = isset($input['gzip_compression']) ? 1 : 0;
        $sanitized['optimize_db'] = isset($input['optimize_db']) ? 1 : 0;
        $sanitized['limit_revisions'] = isset($input['limit_revisions']) ? 1 : 0;
        $sanitized['max_revisions'] = absint($input['max_revisions'] ?? 5);
        
        return $sanitized;
    }

    /**
     * Sanitize SEO options
     */
    public function sanitize_seo_options($input) {
        $sanitized = array();
        
        $sanitized['default_meta_description'] = sanitize_textarea_field($input['default_meta_description'] ?? '');
        $sanitized['auto_meta_description'] = isset($input['auto_meta_description']) ? 1 : 0;
        $sanitized['enable_og'] = isset($input['enable_og']) ? 1 : 0;
        $sanitized['enable_twitter_cards'] = isset($input['enable_twitter_cards']) ? 1 : 0;
        $sanitized['twitter_username'] = sanitize_text_field($input['twitter_username'] ?? '');
        $sanitized['enable_schema'] = isset($input['enable_schema']) ? 1 : 0;
        $sanitized['organization_name'] = sanitize_text_field($input['organization_name'] ?? '');
        $sanitized['organization_logo'] = esc_url_raw($input['organization_logo'] ?? '');
        $sanitized['enable_sitemap'] = isset($input['enable_sitemap']) ? 1 : 0;
        $sanitized['ping_search_engines'] = isset($input['ping_search_engines']) ? 1 : 0;
        $sanitized['robots_txt'] = sanitize_textarea_field($input['robots_txt'] ?? '');
        $sanitized['enable_breadcrumbs'] = isset($input['enable_breadcrumbs']) ? 1 : 0;
        $sanitized['breadcrumb_separator'] = sanitize_text_field($input['breadcrumb_separator'] ?? ' > ');
        
        return $sanitized;
    }

    /**
     * Sanitize social options
     */
    public function sanitize_social_options($input) {
        $sanitized = array();
        
        $social_networks = array('facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'pinterest', 'tiktok', 'whatsapp', 'telegram', 'discord');
        
        foreach ($social_networks as $network) {
            $sanitized[$network] = esc_url_raw($input[$network] ?? '');
        }
        
        $sanitized['enable_sharing'] = isset($input['enable_sharing']) ? 1 : 0;
        $sanitized['sharing_position'] = in_array($input['sharing_position'] ?? 'bottom', array('top', 'bottom', 'both', 'floating')) ? $input['sharing_position'] : 'bottom';
        $sanitized['sharing_style'] = in_array($input['sharing_style'] ?? 'default', array('default', 'minimal', 'rounded', 'square', 'icon-only')) ? $input['sharing_style'] : 'default';
        $sanitized['sharing_networks'] = isset($input['sharing_networks']) && is_array($input['sharing_networks']) ? array_map('sanitize_text_field', $input['sharing_networks']) : array();
        $sanitized['show_share_count'] = isset($input['show_share_count']) ? 1 : 0;
        $sanitized['open_new_window'] = isset($input['open_new_window']) ? 1 : 0;
        $sanitized['nofollow_links'] = isset($input['nofollow_links']) ? 1 : 0;
        $sanitized['custom_sharing_text'] = sanitize_text_field($input['custom_sharing_text'] ?? '');
        
        return $sanitized;
    }

    /**
     * Sanitize security options
     */
    public function sanitize_security_options($input) {
        $sanitized = array();
        
        $sanitized['limit_login_attempts'] = isset($input['limit_login_attempts']) ? 1 : 0;
        $sanitized['max_login_attempts'] = absint($input['max_login_attempts'] ?? 5);
        $sanitized['lockout_duration'] = absint($input['lockout_duration'] ?? 30);
        $sanitized['hide_login_errors'] = isset($input['hide_login_errors']) ? 1 : 0;
        $sanitized['remove_wp_version'] = isset($input['remove_wp_version']) ? 1 : 0;
        $sanitized['disable_xmlrpc'] = isset($input['disable_xmlrpc']) ? 1 : 0;
        $sanitized['disable_file_editing'] = isset($input['disable_file_editing']) ? 1 : 0;
        $sanitized['force_ssl_admin'] = isset($input['force_ssl_admin']) ? 1 : 0;
        $sanitized['disable_right_click'] = isset($input['disable_right_click']) ? 1 : 0;
        $sanitized['disable_text_selection'] = isset($input['disable_text_selection']) ? 1 : 0;
        $sanitized['disable_image_hotlinking'] = isset($input['disable_image_hotlinking']) ? 1 : 0;
        $sanitized['enable_security_logs'] = isset($input['enable_security_logs']) ? 1 : 0;
        $sanitized['email_notifications'] = isset($input['email_notifications']) ? 1 : 0;
        $sanitized['notification_email'] = sanitize_email($input['notification_email'] ?? '');
        
        return $sanitized;
    }

    /**
     * Sanitize advanced options
     */
    public function sanitize_advanced_options($input) {
        $sanitized = array();
        
        $sanitized['custom_css'] = wp_strip_all_tags($input['custom_css'] ?? '');
        $sanitized['custom_js'] = wp_strip_all_tags($input['custom_js'] ?? '');
        $sanitized['custom_functions'] = wp_strip_all_tags($input['custom_functions'] ?? '');
        $sanitized['dev_mode'] = isset($input['dev_mode']) ? 1 : 0;
        $sanitized['debug_mode'] = isset($input['debug_mode']) ? 1 : 0;
        $sanitized['query_debug'] = isset($input['query_debug']) ? 1 : 0;
        $sanitized['enable_rest_api'] = isset($input['enable_rest_api']) ? 1 : 0;
        $sanitized['api_rate_limit'] = absint($input['api_rate_limit'] ?? 60);
        $sanitized['api_key'] = sanitize_text_field($input['api_key'] ?? '');
        $sanitized['auto_optimize_db'] = isset($input['auto_optimize_db']) ? 1 : 0;
        $sanitized['db_cleanup_frequency'] = in_array($input['db_cleanup_frequency'] ?? 'weekly', array('daily', 'weekly', 'monthly')) ? $input['db_cleanup_frequency'] : 'weekly';
        $sanitized['backup_before_optimize'] = isset($input['backup_before_optimize']) ? 1 : 0;
        $sanitized['memory_limit'] = in_array($input['memory_limit'] ?? '256M', array('128M', '256M', '512M', '1024M')) ? $input['memory_limit'] : '256M';
        $sanitized['max_execution_time'] = absint($input['max_execution_time'] ?? 30);
        
        return $sanitized;
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'appearance_page_nosfirnews-advanced-options') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script('nosfirnews-advanced-options', get_template_directory_uri() . '/assets/js/advanced-options.js', array('jquery'), '1.0.0', true);
        wp_enqueue_style('nosfirnews-advanced-options', get_template_directory_uri() . '/assets/css/advanced-options.css', array(), '1.0.0');
        
        wp_localize_script('nosfirnews-advanced-options', 'nosfirnews_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nosfirnews_advanced_options_nonce'),
            'strings' => array(
                'confirm_reset' => __('Tem certeza que deseja restaurar todas as configurações para os valores padrão?', 'nosfirnews'),
                'confirm_clear_cache' => __('Tem certeza que deseja limpar todo o cache?', 'nosfirnews'),
                'success' => __('Operação realizada com sucesso!', 'nosfirnews'),
                'error' => __('Ocorreu um erro. Tente novamente.', 'nosfirnews')
            )
        ));
    }

    /**
     * AJAX: Reset theme options
     */
    public function ajax_reset_options() {
        check_ajax_referer('nosfirnews_advanced_options_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para realizar esta ação.', 'nosfirnews'));
        }
        
        delete_option('nosfirnews_performance_options');
        delete_option('nosfirnews_seo_options');
        delete_option('nosfirnews_social_options');
        delete_option('nosfirnews_security_options');
        delete_option('nosfirnews_advanced_options');
        
        wp_send_json_success(__('Configurações restauradas para os valores padrão.', 'nosfirnews'));
    }

    /**
     * AJAX: Export theme options
     */
    public function ajax_export_options() {
        check_ajax_referer('nosfirnews_advanced_options_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para realizar esta ação.', 'nosfirnews'));
        }
        
        $export_data = array(
            'performance' => get_option('nosfirnews_performance_options', array()),
            'seo' => get_option('nosfirnews_seo_options', array()),
            'social' => get_option('nosfirnews_social_options', array()),
            'security' => get_option('nosfirnews_security_options', array()),
            'advanced' => get_option('nosfirnews_advanced_options', array()),
            'export_date' => current_time('mysql'),
            'site_url' => get_site_url(),
            'theme_version' => wp_get_theme()->get('Version')
        );
        
        wp_send_json_success($export_data);
    }

    /**
     * AJAX: Import theme options
     */
    public function ajax_import_options() {
        check_ajax_referer('nosfirnews_advanced_options_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para realizar esta ação.', 'nosfirnews'));
        }
        
        $import_data = json_decode(stripslashes($_POST['import_data']), true);
        
        if (!$import_data || !is_array($import_data)) {
            wp_send_json_error(__('Dados de importação inválidos.', 'nosfirnews'));
        }
        
        if (isset($import_data['performance'])) {
            update_option('nosfirnews_performance_options', $import_data['performance']);
        }
        if (isset($import_data['seo'])) {
            update_option('nosfirnews_seo_options', $import_data['seo']);
        }
        if (isset($import_data['social'])) {
            update_option('nosfirnews_social_options', $import_data['social']);
        }
        if (isset($import_data['security'])) {
            update_option('nosfirnews_security_options', $import_data['security']);
        }
        if (isset($import_data['advanced'])) {
            update_option('nosfirnews_advanced_options', $import_data['advanced']);
        }
        
        wp_send_json_success(__('Configurações importadas com sucesso.', 'nosfirnews'));
    }

    /**
     * AJAX: Clear theme cache
     */
    public function ajax_clear_cache() {
        check_ajax_referer('nosfirnews_advanced_options_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para realizar esta ação.', 'nosfirnews'));
        }
        
        // Clear WordPress cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear object cache
        if (function_exists('wp_cache_delete_group')) {
            wp_cache_delete_group('nosfirnews');
        }
        
        // Clear transients
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_nosfirnews_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_nosfirnews_%'");
        
        wp_send_json_success(__('Cache limpo com sucesso.', 'nosfirnews'));
    }
}

// Initialize the class
new NosfirNews_Advanced_Theme_Options();