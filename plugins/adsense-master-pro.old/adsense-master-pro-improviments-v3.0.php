<?php
/**
 * MELHORIAS PARA ADSENSE MASTER PRO v3.0
 * 
 * Arquivo de documentação com todas as melhorias para remover limitação de anúncios
 * e adicionar novos recursos avançados de posicionamento
 * 
 * @package AdSense Master Pro
 * @version 3.0.0
 * @since 2.1.0
 */

// ============================================================================
// 1. NOVAS OPÇÕES DE CONFIGURAÇÃO (Substituir em class AdSenseMasterPro)
// ============================================================================

/**
 * ANTES (v2.0.0):
 * 'max_ads_per_page' => 10,  // ❌ Limitado a 10
 * 
 * DEPOIS (v3.0.0):
 */
$default_options_v3 = array(
    // ✅ Sistema de Limite Flexível
    'max_ads_per_page' => 999,  // ✅ Sem limite prático
    'enable_max_ads_limit' => 0,  // ✅ Desabilitado por padrão
    'max_ads_per_page_custom' => 50,  // ✅ Limite customizável (opcional)
    'max_ads_per_section' => 999,  // ✅ Máximo por seção
    'ads_per_1000_words' => 1,  // ✅ 1 anúncio a cada 1000 palavras (novo)
    
    // ✅ Sistema de Frequência de Anúncios
    'ad_frequency_mode' => 'unlimited',  // ✅ unlimited|fixed|per_words|smart
    'min_words_between_ads' => 250,  // ✅ Mínimo de palavras entre anúncios
    'min_paragraphs_between_ads' => 2,  // ✅ Mínimo de parágrafos entre anúncios
    
    // ✅ Posicionamento Avançado (15 posições!)
    'ad_positions' => array(
        // === ANTES DO CONTEÚDO (3) ===
        'before_title' => 0,
        'before_excerpt' => 0,
        'before_content' => 1,
        
        // === DENTRO DO CONTEÚDO (6) ===
        'after_first_paragraph' => 1,
        'after_nth_paragraph' => 1,
        'middle_content' => 1,
        'before_last_paragraph' => 1,
        'every_nth_paragraph' => 1,
        'sticky_on_scroll' => 0,
        
        // === DEPOIS DO CONTEÚDO (3) ===
        'after_content' => 1,
        'after_tags' => 0,
        'after_related_posts' => 0,
        
        // === SIDEBAR E OUTROS (3) ===
        'primary_sidebar' => 1,
        'secondary_sidebar' => 0,
        'footer_sticky' => 1,
    ),
    
    // ✅ Posicionamento Inteligente por Tipo
    'position_by_content_type' => array(
        'post' => array(
            'after_first_paragraph' => 1,
            'middle_content' => 1,
            'after_content' => 1,
        ),
        'page' => array(
            'before_content' => 1,
            'after_content' => 1,
        ),
        'custom_post' => array(),
    ),
    
    // ✅ Responsividade por Dispositivo
    'device_specific_positions' => array(
        'mobile' => array(
            'sticky_positions' => array('footer_sticky'),
            'hide_positions' => array('before_title', 'before_excerpt'),
            'max_ads' => 5,
        ),
        'tablet' => array(
            'sticky_positions' => array(),
            'hide_positions' => array('before_title'),
            'max_ads' => 7,
        ),
        'desktop' => array(
            'sticky_positions' => array('sticky_on_scroll'),
            'hide_positions' => array(),
            'max_ads' => 999,
        ),
    ),
    
    // ✅ Anúncios Flutuantes (Sticky)
    'floating_ads' => array(
        'top' => 0,
        'bottom' => 1,
        'left' => 0,
        'right' => 0,
        'float_speed' => 'normal',  // slow|normal|fast
        'show_after_scroll' => 500,  // pixels
        'close_button' => 1,
        'auto_hide_after' => 0,  // 0 = nunca
    ),
    
    // ✅ Anúncios em Colunas (Inline)
    'inline_ads' => array(
        'enable_inline' => 1,
        'max_inline_ads' => 999,
        'position_in_text' => array('after_sentence', 'after_paragraph'),
    ),
    
    // ✅ Anúncios Pop-up / Lightbox
    'popup_ads' => array(
        'enable_popup' => 0,
        'trigger_on' => 'time|scroll|exit',  // Quando mostrar
        'trigger_value' => 3,  // segundos ou pixels
        'frequency' => 'once_per_session',  // once_per_session|once_per_day
        'max_popups' => 1,
        'dismiss_button' => 1,
    ),
    
    // ✅ Anúncios em Widget
    'widget_ads' => array(
        'enable_widget_ads' => 1,
        'max_widget_ads' => 999,
        'widget_priority' => array(
            'primary_sidebar' => 10,
            'secondary_sidebar' => 8,
            'footer_widgets' => 5,
        ),
    ),
    
    // ✅ Anúncios entre Posts
    'between_posts_ads' => array(
        'enable' => 1,
        'every_nth_post' => 2,  // A cada 2 posts
        'max_ads' => 999,
        'only_on_archives' => 0,
    ),
    
    // ✅ Anúncios em Comentários
    'comment_ads' => array(
        'enable' => 1,
        'every_nth_comment' => 5,
        'max_comment_ads' => 999,
    ),
);

