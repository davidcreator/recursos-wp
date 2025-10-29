# Template de Página Sem Sidebar - NosfirNews

## Descrição

O template **No Sidebar Page** é um template personalizado para o tema NosfirNews que permite exibir páginas sem sidebar, utilizando a largura padrão do container. Este template é ideal para páginas que precisam de mais espaço para o conteúdo, mas ainda mantêm a estrutura visual do tema.

## Arquivos do Template

### 1. Template Principal
- **Arquivo**: `templates/page-templates/page-no-sidebar.php`
- **Template Name**: "No Sidebar Page"
- **Descrição**: Template principal que renderiza páginas sem sidebar

### 2. Estilos CSS
- **Arquivo**: `assets/css/page-no-sidebar.css`
- **Descrição**: Estilos específicos para o template sem sidebar

### 3. Enqueue de Estilos
- **Arquivo**: `functions.php` (função `nosfirnews_scripts`)
- **Descrição**: Carregamento condicional do CSS apenas quando o template é usado

## Como Usar

### 1. Aplicar o Template
1. No painel administrativo do WordPress, vá para **Páginas**
2. Edite uma página existente ou crie uma nova
3. No painel **Atributos da Página**, selecione **"No Sidebar Page"** no dropdown **Template**
4. Salve ou publique a página

### 2. Personalização via Editor
- O template é totalmente compatível com o editor Gutenberg
- Suporta todos os blocos padrão do WordPress
- Funciona com plugins de page builders

## Recursos e Funcionalidades

### Layout e Design
- **Container padrão**: Usa a largura padrão do tema (não full-width)
- **Sem sidebar**: Remove completamente a sidebar da página
- **Design responsivo**: Adapta-se a todos os tamanhos de tela
- **Grid system**: Utiliza Bootstrap grid (col-12)

### Elementos da Página
- **Imagem destacada**: Exibição opcional com caption
- **Título da página**: H1 otimizado para SEO
- **Excerpt**: Exibição opcional do resumo da página
- **Meta informações**: Data de publicação, última modificação, tempo de leitura
- **Conteúdo principal**: Área principal do conteúdo
- **Taxonomias**: Exibição de categorias e tags (se aplicável)
- **Navegação**: Links para página anterior/próxima
- **Comentários**: Suporte completo a comentários
- **Link de edição**: Para usuários com permissão

### Recursos Avançados
- **Paginação**: Suporte a páginas divididas com `<!--nextpage-->`
- **Campos personalizados**: Integração com ACF (tempo de leitura)
- **Acessibilidade**: ARIA labels e navegação por teclado
- **SEO otimizado**: Estrutura semântica e meta tags

## Classes CSS Principais

### Container e Layout
```css
.no-sidebar-page          /* Container principal */
.no-sidebar-main          /* Área principal do conteúdo */
.no-sidebar-article       /* Artigo da página */
```

### Elementos da Página
```css
.page-featured-image      /* Imagem destacada */
.page-header              /* Cabeçalho da página */
.page-title               /* Título da página */
.page-excerpt             /* Resumo da página */
.page-meta                /* Meta informações */
.page-content             /* Conteúdo principal */
.page-taxonomy            /* Categorias e tags */
.page-navigation          /* Navegação entre páginas */
```

### Estados e Interações
```css
.featured-image:hover     /* Hover na imagem destacada */
.nav-previous:hover       /* Hover na navegação anterior */
.nav-next:hover           /* Hover na navegação próxima */
```

## Customização

### 1. Modificar Estilos
Edite o arquivo `assets/css/page-no-sidebar.css` para personalizar:
- Cores e tipografia
- Espaçamentos e margens
- Efeitos de hover e transições
- Layout responsivo

### 2. Adicionar Funcionalidades
Modifique o arquivo `templates/page-templates/page-no-sidebar.php` para:
- Adicionar novos elementos
- Modificar a estrutura HTML
- Integrar com plugins específicos

### 3. Hooks Disponíveis
O template suporta os hooks padrão do WordPress:
- `wp_head()` - No cabeçalho
- `wp_footer()` - No rodapé
- `body_class()` - Classes do body
- `post_class()` - Classes do post

