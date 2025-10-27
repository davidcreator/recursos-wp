<?php
/**
 * Script para criar a página demonstrativa no WordPress
 * Execute este arquivo acessando: http://localhost/wordpress/wp-content/themes/nosfirnews/create-demo-page.php
 */

// Carregar o WordPress
require_once('../../../wp-load.php');

// Verificar se o usuário tem permissões
if (!current_user_can('manage_options')) {
    wp_die('Você não tem permissão para executar este script.');
}

// Verificar se a página já existe
$existing_page = get_page_by_path('pagina-exemplo');

if ($existing_page) {
    echo '<h2>Página já existe!</h2>';
    echo '<p>A página "pagina-exemplo" já existe com ID: ' . $existing_page->ID . '</p>';
    echo '<p><a href="' . get_permalink($existing_page->ID) . '" target="_blank">Ver página</a></p>';
    echo '<p><a href="' . admin_url('post.php?post=' . $existing_page->ID . '&action=edit') . '" target="_blank">Editar página</a></p>';
    
    // Atualizar o template da página existente
    update_post_meta($existing_page->ID, '_wp_page_template', 'templates/page-templates/page-demo.php');
    echo '<p><strong>Template atualizado para:</strong> templates/page-templates/page-demo.php</p>';
    
} else {
    // Criar nova página
    $page_data = array(
        'post_title'    => 'Página Demonstrativa',
        'post_content'  => '<h2>Bem-vindo à Página Demonstrativa do NosfirNews</h2>

<p>Esta página foi criada para demonstrar todos os recursos e funcionalidades do tema NosfirNews v2.0.0. Aqui você pode ver em ação:</p>

<ul>
<li><strong>Design Responsivo:</strong> Layout que se adapta a qualquer dispositivo</li>
<li><strong>Componentes Interativos:</strong> Cards, grids e elementos dinâmicos</li>
<li><strong>Acessibilidade:</strong> Navegação otimizada para todos os usuários</li>
<li><strong>Performance:</strong> Carregamento rápido e otimizado</li>
<li><strong>SEO:</strong> Estrutura otimizada para mecanismos de busca</li>
<li><strong>AMP Ready:</strong> Suporte completo ao Accelerated Mobile Pages</li>
</ul>

<h3>Recursos Técnicos</h3>
<p>O tema NosfirNews foi desenvolvido com as melhores práticas de desenvolvimento web, incluindo:</p>

<blockquote>
<p>"Um tema moderno e profissional para sites de notícias, blogs e portais de conteúdo, com foco em performance, acessibilidade e experiência do usuário."</p>
</blockquote>

<h3>Personalização</h3>
<p>Através do WordPress Customizer, você pode personalizar cores, fontes, layouts e muito mais para adequar o tema à sua identidade visual.</p>',
        'post_excerpt'  => 'Demonstração completa dos recursos e funcionalidades do tema NosfirNews v2.0.0, incluindo layouts responsivos, acessibilidade, SEO otimizado e suporte ao AMP.',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_name'     => 'pagina-exemplo',
        'comment_status' => 'closed',
        'ping_status'   => 'closed',
        'post_author'   => 1
    );

    // Inserir a página
    $page_id = wp_insert_post($page_data);

    if ($page_id && !is_wp_error($page_id)) {
        echo '<h2>Página criada com sucesso!</h2>';
        echo '<p><strong>ID da página:</strong> ' . $page_id . '</p>';
        echo '<p><strong>URL:</strong> <a href="' . get_permalink($page_id) . '" target="_blank">' . get_permalink($page_id) . '</a></p>';
        
        // Definir o template personalizado
        update_post_meta($page_id, '_wp_page_template', 'templates/page-templates/page-demo.php');
        echo '<p><strong>Template aplicado:</strong> templates/page-templates/page-demo.php</p>';
        
        // Adicionar metadados para SEO
        update_post_meta($page_id, '_yoast_wpseo_title', 'Página Demonstrativa - NosfirNews Theme');
        update_post_meta($page_id, '_yoast_wpseo_metadesc', 'Demonstração completa dos recursos do tema NosfirNews: design responsivo, acessibilidade, SEO otimizado e suporte AMP.');
        update_post_meta($page_id, '_yoast_wpseo_canonical', home_url('/pagina-exemplo/'));
        update_post_meta($page_id, '_yoast_wpseo_opengraph-title', 'Página Demonstrativa - NosfirNews Theme');
        update_post_meta($page_id, '_yoast_wpseo_opengraph-description', 'Veja todos os recursos do tema NosfirNews em ação: layouts responsivos, componentes interativos e muito mais.');
        update_post_meta($page_id, '_yoast_wpseo_twitter-title', 'Página Demonstrativa - NosfirNews Theme');
        update_post_meta($page_id, '_yoast_wpseo_twitter-description', 'Demonstração completa do tema NosfirNews com todos os seus recursos e funcionalidades.');
        
        echo '<p><strong>Metadados SEO adicionados com sucesso!</strong></p>';
        
        echo '<div style="margin-top: 20px; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;">';
        echo '<h3>Próximos passos:</h3>';
        echo '<ol>';
        echo '<li><a href="' . get_permalink($page_id) . '" target="_blank">Visualizar a página demonstrativa</a></li>';
        echo '<li><a href="' . admin_url('post.php?post=' . $page_id . '&action=edit') . '" target="_blank">Editar conteúdo da página</a></li>';
        echo '<li><a href="' . admin_url('customize.php') . '" target="_blank">Personalizar o tema</a></li>';
        echo '</ol>';
        echo '</div>';
        
    } else {
        echo '<h2>Erro ao criar a página!</h2>';
        if (is_wp_error($page_id)) {
            echo '<p><strong>Erro:</strong> ' . $page_id->get_error_message() . '</p>';
        }
    }
}

echo '<hr>';
echo '<h3>Informações do Sistema</h3>';
echo '<p><strong>Tema ativo:</strong> ' . get_template() . '</p>';
echo '<p><strong>Versão do WordPress:</strong> ' . get_bloginfo('version') . '</p>';
echo '<p><strong>URL do site:</strong> ' . home_url() . '</p>';

// Verificar se o template existe
$template_path = get_template_directory() . '/templates/page-templates/page-demo.php';
if (file_exists($template_path)) {
    echo '<p><strong>Template personalizado:</strong> ✅ Encontrado</p>';
} else {
    echo '<p><strong>Template personalizado:</strong> ❌ Não encontrado em: ' . $template_path . '</p>';
}

echo '<hr>';
echo '<p><a href="' . admin_url() . '">← Voltar ao painel administrativo</a></p>';
?>