// ============================================================================
// 2. NOVAS TABELAS DO BANCO DE DADOS (v3.0)
// ============================================================================

/**
 * Adicionar à função create_tables()
 */

// Tabela de Posições de Anúncios
$table_ad_positions = $wpdb->prefix . 'amp_ad_positions';
$sql_ad_positions = "CREATE TABLE $table_ad_positions (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    ad_id mediumint(9) NOT NULL,
    position_key varchar(100) NOT NULL,
    position_name varchar(255),
    position_order int(3) DEFAULT 10,
    enabled tinyint(1) DEFAULT 1,
    device_type varchar(20) DEFAULT 'all',
    content_type varchar(50) DEFAULT 'all',
    custom_css text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ad_id (ad_id),
    KEY position_key (position_key),
    KEY device_type (device_type)
) $charset_collate;";

// Tabela de Anúncios Flutuantes
$table_floating_ads = $wpdb->prefix . 'amp_floating_ads';
$sql_floating_ads = "CREATE TABLE $table_floating_ads (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    ad_id mediumint(9) NOT NULL,
    position varchar(20) NOT NULL,
    show_after_scroll int(5) DEFAULT 0,
    close_button tinyint(1) DEFAULT 1,
    auto_hide_after int(5) DEFAULT 0,
    z_index int(5) DEFAULT 9999,
    mobile_enabled tinyint(1) DEFAULT 1,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ad_id (ad_id),
    KEY position (position)
) $charset_collate;";

// Tabela de Anúncios Pop-up
$table_popup_ads = $wpdb->prefix . 'amp_popup_ads';
$sql_popup_ads = "CREATE TABLE $table_popup_ads (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    ad_id mediumint(9) NOT NULL,
    trigger_type varchar(20) NOT NULL,
    trigger_value int(5),
    frequency varchar(50) DEFAULT 'once_per_session',
    max_shows int(3) DEFAULT 1,
    dismiss_button tinyint(1) DEFAULT 1,
    animation varchar(50) DEFAULT 'fadeIn',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ad_id (ad_id),
    KEY trigger_type (trigger_type)
) $charset_collate;";

// Tabela de Anúncios em Comentários
$table_comment_ads = $wpdb->prefix . 'amp_comment_ads';
$sql_comment_ads = "CREATE TABLE $table_comment_ads (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    ad_id mediumint(9) NOT NULL,
    every_nth_comment int(3) DEFAULT 5,
    max_ads int(3) DEFAULT 999,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ad_id (ad_id)
) $charset_collate;";

// ============================================================================
// 3. NOVOS MÉTODOS PARA CLASSE AdSenseMasterPro
// ============================================================================

/**
 * Adicionar à classe AdSenseMasterPro
 */

class AdSenseMasterPro_v3 extends AdSenseMasterPro {
    
