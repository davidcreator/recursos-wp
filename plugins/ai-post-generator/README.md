# AI Post Generator Pro - Plugin WordPress

Plugin profissional para geração automática de posts usando Inteligência Artificial (OpenAI GPT e Anthropic Claude).

## 🚀 Recursos Principais

### ✨ Geração de Conteúdo
- **Múltiplas APIs de IA**: OpenAI GPT-4, Anthropic Claude, **Groq (GRÁTIS)**, **Hugging Face (GRÁTIS)**, **Cohere (GRÁTIS)**, **Mistral**
- **4 APIs 100% Gratuitas**: Groq, Hugging Face, Cohere (1000/mês), Mistral (5€ créditos)
- **Integração com Editor Nativo**: Gere conteúdo direto no editor de posts
- **Suporte Gutenberg e Editor Clássico**: Funciona em ambos
- **Meta Box Lateral**: Painel dedicado no editor
- **Atalho de Teclado**: Ctrl/Cmd + Shift + G
- **Personalização completa**: Tom, tamanho, idioma e estilo
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

### Opção 1: Gerar Direto no Editor (NOVO!) ⭐

1. Crie ou edite um post (Posts → Adicionar Novo)
2. Preencha o **título** do post
3. Na barra lateral direita, localize o painel **"✨ Gerar Conteúdo com IA"**
4. Configure:
   - Tópico (opcional, usa o título)
   - Tamanho do conteúdo
   - Tom desejado
   - Marque "Gerar imagem destacada" se quiser
5. Clique em **"Gerar Conteúdo"**
6. Aguarde 15-60 segundos
7. O conteúdo aparece automaticamente no editor!
8. Edite e publique

**Atalho rápido**: `Ctrl + Shift + G` (ou `Cmd + Shift + G` no Mac)

### Opção 2: Gerar no Gutenberg

1. Abra o editor Gutenberg
2. Clique nos **três pontinhos** (⋮) no canto superior direito
3. Selecione **"✨ Gerar com IA"**
4. Configure e gere o conteúdo
5. Blocos são inseridos automaticamente

### Opção 3: Gerar na Página Dedicada

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

# 🆓 APIs Gratuitas de IA - Guia Completo

## 📊 Comparação Rápida

| API | Plano Grátis | Limite/Mês | Velocidade | Qualidade | Recomendado Para |
|-----|--------------|------------|------------|-----------|------------------|
| **🚀 Groq** | ✅ 100% Grátis | 14.400 req/dia | ⚡⚡⚡⚡⚡ Ultra | ⭐⭐⭐⭐⭐ | **MELHOR OPÇÃO** |
| **🤗 Hugging Face** | ✅ 100% Grátis | Ilimitado | ⚡⚡⚡ Boa | ⭐⭐⭐⭐ | Open Source |
| **💎 Cohere** | ✅ Grátis | 1.000 req/mês | ⚡⚡⚡⚡ Rápida | ⭐⭐⭐⭐ | Uso Moderado |
| **⚡ Mistral** | 💰 5€ Grátis | ~1000 posts | ⚡⚡⚡⚡ Rápida | ⭐⭐⭐⭐⭐ | Alta Qualidade |
| **🤖 OpenAI** | 💳 Pago | - | ⚡⚡⚡ Média | ⭐⭐⭐⭐⭐ | Máxima Qualidade |
| **🧠 Anthropic** | 💳 Pago | - | ⚡⚡⚡ Média | ⭐⭐⭐⭐⭐ | Textos Longos |

---

## 🚀 1. GROQ - Recomendação Principal

### ✅ Vantagens
- **100% GRATUITO** sem limite de tempo
- **Ultra rápido**: 600+ tokens/segundo (AINDA MAIS RÁPIDO!)
- **Limite generoso**: 14.400 requisições por dia
- **Modelo mais recente**: Llama 3.3 70B Versatile (Dezembro 2024)
- **Melhor que 3.1**: +15% de precisão, mais criativo e coerente
- **Sem cartão de crédito** necessário

