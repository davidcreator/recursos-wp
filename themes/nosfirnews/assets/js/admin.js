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

})(jQuery);

// Localization object (to be populated by wp_localize_script)
var nosfirnews_admin = nosfirnews_admin || {
    upload_image_title: 'Select Image',
    use_image_text: 'Use This Image',
    required_fields_error: 'Please fill in all required fields.',
    nonce: ''
};