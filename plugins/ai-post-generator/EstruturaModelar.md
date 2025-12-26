# 📋 Guia de Instalação - Plugin Modular

## ✅ Arquivos Criados Até Agora

```
plugins/ai-post-generator/
├── ✅ ai-post-generator.php              (Principal - 50 linhas)
└── ✅ includes/
    ├── ✅ class-ai-post-generator.php    (Core - 400 linhas)
    ├── ✅ class-content-generator.php    (IA Texto - 250 linhas)
    └── ✅ class-image-generator.php      (IA Imagens - 400 linhas)
```

---

## 🚀 Passo a Passo de Instalação

### **Passo 1: Backup**
```bash
# Faça backup do arquivo atual
cp ai-post-generator.php ai-post-generator-backup-$(date +%Y%m%d).php
```

### **Passo 2: Criar Estrutura de Pastas**
```bash
cd wp-content/plugins/ai-post-generator/
mkdir -p includes/pages
```

### **Passo 3: Substituir Arquivo Principal**

**Copie** o conteúdo do artifact **"ai-post-generator.php (Principal)"** e:
1. Apague o conteúdo do seu arquivo atual `ai-post-generator.php`
2. Cole o novo conteúdo
3. Salve

### **Passo 4: Criar Classes**

**Arquivo 1:** `includes/class-ai-post-generator.php`
- Copie do artifact **"includes/class-ai-post-generator.php"**
- Crie o arquivo e cole o conteúdo

**Arquivo 2:** `includes/class-content-generator.php`
- Copie do artifact **"includes/class-content-generator.php"**
- Crie o arquivo e cole o conteúdo

**Arquivo 3:** `includes/class-image-generator.php`
- Copie do artifact **"includes/class-image-generator.php"**
- Crie o arquivo e cole o conteúdo

---

## 📁 Estrutura Final Completa

```
plugins/ai-post-generator/
├── ai-post-generator.php
├── includes/
│   ├── class-ai-post-generator.php
│   ├── class-content-generator.php
│   ├── class-image-generator.php
│   └── pages/
│       ├── admin-page.php          (⏳ Próximo)
│       ├── settings-page.php       (⏳ Próximo)
│       ├── image-manager-page.php  (⏳ Próximo)
│       └── editor-meta-box.php     (⏳ Próximo)
├── assets/
│   ├── admin-script.js             (✅ Você já tem)
│   ├── admin-style.css             (✅ Você já tem)
│   ├── editor-script.js            (✅ Você já tem)
│   └── editor-style.css            (✅ Você já tem)
└── README.md
```

---

## ✅ Teste Rápido

Depois de criar os 3 arquivos:

1. **Desative o plugin** no WordPress
2. **Ative novamente**
3. **Vá em:** WordPress Admin → AI Posts → Gerar Post
4. **Se não houver erro** = Está funcionando! ✅

---

## 🔧 Próximos Arquivos que Preciso Criar

Agora falta criar as **páginas HTML** (views):

1. ⏳ `includes/pages/admin-page.php` - Página principal
2. ⏳ `includes/pages/settings-page.php` - Configurações
3. ⏳ `includes/pages/image-manager-page.php` - Gerenciador de imagens
4. ⏳ `includes/pages/editor-meta-box.php` - Meta box do editor

---

## 💡 Quer que eu continue?

**Me diga:**
- [ ] "Continue" - Vou criar as páginas HTML
- [ ] "Aguarde" - Vou testar primeiro
- [ ] "Tenho erro X" - Me mostre o erro e eu corrijo

**Se escolher "Continue", vou criar:**
1. Página principal de geração de posts
2. Página de configurações
3. Gerenciador de imagens
4. Meta box do editor

Tudo otimizado e sem código duplicado! 🚀