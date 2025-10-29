/**
 * Widget Admin JavaScript
 * Handles dynamic widget area management in the Customizer
 */

(function($) {
    'use strict';

    var NosfirNewsWidgetAdmin = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.setupCustomWidgetAreas();
            this.initWidgetVisibilityControls();
            this.initWidgetStylingControls();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            $(document).on('click', '.add-widget-area-btn', this.addWidgetArea);
            $(document).on('click', '.remove-widget-area-btn', this.removeWidgetArea);
            $(document).on('widget-updated', this.onWidgetUpdated);
            $(document).on('widget-added', this.onWidgetAdded);
        },

        /**
         * Setup custom widget areas section
         */
        setupCustomWidgetAreas: function() {
            var self = this;
            
            // Add button to add new widget area
            var addWidgetSection = $('#customize-control-nosfirnews_add_widget_area');
            if (addWidgetSection.length) {
                var addButton = $('<button type="button" class="button add-widget-area-btn">' + 
                    nosfirnews_widget_ajax.strings.add_widget + '</button>');
                addWidgetSection.find('.customize-control-content').append(addButton);
                
                // Display existing custom widget areas
                this.displayCustomWidgetAreas();
            }
        },

        /**
         * Display existing custom widget areas
         */
        displayCustomWidgetAreas: function() {
            var self = this;
            
            $.ajax({
                url: nosfirnews_widget_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_custom_widget_areas',
                    nonce: nosfirnews_widget_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        self.renderCustomWidgetList(response.data);
                    }
                }
            });
        },

        /**
         * Render custom widget list
         */
        renderCustomWidgetList: function(widgets) {
            var customSection = $('#customize-control-nosfirnews_add_widget_area');
            var listContainer = customSection.find('.custom-widget-list');
            
            if (!listContainer.length) {
                listContainer = $('<div class="custom-widget-list"></div>');
                customSection.find('.customize-control-content').append(listContainer);
            }
            
            listContainer.empty();
            
            $.each(widgets, function(id, widget) {
                var widgetItem = $('<div class="custom-widget-item" data-widget-id="' + id + '">' +
                    '<span class="widget-name">' + widget.name + '</span>' +
                    '<button type="button" class="button-link remove-widget-area-btn" data-widget-id="' + id + '">' +
                    nosfirnews_widget_ajax.strings.remove_widget + '</button>' +
                    '</div>');
                listContainer.append(widgetItem);
            });
        },

        /**
         * Add new widget area
         */
        addWidgetArea: function(e) {
            e.preventDefault();
            
            var nameInput = $('#customize-control-nosfirnews_add_widget_area input');
            var name = nameInput.val().trim();
            
            if (!name) {
                alert('Por favor, digite um nome para a área de widget.');
                return;
            }
            
            $.ajax({
                url: nosfirnews_widget_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'add_widget_area',
                    name: name,
                    nonce: nosfirnews_widget_ajax.nonce
                },
                beforeSend: function() {
                    $('.add-widget-area-btn').prop('disabled', true).text('Adicionando...');
                },
                success: function(response) {
                    if (response.success) {
                        nameInput.val('');
                        NosfirNewsWidgetAdmin.displayCustomWidgetAreas();
                        
                        // Show success message
                        var notice = $('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        $('#customize-control-nosfirnews_add_widget_area').prepend(notice);
                        setTimeout(function() {
                            notice.fadeOut();
                        }, 3000);
                        
                        // Refresh customizer to show new widget area
                        wp.customize.previewer.refresh();
                    } else {
                        alert(response.data || 'Erro ao adicionar área de widget.');
                    }
                },
                error: function() {
                    alert('Erro de conexão. Tente novamente.');
                },
                complete: function() {
                    $('.add-widget-area-btn').prop('disabled', false).text(nosfirnews_widget_ajax.strings.add_widget);
                }
            });
        },

        /**
         * Remove widget area
         */
        removeWidgetArea: function(e) {
            e.preventDefault();
            
            if (!confirm(nosfirnews_widget_ajax.strings.confirm_remove)) {
                return;
            }
            
            var widgetId = $(this).data('widget-id');
            
            $.ajax({
                url: nosfirnews_widget_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'remove_widget_area',
                    id: widgetId,
                    nonce: nosfirnews_widget_ajax.nonce
                },
                beforeSend: function() {
                    $('.remove-widget-area-btn[data-widget-id="' + widgetId + '"]')
                        .prop('disabled', true).text('Removendo...');
                },
                success: function(response) {
                    if (response.success) {
                        $('.custom-widget-item[data-widget-id="' + widgetId + '"]').fadeOut(function() {
                            $(this).remove();
                        });
                        
                        // Refresh customizer
                        wp.customize.previewer.refresh();
                    } else {
                        alert(response.data || 'Erro ao remover área de widget.');
                    }
                },
                error: function() {
                    alert('Erro de conexão. Tente novamente.');
                },
                complete: function() {
                    $('.remove-widget-area-btn[data-widget-id="' + widgetId + '"]')
                        .prop('disabled', false).text(nosfirnews_widget_ajax.strings.remove_widget);
                }
            });
        },

        /**
         * Initialize widget visibility controls
         */
        initWidgetVisibilityControls: function() {
            var self = this;
            
            // Add toggle functionality for widget visibility
            $('[id*="nosfirnews_show_widget_"]').each(function() {
                var control = $(this);
                var checkbox = control.find('input[type="checkbox"]');
                
                checkbox.on('change', function() {
                    var isChecked = $(this).is(':checked');
                    var widgetId = $(this).attr('id').replace('_customize-input-nosfirnews_show_widget_', '');
                    
                    // Add visual feedback
                    control.toggleClass('widget-disabled', !isChecked);
                    
                    // Live preview update
                    wp.customize.previewer.send('widget-visibility-changed', {
                        widget_id: widgetId,
                        visible: isChecked
                    });
                });
            });
        },

        /**
         * Initialize widget styling controls
         */
        initWidgetStylingControls: function() {
            var self = this;
            
            // Widget title style preview
            wp.customize('nosfirnews_widget_title_style', function(value) {
                value.bind(function(newval) {
                    wp.customize.previewer.send('widget-title-style-changed', newval);
                });
            });
            
            // Widget background color preview
            wp.customize('nosfirnews_widget_background', function(value) {
                value.bind(function(newval) {
                    wp.customize.previewer.send('widget-background-changed', newval);
                });
            });
            
            // Widget border color preview
            wp.customize('nosfirnews_widget_border', function(value) {
                value.bind(function(newval) {
                    wp.customize.previewer.send('widget-border-changed', newval);
                });
            });
            
            // Widget spacing preview
            wp.customize('nosfirnews_widget_spacing', function(value) {
                value.bind(function(newval) {
                    wp.customize.previewer.send('widget-spacing-changed', newval);
                });
            });
        },

        /**
         * Handle widget updated event
         */
        onWidgetUpdated: function(e, widget) {
            // Add any custom logic when a widget is updated
            console.log('Widget updated:', widget);
        },

        /**
         * Handle widget added event
         */
        onWidgetAdded: function(e, widget) {
            // Add any custom logic when a widget is added
            console.log('Widget added:', widget);
        },

        /**
         * Add widget area management to existing widget areas
         */
        enhanceWidgetAreas: function() {
            $('.widgets-sortables').each(function() {
                var widgetArea = $(this);
                var widgetAreaId = widgetArea.attr('id');
                
                // Add widget area controls
                if (!widgetArea.find('.widget-area-controls').length) {
                    var controls = $('<div class="widget-area-controls">' +
                        '<button type="button" class="button widget-area-settings" data-widget-area="' + widgetAreaId + '">' +
                        'Configurações</button>' +
                        '</div>');
                    widgetArea.prepend(controls);
                }
            });
        },

        /**
         * Initialize widget drag and drop enhancements
         */
        initDragDropEnhancements: function() {
            $('.widgets-sortables').sortable({
                connectWith: '.widgets-sortables',
                placeholder: 'widget-placeholder',
                forcePlaceholderSize: true,
                start: function(e, ui) {
                    ui.placeholder.height(ui.item.height());
                    ui.placeholder.addClass('widget-placeholder-active');
                },
                stop: function(e, ui) {
                    // Trigger widget reorder event
                    $(document).trigger('widget-reordered', [ui.item]);
                }
            });
        },

        /**
         * Add widget search functionality
         */
        addWidgetSearch: function() {
            var searchContainer = $('.widget-liquid-left');
            
            if (!searchContainer.find('.widget-search').length) {
                var searchBox = $('<div class="widget-search">' +
                    '<input type="text" placeholder="Buscar widgets..." class="widget-search-input">' +
                    '</div>');
                
                searchContainer.prepend(searchBox);
                
                // Bind search functionality
                searchBox.find('input').on('keyup', function() {
                    var searchTerm = $(this).val().toLowerCase();
                    
                    $('.widget').each(function() {
                        var widget = $(this);
                        var widgetTitle = widget.find('.widget-title h3, .widget-title h4').text().toLowerCase();
                        var widgetDescription = widget.find('.widget-description').text().toLowerCase();
                        
                        if (widgetTitle.indexOf(searchTerm) !== -1 || widgetDescription.indexOf(searchTerm) !== -1) {
                            widget.show();
                        } else {
                            widget.hide();
                        }
                    });
                });
            }
        }
    };

    // Initialize when customizer is ready
    wp.customize.bind('ready', function() {
        NosfirNewsWidgetAdmin.init();
    });

    // Initialize for widgets page
    $(document).ready(function() {
        if ($('body').hasClass('widgets-php')) {
            NosfirNewsWidgetAdmin.enhanceWidgetAreas();
            NosfirNewsWidgetAdmin.initDragDropEnhancements();
            NosfirNewsWidgetAdmin.addWidgetSearch();
        }
    });

})(jQuery);

