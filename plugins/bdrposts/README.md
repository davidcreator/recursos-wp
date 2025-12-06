# 🚀 Guia de Instalação Rápida - BDRPosts

## ⚡ Instalação em 3 Passos

### 1️⃣ Upload dos Arquivos

Estrutura de pastas necessária:

```
wp-content/
└── plugins/
    └── bdrposts/
        ├── bdrposts.php          ← Arquivo principal
        ├── uninstall.php         ← Script de desinstalação
        ├── README.md             ← Documentação
        └── build/                ← Assets compilados
            ├── index.js          ← Editor JS
            ├── frontend.js       ← Frontend JS
            ├── style.css         ← Frontend CSS
            └── editor.css        ← Editor CSS
```

### 2️⃣ Ativação

1. Acesse **WordPress Admin → Plugins**
2. Localize **BDRPosts**
3. Clique em **Ativar**
4. ✅ Pronto! O bloco está disponível

### 3️⃣ Primeiro Uso

1. Edite uma página/post
2. Clique no **+** para adicionar bloco
3. Busque por **"BDR Posts"**
4. Configure e publique!

---

## 📋 Checklist de Verificação

Após instalar, verifique:

- [ ] Plugin aparece na lista de plugins ativos
- [ ] Bloco "BDR Posts" disponível no editor
- [ ] Preview funciona no editor
- [ ] Barra de ferramentas do bloco aparece (mover/excluir)
- [ ] Posts aparecem no frontend
- [ ] Estilos carregados corretamente
- [ ] Slider funciona (se usar layout slider)
- [ ] Barra de filtros funciona (quando ativada)

---

## 🔧 Configuração Inicial Recomendada

### Configurações Básicas

```php
Layout: Grid
Colunas: 3
Posts por página: 6
Ordenar por: Data
Ordem: DESC (mais recentes primeiro)
```

### Elementos Visuais

```php
✅ Imagem Destacada: Sim
✅ Tamanho: Medium
✅ Título: Sim
✅ Resumo: Sim (20 palavras)
✅ Botão Ler Mais: Sim
```

### Meta Informações

```php
✅ Data: Sim
✅ Autor: Sim
✅ Categorias: Sim
❌ Tags: Não
❌ Tempo de Leitura: Não
```

---

## 🎯 Casos de Uso Comuns

### Blog/Notícias

```
Layout: Grid
Colunas: 3
Posts: 9
Mostrar: Imagem, Título, Data, Autor, Resumo
```

### Portfolio

```
Layout: Masonry
Colunas: 3
Posts: 12
Mostrar: Apenas Imagem e Título
Sub-layout: Overlay
```

### Últimas Atualizações

```
Layout: Ticker
Posts: 10
Mostrar: Apenas Títulos
```

### Destaque Principal

```
Layout: Slider
Posts: 5
Colunas: 1 (automático)
Mostrar: Tudo
```

---

## ⚙️ Configurações Avançadas

### Filtrar por Categoria

```php
// Via Bloco: Adicione IDs nas configurações
// Via Shortcode:
[bdrposts categories="5,8,12"]
```

### Barra de Filtros (frontend)

No editor do bloco:

```
Mostrar barra de filtros: ✅
Filtrar por: Categoria ou Tag
IDs de Termos: (opcional, separados por vírgula) → lista os 10 mais populares quando vazio
Rótulo do botão "Todos": personalizável
```

No frontend, a barra aparece acima dos cards. Ao clicar em um termo, a lista é atualizada via REST sem recarregar a página.

### Posts Aleatórios

```php
Ordenar Por: Random (rand)
```

### Excluir Post Atual

```php
// Útil em single.php
Excluir Post Atual: ✅ Sim
```

### Paginação

```php
// Para listas longas
Ativar Paginação: ✅ Sim
Posts por Página: 12
```

---

## 🎨 Personalizações Rápidas

### Mudar Cores

Adicione no **Aparência → Personalizar → CSS Adicional**:

```css
/* Cor primária */
.bdrposts-read-more {
    background: #seu-cor !important;
}

.bdrposts-title a:hover {
    color: #seu-cor !important;
}

/* Bordas dos cards */
.bdrposts-item:hover {
    border-color: #seu-cor !important;
}
```

### Ajustar Espaçamento

```css
/* Espaço entre cards */
.bdrposts-grid {
    gap: 40px !important;
}

/* Padding interno */
.bdrposts-content {
    padding: 30px !important;
}
```

