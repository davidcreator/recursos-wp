# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/spec/v2.0.0.html).

## 📖 Tipos de Mudanças

- `Added` (Adicionado) - para novas funcionalidades.
- `Changed` (Modificado) - para mudanças em funcionalidades existentes.
- `Deprecated` (Obsoleto) - para funcionalidades que serão removidas em breve.
- `Removed` (Removido) - para funcionalidades removidas.
- `Fixed` (Corrigido) - para correções de bugs.
- `Security` (Segurança) - para vulnerabilidades corrigidas.

---

## [Unreleased]

### Planejado para Próxima Release

#### Added
- Sistema de notificações toast para feedback do usuário
- Live search com autocomplete
- Skeleton screens para loading states
- Suporte a AOS (Animate On Scroll)

#### Changed
- Melhorias na performance de lazy loading de imagens

#### In Progress
- Dark mode implementation
- Advanced typography system
- Header builder drag-and-drop

---

## [1.0.1] - 2026-01-07

### 🚨 RELEASE CRÍTICA - Correções de Bugs

Esta versão corrige problemas críticos de CSS e layout que afetavam a experiência do usuário em dispositivos mobile e tablet.

### Fixed
- **[CRÍTICO]** Conflitos CSS causados por uso excessivo de `!important` no grid system
- **[CRÍTICO]** Header layout quebrado em mobile devido a grid de 3 colunas forçado
- **[CRÍTICO]** Menu mobile não abrindo corretamente em alguns dispositivos
- **[ALTO]** Grid de posts quebrando em resoluções intermediárias (768px-991px)
- **[ALTO]** Overflow horizontal em mobile causado por containers mal dimensionados
- **[MÉDIO]** Focus states inconsistentes em elementos interativos
- **[MÉDIO]** Transições CSS stuttering devido a propriedades não otimizadas
- **[BAIXO]** Sombras CSS renderizando incorretamente em Safari iOS

### Changed
- **CSS Architecture**: Refatoração completa do `style.css` (570 → 1850 linhas)
  - Removido todos os 5 usos de `!important`
  - Implementado sistema de 90+ CSS variables para fácil customização
  - Convertido layout de header de Grid para Flexbox
  - Melhorado sistema de grid responsivo com mobile-first approach
  - Adicionado índice navegável com números de linha
- **Typography**: Sistema fluído usando `clamp()` para escalabilidade responsiva
- **Spacing**: Padronização usando CSS custom properties (`--space-*`)
- **Transitions**: Otimizado para GPU acceleration (transform/opacity)
- **Shadow System**: Implementado escala de sombras consistente (`--shadow-*`)

### Added
- **Acessibilidade**: Suporte completo a WCAG 2.1 Level AA
  - Skip links funcionais
  - Focus states visíveis em todos elementos interativos
  - Suporte a `prefers-reduced-motion`
  - Suporte a `prefers-contrast: high`
  - Screen reader classes (`.sr-only`)
- **CSS Variables**: 90+ variáveis para customização
  - Cores (primary, secondary, text, borders)
  - Espaçamentos (xs, sm, md, lg, xl, 2xl, 3xl)
  - Typography (font-families, sizes, weights, line-heights)
  - Transições (timing, easing functions)
  - Z-index scale organizado
  - Border radius scale
  - Shadow system
- **Loading States**: Classes utilitárias para estados de carregamento
- **Animation Keyframes**: fadeIn, slideUp, spin
- **Mobile Menu**: Backdrop blur effect para melhor UX
- **Hover Effects**: Micro-interactions refinadas em botões e cards

### Performance
- **CSS**: Reduzido uso de seletores complexos
- **Animations**: Uso de `transform` e `opacity` para 60fps
- **Paint**: Minimizado reflow/repaint com `will-change` estratégico

### Documentation
- Adicionado comentários extensivos em todo o CSS
- Criado índice navegável no header do arquivo
- Documentado todas as CSS variables

