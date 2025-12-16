# AI Post Generator Pro - Plugin WordPress

Plugin profissional para geração automática de posts usando Inteligência Artificial (OpenAI GPT e Anthropic Claude).

## 🚀 Recursos Principais

### ✨ Geração de Conteúdo
- **Múltiplas APIs de IA**: OpenAI GPT-4 e Anthropic Claude
- **Personalização completa**: Tom, tamanho, idioma e estilo
- **Formato JSON estruturado**: Resposta organizada da IA
- **6 tons diferentes**: Profissional, Casual, Técnico, Amigável, Educacional, Persuasivo
- **4 tamanhos**: Curto (300-500), Médio (500-800), Longo (800-1200), Muito Longo (1200-2000)
- **3 idiomas**: Português BR, Inglês, Espanhol

### 🖼️ Imagens Automáticas
- Integração com **Unsplash API**
- Geração automática de imagem destacada
- Download e configuração automática no post
- Busca por palavras-chave relacionadas

### 🏷️ SEO e Tags
- **Otimização SEO automática**:
  - Meta description (máx 160 caracteres)
  - Título SEO otimizado (máx 60 caracteres)
  - Compatível com Yoast SEO
- **Geração automática de tags**: 5-8 tags relevantes por post
- **Links internos**: Adiciona links para posts relacionados

### 📅 Agendamento
- **Agendar publicação**: Defina data e hora exatas
- **Processamento automático**: Posts gerados no horário agendado
- **Painel de gerenciamento**: Visualize e cancele agendamentos
- **Sistema de cron jobs**: Usa WordPress Cron API

### 📋 Templates Reutilizáveis
- **Salvar configurações**: Crie templates personalizados
- **Uso rápido**: Aplique templates com um clique
- **Gerenciamento**: Edite e exclua templates
- **Biblioteca visual**: Grid organizado de templates

### 📊 Histórico e Monitoramento
- **Rastreamento completo**: Todos os posts gerados
- **Indicador visual**: Coluna "Gerado por IA" na lista de posts
- **Metadados**: Data de geração e configurações usadas
- **Estatísticas**: Visualize padrões de uso

## 📦 Instalação

### Requisitos
- WordPress 6.0 ou superior
- PHP 7.4 ou superior
- MySQL 5.6 ou superior

### Passos

1. **Faça upload dos arquivos**:
```
wp-content/plugins/ai-post-generator/
├── ai-post-generator.php
├── assets/
│   ├── admin-style.css
│   └── admin-script.js
└── README.md
```

2. **Ative o plugin** no painel do WordPress (Plugins → Plugins Instalados)

3. **Configure as chaves de API** (AI Posts → Configurações)

## 🔑 Configuração de APIs

### OpenAI (ChatGPT)
1. Acesse: https://platform.openai.com/api-keys
2. Crie uma nova chave de API
3. Cole em **Configurações → Chave API OpenAI**
4. Modelo usado: `gpt-4o-mini`

### Anthropic (Claude)
1. Acesse: https://console.anthropic.com/
2. Crie uma conta e gere uma API Key
3. Cole em **Configurações → Chave API Anthropic**
4. Modelo usado: `claude-3-5-sonnet-20241022`

### Unsplash (Imagens)
1. Acesse: https://unsplash.com/developers
2. Crie um aplicativo
3. Copie a Access Key
4. Cole em **Configurações → Chave API Unsplash**

## 📖 Como Usar

### Gerar um Post Simples

1. Vá em **AI Posts → Gerar Post**
2. Preencha o **Tópico/Assunto**
3. Ajuste as configurações (opcional):
   - Tom do post
   - Tamanho
   - Idioma
   - Categoria
4. Clique em **Gerar Post**
5. Aguarde a geração (15-60 segundos)
6. Edite ou publique o post gerado

### Usar Templates

1. Configure um post como desejado
2. Clique em **Salvar como Template**
3. Dê um nome ao template
4. Para usar: Selecione o template no campo **Template**

### Agendar Posts

1. Marque **Agendar publicação**
2. Selecione data e hora
3. Clique em **Gerar Post**
4. O post será criado automaticamente no horário definido

### Recursos Avançados

- ☑️ **Gerar imagem destacada**: Imagem relacionada ao tópico
- ☑️ **Gerar tags automaticamente**: Tags SEO relevantes
- ☑️ **Otimizar para SEO**: Meta tags otimizadas
- ☑️ **Adicionar links internos**: Links para posts relacionados

## 🎨 Estrutura do Banco de Dados

O plugin cria uma tabela adicional:

```sql
wp_aipg_scheduled
├── id (bigint)
├── topic (varchar 255)
├── config (text)
├── schedule_date (datetime)
├── status (varchar 20)
├── post_id (bigint)
└── created_at (datetime)
```

