/**
 * Advanced Custom Fields JavaScript
 *
 * @package NosfirNews
 * @since 1.0.0
 */

(function($) {
    'use strict';

    var AdvancedFields = {
        
        init: function() {
            this.bindEvents();
            this.initTabs();
            this.initRepeaters();
            this.initLayoutBuilder();
            this.initMediaUpload();
            this.initGalleryManager();
        },

        bindEvents: function() {
            // Tab navigation
            $(document).on('click', '.nosfirnews-tab-nav a', this.switchTab);
            
            // Repeater controls
            $(document).on('click', '.add-repeater-item', this.addRepeaterItem);
            $(document).on('click', '.remove-repeater-item', this.removeRepeaterItem);
            
            // Layout builder controls
            $(document).on('click', '.add-section', this.addLayoutSection);
            $(document).on('click', '.remove-section', this.removeLayoutSection);
            $(document).on('click', '.move-section-up', this.moveSectionUp);
            $(document).on('click', '.move-section-down', this.moveSectionDown);
            $(document).on('change', '.columns-selector', this.updateColumnsFields);
            
            // Media upload controls
            $(document).on('click', '.nosfirnews-upload-media', this.uploadMedia);
            $(document).on('click', '.nosfirnews-remove-media', this.removeMedia);
            
            // Gallery controls
            $(document).on('click', '.add-gallery-images', this.addGalleryImages);
            $(document).on('click', '.remove-gallery-image', this.removeGalleryImage);
            
            // Range input updates
            $(document).on('input', 'input[type="range"]', this.updateRangeValue);
        },

        initTabs: function() {
            $('.nosfirnews-tab-content').hide();
            $('.nosfirnews-tab-content.active').show();
        },

        switchTab: function(e) {
            e.preventDefault();
            
            var $this = $(this);
            var target = $this.attr('href');
            
            // Update navigation
            $this.closest('.nosfirnews-tab-nav').find('a').removeClass('active');
            $this.addClass('active');
            
            // Update content
            $this.closest('.nosfirnews-tabs').find('.nosfirnews-tab-content').removeClass('active').hide();
            $(target).addClass('active').show();
        },

        initRepeaters: function() {
            $('.nosfirnews-repeater').each(function() {
                var $repeater = $(this);
                $repeater.data('index', $repeater.find('.repeater-item').length);
            });
        },

        addRepeaterItem: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $repeater = $button.closest('.nosfirnews-repeater');
            var field = $repeater.data('field');
            var index = $repeater.data('index') || 0;
            var template = $('#' + field.replace('_', '-') + '-template').html();
            
            if (template) {
                template = template.replace(/\{\{INDEX\}\}/g, index);
                $repeater.find('.repeater-items').append(template);
                $repeater.data('index', index + 1);
                
                // Initialize media upload for new item
                AdvancedFields.initMediaUpload();
            }
        },

        removeRepeaterItem: function(e) {
            e.preventDefault();
            
            if (confirm(nosfirnews_advanced.confirm_remove)) {
                $(this).closest('.repeater-item').remove();
            }
        },

        initLayoutBuilder: function() {
            var $sections = $('#layout-sections');
            
            if ($sections.length) {
                $sections.sortable({
                    handle: '.section-header',
                    placeholder: 'section-placeholder',
                    update: function() {
                        AdvancedFields.reindexSections();
                    }
                });
            }
        },

        addLayoutSection: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var type = $button.data('type');
            var $container = $('#layout-sections');
            var index = $container.find('.layout-section').length;
            var template = $('#' + type + '-section-template').html();
            
            if (template) {
                template = template.replace(/\{\{INDEX\}\}/g, index);
                $container.append(template);
                
                // Initialize media upload for new section
                AdvancedFields.initMediaUpload();
                
                // Initialize editor for text sections
                if (type === 'text') {
                    AdvancedFields.initTextEditor(index);
                }
            }
        },

        removeLayoutSection: function(e) {
            e.preventDefault();
            
            if (confirm(nosfirnews_advanced.confirm_remove)) {
                $(this).closest('.layout-section').remove();
                AdvancedFields.reindexSections();
            }
        },

        moveSectionUp: function(e) {
            e.preventDefault();
            
            var $section = $(this).closest('.layout-section');
            var $prev = $section.prev('.layout-section');
            
            if ($prev.length) {
                $section.insertBefore($prev);
                AdvancedFields.reindexSections();
            }
        },

        moveSectionDown: function(e) {
            e.preventDefault();
            
            var $section = $(this).closest('.layout-section');
            var $next = $section.next('.layout-section');
            
            if ($next.length) {
                $section.insertAfter($next);
                AdvancedFields.reindexSections();
            }
        },

        reindexSections: function() {
            $('#layout-sections .layout-section').each(function(index) {
                var $section = $(this);
                $section.attr('data-index', index);
                
                // Update field names
                $section.find('input, textarea, select').each(function() {
                    var $field = $(this);
                    var name = $field.attr('name');
                    
                    if (name && name.indexOf('nosfirnews_layout_sections[') === 0) {
                        name = name.replace(/\[\d+\]/, '[' + index + ']');
                        $field.attr('name', name);
                    }
                });
                
                // Update editor IDs for text sections
                $section.find('.wp-editor-area').each(function() {
                    var $editor = $(this);
                    var id = $editor.attr('id');
                    
                    if (id && id.indexOf('section_content_') === 0) {
                        var newId = 'section_content_' + index;
                        $editor.attr('id', newId);
                    }
                });
            });
        },

        updateColumnsFields: function() {
            var $select = $(this);
            var columns = parseInt($select.val());
            var $section = $select.closest('.layout-section');
            var index = $section.data('index');
            
            // Remove existing column fields
            $section.find('.column-content').remove();
            
            // Add new column fields
            for (var i = 1; i <= columns; i++) {
                var fieldHtml = '<tr class="column-content" data-column="' + i + '">' +
                    '<th><label>Column ' + i + ' Content</label></th>' +
                    '<td>' +
                    '<textarea name="nosfirnews_layout_sections[' + index + '][column_' + i + ']" rows="3" class="large-text"></textarea>' +
                    '</td>' +
                    '</tr>';
                
                $select.closest('tr').after(fieldHtml);
            }
        },

        initTextEditor: function(index) {
            // Initialize WordPress editor for text sections
            if (typeof tinymce !== 'undefined') {
                var editorId = 'section_content_' + index;
                
                tinymce.init({
                    selector: '#' + editorId,
                    height: 200,
                    menubar: false,
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen',
                        'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
                });
            }
        },

        initMediaUpload: function() {
            // Media upload functionality
            $('.nosfirnews-upload-media').each(function() {
                var $button = $(this);
                
                if ($button.data('media-frame')) {
                    return; // Already initialized
                }
                
                var frame = wp.media({
                    title: 'Select Media',
                    button: {
                        text: 'Use Media'
                    },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    var $container = $button.closest('.nosfirnews-media-upload');
                    var $input = $container.find('input[type="hidden"]');
                    var $preview = $container.find('.media-preview');
                    
                    $input.val(attachment.url);
                    $preview.html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto;" />');
                });
                
                $button.data('media-frame', frame);
            });
        },

        uploadMedia: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var frame = $button.data('media-frame');
            
            if (frame) {
                frame.open();
            }
        },

        removeMedia: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $container = $button.closest('.nosfirnews-media-upload');
            var $input = $container.find('input[type="hidden"]');
            var $preview = $container.find('.media-preview');
            
            $input.val('');
            $preview.empty();
        },

        initGalleryManager: function() {
            var $galleryImages = $('#gallery-images');
            
            if ($galleryImages.length) {
                $galleryImages.sortable({
                    placeholder: 'gallery-image-placeholder',
                    update: function() {
                        AdvancedFields.reindexGalleryImages();
                    }
                });
            }
        },

        addGalleryImages: function(e) {
            e.preventDefault();
            
            var frame = wp.media({
                title: nosfirnews_advanced.select_images,
                button: {
                    text: nosfirnews_advanced.use_images
                },
                multiple: true
            });
            
            frame.on('select', function() {
                var attachments = frame.state().get('selection').toJSON();
                var $container = $('#gallery-images');
                var index = $container.find('.gallery-image-item').length;
                
                attachments.forEach(function(attachment) {
                    var imageHtml = '<div class="gallery-image-item" data-index="' + index + '">' +
                        '<div class="image-preview">' +
                        '<img src="' + attachment.sizes.thumbnail.url + '" alt="" />' +
                        '</div>' +
                        '<div class="image-controls">' +
                        '<input type="hidden" name="nosfirnews_gallery_images[' + index + '][id]" value="' + attachment.id + '" />' +
                        '<input type="text" name="nosfirnews_gallery_images[' + index + '][caption]" placeholder="Caption" class="regular-text" />' +
                        '<button type="button" class="button remove-gallery-image">Remove</button>' +
                        '</div>' +
                        '</div>';
                    
                    $container.append(imageHtml);
                    index++;
                });
            });
            
            frame.open();
        },

        removeGalleryImage: function(e) {
            e.preventDefault();
            
            if (confirm(nosfirnews_advanced.confirm_remove)) {
                $(this).closest('.gallery-image-item').remove();
                AdvancedFields.reindexGalleryImages();
            }
        },

        reindexGalleryImages: function() {
            $('#gallery-images .gallery-image-item').each(function(index) {
                var $item = $(this);
                $item.attr('data-index', index);
                
                $item.find('input').each(function() {
                    var $input = $(this);
                    var name = $input.attr('name');
                    
                    if (name && name.indexOf('nosfirnews_gallery_images[') === 0) {
                        name = name.replace(/\[\d+\]/, '[' + index + ']');
                        $input.attr('name', name);
                    }
                });
            });
        },

        updateRangeValue: function() {
            var $range = $(this);
            var $valueSpan = $range.next('.range-value');
            
            if ($valueSpan.length) {
                $valueSpan.text($range.val());
            }
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        AdvancedFields.init();
    });

})(jQuery);