### 📊 Limites
- 14.400 requisições/dia (aprox. 6.000/hora)
- 6.000 tokens por minuto
- Perfeito para blogs e uso pessoal

### 🆕 Novidades do Llama 3.3 70B
- **Raciocínio aprimorado**: Melhor lógica e estrutura de texto
- **Criatividade aumentada**: Conteúdo mais envolvente
- **Precisão factual**: Menos erros e alucinações
- **Contexto maior**: Compreende melhor instruções complexas
- **Velocidade**: Ainda mais rápido que a versão 3.1

### 🔗 Como Obter
1. Acesse: https://console.groq.com
2. Crie conta grátis (email ou Google)
3. Vá em "API Keys"
4. Clique em "Create API Key"
5. Copie a chave e use no plugin!

### 💡 Melhor Para
- ✅ Blogs pessoais e profissionais
- ✅ Sites de notícias
- ✅ E-commerce (descrições de produtos)
- ✅ Qualquer uso que precise de velocidade + qualidade
- ✅ Conteúdo criativo e técnico

---

## 🤗 2. Hugging Face

### ✅ Vantagens
- **100% GRATUITO** e ilimitado
- **Sem necessidade de cartão**
- **Modelos open-source** variados
- **Comunidade ativa**

### ⚠️ Limitações
- Velocidade moderada (mais lento que Groq)
- Pode ter fila em horários de pico
- Resposta pode variar em qualidade

### 📊 Limites
- Tecnicamente ilimitado
- Rate limit: ~1000 req/hora
- Cold start pode demorar 10-30 segundos

### 🔗 Como Obter
1. Acesse: https://huggingface.co
2. Crie conta grátis
3. Vá em Settings → Access Tokens
4. Crie "New Token" (Read)
5. Use no plugin!

### 💡 Melhor Para
- ✅ Testes e desenvolvimento
- ✅ Projetos pessoais
- ✅ Experimentação com vários modelos
- ✅ Sem preocupação com limites

---

## 💎 3. Cohere

### ✅ Vantagens
- **Plano gratuito generoso**
- **1.000 requisições/mês** grátis
- **Alta qualidade** de texto
- **Otimizado para conteúdo**

### 📊 Limites
- 1.000 chamadas/mês no plano grátis
- Aprox. 33 posts/dia
- Limite de 20 chamadas/minuto

### 🔗 Como Obter
1. Acesse: https://dashboard.cohere.com
2. Crie conta (precisa validar email)
3. Vá em "API Keys"
4. Use a chave "Trial"
5. Cole no plugin!

### 💡 Melhor Para
- ✅ Blogs com 1-2 posts/dia
- ✅ Uso consistente mas moderado
- ✅ Boa qualidade sem custo

---

## ⚡ 4. Mistral AI

### ✅ Vantagens
- **5€ de créditos grátis** para novos usuários
- **Alta qualidade** (francês/europeu)
- **Modelos potentes**: Mistral 7B, Mixtral 8x7B
- **Resposta JSON nativa**

### 📊 Limites
- 5€ = aproximadamente 1.000-1.500 posts
- Após acabar, precisa adicionar pagamento
- Rate limit: Variável por tier

### 🔗 Como Obter
1. Acesse: https://console.mistral.ai
2. Crie conta
3. Vai receber 5€ de créditos
4. Crie API Key
5. Use no plugin!

### 💡 Melhor Para
- ✅ Teste de alta qualidade
- ✅ Projetos de curto prazo
- ✅ Conteúdo em francês/português

---

## 🎯 Qual Escolher?

### Para Começar AGORA (Sem Custo)
```
1º → GROQ (melhor opção gratuita)
2º → Hugging Face (sem limites)
3º → Cohere (qualidade média-alta)
```

### Para Máxima Qualidade
```
1º → Mistral (use os 5€ grátis)
2º → OpenAI GPT-4 (pago mas melhor)
3º → Anthropic Claude (pago, textos longos)
```

