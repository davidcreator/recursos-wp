/**
 * Admin JavaScript for NosfirNews Theme
 * 
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Media uploader for custom header image
        $('#upload_header_image').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var input = $('#nosfirnews_custom_header_image');
            
            // Create the media frame
            var frame = wp.media({
                title: nosfirnews_admin.upload_image_title,
                button: {
                    text: nosfirnews_admin.use_image_text
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            // When an image is selected
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                input.val(attachment.url);
                
                // Show preview if container exists
                var preview = button.siblings('.image-preview');
                if (preview.length) {
                    preview.html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto;" />');
                } else {
                    button.after('<div class="image-preview" style="margin-top: 10px;"><img src="' + attachment.url + '" style="max-width: 200px; height: auto;" /></div>');
                }
            });
            
            // Open the modal
            frame.open();
        });

        // Remove image functionality
        
        // Menu Custom Fields Enhancement
        initMenuCustomFields();
        
        // Color Picker Enhancement
        initColorPickers();
        
        // Icon Selector Enhancement
        initIconSelector();
        
        // Badge Preview
        initBadgePreview();
        $(document).on('click', '.remove-image', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var input = button.siblings('input[type="text"]');
            var preview = button.siblings('.image-preview');
            
            input.val('');
            preview.remove();
        });

        // Color picker initialization
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker();
        }

        // Conditional fields visibility
        function toggleConditionalFields() {
            // Full width page toggle
            var fullWidthCheckbox = $('#nosfirnews_full_width_page');
            var sidebarCheckbox = $('#nosfirnews_hide_sidebar');
            
            if (fullWidthCheckbox.is(':checked')) {
                sidebarCheckbox.closest('tr').hide();
            } else {
                sidebarCheckbox.closest('tr').show();
            }
        }

        // Initialize conditional fields
        toggleConditionalFields();
        
        // Handle conditional field changes
        $('#nosfirnews_full_width_page').on('change', toggleConditionalFields);

        // Meta box tabs functionality
        $('.nosfirnews-tabs').each(function() {
            var $tabs = $(this);
            var $tabButtons = $tabs.find('.tab-button');
            var $tabContents = $tabs.find('.tab-content');
            
            $tabButtons.on('click', function(e) {
                e.preventDefault();
                
                var $this = $(this);
                var target = $this.data('tab');
                
                // Remove active class from all tabs
                $tabButtons.removeClass('active');
                $tabContents.removeClass('active');
                
                // Add active class to clicked tab
                $this.addClass('active');
                $('#' + target).addClass('active');
            });
        });

        // Sortable functionality for custom fields
        if ($.fn.sortable) {
            $('.sortable-list').sortable({
                handle: '.sort-handle',
                placeholder: 'sort-placeholder',
                update: function(event, ui) {
                    updateSortOrder($(this));
                }
            });
        }

        function updateSortOrder($list) {
            $list.find('.sort-item').each(function(index) {
                $(this).find('.sort-order').val(index);
            });
        }

        // Add new repeater field
        $('.add-repeater-field').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $container = $button.siblings('.repeater-container');
            var template = $button.data('template');
            var index = $container.find('.repeater-item').length;
            
            // Replace placeholder with actual index
            var newField = template.replace(/\[INDEX\]/g, index);
            
            $container.append(newField);
            
            // Re-initialize any special fields in the new item
            initializeSpecialFields($container.find('.repeater-item').last());
        });

        // Remove repeater field
        $(document).on('click', '.remove-repeater-field', function(e) {
            e.preventDefault();
            
            var $item = $(this).closest('.repeater-item');
            $item.fadeOut(300, function() {
                $item.remove();
            });
        });

        function initializeSpecialFields($container) {
            // Re-initialize color pickers
            if ($.fn.wpColorPicker) {
                $container.find('.color-picker').wpColorPicker();
            }
            
            // Re-initialize media uploaders
            $container.find('.upload-button').on('click', function(e) {
                e.preventDefault();
                // Media uploader logic here
            });
        }

        // Auto-save functionality for meta boxes
        var autoSaveTimer;
        $('.nosfirnews-meta-field').on('change input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // Auto-save logic here if needed
                console.log('Auto-saving meta fields...');
            }, 2000);
        });

        // Validation for required fields
        $('form#post').on('submit', function(e) {
            var hasErrors = false;
            
            $('.required-field').each(function() {
                var $field = $(this);
                var value = $field.val().trim();
                
                if (!value) {
                    hasErrors = true;
                    $field.addClass('error');
                    
                    // Show error message
                    if (!$field.siblings('.error-message').length) {
                        $field.after('<span class="error-message" style="color: red; font-size: 12px;">This field is required.</span>');
                    }
                } else {
                    $field.removeClass('error');
                    $field.siblings('.error-message').remove();
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert(nosfirnews_admin.required_fields_error);
            }
        });

        // Clear error states on input
        $('.required-field').on('input', function() {
            var $field = $(this);
            if ($field.val().trim()) {
                $field.removeClass('error');
                $field.siblings('.error-message').remove();
            }
        });

        // Help tooltips
        $('.help-tooltip').on('mouseenter', function() {
            var $this = $(this);
            var helpText = $this.data('help');
            
            if (helpText) {
                $this.append('<div class="tooltip-content">' + helpText + '</div>');
            }
        }).on('mouseleave', function() {
            $(this).find('.tooltip-content').remove();
        });

        // Accordion functionality
        $('.accordion-header').on('click', function() {
            var $header = $(this);
            var $content = $header.next('.accordion-content');
            var $accordion = $header.closest('.accordion');
            
            // Close other accordion items if single-open
            if ($accordion.hasClass('single-open')) {
                $accordion.find('.accordion-content').not($content).slideUp();
                $accordion.find('.accordion-header').not($header).removeClass('active');
            }
            
            $header.toggleClass('active');
            $content.slideToggle();
        });

    });

    // Global functions
    window.nosfirnewsAdmin = {
        
        // Function to refresh meta box content
        refreshMetaBox: function(metaBoxId) {
            var $metaBox = $('#' + metaBoxId);
            $metaBox.addClass('loading');
            
            // AJAX call to refresh content
            $.post(ajaxurl, {
                action: 'nosfirnews_refresh_metabox',
                meta_box_id: metaBoxId,
                post_id: $('#post_ID').val(),
                nonce: nosfirnews_admin.nonce
            }, function(response) {
                if (response.success) {
                    $metaBox.find('.inside').html(response.data);
                    $metaBox.removeClass('loading');
                    
                    // Re-initialize fields
                    initializeSpecialFields($metaBox);
                }
            });
        },
        
        // Function to validate form data
        validateForm: function() {
            var isValid = true;
            
            $('.required-field').each(function() {
                if (!$(this).val().trim()) {
                    isValid = false;
                    return false;
                }
            });
            
            return isValid;
        }
    };

    /**
     * Initialize Menu Custom Fields
     */
    function initMenuCustomFields() {
        // Add event listeners for dynamic menu items
        $(document).on('DOMNodeInserted', function(e) {
            if ($(e.target).hasClass('menu-item')) {
                initMenuItemFields($(e.target));
            }
        });

        // Initialize existing menu items
        $('.menu-item').each(function() {
            initMenuItemFields($(this));
        });
    }

    /**
     * Initialize individual menu item fields
     */
    function initMenuItemFields($menuItem) {
        var itemId = $menuItem.find('.menu-item-data-db-id').val();
        
        if (!itemId) return;

        // Icon preview functionality
        var $iconInput = $menuItem.find('[name="menu-item-icon[' + itemId + ']"]');
        var $iconPreview = $menuItem.find('#icon-preview-' + itemId);

        $iconInput.on('input', function() {
            updateIconPreview($(this).val(), $iconPreview);
        });

        // Initial icon preview
        updateIconPreview($iconInput.val(), $iconPreview);

        // Badge preview functionality
        var $badgeInput = $menuItem.find('[name="menu-item-badge[' + itemId + ']"]');
        var $badgeColorInput = $menuItem.find('[name="menu-item-badge-color[' + itemId + ']"]');

        $badgeInput.add($badgeColorInput).on('input', function() {
            updateBadgePreview(itemId, $badgeInput.val(), $badgeColorInput.val());
        });
    }

    /**
     * Update icon preview
     */
    function updateIconPreview(iconClass, $preview) {
        if (iconClass && iconClass.trim()) {
            $preview.html('<i class="' + iconClass + '"></i>');
        } else {
            $preview.html('<em>Nenhum ícone</em>');
        }
    }

    /**
     * Update badge preview
     */
    function updateBadgePreview(itemId, badgeText, badgeColor) {
        var $preview = $('#badge-preview-' + itemId);
        
        if (!$preview.length) {
            $preview = $('<div id="badge-preview-' + itemId + '" style="margin-top: 5px;"><strong>Preview do Badge:</strong> <span class="badge-preview-text"></span></div>');
            $('#icon-preview-' + itemId).parent().append($preview);
        }

        var $badgeSpan = $preview.find('.badge-preview-text');
        
        if (badgeText && badgeText.trim()) {
            $badgeSpan.html('<span style="background-color: ' + (badgeColor || '#ff0000') + '; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">' + badgeText + '</span>');
        } else {
            $badgeSpan.html('<em>Nenhum badge</em>');
        }
    }

    /**
     * Initialize Color Pickers
     */
    function initColorPickers() {
        // Enhanced color picker functionality
        $(document).on('change', 'input[type="color"]', function() {
            var $this = $(this);
            var color = $this.val();
            
            // Add color preview next to input
            var $preview = $this.next('.color-preview');
            if (!$preview.length) {
                $preview = $('<span class="color-preview" style="display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc; margin-left: 5px; vertical-align: middle;"></span>');
                $this.after($preview);
            }
            
            $preview.css('background-color', color);
        });

        // Trigger initial color preview
        $('input[type="color"]').trigger('change');
    }

    /**
     * Initialize Icon Selector
     */
    function initIconSelector() {
        // Add icon selector button
        $(document).on('focus', '.edit-menu-item-icon', function() {
            var $input = $(this);
            
            if ($input.next('.icon-selector-btn').length) return;
            
            var $button = $('<button type="button" class="button icon-selector-btn" style="margin-left: 5px;">Selecionar Ícone</button>');
            $input.after($button);
            
            $button.on('click', function(e) {
                e.preventDefault();
                openIconSelector($input);
            });
        });
    }

    /**
     * Open icon selector modal
     */
    function openIconSelector($input) {
        // Create modal if it doesn't exist
        var $modal = $('#icon-selector-modal');
        if (!$modal.length) {
            createIconSelectorModal();
            $modal = $('#icon-selector-modal');
        }
        
        // Show modal
        $modal.show();
        
        // Set current input reference
        $modal.data('current-input', $input);
        
        // Populate icons
        populateIconSelector($modal);
    }

    /**
     * Create icon selector modal
     */
    function createIconSelectorModal() {
        var modalHtml = `
            <div id="icon-selector-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 999999;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; max-width: 600px; max-height: 80%; overflow-y: auto;">
                    <h3>Selecionar Ícone</h3>
                    <div class="icon-search">
                        <input type="text" placeholder="Pesquisar ícones..." style="width: 100%; margin-bottom: 15px; padding: 8px;">
                    </div>
                    <div class="icon-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(40px, 1fr)); gap: 10px; max-height: 300px; overflow-y: auto;">
                        <!-- Icons will be populated here -->
                    </div>
                    <div style="margin-top: 15px; text-align: right;">
                        <button type="button" class="button" onclick="jQuery('#icon-selector-modal').hide();">Cancelar</button>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
    }

    /**
     * Populate icon selector with common icons
     */
    function populateIconSelector($modal) {
        var commonIcons = [
            'fas fa-home', 'fas fa-user', 'fas fa-envelope', 'fas fa-phone', 'fas fa-info',
            'fas fa-search', 'fas fa-shopping-cart', 'fas fa-heart', 'fas fa-star', 'fas fa-cog',
            'fas fa-bars', 'fas fa-times', 'fas fa-arrow-right', 'fas fa-arrow-left', 'fas fa-arrow-up',
            'fas fa-arrow-down', 'fas fa-play', 'fas fa-pause', 'fas fa-stop', 'fas fa-download',
            'fab fa-facebook', 'fab fa-twitter', 'fab fa-instagram', 'fab fa-youtube', 'fab fa-linkedin'
        ];
        
        var $grid = $modal.find('.icon-grid');
        $grid.empty();
        
        commonIcons.forEach(function(iconClass) {
            var $iconBtn = $('<button type="button" style="padding: 8px; border: 1px solid #ddd; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="' + iconClass + '"></i></button>');
            
            $iconBtn.on('click', function() {
                var $currentInput = $modal.data('current-input');
                $currentInput.val(iconClass).trigger('input');
                $modal.hide();
            });
            
            $grid.append($iconBtn);
        });
        
        // Search functionality
        $modal.find('.icon-search input').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $grid.find('button').each(function() {
                var iconClass = $(this).find('i').attr('class');
                if (iconClass.toLowerCase().includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }

    /**
     * Initialize Badge Preview
     */
    function initBadgePreview() {
        // Real-time badge preview as user types
        $(document).on('input', '[name*="menu-item-badge"]', function() {
            var $this = $(this);
            var itemId = $this.attr('name').match(/\[(\d+)\]/)[1];
            var badgeText = $this.val();
            var badgeColor = $('[name="menu-item-badge-color[' + itemId + ']"]').val();
            
            updateBadgePreview(itemId, badgeText, badgeColor);
        });
        
        $(document).on('change', '[name*="menu-item-badge-color"]', function() {
            var $this = $(this);
            var itemId = $this.attr('name').match(/\[(\d+)\]/)[1];
            var badgeText = $('[name="menu-item-badge[' + itemId + ']"]').val();
            var badgeColor = $this.val();
            
            updateBadgePreview(itemId, badgeText, badgeColor);
        });
    }

    // Close modal when clicking outside
    $(document).on('click', '#icon-selector-modal', function(e) {
        if (e.target === this) {
            $(this).hide();
        }
    });

    // Escape key to close modal
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // Escape key
            $('#icon-selector-modal').hide();
        }
    });

})(jQuery);

// Localization object (to be populated by wp_localize_script)
var nosfirnews_admin = nosfirnews_admin || {
    upload_image_title: 'Select Image',
    use_image_text: 'Use This Image',
    required_fields_error: 'Please fill in all required fields.',
    nonce: ''
};