### Fonte Personalizada

```css
.bdrposts-title {
    font-family: 'Sua Fonte', sans-serif;
    font-size: 24px;
}
```

---

## 🐛 Solução Rápida de Problemas

### Bloco não aparece

```bash
1. Limpe cache (Ctrl+Shift+Delete)
2. Desative/Reative o plugin
3. Teste em modo anônimo
```

### Estilos quebrados

```bash
1. Verifique se arquivos existem em /build/
2. Limpe cache do site
3. Force refresh: Ctrl+F5
```

### Slider não funciona

```bash
1. Abra Console (F12)
2. Procure por erros do Swiper
3. Desative outros plugins temporariamente
```

### Posts não aparecem

```bash
1. Verifique: Tem posts publicados?
2. Remova filtros de categoria/tag
3. Aumente "Posts por Página"
```

### Não consigo selecionar/excluir o bloco no editor

```
1. Atualize a página do editor (Ctrl+F5)
2. Clique dentro da área de preview do bloco para forçar a seleção
3. Verifique se a barra do bloco aparece no topo (mover, transformar, menu de três pontos)
4. Se usar tema com CSS agressivo, desative temporariamente estilos customizados do editor e teste
```

---

## 📞 Precisa de Ajuda?

### Recursos Disponíveis

- 📖 **README.md** - Documentação completa
- 💬 **Issues GitHub** - Reporte bugs
- 📧 **Email** - Suporte direto
- 🎥 **Vídeos** - Tutoriais (em breve)

### Antes de Pedir Ajuda

Tenha em mãos:
- ✅ Versão do WordPress
- ✅ Versão do PHP
- ✅ Tema ativo
- ✅ Lista de plugins ativos
- ✅ Mensagens de erro (se houver)
- ✅ Screenshots do problema

---

## 🎓 Próximos Passos

Após instalação bem-sucedida:

1. ✅ Experimente todos os 4 layouts
2. ✅ Teste os 5 sub-layouts
3. ✅ Configure filtros personalizados
4. ✅ Personalize cores e fontes
5. ✅ Teste em mobile
6. ✅ Compartilhe seu feedback!

---

## ⭐ Dica Pro

Crie vários blocos com configurações diferentes:

```
Bloco 1: Posts Recentes (Grid 3 colunas)
Bloco 2: Destaque (Slider)
Bloco 3: Últimas Notícias (Ticker)
Bloco 4: Portfolio (Masonry)
```

Isso cria uma homepage dinâmica e profissional! 🚀

---

**Instalação concluída! Agora é só criar conteúdo incrível! 🎉**

# 🔧 Guia de Correção - BDRPosts v1.0.1

## 🐛 Problemas Corrigidos

### 1. **Seleção de Categorias e Tags**
- ✅ Adicionadas rotas REST API para buscar categorias e tags
- ✅ Implementado sistema de checkboxes no editor
- ✅ Correção na validação de arrays vazios

### 2. **Compatibilidade com Twenty Twenty-Five**
- ✅ Corrigida dependência do ServerSideRender
- ✅ Adicionado fallback para temas que não suportam todas as features
- ✅ Melhorada a renderização no editor

### 3. **Preview no Editor**
- ✅ ServerSideRender agora funciona corretamente
- ✅ Adicionado estado de loading
- ✅ Fallback quando preview não está disponível

---

## 📦 Arquivos Modificados

### 1. `bdrposts.php` (Arquivo Principal)

**Principais mudanças:**

```php
// ✅ Nova versão
define('BDRPOSTS_VERSION', '1.0.1');

// ✅ Novas rotas REST API
- /bdrposts/v1/categories (GET)
- /bdrposts/v1/tags (GET)  
- /bdrposts/v1/terms/{taxonomy} (GET)

// ✅ Melhorias na validação de arrays
- Verificação de arrays vazios antes de usar
- Validação com count() > 0

// ✅ Correção no enqueue de scripts
- Adicionado 'wp-server-side-render' nas dependências
- Melhor fallback para temas incompatíveis
```

### 2. `build/index.js` (Editor JavaScript)

**Principais mudanças:**

```javascript
// ✅ Novos estados para dados dinâmicos
const [categories, setCategories] = useState([]);
const [tags, setTags] = useState([]);
const [taxonomyTerms, setTaxonomyTerms] = useState([]);

// ✅ Checkboxes para categorias
categories.map(cat => 
    wp.element.createElement(CheckboxControl, {
        label: cat.label,
        checked: attributes.categories.includes(cat.value),
        onChange: (checked) => { /* toggle */ }
    })
)

// ✅ Melhor tratamento do ServerSideRender
const { ServerSideRender } = wp.serverSideRender || wp.editor || { ServerSideRender: null };

// ✅ Estado de loading
const [loading, setLoading] = useState(true);
```

