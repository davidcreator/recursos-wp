# WP Dracaunos Security - Guia de Instalação e Configuração

## 📋 Requisitos

- WordPress 5.8 ou superior
- PHP 7.4 ou superior
- MySQL 5.6 ou superior
- Acesso FTP ou cPanel
- **IMPORTANTE:** Backup completo antes da instalação

## 🚀 Instalação

### Método 1: Via WordPress Admin

1. Faça login no WordPress Admin
2. Vá para **Plugins → Adicionar Novo**
3. Clique em **Enviar Plugin**
4. Selecione o arquivo `wp-dracaunos-security.zip`
5. Clique em **Instalar Agora**
6. Após a instalação, clique em **Ativar Plugin**

### Método 2: Via FTP

1. Descompacte o arquivo `wp-dracaunos-security.zip`
2. Faça upload da pasta `wp-dracaunos-security` para `/wp-content/plugins/`
3. Acesse o WordPress Admin
4. Vá para **Plugins**
5. Ative o **WP Dracaunos Security**

## ⚙️ Configuração Inicial

### 1. Dashboard (Primeira Parada)

Após ativar, vá para **Security Pro → Dashboard** para ver:
- Status de segurança atual
- Estatísticas
- Logs recentes
- Ações rápidas

### 2. Configuração de URLs (CRÍTICO!)

⚠️ **ATENÇÃO:** Esta é a configuração mais importante e perigosa!

1. Vá para **Security Pro → URL Settings**
2. Configure URLs customizadas (ANOTE TODAS!):
   - **Custom Admin URL**: Ex: `meu-painel`
   - **Custom Login URL**: Ex: `entrar`
   - **Custom Theme URL**: Ex: `assets`
   - **Custom Plugins URL**: Ex: `modulos`
   - **Custom Uploads URL**: Ex: `arquivos`

3. **ANTES DE SALVAR:**
   - Salve as novas URLs em um documento de texto
   - Salve em local seguro (ex: gerenciador de senhas)
   - Faça backup do banco de dados
   - Teste em ambiente de desenvolvimento primeiro!

4. Clique em **Save URL Settings**

5. **Após salvar:**
   - Você será deslogado
   - Acesse a nova URL de login: `seusite.com/entrar`
   - Bookmark a nova URL de admin

### 3. Two-Factor Authentication (2FA)

#### Configuração Global:

1. Vá para **Security Pro → 2FA Settings**
2. Marque **Enable 2FA**
3. Selecione métodos disponíveis:
   - ✅ Email Verification (Recomendado para começar)
   - ✅ Authenticator App (Mais seguro)
   - ✅ Backup Recovery Codes (Obrigatório!)

#### Configuração por Usuário:

**Via Admin:**
1. Vá para **Usuários → Seu Perfil**
2. Role até **Two-Factor Authentication**
3. Configure seus métodos preferidos

**Via Front-end (para usuários):**
1. Adicione o shortcode `[wpsp_2fa_settings]` em qualquer página
2. Usuários podem configurar seu próprio 2FA

#### Configurando Email 2FA:

1. No perfil, clique em **Enable** em Email Verification
2. Na próxima tentativa de login, você receberá código por email
3. Digite o código para acessar

#### Configurando Authenticator App:

1. Baixe um app autenticador:
   - Google Authenticator
   - Microsoft Authenticator
   - Authy
   - 1Password

2. No perfil, clique em **Setup Authenticator**
3. Escaneie o QR Code com o app
4. Digite o código de 6 dígitos mostrado no app
5. Clique em **Verificar e Ativar**

#### Gerando Códigos de Recuperação:

1. No perfil, clique em **Generate Backup Codes**
2. Você receberá 10 códigos únicos
3. **SALVE ESTES CÓDIGOS!** Imprima ou guarde em local seguro
4. Cada código pode ser usado apenas uma vez
5. Use se perder acesso ao email ou authenticator

### 4. Google reCAPTCHA

#### Obter Chaves:

1. Acesse: https://www.google.com/recaptcha/admin
2. Registre um novo site
3. Escolha reCAPTCHA v2 ("I'm not a robot")
4. Adicione seu domínio
5. Copie **Site Key** e **Secret Key**

#### Configurar no Plugin:

1. Vá para **Security Pro → Security Settings**
2. Role até seção **Captcha**
3. Cole as chaves
4. Marque onde aplicar:
   - Login
   - Registro
   - Recuperação de senha
   - Comentários

### 5. Security Headers

1. Vá para **Security Pro → Security Settings**
2. **Basic Security:**
   - ✅ Block Default Admin
   - ✅ Block wp-includes
   - ✅ Block wp-content
   - ✅ Block XML-RPC (desmarque se usar app mobile)

3. **Security Headers:**
   - ✅ Enable Security Headers
   - Configure X-Frame-Options: `SAMEORIGIN`
   - Configure Referrer-Policy: `strict-origin-when-cross-origin`

