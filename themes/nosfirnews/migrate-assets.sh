#!/bin/bash

##############################################
# NosfirNews Asset Migration Script
# Automatiza a reorganização de assets
# 
# Uso: ./migrate-assets.sh
# 
# @version 1.0.0
# @author David L. Almeida
##############################################

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variáveis
THEME_DIR="$(pwd)"
BACKUP_DIR="${THEME_DIR}-backup-$(date +%Y%m%d-%H%M%S)"
ASSETS_DIR="${THEME_DIR}/assets"

##############################################
# Funções Helper
##############################################

print_header() {
    echo ""
    echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║   NosfirNews Asset Migration Tool    ║${NC}"
    echo -e "${BLUE}║              v1.0.0                    ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
    echo ""
}

print_step() {
    echo -e "${GREEN}▶${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✖${NC} $1"
}

print_success() {
    echo -e "${GREEN}✔${NC} $1"
}

confirm() {
    read -p "$(echo -e ${YELLOW}$1${NC}) [y/N]: " response
    case "$response" in
        [yY][eE][sS]|[yY]) 
            return 0
            ;;
        *)
            return 1
            ;;
    esac
}

##############################################
# Verificações Iniciais
##############################################

check_requirements() {
    print_step "Verificando requisitos..."
    
    # Verifica se está no diretório correto
    if [ ! -f "style.css" ] || [ ! -f "functions.php" ]; then
        print_error "Execute este script do diretório raiz do tema!"
        exit 1
    fi
    
    # Verifica se é o tema NosfirNews
    if ! grep -q "Theme Name: NosfirNews" style.css; then
        print_error "Este não parece ser o tema NosfirNews!"
        exit 1
    fi
    
    print_success "Requisitos verificados!"
}

##############################################
# Backup
##############################################

create_backup() {
    print_step "Criando backup..."
    
    if confirm "Deseja criar backup completo do tema?"; then
        cp -r "${THEME_DIR}" "${BACKUP_DIR}"
        
        if [ -d "${BACKUP_DIR}" ]; then
            print_success "Backup criado em: ${BACKUP_DIR}"
        else
            print_error "Falha ao criar backup!"
            exit 1
        fi
    else
        print_warning "Continuando SEM backup. Use por sua conta e risco!"
        sleep 2
    fi
}

##############################################
# Criação de Estrutura
##############################################

create_structure() {
    print_step "Criando nova estrutura de diretórios..."
    
    # Criar diretórios principais
    mkdir -p "${ASSETS_DIR}"/{css,js,img,fonts}
    
    # Subdiretórios CSS
    mkdir -p "${ASSETS_DIR}/css/customizer"
    mkdir -p "${ASSETS_DIR}/css/src"/{base,components,layouts,utilities}
    
    # Subdiretórios JS
    mkdir -p "${ASSETS_DIR}/js/customizer"
    mkdir -p "${ASSETS_DIR}/js/src"/{components,modules,utils}
    
    # Subdiretórios IMG
    mkdir -p "${ASSETS_DIR}/img"/{icons,placeholders,patterns}
    
    print_success "Estrutura criada!"
}

##############################################
# Migração de CSS
##############################################

migrate_css() {
    print_step "Migrando arquivos CSS..."
    
    # CSS do header-footer-grid
    if [ -f "header-footer-grid/assets/css/style-rtl.css" ]; then
        cp "header-footer-grid/assets/css/style-rtl.css" "${ASSETS_DIR}/css/header-footer-rtl.css"
        print_success "Movido: header-footer-rtl.css"
    fi
    
    # Criar placeholders para novos arquivos
    touch "${ASSETS_DIR}/css/main.css"
    touch "${ASSETS_DIR}/css/main-rtl.css"
    touch "${ASSETS_DIR}/css/admin.css"
    touch "${ASSETS_DIR}/css/editor.css"
    touch "${ASSETS_DIR}/css/critical.css"
    touch "${ASSETS_DIR}/css/customizer/controls.css"
    
    # Adicionar comentário nos novos arquivos
    echo "/* NosfirNews - $(basename $f) */" > "${ASSETS_DIR}/css/main.css"
    echo "/* TODO: Consolidar CSS de style-main-nosfirnews.css aqui */" >> "${ASSETS_DIR}/css/main.css"
    
    print_success "Arquivos CSS preparados!"
    print_warning "AÇÃO NECESSÁRIA: Consolidar manualmente style-main-nosfirnews.css em assets/css/main.css"
}

##############################################
# Migração de JavaScript
##############################################

