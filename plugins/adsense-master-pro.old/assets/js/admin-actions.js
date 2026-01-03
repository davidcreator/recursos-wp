/**
 * AdSense Master Pro v3.0
 * Actions Handler - Adicionar, Importar e Exportar Anúncios
 * 
 * @package AdSense Master Pro
 * @version 3.0.0
 */

(function($) {
    'use strict';

    const AdminActions = {
        /**
         * Inicialização
         */
        init: function() {
            this.bindAddAd();
            this.bindImportAds();
            this.bindExportAds();
            this.bindModalClose();
            this.bindFormSubmit();
            this.bindTemplates();
        },

        /**
         * Adicionar Novo Anúncio
         */
        bindAddAd: function() {
            $(document).on('click', '#add-new-ad, #create-first-ad', function(e) {
                e.preventDefault();
                AdminActions.openAddModal();
            });
        },

        openAddModal: function() {
            $('#amp-ad-form')[0].reset();
            $('#amp-modal-title').text('Adicionar Novo Anúncio');
            $('#amp-ad-modal').fadeIn();
            $('#ad-name').focus();
        },

        /**
         * Importar Anúncios
         */
        bindImportAds: function() {
            $(document).on('click', '#import-ads', function(e) {
                e.preventDefault();
                AdminActions.openImportDialog();
            });
        },

        openImportDialog: function() {
            const fileInput = $('<input type="file" accept=".json" id="amp-import-file" style="display: none;">');
            
            fileInput.on('change', function() {
                const file = this.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const data = JSON.parse(e.target.result);
                        AdminActions.processImport(data);
                    } catch (error) {
                        alert('❌ Erro ao ler arquivo: ' + error.message);
                    }
                };
                reader.readAsText(file);
            });

            fileInput.click();
        },

        processImport: function(data) {
            if (!Array.isArray(data)) {
                alert('❌ Formato de arquivo inválido. Deve ser um array de anúncios.');
                return;
            }

            if (data.length === 0) {
                alert('⚠️ Arquivo vazio.');
                return;
            }

            // Validar primeira entrada
            if (!data[0].name || !data[0].code || !data[0].position) {
                alert('❌ Arquivo inválido. Campos obrigatórios: name, code, position');
                return;
            }

            // Confirmar importação
            const confirmMsg = `Tem certeza que deseja importar ${data.length} anúncio(s)?\n\nIsso pode levar alguns segundos.`;
            
            if (!confirm(confirmMsg)) {
                return;
            }

            // Importar com barra de progresso
            AdminActions.importAdsWithProgress(data);
        },

        importAdsWithProgress: function(ads) {
            const total = ads.length;
            let imported = 0;
            let failed = 0;

            // Criar modal de progresso
            const progressHtml = `
                <div id="amp-import-progress" class="amp-modal" style="display: block;">
                    <div class="amp-modal-content" style="max-width: 500px;">
                        <div class="amp-modal-header">
                            <h2>Importando Anúncios...</h2>
                        </div>
                        <div class="amp-modal-body">
                            <div class="amp-progress-bar">
                                <div class="amp-progress-fill" style="width: 0%;"></div>
                            </div>
                            <p id="amp-import-status" style="text-align: center; margin-top: 20px;">
                                0/${total} importados
                            </p>
                            <ul id="amp-import-log" style="max-height: 300px; overflow-y: auto; font-size: 12px; margin-top: 20px;">
                            </ul>
                        </div>
                    </div>
                </div>
            `;

            $(progressHtml).appendTo('body');

            // Importar anúncios sequencialmente
            const importNext = function(index) {
                if (index >= total) {
                    // Conclusão
                    $('#amp-import-progress').fadeOut(function() {
                        $(this).remove();
                    });

                    const message = `✅ Importação concluída!\n\n${imported} anúncio(s) importado(s)\n${failed} erro(s)`;
                    alert(message);

                    // Recarregar página
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                    return;
                }

                const ad = ads[index];
                
                // Fazer requisição AJAX
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'amp_import_ad',
                        nonce: amp_ajax.nonce,
                        ad: JSON.stringify(ad)
                    },
                    success: function(response) {
                        imported++;
                        const percent = Math.round((imported + failed) / total * 100);
                        
                        $('#amp-progress-fill').css('width', percent + '%');
                        $('#amp-import-status').text(`${imported + failed}/${total} processados`);
                        $('#amp-import-log').append(`<li style="color: green;">✓ ${ad.name}</li>`);
                        
                        importNext(index + 1);
                    },
                    error: function() {
                        failed++;
                        const percent = Math.round((imported + failed) / total * 100);
                        
                        $('#amp-progress-fill').css('width', percent + '%');
                        $('#amp-import-status').text(`${imported + failed}/${total} processados`);
                        $('#amp-import-log').append(`<li style="color: red;">✗ ${ad.name}</li>`);
                        
                        importNext(index + 1);
                    }
                });
            };

            importNext(0);
        },

        /**
         * Exportar Anúncios
         */
        bindExportAds: function() {
            $(document).on('click', '#export-ads', function(e) {
                e.preventDefault();
                AdminActions.exportAllAds();
            });
        },

        exportAllAds: function() {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'amp_export_ads',
                    nonce: amp_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Fazer download do JSON
                        const json = JSON.stringify(response.data, null, 2);
                        const blob = new Blob([json], { type: 'application/json' });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'adsense-master-pro-backup-' + new Date().toISOString().split('T')[0] + '.json';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);

                        alert(`✅ ${response.data.length} anúncio(s) exportado(s) com sucesso!`);
                    } else {
                        alert('❌ Erro: ' + response.data);
                    }
                },
                error: function() {
                    alert('❌ Erro ao exportar anúncios.');
                }
            });
        },

        /**
         * Fechar Modal
         */
        bindModalClose: function() {
            $(document).on('click', '.amp-modal-close, #amp-cancel-ad', function(e) {
                e.preventDefault();
                $('#amp-ad-modal').fadeOut();
                $('#amp-preview-modal').fadeOut();
            });

            $(document).on('click', function(e) {
                if ($(e.target).hasClass('amp-modal') && !$(e.target).find('.amp-modal-content').is(':contains(' + e.target + ')')) {
                    $('.amp-modal').fadeOut();
                }
            });
        },

        /**
         * Enviar Formulário
         */
        bindFormSubmit: function() {
            $(document).on('submit', '#amp-ad-form', function(e) {
                e.preventDefault();
                AdminActions.saveAd($(this));
            });
        },

        saveAd: function($form) {
            const formData = {
                name: $('#ad-name').val(),
                code: $('#ad-code').val(),
                position: $('#ad-position').val(),
                alignment: $('#ad-alignment').val(),
                css_selector: $('#css-selector').val(),
                show_on_desktop: $('input[name="show_on_desktop"]:checked').length ? 1 : 0,
                show_on_mobile: $('input[name="show_on_mobile"]:checked').length ? 1 : 0,
                show_on_homepage: $('input[name="show_on_homepage"]:checked').length ? 1 : 0,
                show_on_posts: $('input[name="show_on_posts"]:checked').length ? 1 : 0,
                show_on_pages: $('input[name="show_on_pages"]:checked').length ? 1 : 0,
            };

            // Validação
            if (!formData.name || !formData.code || !formData.position) {
                alert('❌ Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            // Botão de envio
            const $submitBtn = $('#amp-save-ad');
            const originalText = $submitBtn.text();
            $submitBtn.prop('disabled', true).text('Salvando...');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'amp_save_ad',
                    nonce: amp_ajax.nonce,
                    ...formData
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ Anúncio salvo com sucesso!');
                        $('#amp-ad-modal').fadeOut();
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('❌ Erro: ' + response.data);
                    }
                },
                error: function() {
                    alert('❌ Erro ao salvar anúncio.');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Templates de Código
         */
        bindTemplates: function() {
            $(document).on('click', '.amp-code-templates .button', function(e) {
                e.preventDefault();
                const template = $(this).data('template');
                AdminActions.insertTemplate(template);
            });
        },

        insertTemplate: function(template) {
            let code = '';

            switch (template) {
                case 'adsense':
                    code = `<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-xxxxxxxxxxxxxxxxxx" crossorigin="anonymous"></script>
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-xxxxxxxxxxxxxxxxxx"
     data-ad-slot="xxxxxxxxxx"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>`;
                    break;

                case 'banner':
                    code = `<a href="https://seu-link.com" target="_blank" rel="noopener noreferrer">
    <img src="https://seu-dominio.com/imagem-banner.jpg" alt="Banner" style="max-width: 100%; height: auto; display: block;">
</a>`;
                    break;

                case 'responsive':
                    code = `<div style="text-align: center;">
    <img src="https://seu-dominio.com/imagem-responsive.jpg" 
         alt="Anúncio" 
         style="max-width: 100%; height: auto; display: block; margin: 20px auto;"
         srcset="https://seu-dominio.com/imagem-300.jpg 300w, https://seu-dominio.com/imagem-600.jpg 600w, https://seu-dominio.com/imagem-900.jpg 900w"
         sizes="(max-width: 600px) 100vw, (max-width: 1000px) 80vw, 728px">
</div>`;
                    break;
            }

            $('#ad-code').val(code).focus();
            $('#ad-code').trigger('change');
        }
    };

    // Inicializar quando DOM estiver pronto
    $(document).ready(function() {
        AdminActions.init();
    });

    // Expor globalmente
    window.AMP_AdminActions = AdminActions;

})(jQuery);

// Estilos para progresso
jQuery(function($) {
    const style = document.createElement('style');
    style.textContent = `
        .amp-progress-bar {
            width: 100%;
            height: 30px;
            background: #f0f0f0;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        
        .amp-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #0073aa, #005a87);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
    `;
    document.head.appendChild(style);
});