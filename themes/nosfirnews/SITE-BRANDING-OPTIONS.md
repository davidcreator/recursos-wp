# Opções de Site Branding - NosfirNews Theme

## Visão Geral

O tema NosfirNews agora oferece controle total sobre a exibição do título e descrição do site, permitindo criar layouts flexíveis que vão desde a exibição completa até um modo "apenas logo".

## Opções Disponíveis

### 1. Exibir Título do Site
- **Localização**: Customizer > Opções do Tema > Opções de Cabeçalho
- **Controle**: `nosfirnews_display_site_title`
- **Padrão**: Ativado
- **Descrição**: Controla se o título do site é exibido ao lado do logo

### 2. Exibir Descrição do Site
- **Localização**: Customizer > Opções do Tema > Opções de Cabeçalho
- **Controle**: `nosfirnews_display_site_description`
- **Padrão**: Ativado
- **Descrição**: Controla se a descrição/tagline do site é exibida

### 3. Modo Apenas Logo
- **Localização**: Customizer > Opções do Tema > Opções de Cabeçalho
- **Controle**: `nosfirnews_logo_only_mode`
- **Padrão**: Desativado
- **Descrição**: Quando ativado, oculta automaticamente título e descrição, exibindo apenas o logo

## Como Usar

### Acessando as Opções
1. Acesse o **Customizer** do WordPress (Aparência > Personalizar)
2. Navegue até **Opções do Tema**
3. Clique em **Opções de Cabeçalho**
4. Configure as opções conforme desejado

### Cenários de Uso

#### Cenário 1: Layout Completo (Padrão)
- ✅ Exibir Título do Site
- ✅ Exibir Descrição do Site
- ❌ Modo Apenas Logo

**Resultado**: Logo + Título + Descrição

#### Cenário 2: Logo + Título (Sem Descrição)
- ✅ Exibir Título do Site
- ❌ Exibir Descrição do Site
- ❌ Modo Apenas Logo

**Resultado**: Logo + Título

#### Cenário 3: Apenas Logo
- ❌ Exibir Título do Site
- ❌ Exibir Descrição do Site
- ✅ Modo Apenas Logo

**Resultado**: Apenas Logo (centralizado)

#### Cenário 4: Apenas Título (Sem Logo)
- ✅ Exibir Título do Site
- ✅ Exibir Descrição do Site
- ❌ Modo Apenas Logo
- Sem logo personalizado configurado

**Resultado**: Título + Descrição

## Recursos Técnicos

### Acessibilidade
- Todos os elementos mantêm labels ARIA apropriados
- Links do logo incluem texto alternativo descritivo
- Foco do teclado é preservado em todos os modos

### SEO
- Microdata Schema.org é mantido mesmo quando elementos são ocultos
- Dados estruturados incluem informações da organização
- Títulos H1 são preservados na página inicial

### Responsividade
- Logo redimensiona automaticamente em dispositivos móveis
- Layouts se adaptam conforme o espaço disponível
- Transições suaves entre estados

### Customizer Preview
- Mudanças são visíveis em tempo real
- Elementos ocultos aparecem com opacidade reduzida durante a edição
- Indicação visual de elementos ocultos

## Personalização Avançada

### CSS Personalizado
Para personalizar ainda mais a aparência, você pode adicionar CSS personalizado:

```css
/* Ajustar tamanho do logo */
.site-branding .site-logo .custom-logo {
    max-height: 100px; /* Ajuste conforme necessário */
}

/* Personalizar espaçamento no modo apenas logo */
.site-branding:not(:has(.site-identity:not([style*="display: none"]))) {
    padding: 20px 0; /* Adicionar espaçamento vertical */
}

/* Customizar transições */
.site-identity .site-title-wrapper,
.site-identity .site-description-wrapper {
    transition: all 0.5s ease; /* Transição mais lenta */
}
```

### Hooks para Desenvolvedores
O tema oferece hooks para personalização adicional:

```php
// Filtrar opções padrão
add_filter( 'nosfirnews_default_site_title_display', '__return_false' );
add_filter( 'nosfirnews_default_site_description_display', '__return_false' );
add_filter( 'nosfirnews_default_logo_only_mode', '__return_true' );

// Ação após renderização do site branding
add_action( 'nosfirnews_after_site_branding', 'minha_funcao_personalizada' );
```

## Compatibilidade

- ✅ WordPress 5.0+
- ✅ Customizer nativo do WordPress
- ✅ Todos os navegadores modernos
- ✅ Dispositivos móveis e tablets
- ✅ Leitores de tela
- ✅ Plugins de SEO populares

## Suporte

Para dúvidas ou problemas relacionados a essas funcionalidades, consulte:
1. Documentação do tema
2. Fórum de suporte do WordPress
3. Repositório do tema (se aplicável)

---

**Versão**: 2.0.0  
**Última atualização**: Dezembro 2024