migrate_js() {
    print_step "Migrando arquivos JavaScript..."
    
    # JS do header-footer-grid
    if [ -f "header-footer-grid/assets/js/theme.js" ]; then
        cp "header-footer-grid/assets/js/theme.js" "${ASSETS_DIR}/js/hfg-theme.js"
        print_success "Movido: hfg-theme.js"
    fi
    
    # Customizer
    if [ -f "header-footer-grid/assets/js/customizer/builder.js" ]; then
        cp "header-footer-grid/assets/js/customizer/builder.js" "${ASSETS_DIR}/js/customizer/preview.js"
        print_success "Movido: customizer/preview.js"
    fi
    
    if [ -f "header-footer-grid/assets/js/customizer/customizer.js" ]; then
        cp "header-footer-grid/assets/js/customizer/customizer.js" "${ASSETS_DIR}/js/customizer/controls.js"
        print_success "Movido: customizer/controls.js"
    fi
    
    # Criar placeholders
    touch "${ASSETS_DIR}/js/theme.js"
    touch "${ASSETS_DIR}/js/admin.js"
    touch "${ASSETS_DIR}/js/editor.js"
    
    # Adicionar comentário
    echo "/* NosfirNews Theme JavaScript */" > "${ASSETS_DIR}/js/theme.js"
    echo "/* TODO: Consolidar scripts aqui */" >> "${ASSETS_DIR}/js/theme.js"
    
    print_success "Arquivos JavaScript preparados!"
    print_warning "AÇÃO NECESSÁRIA: Consolidar scripts em assets/js/theme.js"
}

##############################################
# Atualização de Referências
##############################################

update_references() {
    print_step "Atualizando referências nos arquivos PHP..."
    
    # Buscar e reportar arquivos que precisam atualização
    print_warning "Arquivos PHP com referências antigas:"
    
    grep -r "header-footer-grid/assets" --include="*.php" . | \
        cut -d: -f1 | \
        sort -u | \
        while read file; do
            echo "  - $file"
        done
    
    echo ""
    print_warning "AÇÃO NECESSÁRIA: Atualizar manualmente as referências nos arquivos acima"
    echo ""
    echo "Substituir:"
    echo "  get_template_directory_uri() . '/header-footer-grid/assets/...'"
    echo "Por:"
    echo "  NOSFIRNEWS_ASSETS_URI . '/...'"
}

##############################################
# Criação de Arquivos Base
##############################################

create_base_files() {
    print_step "Criando arquivos base..."
    
    # Critical CSS
    cat > "${ASSETS_DIR}/css/critical.css" << 'EOF'
/* NosfirNews Critical CSS */
/* Apenas estilos above-the-fold */

:root {
  --primary: #1a73e8;
  --text: #333;
  --white: #fff;
  --border: #e1e5e9;
}

.site-header {
  background: var(--white);
  border-bottom: 1px solid var(--border);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}
EOF
    
    # Admin CSS
    cat > "${ASSETS_DIR}/css/admin.css" << 'EOF'
/* NosfirNews Admin Styles */

.nosfirnews-admin-page {
  padding: 20px;
}

.nosfirnews-admin-section {
  background: #fff;
  border: 1px solid #ccd0d4;
  border-radius: 4px;
  padding: 20px;
  margin-bottom: 20px;
}
EOF
    
    # Admin JS
    cat > "${ASSETS_DIR}/js/admin.js" << 'EOF'
/**
 * NosfirNews Admin JavaScript
 */

(function($) {
  'use strict';
  
  $(document).ready(function() {
    console.log('NosfirNews Admin loaded');
  });
  
})(jQuery);
EOF
    
    print_success "Arquivos base criados!"
}

##############################################
# Verificação Final
##############################################

final_check() {
    print_step "Verificação final..."
    
    echo ""
    echo -e "${BLUE}Estrutura criada:${NC}"
    tree -L 2 "${ASSETS_DIR}" 2>/dev/null || ls -R "${ASSETS_DIR}"
    
    echo ""
    print_success "Migração estrutural concluída!"
    echo ""
    echo -e "${YELLOW}═══════════════════════════════════════${NC}"
    echo -e "${YELLOW}AÇÕES MANUAIS NECESSÁRIAS:${NC}"
    echo -e "${YELLOW}═══════════════════════════════════════${NC}"
    echo ""
    echo "1. Consolidar CSS:"
    echo "   - Mesclar style-main-nosfirnews.css em assets/css/main.css"
    echo ""
    echo "2. Consolidar JavaScript:"
    echo "   - Mesclar scripts inline em assets/js/theme.js"
    echo ""
    echo "3. Atualizar functions.php:"
    echo "   - Substituir função nosfirnews_scripts()"
    echo "   - Adicionar constantes NOSFIRNEWS_ASSETS_*"
    echo ""
    echo "4. Atualizar referências:"
    echo "   - Substituir paths antigos nos arquivos PHP"
    echo ""
    echo "5. Testar:"
    echo "   - Frontend"
    echo "   - Customizer"
    echo "   - Admin"
    echo ""
    echo -e "${GREEN}Backup salvo em:${NC}"
    echo "${BACKUP_DIR}"
    echo ""
}

##############################################
# Main
##############################################

main() {
    print_header
    
    check_requirements
    create_backup
    create_structure
    migrate_css
    migrate_js
    update_references
    create_base_files
    final_check
    
    echo ""
    print_success "Script concluído!"
    echo ""
}

# Executar
main