    /**
     * Calcula número ideal de anúncios baseado em comprimento do conteúdo
     * 
     * @param int $word_count Número de palavras
     * @param string $frequency_mode Modo de frequência (unlimited|fixed|per_words|smart)
     * @return int Número ideal de anúncios
     */
    public function calculate_ideal_ad_count($word_count, $frequency_mode = 'unlimited') {
        
        switch ($frequency_mode) {
            case 'unlimited':
                return 999;  // Sem limite
                
            case 'fixed':
                return intval($this->options['max_ads_per_page_custom'] ?? 50);
                
            case 'per_words':
                // 1 anúncio a cada X palavras
                $words_per_ad = 1000 / intval($this->options['ads_per_1000_words'] ?? 1);
                return max(1, intval($word_count / $words_per_ad));
                
            case 'smart':
                // Algoritmo inteligente
                if ($word_count < 500) {
                    return 1;
                } elseif ($word_count < 1000) {
                    return 2;
                } elseif ($word_count < 2000) {
                    return 3;
                } elseif ($word_count < 3000) {
                    return 4;
                } else {
                    return intval($word_count / 500);  // 1 a cada 500 palavras
                }
                
            default:
                return 999;
        }
    }
    
    /**
     * Insere anúncios respeitando distância mínima entre eles
     * 
     * @param string $content Conteúdo do post
     * @return string Conteúdo com anúncios inseridos
     */
    public function insert_ads_with_spacing($content) {
        $min_words = intval($this->options['min_words_between_ads'] ?? 250);
        $min_paragraphs = intval($this->options['min_paragraphs_between_ads'] ?? 2);
        
        $paragraphs = explode('</p>', $content);
        $total_words = str_word_count(strip_tags($content));
        $max_ads = $this->calculate_ideal_ad_count($total_words, $this->options['ad_frequency_mode']);
        
        $inserted_ads = 0;
        $words_since_last_ad = 0;
        $paragraphs_since_last_ad = 0;
        
        foreach ($paragraphs as $index => &$paragraph) {
            if ($inserted_ads >= $max_ads) break;
            
            $paragraph_words = str_word_count(strip_tags($paragraph));
            $words_since_last_ad += $paragraph_words;
            $paragraphs_since_last_ad++;
            
            // Verificar espaçamento mínimo
            if ($words_since_last_ad >= $min_words && $paragraphs_since_last_ad >= $min_paragraphs) {
                $paragraph .= '</p>' . $this->get_next_ad_html();
                $inserted_ads++;
                $words_since_last_ad = 0;
                $paragraphs_since_last_ad = 0;
            }
        }
        
        return implode('</p>', $paragraphs);
    }
    
    /**
     * Insere anúncios flutuantes (sticky)
     * 
     * @return string HTML dos anúncios flutuantes
     */
    public function insert_floating_ads() {
        $floating_options = $this->options['floating_ads'] ?? array();
        
        if (!intval($floating_options['enable'] ?? 0)) {
            return '';
        }
        
        $html = '';
        $positions = array('top', 'bottom', 'left', 'right');
        
        foreach ($positions as $position) {
            if (intval($floating_options[$position] ?? 0)) {
                $html .= $this->render_floating_ad($position, $floating_options);
            }
        }
        
        return $html;
    }
    
