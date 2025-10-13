<?php
/**
 * Arquivo de teste para verificar traduções
 * Template Name: Teste de Traduções
 */

get_header(); ?>

<div class="container" style="padding: 2rem;">
    <h1>Teste de Traduções - NosfirNews</h1>
    
    <div style="background: #f8f9fa; padding: 1.5rem; margin: 1rem 0; border-radius: 8px;">
        <h2>Strings do Search Template:</h2>
        <ul>
            <li><strong>Digite sua busca:</strong> <?php esc_html_e( 'Digite sua busca...', 'nosfirnews' ); ?></li>
            <li><strong>Buscar:</strong> <?php esc_html_e( 'Buscar', 'nosfirnews' ); ?></li>
            <li><strong>Categorias:</strong> <?php esc_html_e( 'Categorias', 'nosfirnews' ); ?></li>
            <li><strong>Período:</strong> <?php esc_html_e( 'Período', 'nosfirnews' ); ?></li>
            <li><strong>Autor:</strong> <?php esc_html_e( 'Autor', 'nosfirnews' ); ?></li>
            <li><strong>Limpar Filtros:</strong> <?php esc_html_e( 'Limpar Filtros', 'nosfirnews' ); ?></li>
            <li><strong>Resultados da Busca:</strong> <?php esc_html_e( 'Resultados da Busca', 'nosfirnews' ); ?></li>
        </ul>
    </div>
    
    <div style="background: #e3f2fd; padding: 1.5rem; margin: 1rem 0; border-radius: 8px;">
        <h2>Strings do Tag Template:</h2>
        <ul>
            <li><strong>Posts:</strong> <?php esc_html_e( 'Posts', 'nosfirnews' ); ?></li>
            <li><strong>Página:</strong> <?php esc_html_e( 'Página', 'nosfirnews' ); ?></li>
            <li><strong>Total:</strong> <?php esc_html_e( 'Total', 'nosfirnews' ); ?></li>
            <li><strong>Tags Relacionadas:</strong> <?php esc_html_e( 'Tags Relacionadas', 'nosfirnews' ); ?></li>
            <li><strong>Posts Populares:</strong> <?php esc_html_e( 'Posts Populares', 'nosfirnews' ); ?></li>
            <li><strong>Newsletter:</strong> <?php esc_html_e( 'Newsletter', 'nosfirnews' ); ?></li>
            <li><strong>Seu email:</strong> <?php esc_html_e( 'Seu email', 'nosfirnews' ); ?></li>
            <li><strong>Inscrever-se:</strong> <?php esc_html_e( 'Inscrever-se', 'nosfirnews' ); ?></li>
        </ul>
    </div>
    
    <div style="background: #f3e5f5; padding: 1.5rem; margin: 1rem 0; border-radius: 8px;">
        <h2>Informações do Sistema:</h2>
        <ul>
            <li><strong>Idioma atual:</strong> <?php echo get_locale(); ?></li>
            <li><strong>Diretório do tema:</strong> <?php echo get_template_directory(); ?></li>
            <li><strong>Diretório de idiomas:</strong> <?php echo get_template_directory() . '/languages'; ?></li>
            <li><strong>Text domain carregado:</strong> <?php echo is_textdomain_loaded('nosfirnews') ? 'Sim' : 'Não'; ?></li>
        </ul>
    </div>
    
    <div style="background: #fff3e0; padding: 1.5rem; margin: 1rem 0; border-radius: 8px;">
        <h2>Teste de Pluralização:</h2>
        <ul>
            <li><strong>1 comentário:</strong> <?php printf( _n( '%s comentário', '%s comentários', 1, 'nosfirnews' ), 1 ); ?></li>
            <li><strong>5 comentários:</strong> <?php printf( _n( '%s comentário', '%s comentários', 5, 'nosfirnews' ), 5 ); ?></li>
        </ul>
    </div>
    
    <div style="background: #e8f5e8; padding: 1.5rem; margin: 1rem 0; border-radius: 8px;">
        <h2>Teste de Printf:</h2>
        <ul>
            <li><strong>Posts com tag:</strong> <?php printf( esc_html__( 'Posts com a tag "%s"', 'nosfirnews' ), 'exemplo' ); ?></li>
            <li><strong>Mostrando posts:</strong> <?php printf( esc_html__( 'Mostrando %1$d-%2$d de %3$d posts', 'nosfirnews' ), 1, 10, 25 ); ?></li>
        </ul>
    </div>
</div>

<?php get_footer(); ?>