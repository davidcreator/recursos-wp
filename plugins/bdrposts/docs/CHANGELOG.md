# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

---

## [Unreleased]

### 🔒 Security
- Correção de vulnerabilidade XSS em atributos do bloco
- Implementação de proteção CSRF no endpoint `/render`
- Validação rigorosa de inputs em arrays
- Escape JSON seguro com flags `JSON_HEX_*`
- Rate limiting nas rotas REST API

### ⚡ Performance
- Cache stampede protection implementado
- Otimização de queries com cache de objeto
- Lazy loading de taxonomias com paginação
- Redução de memory leaks no ticker animation

### ♿ Accessibility
- Skip links adicionados para navegação por teclado
- ARIA labels melhorados em todos os componentes interativos
- Suporte completo a leitores de tela
- Focus management aprimorado no editor

### 🐛 Bug Fixes
- Correção de race condition no sistema de cache
- Fix de memory leak em `requestAnimationFrame` do ticker
- Melhor tratamento de erros com try-catch
- Validação de elementos removidos do DOM

### 📚 Documentation
- PHPDoc adicionado em todas as funções
- Comentários inline melhorados
- Guia de contribuição criado

---

## [1.0.1] - 2024-01-06

### ✨ Added
- Barra de filtros dinâmica no frontend (categoria/tag)
- Ferramentas de busca e ordenação em tempo real
- Botão "Carregar mais" com paginação AJAX
- Sistema de cache inteligente com transients (120s)
- Carregamento condicional do Swiper (apenas quando necessário)
- Endpoint REST `/render` para atualização dinâmica
- Suporte a taxonomias customizadas com termos
- Checkboxes visuais para seleção de categorias/tags
- Atributos `allowSearch`, `allowOrderChange`, `loadMore`
- Opções de personalização da barra de filtros

### 🔧 Changed
- Rotas REST expandidas (`/categories`, `/tags`, `/terms/{taxonomy}`)
- Editor JavaScript completamente refatorado com React Hooks
- Validação de arrays vazios melhorada (`count() > 0`)
- Preview do editor agora usa `ServerSideRender` nativo
- Masonry otimizado com CSS columns + ResizeObserver
- Ticker com suporte a `prefers-reduced-motion`
- Sub-layout "Overlay" com gradient melhorado

### 🐛 Fixed
- Seleção de categorias/tags agora funciona corretamente
- Compatibilidade total com tema Twenty Twenty-Five
- Preview no editor não trava mais em temas específicos
- Bloco agora é selecionável e removível no Gutenberg
- ServerSideRender com fallback para temas incompatíveis
- Swiper inicializa corretamente após carregamento dinâmico
- Imagens com `fetchpriority` e `decoding="async"` para LCP

### 🎨 Improved
- CSS responsivo completamente reescrito (mobile-first)
- Breakpoints otimizados: 374px, 600px, 768px, 1024px, 1280px, 1440px, 1920px+
- Estilos do editor com melhor UX em tablets
- Animações respeitam `prefers-reduced-motion`
- Touch devices com áreas de toque de 44x44px
- Modo paisagem mobile otimizado
- Print styles adicionados

### 📦 Dependencies
- Swiper v9.0.0 carregado via CDN (condicional)
- WordPress 5.8+ como requisito mínimo
- PHP 7.4+ como requisito mínimo

### 🗑️ Removed
- Dependência forçada do Swiper removida
- CSS inline desnecessário removido
- Código legado do editor removido

---

## [1.0.0] - 2024-01-01

### 🎉 Initial Release

#### ✨ Core Features
- **4 Layouts Principais**:
  - Grid (responsivo com colunas configuráveis)
  - Masonry (estilo Pinterest)
  - Slider (carrossel com Swiper)
  - Ticker (marquee horizontal)

- **5 Sub-layouts**:
  - Title + Meta (padrão)
  - Meta + Title (invertido)
  - Left Image (imagem à esquerda)
  - Right Image (imagem à direita)
  - Overlay (conteúdo sobre imagem)