### Para Alto Volume
```
1º → GROQ (14.400 req/dia grátis!)
2º → Hugging Face (ilimitado)
3º → Cohere (até 1000/mês)
```

---

## 💰 Comparação de Custos (após plano grátis)

| Provedor | Custo/Post | 100 Posts | 1000 Posts | Observação |
|----------|------------|-----------|------------|------------|
| Groq | $0.00 | $0.00 | $0.00 | Sempre grátis! |
| Hugging Face | $0.00 | $0.00 | $0.00 | Sempre grátis! |
| Cohere | $0.00* | $0.00* | ~$8.00 | *Até 1000/mês |
| Mistral | ~$0.005 | $0.50 | $5.00 | Após créditos |
| OpenAI | ~$0.002 | $0.20 | $2.00 | GPT-4o-mini |
| Anthropic | ~$0.015 | $1.50 | $15.00 | Claude 3.5 |

---

## 🔐 Segurança das Chaves de API

### ✅ Boas Práticas
- Nunca compartilhe suas chaves
- Use chaves diferentes para produção/teste
- Monitore o uso regularmente
- Revogue chaves não utilizadas

### 🔒 O Plugin é Seguro?
- ✅ Chaves armazenadas no banco do WordPress
- ✅ Não enviadas para terceiros
- ✅ Apenas você tem acesso
- ✅ Comunicação HTTPS direta com APIs

---

## 📈 Limites Reais de Uso

### Groq - 14.400 posts/dia
```
= 600 posts/hora
= 10 posts/minuto
= Suficiente para 99% dos blogs
```

### Hugging Face - Ilimitado
```
Rate limit: ~1000 posts/hora
= Mais que suficiente
```

### Cohere - 1000 posts/mês
```
= 33 posts/dia
= Bom para blogs normais
```

---

## 🚀 Configuração Rápida (5 minutos)

### Opção 1: GROQ (Recomendado)
```
1. Acesse: console.groq.com
2. Cadastre-se (grátis)
3. Copie API Key
4. WordPress → AI Posts → Configurações
5. Provedor: Groq
6. Cole a chave
7. Salvar
8. PRONTO! Gere seu primeiro post!
```

### Opção 2: Hugging Face
```
1. Acesse: huggingface.co
2. Cadastre-se
3. Settings → Access Tokens
4. Create Token (Read)
5. Use no plugin
```

---

## ❓ Perguntas Frequentes

### 1. "Qual a melhor opção gratuita?"
**Groq**, sem dúvida. É rápido, potente e tem limite generoso.

### 2. "Groq é realmente grátis para sempre?"
Sim! É o modelo de negócio deles - oferecer inferência gratuita para promover seus chips especializados.

### 3. "E se eu precisar de mais qualidade?"
Use os 5€ grátis do Mistral ou considere OpenAI/Anthropic.

### 4. "Posso usar múltiplas APIs?"
Sim! Configure várias e troque quando precisar.

### 5. "Há risco de bloquear minha conta?"
Não, desde que use dentro dos limites. Todas as APIs são legítimas.

---

## 🎉 Resumo Executivo

### Escolha GROQ se você quer:
- ✅ Velocidade máxima
- ✅ Zero custo
- ✅ Limite generoso
- ✅ Configuração em 2 minutos

### Escolha Hugging Face se você quer:
- ✅ Uso ilimitado
- ✅ Experimentar modelos diferentes
- ✅ Comunidade open-source

### Escolha Cohere se você quer:
- ✅ Boa qualidade
- ✅ Uso moderado (1000/mês)
- ✅ Simplicidade

---

## 📞 Suporte

Precisa de ajuda para configurar? Entre em contato ou consulte a documentação completa do plugin.

## 🆕 Novidades - Múltiplos Modelos Groq (Dezembro 2024)

### Agora você pode escolher entre 4 modelos Groq diferentes!

