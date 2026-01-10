<?php
/**
 * NosfirNews Functions - Asset Management
 * Organização otimizada de CSS e JS
 * 
 * @package NosfirNews
 * @version 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================
// 1. CONSTANTES DE PATHS
// ============================================

define( 'NOSFIRNEWS_ASSETS_DIR', get_template_directory() . '/assets' );
define( 'NOSFIRNEWS_ASSETS_URI', get_template_directory_uri() . '/assets' );

// ============================================
// 2. HELPER FUNCTION PARA VERSIONING
// ============================================

/**
 * Retorna versão do arquivo baseada em modificação
 * @param string $file_path Caminho relativo do arquivo
 * @return string Versão do arquivo ou versão do tema
 */
function nosfirnews_asset_version( $file_path ) {
    $file = NOSFIRNEWS_ASSETS_DIR . '/' . ltrim( $file_path, '/' );
    
    if ( file_exists( $file ) ) {
        return filemtime( $file );
    }
    
    return wp_get_theme()->get( 'Version' );
}

// ============================================
// 3. ENQUEUE SCRIPTS E STYLES - FRONTEND
// ============================================

function nosfirnews_enqueue_assets() {
    
    // ===== CSS PRINCIPAL =====
    
    // Style.css (obrigatório pelo WordPress)
    wp_enqueue_style(
        'nosfirnews-style',
        get_stylesheet_uri(),
        [],
        nosfirnews_asset_version( '../style.css' )
    );
    
    // CSS principal compilado
    $main_css = NOSFIRNEWS_ASSETS_DIR . '/css/main.css';
    if ( file_exists( $main_css ) ) {
        wp_enqueue_style(
            'nosfirnews-main',
            NOSFIRNEWS_ASSETS_URI . '/css/main.css',
            [ 'nosfirnews-style' ],
            nosfirnews_asset_version( 'css/main.css' )
        );
    }
    
    // RTL Support
    if ( is_rtl() ) {
        $rtl_css = NOSFIRNEWS_ASSETS_DIR . '/css/main-rtl.css';
        if ( file_exists( $rtl_css ) ) {
            wp_enqueue_style(
                'nosfirnews-rtl',
                NOSFIRNEWS_ASSETS_URI . '/css/main-rtl.css',
                [ 'nosfirnews-main' ],
                nosfirnews_asset_version( 'css/main-rtl.css' )
            );
        }
    }
    
    // ===== JAVASCRIPT =====
    
    // JS principal do tema
    $theme_js = NOSFIRNEWS_ASSETS_DIR . '/js/theme.js';
    if ( file_exists( $theme_js ) ) {
        wp_enqueue_script(
            'nosfirnews-theme',
            NOSFIRNEWS_ASSETS_URI . '/js/theme.js',
            [],
            nosfirnews_asset_version( 'js/theme.js' ),
            true // no footer
        );
        
        // Localize script para AJAX
        wp_localize_script( 'nosfirnews-theme', 'nosfirnewsData', [
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'nosfirnews_nonce' ),
            'homeUrl'   => home_url( '/' ),
            'themeUri'  => get_template_directory_uri(),
            'isMobile'  => wp_is_mobile(),
            'i18n'      => [
                'loading'      => __( 'Carregando...', 'nosfirnews' ),
                'error'        => __( 'Erro ao carregar', 'nosfirnews' ),
                'close'        => __( 'Fechar', 'nosfirnews' ),
                'readMore'     => __( 'Leia mais', 'nosfirnews' ),
            ]
        ] );
    }
    
    // ===== COMPONENTES OPCIONAIS =====
    
    // Bootstrap (se habilitado)
    if ( (bool) get_theme_mod( 'nn_enable_bootstrap', false ) ) {
        wp_enqueue_style(
            'bootstrap5',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
            [],
            '5.3.2'
        );
        
        wp_enqueue_script(
            'bootstrap5',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
            [],
            '5.3.2',
            true
        );
    }
    
    // ===== CONDITIONAL LOADS =====
    
    // Comments reply
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'nosfirnews_enqueue_assets' );

// ============================================
// 4. CUSTOMIZER ASSETS
// ============================================

function nosfirnews_customizer_assets() {
    
    // CSS do Customizer
    $customizer_css = NOSFIRNEWS_ASSETS_DIR . '/css/customizer/controls.css';
    if ( file_exists( $customizer_css ) ) {
        wp_enqueue_style(
            'nosfirnews-customizer-controls',
            NOSFIRNEWS_ASSETS_URI . '/css/customizer/controls.css',
            [ 'customize-controls' ],
            nosfirnews_asset_version( 'css/customizer/controls.css' )
        );
    }
    
    // JS do Customizer
    $customizer_js = NOSFIRNEWS_ASSETS_DIR . '/js/customizer/controls.js';
    if ( file_exists( $customizer_js ) ) {
        wp_enqueue_script(
            'nosfirnews-customizer-controls',
            NOSFIRNEWS_ASSETS_URI . '/js/customizer/controls.js',
            [ 'customize-controls', 'jquery' ],
            nosfirnews_asset_version( 'js/customizer/controls.js' ),
            true
        );
    }
}
add_action( 'customize_controls_enqueue_scripts', 'nosfirnews_customizer_assets' );

// ============================================
// 5. CUSTOMIZER PREVIEW
// ============================================