---

## 🚀 Instruções de Instalação

### Passo 1: Backup

```bash
# Faça backup dos arquivos atuais
cp -r wp-content/plugins/bdrposts wp-content/plugins/bdrposts-backup
```

### Passo 2: Substituir Arquivos

Substitua os seguintes arquivos:

1. **`bdrposts.php`** - Arquivo principal do plugin
2. **`build/index.js`** - JavaScript do editor

### Passo 3: Limpar Cache

```bash
# WordPress
1. Vá em Plugins → Desativar BDRPosts
2. Ativar novamente
3. Limpar cache do navegador (Ctrl+Shift+Delete)
4. Recarregar editor (Ctrl+F5)

# Se usar cache de objeto
wp cache flush
```

### Passo 4: Verificação

Após atualizar, verifique:

- [ ] Versão do plugin é 1.0.1
- [ ] Checkboxes de categorias aparecem
- [ ] Checkboxes de tags aparecem
- [ ] Preview funciona no editor
- [ ] Posts aparecem no frontend
- [ ] Filtros por categoria funcionam

---

## 🧪 Testes Recomendados

### Teste 1: Filtro por Categoria

```
1. Abra o editor de uma página
2. Adicione bloco BDR Posts
3. Na barra lateral → Filtros
4. Marque 2-3 categorias
5. Verifique se apenas posts dessas categorias aparecem
```

### Teste 2: Filtro por Tags

```
1. No mesmo bloco
2. Vá em Filtros → Tags
3. Selecione algumas tags
4. Verifique se filtragem funciona
```

### Teste 3: Preview no Editor

```
1. Ao adicionar o bloco, o preview deve carregar
2. Ao mudar configurações, preview deve atualizar
3. Não deve mostrar erros no console (F12)
```

### Teste 4: Tema Twenty Twenty-Five

```
1. Ative o tema Twenty Twenty-Five
2. Crie nova página com o bloco
3. Verifique se renderiza corretamente
4. Publique e veja no frontend
```

---

## 🔍 Solução de Problemas Comuns

### Problema: Checkboxes não aparecem

**Solução:**
```javascript
// Verifique no console do navegador (F12):
console.log(bdrpostsData);

// Deve retornar:
{
  restUrl: "https://seusite.com/wp-json/bdrposts/v1/",
  nonce: "abc123...",
  pluginUrl: "https://seusite.com/wp-content/plugins/bdrposts/"
}
```

Se não aparecer, verifique:
1. Plugin está ativo
2. Permalinks estão configuradas (Configurações → Links permanentes → Salvar)
3. REST API está funcionando: `/wp-json/bdrposts/v1/categories`

### Problema: Erro "ServerSideRender is not defined"

**Solução:**
O código corrigido já tem fallback. Se ainda ocorrer:

```javascript
// Adicione no functions.php do tema:
add_action('enqueue_block_editor_assets', function() {
    wp_enqueue_script('wp-server-side-render');
});
```

### Problema: Posts não aparecem no frontend

**Solução:**
```php
// Ative debug no wp-config.php:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Verifique o log em: wp-content/debug.log
```

Verifique:
1. Há posts publicados?
2. Categorias/tags existem?
3. IDs estão corretos?

### Problema: Preview não atualiza

**Solução:**
```bash
# Limpe cache agressivamente:
1. Desative plugins de cache
2. Ctrl+Shift+Delete (limpar tudo)
3. Modo anônimo do navegador
4. Recarregar editor
```

---

## 📊 Comparação de Versões

| Feature | v1.0.0 | v1.0.1 |
|---------|--------|--------|
| Filtro Categorias | ❌ Campo texto | ✅ Checkboxes |
| Filtro Tags | ❌ Campo texto | ✅ Checkboxes |
| Preview Editor | ⚠️ Parcial | ✅ Completo |
| Twenty Twenty-Five | ❌ Erro | ✅ Funciona |
| REST API | ⚠️ Básica | ✅ Completa |
| Taxonomias Custom | ❌ Não | ✅ Com termos |
| Barra de Filtros (frontend) | ❌ Não | ✅ Sim |
| Swiper condicional | ❌ Não | ✅ Sim |
| Cache de HTML | ❌ Não | ✅ Sim |

