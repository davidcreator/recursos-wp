# AdSense Master Pro

Um plugin WordPress completo e profissional para gerenciamento avançado de anúncios Google AdSense e outras redes publicitárias.

## 🚀 Características Principais

### 📊 Gerenciamento de Anúncios
- Interface intuitiva para criar e gerenciar anúncios
- Suporte a múltiplos tipos de anúncios (AdSense, HTML, JavaScript, PHP)
- Sistema de posicionamento flexível (antes/depois do conteúdo, cabeçalho, rodapé, sidebar)
- Controle de exibição por tipo de página (posts, páginas, home, arquivo, categoria)
- Segmentação por dispositivo (desktop, mobile, tablet)

### 🎯 Recursos Avançados
- **Lazy Loading**: Carregamento otimizado de anúncios
- **Auto Ads**: Integração completa com Google AdSense Auto Ads
- **Detecção de Ad Blocker**: Mensagens personalizadas para usuários com bloqueadores
- **Conformidade GDPR**: Sistema de consentimento integrado
- **Rastreamento de Performance**: Estatísticas de impressões e cliques
- **A/B Testing**: Teste diferentes versões de anúncios

### 🛠️ Ferramentas de Gerenciamento
- **Editor ads.txt**: Interface completa para gerenciar arquivo ads.txt
- **Templates Prontos**: Templates para principais redes publicitárias
- **Backup e Restauração**: Sistema de backup automático
- **Validação de Sintaxe**: Verificação automática do formato ads.txt

### 🎨 Widgets e Shortcodes
- Widget simples para sidebar
- Widget avançado com múltiplas opções
- Shortcode `[amp_ad id="X"]` para inserção manual
- Funções PHP para desenvolvedores

## 📋 Requisitos

- WordPress 5.0 ou superior
- PHP 7.4 ou superior
- MySQL 5.6 ou superior

## 🔧 Instalação

1. Faça upload dos arquivos para `/wp-content/plugins/adsense-master-pro/`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Configure o plugin em 'AdSense Master Pro' no menu administrativo

## 📖 Como Usar

### Criando seu Primeiro Anúncio

1. Acesse **AdSense Master Pro > Anúncios**
2. Clique em **"Adicionar Novo Anúncio"**
3. Preencha as informações:
   - **Nome**: Nome identificador do anúncio
   - **Tipo**: Selecione o tipo de anúncio (AdSense, HTML, etc.)
   - **Código**: Cole o código do anúncio
   - **Posição**: Escolha onde exibir o anúncio
   - **Dispositivos**: Selecione em quais dispositivos exibir
   - **Páginas**: Configure em quais tipos de página exibir

### Configurando o AdSense

1. Acesse **AdSense Master Pro > Configurações**
2. Na aba **"AdSense"**:
   - Insira seu **ID do Cliente AdSense** (ca-pub-xxxxxxxxxx)
   - Configure **Auto Ads** se desejar
   - Ative **Lazy Loading** para melhor performance

### Gerenciando ads.txt

1. Acesse **AdSense Master Pro > ads.txt**
2. Use a **"Configuração Rápida"** para adicionar seu ID do AdSense
3. Ou edite manualmente o arquivo usando o editor integrado
4. Utilize os **templates prontos** para outras redes

### Usando Shortcodes

```php
// Exibir anúncio específico
[amp_ad id="1"]

// Exibir anúncio com classe CSS personalizada
[amp_ad id="1" class="minha-classe"]
```

### Usando Funções PHP

```php
// Exibir anúncio em templates
<?php amp_display_ad(1); ?>

// Obter dados do anúncio
<?php $ad = amp_get_ad(1); ?>

// Verificar se anúncio existe
<?php if (amp_get_ad(1)): ?>
    <?php amp_display_ad(1); ?>
<?php endif; ?>
```

## 🎛️ Configurações Disponíveis

### Aba Geral
- **Lazy Loading**: Ativa carregamento sob demanda
- **Detecção de Ad Blocker**: Exibe mensagem para usuários com bloqueadores
- **Conformidade GDPR**: Sistema de consentimento

### Aba AdSense
- **ID do Cliente**: Seu identificador do AdSense
- **Auto Ads**: Ativação dos anúncios automáticos
- **Otimização**: Configurações de performance