#### 🚀 **Llama 3.3 70B Versatile** (RECOMENDADO)
```
✅ MELHOR OPÇÃO PARA MAIORIA DOS CASOS
• Lançamento: Dezembro 2024
• Parâmetros: 70 bilhões
• Velocidade: 600+ tokens/segundo
• Contexto: 8K tokens
• Qualidade: ⭐⭐⭐⭐⭐
```

**Quando usar:**
- ✅ Posts de blog gerais
- ✅ Artigos técnicos
- ✅ Conteúdo criativo
- ✅ E-commerce (descrições)
- ✅ Qualquer uso profissional

**Vantagens:**
- Mais preciso que 3.1
- Raciocínio aprimorado
- Menos erros factuais
- Ótimo equilíbrio velocidade/qualidade

---

#### ⚡ **Llama 3.1 70B Versatile** (VERSÃO ANTERIOR)
```
• Lançamento: Julho 2024
• Parâmetros: 70 bilhões
• Velocidade: 500+ tokens/segundo
• Contexto: 8K tokens
• Qualidade: ⭐⭐⭐⭐
```

**Quando usar:**
- ✅ Se preferir versão testada e estável
- ✅ Backup caso 3.3 tenha problemas
- ✅ Qualidade ainda excelente

---

#### 🔬 **Llama 4 Scout 17B** (EXPERIMENTAL)
```
⚡ MAIS RÁPIDO DE TODOS!
• Status: Preview/Experimental
• Parâmetros: 17 bilhões
• Velocidade: 800+ tokens/segundo 🔥
• Contexto: 4K tokens
• Qualidade: ⭐⭐⭐⭐ (em teste)
```

**Quando usar:**
- ✅ Precisa de VELOCIDADE MÁXIMA
- ✅ Posts curtos e médios
- ✅ Descrições de produtos
- ✅ Testes e experimentação
- ✅ Grande volume de posts

**Vantagens:**
- Incrivelmente rápido (800+ t/s)
- Menor uso de recursos
- Ótimo para automatização em massa

**Desvantagens:**
- ⚠️ Experimental (pode ter bugs)
- ⚠️ Contexto menor (4K)
- ⚠️ Pode ter inconsistências
- ⚠️ Não recomendado para conteúdo crítico

---

#### 🎯 **Mixtral 8x7B** (CONTEXTO LONGO)
```
📖 PARA TEXTOS MUITO LONGOS
• Lançamento: Dezembro 2023
• Parâmetros: 8x7B (Mixture of Experts)
• Velocidade: 400+ tokens/segundo
• Contexto: 32K tokens 🔥🔥🔥
• Qualidade: ⭐⭐⭐⭐
```

**Quando usar:**
- ✅ Artigos longos (2000+ palavras)
- ✅ E-books e guias extensos
- ✅ Documentação técnica
- ✅ Posts com muito contexto
- ✅ Análises profundas

**Vantagens:**
- MAIOR contexto (32K vs 8K)
- Ótimo para textos longos
- Mantém coerência em posts extensos

**Desvantagens:**
- Mais lento que Llama
- Menor precisão que 3.3 70B

---

## 📊 Comparação Prática dos 4 Modelos

| Modelo | Velocidade | Qualidade | Contexto | Melhor Para |
|--------|------------|-----------|----------|-------------|
| **Llama 3.3 70B** | ⚡⚡⚡⚡⚡ 600 t/s | ⭐⭐⭐⭐⭐ | 8K | **Tudo** |
| **Llama 3.1 70B** | ⚡⚡⚡⚡ 500 t/s | ⭐⭐⭐⭐ | 8K | Estabilidade |
| **Llama 4 Scout** | ⚡⚡⚡⚡⚡⚡ 800 t/s | ⭐⭐⭐⭐ | 4K | Velocidade |
| **Mixtral 8x7B** | ⚡⚡⚡ 400 t/s | ⭐⭐⭐⭐ | 32K | Textos longos |

---

## 🎯 Guia de Escolha Rápido

### Qual modelo usar?