---

## 🎯 Novos Recursos Adicionados

### 1. API REST Expandida

```http
GET /wp-json/bdrposts/v1/categories
GET /wp-json/bdrposts/v1/tags
GET /wp-json/bdrposts/v1/terms/{taxonomy}
POST /wp-json/bdrposts/v1/render
```

**Exemplo de resposta:**
```json
[
  {
    "value": 5,
    "label": "Tecnologia (12)"
  },
  {
    "value": 8,
    "label": "Design (7)"
  }
]
```

### 2. Barra de Filtros (frontend)

Renderiza termos de categoria ou tag e atualiza a lista via REST sem recarregar.
Configurações no editor: ativar barra, escolher taxonomia, limitar por IDs e rótulo de "Todos".

### 2. Seleção Visual de Filtros

Antes:
```
IDs de Categorias: [5,8,12]
```

Agora:
```
☑ Tecnologia (12)
☑ Design (7)
☐ Marketing (3)
```

### 3. Validação Aprimorada

```php
// Antes
if (!empty($attributes['categories'])) {
    // Erro se array vazio []
}

// Agora  
if (!empty($attributes['categories']) && count($attributes['categories']) > 0) {
    // Funciona corretamente
}
```

## 🛠️ Otimizações e Compatibilidade

- Swiper condicional: só carrega em páginas que usam layout `slider`.
- Imagens otimizadas: `sizes`, `decoding="async"`, `fetchpriority` para melhor LCP.
- Acessibilidade: respeita `prefers-reduced-motion` para ticker/animações.
- Cache de HTML: respostas sem paginação são cacheadas por 120s e limpas em alterações de conteúdo.
- Editor compatível: integra `useBlockProps` e `BlockControls`; preview evita navegação e permite seleção do bloco.
- Uninstall corrigido: remove opções/transients/meta com prefixo `bdrposts_*`.

---

## 📝 Notas de Atualização

### Compatibilidade

- ✅ WordPress 5.8+
- ✅ PHP 7.4+
- ✅ Gutenberg Editor
- ✅ Classic Editor (via shortcode)

### Temas Testados

- ✅ Twenty Twenty-Five
- ✅ Twenty Twenty-Four
- ✅ Twenty Twenty-Three
- ✅ Astra
- ✅ GeneratePress
- ✅ OceanWP

### Plugins Compatíveis

- ✅ Yoast SEO
- ✅ Rank Math
- ✅ WooCommerce
- ✅ Advanced Custom Fields
- ✅ WPML

---

## 🔐 Segurança

### Melhorias de Segurança

1. **Validação de Permissões**
```php
'permission_callback' => function() {
    return current_user_can('edit_posts');
}
```

2. **Sanitização de Dados**
```php
// Todas as saídas usam esc_*
esc_html(), esc_url(), esc_attr()
```

3. **Nonce Verificado**
```php
wp_localize_script('bdrposts-block-editor', 'bdrpostsData', array(
    'nonce' => wp_create_nonce('wp_rest')
));
```

---

## 💡 Dicas de Uso

### Dica 1: Combine Filtros

```
Categorias: ☑ Tech + ☑ News
Tags: ☑ Tutorial
→ Mostra posts que têm (Tech OU News) E Tutorial
```

### Dica 2: Use Taxonomias Customizadas

```
1. Selecione Post Type customizado
2. Escolha Taxonomia (ex: "Portfolio Type")
3. Marque termos desejados
```

### Dica 3: Otimize Performance

```
Posts por Página: 6-12 (ideal)
Tamanho Imagem: medium (melhor)
Paginação: Ative para muitos posts
```

---

## 📞 Suporte

### Problemas Conhecidos

Nenhum no momento.

### Reportar Bugs

Se encontrar problemas:

1. Ative WP_DEBUG
2. Verifique console do navegador (F12)
3. Capture screenshot
4. Envie:
   - Versão do WordPress
   - Versão do PHP
   - Tema ativo
   - Mensagem de erro completa

---

## ✨ Próximas Atualizações (v1.1.0)

- [ ] Filtro por autor (visual)
- [ ] Busca por palavra-chave
- [ ] Ordenação por views
- [ ] Lazy loading de imagens
- [ ] Skeleton loading
- [ ] Cache inteligente

---

**Atualização bem-sucedida! Aproveite o plugin corrigido! 🎉**
