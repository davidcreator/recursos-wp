/**
 * Custom Templates JavaScript
 * Handles template selection, preview, editing, and management
 */

(function($) {
    'use strict';

    // Template Manager Class
    class TemplateManager {
        constructor() {
            this.currentTemplate = null;
            this.previewMode = false;
            this.unsavedChanges = false;
            this.init();
        }

        init() {
            this.bindEvents();
            this.initTemplateSelector();
            this.initEditor();
            this.initPreview();
            this.initAutoSave();
            this.initKeyboardShortcuts();
        }

        bindEvents() {
            // Template selection
            $(document).on('change', '.template-selector', this.handleTemplateChange.bind(this));
            
            // Template actions
            $(document).on('click', '.template-preview-btn', this.showPreview.bind(this));
            $(document).on('click', '.template-edit-btn', this.showEditor.bind(this));
            $(document).on('click', '.template-save-btn', this.saveTemplate.bind(this));
            $(document).on('click', '.template-duplicate-btn', this.duplicateTemplate.bind(this));
            $(document).on('click', '.template-delete-btn', this.deleteTemplate.bind(this));
            $(document).on('click', '.template-export-btn', this.exportTemplate.bind(this));
            $(document).on('click', '.template-import-btn', this.importTemplate.bind(this));
            
            // Editor tabs
            $(document).on('click', '.editor-tab', this.switchEditorTab.bind(this));
            
            // Template builder
            $(document).on('click', '.add-section-btn', this.addSection.bind(this));
            $(document).on('click', '.remove-section-btn', this.removeSection.bind(this));
            $(document).on('click', '.move-section-up', this.moveSectionUp.bind(this));
            $(document).on('click', '.move-section-down', this.moveSectionDown.bind(this));
            
            // Drag and drop
            this.initDragAndDrop();
            
            // Form changes
            $(document).on('change input', '.template-editor input, .template-editor textarea, .template-editor select', 
                this.handleFormChange.bind(this));
            
            // Modal events
            $(document).on('click', '.close-modal', this.closeModal.bind(this));
            $(document).on('click', '.modal-overlay', this.closeModal.bind(this));
            
            // Window events
            $(window).on('beforeunload', this.handleBeforeUnload.bind(this));
        }

        initTemplateSelector() {
            const selector = $('.template-selector');
            if (selector.length) {
                // Initialize select2 if available
                if ($.fn.select2) {
                    selector.select2({
                        placeholder: 'Selecionar template...',
                        allowClear: true
                    });
                }
                
                // Load current template
                const currentValue = selector.val();
                if (currentValue) {
                    this.loadTemplate(currentValue);
                }
            }
        }

        initEditor() {
            // Initialize code editor if available
            if (typeof CodeMirror !== 'undefined') {
                $('.code-editor').each(function() {
                    const editor = CodeMirror.fromTextArea(this, {
                        mode: 'php',
                        theme: 'default',
                        lineNumbers: true,
                        autoCloseTags: true,
                        autoCloseBrackets: true,
                        matchBrackets: true,
                        indentUnit: 4,
                        indentWithTabs: true
                    });
                    
                    $(this).data('codemirror', editor);
                });
            }
            
            // Initialize color pickers
            if ($.fn.wpColorPicker) {
                $('.color-picker').wpColorPicker();
            }
            
            // Initialize media uploader
            this.initMediaUploader();
        }

        initPreview() {
            const previewFrame = $('#template-preview-frame');
            if (previewFrame.length) {
                previewFrame.on('load', this.handlePreviewLoad.bind(this));
            }
        }

        initAutoSave() {
            setInterval(() => {
                if (this.unsavedChanges && this.currentTemplate) {
                    this.autoSaveTemplate();
                }
            }, 30000); // Auto-save every 30 seconds
        }

        initKeyboardShortcuts() {
            $(document).on('keydown', (e) => {
                // Ctrl+S to save
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    this.saveTemplate();
                }
                
                // Ctrl+P to preview
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    this.showPreview();
                }
                
                // Escape to close modal
                if (e.key === 'Escape') {
                    this.closeModal();
                }
            });
        }

        initDragAndDrop() {
            if ($.fn.sortable) {
                $('.template-sections').sortable({
                    handle: '.section-handle',
                    placeholder: 'section-placeholder',
                    update: this.handleSectionReorder.bind(this)
                });
            }
        }

        initMediaUploader() {
            $(document).on('click', '.upload-media-btn', function(e) {
                e.preventDefault();
                
                const button = $(this);
                const targetInput = button.siblings('input[type="hidden"]');
                const preview = button.siblings('.media-preview');
                
                const mediaUploader = wp.media({
                    title: 'Selecionar Mídia',
                    button: {
                        text: 'Usar esta mídia'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    targetInput.val(attachment.id);
                    
                    if (attachment.type === 'image') {
                        preview.html(`<img src="${attachment.url}" alt="${attachment.alt}" style="max-width: 100px; height: auto;">`);
                    } else {
                        preview.html(`<span>${attachment.filename}</span>`);
                    }
                });
                
                mediaUploader.open();
            });
        }

        handleTemplateChange(e) {
            const templateId = $(e.target).val();
            
            if (this.unsavedChanges) {
                if (!confirm('Você tem alterações não salvas. Deseja continuar?')) {
                    $(e.target).val(this.currentTemplate);
                    return;
                }
            }
            
            if (templateId) {
                this.loadTemplate(templateId);
            } else {
                this.clearTemplate();
            }
        }

        loadTemplate(templateId) {
            this.showLoading();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'load_custom_template',
                    template_id: templateId,
                    nonce: customTemplatesData.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.populateEditor(response.data);
                        this.currentTemplate = templateId;
                        this.unsavedChanges = false;
                        this.showSuccess('Template carregado com sucesso!');
                    } else {
                        this.showError(response.data || 'Erro ao carregar template');
                    }
                },
                error: () => {
                    this.showError('Erro de conexão');
                },
                complete: () => {
                    this.hideLoading();
                }
            });
        }

        populateEditor(data) {
            // Populate form fields
            Object.keys(data).forEach(key => {
                const field = $(`[name="${key}"]`);
                if (field.length) {
                    if (field.is(':checkbox')) {
                        field.prop('checked', data[key]);
                    } else {
                        field.val(data[key]);
                    }
                }
            });
            
            // Update CodeMirror editors
            $('.code-editor').each(function() {
                const editor = $(this).data('codemirror');
                if (editor && data[$(this).attr('name')]) {
                    editor.setValue(data[$(this).attr('name')]);
                }
            });
            
            // Trigger change events
            $('.template-editor input, .template-editor textarea, .template-editor select').trigger('change');
        }

        clearTemplate() {
            $('.template-editor')[0].reset();
            $('.code-editor').each(function() {
                const editor = $(this).data('codemirror');
                if (editor) {
                    editor.setValue('');
                }
            });
            this.currentTemplate = null;
            this.unsavedChanges = false;
        }

        showPreview() {
            if (!this.currentTemplate) {
                this.showError('Selecione um template primeiro');
                return;
            }
            
            const previewUrl = `${customTemplatesData.previewUrl}&template=${this.currentTemplate}`;
            const previewFrame = $('#template-preview-frame');
            
            if (previewFrame.length) {
                previewFrame.attr('src', previewUrl);
                $('.template-preview-modal').addClass('active');
            } else {
                window.open(previewUrl, '_blank');
            }
        }

        showEditor() {
            $('.template-editor-modal').addClass('active');
        }

        saveTemplate() {
            if (!this.currentTemplate) {
                this.showError('Nenhum template selecionado');
                return;
            }
            
            const formData = new FormData($('.template-editor')[0]);
            formData.append('action', 'save_custom_template');
            formData.append('template_id', this.currentTemplate);
            formData.append('nonce', customTemplatesData.nonce);
            
            // Add CodeMirror content
            $('.code-editor').each(function() {
                const editor = $(this).data('codemirror');
                if (editor) {
                    formData.set($(this).attr('name'), editor.getValue());
                }
            });
            
            this.showLoading();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    if (response.success) {
                        this.unsavedChanges = false;
                        this.showSuccess('Template salvo com sucesso!');
                    } else {
                        this.showError(response.data || 'Erro ao salvar template');
                    }
                },
                error: () => {
                    this.showError('Erro de conexão');
                },
                complete: () => {
                    this.hideLoading();
                }
            });
        }

        autoSaveTemplate() {
            if (!this.currentTemplate) return;
            
            const formData = new FormData($('.template-editor')[0]);
            formData.append('action', 'autosave_custom_template');
            formData.append('template_id', this.currentTemplate);
            formData.append('nonce', customTemplatesData.nonce);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    if (response.success) {
                        $('.autosave-indicator').text('Salvo automaticamente').fadeIn().delay(2000).fadeOut();
                    }
                }
            });
        }

        duplicateTemplate() {
            if (!this.currentTemplate) {
                this.showError('Selecione um template primeiro');
                return;
            }
            
            const newName = prompt('Nome do novo template:');
            if (!newName) return;
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'duplicate_custom_template',
                    template_id: this.currentTemplate,
                    new_name: newName,
                    nonce: customTemplatesData.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.showSuccess('Template duplicado com sucesso!');
                        location.reload(); // Reload to update template list
                    } else {
                        this.showError(response.data || 'Erro ao duplicar template');
                    }
                },
                error: () => {
                    this.showError('Erro de conexão');
                }
            });
        }

        deleteTemplate() {
            if (!this.currentTemplate) {
                this.showError('Selecione um template primeiro');
                return;
            }
            
            if (!confirm('Tem certeza que deseja excluir este template?')) {
                return;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_custom_template',
                    template_id: this.currentTemplate,
                    nonce: customTemplatesData.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.showSuccess('Template excluído com sucesso!');
                        this.clearTemplate();
                        $('.template-selector').val('').trigger('change');
                    } else {
                        this.showError(response.data || 'Erro ao excluir template');
                    }
                },
                error: () => {
                    this.showError('Erro de conexão');
                }
            });
        }

        exportTemplate() {
            if (!this.currentTemplate) {
                this.showError('Selecione um template primeiro');
                return;
            }
            
            const exportUrl = `${ajaxurl}?action=export_custom_template&template_id=${this.currentTemplate}&nonce=${customTemplatesData.nonce}`;
            window.location.href = exportUrl;
        }

        importTemplate() {
            const fileInput = $('<input type="file" accept=".json">');
            fileInput.on('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                
                const formData = new FormData();
                formData.append('action', 'import_custom_template');
                formData.append('template_file', file);
                formData.append('nonce', customTemplatesData.nonce);
                
                this.showLoading();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        if (response.success) {
                            this.showSuccess('Template importado com sucesso!');
                            location.reload();
                        } else {
                            this.showError(response.data || 'Erro ao importar template');
                        }
                    },
                    error: () => {
                        this.showError('Erro de conexão');
                    },
                    complete: () => {
                        this.hideLoading();
                    }
                });
            });
            
            fileInput.click();
        }

        switchEditorTab(e) {
            e.preventDefault();
            
            const tab = $(e.target);
            const tabId = tab.data('tab');
            
            // Update active tab
            $('.editor-tab').removeClass('active');
            tab.addClass('active');
            
            // Show corresponding content
            $('.editor-tab-content').removeClass('active');
            $(`#${tabId}`).addClass('active');
        }

        addSection(e) {
            const sectionType = $(e.target).data('section-type');
            const sectionsContainer = $('.template-sections');
            
            // Create new section HTML
            const sectionHtml = this.createSectionHtml(sectionType);
            sectionsContainer.append(sectionHtml);
            
            this.unsavedChanges = true;
        }

        removeSection(e) {
            if (confirm('Remover esta seção?')) {
                $(e.target).closest('.template-section').remove();
                this.unsavedChanges = true;
            }
        }

        moveSectionUp(e) {
            const section = $(e.target).closest('.template-section');
            const prev = section.prev('.template-section');
            
            if (prev.length) {
                section.insertBefore(prev);
                this.unsavedChanges = true;
            }
        }

        moveSectionDown(e) {
            const section = $(e.target).closest('.template-section');
            const next = section.next('.template-section');
            
            if (next.length) {
                section.insertAfter(next);
                this.unsavedChanges = true;
            }
        }

        createSectionHtml(type) {
            // Return HTML for different section types
            const templates = {
                header: '<div class="template-section" data-type="header">...</div>',
                content: '<div class="template-section" data-type="content">...</div>',
                sidebar: '<div class="template-section" data-type="sidebar">...</div>',
                footer: '<div class="template-section" data-type="footer">...</div>'
            };
            
            return templates[type] || templates.content;
        }

        handleSectionReorder() {
            this.unsavedChanges = true;
        }

        handleFormChange() {
            this.unsavedChanges = true;
        }

        handlePreviewLoad() {
            $('.preview-loading').hide();
        }

        handleBeforeUnload(e) {
            if (this.unsavedChanges) {
                const message = 'Você tem alterações não salvas. Deseja sair mesmo assim?';
                e.returnValue = message;
                return message;
            }
        }

        closeModal() {
            $('.modal').removeClass('active');
        }

        showLoading() {
            $('.loading-overlay').addClass('active');
        }

        hideLoading() {
            $('.loading-overlay').removeClass('active');
        }

        showSuccess(message) {
            this.showNotification(message, 'success');
        }

        showError(message) {
            this.showNotification(message, 'error');
        }

        showNotification(message, type = 'info') {
            const notification = $(`
                <div class="notification notification-${type}">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `);
            
            $('.notifications-container').append(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 5000);
            
            // Manual close
            notification.find('.notification-close').on('click', () => {
                notification.fadeOut(() => notification.remove());
            });
        }
    }

    // Initialize when document is ready
    $(document).ready(() => {
        if ($('.custom-templates-page').length) {
            new TemplateManager();
        }
    });

})(jQuery);