### Developer Experience
- Estrutura de código mais organizada e manutenível
- Nomenclatura consistente seguindo metodologia BEM-like
- Comentários explicativos para código complexo

---

## [1.0.0] - 2026-01-06

### 🎉 RELEASE INICIAL

Primeira versão pública do tema NosfirNews.

### Added
- **Core Features**
  - Sistema de templates WordPress completo
  - Suporte a posts, páginas e custom post types
  - Sistema de menus (Primary, Secondary, Footer, Mobile, Sidebar, Social)
  - Widgets areas (Sidebar, Footer 1-4, Header, Bottom)
  - Sistema de thumbnails com múltiplos tamanhos
  - Comentários com threading
  - Busca nativa do WordPress
  - Paginação (números e infinite scroll)
  
- **Customizer Options**
  - Global Settings (container width, layout boxed/full)
  - Typography (font family, size, heading scale)
  - Colors (primary color, backgrounds)
  - Blog Settings (pagination type, excerpt length, featured images)
  - Homepage (layout grid/list/masonry, columns, hero section)
  - Header (logo alignment, menu location, mobile breakpoint)
  - Footer (logo, description, social menu, columns, alignment)
  - Media (thumbnail settings, crop, fit modes, filters, hover effects)
  - 404 & 500 pages (custom titles and messages)
  - Menu Options (search in menu, social links)

- **Layout System**
  - Container max-width configurável
  - Sidebar system (left/right/both/none)
  - Resizable sidebars (drag handles)
  - Footer widget areas (1-4 columns)
  - Archive layouts (list/grid/masonry)
  
- **Media Handling**
  - Custom image sizes (nn_thumb_standard, nn_single_cover)
  - Responsive images with srcset
  - Lazy loading support
  - WebP conversion (opcional)
  - Thumbnail effects (zoom, lift)
  - Image filters (grayscale, sepia, saturate, etc.)
  - Crop modes (cover, contain, auto)
  - Per-post thumbnail overrides via meta boxes
  
- **Navigation**
  - Multi-level dropdown menus
  - Mobile hamburger menu with drawer
  - Social icons menu
  - Search in menu (opcional)
  - Breadcrumbs (function available)
  
- **WooCommerce Integration**
  - Product carousel shortcode `[nn_wc_carousel]`
  - Featured products block on shop page
  - Custom popup system (shop/product/all pages)
  - Shop sidebar
  - Product templates compatibility
  
- **Performance Features**
  - CSS/JS minification ready
  - Asset versioning with filemtime
  - Conditional loading (Bootstrap opcional)
  - Fetch priority para imagens críticas
  - Eager loading para above-the-fold images
  
- **Developer Features**
  - Autoloader para classes PSR-4
  - Hook system extensivo (`nosfirnews_*` actions/filters)
  - Template parts system
  - Nav walker customizável
  - Pluggable functions
  - Child theme ready

- **Compatibility**
  - WordPress 6.0+
  - PHP 7.4+
  - WooCommerce 7.0+
  - Elementor (partial)
  - Beaver Builder (partial)
  - RTL support
  - Translation ready (.pot file)

- **Accessibility**
  - Semantic HTML5
  - ARIA labels básicos
  - Skip links (função disponível)
  - Keyboard navigation
  - Focus states

- **SEO**
  - Schema.org markup básico
  - Semantic heading structure
  - Alt text support
  - Meta description via excerpt

### Known Issues

⚠️ **Estas issues são corrigidas na v1.0.1:**

- Header layout quebra em mobile devido a grid forçado de 3 colunas
- Grid system usa `!important` excessivamente causando conflitos
- Menu mobile pode não abrir em alguns dispositivos
- Overflow horizontal em algumas resoluções
- CSS inline excessivo no footer (performance)
- Sistema de thumbnails muito complexo e difícil de manter
- JavaScript não consolidado (múltiplos blocos inline)
- Customizer com controles não funcionais (React stubs vazios)

### Notes

Esta é a versão base do tema, estável para uso geral mas com algumas limitações conhecidas que serão abordadas nas próximas versões. Recomendamos atualizar para v1.0.1 assim que possível.