4. **Content Security Policy (Avançado):**
   - ⚠️ Só habilite se souber o que está fazendo
   - Pode quebrar o site se mal configurado
   - Teste em staging primeiro

5. **HSTS (apenas se tiver SSL):**
   - ✅ Enable HSTS
   - Max Age: `31536000` (1 ano)
   - ✅ Include Subdomains
   - ⚠️ Preload: só marque se for registrar no HSTS Preload

### 6. Otimizações

1. Vá para **Security Pro → Optimization**

2. **Header Cleanup (Recomendado):**
   - ✅ Remove WordPress Version
   - ✅ Remove Meta Generator
   - ✅ Disable Emojis
   - ✅ Remove Feed Links (se não usar RSS)
   - ✅ Remove REST API Links (cuidado com Gutenberg)

3. **Minificação (Teste antes!):**
   - Minify HTML (geralmente seguro)
   - Minify CSS (pode causar problemas visuais)
   - Minify JavaScript (pode quebrar funcionalidades)

4. **Após habilitar minificação:**
   - Limpe cache do site
   - Limpe cache do CDN (se usar)
   - Teste todas as páginas
   - Teste formulários
   - Teste checkout (WooCommerce)

## 🔍 Monitoramento

### Logs de Segurança

1. Vá para **Security Pro → Dashboard**
2. Veja seção **Recent Security Logs**
3. Monitore:
   - Tentativas de login falhadas
   - Uso de códigos 2FA
   - Acessos bloqueados
   - IPs suspeitos

### Estatísticas

O dashboard mostra:
- Total de logs
- Logs de hoje
- Logs da semana
- IPs bloqueados
- Sessões ativas
- Usuários com 2FA

## 🚨 Solução de Problemas

### Esqueci a URL de Login

1. Acesse via FTP: `/wp-content/plugins/`
2. Renomeie pasta: `wp-dracaunos-security` para `wp-dracaunos-security-disabled`
3. Acesse `seusite.com/wp-login.php`
4. Após logar, renomeie a pasta de volta
5. Reconfigure as URLs

### Site quebrou após minificação

1. Desative minificação:
   - Via admin: **Security Pro → Optimization**
   - Desmarque todas as opções de minificação
2. Limpe cache
3. Teste qual minificação causa problema
4. Mantenha desabilitada

### Não recebo emails 2FA

1. Verifique se WordPress está enviando emails:
   - Use plugin **WP Mail SMTP**
2. Verifique spam/lixo eletrônico
3. Configure SMTP adequadamente
4. Use authenticator app como alternativa

### Erro 403 ao acessar site

1. Verifique se bloqueou algo importante
2. Acesse via FTP
3. Edite `.htaccess` e remova seção do plugin
4. Reconfigure bloqueios com cuidado

### Perdi acesso ao 2FA

1. Use código de recuperação
2. Se não tiver códigos:
   - Acesse banco de dados (phpMyAdmin)
   - Tabela: `wp_wpsp_two_factor`
   - Delete registros do seu usuário
   - Ou use método de email

## 📱 Uso do 2FA

### Login com 2FA Ativo:

1. Acesse URL de login
2. Digite usuário e senha
3. Sistema detecta 2FA ativo
4. Você recebe/gera código
5. Digite código na próxima tela
6. Acesso liberado

### Opções de Código:

- **Email:** Código enviado para seu email
- **Authenticator:** Código gerado no app
- **Recovery:** Use código de backup

## 🔐 Melhores Práticas

### URLs Customizadas:

✅ Use palavras não óbvias
❌ Evite: `admin`, `login`, `painel`
✅ Prefira: `painel2024`, `acesso-seguro`

### 2FA:

✅ Use authenticator app (mais seguro)
✅ Sempre gere códigos de recuperação
✅ Guarde códigos em local físico seguro
❌ Não compartilhe códigos

### Headers de Segurança:

✅ Habilite todos os básicos
✅ Teste CSP em staging
✅ Use HSTS apenas com SSL válido

### Minificação:

✅ Teste em staging primeiro
✅ Habilite um por vez
✅ Monitore erros JavaScript
❌ Não use em desenvolvimento

## 📞 Suporte

- Site: https://davidalmeida.xyz
- Email: contato@davidalmeida.xyz
- Documentação: https://davidalmeida.xyz/docs/wp-dracaunos-security

## 🔄 Atualizações

O plugin verifica atualizações automaticamente. Para atualizar:

1. Faça backup completo
2. Vá para **Dashboard → Atualizações**
3. Clique em atualizar
4. Teste funcionalidades após atualização

## ⚠️ Avisos Importantes

1. **SEMPRE faça backup antes de qualquer alteração**
2. **Salve URLs customizadas em local seguro**
3. **Teste em ambiente de desenvolvimento primeiro**
4. **CSP pode quebrar o site - use com cautela**
5. **HSTS Preload é irreversível - pense 2x**
6. **Minificação pode causar problemas - teste**
7. **2FA: sempre tenha códigos de recuperação**

---

**Desenvolvido por David Almeida**
**Licença: GPL v2 or later**