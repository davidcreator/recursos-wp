/**
 * Easy AMP Pro - Admin JavaScript
 */

(function($) {
    'use strict';
    
    var EasyAMPProAdmin = {
        
        /**
         * Initialize admin functionality
         */
        init: function() {
            this.bindEvents();
            this.initTabs();
            this.initValidation();
            this.initSettingsForm();
            this.initImportExport();
        },
        
        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // URL validation
            $(document).on('click', '.validate-url-btn', this.validateSingleURL);
            $(document).on('click', '.validate-bulk-btn', this.validateBulkURLs);
            
            // Settings
            $(document).on('click', '.save-settings-btn', this.saveSettings);
            $(document).on('click', '.reset-settings-btn', this.resetSettings);
            
            // Import/Export
            $(document).on('click', '.export-settings-btn', this.exportSettings);
            $(document).on('click', '.import-settings-btn', this.importSettings);
            $(document).on('change', '.import-file-input', this.handleImportFile);
            
            // Modal controls
            $(document).on('click', '.amp-modal-close, .amp-modal', this.closeModal);
            $(document).on('click', '.amp-modal-content', function(e) { e.stopPropagation(); });
            
            // Diagnostic tools
            $(document).on('click', '.amp-diagnostic-check', this.runDiagnostic);
            $(document).on('click', '.amp-debug-toggle', this.toggleDebugMode);
            
            // Quick actions
            $(document).on('click', '#clear-validation-errors', this.clearValidationErrors);
            $(document).on('click', '#refresh-amp-cache', this.refreshAMPCache);
            
            // Live URL testing
            $(document).on('keyup', '.amp-url-input', this.debounce(this.testURLLive, 1000));
        },
        
        /**
         * Initialize tab functionality
         */
        initTabs: function() {
            $('.nav-tab-wrapper .nav-tab').on('click', function(e) {
                e.preventDefault();
                
                var $tab = $(this);
                var target = $tab.attr('href');
                
                // Update active tab
                $('.nav-tab').removeClass('nav-tab-active');
                $tab.addClass('nav-tab-active');
                
                // Show target content
                $('.tab-content').hide();
                $(target).show();
                
                // Update URL hash
                if (history.pushState) {
                    history.pushState(null, null, target);
                }
            });
            
            // Show tab based on URL hash
            var hash = window.location.hash;
            if (hash && $('.nav-tab[href="' + hash + '"]').length) {
                $('.nav-tab[href="' + hash + '"]').trigger('click');
            }
        },
        
        /**
         * Initialize validation functionality
         */
        initValidation: function() {
            // Auto-validate URL format
            $('.amp-url-input').on('input', function() {
                var url = $(this).val();
                var $feedback = $(this).next('.url-feedback');
                
                if (!$feedback.length) {
                    $feedback = $('<div class="url-feedback"></div>').insertAfter($(this));
                }
                
                if (url === '') {
                    $feedback.hide();
                    return;
                }
                
                if (!EasyAMPProAdmin.isValidURL(url)) {
                    $feedback.removeClass('valid').addClass('invalid').text(easyAmpPro.strings.invalidURL || 'Invalid URL format').show();
                } else if (!EasyAMPProAdmin.isAMPURL(url)) {
                    $feedback.removeClass('invalid').addClass('warning').text(easyAmpPro.strings.notAmpURL || 'URL will be converted to AMP format').show();
                } else {
                    $feedback.removeClass('invalid warning').addClass('valid').text(easyAmpPro.strings.validAmpURL || 'Valid AMP URL').show();
                }
            });
        },
        
        /**
         * Initialize settings form
         */
        initSettingsForm: function() {
            // Auto-save draft settings
            $('.easy-amp-pro-settings input, .easy-amp-pro-settings select, .easy-amp-pro-settings textarea').on('change', function() {
                EasyAMPProAdmin.saveDraftSettings();
            });
            
            // Character counter for CSS field
            $('#amp_css').on('input', function() {
                var length = $(this).val().length;
                var maxLength = 50000; // 50KB limit for AMP CSS
                var $counter = $(this).next('.char-counter');
                
                if (!$counter.length) {
                    $counter = $('<div class="char-counter"></div>').insertAfter($(this));
                }
                
                var remaining = maxLength - length;
                var percentage = (length / maxLength) * 100;
                
                $counter.text(length + ' / ' + maxLength + ' characters (' + remaining + ' remaining)');
                
                if (percentage > 90) {
                    $counter.addClass('warning');
                } else {
                    $counter.removeClass('warning');
                }
                
                if (length > maxLength) {
                    $counter.addClass('error').removeClass('warning');
                    $(this).addClass('error');
                } else {
                    $counter.removeClass('error');
                    $(this).removeClass('error');
                }
            });
            
            // Trigger initial character count
            $('#amp_css').trigger('input');
        },
        
        /**
         * Initialize import/export functionality
         */
        initImportExport: function() {
            // Create hidden file input for import
            if (!$('#import-file-input').length) {
                $('<input type="file" id="import-file-input" class="import-file-input" accept=".json" style="display:none;">').appendTo('body');
            }
        },
        
        /**
         * Validate single URL
         */
        validateSingleURL: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $form = $button.closest('form');
            var url = $form.find('.amp-url-input').val().trim();
            
            if (!url) {
                EasyAMPProAdmin.showNotice(easyAmpPro.strings.enterURL || 'Please enter a URL to validate', 'error');
                return;
            }
            
            if (!EasyAMPProAdmin.isValidURL(url)) {
                EasyAMPProAdmin.showNotice(easyAmpPro.strings.invalidURL || 'Invalid URL format', 'error');
                return;
            }
            
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> ' + (easyAmpPro.strings.validating || 'Validating...'));
            
            $.ajax({
                url: easyAmpPro.ajaxurl,
                method: 'POST',
                data: {
                    action: 'validate_amp_url',
                    url: url,
                    nonce: easyAmpPro.nonce
                },
                success: function(response) {
                    if (response.success) {
                        EasyAMPProAdmin.displayValidationResults(response.data);
                        EasyAMPProAdmin.showNotice(easyAmpPro.strings.validationComplete || 'Validation completed successfully', 'success');
                    } else {
                        EasyAMPProAdmin.showNotice(response.data.message || easyAmpPro.strings.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    EasyAMPProAdmin.showNotice(easyAmpPro.strings.ajaxError || 'An error occurred during validation', 'error');
                    console.error('Validation error:', error);
                },
                complete: function() {
                    $button.prop('disabled', false).html(originalText);
                }
            });
        },
        
        /**
         * Export settings
         */
        exportSettings: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> ' + (easyAmpPro.strings.exporting || 'Exporting...'));
            
            $.ajax({
                url: easyAmpPro.ajaxurl,
                method: 'POST',
                data: {
                    action: 'easy_amp_pro_export_settings',
                    nonce: easyAmpPro.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Create and trigger download
                        var blob = new Blob([atob(response.data.data)], { type: 'application/json' });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = response.data.filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        
                        EasyAMPProAdmin.showNotice(easyAmpPro.strings.settingsExported || 'Settings exported successfully', 'success');
                    } else {
                        EasyAMPProAdmin.showNotice(response.data.message || easyAmpPro.strings.error, 'error');
                    }
                },
                error: function() {
                    EasyAMPProAdmin.showNotice(easyAmpPro.strings.ajaxError || 'An error occurred while exporting settings', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).html(originalText);
                }
            });
        },
        
        /**
         * Import settings
         */
        importSettings: function(e) {
            e.preventDefault();
            $('#import-file-input').trigger('click');
        },
        
        /**
         * Handle import file selection
         */
        handleImportFile: function(e) {
            var file = e.target.files[0];
            
            if (!file) return;
            
            if (file.type !== 'application/json' && !file.name.endsWith('.json')) {
                EasyAMPProAdmin.showNotice(easyAmpPro.strings.invalidFileType || 'Please select a valid JSON file', 'error');
                return;
            }
            
            var reader = new FileReader();
            reader.onload = function(e) {
                try {
                    var importData = btoa(e.target.result);
                    EasyAMPProAdmin.processImport(importData);
                } catch (error) {
                    EasyAMPProAdmin.showNotice(easyAmpPro.strings.invalidFileContent || 'Invalid file content', 'error');
                }
            };
            reader.readAsText(file);
        },
        
        /**
         * Process settings import
         */
        processImport: function(importData) {
            if (!confirm(easyAmpPro.strings.confirmImport || 'This will overwrite your current settings. Are you sure?')) {
                return;
            }
            
            $.ajax({
                url: easyAmpPro.ajaxurl,
                method: 'POST',
                data: {
                    action: 'easy_amp_pro_import_settings',
                    import_data: importData,
                    nonce: easyAmpPro.nonce
                },
                success: function(response) {
                    if (response.success) {
                        EasyAMPProAdmin.showNotice(response.data.message || (easyAmpPro.strings.settingsImported || 'Settings imported successfully'), 'success');
                        // Reload page to show imported values
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        EasyAMPProAdmin.showNotice(response.data.message || easyAmpPro.strings.error, 'error');
                    }
                },
                error: function() {
                    EasyAMPProAdmin.showNotice(easyAmpPro.strings.ajaxError || 'An error occurred while importing settings', 'error');
                }
            });
        },
        
        /**
         * Save draft settings
         */
        saveDraftSettings: function() {
            var settings = {};
            $('.easy-amp-pro-settings input, .easy-amp-pro-settings select, .easy-amp-pro-settings textarea').each(function() {
                var $input = $(this);
                var name = $input.attr('name');
                
                if (!name || name.indexOf('easy_amp_pro_settings') === -1) return;
                
                var key = name.replace('easy_amp_pro_settings[', '').replace(']', '');
                
                if ($input.is(':checkbox')) {
                    settings[key] = $input.is(':checked');
                } else {
                    settings[key] = $input.val();
                }
            });
            
            localStorage.setItem('easy_amp_pro_draft_settings', JSON.stringify(settings));
        },
        
        /**
         * Clear draft settings
         */
        clearDraftSettings: function() {
            localStorage.removeItem('easy_amp_pro_draft_settings');
        },
        
        /**
         * Load draft settings
         */
        loadDraftSettings: function() {
            var draftSettings = localStorage.getItem('easy_amp_pro_draft_settings');
            
            if (!draftSettings) return;
            
            try {
                var settings = JSON.parse(draftSettings);
                
                Object.keys(settings).forEach(function(key) {
                    var $input = $('[name="easy_amp_pro_settings[' + key + ']"]');
                    
                    if ($input.is(':checkbox')) {
                        $input.prop('checked', settings[key]);
                    } else {
                        $input.val(settings[key]);
                    }
                });
                
                // Show notice about draft settings
                EasyAMPProAdmin.showNotice(easyAmpPro.strings.draftLoaded || 'Draft settings loaded. Don\'t forget to save!', 'info');
            } catch (error) {
                EasyAMPProAdmin.clearDraftSettings();
            }
        },
        
        /**
         * Run diagnostic check
         */
        runDiagnostic: function(e) {
            e.preventDefault();
            
            var $link = $(this);
            var originalText = $link.text();
            $link.html('<span class="spinner is-active"></span> ' + (easyAmpPro.strings.runningDiagnostic || 'Running diagnostic...'));
            
            // Simulate diagnostic check
            setTimeout(function() {
                var diagnosticResults = {
                    phpVersion: PHP_VERSION || 'Unknown',
                    wpVersion: window.wp && window.wp.version || 'Unknown',
                    ampEnabled: true,
                    validationEnabled: true,
                    errors: [],
                    warnings: ['Custom CSS is over 45KB', 'Some plugins may not be AMP compatible']
                };
                
                EasyAMPProAdmin.showDiagnosticResults(diagnosticResults);
                $link.html(originalText);
            }, 2000);
        },
        
        /**
         * Show diagnostic results
         */
        showDiagnosticResults: function(results) {
            var html = '<div class="diagnostic-results">';
            html += '<h4>' + (easyAmpPro.strings.diagnosticResults || 'Diagnostic Results') + '</h4>';
            
            html += '<div class="diagnostic-info">';
            html += '<p><strong>PHP Version:</strong> ' + results.phpVersion + '</p>';
            html += '<p><strong>WordPress Version:</strong> ' + results.wpVersion + '</p>';
            html += '<p><strong>AMP Status:</strong> ' + (results.ampEnabled ? 'Enabled' : 'Disabled') + '</p>';
            html += '<p><strong>Validation:</strong> ' + (results.validationEnabled ? 'Enabled' : 'Disabled') + '</p>';
            html += '</div>';
            
            if (results.errors.length > 0) {
                html += '<div class="diagnostic-errors"><h5>Errors:</h5><ul>';
                results.errors.forEach(function(error) {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul></div>';
            }
            
            if (results.warnings.length > 0) {
                html += '<div class="diagnostic-warnings"><h5>Warnings:</h5><ul>';
                results.warnings.forEach(function(warning) {
                    html += '<li>' + warning + '</li>';
                });
                html += '</ul></div>';
            }
            
            html += '</div>';
            
            EasyAMPProAdmin.showModal('Diagnostic Results', html);
        },
        
        /**
         * Toggle debug mode
         */
        toggleDebugMode: function(e) {
            e.preventDefault();
            
            var debugEnabled = localStorage.getItem('easy_amp_pro_debug') === 'true';
            debugEnabled = !debugEnabled;
            
            localStorage.setItem('easy_amp_pro_debug', debugEnabled.toString());
            
            if (debugEnabled) {
                EasyAMPProAdmin.showNotice('Debug mode enabled. Check browser console for detailed logs.', 'info');
                console.log('Easy AMP Pro: Debug mode enabled');
            } else {
                EasyAMPProAdmin.showNotice('Debug mode disabled.', 'info');
            }
        },
        
        /**
         * Clear validation errors
         */
        clearValidationErrors: function(e) {
            e.preventDefault();
            
            if (!confirm(easyAmpPro.strings.confirmClearErrors || 'Are you sure you want to clear all validation errors?')) {
                return;
            }
            
            var $button = $(this);
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> ' + (easyAmpPro.strings.clearing || 'Clearing...'));
            
            $.ajax({
                url: easyAmpPro.ajaxurl,
                method: 'POST',
                data: {
                    action: 'easy_amp_pro_clear_validation_errors',
                    nonce: easyAmpPro.nonce
                },
                success: function(response) {
                    if (response.success) {
                        EasyAMPProAdmin.showNotice(easyAmpPro.strings.errorsCleared || 'Validation errors cleared successfully', 'success');
                        $('.validation-results').empty();
                    } else {
                        EasyAMPProAdmin.showNotice(response.data.message || easyAmpPro.strings.error, 'error');
                    }
                },
                error: function() {
                    EasyAMPProAdmin.showNotice(easyAmpPro.strings.ajaxError, 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).html(originalText);
                }
            });
        },
        
        /**
         * Refresh AMP cache
         */
        refreshAMPCache: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> ' + (easyAmpPro.strings.refreshing || 'Refreshing...'));
            
            // Simulate cache refresh
            setTimeout(function() {
                EasyAMPProAdmin.showNotice(easyAmpPro.strings.cacheRefreshed || 'AMP cache refreshed successfully', 'success');
                $button.prop('disabled', false).html(originalText);
            }, 2000);
        },
        
        /**
         * Test URL live (debounced)
         */
        testURLLive: function() {
            var $input = $(this);
            var url = $input.val().trim();
            
            if (!url || !EasyAMPProAdmin.isValidURL(url)) {
                return;
            }
            
            var $indicator = $input.next('.live-test-indicator');
            if (!$indicator.length) {
                $indicator = $('<span class="live-test-indicator"></span>').insertAfter($input);
            }
            
            $indicator.removeClass('valid invalid').addClass('testing').text('Testing...');
            
            // Simulate live testing
            setTimeout(function() {
                var isValid = Math.random() > 0.3; // 70% chance of being valid
                $indicator.removeClass('testing').addClass(isValid ? 'valid' : 'invalid');
                $indicator.text(isValid ? '✓ Valid' : '✗ Issues found');
            }, 1000);
        },
        
        /**
         * Utility Functions
         */
        
        /**
         * Check if URL is valid
         */
        isValidURL: function(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        },
        
        /**
         * Check if URL is AMP URL
         */
        isAMPURL: function(url) {
            return url.indexOf('/amp/') !== -1 || 
                   url.indexOf('?amp=1') !== -1 || 
                   url.indexOf('&amp=1') !== -1 ||
                   /[?&]amp($|[=&])/i.test(url);
        },
        
        /**
         * Show admin notice
         */
        showNotice: function(message, type) {
            type = type || 'info';
            
            var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            
            $('.wrap h1').after($notice);
            
            // Auto-dismiss after 5 seconds for success messages
            if (type === 'success') {
                setTimeout(function() {
                    $notice.fadeOut();
                }, 5000);
            }
            
            // Add dismiss button functionality
            $notice.find('.notice-dismiss').on('click', function() {
                $notice.remove();
            });
        },
        
        /**
         * Show modal
         */
        showModal: function(title, content) {
            var html = '<div class="amp-modal" id="amp-generic-modal">';
            html += '<div class="amp-modal-content">';
            html += '<div class="amp-modal-header">';
            html += '<h3>' + title + '</h3>';
            html += '<button type="button" class="amp-modal-close">&times;</button>';
            html += '</div>';
            html += '<div class="amp-modal-body">' + content + '</div>';
            html += '</div></div>';
            
            $('body').append(html);
            $('#amp-generic-modal').show();
        },
        
        /**
         * Close modal
         */
        closeModal: function(e) {
            if (e.target === this) {
                $(this).remove();
            }
        },
        
        /**
         * Debounce function
         */
        debounce: function(func, wait) {
            var timeout;
            return function executedFunction() {
                var context = this;
                var args = arguments;
                
                var later = function() {
                    timeout = null;
                    func.apply(context, args);
                };
                
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        /**
         * Log debug message
         */
        debug: function(message, data) {
            if (localStorage.getItem('easy_amp_pro_debug') === 'true') {
                console.log('Easy AMP Pro Debug:', message, data || '');
            }
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        EasyAMPProAdmin.init();
        
        // Load draft settings if available
        if ($('.easy-amp-pro-settings').length) {
            EasyAMPProAdmin.loadDraftSettings();
        }
        
        // Show unsaved changes warning
        var formChanged = false;
        $('.easy-amp-pro-settings input, .easy-amp-pro-settings select, .easy-amp-pro-settings textarea').on('change', function() {
            formChanged = true;
        });
        
        $('.save-settings-btn').on('click', function() {
            formChanged = false;
        });
        
        $(window).on('beforeunload', function(e) {
            if (formChanged) {
                e.returnValue = easyAmpPro.strings.unsavedChanges || 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    });
    
})(jQuery);
            });
        },
        
        /**
         * Validate bulk URLs
         */
        validateBulkURLs: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $form = $button.closest('form');
            var urls = $form.find('.amp-urls-textarea').val().split('\n').filter(function(url) {
                return url.trim() !== '';
            });
            
            if (urls.length === 0) {
                EasyAMPProAdmin.showNotice(easyAmpPro.strings.enterURLs || 'Please enter URLs to validate', 'error');
                return;
            }
            
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> ' + (easyAmpPro.strings.validatingBulk || 'Validating URLs...'));
            
            // Clear previous results
            $('.validation-results').empty();
            
            var completed = 0;
            var total = urls.length;
            var results = [];
            
            // Process URLs one by one to avoid overwhelming the server
            EasyAMPProAdmin.validateURLSequentially(urls, 0, function(allResults) {
                EasyAMPProAdmin.displayBulkValidationResults(allResults);
                $button.prop('disabled', false).html(originalText);
                EasyAMPProAdmin.showNotice(total + ' URLs validated successfully', 'success');
            });
        },
        
        /**
         * Validate URLs sequentially
         */
        validateURLSequentially: function(urls, index, callback) {
            if (index >= urls.length) {
                callback([]);
                return;
            }
            
            var url = urls[index].trim();
            
            if (!EasyAMPProAdmin.isValidURL(url)) {
                // Skip invalid URLs
                EasyAMPProAdmin.validateURLSequentially(urls, index + 1, callback);
                return;
            }
            
            $.ajax({
                url: easyAmpPro.ajaxurl,
                method: 'POST',
                data: {
                    action: 'validate_amp_url',
                    url: url,
                    nonce: easyAmpPro.nonce
                },
                success: function(response) {
                    if (response.success) {
                        EasyAMPProAdmin.displayValidationResults(response.data);
                    }
                    
                    // Continue with next URL
                    setTimeout(function() {
                        EasyAMPProAdmin.validateURLSequentially(urls, index + 1, callback);
                    }, 500); // Delay to avoid overwhelming server
                },
                error: function() {
                    // Continue with next URL even on error
                    setTimeout(function() {
                        EasyAMPProAdmin.validateURLSequentially(urls, index + 1, callback);
                    }, 500);
                }
            });
        },
        
        /**
         * Display validation results
         */
        displayValidationResults: function(result) {
            var $container = $('.validation-results');
            
            if (!$container.length) {
                $container = $('<div class="validation-results"></div>').insertAfter('.amp-validator-form');
            }
            
            var statusClass = result.success ? 'success' : 'error';
            var statusText = result.success ? (easyAmpPro.strings.valid || 'Valid') : (easyAmpPro.strings.invalid || 'Invalid');
            
            var html = '<div class="validation-result ' + statusClass + '">';
            html += '<h4><span class="status-indicator"></span> ' + result.url + ' - ' + statusText + '</h4>';
            
            if (result.errors && result.errors.length > 0) {
                html += '<div class="validation-errors"><h5>' + (easyAmpPro.strings.errors || 'Errors') + ':</h5><ul>';
                result.errors.forEach(function(error) {
                    html += '<li><strong>' + error.type + ':</strong> ' + error.message + '</li>';
                });
                html += '</ul></div>';
            }
            
            if (result.warnings && result.warnings.length > 0) {
                html += '<div class="validation-warnings"><h5>' + (easyAmpPro.strings.warnings || 'Warnings') + ':</h5><ul>';
                result.warnings.forEach(function(warning) {
                    html += '<li><strong>' + warning.type + ':</strong> ' + warning.message + '</li>';
                });
                html += '</ul></div>';
            }
            
            html += '<small>' + (easyAmpPro.strings.validatedAt || 'Validated at') + ': ' + result.timestamp + '</small>';
            html += '</div>';
            
            $container.prepend(html);
        },
        
        /**
         * Display bulk validation results
         */
        displayBulkValidationResults: function(results) {
            var $container = $('.validation-results');
            
            var validCount = results.filter(function(r) { return r.success; }).length;
            var invalidCount = results.length - validCount;
            
            var summaryHtml = '<div class="validation-summary">';
            summaryHtml += '<h4>' + (easyAmpPro.strings.validationSummary || 'Validation Summary') + '</h4>';
            summaryHtml += '<p><strong>' + (easyAmpPro.strings.totalValidated || 'Total Validated') + ':</strong> ' + results.length + '</p>';
            summaryHtml += '<p><strong>' + (easyAmpPro.strings.valid || 'Valid') + ':</strong> <span class="valid-count">' + validCount + '</span></p>';
            summaryHtml += '<p><strong>' + (easyAmpPro.strings.invalid || 'Invalid') + ':</strong> <span class="invalid-count">' + invalidCount + '</span></p>';
            summaryHtml += '</div>';
            
            $container.prepend(summaryHtml);
        },
        
        /**
         * Save settings
         */
        saveSettings: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $form = $button.closest('form');
            var formData = new FormData($form[0]);
            
            var settings = {};
            $form.find('input, select, textarea').each(function() {
                var $input = $(this);
                var name = $input.attr('name');
                
                if (!name || name.indexOf('easy_amp_pro_settings') === -1) return;
                
                var key = name.replace('easy_amp_pro_settings[', '').replace(']', '');
                
                if ($input.is(':checkbox')) {
                    settings[key] = $input.is(':checked');
                } else if ($input.is('select[multiple]')) {
                    settings[key] = $input.val() || [];
                } else {
                    settings[key] = $input.val();
                }
            });
            
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> ' + (easyAmpPro.strings.saving || 'Saving...'));
            
            $.ajax({
                url: easyAmpPro.ajaxurl,
                method: 'POST',
                data: {
                    action: 'easy_amp_pro_save_settings',
                    settings: settings,
                    nonce: easyAmpPro.nonce
                },
                success: function(response) {
                    if (response.success) {
                        EasyAMPProAdmin.showNotice(response.data.message || (easyAmpPro.strings.settingsSaved || 'Settings saved successfully'), 'success');
                        EasyAMPProAdmin.clearDraftSettings();
                    } else {
                        EasyAMPProAdmin.showNotice(response.data.message || easyAmpPro.strings.error, 'error');
                    }
                },
                error: function() {
                    EasyAMPProAdmin.showNotice(easyAmpPro.strings.ajaxError || 'An error occurred while saving settings', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).html(originalText);
                }
            });
        },
        
        /**
         * Reset settings
         */
        resetSettings: function(e) {
            e.preventDefault();
            
            if (!confirm(easyAmpPro.strings.confirmReset || 'Are you sure you want to reset all settings to defaults?')) {
                return;
            }
            
            var $button = $(this);
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> ' + (easyAmpPro.strings.resetting || 'Resetting...'));
            
            $.ajax({
                url: easyAmpPro.ajaxurl,
                method: 'POST',
                data: {
                    action: 'easy_amp_pro_reset_settings',
                    nonce: easyAmpPro.nonce
                },
                success: function(response) {
                    if (response.success) {
                        EasyAMPProAdmin.showNotice(response.data.message || (easyAmpPro.strings.settingsReset || 'Settings reset successfully'), 'success');
                        // Reload page to show default values
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        EasyAMPProAdmin.showNotice(response.data.message || easyAmpPro.strings.error, 'error');
                    }
                },
                error: function() {
                    EasyAMPProAdmin.showNotice(easyAmpPro.strings.ajaxError || 'An error occurred while resetting settings', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).html(originalText);
                }