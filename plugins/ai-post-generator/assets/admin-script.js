/* AI Post Generator Pro - Admin Script */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Provider selector - show/hide API key fields
        function updateProviderFields() {
            const provider = $('#aipg_api_provider').val();
            
            // Esconde todos os campos de API
            $('.api-key-row').removeClass('active');
            
            // Mostra apenas o campo do provedor selecionado
            $('.api-key-row[data-provider="' + provider + '"]').addClass('active');
            
            // Atualiza descrição
            const descriptions = {
                'groq': '🚀 Groq oferece acesso GRATUITO ao Llama 3.3 70B (mais recente) com velocidade ultra-rápida (600+ tokens/seg)',
                'huggingface': '🤗 Hugging Face tem uso GRATUITO e ILIMITADO para modelos open-source',
                'cohere': '💎 Cohere oferece 1000 requisições GRÁTIS por mês - ótimo para começar',
                'mistral': '⚡ Mistral AI dá 5€ de créditos gratuitos para novos usuários',
                'openai': '🤖 OpenAI tem a melhor qualidade mas é pago (~$0.002 por post)',
                'anthropic': '🧠 Claude oferece excelente qualidade mas é mais caro (~$0.015 por post)'
            };
            
            $('#provider-description').text(descriptions[provider] || '');
        }
        
        // Executa ao carregar a página
        updateProviderFields();
        
        // Executa ao mudar o provedor
        $('#aipg_api_provider').on('change', updateProviderFields);
        
        // Informações dos modelos Groq
        function updateGroqModelInfo() {
            const model = $('#aipg_groq_model').val();
            const descriptions = {
                'llama-3.3-70b-versatile': `
                    <strong>🚀 Llama 3.3 70B Versatile</strong><br>
                    • <strong>Lançamento:</strong> Dezembro 2024<br>
                    • <strong>Velocidade:</strong> 600+ tokens/segundo<br>
                    • <strong>Qualidade:</strong> ⭐⭐⭐⭐⭐ (Superior)<br>
                    • <strong>Contexto:</strong> 8K tokens<br>
                    • <strong>Melhor para:</strong> Qualquer tipo de conteúdo<br>
                    • <strong>Destaque:</strong> Mais preciso e criativo que 3.1
                `,
                'llama-3.1-70b-versatile': `
                    <strong>⚡ Llama 3.1 70B Versatile</strong><br>
                    • <strong>Lançamento:</strong> Julho 2024<br>
                    • <strong>Velocidade:</strong> 500+ tokens/segundo<br>
                    • <strong>Qualidade:</strong> ⭐⭐⭐⭐ (Muito Boa)<br>
                    • <strong>Contexto:</strong> 8K tokens<br>
                    • <strong>Melhor para:</strong> Uso geral<br>
                    • <strong>Destaque:</strong> Versão anterior estável
                `,
                'meta-llama/llama-4-scout-17b-16e-instruct': `
                    <strong>🔬 Llama 4 Scout 17B</strong><br>
                    • <strong>Status:</strong> Experimental / Preview<br>
                    • <strong>Velocidade:</strong> 800+ tokens/segundo ⚡⚡⚡<br>
                    • <strong>Qualidade:</strong> ⭐⭐⭐⭐ (Em Teste)<br>
                    • <strong>Contexto:</strong> 4K tokens<br>
                    • <strong>Melhor para:</strong> Conteúdo curto e rápido<br>
                    • <strong>Destaque:</strong> MUITO RÁPIDO - Modelo menor<br>
                    • <strong>Aviso:</strong> Pode ter inconsistências
                `,
                'mixtral-8x7b-32768': `
                    <strong>🎯 Mixtral 8x7B</strong><br>
                    • <strong>Lançamento:</strong> Dezembro 2023<br>
                    • <strong>Velocidade:</strong> 400+ tokens/segundo<br>
                    • <strong>Qualidade:</strong> ⭐⭐⭐⭐ (Excelente)<br>
                    • <strong>Contexto:</strong> 32K tokens 🔥<br>
                    • <strong>Melhor para:</strong> Textos muito longos<br>
                    • <strong>Destaque:</strong> Maior contexto disponível
                `
            };
            
            $('#groq-model-description').html(descriptions[model] || '');
        }
        
        // Atualiza ao carregar e ao mudar
        if ($('#aipg_groq_model').length) {
            updateGroqModelInfo();
            $('#aipg_groq_model').on('change', updateGroqModelInfo);
        }
        
        // Image provider selector
        function updateImageProviderFields() {
            const provider = $('#aipg_image_provider').val();
            
            $('.image-api-key-row').removeClass('active');
            $('.image-api-key-row[data-provider="' + provider + '"]').addClass('active');
            
            const descriptions = {
                'unsplash': '📷 Fotos profissionais gratuitas de alta qualidade',
                'pexels': '🎨 Maior biblioteca gratuita - 200 req/hora',
                'pixabay': '🖼️ Sem limites práticos - Melhor para alto volume',
                'dall-e': '🤖 IA gera imagens únicas mas custa $0.04/imagem',
                'stability': '🎭 IA de alta qualidade - 25 créditos grátis',
                'pollinations': '🌺 IA 100% GRÁTIS e ILIMITADO - Melhor opção!'
            };
            
            $('#image-provider-description').text(descriptions[provider] || '');
        }
        
        if ($('#aipg_image_provider').length) {
            updateImageProviderFields();
            $('#aipg_image_provider').on('change', updateImageProviderFields);
        }
        
        // Preset de tamanhos de imagem
        $('.aipg-preset-size').on('click', function(e) {
            e.preventDefault();
            const width = $(this).data('width');
            const height = $(this).data('height');
            
            $('#aipg_image_width').val(width);
            $('#aipg_image_height').val(height);
            
            updateImagePreview();
            
            // Feedback visual
            $('.aipg-preset-size').removeClass('button-primary');
            $(this).addClass('button-primary');
        });
        
        // Atualiza preview das dimensões
        function updateImagePreview() {
            const width = parseInt($('#aipg_image_width').val()) || 1920;
            const height = parseInt($('#aipg_image_height').val()) || 1080;
            
            const ratio = (width / height).toFixed(2);
            const megapixels = ((width * height) / 1000000).toFixed(1);
            
            let ratioName = '';
            if (Math.abs(ratio - 1.78) < 0.05) ratioName = '16:9 (widescreen)';
            else if (Math.abs(ratio - 1.33) < 0.05) ratioName = '4:3 (padrão)';
            else if (Math.abs(ratio - 1) < 0.05) ratioName = '1:1 (quadrado)';
            else if (Math.abs(ratio - 0.56) < 0.05) ratioName = '9:16 (vertical)';
            else ratioName = 'customizado';
            
            let useCase = '';
            if (width >= 3840) useCase = 'Ideal para: Impressão grande, banners';
            else if (width >= 1920) useCase = 'Ideal para: Blog, redes sociais, web';
            else if (width >= 1280) useCase = 'Ideal para: Thumbnails, miniaturas';
            else useCase = 'Ideal para: Ícones, avatares';
            
            $('#aipg-preview-size').html(`<strong>Tamanho:</strong> ${width}×${height}px (${megapixels}MP)`);
            $('#aipg-preview-ratio').html(`<strong>Proporção:</strong> ${ratio} ${ratioName}`);
            $('#aipg-preview-use').html(`<strong>${useCase}</strong>`);
            $('#aipg-image-preview').slideDown();
        }
        
        if ($('#aipg_image_width').length) {
            $('#aipg_image_width, #aipg_image_height').on('input', updateImagePreview);
            updateImagePreview(); // Mostra ao carregar
        }
        
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
            // Busca os dados do template via AJAX
            $.ajax({
                url: aipgAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'aipg_get_template',
                    nonce: aipgAjax.nonce,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const template = response.data;
                        $('#aipg_tone').val(template.tone);
                        $('#aipg_length').val(template.length);
                        $('#aipg_keywords').val(template.keywords);
                        $('#aipg_category').val(template.category);
                        $('#aipg_language').val(template.language);
                    }
                }
            });
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