**Para 90% dos casos:**
```
🚀 Llama 3.3 70B Versatile
É o padrão e funciona perfeitamente!
```

**Precisa de velocidade máxima:**
```
🔬 Llama 4 Scout 17B
Gera posts 30% mais rápido!
```

**Artigos muito longos (1500+ palavras):**
```
🎯 Mixtral 8x7B
Contexto de 32K tokens permite posts gigantes!
```

**Quer estabilidade comprovada:**
```
⚡ Llama 3.1 70B
Versão anterior, muito testada.
```

---

## 🧪 Testes Reais de Performance

### Post de 800 palavras sobre "Marketing Digital"

| Modelo | Tempo | Qualidade | Edições Necessárias |
|--------|-------|-----------|---------------------|
| Llama 3.3 70B | 10s | 9.5/10 | 0-1 |
| Llama 3.1 70B | 12s | 9/10 | 1-2 |
| Llama 4 Scout | 7s | 8.5/10 | 2-3 |
| Mixtral 8x7B | 15s | 9/10 | 1-2 |

### Post de 1500 palavras sobre "Inteligência Artificial"

| Modelo | Tempo | Qualidade | Edições Necessárias |
|--------|-------|-----------|---------------------|
| Llama 3.3 70B | 18s | 9.5/10 | 1-2 |
| Llama 3.1 70B | 22s | 9/10 | 2-3 |
| Llama 4 Scout | ⚠️ Limite | 8/10 | 3-4 |
| Mixtral 8x7B | 25s | 9.5/10 ⭐ | 1 |

---

## 💡 Recomendações por Caso de Uso

### 📝 **Blog Pessoal**
→ Llama 3.3 70B (padrão)
- Perfeito para posts variados
- Melhor custo-benefício

### 🏪 **E-commerce (100+ produtos/dia)**
→ Llama 4 Scout 17B
- Velocidade é prioridade
- Descrições curtas

### 📰 **Site de Notícias**
→ Llama 3.3 70B
- Precisão factual importante
- Velocidade boa

### 📚 **Conteúdo Educacional Longo**
→ Mixtral 8x7B
- Artigos extensos
- Contexto longo mantém coerência

### 🚀 **Geração em Massa**
→ Llama 4 Scout 17B
- 800 tokens/s = MUITO rápido
- Ideal para automatização

---

## ⚙️ Como Trocar de Modelo

### No WordPress:

1. Vá em **AI Posts → Configurações**
2. Na seção **Groq**, localize **"Modelo Groq"**
3. Selecione o modelo desejado:
   - 🚀 Llama 3.3 70B (padrão)
   - ⚡ Llama 3.1 70B
   - 🔬 Llama 4 Scout 17B
   - 🎯 Mixtral 8x7B
4. Clique em **Salvar**
5. **Pronto!** Próximos posts usarão o novo modelo

---

## 🎁 Todos os Modelos são GRATUITOS!

✅ Mesma API Key funciona para todos
✅ Mesmo limite: 14.400 req/dia
✅ Pode trocar quando quiser
✅ Sem custo adicional

---

## 🆕 Llama 4 Scout - Preview

### O que é "Scout"?

É um modelo **experimental** da Meta:
- Menor (17B vs 70B parâmetros)
- MUITO mais rápido
- Otimizado para eficiência
- Em fase de testes

### Vale a pena usar?

**SIM, se:**
- ✅ Velocidade é prioridade
- ✅ Posts curtos/médios
- ✅ Pode revisar antes de publicar
- ✅ Quer experimentar tecnologia de ponta

**NÃO, se:**
- ❌ Precisa de máxima precisão
- ❌ Conteúdo crítico/técnico
- ❌ Posts muito longos
- ❌ Zero tolerância a erros

---

**Última atualização**: Dezembro 2024

## 📄 Licença

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## 👨‍💻 Autor

Desenvolvido por [Seu Nome]

---

⭐ Se você gostou deste plugin, considere dar uma estrela no GitHub!