---

## [Planejado] - Versões Futuras

### [2.0.0] - Planejado para Q1 2026 (Março)

**Major Release com Breaking Changes**

#### Added
- ✨ Dark mode completo com toggle e persistência
- ✨ Sistema de typography avançado com fluid scales
- ✨ Header builder drag-and-drop (similar ao Neve/Kadence)
- ✨ Mega menu system com widgets
- ✨ WooCommerce deep integration (quick view, wishlist, ajax cart)
- ✨ Advanced search com autocomplete e filtros
- ✨ Toast notification system
- ✨ Skeleton screens para lazy loading
- ✨ Live customizer preview (real-time)
- ✨ Block patterns library (20+ patterns)

#### Changed
- 🔄 **[BREAKING]** Sistema de thumbnails completamente reescrito
  - Nova API: `NosfirNews_Thumbnail_Manager` class
  - Post meta keys alterados (migração automática incluída)
- 🔄 **[BREAKING]** Classes CSS renomeadas para consistência BEM
  - `.post-card` → `.nn-post-card`
  - `.entry-thumb` → `.nn-entry-thumb`
- 🔄 Sidebar system simplificado (removido drag-resize se não usado)
- 🔄 Customizer reorganizado em tabs
- 🔄 Build system implementado (Webpack/Gulp)

#### Removed
- ❌ **[BREAKING]** Controles React vazios do Customizer
- ❌ **[BREAKING]** Funções deprecadas (ver guia de migração)
- ❌ Código legado de compatibilidade com WP < 6.0

#### Performance
- ⚡ Critical CSS extraction
- ⚡ Asset minification e concatenação
- ⚡ Database query optimization
- ⚡ Object caching implementation
- ⚡ Image optimization pipeline (WebP, AVIF)
- ⚡ Lazy loading avançado com IntersectionObserver

#### Migration Guide
- Guia completo de migração de 1.x para 2.0.0
- Script automático de migração incluído
- Backward compatibility layer (opcional)

---

### [1.4.0] - Planejado para Q1 2026 (Final de Fevereiro)

**UX Refinements & Polish**

#### Added
- Micro-interactions e animations refinadas
- Scroll-based animations (AOS integration)
- Pull-to-refresh em mobile (opcional)
- Sticky "back to top" button
- Touch gestures para mobile menu (swipe-to-close)

#### Changed
- Touch targets aumentados para 48x48px (WCAG AAA)
- Mobile forms otimizados para keyboards nativos
- Improved scroll performance (passive listeners)

#### Fixed
- Edge cases em animações CSS
- Safari iOS rendering quirks
- Android Chrome font rendering

---

### [1.3.0] - Planejado para Q1 2026 (Início de Fevereiro)

**Advanced Features**

#### Added
- Dark mode implementation
- Advanced typography system (Google Fonts chooser)
- Font pairing presets
- Variable fonts support
- FOUT prevention
- Typography scale configurável no Customizer

#### Changed
- Sistema de cores expandido (8 color stops)
- Typography scale usando `clamp()` em todo o tema

---

### [1.2.0] - Planejado para Janeiro 2026 (Final)

**Performance & Optimization**

#### Added
- Lazy loading avançado com IntersectionObserver
- Critical CSS extraction e inline
- Database query optimization com object caching
- Asset optimization pipeline (Webpack/Gulp)
- Image optimization (WebP conversion automática)
- Fragment caching para widgets
- Redis/Memcached support

#### Changed
- CSS consolidado (style.css + style-main-nosfirnews.css merged)
- JavaScript consolidado em único bundle
- Build process implementado

#### Performance Improvements
- PageSpeed score mobile: 60 → 90+
- PageSpeed score desktop: 70 → 95+
- LCP: < 2.5s
- FID: < 100ms
- CLS: < 0.1

---

### [1.1.0] - Planejado para Janeiro 2026 (Meio)

**Stabilization & Cleanup**

