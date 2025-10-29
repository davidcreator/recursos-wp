/**
 * Advanced Theme Options JavaScript
 */
(function($) {
    'use strict';

    var NosfirNewsAdvancedOptions = {
        
        init: function() {
            this.bindEvents();
            this.initTabs();
            this.initColorPickers();
            this.initRangeSliders();
            this.initMediaUploaders();
            this.initTooltips();
        },

        bindEvents: function() {
            // Tab navigation
            $('.nosfirnews-admin-nav a').on('click', this.handleTabClick);
            
            // Tool actions
            $('.nosfirnews-clear-cache').on('click', this.clearCache);
            $('.nosfirnews-reset-options').on('click', this.resetOptions);
            $('.nosfirnews-export-options').on('click', this.exportOptions);
            $('.nosfirnews-import-options').on('click', this.importOptions);
            $('.nosfirnews-system-info').on('click', this.showSystemInfo);
            $('.nosfirnews-optimize-db').on('click', this.optimizeDatabase);
            $('.nosfirnews-cleanup-orphans').on('click', this.cleanupOrphans);
            
            // Modal controls
            $('.nosfirnews-modal-close, .nosfirnews-modal').on('click', this.closeModal);
            $('.nosfirnews-modal-content').on('click', function(e) {
                e.stopPropagation();
            });
            
            // Form validation
            $('form').on('submit', this.validateForm);
            
            // Auto-save functionality
            $('.nosfirnews-field input, .nosfirnews-field select, .nosfirnews-field textarea').on('change', this.autoSave);
        },

        initTabs: function() {
            var hash = window.location.hash;
            var activeTab = hash ? hash.substring(1) : 'performance';
            
            this.showTab(activeTab);
        },

        handleTabClick: function(e) {
            e.preventDefault();
            var tabId = $(this).attr('href').substring(1);
            NosfirNewsAdvancedOptions.showTab(tabId);
            window.location.hash = tabId;
        },

        showTab: function(tabId) {
            $('.nosfirnews-admin-nav a').removeClass('active');
            $('.nosfirnews-tab-content').removeClass('active');
            
            $('.nosfirnews-admin-nav a[href="#' + tabId + '"]').addClass('active');
            $('#' + tabId).addClass('active');
        },

        initColorPickers: function() {
            if (typeof wp !== 'undefined' && wp.colorPicker) {
                $('.nosfirnews-color-field').wpColorPicker({
                    change: function(event, ui) {
                        $(this).trigger('change');
                    }
                });
            }
        },

        initRangeSliders: function() {
            $('.nosfirnews-range-slider input[type="range"]').on('input', function() {
                var value = $(this).val();
                var unit = $(this).data('unit') || '';
                $(this).siblings('.nosfirnews-range-value').text(value + unit);
            });
        },

        initMediaUploaders: function() {
            $('.nosfirnews-media-upload').on('click', function(e) {
                e.preventDefault();
                
                var button = $(this);
                var input = button.siblings('input');
                var preview = button.siblings('.nosfirnews-media-preview');
                
                var mediaUploader = wp.media({
                    title: 'Selecionar Imagem',
                    button: {
                        text: 'Usar esta imagem'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    input.val(attachment.url);
                    if (preview.length) {
                        preview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;">');
                    }
                    input.trigger('change');
                });
                
                mediaUploader.open();
            });
            
            $('.nosfirnews-media-remove').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var input = button.siblings('input');
                var preview = button.siblings('.nosfirnews-media-preview');
                
                input.val('');
                preview.empty();
                input.trigger('change');
            });
        },

        initTooltips: function() {
            $('.nosfirnews-tooltip').on('mouseenter', function() {
                var tooltip = $(this).data('tooltip');
                if (tooltip) {
                    $('<div class="nosfirnews-tooltip-content">' + tooltip + '</div>')
                        .appendTo('body')
                        .fadeIn(200);
                }
            }).on('mouseleave', function() {
                $('.nosfirnews-tooltip-content').remove();
            });
        },

        clearCache: function(e) {
            e.preventDefault();
            
            if (!confirm(nosfirnews_ajax.strings.confirm_clear_cache)) {
                return;
            }
            
            var button = $(this);
            button.prop('disabled', true).text('Limpando...');
            
            $.ajax({
                url: nosfirnews_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nosfirnews_clear_cache',
                    nonce: nosfirnews_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        NosfirNewsAdvancedOptions.showNotice('success', response.data);
                    } else {
                        NosfirNewsAdvancedOptions.showNotice('error', response.data || nosfirnews_ajax.strings.error);
                    }
                },
                error: function() {
                    NosfirNewsAdvancedOptions.showNotice('error', nosfirnews_ajax.strings.error);
                },
                complete: function() {
                    button.prop('disabled', false).text('Limpar Cache');
                }
            });
        },

        resetOptions: function(e) {
            e.preventDefault();
            
            if (!confirm(nosfirnews_ajax.strings.confirm_reset)) {
                return;
            }
            
            var button = $(this);
            button.prop('disabled', true).text('Restaurando...');
            
            $.ajax({
                url: nosfirnews_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nosfirnews_reset_options',
                    nonce: nosfirnews_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        NosfirNewsAdvancedOptions.showNotice('success', response.data);
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        NosfirNewsAdvancedOptions.showNotice('error', response.data || nosfirnews_ajax.strings.error);
                    }
                },
                error: function() {
                    NosfirNewsAdvancedOptions.showNotice('error', nosfirnews_ajax.strings.error);
                },
                complete: function() {
                    button.prop('disabled', false).text('Restaurar Padrões');
                }
            });
        },

        exportOptions: function(e) {
            e.preventDefault();
            
            var button = $(this);
            button.prop('disabled', true).text('Exportando...');
            
            $.ajax({
                url: nosfirnews_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nosfirnews_export_options',
                    nonce: nosfirnews_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var dataStr = JSON.stringify(response.data, null, 2);
                        var dataBlob = new Blob([dataStr], {type: 'application/json'});
                        var url = URL.createObjectURL(dataBlob);
                        var link = document.createElement('a');
                        link.href = url;
                        link.download = 'nosfirnews-options-' + new Date().toISOString().slice(0, 10) + '.json';
                        link.click();
                        URL.revokeObjectURL(url);
                        
                        NosfirNewsAdvancedOptions.showNotice('success', 'Configurações exportadas com sucesso!');
                    } else {
                        NosfirNewsAdvancedOptions.showNotice('error', response.data || nosfirnews_ajax.strings.error);
                    }
                },
                error: function() {
                    NosfirNewsAdvancedOptions.showNotice('error', nosfirnews_ajax.strings.error);
                },
                complete: function() {
                    button.prop('disabled', false).text('Exportar Configurações');
                }
            });
        },

        importOptions: function(e) {
            e.preventDefault();
            
            var input = document.createElement('input');
            input.type = 'file';
            input.accept = '.json';
            
            input.onchange = function(event) {
                var file = event.target.files[0];
                if (!file) return;
                
                var reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        var importData = JSON.parse(e.target.result);
                        NosfirNewsAdvancedOptions.processImport(importData);
                    } catch (error) {
                        NosfirNewsAdvancedOptions.showNotice('error', 'Arquivo JSON inválido.');
                    }
                };
                reader.readAsText(file);
            };
            
            input.click();
        },

        processImport: function(importData) {
            if (!confirm('Tem certeza que deseja importar estas configurações? As configurações atuais serão substituídas.')) {
                return;
            }
            
            $.ajax({
                url: nosfirnews_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nosfirnews_import_options',
                    nonce: nosfirnews_ajax.nonce,
                    import_data: JSON.stringify(importData)
                },
                success: function(response) {
                    if (response.success) {
                        NosfirNewsAdvancedOptions.showNotice('success', response.data);
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        NosfirNewsAdvancedOptions.showNotice('error', response.data || nosfirnews_ajax.strings.error);
                    }
                },
                error: function() {
                    NosfirNewsAdvancedOptions.showNotice('error', nosfirnews_ajax.strings.error);
                }
            });
        },

        showSystemInfo: function(e) {
            e.preventDefault();
            
            var systemInfo = NosfirNewsAdvancedOptions.getSystemInfo();
            var modal = $('#nosfirnews-system-info-modal');
            
            modal.find('.nosfirnews-system-info').text(systemInfo);
            modal.show();
        },

        getSystemInfo: function() {
            var info = '';
            info += 'WordPress Version: ' + (window.wp_version || 'Unknown') + '\n';
            info += 'Theme: NosfirNews\n';
            info += 'Browser: ' + navigator.userAgent + '\n';
            info += 'Screen Resolution: ' + screen.width + 'x' + screen.height + '\n';
            info += 'Viewport: ' + window.innerWidth + 'x' + window.innerHeight + '\n';
            info += 'User Agent: ' + navigator.userAgent + '\n';
            info += 'Language: ' + navigator.language + '\n';
            info += 'Platform: ' + navigator.platform + '\n';
            info += 'Cookie Enabled: ' + navigator.cookieEnabled + '\n';
            info += 'Online: ' + navigator.onLine + '\n';
            info += 'Timestamp: ' + new Date().toISOString() + '\n';
            
            return info;
        },

        closeModal: function(e) {
            if (e.target === this || $(e.target).hasClass('nosfirnews-modal-close')) {
                $('.nosfirnews-modal').hide();
            }
        },

        validateForm: function(e) {
            var isValid = true;
            var errors = [];
            
            // Validate required fields
            $(this).find('[required]').each(function() {
                if (!$(this).val().trim()) {
                    isValid = false;
                    errors.push('O campo "' + $(this).prev('label').text() + '" é obrigatório.');
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            // Validate email fields
            $(this).find('input[type="email"]').each(function() {
                var email = $(this).val().trim();
                if (email && !NosfirNewsAdvancedOptions.isValidEmail(email)) {
                    isValid = false;
                    errors.push('O campo "' + $(this).prev('label').text() + '" deve conter um email válido.');
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            // Validate URL fields
            $(this).find('input[type="url"]').each(function() {
                var url = $(this).val().trim();
                if (url && !NosfirNewsAdvancedOptions.isValidUrl(url)) {
                    isValid = false;
                    errors.push('O campo "' + $(this).prev('label').text() + '" deve conter uma URL válida.');
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                NosfirNewsAdvancedOptions.showNotice('error', errors.join('<br>'));
                return false;
            }
            
            return true;
        },

        autoSave: function() {
            var field = $(this);
            var form = field.closest('form');
            
            // Debounce auto-save
            clearTimeout(form.data('autoSaveTimeout'));
            form.data('autoSaveTimeout', setTimeout(function() {
                // Auto-save logic here if needed
                console.log('Auto-saving field:', field.attr('name'));
            }, 2000));
        },

        showNotice: function(type, message) {
            var notice = $('<div class="nosfirnews-notice ' + type + '">' + message + '</div>');
            
            // Remove existing notices
            $('.nosfirnews-notice').remove();
            
            // Add new notice
            var container = $('.wrap.nosfirnews-advanced-options');
            if (container.length) {
                container.prepend(notice);
            } else {
                $('body').prepend(notice);
            }
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                notice.fadeOut(function() {
                    notice.remove();
                });
            }, 5000);
            
            // Scroll to top
            var target = container.length ? container : $('body');
            $('html, body').animate({
                scrollTop: target.offset() ? (target.offset().top - 50) : 0
            }, 500);
        },

        optimizeDatabase: function(e) {
            e.preventDefault();

            if (!confirm((nosfirnews_ajax.strings && nosfirnews_ajax.strings.confirm_optimize) ? nosfirnews_ajax.strings.confirm_optimize : 'Deseja otimizar o banco de dados agora?')) {
                return;
            }

            var button = $(this);
            button.prop('disabled', true).text('Otimizando...');

            $.ajax({
                url: nosfirnews_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nosfirnews_optimize_database',
                    nonce: nosfirnews_ajax.nonce
                },
                success: function(response) {
                    if (response && response.success) {
                        NosfirNewsAdvancedOptions.showNotice('success', response.data || (nosfirnews_ajax.strings ? nosfirnews_ajax.strings.success : 'Sucesso.'));
                    } else {
                        NosfirNewsAdvancedOptions.showNotice('error', (response && response.data) || (nosfirnews_ajax.strings ? nosfirnews_ajax.strings.error : 'Erro.'));
                    }
                },
                error: function() {
                    NosfirNewsAdvancedOptions.showNotice('error', (nosfirnews_ajax.strings ? nosfirnews_ajax.strings.error : 'Erro.'));
                },
                complete: function() {
                    button.prop('disabled', false).text('Otimizar Banco');
                }
            });
        },

        cleanupOrphans: function(e) {
            e.preventDefault();

            if (!confirm((nosfirnews_ajax.strings && nosfirnews_ajax.strings.confirm_cleanup) ? nosfirnews_ajax.strings.confirm_cleanup : 'Deseja limpar dados órfãos agora?')) {
                return;
            }

            var button = $(this);
            button.prop('disabled', true).text('Limpando...');

            $.ajax({
                url: nosfirnews_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nosfirnews_cleanup_orphaned_data',
                    nonce: nosfirnews_ajax.nonce
                },
                success: function(response) {
                    if (response && response.success) {
                        NosfirNewsAdvancedOptions.showNotice('success', response.data || (nosfirnews_ajax.strings ? nosfirnews_ajax.strings.success : 'Sucesso.'));
                    } else {
                        NosfirNewsAdvancedOptions.showNotice('error', (response && response.data) || (nosfirnews_ajax.strings ? nosfirnews_ajax.strings.error : 'Erro.'));
                    }
                },
                error: function() {
                    NosfirNewsAdvancedOptions.showNotice('error', (nosfirnews_ajax.strings ? nosfirnews_ajax.strings.error : 'Erro.'));
                },
                complete: function() {
                    button.prop('disabled', false).text('Limpar Dados Órfãos');
                }
            });
        },

        isValidEmail: function(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        isValidUrl: function(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        },

        // Utility functions
        debounce: function(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        throttle: function(func, limit) {
            var inThrottle;
            return function() {
                var args = arguments;
                var context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(function() {
                        inThrottle = false;
                    }, limit);
                }
            };
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        NosfirNewsAdvancedOptions.init();
    });

    // Handle escape key for modals
    $(document).keyup(function(e) {
        if (e.keyCode === 27) { // Escape key
            $('.nosfirnews-modal').hide();
        }
    });

    // Handle window resize for responsive adjustments
    $(window).resize(NosfirNewsAdvancedOptions.throttle(function() {
        // Responsive adjustments if needed
    }, 250));

})(jQuery);