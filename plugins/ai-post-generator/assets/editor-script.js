/* AI Post Generator - Editor Script */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Verifica se está no editor
        if (!$('#aipg_generate_content').length) {
            return;
        }
        
        const $generateBtn = $('#aipg_generate_content');
        const $improveBtn = $('#aipg_improve_content');
        const $status = $('#aipg_editor_status');
        const $topicField = $('#aipg_editor_topic');
        const $lengthField = $('#aipg_editor_length');
        const $toneField = $('#aipg_editor_tone');
        const $imageCheckbox = $('#aipg_editor_image');
        
        // Detecta editor (Clássico ou Gutenberg)
        const isGutenberg = typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor');
        
        /**
         * Gera conteúdo com IA
         */
        $generateBtn.on('click', function(e) {
            e.preventDefault();
            
            // Pega o título do post
            let topic = $topicField.val().trim();
            
            if (!topic) {
                if (isGutenberg) {
                    topic = wp.data.select('core/editor').getEditedPostAttribute('title');
                } else {
                    topic = $('#title').val();
                }
            }
            
            if (!topic) {
                showStatus('error', aipgEditor.strings.fill_topic);
                return;
            }
            
            // Desabilita botão
            $generateBtn.prop('disabled', true);
            $generateBtn.html('<span class="aipg-spinner"></span>' + aipgEditor.strings.generating);
            
            showStatus('loading', aipgEditor.strings.generating);
            
            // Prepara dados
            const data = {
                action: 'aipg_generate_content_only',
                nonce: aipgEditor.nonce,
                topic: topic,
                length: $lengthField.val(),
                tone: $toneField.val(),
                language: 'pt-br'
            };
            
            // Faz requisição
            $.ajax({
                url: aipgEditor.ajax_url,
                type: 'POST',
                data: data,
                timeout: 120000,
                success: function(response) {
                    if (response.success) {
                        // Insere conteúdo no editor
                        insertContent(response.data.content);
                        
                        // Atualiza título se vazio
                        if (isGutenberg) {
                            const currentTitle = wp.data.select('core/editor').getEditedPostAttribute('title');
                            if (!currentTitle) {
                                wp.data.dispatch('core/editor').editPost({
                                    title: response.data.title
                                });
                            }
                        } else {
                            const $titleField = $('#title');
                            if (!$titleField.val()) {
                                $titleField.val(response.data.title);
                            }
                        }
                        
                        // Gera imagem se solicitado
                        if ($imageCheckbox.is(':checked')) {
                            generateFeaturedImage(topic);
                        }
                        
                        showStatus('success', aipgEditor.strings.success);
                        $improveBtn.show();
                        
                    } else {
                        showStatus('error', response.data.message || aipgEditor.strings.error);
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = aipgEditor.strings.error;
                    
                    if (status === 'timeout') {
                        errorMsg += ' Tempo limite excedido.';
                    } else if (xhr.responseJSON && xhr.responseJSON.data) {
                        errorMsg = xhr.responseJSON.data.message;
                    }
                    
                    showStatus('error', errorMsg);
                },
                complete: function() {
                    // Reabilita botão
                    $generateBtn.prop('disabled', false);
                    $generateBtn.html('<span class="dashicons dashicons-edit"></span> Gerar Conteúdo');
                }
            });
        });
        
        /**
         * Melhora conteúdo existente
         */
        $improveBtn.on('click', function(e) {
            e.preventDefault();
            
            let currentContent = '';
            
            if (isGutenberg) {
                currentContent = wp.data.select('core/editor').getEditedPostAttribute('content');
            } else {
                if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
                    currentContent = tinyMCE.activeEditor.getContent();
                } else {
                    currentContent = $('#content').val();
                }
            }
            
            if (!currentContent) {
                showStatus('error', 'Não há conteúdo para melhorar.');
                return;
            }
            
            $improveBtn.prop('disabled', true);
            showStatus('loading', 'Melhorando texto...');
            
            // Aqui você pode implementar lógica de melhoria
            // Por enquanto, vamos apenas mostrar uma mensagem
            setTimeout(function() {
                showStatus('success', 'Use o botão "Gerar Conteúdo" novamente para criar uma nova versão.');
                $improveBtn.prop('disabled', false);
            }, 1000);
        });
        
        /**
         * Insere conteúdo no editor
         */
        function insertContent(content) {
            if (isGutenberg) {
                // Gutenberg
                const blocks = wp.blocks.parse(content);
                wp.data.dispatch('core/block-editor').insertBlocks(blocks);
            } else {
                // Editor Clássico
                if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
                    tinyMCE.activeEditor.setContent(content);
                } else {
                    $('#content').val(content);
                }
            }
        }
        
        /**
         * Gera imagem destacada
         */
        function generateFeaturedImage(topic) {
            $.ajax({
                url: aipgEditor.ajax_url,
                type: 'POST',
                data: {
                    action: 'aipg_generate_image',
                    nonce: aipgEditor.nonce,
                    topic: topic,
                    post_id: aipgEditor.post_id
                },
                success: function(response) {
                    if (response.success && response.data.image_id) {
                        // Atualiza imagem destacada
                        if (isGutenberg) {
                            wp.data.dispatch('core/editor').editPost({
                                featured_media: response.data.image_id
                            });
                        } else {
                            // Recarrega meta box de imagem destacada
                            location.reload();
                        }
                    }
                }
            });
        }
        
        /**
         * Mostra status
         */
        function showStatus(type, message) {
            $status.removeClass('loading success error');
            $status.addClass(type);
            
            let icon = '';
            if (type === 'loading') {
                icon = '<span class="aipg-spinner"></span>';
            } else if (type === 'success') {
                icon = '<span class="dashicons dashicons-yes-alt"></span>';
            } else if (type === 'error') {
                icon = '<span class="dashicons dashicons-warning"></span>';
            }
            
            $status.html(icon + message);
            $status.show();
            
            // Auto-hide após 5 segundos (exceto loading)
            if (type !== 'loading') {
                setTimeout(function() {
                    $status.fadeOut();
                }, 5000);
            }
        }
        
        /**
         * Carrega template
         */
        $('#aipg_editor_template').on('change', function() {
            const templateId = $(this).val();
            
            if (!templateId) {
                return;
            }
            
            $.ajax({
                url: aipgEditor.ajax_url,
                type: 'POST',
                data: {
                    action: 'aipg_get_template',
                    nonce: aipgEditor.nonce,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const template = response.data;
                        $toneField.val(template.tone);
                        $lengthField.val(template.length);
                        
                        if (template.keywords) {
                            $topicField.attr('placeholder', 'Palavras-chave: ' + template.keywords);
                        }
                        
                        showStatus('success', 'Template carregado!');
                    }
                }
            });
        });
        
        /**
         * Atalho de teclado: Ctrl/Cmd + Shift + G
         */
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.keyCode === 71) {
                e.preventDefault();
                $generateBtn.click();
            }
        });
        
        /**
         * Pré-visualização do tamanho
         */
        $lengthField.on('change', function() {
            const lengths = {
                'short': '300-500 palavras (~2-3 minutos de leitura)',
                'medium': '500-800 palavras (~3-5 minutos de leitura)',
                'long': '800-1200 palavras (~5-8 minutos de leitura)',
                'verylong': '1200-2000 palavras (~8-12 minutos de leitura)'
            };
            
            const desc = lengths[$(this).val()];
            let $description = $(this).next('.description');
            
            if (!$description.length) {
                $description = $('<p class="description"></p>');
                $(this).after($description);
            }
            
            $description.text(desc);
        });
        
        // Mostra descrição inicial
        $lengthField.trigger('change');
        
        /**
         * Tooltip de ajuda
         */
        $('[data-tooltip]').each(function() {
            $(this).attr('title', $(this).data('tooltip'));
        });
        
        /**
         * Aviso de não salvar
         */
        let contentGenerated = false;
        
        $(document).on('ajaxSuccess', function(event, xhr, settings) {
            if (settings.data && settings.data.indexOf('aipg_generate_content_only') !== -1) {
                contentGenerated = true;
            }
        });
        
        // Reseta ao salvar
        if (isGutenberg) {
            wp.data.subscribe(function() {
                const isSaving = wp.data.select('core/editor').isSavingPost();
                if (isSaving) {
                    contentGenerated = false;
                }
            });
        } else {
            $('#publish, #save-post').on('click', function() {
                contentGenerated = false;
            });
        }
        
    });
    
})(jQuery);