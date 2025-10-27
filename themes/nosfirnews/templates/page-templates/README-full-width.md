# Template de Página Full Width

## Descrição
O template `page-full-width.php` permite criar páginas que ocupam toda a largura da tela, sem sidebar, ideal para landing pages, páginas de apresentação ou conteúdo que precisa de mais espaço visual.

## Como Usar

### 1. Criando uma Nova Página
1. Acesse o painel administrativo do WordPress
2. Vá em **Páginas > Adicionar Nova**
3. Crie o conteúdo da sua página
4. No painel **Atributos da Página**, selecione **Full Width Page** no dropdown **Modelo**
5. Publique a página

### 2. Editando uma Página Existente
1. Acesse **Páginas > Todas as Páginas**
2. Edite a página desejada
3. No painel **Atributos da Página**, altere o **Modelo** para **Full Width Page**
4. Atualize a página

## Recursos Incluídos

### Layout
- **Container Fluid**: Utiliza toda a largura da tela
- **Sem Sidebar**: Remove completamente a sidebar
- **Design Responsivo**: Adapta-se a todos os tamanhos de tela
- **Imagem Destacada**: Suporte completo com efeito hover

### Estilos
- **Typography Responsiva**: Tamanhos de fonte que se adaptam ao dispositivo
- **Espaçamento Otimizado**: Margens e paddings balanceados
- **Modo Escuro**: Suporte automático baseado na preferência do usuário
- **Alto Contraste**: Compatibilidade com modo de alto contraste
- **Impressão**: Estilos otimizados para impressão

### Acessibilidade
- **Navegação por Teclado**: Suporte completo
- **Screen Readers**: Textos alternativos e estrutura semântica
- **Focus Indicators**: Indicadores visuais de foco
- **Reduced Motion**: Respeita preferências de movimento reduzido

## Estrutura do Template

```php
<?php
/**
 * Template Name: Full Width Page
 */

get_header(); ?>

<div class="site-content full-width-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <main class="site-main full-width-main">
                    <!-- Conteúdo da página -->
                </main>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
```

## Classes CSS Principais

### Layout
- `.full-width-page` - Container principal
- `.full-width-main` - Área de conteúdo principal
- `.full-width-article` - Artigo da página

### Componentes
- `.page-featured-image` - Imagem destacada
- `.page-header` - Cabeçalho da página
- `.page-title` - Título da página
- `.page-excerpt` - Resumo da página
- `.page-content` - Conteúdo principal
- `.page-links` - Links de paginação
- `.entry-footer` - Rodapé da entrada

## Customização

### Alterando Largura Máxima do Conteúdo
```css
.page-content {
    max-width: 1400px; /* Altere conforme necessário */
    margin: 0 auto;
}
```

### Personalizando Cores
```css
:root {
    --primary-color: #your-color;
    --heading-color: #your-color;
    --text-color: #your-color;
    --text-muted: #your-color;
}
```

### Adicionando Animações Personalizadas
```css
.full-width-article {
    animation: your-animation 0.6s ease-out;
}

@keyframes your-animation {
    /* Sua animação aqui */
}
```

## Casos de Uso Ideais

1. **Landing Pages**: Páginas de destino para campanhas
2. **Páginas de Produto**: Apresentação detalhada de produtos/serviços
3. **Portfólio**: Galeria de trabalhos ou projetos
4. **Sobre Nós**: Páginas institucionais com muito conteúdo
5. **Contato**: Formulários e informações de contato
6. **Eventos**: Páginas de eventos com programação detalhada

## Compatibilidade

- **WordPress**: 5.0+
- **PHP**: 7.4+
- **Browsers**: Chrome, Firefox, Safari, Edge (últimas 2 versões)
- **Dispositivos**: Desktop, Tablet, Mobile

## Suporte a Plugins

O template é compatível com:
- **Gutenberg**: Suporte completo aos blocos
- **Elementor**: Funciona com page builders
- **Contact Form 7**: Formulários de contato
- **WooCommerce**: Páginas de e-commerce
- **Yoast SEO**: Otimização para SEO

## Troubleshooting

### Problema: Template não aparece na lista
**Solução**: Verifique se o arquivo está na pasta correta e contém o cabeçalho `Template Name`.

### Problema: Estilos não carregam
**Solução**: Verifique se o CSS está sendo enfileirado corretamente no `functions.php`.

### Problema: Layout quebrado em mobile
**Solução**: Verifique se não há CSS conflitante do tema pai ou plugins.

## Arquivos Relacionados

- `templates/page-templates/page-full-width.php` - Template principal
- `assets/css/page-full-width.css` - Estilos específicos
- `functions.php` - Enqueue de estilos (linha ~233)

## Changelog

### v1.0.0
- Lançamento inicial
- Suporte responsivo completo
- Integração com tema NosfirNews
- Acessibilidade implementada
- Modo escuro incluído