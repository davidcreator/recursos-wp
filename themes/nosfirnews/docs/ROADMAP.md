# 🗺️ ROADMAP - NosfirNews Theme

> **Versão Atual:** 1.0.0  
> **Última Atualização:** 06 de Janeiro de 2026  
> **Status:** Em Desenvolvimento Ativo

---

## 📋 Índice

1. [Visão Geral](#-visão-geral)
2. [Fase 0: Correções Críticas](#-fase-0-correções-críticas-urgente)
3. [Fase 1: Estabilização](#-fase-1-estabilização)
4. [Fase 2: Performance & Otimização](#-fase-2-performance--otimização)
5. [Fase 3: Recursos Avançados](#-fase-3-recursos-avançados)
6. [Fase 4: Polimento & UX](#-fase-4-polimento--ux)
7. [Fase 5: Documentação & Release](#-fase-5-documentação--release)
8. [Backlog de Funcionalidades](#-backlog-de-funcionalidades)
9. [Métricas de Sucesso](#-métricas-de-sucesso)

---

## 🎯 Visão Geral

### Objetivo Principal
Transformar o NosfirNews em um tema WordPress moderno, performático e elegante, pronto para produção, com foco em:
- ✅ **Estabilidade:** Zero bugs críticos
- ⚡ **Performance:** PageSpeed Score 90+
- 🎨 **Design:** UI moderna e responsiva
- ♿ **Acessibilidade:** WCAG 2.1 AA compliant
- 🔧 **Manutenibilidade:** Código limpo e documentado

### Timeline Estimado
```
┌─────────────────────────────────────────────────────────────────┐
│ Fase 0: 2-3 dias    (Crítico - Não Negociável)                 │
│ Fase 1: 1 semana    (Alta Prioridade)                          │
│ Fase 2: 1-2 semanas (Média Prioridade)                         │
│ Fase 3: 2-3 semanas (Recursos Avançados)                       │
│ Fase 4: 1 semana    (Polimento)                                │
│ Fase 5: 3-5 dias    (Release)                                  │
└─────────────────────────────────────────────────────────────────┘
Total: ~8-10 semanas para versão 2.0.0
```

---

## 🚨 FASE 0: Correções Críticas (URGENTE)

**Prazo:** 2-3 dias  
**Prioridade:** 🔴 CRÍTICA  
**Versão Alvo:** 1.0.1

### 0.1 Conflitos CSS com `!important`

**Problema:** Uso excessivo de `!important` causando comportamento imprevisível em grid e layout.

**Arquivos Afetados:**
- `style.css` (linhas 206, 294, 323, 343)
- `functions.php` (CSS inline no wp_footer)

**Tarefas:**
- [ ] Remover todos os `!important` desnecessários do grid system
- [ ] Implementar hierarquia CSS correta usando especificidade
- [ ] Substituir `style.css` pelo arquivo corrigido
- [ ] Testar em Chrome, Firefox, Safari (mobile + desktop)

**Arquivos de Referência:**
```
themes/nosfirnews/
├── style.css (SUBSTITUIR COMPLETO)
└── docs/fixes/0.1-css-conflicts-fix.md (CRIAR)
```

**Estimativa:** 4-6 horas

---

### 0.2 Sistema de Grid do Header

**Problema:** Grid de 3 colunas sempre ativo causa sobreposição em mobile.

**Arquivos Afetados:**
- `style.css` (linha 77)
- `functions.php` (inline CSS)

**Tarefas:**
- [ ] Substituir grid 3-col por flexbox responsivo
- [ ] Implementar sistema de ordem com `order` property
- [ ] Remover classes de posicionamento conflitantes
- [ ] Simplificar lógica de alinhamento (left/center/right)

**Código Atual:**
```css
/* ❌ PROBLEMA */
.header-inner {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
}
```

**Código Corrigido:**
```css
/* ✅ SOLUÇÃO */
.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: nowrap;
}
```

**Estimativa:** 3-4 horas

---

### 0.3 CSS Inline Excessivo

**Problema:** ~200 linhas de CSS sendo geradas dinamicamente no `wp_footer`, causando:
- FOUC (Flash of Unstyled Content)
- Bloating do HTML
- Problemas de cache

**Arquivos Afetados:**
- `functions.php` (linhas 94-196, 1191-1200)

**Tarefas:**
- [ ] Criar arquivo `inc/core/dynamic-styles.php`
- [ ] Criar template `inc/core/dynamic-styles-template.php`
- [ ] Mover toda lógica de CSS dinâmico para função separada
- [ ] Usar `wp_add_inline_style()` em vez de `wp_footer`
- [ ] Adicionar caching para CSS dinâmico

**Nova Estrutura:**
```php
// inc/core/dynamic-styles.php
function nosfirnews_generate_dynamic_css() {
    // Cache check
    $cache_key = 'nn_dynamic_css_' . md5(serialize(get_theme_mods()));
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    // Generate CSS
    ob_start();
    include get_template_directory() . '/inc/core/dynamic-styles-template.php';
    $css = ob_get_clean();
    
    // Minify
    $css = preg_replace('/\s+/', ' ', $css);
    
    // Cache por 1 hora
    set_transient($cache_key, $css, HOUR_IN_SECONDS);
    
    return $css;
}

add_action('wp_enqueue_scripts', function() {
    wp_add_inline_style('nosfirnews-style', nosfirnews_generate_dynamic_css());
}, 20);
```

**Estimativa:** 6-8 horas

---

### 0.4 Sistema de Thumbnails Complexo

**Problema:** Lógica espalhada, 8+ theme_mods + post_metas, código duplicado.

**Arquivos Afetados:**
- `template-parts/content.php` (linhas 8-40)
- `page-templates/content-single.php` (linhas 8-45)
- `page-templates/content-page.php` (linhas 8-45)

**Tarefas:**
- [ ] Criar classe `NosfirNews_Thumbnail_Manager`
- [ ] Centralizar toda lógica de thumbnails
- [ ] Implementar cache de configurações
- [ ] Adicionar filtros para extensibilidade
- [ ] Refatorar templates para usar a classe

**Nova Estrutura:**
```
themes/nosfirnews/inc/core/
├── class-thumbnail-manager.php (CRIAR)
├── class-thumbnail-config.php (CRIAR)
└── thumbnail-functions.php (CRIAR - helpers)
```

**API Simplificada:**
```php
// Uso nos templates
$thumb = new NosfirNews_Thumbnail_Manager(get_the_ID(), 'archive');
$thumb->render();

// Com customização
$thumb = new NosfirNews_Thumbnail_Manager(get_the_ID(), 'single');
$thumb->set_size('large')
      ->set_hover('zoom')
      ->set_fit('cover')
      ->render();
```

**Estimativa:** 8-10 horas

---

### 0.5 JavaScript Inline Performance

**Problema:** Múltiplos blocos `<script>` inline, sem minificação, código duplicado.

**Arquivos Afetados:**
- `functions.php` (vários blocos inline)
- `header-footer-grid/assets/js/theme.js`

**Tarefas:**
- [ ] Criar `assets/js/theme-consolidated.js`
- [ ] Consolidar todo JS inline em um arquivo
- [ ] Implementar build process (opcional: webpack/gulp)
- [ ] Minificar para produção
- [ ] Enfileirar corretamente com dependências

**Nova Estrutura:**
```
themes/nosfirnews/assets/js/
├── src/
│   ├── header.js
│   ├── mobile-menu.js
│   ├── sidebar-resize.js
│   └── main.js
├── theme-consolidated.js (COMPILADO)
└── theme-consolidated.min.js (PRODUÇÃO)
```

**Build Script (package.json):**
```json
{
  "scripts": {
    "dev": "webpack --mode development --watch",
    "build": "webpack --mode production"
  }
}
```

**Estimativa:** 6-8 horas

---

## ✅ Checklist Fase 0 (Pré-Requisitos para Próxima Fase)

- [ ] Todos os testes de responsividade passam (375px, 768px, 1200px, 1920px)
- [ ] Menu mobile abre/fecha sem bugs
- [ ] Grid de posts não quebra em nenhuma resolução
- [ ] CSS inline reduzido em 80%+
- [ ] JavaScript consolidado e minificado
- [ ] Zero erros no console do browser
- [ ] Validação HTML sem erros críticos
- [ ] Performance Lighthouse mobile: 60+ (antes das otimizações)

**Critério de Aceitação:** Se algum item acima falhar, Fase 1 não pode começar.

---

## 🔧 FASE 1: Estabilização

**Prazo:** 1 semana  
**Prioridade:** 🟠 ALTA  
**Versão Alvo:** 1.1.0

### 1.1 Refatoração do Sistema de Sidebar

**Objetivo:** Simplificar lógica de sidebar, remover feature de resize se não essencial.

**Arquivos Afetados:**
- `functions.php` (linhas 1135-1200)
- `sidebar.php`
- `sidebar-shop.php`

**Tarefas:**
- [ ] Avaliar uso real da feature de resize
- [ ] Se não usado: remover completamente
- [ ] Se usado: mover para plugin separado
- [ ] Simplificar sistema left/right/both
- [ ] Implementar CSS Grid para sidebars

**Decisão:**
```
┌─────────────────────────────────────────────┐
│ Feature de Resize é Essencial?             │
│                                             │
│ [ ] SIM → Mover para plugin/addon          │
│ [ ] NÃO → Remover e simplificar            │
└─────────────────────────────────────────────┘
```

**Nova Estrutura (se simplificar):**
```php
// Apenas CSS Grid simples
.archive-container {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

@media (min-width: 992px) {
  .archive-container.has-sidebar-right {
    grid-template-columns: 1fr 300px;
  }
  
  .archive-container.has-sidebar-left {
    grid-template-columns: 300px 1fr;
  }
  
  .archive-container.has-sidebar-both {
    grid-template-columns: 250px 1fr 250px;
  }
}
```

**Estimativa:** 6-8 horas

---

### 1.2 Consolidação de Arquivos CSS

**Problema:** Duplicação entre `style.css` e `style-main-nosfirnews.css`.

**Tarefas:**
- [ ] Analisar conteúdo de ambos os arquivos
- [ ] Mesclar em estrutura única e hierárquica
- [ ] Separar: base → components → layouts → utilities
- [ ] Criar sistema de build CSS (PostCSS/SCSS opcional)
- [ ] Implementar purge CSS para produção

**Nova Estrutura:**
```
themes/nosfirnews/assets/css/
├── src/
│   ├── 01-settings/
│   │   ├── _variables.css
│   │   └── _mixins.css
│   ├── 02-tools/
│   │   └── _utilities.css
│   ├── 03-generic/
│   │   └── _reset.css
│   ├── 04-elements/
│   │   ├── _typography.css
│   │   └── _forms.css
│   ├── 05-components/
│   │   ├── _buttons.css
│   │   ├── _cards.css
│   │   ├── _navigation.css
│   │   └── _thumbnails.css
│   ├── 06-layouts/
│   │   ├── _header.css
│   │   ├── _footer.css
│   │   └── _grid.css
│   └── 07-utilities/
│       └── _responsive.css
├── style.css (COMPILADO)
└── style.min.css (PRODUÇÃO)
```

**Estimativa:** 10-12 horas

---

### 1.3 Otimização do Customizer

**Problema:** Muitos controles não utilizados, código React vazio.

**Tarefas:**
- [ ] Auditar todos os controles registrados
- [ ] Remover controles não funcionais
- [ ] Consolidar controles similares
- [ ] Documentar cada opção do Customizer
- [ ] Criar seções colapsáveis para organização

**Limpeza:**
```
inc/customizer/controls/react/
├── builder.php (❌ REMOVER - vazio)
├── builder_columns.php (❌ REMOVER - vazio)
├── builder_section.php (❌ REMOVER - vazio)
├── button_appearance.php (❌ REMOVER - vazio)
├── conditional_selector.php (❌ REMOVER - vazio)
... (15+ arquivos vazios para remover)
```

**Manter Apenas:**
```
inc/customizer/controls/
├── color.php (✅ USAR)
├── range.php (✅ USAR)
├── radio.php (✅ USAR)
├── checkbox.php (✅ USAR)
└── spacing.php (✅ USAR)
```

**Estimativa:** 8-10 horas

---

### 1.4 Testes de Regressão

**Objetivo:** Garantir que correções da Fase 0 não introduziram novos bugs.

**Tarefas:**
- [ ] Criar suite de testes manuais
- [ ] Testar em 5 browsers (Chrome, Firefox, Safari, Edge, Opera)
- [ ] Testar em dispositivos reais (iOS + Android)
- [ ] Validar formulários (comentários, busca)
- [ ] Testar integração WooCommerce (se habilitado)

**Checklist de Testes:**
```markdown
## Desktop (1920x1080)
- [ ] Homepage carrega corretamente
- [ ] Navegação funciona
- [ ] Posts archive responsivo
- [ ] Single post layout correto
- [ ] Footer widgets alinhados
- [ ] Sidebar posicionada corretamente

## Tablet (768x1024)
- [ ] Grid adapta para 2 colunas
- [ ] Navegação adaptativa
- [ ] Touch targets adequados (44x44px mín)

## Mobile (375x667)
- [ ] Grid 1 coluna
- [ ] Menu mobile abre/fecha
- [ ] Thumbnails carregam
- [ ] Formulários utilizáveis
- [ ] Sem scroll horizontal
```

**Estimativa:** 6-8 horas

---

## ⚡ FASE 2: Performance & Otimização

**Prazo:** 1-2 semanas  
**Prioridade:** 🟡 MÉDIA  
**Versão Alvo:** 1.2.0

### 2.1 Lazy Loading Avançado

**Objetivo:** Implementar lazy loading inteligente para imagens e iframes.

**Tarefas:**
- [ ] Adicionar `loading="lazy"` nativo em todas as imagens
- [ ] Implementar IntersectionObserver para thumbs archive
- [ ] Lazy load para WooCommerce product images
- [ ] Placeholder blur-up effect (LQIP - Low Quality Image Placeholder)
- [ ] Preload para imagens above-the-fold

**Implementação:**
```php
// inc/core/class-lazy-loader.php
class NosfirNews_Lazy_Loader {
    public function __construct() {
        add_filter('wp_get_attachment_image_attributes', [$this, 'add_lazy_loading'], 10, 3);
        add_filter('the_content', [$this, 'add_lazy_to_content']);
    }
    
    public function add_lazy_loading($attr, $attachment, $size) {
        // Above fold = eager, resto = lazy
        static $image_count = 0;
        $image_count++;
        
        if ($image_count <= 3) {
            $attr['loading'] = 'eager';
            $attr['fetchpriority'] = 'high';
        } else {
            $attr['loading'] = 'lazy';
        }
        
        return $attr;
    }
}
```

**Estimativa:** 8-10 horas

---

### 2.2 Critical CSS Extraction

**Objetivo:** Extrair CSS crítico para above-the-fold, inline no `<head>`.

**Tarefas:**
- [ ] Instalar e configurar Critical CSS tool
- [ ] Gerar CSS crítico para: home, archive, single, page
- [ ] Implementar inline de CSS crítico
- [ ] Defer non-critical CSS
- [ ] Adicionar fallback para browsers antigos

**Tools Recomendadas:**
- [Critical](https://github.com/addyosmani/critical)
- [Penthouse](https://github.com/pocketjoso/penthouse)

**Implementação:**
```php
function nosfirnews_inline_critical_css() {
    $template = get_page_template_slug();
    $critical_file = get_template_directory() . '/assets/css/critical/' . ($template ?: 'default') . '.css';
    
    if (file_exists($critical_file)) {
        echo '<style id="nn-critical-css">' . file_get_contents($critical_file) . '</style>';
    }
}
add_action('wp_head', 'nosfirnews_inline_critical_css', 1);

// Defer main CSS
function nosfirnews_defer_css($html, $handle) {
    if ($handle === 'nosfirnews-style') {
        return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
    }
    return $html;
}
add_filter('style_loader_tag', 'nosfirnews_defer_css', 10, 2);
```

**Estimativa:** 10-12 horas

---

### 2.3 Database Query Optimization

**Objetivo:** Otimizar queries WP_Query, implementar object caching.

**Tarefas:**
- [ ] Auditar todas as queries customizadas
- [ ] Implementar transients caching
- [ ] Otimizar loop principal (index.php)
- [ ] Adicionar suporte a Redis/Memcached
- [ ] Implementar fragment caching para widgets

**Antes:**
```php
// ❌ Query não otimizada
$posts = get_posts([
    'post_type' => 'post',
    'posts_per_page' => -1, // Pega TODOS os posts!
    'meta_query' => [
        // Meta query sem índice
    ]
]);
```

**Depois:**
```php
// ✅ Query otimizada com cache
function nosfirnews_get_featured_posts() {
    $cache_key = 'nn_featured_posts';
    $posts = wp_cache_get($cache_key);
    
    if ($posts === false) {
        $posts = new WP_Query([
            'post_type' => 'post',
            'posts_per_page' => 6,
            'meta_key' => 'featured',
            'meta_value' => '1',
            'fields' => 'ids', // Apenas IDs se não precisar de todo o objeto
            'no_found_rows' => true, // Pula contagem total
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);
        
        wp_cache_set($cache_key, $posts, '', HOUR_IN_SECONDS);
    }
    
    return $posts;
}
```

**Estimativa:** 10-14 horas

---

### 2.4 Asset Optimization Pipeline

**Objetivo:** Minificar, concatenar e versionar assets automaticamente.

**Tarefas:**
- [ ] Configurar webpack/gulp para build
- [ ] Minificar JS e CSS para produção
- [ ] Implementar cache busting com hash de arquivos
- [ ] Gerar source maps para desenvolvimento
- [ ] Configurar autoprefixer para CSS

**Estrutura de Build:**
```javascript
// webpack.config.js
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

module.exports = {
  entry: {
    'theme': './assets/js/src/main.js',
    'admin': './assets/js/src/admin.js',
  },
  output: {
    path: path.resolve(__dirname, 'assets/js'),
    filename: '[name].[contenthash].min.js'
  },
  optimization: {
    minimizer: [
      new TerserPlugin(),
      new CssMinimizerPlugin(),
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '../css/[name].[contenthash].min.css'
    })
  ]
};
```

**Estimativa:** 12-16 horas

---

### 2.5 Image Optimization

**Objetivo:** Servir imagens otimizadas, WebP, dimensionamento correto.

**Tarefas:**
- [ ] Implementar conversão automática para WebP
- [ ] Gerar múltiplos tamanhos com `srcset`
- [ ] Implementar adaptive images (device pixel ratio)
- [ ] Comprimir imagens no upload
- [ ] Adicionar dimensões de imagem no HTML (CLS fix)

**Implementação:**
```php
// Forçar WebP quando suportado
add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id) {
    if (!function_exists('imagewebp')) {
        return $metadata;
    }
    
    $file = get_attached_file($attachment_id);
    $upload_dir = wp_upload_dir();
    
    foreach ($metadata['sizes'] as $size => $data) {
        $image_path = $upload_dir['path'] . '/' . $data['file'];
        $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image_path);
        
        $image = wp_get_image_editor($image_path);
        if (!is_wp_error($image)) {
            $image->save($webp_path, 'image/webp');
        }
    }
    
    return $metadata;
}, 10, 2);

// Servir WebP com fallback
add_filter('wp_get_attachment_image_src', function($image, $attachment_id, $size) {
    if (!isset($_SERVER['HTTP_ACCEPT']) || strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') === false) {
        return $image;
    }
    
    $webp_url = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image[0]);
    
    if (file_exists(str_replace(home_url(), ABSPATH, $webp_url))) {
        $image[0] = $webp_url;
    }
    
    return $image;
}, 10, 3);
```

**Estimativa:** 8-10 horas

---

## 🎨 FASE 3: Recursos Avançados

**Prazo:** 2-3 semanas  
**Prioridade:** 🟢 NORMAL  
**Versão Alvo:** 1.3.0

### 3.1 Dark Mode Implementation

**Objetivo:** Tema escuro completo com toggle e persistência.

**Tarefas:**
- [ ] Criar paleta de cores dark mode
- [ ] Implementar toggle no header
- [ ] Salvar preferência em localStorage
- [ ] Respeitar `prefers-color-scheme`
- [ ] Animar transição entre modos

**Estrutura:**
```css
/* CSS Variables para Dark Mode */
:root {
  --bg: #ffffff;
  --text: #333333;
  --border: #e1e5e9;
  --card-bg: #ffffff;
}

[data-theme="dark"] {
  --bg: #1a1a1a;
  --text: #e4e4e4;
  --border: #333333;
  --card-bg: #252525;
}

/* Smooth transition */
body {
  background: var(--bg);
  color: var(--text);
  transition: background-color 0.3s ease, color 0.3s ease;
}
```

**JavaScript:**
```javascript
class ThemeToggle {
  constructor() {
    this.theme = localStorage.getItem('nn-theme') || 
                 (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    this.init();
  }
  
  init() {
    document.documentElement.setAttribute('data-theme', this.theme);
    
    document.querySelector('.theme-toggle')?.addEventListener('click', () => {
      this.toggle();
    });
  }
  
  toggle() {
    this.theme = this.theme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', this.theme);
    localStorage.setItem('nn-theme', this.theme);
  }
}

new ThemeToggle();
```

**Estimativa:** 12-16 horas

---

### 3.2 Advanced Typography System

**Objetivo:** Sistema tipográfico fluido e responsivo com múltiplas fontes.

**Tarefas:**
- [ ] Implementar fluid typography com `clamp()`
- [ ] Adicionar seletor de Google Fonts no Customizer
- [ ] Font pairing presets (heading + body)
- [ ] Variable fonts support
- [ ] FOUT (Flash of Unstyled Text) prevention

**Sistema de Escalas:**
```css
:root {
  /* Fluid Type Scale */
  --fs-300: clamp(0.875rem, 0.8rem + 0.375vw, 1rem);
  --fs-400: clamp(1rem, 0.9rem + 0.5vw, 1.125rem);
  --fs-500: clamp(1.125rem, 1rem + 0.625vw, 1.375rem);
  --fs-600: clamp(1.375rem, 1.2rem + 0.875vw, 1.75rem);
  --fs-700: clamp(1.75rem, 1.5rem + 1.25vw, 2.25rem);
  --fs-800: clamp(2.25rem, 1.9rem + 1.75vw, 3rem);
  --fs-900: clamp(3rem, 2.5rem + 2.5vw, 4rem);
  
  /* Line Heights */
  --lh-tight: 1.2;
  --lh-normal: 1.5;
  --lh-loose: 1.75;
  
  /* Font Families */
  --ff-base: var(--user-font-base, system-ui, sans-serif);
  --ff-heading: var(--user-font-heading, var(--ff-base));
  --ff-mono: ui-monospace, monospace;
}

/* Typography Classes */
.text-xs { font-size: var(--fs-300); }
.text-sm { font-size: var(--fs-400); }
.text-base { font-size: var(--fs-400); }
.text-lg { font-size: var(--fs-500); }
.text-xl { font-size: var(--fs-600); }
.text-2xl { font-size: var(--fs-700); }
.text-3xl { font-size: var(--fs-800); }
.text-4xl { font-size: var(--fs-900); }
```

**Customizer Options:**
```php
// Font Pairing Presets
$presets = [
  'classic' => [
    'heading' => 'Playfair Display',
    'body' => 'Source Sans Pro',
  ],
  'modern' => [
    'heading' => 'Montserrat',
    'body' => 'Open Sans',
  ],
  'editorial' => [
    'heading' => 'Merriweather',
    'body' => 'Lato',
  ],
  'minimal' => [
    'heading' => 'Poppins',
    'body' => 'Inter',
  ],
];
```

**Estimativa:** 16-20 horas

---

### 3.3 Advanced Header Builder

**Objetivo:** Drag-and-drop header builder similar ao Neve/Kadence.

**Decisão Estratégica:**
```
┌─────────────────────────────────────────────┐
│ Header Builder: Desenvolver ou Integrar?   │
│                                             │
│ OPÇÃO A: Desenvolver do zero              │
│   Prós: Controle total, integração perfeita│
│   Contras: 60-80h de desenvolvimento       │
│   Estimativa: 3-4 semanas                  │
│                                             │
│ OPÇÃO B: Integrar Elementor/Beaver Builder│
│   Prós: Rápido, features prontas           │
│   Contras: Dependência de plugin           │
│   Estimativa: 1 semana                     │
│                                             │
│ RECOMENDAÇÃO: OPÇÃO B para v1.3, OPÇÃO A  │
│ para v2.0 se houver demanda                │
└─────────────────────────────────────────────┘
```

**Se desenvolver (Opção A):**
```javascript
// Estrutura JSON do Header
{
  "desktop": {
    "top": ["social", "search"],
    "main": ["logo", "navigation", "cart"],
    "bottom": []
  },
  "mobile": {
    "main": ["logo", "toggle"],
    "drawer": ["navigation", "search"]
  }
}
```

**Estimativa (Opção B):** 40-50 horas  
**Estimativa (Opção A):** 60-80 horas

---

### 3.4 Mega Menu System

**Objetivo:** Mega menu dropdown com colunas e widgets.

**Tarefas:**
- [ ] Implementar walker customizado
- [ ] Adicionar campos de configuração no menu admin
- [ ] Layout multi-coluna para mega menu
- [ ] Suporte a widgets em mega menu
- [ ] Ícones para items de menu

**Campos Customizados:**
```php
// Menu Item Custom Fields
add_action('wp_nav_menu_item_custom_fields', function($item_id, $item) {
    $mega_enabled = get_post_meta($item_id, '_menu_item_mega_enabled', true);
    $mega_columns = get_post_meta($item_id, '_menu_item_mega_columns', true) ?: 4;
    $menu_icon = get_post_meta($item_id, '_menu_item_icon', true);
    ?>
    <p class="field-mega-menu">
        <label>
            <input type="checkbox" name="menu-item-mega-enabled[<?php echo $item_id; ?>]" 
                   value="1" <?php checked($mega_enabled, '1'); ?>>
            Enable Mega Menu
        </label>
    </p>
    <p class="field-mega-columns">
        <label>
            Columns:
            <select name="menu-item-mega-columns[<?php echo $item_id; ?>]">
                <?php for($i = 2; $i <= 6; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php selected($mega_columns, $i); ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </label>
    </p>
    <p class="field-menu-icon">
        <label>
            Icon Class:
            <input type="text" name="menu-item-icon[<?php echo $item_id; ?>]" 
                   value="<?php echo esc_attr($menu_icon); ?>">
        </label>
    </p>
    <?php
}, 10, 2);
```

**Estimativa:** 20-25 horas

---

### 3.5 WooCommerce Deep Integration

**Objetivo:** Integração completa e otimizada com WooCommerce.

**Tarefas:**
- [ ] Templates customizados para shop/product
- [ ] Quick view modal
- [ ] Wishlist integration
- [ ] Ajax add to cart com notificações
- [ ] Mini cart no header com preview
- [ ] Filtros avançados (AJAX)
- [ ] Otimizar queries de produtos

**Features:**
```php
// Quick View
add_action('woocommerce_after_shop_loop_item', function() {
    global $product;
    echo '<button class="nn-quick-view" data-product-id="' . $product->get_id() . '">
            Quick View
          </button>';
}, 15);

// Ajax Add to Cart
add_action('wp_ajax_nn_add_to_cart', function() {
    $product_id = absint($_POST['product_id']);
    $quantity = absint($_POST['quantity']) ?: 1;
    
    $added = WC()->cart->add_to_cart($product_id, $quantity);
    
    if ($added) {
        wp_send_json_success([
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total(),
            'fragments' => apply_filters('woocommerce_add_to_cart_fragments', [])
        ]);
    } else {
        wp_send_json_error(['message' => 'Failed to add product']);
    }
});
add_action('wp_ajax_nopriv_nn_add_to_cart', 'wp_ajax_nn_add_to_cart');
```

**Estimativa:** 30-40 horas

---

## 💅 FASE 4: Polimento & UX

**Prazo:** 1 semana  
**Prioridade:** 🟢 NORMAL  
**Versão Alvo:** 1.4.0

### 4.1 Micro-Interactions & Animations

**Objetivo:** Adicionar feedback visual e transições suaves.

**Tarefas:**
- [ ] Hover states refinados em todos os elementos
- [ ] Loading states para Ajax actions
- [ ] Skeleton screens para lazy loading
- [ ] Toast notifications system
- [ ] Page transitions suaves
- [ ] Scroll-based animations (AOS/GSAP)

**Bibliotecas Recomendadas:**
- AOS (Animate On Scroll): https://michalsnik.github.io/aos/
- GSAP: https://greensock.com/gsap/ (premium features opcional)

**Implementação:**
```css
/* Micro-interactions */
.nn-btn {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.nn-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.nn-btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Loading State */
.nn-btn.loading {
  pointer-events: none;
  opacity: 0.6;
  position: relative;
}

.nn-btn.loading::after {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  border: 2px solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Skeleton Screen */
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s ease-in-out infinite;
}

@keyframes skeleton-loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
```

**Estimativa:** 12-16 horas

---

### 4.2 Acessibilidade (WCAG 2.1 AA)

**Objetivo:** Conformidade completa com WCAG 2.1 Level AA.

**Tarefas:**
- [ ] Auditar com WAVE, axe DevTools
- [ ] Garantir contraste de cores 4.5:1 (texto) e 3:1 (UI)
- [ ] Skip links funcionais
- [ ] Focus management (modal, mobile menu)
- [ ] ARIA labels em todos os elementos interativos
- [ ] Keyboard navigation completa
- [ ] Screen reader testing

**Checklist:**
```markdown
## Perceivable
- [ ] Alt text em todas as imagens
- [ ] Contraste adequado em todos os estados
- [ ] Vídeos com legendas (se aplicável)
- [ ] Sem informação apenas por cor

## Operable
- [ ] Todo conteúdo acessível via teclado
- [ ] Sem keyboard traps
- [ ] Skip to main content link
- [ ] Focus visível em todos os elementos
- [ ] Tempo suficiente para interações

## Understandable
- [ ] Linguagem clara
- [ ] Mensagens de erro descritivas
- [ ] Labels em campos de formulário
- [ ] Navegação consistente

## Robust
- [ ] Validação HTML sem erros
- [ ] ARIA usado corretamente
- [ ] Compatível com leitores de tela
```

**Estimativa:** 16-20 horas

---

### 4.3 Mobile UX Refinements

**Objetivo:** Experiência mobile perfeita com touch gestures.

**Tarefas:**
- [ ] Aumentar touch targets para 44x44px mínimo
- [ ] Swipe gestures para mobile menu
- [ ] Pull-to-refresh (opcional)
- [ ] Sticky "Back to top" button
- [ ] Melhorar scroll performance (passive listeners)
- [ ] Otimizar forms para mobile keyboards

**Touch Enhancements:**
```javascript
// Swipe to Close Mobile Menu
class SwipeMenu {
  constructor(element) {
    this.element = element;
    this.startX = 0;
    this.currentX = 0;
    this.isDragging = false;
    
    this.element.addEventListener('touchstart', this.onTouchStart.bind(this), { passive: true });
    this.element.addEventListener('touchmove', this.onTouchMove.bind(this), { passive: false });
    this.element.addEventListener('touchend', this.onTouchEnd.bind(this));
  }
  
  onTouchStart(e) {
    this.startX = e.touches[0].clientX;
    this.isDragging = true;
  }
  
  onTouchMove(e) {
    if (!this.isDragging) return;
    
    this.currentX = e.touches[0].clientX;
    const diff = this.currentX - this.startX;
    
    if (diff > 0) {
      e.preventDefault();
      this.element.style.transform = `translateX(${diff}px)`;
    }
  }
  
  onTouchEnd() {
    const diff = this.currentX - this.startX;
    
    if (diff > 100) {
      // Close menu
      this.element.closest('.nn-mobile-drawer').classList.remove('open');
    }
    
    this.element.style.transform = '';
    this.isDragging = false;
  }
}

document.querySelectorAll('.mobile-nav-menu').forEach(menu => {
  new SwipeMenu(menu);
});
```

**Estimativa:** 10-12 horas

---

### 4.4 Advanced Search Experience

**Objetivo:** Busca instantânea com autocompletar e filtros.

**Tarefas:**
- [ ] Ajax live search
- [ ] Autocomplete com sugestões
- [ ] Busca em categorias/tags
- [ ] Resultados com thumbnails
- [ ] Histórico de buscas
- [ ] Integração com Elasticsearch (opcional)

**Implementação:**
```javascript
class LiveSearch {
  constructor(input) {
    this.input = input;
    this.resultsContainer = document.createElement('div');
    this.resultsContainer.className = 'nn-search-results';
    this.input.parentNode.appendChild(this.resultsContainer);
    
    this.input.addEventListener('input', this.debounce(this.search.bind(this), 300));
  }
  
  debounce(func, wait) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  }
  
  async search() {
    const query = this.input.value;
    
    if (query.length < 3) {
      this.resultsContainer.innerHTML = '';
      return;
    }
    
    try {
      const response = await fetch(`/wp-json/nn/v1/search?q=${encodeURIComponent(query)}`);
      const results = await response.json();
      this.displayResults(results);
    } catch (error) {
      console.error('Search error:', error);
    }
  }
  
  displayResults(results) {
    if (results.length === 0) {
      this.resultsContainer.innerHTML = '<p>No results found</p>';
      return;
    }
    
    const html = results.map(result => `
      <a href="${result.url}" class="nn-search-result">
        ${result.thumbnail ? `<img src="${result.thumbnail}" alt="">` : ''}
        <div>
          <h4>${result.title}</h4>
          <p>${result.excerpt}</p>
        </div>
      </a>
    `).join('');
    
    this.resultsContainer.innerHTML = html;
  }
}

// Init
document.querySelectorAll('.search-field').forEach(input => {
  new LiveSearch(input);
});
```

**Estimativa:** 14-18 horas

---

### 4.5 Loading States & Feedback

**Objetivo:** Feedback visual para todas as ações do usuário.

**Tarefas:**
- [ ] Skeleton screens para posts grid
- [ ] Progress bar para page loads
- [ ] Spinners para Ajax actions
- [ ] Success/error toast notifications
- [ ] Smooth page transitions
- [ ] Loading overlay para forms

**Toast System:**
```javascript
class ToastNotification {
  constructor() {
    this.container = document.createElement('div');
    this.container.className = 'nn-toast-container';
    document.body.appendChild(this.container);
  }
  
  show(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `nn-toast nn-toast-${type}`;
    toast.textContent = message;
    
    this.container.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Auto remove
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, duration);
  }
}

// Global instance
window.nnToast = new ToastNotification();

// Usage
nnToast.show('Item added to cart!', 'success');
nnToast.show('Something went wrong', 'error');
```

**Estimativa:** 8-10 horas

---

## 📚 FASE 5: Documentação & Release

**Prazo:** 3-5 dias  
**Prioridade:** 🔴 CRÍTICA (antes do release)  
**Versão Alvo:** 2.0.0

### 5.1 Code Documentation

**Tarefas:**
- [ ] Documentar todas as funções públicas (PHPDoc)
- [ ] Documentar hooks e filters
- [ ] Comentários inline em código complexo
- [ ] JSDoc para JavaScript
- [ ] Generate API documentation (phpDocumentor)

**Padrão PHPDoc:**
```php
/**
 * Render post thumbnail with optimized settings
 *
 * This function handles thumbnail rendering with support for lazy loading,
 * responsive images, and custom styling based on theme settings and post meta.
 *
 * @since 1.0.0
 * @since 2.0.0 Added lazy loading support
 *
 * @param int    $post_id  Post ID to get thumbnail for
 * @param string $context  Context where thumbnail is displayed (archive|single)
 * @param array  $args     Optional. Additional arguments {
 *     @type string $size    Image size. Default 'large'
 *     @type bool   $lazy    Enable lazy loading. Default true
 *     @type string $fit     Fit mode (cover|contain|auto). Default 'cover'
 * }
 *
 * @return void Outputs HTML directly
 */
function nosfirnews_render_thumbnail($post_id, $context = 'archive', $args = []) {
    // Implementation
}
```

**Estimativa:** 12-16 horas

---

### 5.2 User Documentation

**Tarefas:**
- [ ] Escrever guia de instalação
- [ ] Documentar opções do Customizer
- [ ] Tutorial de setup inicial
- [ ] FAQ com problemas comuns
- [ ] Video tutoriais (opcional)
- [ ] Changelog detalhado

**Estrutura de Docs:**
```
docs/
├── README.md (Overview)
├── installation.md
├── getting-started.md
├── customization/
│   ├── colors.md
│   ├── typography.md
│   ├── header.md
│   └── footer.md
├── features/
│   ├── dark-mode.md
│   ├── woocommerce.md
│   └── performance.md
├── developer/
│   ├── hooks-filters.md
│   ├── child-theme.md
│   └── api-reference.md
├── troubleshooting.md
└── changelog.md
```

**Estimativa:** 16-20 horas

---

### 5.3 Pre-Launch Checklist

**Quality Assurance Final:**

```markdown
## Funcionalidade
- [ ] Todos os templates renderizam corretamente
- [ ] Formulários funcionam (comentários, busca, contato)
- [ ] Widgets funcionam em todas as áreas
- [ ] Menus navegam corretamente
- [ ] Paginação funciona
- [ ] Busca retorna resultados

## Performance
- [ ] PageSpeed Insights: 90+ (mobile e desktop)
- [ ] GTmetrix: Grade A
- [ ] WebPageTest: < 3s load time
- [ ] Lighthouse: 90+ em todas as métricas

## Compatibilidade
- [ ] WordPress 6.0+
- [ ] PHP 7.4+
- [ ] Testado em Chrome, Firefox, Safari, Edge
- [ ] Testado em iOS Safari e Chrome Android
- [ ] WooCommerce 7.0+
- [ ] Elementor (se suportado)

## Acessibilidade
- [ ] WAVE: 0 erros
- [ ] axe DevTools: 0 violações críticas
- [ ] Keyboard navigation completa
- [ ] Screen reader testado (NVDA/JAWS)

## Segurança
- [ ] Todos os inputs sanitizados
- [ ] Outputs escaped corretamente
- [ ] Nonces verificados
- [ ] Permissões checadas (capabilities)
- [ ] XSS prevention
- [ ] SQL injection prevention

## SEO
- [ ] Schema markup correto
- [ ] Meta tags implementados
- [ ] Open Graph tags
- [ ] Twitter Cards
- [ ] Sitemap XML gerado

## Code Quality
- [ ] WordPress Coding Standards
- [ ] PHP_CodeSniffer: 0 erros
- [ ] ESLint: 0 erros
- [ ] CSS Validator: 0 erros
- [ ] HTML Validator: 0 erros

## Legal
- [ ] GPL-compatible license
- [ ] Third-party licenses documentadas
- [ ] Copyright notices corretos
```

**Estimativa:** 8-12 horas (testes)

---

### 5.4 Release Preparation

**Tarefas:**
- [ ] Atualizar version numbers em todos os arquivos
- [ ] Gerar build final (minified assets)
- [ ] Criar package ZIP
- [ ] Escrever release notes
- [ ] Preparar changelog
- [ ] Screenshot.png otimizado
- [ ] Verificar theme.json (se FSE parcial)

**Release Notes Template:**
```markdown
# NosfirNews v2.0.0

Released: [DATE]

## 🎉 Highlights

- Complete CSS refactor for better performance
- Dark mode support
- Advanced typography system
- WooCommerce deep integration
- WCAG 2.1 AA compliant

## ✨ New Features

- [Feature 1]
- [Feature 2]

## 🐛 Bug Fixes

- Fixed CSS grid issues on mobile
- Fixed header overlap on scroll
- Fixed thumbnail loading performance

## 💅 Improvements

- Reduced CSS size by 35%
- Improved mobile menu UX
- Better touch targets

## ⚠️ Breaking Changes

- Deprecated old thumbnail system (use new API)
- Changed class naming for grid (update child themes)

## 📦 Upgrade Guide

[Link to upgrade documentation]

## 🙏 Credits

Thanks to [contributors]

---

**Full Changelog:** https://github.com/user/nosfirnews/compare/v1.0.0...v2.0.0
```

**Estimativa:** 4-6 horas

---

## 🎒 BACKLOG de Funcionalidades

### Recursos para Versões Futuras

#### v2.1.0 - Block Patterns & FSE
- [ ] Block patterns library (20+ patterns)
- [ ] Full Site Editing support (experimental)
- [ ] Block styles customization
- [ ] Template parts system

#### v2.2.0 - Advanced Integrations
- [ ] LifterLMS integration
- [ ] MemberPress integration
- [ ] WPML full compatibility
- [ ] Polylang support
- [ ] bbPress/BuddyPress integration

#### v2.3.0 - Marketing Features
- [ ] Newsletter popup
- [ ] Social share buttons
- [ ] Reading progress bar
- [ ] Estimated reading time
- [ ] Related posts (AI-powered)
- [ ] Trending posts widget

#### v2.4.0 - Performance Pro
- [ ] Service Worker / PWA
- [ ] Offline mode
- [ ] Background sync
- [ ] Push notifications
- [ ] Edge caching support

#### v2.5.0 - AI & Personalization
- [ ] AI-powered content recommendations
- [ ] Personalized layouts
- [ ] A/B testing framework
- [ ] User behavior analytics
- [ ] Adaptive loading based on connection

---

## 📊 Métricas de Sucesso

### KPIs por Fase

#### Fase 0 - Correções Críticas
```
✅ SUCESSO SE:
- CSS conflicts: 0
- Console errors: 0
- Mobile menu works: 100%
- Grid breaks: 0
- Lighthouse Performance: 60+ (baseline)
```

#### Fase 1 - Estabilização
```
✅ SUCESSO SE:
- Code coverage: 70%+
- Regression bugs: 0
- Browser compatibility: 95%+
- Customizer options working: 100%
```

#### Fase 2 - Performance
```
✅ SUCESSO SE:
- PageSpeed mobile: 90+
- PageSpeed desktop: 95+
- LCP: < 2.5s
- FID: < 100ms
- CLS: < 0.1
- Total blocking time: < 300ms
```

#### Fase 3 - Recursos Avançados
```
✅ SUCESSO SE:
- Dark mode works: 100%
- WooCommerce integration: Complete
- Typography scales: Fluid
- Mega menu loads: < 200ms
```

#### Fase 4 - Polimento
```
✅ SUCESSO SE:
- WCAG 2.1 AA: 100% compliant
- Touch targets: 44x44px mínimo
- Animation performance: 60fps
- User satisfaction: 4.5/5+
```

#### Fase 5 - Release
```
✅ SUCESSO SE:
- Documentation: Complete
- Known bugs: 0 critical, < 5 minor
- Test coverage: 80%+
- Theme Check Plugin: Passed
```

---

## 🔄 Processo de Desenvolvimento

### Git Workflow

```bash
# Branch structure
main                    # Stable releases only
├── develop            # Integration branch
│   ├── feature/*      # New features
│   ├── fix/*          # Bug fixes
│   └── refactor/*     # Code improvements

# Example workflow
git checkout develop
git checkout -b feature/dark-mode
# ... work on feature ...
git commit -m "feat: add dark mode toggle"
git push origin feature/dark-mode
# Create PR to develop
```

### Commit Convention (Conventional Commits)

```
feat: add dark mode support
fix: resolve mobile menu overlay issue
refactor: simplify thumbnail system
docs: update README with installation guide
style: format CSS according to standards
perf: optimize image loading
test: add unit tests for thumbnail class
chore: update dependencies
```

### Release Workflow

```bash
# Prepare release
git checkout develop
git checkout -b release/2.0.0
# Bump version numbers
# Update changelog
# Final testing

# Merge to main
git checkout main
git merge release/2.0.0
git tag -a v2.0.0 -m "Release version 2.0.0"
git push origin main --tags

# Merge back to develop
git checkout develop
git merge release/2.0.0
```

---

## 🛠️ Ferramentas Recomendadas

### Desenvolvimento
- **IDE:** VS Code com extensões WP
- **Local Server:** LocalWP ou Docker
- **Version Control:** Git + GitHub
- **Package Manager:** npm/yarn

### Testing
- **Browser Testing:** BrowserStack
- **Performance:** Lighthouse, WebPageTest, GTmetrix
- **Accessibility:** WAVE, axe DevTools
- **Code Quality:** PHP_CodeSniffer, ESLint

### Build & Deploy
- **Build Tool:** Webpack ou Gulp
- **CI/CD:** GitHub Actions
- **Hosting:** WP Engine, Kinsta, ou VPS otimizado

---

## 📞 Suporte & Comunidade

### Canais de Comunicação
- **Issues:** GitHub Issues para bugs
- **Discussions:** GitHub Discussions para features
- **Email:** support@nosfirnews.com
- **Docs:** https://docs.nosfirnews.com

### Contribuindo
Leia [CONTRIBUTING.md](CONTRIBUTING.md) para guidelines de contribuição.

---

## 📅 Cronograma Detalhado

### Q1 2026 (Jan-Mar)
```
Semana 1-2:   Fase 0 (Correções Críticas)
Semana 3:     Fase 1 (Estabilização)
Semana 4-5:   Fase 2 (Performance)
Semana 6-8:   Fase 3 (Recursos Avançados)
Semana 9:     Fase 4 (Polimento)
Semana 10:    Fase 5 (Documentação)
Semana 11:    Buffer / Refinamentos finais
Semana 12:    🚀 RELEASE v2.0.0
```

### Q2 2026 (Abr-Jun)
- Manutenção v2.0.x
- Planejamento v2.1.0 (Block Patterns)
- Community feedback integration

### Q3 2026 (Jul-Set)
- Release v2.1.0
- Start v2.2.0 (Integrations)

### Q4 2026 (Out-Dez)
- Release v2.2.0
- Planning v3.0.0 (major rewrite?)

---

## ✅ Definition of Done

Uma tarefa está completa quando:

- [ ] Código implementado e testado
- [ ] Documentação atualizada
- [ ] Testes unitários passando (se aplicável)
- [ ] Code review aprovado
- [ ] Browser testing completo
- [ ] Acessibilidade verificada
- [ ] Performance validada
- [ ] Merged para develop branch
- [ ] Changelog atualizado

---

## 🎯 Próximas Ações Imediatas

### ESTA SEMANA (Prioridade Máxima)

1. **Segunda-feira:**
   - [ ] Backup completo do tema atual
   - [ ] Substituir `style.css` com versão corrigida
   - [ ] Testar em 3 resoluções (mobile/tablet/desktop)

2. **Terça-feira:**
   - [ ] Criar `inc/core/class-thumbnail-manager.php`
   - [ ] Refatorar `template-parts/content.php`
   - [ ] Testes visuais

3. **Quarta-feira:**
   - [ ] Consolidar JavaScript em arquivo único
   - [ ] Implementar CSS dinâmico com cache
   - [ ] Performance testing

4. **Quinta-feira:**
   - [ ] Simplificar sistema de sidebars
   - [ ] Limpar código do Customizer
   - [ ] Code review

5. **Sexta-feira:**
   - [ ] Testes de regressão completos
   - [ ] Preparar release 1.0.1
   - [ ] Update CHANGELOG.md

---

## 🎬 Conclusão

Este roadmap é um documento vivo. Será atualizado conforme:
- Feedback da comunidade
- Mudanças no WordPress core
- Novas tendências de design
- Performance benchmarks
- Necessidades do mercado

**Última revisão:** 06 de Janeiro de 2026  
**Próxima revisão:** Fim da Fase 0

---

**Preparado por:** [David L. Almeida]  
**Aprovado por:** [Stakeholder]  
**Status:** 🟢 ATIVO