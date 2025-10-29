# WP Dracaunos Security - Checklist de Testes

## ✅ Pré-Instalação

- [ ] Backup completo do site criado
- [ ] Backup do banco de dados criado
- [ ] Ambiente de teste disponível (staging)
- [ ] Acesso FTP/SSH disponível
- [ ] Acesso ao banco de dados (phpMyAdmin)

## ✅ Instalação

- [ ] Plugin instalado via ZIP ou FTP
- [ ] Plugin ativado com sucesso
- [ ] Sem erros PHP no error_log
- [ ] Tabelas criadas no banco:
  - [ ] wp_wpsp_two_factor
  - [ ] wp_wpsp_security_logs
  - [ ] wp_wpsp_sessions
  - [ ] wp_wpsp_blocked_ips
- [ ] Opções padrão criadas
- [ ] Regras .htaccess adicionadas

## ✅ Dashboard

- [ ] Acesso ao menu Security Pro
- [ ] Dashboard carrega sem erros
- [ ] Estatísticas exibidas corretamente
- [ ] Status de segurança mostrado
- [ ] Logs recentes visíveis (se houver)
- [ ] Ações rápidas funcionando

## ✅ Configuração de URLs

### Testes Básicos
- [ ] Página de configuração carrega
- [ ] Formulário aceita entrada
- [ ] Validação de campos funciona
- [ ] Mensagem de aviso exibida

### Custom Login URL
- [ ] URL customizada configurada
- [ ] Salvamento bem-sucedido
- [ ] Nova URL funciona: `seusite.com/sua-url-login`
- [ ] URL antiga bloqueada: `seusite.com/wp-login.php`
- [ ] Redirecionamento funciona
- [ ] Login via nova URL funciona
- [ ] Logout funciona
- [ ] Recuperação de senha funciona

### Custom Admin URL
- [ ] URL customizada configurada
- [ ] Nova URL funciona: `seusite.com/sua-url-admin`
- [ ] URL antiga bloqueada: `seusite.com/wp-admin`
- [ ] Admin funciona normalmente
- [ ] Ajax funciona
- [ ] Media upload funciona
- [ ] Plugins funcionam

### Custom Theme URL
- [ ] URL configurada
- [ ] CSS carrega corretamente
- [ ] JavaScript carrega
- [ ] Imagens carregam
- [ ] Fonts carregam
- [ ] Layout não quebrou

### Custom Plugins URL
- [ ] URL configurada
- [ ] Plugins funcionam
- [ ] Scripts de plugins carregam
- [ ] Estilos de plugins carregam
- [ ] Ajax de plugins funciona

### Custom Uploads URL
- [ ] URL configurada
- [ ] Upload de mídia funciona
- [ ] Imagens exibidas corretamente
- [ ] Biblioteca de mídia funciona
- [ ] Editor funciona

## ✅ Two-Factor Authentication

### Configuração Global
- [ ] Página de configuração carrega
- [ ] 2FA pode ser habilitado
- [ ] Métodos podem ser selecionados
- [ ] Salvamento funciona

### Email Verification
- [ ] Pode ser habilitado no perfil
- [ ] Email é enviado ao login
- [ ] Código funciona quando correto
- [ ] Código rejeitado quando incorreto
- [ ] Código expira após 5 minutos
- [ ] Reenvio de código funciona

### Authenticator App
- [ ] Setup pode ser iniciado
- [ ] QR Code é gerado
- [ ] Secret é exibido
- [ ] QR Code funciona com Google Authenticator
- [ ] QR Code funciona com Microsoft Authenticator
- [ ] QR Code funciona com Authy
- [ ] Código de 6 dígitos validado corretamente
- [ ] Setup completa com sucesso
- [ ] Login com authenticator funciona

### Recovery Codes
- [ ] Códigos podem ser gerados
- [ ] 10 códigos criados
- [ ] Códigos são únicos
- [ ] Códigos funcionam no login
- [ ] Código usado é removido
- [ ] Contagem atualizada
- [ ] Regeneração funciona

### Interface de Usuário
- [ ] Seção no perfil exibida
- [ ] Status mostrado corretamente
- [ ] Botões funcionam
- [ ] Ajax funciona
- [ ] Mensagens exibidas
- [ ] Shortcode funciona: `[wpsp_2fa_settings]`

## ✅ Google reCAPTCHA

### Configuração
- [ ] Chaves podem ser inseridas
- [ ] Salvamento funciona
- [ ] Opções de localização salvas

### Funcionalidade
- [ ] Captcha aparece no login
- [ ] Captcha aparece no registro
- [ ] Captcha aparece em recuperação de senha
- [ ] Captcha aparece em comentários
- [ ] Validação funciona
- [ ] Bloqueio funciona quando falha
- [ ] Script Google carrega

## ✅ Security Headers

### Basic Security
- [ ] Bloqueio de admin padrão funciona
- [ ] Bloqueio wp-includes funciona
- [ ] Bloqueio wp-content funciona
- [ ] XML-RPC bloqueado (se habilitado)

### Headers HTTP
- [ ] X-Content-Type-Options presente
- [ ] X-Frame-Options presente
- [ ] X-XSS-Protection presente
- [ ] Referrer-Policy presente
- [ ] Headers visíveis no DevTools

