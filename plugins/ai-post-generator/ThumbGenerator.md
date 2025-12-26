# 🔧 Solução de Problemas - Geração de Imagens

## 🚨 Problemas Comuns e Soluções

### **Problema 1: "Imagem não está sendo gerada"**

#### ✅ **Soluções:**

**1. Verificar Provedor Configurado:**
```
WordPress → AI Posts → Configurações
↓
Role até "Configurações de Imagens"
↓
Provedor selecionado: [____]
```

**Se estiver vazio ou "não configurado":**
- Selecione **Pollinations AI** (não precisa de API key!)
- Clique em **Salvar Alterações**

---

**2. Verificar Dimensões:**
```
Largura: [1920] px
Altura: [1080] px
```

**Se estiverem 0 ou vazias:**
- Clique no botão **[Full HD (1920×1080)]**
- Ou digite manualmente: 1920 e 1080
- Clique em **Salvar Alterações**

---

**3. Verificar API Key (se não for Pollinations):**

| Provedor | Precisa API Key? | Onde obter |
|----------|------------------|------------|
| Pollinations | ❌ NÃO | - |
| Pixabay | ✅ SIM | pixabay.com/api |
| Pexels | ✅ SIM | pexels.com/api |
| Unsplash | ✅ SIM | unsplash.com/developers |
| DALL-E | ✅ SIM (OpenAI) | platform.openai.com |
| Stability | ✅ SIM | platform.stability.ai |

---

### **Problema 2: "Erro ao fazer download da imagem"**

#### ✅ **Soluções:**

**1. Verificar Permissões da Pasta Uploads:**
```bash
# Via SSH
cd /caminho/para/wordpress/wp-content/uploads
ls -la

# Deve mostrar: drwxr-xr-x (755)
# Se estiver diferente:
chmod 755 /wp-content/uploads
chmod 755 /wp-content/uploads/2024
chmod 755 /wp-content/uploads/2024/12
```

**2. Verificar se cURL está instalado:**
```php
<?php
// Crie um arquivo test-curl.php na raiz do WordPress
if (function_exists('curl_init')) {
    echo "✅ cURL está instalado!";
    
    // Testa conexão
    $ch = curl_init('https://image.pollinations.ai/prompt/test?width=100&height=100');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    echo "<br>Status: " . $info['http_code'];
    echo "<br>Tamanho baixado: " . strlen($result) . " bytes";
} else {
    echo "❌ cURL NÃO está instalado!";
    echo "<br>Contate seu provedor de hospedagem.";
}
?>
```

**3. Verificar Firewall/Bloqueio:**

Alguns servidores bloqueiam conexões externas. Teste:
```bash
# Via SSH
curl -I https://image.pollinations.ai/prompt/test

# Deve retornar: HTTP/2 200
# Se retornar erro ou timeout, há bloqueio
```

---

### **Problema 3: "Imagem muito pequena ou corrompida"**

#### ✅ **Soluções:**

**1. Aumentar Timeout:**

Adicione no `wp-config.php`:
```php
define('WP_HTTP_BLOCK_EXTERNAL', false);
define('WP_ACCESSIBLE_HOSTS', 'image.pollinations.ai,api.pexels.com,api.unsplash.com');
```

**2. Aumentar Limites de Upload:**

No `.htaccess` ou `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

**3. Testar Manualmente:**

Abra esta URL no navegador:
```
https://image.pollinations.ai/prompt/beautiful%20landscape?width=1920&height=1080&nologo=true
```

Se a imagem carregar, o problema é no servidor WordPress.

---

### **Problema 4: "API Key inválida"**

#### ✅ **Soluções para cada provedor:**

**Pixabay:**
```
1. Acesse: https://pixabay.com/api/docs/
2. Faça login
3. Vá em "API Search"
4. Copie a chave que aparece em amarelo
5. Cole EXATAMENTE como está (sem espaços)
```

**Pexels:**
```
1. Acesse: https://www.pexels.com/api/
2. Clique em "Get Started"
3. Preencha o formulário
4. Copie a API Key do email
5. Cole nas configurações
```

**Unsplash:**
```
1. Acesse: https://unsplash.com/oauth/applications
2. Crie um "New Application"
3. Copie o "Access Key" (não o Secret!)
4. Cole nas configurações
```

---

### **Problema 5: "Imagem não aparece como destaque"**

#### ✅ **Soluções:**

**1. Verificar se o tema suporta:**
```php
// Adicione no functions.php do tema:
add_theme_support('post-thumbnails');
```

**2. Verificar metadados:**

No editor do post, veja na barra lateral se há "Imagem Destacada".

**3. Forçar atualização:**
```php
// Cole no functions.php temporariamente:
add_action('init', function() {
    global $wpdb;
    $posts = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post'");
    foreach ($posts as $post) {
        $thumb_id = get_post_thumbnail_id($post->ID);
        if ($thumb_id) {
            wp_update_post(array('ID' => $post->ID));
        }
    }
});
```

---

## 🔍 Ferramenta de Diagnóstico

### **Use a ferramenta automática:**

1. **Baixe** o arquivo `diagnostic-images.php` (fornecido anteriormente)
2. **Faça upload** para: `/wp-content/plugins/ai-post-generator/`
3. **Acesse**: `http://seusite.com/wp-content/plugins/ai-post-generator/diagnostic-images.php`
4. **Veja os resultados** e siga as recomendações
5. **DELETE** o arquivo após usar!

