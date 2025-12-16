/* AI Post Generator Pro - Admin Script */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Template selector
        $('#aipg_template').on('change', function() {
            const templateId = $(this).val();
            if (templateId) {
                loadTemplate(templateId);
            }
        });
        
        // Schedule checkbox toggle
        $('#aipg_schedule').on('change', function() {
            if ($(this).is(':checked')) {
                $('#aipg_schedule_options').slideDown();
                // Define data mínima como agora
                const now = new Date();
                now.setMinutes(now.getMinutes() + 10);
                $('#aipg_schedule_date').attr('min', formatDateForInput(now));
            } else {
                $('#aipg_schedule_options').slideUp();
            }
        });
        
        // Form submission
        $('#aipg-generate-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const $resultDiv = $('#aipg-result');
            const $resultContent = $('#aipg-result-content');
            
            $('.aipg-success, .aipg-error').remove();
            
            $submitBtn.prop('disabled', true);
            $submitBtn.html(aipgAjax.strings.generating + ' <span class="aipg-loading"></span>');
            
            const formData = {
                action: 'aipg_generate_post',
                nonce: aipgAjax.nonce,
                topic: $('#aipg_topic').val(),
                keywords: $('#aipg_keywords').val(),
                tone: $('#aipg_tone').val(),
                length: $('#aipg_length').val(),
                language: $('#aipg_language').val(),
                category: $('#aipg_category').val(),
                generate_image: $('#aipg_generate_image').is(':checked') ? 1 : 0,
                auto_tags: $('#aipg_auto_tags').is(':checked') ? 1 : 0,
                seo_optimization: $('#aipg_seo').is(':checked') ? 1 : 0,
                add_internal_links: $('#aipg_links').is(':checked') ? 1 : 0,
                schedule_post: $('#aipg_schedule').is(':checked') ? 1 : 0,
                schedule_date: $('#aipg_schedule_date').val()
            };
            
            $.ajax({
                url: aipgAjax.ajax_url,
                type: 'POST',
                data: formData,
                timeout: 120000,
                success: function(response) {
                    if (response.success) {
                        const successMsg = $('<div class="aipg-success">')
                            .html(response.data.message + 
                                (response.data.edit_url ? 
                                '<div class="aipg-actions">' +
                                '<a href="' + response.data.edit_url + '" class="button button-primary">Editar Post</a>' +
                                '<a href="' + response.data.view_url + '" class="button" target="_blank">Visualizar</a>' +
                                '</div>' : ''));
                        
                        $form.before(successMsg);
                        
                        if (response.data.content) {
                            $resultContent.html(
                                '<h2>' + escapeHtml(response.data.title) + '</h2>' +
                                response.data.content
                            );
                            $resultDiv.slideDown();
                            
                            $('html, body').animate({
                                scrollTop: $resultDiv.offset().top - 100
                            }, 500);
                        }
                        
                        // Reset form se agendado
                        if (formData.schedule_post) {
                            $form[0].reset();
                        }
                        
                    } else {
                        showError(response.data.message || aipgAjax.strings.error);
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = aipgAjax.strings.error;
                    
                    if (status === 'timeout') {
                        errorMsg += ' Tempo limite excedido. Tente novamente.';
                    } else if (xhr.responseJSON && xhr.responseJSON.data) {
                        errorMsg = xhr.responseJSON.data.message;
                    }
                    
                    showError(errorMsg);
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                    $submitBtn.text($submitBtn.data('original-text') || 'Gerar Post');
                }
            });
            
            if (!$submitBtn.data('original-text')) {
                $submitBtn.data('original-text', $submitBtn.text());
            }
        });
        
        // Save template button
        $('#aipg-save-template').on('click', function(e) {
            e.preventDefault();
            
            const templateName = prompt('Nome do template:');
            
            if (!templateName) {
                return;
            }
            
            const templateData = {
                action: 'aipg_save_template',
                nonce: aipgAjax.nonce,
                template_name: templateName,
                tone: $('#aipg_tone').val(),
                length: $('#aipg_length').val(),
                keywords: $('#aipg_keywords').val(),
                category: $('#aipg_category').val(),
                language: $('#aipg_language').val()
            };
            
            $.ajax({
                url: aipgAjax.ajax_url,
                type: 'POST',
                data: templateData,
                success: function(response) {
                    if (response.success) {
                        showSuccess('Template salvo com sucesso!');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showError(response.data.message);
                    }
                }
            });
        });
        
        // Delete template
        $(document).on('click', '.aipg-delete-template', function(e) {
            e.preventDefault();
            
            if (!confirm(aipgAjax.strings.confirm_delete)) {
                return;
            }
            
            const templateId = $(this).data('template-id');
            const $card = $(this).closest('.aipg-template-card');
            
            $.ajax({
                url: aipgAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'aipg_delete_template',
                    nonce: aipgAjax.nonce,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success) {
                        $card.fadeOut(function() {
                            $(this).remove();
                        });
                    } else {
                        showError(response.data.message);
                    }
                }
            });
        });
        
        // Load template from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const templateParam = urlParams.get('template');
        if (templateParam) {
            $('#aipg_template').val(templateParam).trigger('change');
        }
        
        // Helper functions
        function loadTemplate(templateId) {
            // Em produção, isso buscaria os dados do servidor
            // Por enquanto, vamos simular com os dados do select
            const templates = <?php echo json_encode(get_option('aipg_templates', array())); ?>;
            const template = templates[templateId];
            
            if (template) {
                $('#aipg_tone').val(template.tone);
                $('#aipg_length').val(template.length);
                $('#aipg_keywords').val(template.keywords);
                $('#aipg_category').val(template.category);
                $('#aipg_language').val(template.language);
            }
        }
        
        function showError(message) {
            const errorDiv = $('<div class="aipg-error">').text(message);
            $('#aipg-generate-form').before(errorDiv);
            
            $('html, body').animate({
                scrollTop: errorDiv.offset().top - 100
            }, 500);
        }
        
        function showSuccess(message) {
            const successDiv = $('<div class="aipg-success">').text(message);
            $('#aipg-generate-form').before(successDiv);
            
            $('html, body').animate({
                scrollTop: successDiv.offset().top - 100
            }, 500);
        }
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
        
        function formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
        
        // Auto-remove messages after 10 seconds
        $(document).on('click', '.aipg-success, .aipg-error', function() {
            $(this).fadeOut(function() {
                $(this).remove();
            });
        });
        
        setTimeout(function() {
            $('.aipg-success, .aipg-error').fadeOut(function() {
                $(this).remove();
            });
        }, 10000);
        
        // Prevent leaving with unsaved content
        let hasGeneratedContent = false;
        
        $(document).on('ajaxSuccess', function(event, xhr, settings) {
            if (settings.data && settings.data.indexOf('aipg_generate_post') !== -1) {
                hasGeneratedContent = true;
            }
        });
        
        $(window).on('beforeunload', function(e) {
            if (hasGeneratedContent && $('#aipg-result').is(':visible')) {
                const message = 'Você tem conteúdo gerado não salvo. Tem certeza que deseja sair?';
                e.returnValue = message;
                return message;
            }
        });
        
        $(document).on('click', '.aipg-actions a', function() {
            hasGeneratedContent = false;
        });
        
        // Copy to clipboard functionality
        $(document).on('click', '.aipg-copy-content', function(e) {
            e.preventDefault();
            
            const content = $('#aipg-result-content').html();
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(content).select();
            document.execCommand('copy');
            $temp.remove();
            
            $(this).text('Copiado!').addClass('button-primary');
            setTimeout(() => {
                $(this).text('Copiar Conteúdo').removeClass('button-primary');
            }, 2000);
        });
        
        // Add copy button to result
        if ($('#aipg-result-content').length) {
            const $copyBtn = $('<button class="button aipg-copy-content" style="margin-top:10px;">Copiar Conteúdo</button>');
            $('#aipg-result-content').after($copyBtn);
        }
        
        // Character counter for topic field
        $('#aipg_topic').on('input', function() {
            const length = $(this).val().length;
            let $counter = $(this).next('.char-counter');
            
            if (!$counter.length) {
                $counter = $('<span class="char-counter description"></span>');
                $(this).after($counter);
            }
            
            $counter.text(`${length} caracteres`);
            
            if (length > 200) {
                $counter.css('color', '#dc3232');
            } else {
                $counter.css('color', '#646970');
            }
        });
        
        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + Enter para submeter form
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 13) {
                if ($('#aipg-generate-form').is(':visible')) {
                    $('#aipg-generate-form').submit();
                }
            }
        });
        
        // Add tooltips
        $('[data-tooltip]').each(function() {
            const tooltip = $(this).data('tooltip');
            $(this).attr('title', tooltip);
        });
        
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
        
    });
    
})(jQuery);