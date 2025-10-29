# WP Dracaunos Security
Contributors: davidalmeida
Tags: security, 2fa, two-factor, hide wordpress, url customization
Requires at least: 5.8
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin completo de segurança WordPress com personalização de URLs, 2FA, captcha e otimizações.

== Description ==

WP Dracaunos Security é um plugin completo de segurança para WordPress que oferece múltiplas camadas de proteção:

**Recursos Principais:**

* **Personalização de URLs** - Oculte caminhos padrão do WordPress
* **Autenticação de Dois Fatores (2FA)**
  * Email Verification
  * Authenticator App (TOTP)
  * Códigos de Recuperação
* **Google reCAPTCHA** - Proteção contra bots
* **Security Headers** - CSP, HSTS, X-Frame-Options, etc.
* **Bloqueio XML-RPC** - Previne ataques de força bruta
* **Limpeza de Headers** - Remove informações que revelam WordPress
* **Minificação** - HTML, CSS e JavaScript
* **Logs de Segurança** - Monitore atividades suspeitas
* **Bloqueio de IPs** - Bloqueie IPs maliciosos
* **Gerenciamento de Sessões** - Controle sessões ativas

**Oculte seu WordPress:**

* URL de Admin customizada
* URL de Login customizada
* Caminho de temas customizado
* Caminho de plugins customizado
* Caminho de uploads customizado
* Caminho XML-RPC customizado

**Headers de Segurança:**

* Content-Security-Policy (CSP)
* HTTP Strict Transport Security (HSTS)
* X-Frame-Options
* X-Content-Type-Options
* Referrer-Policy
* Permissions-Policy

**Otimizações:**

* Remover versão do WordPress
* Desabilitar emojis
* Remover feeds RSS (opcional)
* Remover REST API links (opcional)
* Minificação de HTML, CSS e JS

== Installation ==

1. Faça upload do plugin para o diretório `/wp-content/plugins/wp-dracaunos-security/`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Vá para Security Pro no menu admin para configurar

**IMPORTANTE:** Após ativar, vá imediatamente para as configurações de URL e salve suas novas URLs em local seguro!

== Frequently Asked Questions ==

= O que acontece se eu esquecer minha URL de login customizada? =

Você precisará desativar o plugin via FTP ou banco de dados. Por isso é ESSENCIAL salvar suas URLs customizadas!

= O plugin funciona com cache? =

Sim, mas você deve limpar o cache após alterar configurações de minificação ou URLs.

= É compatível com WooCommerce? =

Sim, o plugin é totalmente compatível com WooCommerce e outros plugins populares.

= O 2FA é obrigatório? =

Não, os usuários podem escolher ativar ou não em seus perfis. Administradores podem incentivar o uso.

= Posso usar com outros plugins de segurança? =

Sim, mas evite duplicação de funcionalidades (ex: dois plugins de 2FA).

== Screenshots ==

1. Dashboard principal com estatísticas de segurança
2. Configurações de URLs customizadas
3. Configurações de 2FA
4. Security Headers avançados
5. Otimizações e limpeza de headers

== Changelog ==

= 1.0.0 =
* Lançamento inicial
* Personalização completa de URLs
* Sistema 2FA com Email, Authenticator e Recovery Codes
* Google reCAPTCHA v2
* Security Headers avançados
* Sistema de logs de segurança
* Bloqueio de IPs
* Gerenciamento de sessões
* Minificação de HTML, CSS e JS
* Limpeza de headers WordPress
* Bloqueio XML-RPC

== Upgrade Notice ==

= 1.0.0 =
Versão inicial. Salve suas URLs customizadas antes de ativar!

== Support ==

Para suporte, visite: https://davidalmeida.xyz/support