### CSP (Se habilitado)
- [ ] Header CSP presente
- [ ] Site não quebrou
- [ ] JavaScript funciona
- [ ] CSS funciona
- [ ] Imagens carregam
- [ ] Inline scripts funcionam (se permitido)

### HSTS (Se habilitado)
- [ ] Header HSTS presente
- [ ] Redirect para HTTPS funciona
- [ ] Subdomínios incluídos (se marcado)

## ✅ Otimizações

### Header Cleanup
- [ ] Versão WordPress removida
- [ ] Generator removido
- [ ] Emojis desabilitados
- [ ] Feed links removidos (se habilitado)
- [ ] REST API links removidos (se habilitado)
- [ ] oEmbed removido (se habilitado)

### Minificação HTML
- [ ] HTML minificado
- [ ] Site não quebrou
- [ ] Layout preservado
- [ ] Formulários funcionam
- [ ] JavaScript funciona

### Minificação CSS
- [ ] CSS inline minificado
- [ ] Estilos aplicados corretamente
- [ ] Layout não quebrou
- [ ] Responsivo funciona
- [ ] Animações funcionam

### Minificação JavaScript
- [ ] JS inline minificado
- [ ] Funcionalidades preservadas
- [ ] Console sem erros
- [ ] Ajax funciona
- [ ] Eventos funcionam

## ✅ Logs de Segurança

- [ ] Logs são criados
- [ ] Timestamps corretos
- [ ] IPs registrados
- [ ] User agents registrados
- [ ] Ações logadas corretamente
- [ ] Dashboard mostra logs
- [ ] Logs podem ser filtrados
- [ ] Limpeza de logs funciona

## ✅ Bloqueio de IPs

- [ ] IP pode ser bloqueado
- [ ] Bloqueio temporário funciona
- [ ] Bloqueio permanente funciona
- [ ] IP bloqueado não acessa
- [ ] Desbloqueio funciona
- [ ] Expiração automática funciona
- [ ] Lista de IPs mostrada

## ✅ Sessões

- [ ] Sessões criadas no login
- [ ] Tokens gerados
- [ ] Expiração funciona
- [ ] Logout remove sessão
- [ ] Múltiplas sessões funcionam
- [ ] Limpeza de sessões expiradas funciona

## ✅ Compatibilidade

### WordPress
- [ ] WordPress 5.8+ funciona
- [ ] Gutenberg funciona
- [ ] Classic Editor funciona
- [ ] Media Library funciona
- [ ] Customizer funciona
- [ ] Widgets funcionam

### Temas
- [ ] Tema ativo funciona
- [ ] Child theme funciona
- [ ] Theme Customizer funciona
- [ ] Menu funciona
- [ ] Widgets funcionam

### Plugins Comuns
- [ ] WooCommerce funciona
- [ ] Contact Form 7 funciona
- [ ] Yoast SEO funciona
- [ ] Elementor funciona
- [ ] WPBakery funciona
- [ ] Cache plugins funcionam

### Browsers
- [ ] Chrome funciona
- [ ] Firefox funciona
- [ ] Safari funciona
- [ ] Edge funciona
- [ ] Mobile browsers funcionam

## ✅ Performance

- [ ] Tempo de carregamento aceitável
- [ ] Queries otimizadas
- [ ] Cache funciona
- [ ] CDN funciona (se usado)
- [ ] PageSpeed score mantido/melhorado

## ✅ Segurança

- [ ] Site não detectado como WordPress (teste em: https://builtwith.com)
- [ ] Diretórios não indexados
- [ ] Arquivos PHP bloqueados
- [ ] XML-RPC bloqueado
- [ ] Login protegido com 2FA
- [ ] Headers de segurança presentes
- [ ] Informações sensíveis ocultas

## ✅ Recuperação de Desastres

### Teste de Recuperação
- [ ] Desative o plugin via FTP
- [ ] Acesso ao site restaurado
- [ ] Reative o plugin
- [ ] Tudo funciona novamente

### Backup e Restore
- [ ] Backup funciona
- [ ] Restore funciona
- [ ] Configurações preservadas
- [ ] 2FA preservado

## ✅ Documentação

- [ ] README.md presente
- [ ] INSTALLATION.md presente
- [ ] TESTING_CHECKLIST.md presente
- [ ] Comentários no código adequados
- [ ] readme.txt WordPress presente

## ✅ Testes Finais

- [ ] Site em produção funciona
- [ ] Todos os usuários podem acessar
- [ ] 2FA funciona para todos
- [ ] Nenhum erro no console
- [ ] Nenhum erro no PHP log
- [ ] Performance aceitável
- [ ] SEO não afetado negativamente
- [ ] Analytics funcionando
- [ ] Conversões funcionando

## 🐛 Problemas Encontrados

| Problema | Severidade | Status | Solução |
|----------|-----------|--------|---------|
| | | | |
| | | | |

## 📝 Notas Adicionais

- Data do teste: _______________
- Versão testada: 1.0.0
- WordPress version: _______________
- PHP version: _______________
- Tema: _______________
- Plugins ativos: _______________

---

**Testado por:** _______________
**Data:** _______________
**Aprovado:** [ ] Sim [ ] Não