#### Added
- Testes automatizados (unit tests)
- CI/CD pipeline (GitHub Actions)
- Comprehensive documentation

#### Changed
- Sidebar system simplificado
- Customizer cleanup (controles não funcionais removidos)
- Code organization melhorada

#### Fixed
- Testes de regressão completos
- Browser compatibility issues
- Edge cases em formulários

#### Removed
- 15+ controles React vazios do Customizer
- Arquivos não utilizados da estrutura

---

## Guias de Migração

### Migrando de 1.0.0 para 1.0.1

✅ **Compatibilidade Total** - Esta é uma atualização de correção de bugs, totalmente compatível com 1.0.0.

#### Mudanças Necessárias

**Nenhuma ação necessária!** Esta atualização é 100% compatível.

#### Recomendações

1. **Limpe o cache do browser** após atualizar
2. **Regenere thumbnails** se você teve problemas com imagens:
   ```
   Dashboard → NosfirNews → Thumbs → Regenerar thumbnails
   ```
3. **Teste seu site** em diferentes dispositivos após atualizar

#### Se Você Usa Child Theme

Verifique se você sobrescreve algum destes arquivos:
- `style.css` - Se sim, revise seus overrides
- `header.php` - Classes CSS podem ter mudado
- `template-parts/content.php` - Estrutura de thumbnails pode ter mudado

Se você personalizou CSS inline usando theme mods, **não há impacto**.

---

### Migrando de 1.x para 2.0.0 (Futuro)

⚠️ **Breaking Changes** - Requer atenção durante upgrade.

#### Script de Migração Automática

```bash
# Será fornecido na release 2.0.0
wp nosfirnews migrate --from=1.x --to=2.0.0
```

#### Mudanças Manuais Necessárias

**1. Sistema de Thumbnails**
```php
// ANTES (1.x) - Deprecado
if ( has_post_thumbnail() ) {
    the_post_thumbnail( 'large' );
}

// DEPOIS (2.0.0) - Nova API
$thumb = new NosfirNews_Thumbnail_Manager( get_the_ID(), 'archive' );
$thumb->render();
```

**2. Classes CSS**
```css
/* ANTES (1.x) */
.post-card { }
.entry-thumb { }

/* DEPOIS (2.0.0) */
.nn-post-card { }
.nn-entry-thumb { }
```

**3. Hooks Alterados**
```php
// Removidos em 2.0.0
// - nosfirnews_old_hook (use nosfirnews_new_hook)
// - nosfirnews_legacy_function (use nosfirnews_modern_function)
```

#### Guia Completo

Um guia detalhado será fornecido na documentação da release 2.0.0.

---

## Links Úteis

