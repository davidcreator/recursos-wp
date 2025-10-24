<?php
namespace WPSP\Optimization;

if (!defined('ABSPATH')) exit;

class Minifier {
    
    private $minify_html;
    private $minify_css;
    private $minify_js;
    
    public function __construct() {
        $this->minify_html = get_option('wpsp_minify_html', 0);
        $this->minify_css = get_option('wpsp_minify_css', 0);
        $this->minify_js = get_option('wpsp_minify_js', 0);
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // HTML Minification
        if ($this->minify_html) {
            add_action('template_redirect', [$this, 'start_html_buffer'], 0);
        }
        
        // CSS Minification
        if ($this->minify_css) {
            add_filter('style_loader_tag', [$this, 'minify_inline_css'], 10, 2);
        }
        
        // JS Minification
        if ($this->minify_js) {
            add_filter('script_loader_tag', [$this, 'minify_inline_js'], 10, 2);
        }
    }
    
    /**
     * Iniciar buffer HTML
     */
    public function start_html_buffer() {
        if (!is_admin() && !$this->is_excluded_page()) {
            ob_start([$this, 'minify_html_output']);
        }
    }
    
    /**
     * Verificar se página deve ser excluída
     */
    private function is_excluded_page() {
        // Excluir páginas de carrinho, checkout, etc
        $excluded_pages = apply_filters('wpsp_minify_excluded_pages', [
            'wp-login.php',
            'wp-admin',
            'xmlrpc.php'
        ]);
        
        $current_url = $_SERVER['REQUEST_URI'];
        
        foreach ($excluded_pages as $page) {
            if (strpos($current_url, $page) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Minificar HTML
     */
    public function minify_html_output($html) {
        if (empty($html) || !is_string($html)) {
            return $html;
        }
        
        // Proteger tags pre, textarea, script e style
        $replace = [];
        $protected_tags = ['pre', 'textarea', 'script', 'style'];
        
        foreach ($protected_tags as $tag) {
            preg_match_all('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', $html, $matches);
            foreach ($matches[0] as $i => $match) {
                $placeholder = '___PROTECTED_' . strtoupper($tag) . '_' . $i . '___';
                $replace[$placeholder] = $match;
                $html = str_replace($match, $placeholder, $html);
            }
        }
        
        // Remover comentários HTML (exceto IE conditionals)
        $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
        
        // Remover espaços em branco extras
        $html = preg_replace('/\s+/', ' ', $html);
        
        // Remover espaços entre tags
        $html = preg_replace('/>\s+</', '><', $html);
        
        // Remover espaços ao redor de atributos
        $html = preg_replace('/\s+([^\s=]+)=/', ' $1=', $html);
        
        // Restaurar conteúdo protegido
        $html = str_replace(array_keys($replace), array_values($replace), $html);
        
        return trim($html);
    }
    
    /**
     * Minificar CSS inline
     */
    public function minify_inline_css($tag, $handle) {
        // Buscar CSS inline
        if (preg_match('/<style[^>]*>(.*?)<\/style>/is', $tag, $matches)) {
            $css = $matches[1];
            $minified_css = $this->minify_css_content($css);
            $tag = str_replace($css, $minified_css, $tag);
        }
        
        return $tag;
    }
    
    /**
     * Minificar conteúdo CSS
     */
    private function minify_css_content($css) {
        // Remover comentários
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remover espaços em branco
        $css = preg_replace('/\s+/', ' ', $css);
        
        // Remover espaços desnecessários
        $css = preg_replace('/\s*([\{\}:;,])\s*/', '$1', $css);
        
        // Remover último ponto e vírgula
        $css = preg_replace('/;(?=\})/', '', $css);
        
        // Remover zeros desnecessários
        $css = preg_replace('/(:|\s)0+\.(\d+)/', '$1.$2', $css);
        
        return trim($css);
    }
    
    /**
     * Minificar JS inline
     */
    public function minify_inline_js($tag, $handle) {
        // Buscar JS inline
        if (preg_match('/<script[^>]*>(.*?)<\/script>/is', $tag, $matches)) {
            if (!empty($matches[1])) {
                $js = $matches[1];
                $minified_js = $this->minify_js_content($js);
                $tag = str_replace($js, $min            ]);
        }
    }
}