### Aba Exibição
- **Exclusões**: Páginas e usuários onde não exibir anúncios
- **CSS Personalizado**: Estilos customizados
- **Responsividade**: Configurações para diferentes dispositivos

### Aba Avançado
- **Cache**: Compatibilidade com plugins de cache
- **Analytics**: Integração com Google Analytics
- **Hooks**: Ganchos para desenvolvedores

### Aba Performance
- **Otimização**: Configurações de velocidade
- **Preload**: Pré-carregamento de recursos
- **Minificação**: Compressão de código

## 🔌 Hooks para Desenvolvedores

### Actions (Ações)
```php
// Antes de exibir um anúncio
do_action('amp_before_ad_display', $ad_id, $ad_data);

// Depois de exibir um anúncio
do_action('amp_after_ad_display', $ad_id, $ad_data);

// Quando um anúncio é clicado
do_action('amp_ad_clicked', $ad_id, $user_data);

// Quando um anúncio é visualizado
do_action('amp_ad_impression', $ad_id, $user_data);
```

### Filters (Filtros)
```php
// Modificar código do anúncio antes da exibição
add_filter('amp_ad_code', function($code, $ad_id) {
    // Sua lógica aqui
    return $code;
}, 10, 2);

// Modificar condições de exibição
add_filter('amp_should_display_ad', function($should_display, $ad_id) {
    // Sua lógica aqui
    return $should_display;
}, 10, 2);

// Modificar HTML do container do anúncio
add_filter('amp_ad_container_html', function($html, $ad_id) {
    // Sua lógica aqui
    return $html;
}, 10, 2);
```

## 📊 Estrutura do Banco de Dados

### Tabela: wp_amp_ads
```sql
CREATE TABLE wp_amp_ads (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    type varchar(50) NOT NULL,
    code text NOT NULL,
    position varchar(50) NOT NULL,
    device varchar(50) NOT NULL,
    page_types text,
    status varchar(20) DEFAULT 'active',
    impressions int(11) DEFAULT 0,
    clicks int(11) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

### Opções do WordPress
- `amp_options`: Configurações gerais do plugin
- `amp_adsense_options`: Configurações específicas do AdSense
- `amp_display_options`: Configurações de exibição
- `amp_advanced_options`: Configurações avançadas
- `amp_performance_options`: Configurações de performance

## 🛡️ Segurança

- Validação e sanitização de todos os dados de entrada
- Verificação de nonce em formulários
- Escape de saída para prevenir XSS
- Verificação de permissões de usuário
- Prevenção de acesso direto aos arquivos

## 🔄 Atualizações

O plugin verifica automaticamente por atualizações. Para atualizar manualmente:

1. Faça backup do site
2. Desative o plugin
3. Substitua os arquivos
4. Reative o plugin

## 🐛 Solução de Problemas

### Anúncios não aparecem
1. Verifique se o anúncio está ativo
2. Confirme as configurações de exibição
3. Verifique se não há conflitos com cache
4. Teste em modo de navegação anônima

### Arquivo ads.txt não funciona
1. Verifique permissões de escrita
2. Confirme se não há redirecionamentos
3. Teste o acesso direto: `seusite.com/ads.txt`
4. Verifique configurações do servidor

### Performance lenta
1. Ative o Lazy Loading
2. Configure cache adequadamente
3. Otimize o número de anúncios por página
4. Use CDN se disponível

## 📞 Suporte

Para suporte técnico:
- Documentação: [Link da documentação]
- Fórum: [Link do fórum]
- Email: [email de suporte]

## 📄 Licença

Este plugin é licenciado sob GPL v2 ou posterior.

## 🔄 Changelog

### Versão 1.0.0
- Lançamento inicial
- Sistema completo de gerenciamento de anúncios
- Interface administrativa intuitiva
- Suporte a múltiplos tipos de anúncios
- Sistema de widgets e shortcodes
- Gerenciador de ads.txt integrado
- Sistema de rastreamento de performance
- Conformidade GDPR
- Otimizações de performance

---

**AdSense Master Pro** - A solução completa para monetização do seu site WordPress.