#### 🎛️ Configuration Options
- Seleção de Post Type (posts, páginas, CPTs)
- Filtros por categoria, tag, autor
- Ordenação: data, título, modificado, aleatório, menu_order
- Posts por página configurável (1-50)
- Colunas ajustáveis (1-6)
- Offset para pular posts

#### 🎨 Visual Customization
- Mostrar/ocultar imagem destacada
- Tamanhos de imagem: thumbnail, medium, large, full
- Mostrar/ocultar título
- Resumo com tamanho configurável (5-100 palavras)
- Meta informações: data, autor, categorias, tags
- Tempo de leitura calculado automaticamente
- Botão "Ler Mais" customizável

#### 🔧 Developer Features
- Shortcode `[bdrposts]` para Classic Editor
- REST API endpoints para integração
- Sistema de hooks e filtros:
  - `bdrposts_query_args`
  - `bdrposts_item_classes`
  - `bdrposts_image_size`
- Cache de queries com transients
- Suporte a taxonomias customizadas
- Namespace WordPress correto

#### 📱 Responsive Design
- Mobile-first approach
- Grid adaptativo com `auto-fit`
- Masonry com `column-count` responsivo
- Slider com controles touch-friendly
- Breakpoints: 600px, 768px, 1024px, 1440px

#### ♿ Accessibility
- Semantic HTML5
- ARIA labels em navegação
- Keyboard navigation
- Focus states visíveis
- Screen reader friendly
- Alt text em imagens

#### 🌐 Internationalization
- Text domain: `bdrposts`
- Tradução ready (.pot file)
- RTL support preparado
- `_n()` para pluralizações

#### 🎯 Performance
- Lazy loading de imagens
- Swiper carregado apenas quando necessário
- CSS e JS minificados
- Queries otimizadas
- Cache de HTML

#### 📄 Files Structure
```
bdrposts/
├── bdrposts.php (arquivo principal)
├── uninstall.php (limpeza ao desinstalar)
├── README.md (documentação completa)
├── INSTALACAO.md (guia de instalação)
└── build/
    ├── index.js (editor Gutenberg)
    ├── frontend.js (scripts do site)
    ├── style.css (estilos do site)
    └── editor.css (estilos do editor)
```

#### 🔒 Security
- Escape de outputs (`esc_html`, `esc_url`, `esc_attr`)
- Sanitização de inputs
- Nonce verification
- Permission callbacks nas rotas REST
- Proteção contra acesso direto
- Validação de arrays

#### 📚 Documentation
- README.md completo com exemplos
- INSTALACAO.md com troubleshooting
- Inline code comments
- Casos de uso documentados
- FAQ section

#### 🧪 Tested With
- WordPress 6.4+
- PHP 7.4, 8.0, 8.1, 8.2
- Temas: Twenty Twenty-Three, Twenty Twenty-Four, Astra, GeneratePress
- Plugins: Yoast SEO, Rank Math, WooCommerce, ACF

---

## Tipos de Mudanças

- `Added` - para novas funcionalidades
- `Changed` - para mudanças em funcionalidades existentes
- `Deprecated` - para funcionalidades que serão removidas
- `Removed` - para funcionalidades removidas
- `Fixed` - para correção de bugs
- `Security` - para vulnerabilidades corrigidas
- `Improved` - para melhorias que não são bugs ou features

---

## Links

- [Homepage do Plugin](https://github.com/davidcreator/recursos-wp/bdrposts)
- [Reportar Bug](https://github.com/davidcreator/recursos-wp/issues)
- [Suporte](mailto:contato@davidcreator.com)

---

## Versionamento

Este projeto usa [Versionamento Semântico](https://semver.org/lang/pt-BR/):

- **MAJOR** (X.0.0): Mudanças incompatíveis na API
- **MINOR** (0.X.0): Nova funcionalidade compatível
- **PATCH** (0.0.X): Correção de bugs compatível

---

**Legenda de Emojis:**
- 🎉 Release inicial
- ✨ Novo recurso
- 🔧 Mudança
- 🐛 Correção de bug
- 🔒 Segurança
- ⚡ Performance
- ♿ Acessibilidade
- 🎨 UI/UX
- 📚 Documentação
- 🗑️ Remoção
- 📦 Dependências
- 🧪 Testes