    /**
     * Renderiza um anúncio flutuante
     * 
     * @param string $position Posição (top|bottom|left|right)
     * @param array $options Opções
     * @return string HTML
     */
    private function render_floating_ad($position, $options) {
        $ad = $this->get_next_ad_html();
        $z_index = intval($options['z_index'] ?? 9999);
        $show_after = intval($options['show_after_scroll'] ?? 500);
        $close_button = intval($options['close_button'] ?? 1);
        
        $css_position = '';
        switch ($position) {
            case 'top':
                $css_position = 'top: 0; left: 0; right: 0; width: 100%;';
                break;
            case 'bottom':
                $css_position = 'bottom: 0; left: 0; right: 0; width: 100%;';
                break;
            case 'left':
                $css_position = 'left: 0; top: 50%;';
                break;
            case 'right':
                $css_position = 'right: 0; top: 50%;';
                break;
        }
        
        $html = '<div class="amp-floating-ad amp-floating-' . $position . '" style="position: fixed; ' . $css_position . '; z-index: ' . $z_index . ';" data-show-after="' . $show_after . '">';
        
        if ($close_button) {
            $html .= '<button class="amp-floating-close" aria-label="Fechar">×</button>';
        }
        
        $html .= $ad;
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Insere anúncios em pop-up
     * 
     * @return string HTML do pop-up
     */
    public function insert_popup_ads() {
        $popup_options = $this->options['popup_ads'] ?? array();
        
        if (!intval($popup_options['enable'] ?? 0)) {
            return '';
        }
        
        $trigger_type = $popup_options['trigger_on'] ?? 'time';
        $trigger_value = intval($popup_options['trigger_value'] ?? 3);
        $animation = $popup_options['animation'] ?? 'fadeIn';
        
        $ad = $this->get_next_ad_html();
        
        $html = '<div id="amp-popup-ad" class="amp-popup-ad amp-popup-' . $animation . '" style="display: none;">';
        $html .= '<div class="amp-popup-overlay"></div>';
        $html .= '<div class="amp-popup-content">';
        
        if (intval($popup_options['dismiss_button'] ?? 1)) {
            $html .= '<button class="amp-popup-close" aria-label="Fechar">×</button>';
        }
        
        $html .= $ad;
        $html .= '</div></div>';
        
        // Script para trigger
        $html .= '<script>
        (function() {
            var trigger = "' . $trigger_type . '";
            var value = ' . $trigger_value . ';
            
            if (trigger === "time") {
                setTimeout(function() {
                    document.getElementById("amp-popup-ad").style.display = "block";
                }, value * 1000);
            } else if (trigger === "scroll") {
                window.addEventListener("scroll", function() {
                    if (window.pageYOffset > value && document.getElementById("amp-popup-ad").style.display === "none") {
                        document.getElementById("amp-popup-ad").style.display = "block";
                    }
                });
            }
            
            document.querySelector(".amp-popup-close")?.addEventListener("click", function() {
                document.getElementById("amp-popup-ad").style.display = "none";
            });
        })();
        </script>';
        
        return $html;
    }
    
    /**
     * Insere anúncios entre posts (em listas/archives)
     * 
     * @param string $content
     * @return string
     */
    public function insert_ads_between_posts($content) {
        if (!is_archive() && !is_home() && !is_search()) {
            return $content;
        }
        
        $between_options = $this->options['between_posts_ads'] ?? array();
        
        if (!intval($between_options['enable'] ?? 0)) {
            return $content;
        }
        
        $every_nth = intval($between_options['every_nth_post'] ?? 2);
        $max_ads = intval($between_options['max_ads'] ?? 999);
        
        // Adicionar classe ao post para identificar
        $content = str_replace('</article>', '</article><div class="amp-post-separator"></div>', $content);
        
        return $content;
    }
    
    /**
     * Insere anúncios em comentários
     * 
     * @param string $content Conteúdo dos comentários
     * @return string
     */
    public function insert_comment_ads($content) {
        $comment_options = $this->options['comment_ads'] ?? array();
        
        if (!intval($comment_options['enable'] ?? 0)) {
            return $content;
        }
        
        $every_nth = intval($comment_options['every_nth_comment'] ?? 5);
        $max_ads = intval($comment_options['max_comment_ads'] ?? 999);
        
        // Aqui entra lógica de inserção entre comentários
        
        return $content;
    }
    
    /**
     * Obtém próximo anúncio HTML
     * 
     * @return string HTML do anúncio
     */
    private function get_next_ad_html() {
        $ads = $this->get_ads_by_priority();
        
        if (empty($ads)) {
            return '';
        }
        
        // Retornar primeiro anúncio disponível
        $ad = $ads[0];
        return $this->render_ad_with_tracking($ad);
    }
    
    /**
     * Obtém estatísticas de posicionamento de anúncios
     * 
     * @return array Estatísticas
     */
    public function get_position_statistics() {
        global $wpdb;
        
        $stats = $wpdb->get_results("
            SELECT 
                position_key,
                COUNT(*) as total_ads,
                AVG(CTR) as avg_ctr,
                SUM(impressions) as total_impressions,
                SUM(clicks) as total_clicks
            FROM {$wpdb->prefix}amp_ad_positions ap
            LEFT JOIN {$wpdb->prefix}amp_analytics aa ON ap.ad_id = aa.ad_id
            GROUP BY position_key
            ORDER BY total_clicks DESC
        ");
        
        return $stats ?: array();
    }
}

// ============================================================================
// 4. CSS PARA NOVOS RECURSOS (Adicionar ao admin-style.css)
// ============================================================================

$new_css = "
/* ===== ANÚNCIOS FLUTUANTES ===== */
.amp-floating-ad {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    max-width: 300px;
}

.amp-floating-ad.amp-floating-top {
    top: 20px;
    left: 20px;
}

.amp-floating-ad.amp-floating-bottom {
    bottom: 20px;
    left: 20px;
}

.amp-floating-ad.amp-floating-left {
    left: 10px;
}

.amp-floating-ad.amp-floating-right {
    right: 10px;
}

.amp-floating-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
}

/* ===== ANÚNCIOS POP-UP ===== */
.amp-popup-ad {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
}

.amp-popup-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
}

