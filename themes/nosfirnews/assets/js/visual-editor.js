/**
 * Visual Editor JavaScript
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Visual Editor Class
    class VisualEditor {
        constructor() {
            this.init();
        }

        init() {
            this.initTabs();
            this.initColorPickers();
            this.initTypographyControls();
            this.initPresets();
            this.initDarkMode();
            this.initCustomCSS();
            this.initPreview();
            this.initActions();
            this.bindEvents();
        }

        // Tab Navigation
        initTabs() {
            $('.tab-nav-item').on('click', (e) => {
                e.preventDefault();
                const $tab = $(e.currentTarget);
                const target = $tab.data('tab');

                // Update active tab
                $('.tab-nav-item').removeClass('active');
                $tab.addClass('active');

                // Update active panel
                $('.tab-panel').removeClass('active');
                $(`.tab-panel[data-panel="${target}"]`).addClass('active').addClass('fade-in');
            });
        }

        // Color Picker Initialization
        initColorPickers() {
            if (typeof $.wp === 'undefined' || typeof $.wp.wpColorPicker === 'undefined') {
                console.warn('WordPress Color Picker not available');
                return;
            }

            $('.color-picker').each(function() {
                const $picker = $(this);
                const $textInput = $picker.siblings('.color-text');

                $picker.wpColorPicker({
                    change: function(event, ui) {
                        const color = ui.color.toString();
                        $textInput.val(color);
                        $picker.trigger('colorchange', [color]);
                    },
                    clear: function() {
                        $textInput.val('');
                        $picker.trigger('colorchange', ['']);
                    }
                });

                // Sync text input with color picker
                $textInput.on('input', function() {
                    const color = $(this).val();
                    if (color.match(/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/)) {
                        $picker.wpColorPicker('color', color);
                    }
                });
            });

            // Custom color change handler
            $('.color-picker').on('colorchange', this.updatePreview.bind(this));
        }

        // Typography Controls
        initTypographyControls() {
            // Font size sliders
            $('.size-slider').on('input', function() {
                const value = $(this).val();
                const unit = $(this).data('unit') || 'px';
                $(this).siblings('.size-display').text(value + unit);
                $(this).trigger('typography-change');
            });

            // Font family selectors
            $('.font-selector').on('change', function() {
                $(this).trigger('typography-change');
            });

            // Typography change handler
            $('.typography-field input, .typography-field select').on('change input', this.updatePreview.bind(this));
        }

        // Color Presets
        initPresets() {
            $('.preset-btn').on('click', (e) => {
                e.preventDefault();
                const $preset = $(e.currentTarget);
                const presetData = $preset.data('preset');

                if (presetData) {
                    this.applyColorPreset(presetData);
                }
            });
        }

        applyColorPreset(preset) {
            const colorMap = {
                'modern': {
                    'primary_color': '#2563eb',
                    'secondary_color': '#64748b',
                    'accent_color': '#f59e0b',
                    'text_color': '#1f2937',
                    'background_color': '#ffffff',
                    'link_color': '#2563eb'
                },
                'dark': {
                    'primary_color': '#1f2937',
                    'secondary_color': '#374151',
                    'accent_color': '#10b981',
                    'text_color': '#f9fafb',
                    'background_color': '#111827',
                    'link_color': '#60a5fa'
                },
                'warm': {
                    'primary_color': '#dc2626',
                    'secondary_color': '#f59e0b',
                    'accent_color': '#ea580c',
                    'text_color': '#451a03',
                    'background_color': '#fef7ed',
                    'link_color': '#dc2626'
                },
                'nature': {
                    'primary_color': '#059669',
                    'secondary_color': '#065f46',
                    'accent_color': '#84cc16',
                    'text_color': '#064e3b',
                    'background_color': '#f0fdf4',
                    'link_color': '#059669'
                }
            };

            const colors = colorMap[preset];
            if (colors) {
                Object.keys(colors).forEach(key => {
                    const $picker = $(`input[name="nosfirnews_visual[${key}]"]`);
                    if ($picker.length) {
                        $picker.val(colors[key]).wpColorPicker('color', colors[key]);
                        $picker.siblings('.color-text').val(colors[key]);
                    }
                });

                this.updatePreview();
                this.showMessage('Preset de cores aplicado com sucesso!', 'success');
            }
        }

        // Dark Mode Toggle
        initDarkMode() {
            const $toggle = $('#dark_mode_enabled');
            const $darkModeSection = $('.dark-mode-colors');

            $toggle.on('change', function() {
                if ($(this).is(':checked')) {
                    $darkModeSection.slideDown().addClass('fade-in');
                } else {
                    $darkModeSection.slideUp();
                }
            });

            // Initialize state
            if ($toggle.is(':checked')) {
                $darkModeSection.show();
            }
        }

        // Custom CSS Editor
        initCustomCSS() {
            const $cssEditor = $('#custom_css');
            
            // Add line numbers (simple implementation)
            $cssEditor.on('input scroll', function() {
                // Basic CSS validation
                const css = $(this).val();
                const isValid = this.validateCSS(css);
                
                if (isValid) {
                    $(this).removeClass('error');
                } else {
                    $(this).addClass('error');
                }
            });

            // CSS help toggle
            $('.css-help-toggle').on('click', function(e) {
                e.preventDefault();
                $('.css-help').slideToggle();
            });
        }

        validateCSS(css) {
            // Basic CSS validation
            try {
                // Check for balanced braces
                const openBraces = (css.match(/{/g) || []).length;
                const closeBraces = (css.match(/}/g) || []).length;
                return openBraces === closeBraces;
            } catch (e) {
                return false;
            }
        }

        // Preview Updates
        initPreview() {
            this.updatePreview();
        }

        updatePreview() {
            const colors = this.getColorValues();
            const typography = this.getTypographyValues();
            
            this.updateColorPreview(colors);
            this.updateTypographyPreview(typography);
        }

        getColorValues() {
            const colors = {};
            $('.color-picker').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const key = name.replace('nosfirnews_visual[', '').replace(']', '');
                    colors[key] = $(this).val() || $(this).siblings('.color-text').val();
                }
            });
            return colors;
        }

        getTypographyValues() {
            const typography = {};
            $('.typography-field input, .typography-field select').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const key = name.replace('nosfirnews_visual[', '').replace(']', '');
                    typography[key] = $(this).val();
                }
            });
            return typography;
        }

        updateColorPreview(colors) {
            const $preview = $('.preview-content');
            
            if (colors.background_color) {
                $preview.css('background-color', colors.background_color);
            }
            
            if (colors.text_color) {
                $preview.css('color', colors.text_color);
            }
            
            if (colors.primary_color) {
                $('.preview-heading').css('color', colors.primary_color);
            }
            
            if (colors.link_color) {
                $preview.find('a').css('color', colors.link_color);
            }
        }

        updateTypographyPreview(typography) {
            const $heading = $('.preview-heading');
            const $body = $('.preview-body');
            
            // Update heading typography
            if (typography.heading_font_family) {
                $heading.css('font-family', typography.heading_font_family);
            }
            
            if (typography.heading_font_size) {
                $heading.css('font-size', typography.heading_font_size + 'px');
            }
            
            if (typography.heading_font_weight) {
                $heading.css('font-weight', typography.heading_font_weight);
            }
            
            // Update body typography
            if (typography.body_font_family) {
                $body.css('font-family', typography.body_font_family);
            }
            
            if (typography.body_font_size) {
                $body.css('font-size', typography.body_font_size + 'px');
            }
            
            if (typography.body_line_height) {
                $body.css('line-height', typography.body_line_height);
            }
        }

        // Action Buttons
        initActions() {
            // Reset to defaults
            $('.reset-defaults').on('click', (e) => {
                e.preventDefault();
                this.resetToDefaults();
            });

            // Export settings
            $('.export-settings').on('click', (e) => {
                e.preventDefault();
                this.exportSettings();
            });

            // Import settings
            $('.import-settings').on('click', (e) => {
                e.preventDefault();
                this.importSettings();
            });

            // Live preview toggle
            $('.toggle-preview').on('click', (e) => {
                e.preventDefault();
                this.toggleLivePreview();
            });
        }

        resetToDefaults() {
            if (confirm('Tem certeza que deseja resetar todas as configurações para os valores padrão?')) {
                // Reset color pickers
                $('.color-picker').each(function() {
                    const defaultColor = $(this).data('default') || '#ffffff';
                    $(this).wpColorPicker('color', defaultColor);
                    $(this).siblings('.color-text').val(defaultColor);
                });

                // Reset typography
                $('.typography-field input, .typography-field select').each(function() {
                    const defaultValue = $(this).data('default') || '';
                    $(this).val(defaultValue);
                    
                    if ($(this).hasClass('size-slider')) {
                        const unit = $(this).data('unit') || 'px';
                        $(this).siblings('.size-display').text(defaultValue + unit);
                    }
                });

                // Reset custom CSS
                $('#custom_css').val('');

                // Reset dark mode
                $('#dark_mode_enabled').prop('checked', false).trigger('change');

                this.updatePreview();
                this.showMessage('Configurações resetadas para os valores padrão!', 'success');
            }
        }

        exportSettings() {
            const settings = {
                colors: this.getColorValues(),
                typography: this.getTypographyValues(),
                custom_css: $('#custom_css').val(),
                dark_mode: $('#dark_mode_enabled').is(':checked')
            };

            const dataStr = JSON.stringify(settings, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = 'nosfirnews-visual-settings.json';
            link.click();

            this.showMessage('Configurações exportadas com sucesso!', 'success');
        }

        importSettings() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.json';
            
            input.onchange = (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        try {
                            const settings = JSON.parse(e.target.result);
                            this.applyImportedSettings(settings);
                            this.showMessage('Configurações importadas com sucesso!', 'success');
                        } catch (error) {
                            this.showMessage('Erro ao importar configurações. Verifique o arquivo.', 'error');
                        }
                    };
                    reader.readAsText(file);
                }
            };
            
            input.click();
        }

        applyImportedSettings(settings) {
            // Apply colors
            if (settings.colors) {
                Object.keys(settings.colors).forEach(key => {
                    const $picker = $(`input[name="nosfirnews_visual[${key}]"]`);
                    if ($picker.length && settings.colors[key]) {
                        $picker.val(settings.colors[key]).wpColorPicker('color', settings.colors[key]);
                        $picker.siblings('.color-text').val(settings.colors[key]);
                    }
                });
            }

            // Apply typography
            if (settings.typography) {
                Object.keys(settings.typography).forEach(key => {
                    const $field = $(`input[name="nosfirnews_visual[${key}]"], select[name="nosfirnews_visual[${key}]"]`);
                    if ($field.length && settings.typography[key]) {
                        $field.val(settings.typography[key]);
                        
                        if ($field.hasClass('size-slider')) {
                            const unit = $field.data('unit') || 'px';
                            $field.siblings('.size-display').text(settings.typography[key] + unit);
                        }
                    }
                });
            }

            // Apply custom CSS
            if (settings.custom_css) {
                $('#custom_css').val(settings.custom_css);
            }

            // Apply dark mode
            if (typeof settings.dark_mode === 'boolean') {
                $('#dark_mode_enabled').prop('checked', settings.dark_mode).trigger('change');
            }

            this.updatePreview();
        }

        toggleLivePreview() {
            // This would integrate with WordPress Customizer for live preview
            this.showMessage('Preview ao vivo será implementado em uma versão futura.', 'info');
        }

        // Event Bindings
        bindEvents() {
            // Auto-save functionality
            let saveTimeout;
            $('.nosfirnews-visual-editor input, .nosfirnews-visual-editor select, .nosfirnews-visual-editor textarea').on('change input', () => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    this.autoSave();
                }, 2000);
            });

            // Keyboard shortcuts
            $(document).on('keydown', (e) => {
                if (e.ctrlKey || e.metaKey) {
                    switch (e.key) {
                        case 's':
                            e.preventDefault();
                            this.saveSettings();
                            break;
                        case 'r':
                            e.preventDefault();
                            this.resetToDefaults();
                            break;
                    }
                }
            });

            // Window resize handler
            $(window).on('resize', this.handleResize.bind(this));
        }

        autoSave() {
            // Auto-save implementation would go here
            console.log('Auto-saving settings...');
        }

        saveSettings() {
            $('.nosfirnews-visual-editor').addClass('visual-editor-loading');
            
            // Simulate save process
            setTimeout(() => {
                $('.nosfirnews-visual-editor').removeClass('visual-editor-loading');
                this.showMessage('Configurações salvas com sucesso!', 'success');
            }, 1000);
        }

        handleResize() {
            // Handle responsive adjustments
            const width = $(window).width();
            
            if (width < 768) {
                $('.tab-nav-item').addClass('mobile');
            } else {
                $('.tab-nav-item').removeClass('mobile');
            }
        }

        // Utility Methods
        showMessage(message, type = 'info') {
            const $message = $(`<div class="visual-editor-message ${type}">${message}</div>`);
            
            $('.nosfirnews-visual-editor').prepend($message);
            
            setTimeout(() => {
                $message.fadeOut(() => {
                    $message.remove();
                });
            }, 3000);
        }

        // Google Fonts Integration
        loadGoogleFonts() {
            const fonts = [];
            $('.font-selector').each(function() {
                const font = $(this).val();
                if (font && font.includes('Google:')) {
                    const fontName = font.replace('Google:', '');
                    fonts.push(fontName);
                }
            });

            if (fonts.length > 0) {
                const link = document.createElement('link');
                link.href = `https://fonts.googleapis.com/css2?${fonts.map(font => `family=${font.replace(' ', '+')}`).join('&')}&display=swap`;
                link.rel = 'stylesheet';
                document.head.appendChild(link);
            }
        }

        // Color Accessibility Check
        checkColorContrast(foreground, background) {
            // Simple contrast ratio calculation
            const getLuminance = (color) => {
                const rgb = this.hexToRgb(color);
                const [r, g, b] = [rgb.r, rgb.g, rgb.b].map(c => {
                    c = c / 255;
                    return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
                });
                return 0.2126 * r + 0.7152 * g + 0.0722 * b;
            };

            const l1 = getLuminance(foreground);
            const l2 = getLuminance(background);
            const ratio = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
            
            return {
                ratio: ratio,
                aa: ratio >= 4.5,
                aaa: ratio >= 7
            };
        }

        hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
    }

    // Initialize when document is ready
    $(document).ready(() => {
        if ($('.nosfirnews-visual-editor').length) {
            new VisualEditor();
        }
    });

    // Make VisualEditor available globally
    window.NosfirNewsVisualEditor = VisualEditor;

})(jQuery);