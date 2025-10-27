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
- [ ] Posts aparecem no frontend
- [ ] Estilos carregados corretamente
- [ ] Slider funciona (se usar layout slider)

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