- [Documentação Completa](https://docs.nosfirnews.com)
- [Guias de Migração](https://docs.nosfirnews.com/migration)
- [Reportar Bug](https://github.com/user/nosfirnews/issues)
- [Solicitar Feature](https://github.com/user/nosfirnews/discussions)
- [Roadmap Completo](ROADMAP.md)
- [Guia de Contribuição](CONTRIBUTING.md)

---

## Suporte de Versões

| Versão | Status | Suporte até | Recebe Updates |
|--------|--------|-------------|----------------|
| 2.0.x  | Planejado | - | ✅ Features + Security |
| 1.4.x  | Planejado | - | ✅ Features + Security |
| 1.3.x  | Planejado | - | ✅ Features + Security |
| 1.2.x  | Planejado | - | ✅ Features + Security |
| 1.1.x  | Planejado | - | ✅ Features + Security |
| 1.0.1  | 🟢 Atual | Mar 2026 | ✅ Bugs + Security |
| 1.0.0  | ⚠️ Deprecado | Jan 2026 | ⚠️ Security Only |

### Política de Suporte

- **Versão Atual**: Recebe todas as atualizações (features, bugs, security)
- **Versão Anterior**: Recebe apenas bugs críticos e security patches
- **Versões Antigas**: Apenas security patches críticos

**Recomendação**: Sempre mantenha seu tema atualizado para a versão mais recente.

---

## Compatibilidade

### WordPress

| WordPress | 1.0.0 | 1.0.1 | 2.0.0 (planejado) |
|-----------|-------|-------|-------------------|
| 6.4.x     | ✅    | ✅    | ✅                |
| 6.3.x     | ✅    | ✅    | ✅                |
| 6.2.x     | ✅    | ✅    | ⚠️ Não testado    |
| 6.1.x     | ✅    | ✅    | ❌ Não suportado  |
| 6.0.x     | ✅    | ✅    | ❌ Não suportado  |
| < 6.0     | ❌    | ❌    | ❌                |

### PHP

| PHP   | 1.0.0 | 1.0.1 | 2.0.0 (planejado) |
|-------|-------|-------|-------------------|
| 8.3.x | ✅    | ✅    | ✅                |
| 8.2.x | ✅    | ✅    | ✅                |
| 8.1.x | ✅    | ✅    | ✅                |
| 8.0.x | ✅    | ✅    | ⚠️                |
| 7.4.x | ✅    | ✅    | ❌ Não suportado  |
| < 7.4 | ❌    | ❌    | ❌                |

### Browsers

| Browser | Versões Suportadas |
|---------|-------------------|
| Chrome  | Últimas 2 versões |
| Firefox | Últimas 2 versões |
| Safari  | Últimas 2 versões |
| Edge    | Últimas 2 versões |
| Opera   | Última versão     |

**Mobile:**
- iOS Safari 14+
- Chrome Android 100+
- Samsung Internet 16+

---

## Como Usar Este Changelog

### Para Desenvolvedores

1. **Sempre leia antes de atualizar** - Verifique breaking changes
2. **Siga os guias de migração** - Links fornecidos em cada versão
3. **Teste em ambiente staging** - Nunca atualize direto em produção
4. **Mantenha backup** - Sempre faça backup antes de atualizar

### Para Usuários Finais

1. **Atualize regularmente** - Security patches são importantes
2. **Leia a seção "Changed"** - Pode haver mudanças visuais
3. **Verifique compatibilidade** - Confira versões de WP/PHP
4. **Reporte problemas** - Use GitHub Issues para reportar bugs

### Para Contribuidores

1. **Adicione entradas ao [Unreleased]** - Ao criar PR
2. **Siga o formato Keep a Changelog** - Categorias corretas
3. **Seja descritivo** - Explique o que e por que mudou
4. **Linke PRs/Issues** - Use `#123` para referência

---

## Convenções de Versionamento

Este projeto usa [Versionamento Semântico](https://semver.org/lang/pt-BR/):

```
MAJOR.MINOR.PATCH

MAJOR: Breaking changes (incompatível com versão anterior)
MINOR: Novas features (compatível com versão anterior)
PATCH: Bug fixes (compatível com versão anterior)
```

### Exemplos

- `1.0.0` → `1.0.1`: Bug fixes apenas (patch)
- `1.0.1` → `1.1.0`: Novas features compatíveis (minor)
- `1.4.0` → `2.0.0`: Breaking changes (major)

---

## Créditos e Agradecimentos

### Versão 1.0.1
- Refatoração CSS: [Seu Nome]
- Code Review: [Reviewer]
- Testing: [Testers]

### Versão 1.0.0
- Autor Original: Nosfir
- Contribuidores: [Lista de contribuidores]

### Bibliotecas e Recursos
- Bootstrap 5.3.2 (opcional)
- Font Awesome (social icons)
- System Fonts Stack

---

## Código de Conduta

Ao contribuir para este projeto, você concorda em seguir nosso [Código de Conduta](CODE_OF_CONDUCT.md).

---

**Última atualização:** 2026-01-07  
**Mantido por:** Equipe NosfirNews  
**Licença:** GPL-2.0-or-later

---

[Unreleased]: https://github.com/user/nosfirnews/compare/v1.0.1...HEAD
[1.0.1]: https://github.com/user/nosfirnews/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/user/nosfirnews/releases/tag/v1.0.0