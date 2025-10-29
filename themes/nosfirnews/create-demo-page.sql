-- Script SQL para criar a página demonstrativa no WordPress
-- Execute este script no phpMyAdmin ou ferramenta similar

-- Inserir a página demonstrativa
INSERT INTO wp_posts (
    post_author,
    post_date,
    post_date_gmt,
    post_content,
    post_title,
    post_excerpt,
    post_status,
    comment_status,
    ping_status,
    post_password,
    post_name,
    to_ping,
    pinged,
    post_modified,
    post_modified_gmt,
    post_content_filtered,
    post_parent,
    guid,
    menu_order,
    post_type,
    post_mime_type,
    comment_count
) VALUES (
    1, -- post_author (ID do usuário admin)
    NOW(), -- post_date
    UTC_TIMESTAMP(), -- post_date_gmt
    '<h2>Bem-vindo à Página Demonstrativa do NosfirNews</h2>

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
<p>Através do WordPress Customizer, você pode personalizar cores, fontes, layouts e muito mais para adequar o tema à sua identidade visual.</p>', -- post_content
    'Página Demonstrativa', -- post_title
    'Demonstração completa dos recursos e funcionalidades do tema NosfirNews v2.0.0, incluindo layouts responsivos, acessibilidade, SEO otimizado e suporte ao AMP.', -- post_excerpt
    'publish', -- post_status
    'closed', -- comment_status
    'closed', -- ping_status
    '', -- post_password
    'pagina-exemplo', -- post_name (slug)
    '', -- to_ping
    '', -- pinged
    NOW(), -- post_modified
    UTC_TIMESTAMP(), -- post_modified_gmt
    '', -- post_content_filtered
    0, -- post_parent
    'http://localhost/wordpress/pagina-exemplo/', -- guid
    0, -- menu_order
    'page', -- post_type
    '', -- post_mime_type
    0 -- comment_count
);

-- Obter o ID da página recém-criada
SET @page_id = LAST_INSERT_ID();

-- Adicionar meta para definir o template personalizado
INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES 
(@page_id, '_wp_page_template', 'templates/page-templates/page-demo.php');

-- Adicionar meta para SEO
INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES 
(@page_id, '_yoast_wpseo_title', 'Página Demonstrativa - NosfirNews Theme'),
(@page_id, '_yoast_wpseo_metadesc', 'Demonstração completa dos recursos do tema NosfirNews: design responsivo, acessibilidade, SEO otimizado e suporte AMP.'),
(@page_id, '_yoast_wpseo_canonical', 'http://localhost/wordpress/pagina-exemplo/'),
(@page_id, '_yoast_wpseo_opengraph-title', 'Página Demonstrativa - NosfirNews Theme'),
(@page_id, '_yoast_wpseo_opengraph-description', 'Veja todos os recursos do tema NosfirNews em ação: layouts responsivos, componentes interativos e muito mais.'),
(@page_id, '_yoast_wpseo_twitter-title', 'Página Demonstrativa - NosfirNews Theme'),
(@page_id, '_yoast_wpseo_twitter-description', 'Demonstração completa do tema NosfirNews com todos os seus recursos e funcionalidades.');

-- Verificar se a página foi criada com sucesso
SELECT 
    ID,
    post_title,
    post_name,
    post_status,
    post_type,
    post_date
FROM wp_posts 
WHERE post_name = 'pagina-exemplo' 
AND post_type = 'page';

-- Verificar os metadados
SELECT 
    pm.meta_key,
    pm.meta_value
FROM wp_postmeta pm
JOIN wp_posts p ON pm.post_id = p.ID
WHERE p.post_name = 'pagina-exemplo' 
AND p.post_type = 'page';