## Diferenças dos Outros Templates

### vs. Template Padrão (page.php)
- **Sem sidebar**: Remove completamente a sidebar
- **Largura padrão**: Mantém o container padrão (não full-width)
- **Mais espaço**: Conteúdo ocupa toda a largura disponível

### vs. Template Full-Width
- **Container limitado**: Usa largura máxima do tema
- **Margens laterais**: Mantém espaçamento nas laterais
- **Melhor legibilidade**: Ideal para textos longos

### vs. Template com Sidebar
- **Mais espaço horizontal**: Sem limitação da sidebar
- **Foco no conteúdo**: Destaque total para o conteúdo principal
- **Layout limpo**: Visual mais minimalista

## Casos de Uso Ideais

### 1. Páginas de Conteúdo
- Páginas "Sobre nós"
- Páginas de serviços
- Páginas de política de privacidade
- Páginas de termos de uso

### 2. Landing Pages
- Páginas de produtos
- Páginas de campanhas
- Páginas promocionais
- Páginas de eventos

### 3. Conteúdo Editorial
- Artigos longos
- Guias e tutoriais
- Estudos de caso
- Relatórios

## Compatibilidade

### WordPress
- **Versão mínima**: WordPress 5.0+
- **Editor**: Gutenberg e Classic Editor
- **Multisite**: Totalmente compatível

### Plugins Testados
- **Yoast SEO**: Totalmente compatível
- **Advanced Custom Fields**: Integração nativa
- **Contact Form 7**: Funciona perfeitamente
- **WooCommerce**: Compatível para páginas de conteúdo

### Navegadores
- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+
- Internet Explorer 11 (suporte básico)

## Acessibilidade

### Recursos Implementados
- **ARIA labels**: Navegação e elementos interativos
- **Navegação por teclado**: Todos os elementos focáveis
- **Contraste**: Suporte a modo de alto contraste
- **Screen readers**: Texto alternativo e estrutura semântica
- **Focus indicators**: Indicadores visuais de foco

### Padrões Seguidos
- WCAG 2.1 AA
- Section 508
- WAI-ARIA 1.1

## Suporte a Dispositivos

### Desktop
- Layout otimizado para telas grandes
- Hover effects e interações avançadas
- Tipografia escalável

### Tablet
- Layout adaptativo
- Touch-friendly navigation
- Imagens responsivas

### Mobile
- Layout mobile-first
- Navegação simplificada
- Performance otimizada

## Troubleshooting

### Problemas Comuns

#### 1. CSS não carrega
**Solução**: Verifique se o template está selecionado corretamente na página

#### 2. Layout quebrado
**Solução**: Limpe o cache do site e verifique conflitos com outros plugins

#### 3. Imagem destacada não aparece
**Solução**: Verifique se a imagem destacada está definida na página

#### 4. Comentários não funcionam
**Solução**: Verifique se os comentários estão habilitados na página

### Logs de Debug
Para debug, adicione ao `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Performance

### Otimizações Implementadas
- **CSS condicional**: Carregado apenas quando necessário
- **Imagens responsivas**: Diferentes tamanhos para diferentes telas
- **Lazy loading**: Suporte nativo do WordPress
- **Minificação**: CSS otimizado para produção

### Métricas Esperadas
- **First Contentful Paint**: < 1.5s
- **Largest Contentful Paint**: < 2.5s
- **Cumulative Layout Shift**: < 0.1
- **Time to Interactive**: < 3.5s

## Arquivos Relacionados

```
nosfirnews/
├── templates/
│   └── page-templates/
│       └── page-no-sidebar.php
├── assets/
│   └── css/
│       └── page-no-sidebar.css
├── functions.php
└── README-no-sidebar.md
```

## Changelog

### Versão 1.0.0
- Lançamento inicial do template
- Layout responsivo completo
- Suporte a acessibilidade
- Integração com ACF
- Documentação completa

---

**Desenvolvido para o tema NosfirNews**  
**Versão**: 1.0.0  
**Compatibilidade**: WordPress 5.0+