function nosfirnews_customizer_preview() {
    
    $preview_js = NOSFIRNEWS_ASSETS_DIR . '/js/customizer/preview.js';
    if ( file_exists( $preview_js ) ) {
        wp_enqueue_script(
            'nosfirnews-customizer-preview',
            NOSFIRNEWS_ASSETS_URI . '/js/customizer/preview.js',
            [ 'customize-preview', 'jquery' ],
            nosfirnews_asset_version( 'js/customizer/preview.js' ),
            true
        );
    }
}
add_action( 'customize_preview_init', 'nosfirnews_customizer_preview' );

// ============================================
// 6. ADMIN ASSETS
// ============================================

function nosfirnews_admin_assets( $hook ) {
    
    // Apenas em páginas do tema
    $theme_pages = [ 'appearance_page_nosfirnews-admin', 'themes.php' ];
    
    if ( ! in_array( $hook, $theme_pages, true ) ) {
        return;
    }
    
    // CSS Admin
    $admin_css = NOSFIRNEWS_ASSETS_DIR . '/css/admin.css';
    if ( file_exists( $admin_css ) ) {
        wp_enqueue_style(
            'nosfirnews-admin',
            NOSFIRNEWS_ASSETS_URI . '/css/admin.css',
            [],
            nosfirnews_asset_version( 'css/admin.css' )
        );
    }
    
    // JS Admin
    $admin_js = NOSFIRNEWS_ASSETS_DIR . '/js/admin.js';
    if ( file_exists( $admin_js ) ) {
        wp_enqueue_script(
            'nosfirnews-admin',
            NOSFIRNEWS_ASSETS_URI . '/js/admin.js',
            [ 'jquery' ],
            nosfirnews_asset_version( 'js/admin.js' ),
            true
        );
    }
}
add_action( 'admin_enqueue_scripts', 'nosfirnews_admin_assets' );

// ============================================
// 7. EDITOR ASSETS (Gutenberg)
// ============================================

function nosfirnews_editor_assets() {
    
    // CSS do Editor
    $editor_css = NOSFIRNEWS_ASSETS_DIR . '/css/editor.css';
    if ( file_exists( $editor_css ) ) {
        wp_enqueue_style(
            'nosfirnews-editor',
            NOSFIRNEWS_ASSETS_URI . '/css/editor.css',
            [],
            nosfirnews_asset_version( 'css/editor.css' )
        );
    }
    
    // JS do Editor
    $editor_js = NOSFIRNEWS_ASSETS_DIR . '/js/editor.js';
    if ( file_exists( $editor_js ) ) {
        wp_enqueue_script(
            'nosfirnews-editor',
            NOSFIRNEWS_ASSETS_URI . '/js/editor.js',
            [ 'wp-blocks', 'wp-element', 'wp-editor' ],
            nosfirnews_asset_version( 'js/editor.js' ),
            true
        );
    }
}
add_action( 'enqueue_block_editor_assets', 'nosfirnews_editor_assets' );

// ============================================
// 8. PRELOAD CRITICAL ASSETS
// ============================================

function nosfirnews_preload_assets() {
    
    // Preload CSS principal
    echo '<link rel="preload" href="' . esc_url( NOSFIRNEWS_ASSETS_URI . '/css/main.css' ) . '" as="style">';
    
    // Preload JS principal
    echo '<link rel="preload" href="' . esc_url( NOSFIRNEWS_ASSETS_URI . '/js/theme.js' ) . '" as="script">';
    
    // Preload fontes se existirem
    $fonts_dir = NOSFIRNEWS_ASSETS_DIR . '/fonts';
    if ( is_dir( $fonts_dir ) ) {
        $fonts = glob( $fonts_dir . '/*.{woff2,woff}', GLOB_BRACE );
        foreach ( array_slice( $fonts, 0, 2 ) as $font ) { // Apenas 2 fontes principais
            $font_url = NOSFIRNEWS_ASSETS_URI . '/fonts/' . basename( $font );
            $ext = pathinfo( $font, PATHINFO_EXTENSION );
            echo '<link rel="preload" href="' . esc_url( $font_url ) . '" as="font" type="font/' . esc_attr( $ext ) . '" crossorigin>';
        }
    }
}
add_action( 'wp_head', 'nosfirnews_preload_assets', 1 );

// ============================================
// 9. REMOVER ASSETS DESNECESSÁRIOS
// ============================================

function nosfirnews_dequeue_unnecessary_assets() {
    
    // Remove Emoji scripts se não necessário
    if ( ! get_theme_mod( 'nn_enable_emojis', false ) ) {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
    }
    
    // Remove block library CSS se não usar Gutenberg
    if ( ! get_theme_mod( 'nn_enable_block_css', true ) ) {
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
    }
}
add_action( 'wp_enqueue_scripts', 'nosfirnews_dequeue_unnecessary_assets', 100 );

// ============================================
// 10. INLINE CRITICAL CSS
// ============================================

function nosfirnews_inline_critical_css() {
    
    $critical_css = NOSFIRNEWS_ASSETS_DIR . '/css/critical.css';
    
    if ( file_exists( $critical_css ) ) {
        echo '<style id="nosfirnews-critical-css">';
        echo file_get_contents( $critical_css );
        echo '</style>';
    }
}
add_action( 'wp_head', 'nosfirnews_inline_critical_css', 1 );

// Continua no próximo artifact...