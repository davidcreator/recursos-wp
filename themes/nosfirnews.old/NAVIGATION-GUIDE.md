# Sistema de Navegação Personalizada - NosfirNews

## Visão Geral

O tema NosfirNews agora inclui um sistema completo de navegação personalizada que permite posicionar e estilizar a navegação de diferentes formas através do WordPress Customizer.

## Opções de Posicionamento

### 1. Abaixo do Header (Padrão)
- Navegação posicionada em uma linha separada abaixo do cabeçalho
- Ideal para sites com muitos itens de menu
- Oferece máximo espaço para a navegação

### 2. Inline com Header
- Navegação posicionada na mesma linha do logo
- Layout compacto e moderno
- Adequado para sites com poucos itens de menu

### 3. À Direita do Logo
- Navegação posicionada imediatamente à direita do logo
- Mantém proximidade visual com a marca
- Boa para sites corporativos

### 4. Próximo ao Logo
- Navegação posicionada próxima ao logo com espaçamento
- Equilíbrio entre proximidade e separação visual
- Versátil para diferentes tipos de site

### 5. Centro do Header
- Navegação centralizada no cabeçalho
- Design simétrico e equilibrado
- Ideal para sites com foco em design

### 6. Direita do Header
- Navegação alinhada à direita do cabeçalho
- Layout clean e minimalista
- Adequado para sites modernos

## Opções de Alinhamento

- **Esquerda**: Itens alinhados à esquerda
- **Centro**: Itens centralizados
- **Direita**: Itens alinhados à direita

## Estilos Visuais

### Default
- Estilo padrão com espaçamento equilibrado
- Links com hover suave
- Adequado para a maioria dos sites

### Minimal
- Design limpo e minimalista
- Espaçamento reduzido
- Ideal para sites modernos

### Boxed
- Itens com fundo e bordas
- Aparência de botões
- Destaque visual para a navegação

### Underlined
- Sublinhado nos links ativos
- Indicação visual clara da página atual
- Estilo editorial clássico

## Configuração no WordPress

### Acessando as Opções
1. Vá para **Aparência > Personalizar**
2. Procure pela seção **Layout de Navegação**
3. Configure as opções desejadas:
   - Posição da Navegação
   - Alinhamento da Navegação
   - Estilo Visual
   - Comportamento Mobile
   - Cores Personalizadas

### Opções Disponíveis

#### Posição da Navegação
```php
'below_header'    => 'Abaixo do Header'
'inline_header'   => 'Inline com Header'
'right_of_logo'   => 'À Direita do Logo'
'next_to_logo'    => 'Próximo ao Logo'
'center_header'   => 'Centro do Header'
'right_header'    => 'Direita do Header'
```

#### Alinhamento
```php
'left'   => 'Esquerda'
'center' => 'Centro'
'right'  => 'Direita'
```

#### Estilo Visual
```php
'default'    => 'Padrão'
'minimal'    => 'Minimal'
'boxed'      => 'Boxed'
'underlined' => 'Sublinhado'
```

## Responsividade

### Comportamento Mobile
- Todos os layouts se adaptam automaticamente para dispositivos móveis
- Menu hambúrguer ativado em telas menores que 768px
- Navegação colapsável com animações suaves
- Otimizado para toque e navegação por gestos

### Breakpoints
- **Desktop**: > 768px - Layout completo conforme configurado
- **Tablet**: 768px - 480px - Layout adaptado com espaçamento otimizado
- **Mobile**: < 480px - Menu hambúrguer com navegação colapsável

## Acessibilidade

### Recursos Implementados
- Navegação por teclado (Tab, Enter, Esc)
- Suporte a leitores de tela
- Contraste adequado para WCAG 2.1
- Indicadores visuais para foco
- Textos alternativos para ícones

### Teclas de Atalho
- **Tab**: Navegar entre itens
- **Enter/Space**: Ativar link ou botão
- **Esc**: Fechar menu mobile
- **Setas**: Navegar em submenus (quando implementados)

## Personalização Avançada

### CSS Customizado
Para personalizações adicionais, você pode adicionar CSS customizado:

```css
/* Exemplo: Personalizar cor de hover */
.main-navigation a:hover {
    color: #seu-cor-personalizada;
    background-color: #sua-cor-de-fundo;
}

/* Exemplo: Personalizar espaçamento */
.nav-position-inline-header .main-navigation {
    margin-left: 2rem;
}
```

### Hooks do WordPress
O sistema utiliza hooks padrão do WordPress:

```php
// Modificar itens do menu
add_filter('wp_nav_menu_items', 'sua_funcao_personalizada');

// Adicionar classes CSS personalizadas
add_filter('nav_menu_css_class', 'suas_classes_personalizadas');
```

## Solução de Problemas

### Problemas Comuns

#### Navegação não aparece
- Verifique se um menu foi atribuído à localização "Primary"
- Confirme se o tema está ativo
- Limpe o cache do site

#### Layout quebrado em mobile
- Verifique se não há CSS conflitante
- Confirme se o viewport meta tag está presente
- Teste em diferentes dispositivos

#### Cores não aplicadas
- Verifique se as cores foram salvas no Customizer
- Confirme se não há CSS com !important sobrescrevendo
- Limpe o cache do navegador

### Suporte Técnico

Para suporte adicional:
1. Verifique a documentação do WordPress sobre menus
2. Teste com outros temas para isolar problemas
3. Desative plugins para identificar conflitos
4. Consulte os logs de erro do servidor

## Arquivos do Sistema

### Arquivos Principais
- `inc/navigation-layout.php` - Opções do Customizer
- `assets/css/navigation-layouts.css` - Estilos CSS
- `assets/js/navigation-layouts.js` - Funcionalidades JavaScript
- `header.php` - Estrutura do cabeçalho
- `template-parts/header/navigation.php` - Template da navegação

### Integração
- `inc/responsive-system.php` - Carregamento de assets
- `functions.php` - Inclusão dos arquivos

## Atualizações Futuras

### Recursos Planejados
- Suporte a mega menus
- Animações de transição personalizáveis
- Mais estilos visuais predefinidos
- Integração com page builders
- Suporte a navegação sticky

### Compatibilidade
- WordPress 5.0+
- PHP 7.4+
- Navegadores modernos (Chrome, Firefox, Safari, Edge)
- Dispositivos móveis e tablets