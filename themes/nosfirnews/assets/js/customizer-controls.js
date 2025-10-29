/**
 * Customizer Controls JavaScript
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Wait for the customizer to be ready
    wp.customize.bind('ready', function() {
        
        // Initialize advanced controls
        initColorSchemeControls();
        initTypographyControls();
        initLayoutControls();
        initResponsiveControls();
        initDarkModeControls();
        initSocialMediaControls();
        initPerformanceControls();
        
        // Add custom CSS editor
        initCustomCSSEditor();
        
        // Add live preview functionality
        initLivePreview();
    });

    /**
     * Initialize color scheme controls
     */
    function initColorSchemeControls() {
        // Color palette generator
        wp.customize('nosfirnews_primary_color', function(value) {
            value.bind(function(newval) {
                generateColorPalette(newval);
            });
        });

        // Color contrast checker
        $('.customize-control-color').each(function() {
            var $control = $(this);
            var $input = $control.find('input[type="text"]');
            
            $input.on('change', function() {
                checkColorContrast($control, $(this).val());
            });
        });
    }

    /**
     * Generate color palette based on primary color
     */
    function generateColorPalette(primaryColor) {
        // Generate complementary colors
        var hsl = hexToHsl(primaryColor);
        var complementary = hslToHex((hsl.h + 180) % 360, hsl.s, hsl.l);
        var analogous1 = hslToHex((hsl.h + 30) % 360, hsl.s, hsl.l);
        var analogous2 = hslToHex((hsl.h - 30 + 360) % 360, hsl.s, hsl.l);

        // Update related color controls
        wp.customize('nosfirnews_accent_color').set(complementary);
        
        // Show color suggestions
        showColorSuggestions({
            primary: primaryColor,
            complementary: complementary,
            analogous1: analogous1,
            analogous2: analogous2
        });
    }

    /**
     * Check color contrast for accessibility
     */
    function checkColorContrast($control, color) {
        var backgroundColor = wp.customize('nosfirnews_background_color')();
        var contrast = getContrastRatio(color, backgroundColor);
        
        var $warning = $control.find('.contrast-warning');
        if ($warning.length === 0) {
            $warning = $('<div class="contrast-warning"></div>');
            $control.append($warning);
        }
        
        if (contrast < 4.5) {
            $warning.html('<span style="color: #d63638;">⚠️ Baixo contraste (WCAG AA)</span>').show();
        } else if (contrast < 7) {
            $warning.html('<span style="color: #dba617;">⚠️ Contraste moderado (WCAG AA)</span>').show();
        } else {
            $warning.html('<span style="color: #00a32a;">✓ Bom contraste (WCAG AAA)</span>').show();
        }
    }

    /**
     * Initialize typography controls
     */
    function initTypographyControls() {
        // Font preview
        wp.customize('nosfirnews_heading_font', function(value) {
            value.bind(function(newval) {
                loadGoogleFont(newval);
                updateFontPreview('heading', newval);
            });
        });

        wp.customize('nosfirnews_body_font', function(value) {
            value.bind(function(newval) {
                loadGoogleFont(newval);
                updateFontPreview('body', newval);
            });
        });

        // Font size calculator
        $('.customize-control-range').each(function() {
            var $control = $(this);
            var $input = $control.find('input[type="range"]');
            var $display = $('<span class="range-value"></span>');
            
            $control.find('.customize-control-title').append($display);
            
            $input.on('input', function() {
                var value = $(this).val();
                var unit = $input.data('unit') || 'px';
                $display.text(value + unit);
            }).trigger('input');
        });
    }

    /**
     * Load Google Font dynamically
     */
    function loadGoogleFont(fontName) {
        var fontUrl = 'https://fonts.googleapis.com/css2?family=' + 
                     fontName.replace(' ', '+') + ':wght@300;400;500;600;700;800;900&display=swap';
        
        if (!$('link[href="' + fontUrl + '"]').length) {
            $('<link>')
                .attr('rel', 'stylesheet')
                .attr('href', fontUrl)
                .appendTo('head');
        }
    }

    /**
     * Update font preview
     */
    function updateFontPreview(type, fontName) {
        var $preview = $('#font-preview-' + type);
        if ($preview.length === 0) {
            $preview = $('<div id="font-preview-' + type + '" class="font-preview"></div>');
            $('.customize-control-' + type + '-font').append($preview);
        }
        
        $preview.css('font-family', fontName + ', sans-serif')
                .text('Exemplo de texto com ' + fontName);
    }

    /**
     * Initialize layout controls
     */
    function initLayoutControls() {
        // Layout preview
        wp.customize('nosfirnews_blog_layout_style', function(value) {
            value.bind(function(newval) {
                updateLayoutPreview(newval);
            });
        });

        // Container width calculator
        wp.customize('nosfirnews_container_width', function(value) {
            value.bind(function(newval) {
                updateContainerPreview(newval);
            });
        });

        // Sidebar width dependency
        wp.customize('nosfirnews_content_width', function(value) {
            value.bind(function(newval) {
                var sidebarWidth = 100 - parseInt(newval) - 5; // 5% for gap
                wp.customize('nosfirnews_sidebar_width').set(sidebarWidth);
            });
        });
    }

    /**
     * Update layout preview
     */
    function updateLayoutPreview(layout) {
        var $preview = $('#layout-preview');
        if ($preview.length === 0) {
            $preview = $('<div id="layout-preview" class="layout-preview"></div>');
            $('.customize-control-blog-layout-style').append($preview);
        }
        
        var previewHTML = '';
        switch(layout) {
            case 'grid':
                previewHTML = '<div class="preview-grid"><div class="preview-item"></div><div class="preview-item"></div><div class="preview-item"></div></div>';
                break;
            case 'list':
                previewHTML = '<div class="preview-list"><div class="preview-item-list"></div><div class="preview-item-list"></div></div>';
                break;
            case 'masonry':
                previewHTML = '<div class="preview-masonry"><div class="preview-item short"></div><div class="preview-item tall"></div><div class="preview-item"></div></div>';
                break;
            case 'magazine':
                previewHTML = '<div class="preview-magazine"><div class="preview-featured"></div><div class="preview-sidebar-items"><div class="preview-small"></div><div class="preview-small"></div></div></div>';
                break;
        }
        
        $preview.html(previewHTML);
    }

    /**
     * Initialize responsive controls
     */
    function initResponsiveControls() {
        // Device preview buttons
        var $deviceButtons = $('<div class="device-preview-buttons"></div>');
        $deviceButtons.html(`
            <button type="button" class="device-btn active" data-device="desktop">
                <span class="dashicons dashicons-desktop"></span> Desktop
            </button>
            <button type="button" class="device-btn" data-device="tablet">
                <span class="dashicons dashicons-tablet"></span> Tablet
            </button>
            <button type="button" class="device-btn" data-device="mobile">
                <span class="dashicons dashicons-smartphone"></span> Mobile
            </button>
        `);
        
        $('#customize-controls').prepend($deviceButtons);
        
        // Device switching
        $deviceButtons.on('click', '.device-btn', function() {
            var device = $(this).data('device');
            switchDevicePreview(device);
            
            $deviceButtons.find('.device-btn').removeClass('active');
            $(this).addClass('active');
        });

        // Responsive value inputs
        addResponsiveInputs();
    }

    /**
     * Switch device preview
     */
    function switchDevicePreview(device) {
        var width;
        switch(device) {
            case 'mobile':
                width = wp.customize('nosfirnews_mobile_breakpoint')() || 768;
                break;
            case 'tablet':
                width = wp.customize('nosfirnews_tablet_breakpoint')() || 1024;
                break;
            default:
                width = 1200;
        }
        
        // Update preview frame width
        wp.customize.previewer.previewUrl.set(wp.customize.previewer.previewUrl() + '?customize_preview_device=' + device);
    }

    /**
     * Add responsive inputs for certain controls
     */
    function addResponsiveInputs() {
        var responsiveControls = [
            'nosfirnews_base_font_size',
            'nosfirnews_h1_font_size',
            'nosfirnews_section_padding',
            'nosfirnews_element_margin'
        ];

        responsiveControls.forEach(function(controlId) {
            var $control = $('#customize-control-' + controlId.replace('_', '-'));
            if ($control.length) {
                addResponsiveToggle($control, controlId);
            }
        });
    }

    /**
     * Add responsive toggle to control
     */
    function addResponsiveToggle($control, controlId) {
        var $toggle = $('<button type="button" class="responsive-toggle" title="Configurações responsivas">📱</button>');
        $control.find('.customize-control-title').append($toggle);
        
        $toggle.on('click', function() {
            showResponsiveModal(controlId);
        });
    }

    /**
     * Initialize dark mode controls
     */
    function initDarkModeControls() {
        // Dark mode toggle preview
        wp.customize('nosfirnews_enable_dark_mode', function(value) {
            value.bind(function(newval) {
                toggleDarkModePreview(newval);
            });
        });

        // Auto dark mode colors
        wp.customize('nosfirnews_primary_color', function(value) {
            value.bind(function(newval) {
                if (wp.customize('nosfirnews_enable_dark_mode')()) {
                    generateDarkModeColors(newval);
                }
            });
        });
    }

    /**
     * Toggle dark mode preview
     */
    function toggleDarkModePreview(enabled) {
        if (enabled) {
            wp.customize.previewer.send('toggle-dark-mode', true);
        } else {
            wp.customize.previewer.send('toggle-dark-mode', false);
        }
    }

    /**
     * Generate dark mode colors automatically
     */
    function generateDarkModeColors(primaryColor) {
        var hsl = hexToHsl(primaryColor);
        
        // Generate dark background (very dark version of primary)
        var darkBg = hslToHex(hsl.h, Math.min(hsl.s, 20), 10);
        
        // Generate dark text (light version)
        var darkText = hslToHex(hsl.h, Math.min(hsl.s, 10), 85);
        
        wp.customize('nosfirnews_dark_background_color').set(darkBg);
        wp.customize('nosfirnews_dark_text_color').set(darkText);
    }

    /**
     * Initialize social media controls
     */
    function initSocialMediaControls() {
        // Social media URL validation
        $('[id*="social"]').each(function() {
            var $input = $(this);
            var platform = $input.attr('id').replace('_customize-input-nosfirnews_social_', '');
            
            $input.on('blur', function() {
                validateSocialURL($(this), platform);
            });
        });

        // Social icons preview
        wp.customize('nosfirnews_social_style', function(value) {
            value.bind(function(newval) {
                updateSocialIconsPreview(newval);
            });
        });
    }

    /**
     * Validate social media URL
     */
    function validateSocialURL($input, platform) {
        var url = $input.val();
        if (!url) return;
        
        var platformDomains = {
            'facebook': 'facebook.com',
            'twitter': 'twitter.com',
            'instagram': 'instagram.com',
            'youtube': 'youtube.com',
            'linkedin': 'linkedin.com',
            'pinterest': 'pinterest.com',
            'tiktok': 'tiktok.com'
        };
        
        var expectedDomain = platformDomains[platform];
        if (expectedDomain && url.indexOf(expectedDomain) === -1) {
            showValidationWarning($input, 'URL deve conter ' + expectedDomain);
        } else {
            hideValidationWarning($input);
        }
    }

    /**
     * Show validation warning
     */
    function showValidationWarning($input, message) {
        var $warning = $input.siblings('.validation-warning');
        if ($warning.length === 0) {
            $warning = $('<div class="validation-warning"></div>');
            $input.after($warning);
        }
        $warning.text(message).show();
    }

    /**
     * Hide validation warning
     */
    function hideValidationWarning($input) {
        $input.siblings('.validation-warning').hide();
    }

    /**
     * Initialize performance controls
     */
    function initPerformanceControls() {
        // Performance impact indicators
        var performanceControls = [
            'nosfirnews_minify_css',
            'nosfirnews_lazy_load',
            'nosfirnews_preload_fonts'
        ];

        performanceControls.forEach(function(controlId) {
            var $control = $('#customize-control-' + controlId.replace('_', '-'));
            if ($control.length) {
                addPerformanceIndicator($control, controlId);
            }
        });
    }

    /**
     * Add performance indicator
     */
    function addPerformanceIndicator($control, controlId) {
        var impact = getPerformanceImpact(controlId);
        var $indicator = $('<span class="performance-indicator ' + impact.level + '">' + impact.text + '</span>');
        $control.find('.customize-control-title').append($indicator);
    }

    /**
     * Get performance impact level
     */
    function getPerformanceImpact(controlId) {
        var impacts = {
            'nosfirnews_minify_css': { level: 'positive', text: '⚡ Melhora performance' },
            'nosfirnews_lazy_load': { level: 'positive', text: '⚡ Melhora carregamento' },
            'nosfirnews_preload_fonts': { level: 'neutral', text: '⚖️ Impacto neutro' }
        };
        
        return impacts[controlId] || { level: 'neutral', text: '' };
    }

    /**
     * Initialize custom CSS editor
     */
    function initCustomCSSEditor() {
        var $cssControl = $('#customize-control-nosfirnews-additional-css');
        if ($cssControl.length) {
            var $textarea = $cssControl.find('textarea');
            
            // Add CSS syntax highlighting (basic)
            $textarea.on('input', function() {
                validateCSS($(this).val());
            });
            
            // Add CSS snippets helper
            addCSSSnippetsHelper($cssControl);
        }
    }

    /**
     * Validate CSS syntax
     */
    function validateCSS(css) {
        // Basic CSS validation
        var errors = [];
        var lines = css.split('\n');
        
        lines.forEach(function(line, index) {
            line = line.trim();
            if (line && !line.startsWith('/*') && !line.endsWith('*/')) {
                // Check for missing semicolons
                if (line.includes(':') && !line.endsWith(';') && !line.endsWith('{') && !line.endsWith('}')) {
                    errors.push('Linha ' + (index + 1) + ': Possível ponto e vírgula ausente');
                }
                
                // Check for unmatched braces
                var openBraces = (line.match(/{/g) || []).length;
                var closeBraces = (line.match(/}/g) || []).length;
                if (openBraces !== closeBraces) {
                    errors.push('Linha ' + (index + 1) + ': Chaves não balanceadas');
                }
            }
        });
        
        showCSSValidation(errors);
    }

    /**
     * Show CSS validation results
     */
    function showCSSValidation(errors) {
        var $validation = $('#css-validation');
        if ($validation.length === 0) {
            $validation = $('<div id="css-validation" class="css-validation"></div>');
            $('#customize-control-nosfirnews-additional-css').append($validation);
        }
        
        if (errors.length > 0) {
            $validation.html('<strong>Possíveis problemas:</strong><ul><li>' + errors.join('</li><li>') + '</li></ul>').show();
        } else {
            $validation.hide();
        }
    }

    /**
     * Add CSS snippets helper
     */
    function addCSSSnippetsHelper($control) {
        var $helper = $('<div class="css-snippets-helper"></div>');
        var snippets = {
            'Botão personalizado': '.custom-button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; }',
            'Sombra de caixa': '.shadow-box { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }',
            'Gradiente de fundo': '.gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }',
            'Animação de hover': '.hover-animation:hover { transform: translateY(-2px); transition: transform 0.3s ease; }'
        };
        
        var $select = $('<select><option value="">Escolher snippet...</option></select>');
        Object.keys(snippets).forEach(function(name) {
            $select.append('<option value="' + snippets[name] + '">' + name + '</option>');
        });
        
        $select.on('change', function() {
            var snippet = $(this).val();
            if (snippet) {
                var $textarea = $control.find('textarea');
                var currentValue = $textarea.val();
                $textarea.val(currentValue + '\n\n/* ' + $(this).find('option:selected').text() + ' */\n' + snippet);
                $textarea.trigger('input');
                $(this).val('');
            }
        });
        
        $helper.append('<label>Snippets CSS:</label>').append($select);
        $control.append($helper);
    }

    /**
     * Initialize live preview
     */
    function initLivePreview() {
        // Send live updates to preview
        wp.customize.bind('change', function(setting) {
            wp.customize.previewer.send('setting-changed', {
                id: setting.id,
                value: setting.get()
            });
        });
    }

    /**
     * Utility functions
     */
    
    // Convert hex to HSL
    function hexToHsl(hex) {
        var r = parseInt(hex.slice(1, 3), 16) / 255;
        var g = parseInt(hex.slice(3, 5), 16) / 255;
        var b = parseInt(hex.slice(5, 7), 16) / 255;
        
        var max = Math.max(r, g, b);
        var min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;
        
        if (max === min) {
            h = s = 0;
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        
        return { h: h * 360, s: s * 100, l: l * 100 };
    }
    
    // Convert HSL to hex
    function hslToHex(h, s, l) {
        h /= 360;
        s /= 100;
        l /= 100;
        
        var r, g, b;
        
        if (s === 0) {
            r = g = b = l;
        } else {
            var hue2rgb = function(p, q, t) {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1/6) return p + (q - p) * 6 * t;
                if (t < 1/2) return q;
                if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                return p;
            };
            
            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
        }
        
        var toHex = function(c) {
            var hex = Math.round(c * 255).toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        };
        
        return '#' + toHex(r) + toHex(g) + toHex(b);
    }
    
    // Calculate contrast ratio
    function getContrastRatio(color1, color2) {
        var lum1 = getLuminance(color1);
        var lum2 = getLuminance(color2);
        
        var brightest = Math.max(lum1, lum2);
        var darkest = Math.min(lum1, lum2);
        
        return (brightest + 0.05) / (darkest + 0.05);
    }
    
    // Get luminance
    function getLuminance(hex) {
        var r = parseInt(hex.slice(1, 3), 16) / 255;
        var g = parseInt(hex.slice(3, 5), 16) / 255;
        var b = parseInt(hex.slice(5, 7), 16) / 255;
        
        var toLinear = function(c) {
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        };
        
        return 0.2126 * toLinear(r) + 0.7152 * toLinear(g) + 0.0722 * toLinear(b);
    }

})(jQuery);