/**
 * Customizer Preview JavaScript for Widgets
 */
(function($) {
    'use strict';

    if (typeof wp !== 'undefined' && wp.customize && wp.customize.preview) {
        
        // Listen for widget visibility changes
        wp.customize.preview.bind('widget-visibility-changed', function(data) {
            var widgetArea = $('.widget-area-' + data.widget_id);
            
            if (data.visible) {
                widgetArea.show();
            } else {
                widgetArea.hide();
            }
        });

        // Listen for widget title style changes
        wp.customize.preview.bind('widget-title-style-changed', function(style) {
            $('body').removeClass('widget-title-default widget-title-bordered widget-title-background widget-title-underline widget-title-minimal')
                     .addClass('widget-title-' + style);
        });

        // Listen for widget background changes
        wp.customize.preview.bind('widget-background-changed', function(color) {
            $('<style id="widget-bg-preview">.widget { background-color: ' + color + ' !important; }</style>')
                .appendTo('head');
            $('#widget-bg-preview').remove();
        });

        // Listen for widget border changes
        wp.customize.preview.bind('widget-border-changed', function(color) {
            $('<style id="widget-border-preview">.widget { border-color: ' + color + ' !important; }</style>')
                .appendTo('head');
            $('#widget-border-preview').remove();
        });

        // Listen for widget spacing changes
        wp.customize.preview.bind('widget-spacing-changed', function(spacing) {
            $('<style id="widget-spacing-preview">.widget { margin-bottom: ' + spacing + 'px !important; }</style>')
                .appendTo('head');
            $('#widget-spacing-preview').remove();
        });
    }

})(jQuery);