.amp-popup-content {
    position: relative;
    background: white;
    padding: 30px;
    border-radius: 8px;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.amp-popup-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
}

/* ===== ANIMAÇÕES ===== */
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.amp-popup-fadeIn {
    animation: fadeIn 0.3s ease;
}

@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.amp-popup-slideUp {
    animation: slideUp 0.3s ease;
}
";

// ============================================================================
// 5. JAVASCRIPT PARA NOVOS RECURSOS (Adicionar ao frontend.js)
// ============================================================================

$new_javascript = "
// Floatin Ads Controller
var AMP_FloatingAds = {
    init: function() {
        var floating = document.querySelectorAll('.amp-floating-ad');
        floating.forEach(function(ad) {
            var showAfter = parseInt(ad.dataset.showAfter) || 0;
            
            if (showAfter > 0) {
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > showAfter) {
                        ad.style.opacity = '1';
                        ad.style.visibility = 'visible';
                    }
                });
            }
            
            // Close button
            var closeBtn = ad.querySelector('.amp-floating-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    ad.style.display = 'none';
                });
            }
        });
    }
};

// Popup Ads Controller
var AMP_PopupAds = {
    init: function() {
        var popup = document.getElementById('amp-popup-ad');
        if (!popup) return;
        
        var closeBtn = popup.querySelector('.amp-popup-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                popup.style.display = 'none';
            });
        }
        
        // Fechar ao clicar no overlay
        var overlay = popup.querySelector('.amp-popup-overlay');
        if (overlay) {
            overlay.addEventListener('click', function() {
                popup.style.display = 'none';
            });
        }
    }
};

// Inicializar ao carregar
document.addEventListener('DOMContentLoaded', function() {
    AMP_FloatingAds.init();
    AMP_PopupAds.init();
});
";

// ============================================================================
// 6. MIGRAÇÃO DE BANCO DE DADOS (DB Upgrade Script)
// ============================================================================

/**
 * Adicionar à função activate() para migração automática
 */
public function migrate_to_v3() {
    $installed_ver = get_option('amp_db_version');
    
    if (version_compare($installed_ver, '3.0', '<')) {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Criar novas tabelas
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // ... criar tabelas conforme seção 2 acima ...
        
        // Migrar dados antigos
        $wpdb->query("
            INSERT INTO {$wpdb->prefix}amp_ad_positions 
            (ad_id, position_key, position_name, position_order, enabled)
            SELECT id, 'after_first_paragraph', 'After First Paragraph', 10, 1
            FROM {$wpdb->prefix}amp_ads
            WHERE position = 'after_first_paragraph'
        ");
        
        // Atualizar versão
        update_option('amp_db_version', '3.0');
        error_log('AdSense Master Pro: Migrado para v3.0 com sucesso');
    }
}

// ============================================================================
// 7. EXEMPLO DE USO (Adicionar ao functions.php do tema)
// ============================================================================

/**
 * Exemplo de como usar os novos recursos
 */
\$example_usage = "
// 1. Ativar anúncios ilimitados
update_option('amp_options', array_merge(
    get_option('amp_options'),
    array('max_ads_per_page' => 999)
));

// 2. Configurar anúncios por frequência de palavras
update_option('amp_options', array_merge(
    get_option('amp_options'),
    array(
        'ad_frequency_mode' => 'per_words',
        'ads_per_1000_words' => 1,  // 1 anúncio a cada 1000 palavras
        'min_words_between_ads' => 300
    )
));

// 3. Habilitar anúncios flutuantes
update_option('amp_options', array_merge(
    get_option('amp_options'),
    array(
        'floating_ads' => array(
            'bottom' => 1,
            'show_after_scroll' => 500,
            'close_button' => 1
        )
    )
));

// 4. Usar shortcodes para inserir manualmente
echo do_shortcode('[amp_ad id=\"1\"]');

// 5. Usar funções
amp_display_ad(1);
";

?>