## 🔒 Segurança

- ✅ Validação de nonces em todas as requisições Ajax
- ✅ Verificação de capabilities (manage_options, publish_posts)
- ✅ Sanitização de inputs (sanitize_text_field, intval)
- ✅ Escape de outputs (esc_html, esc_attr, esc_url)
- ✅ Prepared statements no banco de dados
- ✅ Chaves de API armazenadas de forma segura

## 🎯 Boas Práticas Implementadas

### WordPress Coding Standards
- ✅ Nomenclatura consistente (prefixo `aipg_`)
- ✅ Hooks e filtros apropriados
- ✅ Internacionalização (i18n) completa
- ✅ Sanitização e validação de dados
- ✅ Uso de WordPress APIs nativas

### Programação Orientada a Objetos
- ✅ Singleton pattern para classe principal
- ✅ Separação de responsabilidades
- ✅ Métodos privados para lógica interna
- ✅ Encapsulamento adequado

### Performance
- ✅ Enqueue condicional de scripts/estilos
- ✅ Timeout adequado para APIs (90-120s)
- ✅ Cache de configurações
- ✅ Lazy loading de recursos

### UX/UI
- ✅ Interface intuitiva e responsiva
- ✅ Feedback visual (loading, success, error)
- ✅ Confirmações antes de ações destrutivas
- ✅ Atalhos de teclado (Ctrl+Enter para submeter)

## 📱 Responsividade

O plugin é totalmente responsivo e funciona perfeitamente em:
- 🖥️ Desktop (1920px+)
- 💻 Laptop (1366px - 1920px)
- 📱 Tablet (768px - 1365px)
- 📱 Mobile (< 768px)

## 🌍 Internacionalização

O plugin está pronto para tradução:
- Text Domain: `ai-post-generator`
- Domain Path: `/languages`
- Todas as strings são traduzíveis

### Adicionar Tradução

1. Use o Poedit ou Loco Translate
2. Crie arquivo `.po` para seu idioma
3. Salve em `/languages/ai-post-generator-{locale}.mo`

## 🔧 Personalização

### Adicionar Novos Provedores de IA

```php
// No arquivo principal
private function generate_with_custom_provider($prompt) {
    $api_key = get_option('aipg_custom_key');
    
    $response = wp_remote_post('https://api.custom.com/v1/generate', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode(array(
            'prompt' => $prompt
        ))
    ));
    
    // Processar resposta...
}
```

### Adicionar Novos Campos

```php
// Hook para adicionar campo personalizado
add_action('aipg_form_fields', 'add_custom_field');
function add_custom_field() {
    ?>
    <tr>
        <th scope="row">
            <label>Meu Campo</label>
        </th>
        <td>
            <input type="text" name="custom_field">
        </td>
    </tr>
    <?php
}
```

## 🐛 Solução de Problemas

### Erro: "Chave API não configurada"
**Solução**: Configure as chaves de API em Configurações

### Erro: "Timeout"
**Solução**: Aumente o timeout do servidor ou use conteúdo menor

### Posts não aparecem
**Solução**: Verifique o status do post (rascunho/publicado)

### Imagens não são geradas
**Solução**: Verifique a chave Unsplash e limite de requisições

## 📈 Limites e Custos

### OpenAI
- **Modelo**: gpt-4o-mini
- **Custo**: ~$0.15 por 1M tokens de entrada, $0.60 por 1M tokens de saída
- **Post médio**: ~$0.001 - $0.003 por post

### Anthropic
- **Modelo**: claude-3-5-sonnet-20241022
- **Custo**: $3 por 1M tokens de entrada, $15 por 1M tokens de saída
- **Post médio**: ~$0.01 - $0.03 por post

### Unsplash
- **Limite gratuito**: 50 requisições/hora
- **Custo adicional**: Planos pagos disponíveis

## 🤝 Suporte

- **Issues**: Reporte bugs via GitHub Issues
- **Documentação**: Consulte este README
- **Email**: seu-email@exemplo.com

## 📝 Changelog

### Versão 2.0.0
- ✨ Adicionado agendamento de posts
- ✨ Sistema de templates
- ✨ Geração de imagens automática
- ✨ Otimização SEO
- ✨ Histórico de posts gerados
- 🎨 Interface redesenhada
- 🐛 Correções de bugs diversos

### Versão 1.0.0
- 🎉 Lançamento inicial
- ✨ Integração OpenAI e Anthropic
- ✨ Geração básica de posts

## 📄 Licença

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## 👨‍💻 Autor

Desenvolvido por [Seu Nome]

---

⭐ Se você gostou deste plugin, considere dar uma estrela no GitHub!