A ferramenta verifica:
- ✅ Configurações atuais
- ✅ Permissões de pastas
- ✅ Extensões PHP necessárias
- ✅ Conectividade com APIs
- ✅ Logs de erro recentes

---

## 📋 Checklist de Verificação

Use esta lista para identificar o problema:

```
┌────────────────────────────────────────────┐
│ ☐ Provedor configurado                     │
│ ☐ Dimensões definidas (ex: 1920×1080)     │
│ ☐ API Key configurada (se necessário)     │
│ ☐ Pasta uploads com permissão 755         │
│ ☐ cURL instalado no servidor              │
│ ☐ GD Library instalada                    │
│ ☐ Firewall não bloqueia conexões          │
│ ☐ WP_DEBUG ativado para ver erros         │
│ ☐ Tema suporta post-thumbnails            │
│ ☐ Memória PHP suficiente (mín. 128M)      │
└────────────────────────────────────────────┘
```

---

## 🧪 Testes Manuais

### **Teste 1: Download Direto**

```php
<?php
// Salve como test-download.php na raiz
$url = 'https://image.pollinations.ai/prompt/test?width=800&height=600';
$tmp = download_url($url, 30);

if (is_wp_error($tmp)) {
    echo "❌ Erro: " . $tmp->get_error_message();
} else {
    echo "✅ Sucesso! Arquivo: " . $tmp;
    echo "<br>Tamanho: " . filesize($tmp) . " bytes";
    @unlink($tmp);
}
?>
```

### **Teste 2: Geração via AJAX**

Abra o Console do navegador (F12) e execute:
```javascript
fetch('/wp-admin/admin-ajax.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({
        action: 'aipg_generate_image',
        nonce: 'SEU_NONCE_AQUI',
        topic: 'teste',
        post_id: 0
    })
})
.then(r => r.json())
.then(console.log)
```

---

## 📊 Logs de Debug

### **Ativar Logs:**

No `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

### **Ver Logs:**

Os logs ficam em: `/wp-content/debug.log`

**Procure por linhas com "AIPG":**
```bash
# Via SSH
tail -f /caminho/para/wordpress/wp-content/debug.log | grep AIPG

# Ou baixe o arquivo via FTP e abra no editor
```

**Logs úteis:**
```
AIPG: Iniciando geração de imagem para: [tópico]
AIPG Pollinations: URL gerada: [url]
AIPG Download: Arquivo temporário criado: [arquivo]
AIPG Download: Tamanho do arquivo: [bytes]
AIPG Download: Sucesso! Attachment ID: [id]
```

---

## 🆘 Soluções Rápidas

### **Solução 1: Use Pollinations (Mais Fácil)**
```
1. Configurações → Provedor: Pollinations AI
2. Dimensões: 1920×1080
3. Salvar
4. Pronto! Não precisa de API key
```

### **Solução 2: Desative Temporariamente**
```
1. Desmarque "Gerar imagem destacada"
2. Gere apenas o texto
3. Adicione imagem manualmente depois
```

### **Solução 3: Aumente Timeout**
```php
// No wp-config.php
define('WP_HTTP_TIMEOUT', 60);
```

### **Solução 4: Teste Outro Provedor**
```
Se Pollinations não funcionar:
→ Tente Pixabay (5000 req/hora)
→ Ou Pexels (200 req/hora)
```

---

## 📞 Suporte Adicional

### **Informações para Suporte:**

Se precisar de ajuda, forneça:
```
1. Provedor configurado: [____]
2. Dimensões: [____] x [____]
3. Mensagem de erro exata: [____]
4. Última linha do log AIPG: [____]
5. Versão PHP: [____]
6. Hospedagem: [____]
```

### **Teste de Conectividade:**
```bash
# Execute no servidor
curl -v https://image.pollinations.ai/prompt/test

# Deve retornar: HTTP/2 200
```

---

## ✅ Configuração Garantida

**Se NADA funcionar, use esta configuração infalível:**

```
1. Provedor: Pollinations AI ✅
2. Dimensões: 1280×720 (menor, mais rápido)
3. Sem API key necessária
4. Teste com tópico simples: "natureza"
```

**Esta combinação funciona em 99% dos casos!**

---

**Última atualização:** Dezembro 2024  
**Versão:** 2.2.1