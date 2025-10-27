/**
 * NosfirNews Advanced Fields Admin Interactions
 * - Tabs navigation
 * - Media uploaders (image URL for content fields)
 * - Repeaters (Testimonials, Features)
 * - Layout Builder (add/move/remove/sort/reindex)
 * - Gallery Manager (multi-select, sort, remove, reindex)
 */
(function($){
    'use strict';

    var AdvancedFields = {
        init: function(){
            this.bindTabs();
            this.bindRangeSliders();
            this.bindMediaUploaders();
            this.bindRepeaters();
            this.bindLayoutBuilder();
            this.bindGalleryManager();
        },

        // Tabs in Advanced Content meta box
        bindTabs: function(){
            $(document).on('click', '.nosfirnews-tab-nav a', function(e){
                e.preventDefault();
                var $a = $(this);
                var target = $a.attr('href');
                $a.closest('.nosfirnews-tab-nav').find('a').removeClass('active');
                $a.addClass('active');
                var $tabs = $a.closest('.nosfirnews-tabs');
                $tabs.find('.nosfirnews-tab-content').removeClass('active');
                $tabs.find(target).addClass('active');
            });
        },

        // Display current value next to range inputs
        bindRangeSliders: function(){
            $(document).on('input change', '.nosfirnews-advanced-fields input[type="range"]', function(){
                var $range = $(this);
                $range.siblings('.range-value').text($range.val());
            });
        },

        // Generic image uploader for fields storing image URL
        bindMediaUploaders: function(){
            var self = this;

            $(document).on('click', '.nosfirnews-upload-media', function(e){
                e.preventDefault();
                var $btn = $(this);
                var $container = $btn.closest('.nosfirnews-media-upload');
                var targetSelector = $btn.data('target');
                var $input = targetSelector ? $(targetSelector) : $container.find('input[type="hidden"]').first();
                var $preview = $container.find('.media-preview');

                var frame = wp.media({
                    title: $btn.text(),
                    button: { text: $btn.text() },
                    library: { type: 'image' },
                    multiple: false
                });

                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    var url = attachment.url;
                    $input.val(url).trigger('change');
                    $preview.html('<img src="' + url + '" style="max-width: 200px; height: auto;" />');
                });

                frame.open();
            });

            $(document).on('click', '.nosfirnews-remove-media', function(e){
                e.preventDefault();
                var $btn = $(this);
                var $container = $btn.closest('.nosfirnews-media-upload');
                var targetSelector = $btn.data('target');
                var $input = targetSelector ? $(targetSelector) : $container.find('input[type="hidden"]').first();
                var $preview = $container.find('.media-preview');
                $input.val('').trigger('change');
                $preview.empty();
            });
        },

        // Repeaters for testimonials and features
        bindRepeaters: function(){
            var self = this;

            // Add new repeater item
            $(document).on('click', '.nosfirnews-repeater .add-repeater-item', function(e){
                e.preventDefault();
                var $repeater = $(this).closest('.nosfirnews-repeater');
                var field = $repeater.data('field'); // 'testimonials' or 'features'
                var tplId = field === 'testimonials' ? '#testimonial-template' : '#feature-template';
                var tpl = $(tplId).html();
                var index = self.getNextRepeaterIndex($repeater, field);
                var html = tpl.replace(/\{\{INDEX\}\}/g, index);
                $repeater.find('.repeater-items').append(html);
            });

            // Remove repeater item
            $(document).on('click', '.nosfirnews-repeater .remove-repeater-item', function(e){
                e.preventDefault();
                var message = (window.nosfirnews_advanced && nosfirnews_advanced.confirm_remove) ? nosfirnews_advanced.confirm_remove : 'Remove this item?';
                if(!confirm(message)) return;
                var $repeater = $(this).closest('.nosfirnews-repeater');
                var field = $repeater.data('field');
                $(this).closest('.repeater-item').remove();
                AdvancedFields.reindexRepeater($repeater, field);
            });

            // Update item header titles on change
            $(document).on('input change', '.nosfirnews-repeater[data-field="testimonials"] input[name*="[author]"]', function(){
                var $item = $(this).closest('.repeater-item');
                var val = $(this).val().trim();
                $item.find('.item-title').text(val || (typeof nosfirnews_advanced !== 'undefined' ? 'Testimonial' : 'Testimonial'));
            });
            $(document).on('input change', '.nosfirnews-repeater[data-field="features"] input[name*="[title]"]', function(){
                var $item = $(this).closest('.repeater-item');
                var val = $(this).val().trim();
                $item.find('.item-title').text(val || (typeof nosfirnews_advanced !== 'undefined' ? 'Feature' : 'Feature'));
            });

            // Collapse/expand repeater item content
            $(document).on('click', '.repeater-item-header', function(){
                $(this).siblings('.repeater-item-content').slideToggle(150);
            });
        },

        getNextRepeaterIndex: function($repeater, field){
            var names = [];
            $repeater.find('[name]').each(function(){
                var name = $(this).attr('name') || '';
                var m = name.match(new RegExp('nosfirnews_' + field + '\\[(\\d+)\\]'));
                if(m && m[1]) names.push(parseInt(m[1], 10));
            });
            var max = names.length ? Math.max.apply(null, names) : -1;
            return max + 1;
        },

        reindexRepeater: function($repeater, field){
            $repeater.find('.repeater-item').each(function(i){
                $(this).find('[name]').each(function(){
                    var name = $(this).attr('name');
                    if(!name) return;
                    name = name.replace(new RegExp('nosfirnews_' + field + '\\[\\d+\\]'), 'nosfirnews_' + field + '[' + i + ']');
                    $(this).attr('name', name);
                });
            });
        },

        // Layout builder interactions
        bindLayoutBuilder: function(){
            var self = this;

            // Sortable sections
            $('#layout-sections').sortable({
                handle: '.section-header',
                placeholder: 'section-placeholder',
                forcePlaceholderSize: true,
                tolerance: 'pointer',
                update: function(){ self.reindexLayoutSections(); }
            });

            // Add section by type
            $(document).on('click', '.nosfirnews-layout-builder .add-section', function(e){
                e.preventDefault();
                var type = $(this).data('type');
                var tplMap = {
                    text: '#text-section-template',
                    image: '#image-section-template',
                    columns: '#columns-section-template',
                    spacer: '#spacer-section-template'
                };
                var tplId = tplMap[type];
                if(!tplId) return;
                var tpl = $(tplId).html();
                var index = AdvancedFields.getNextLayoutIndex();
                var html = tpl.replace(/\{\{INDEX\}\}/g, index);
                $('#layout-sections').append(html);
                AdvancedFields.reindexLayoutSections();
            });

            // Move up/down
            $(document).on('click', '.nosfirnews-layout-builder .move-section-up', function(){
                var $section = $(this).closest('.layout-section');
                var $prev = $section.prev('.layout-section');
                if($prev.length){ $section.insertBefore($prev); AdvancedFields.reindexLayoutSections(); }
            });
            $(document).on('click', '.nosfirnews-layout-builder .move-section-down', function(){
                var $section = $(this).closest('.layout-section');
                var $next = $section.next('.layout-section');
                if($next.length){ $section.insertAfter($next); AdvancedFields.reindexLayoutSections(); }
            });

            // Remove section
            $(document).on('click', '.nosfirnews-layout-builder .remove-section', function(){
                var message = (window.nosfirnews_advanced && nosfirnews_advanced.confirm_remove) ? nosfirnews_advanced.confirm_remove : 'Remove this section?';
                if(!confirm(message)) return;
                $(this).closest('.layout-section').remove();
                AdvancedFields.reindexLayoutSections();
            });

            // Columns selector dynamic rows
            $(document).on('change', '.nosfirnews-layout-builder .columns-selector', function(){
                var $selector = $(this);
                var count = parseInt($selector.val(), 10) || 2;
                var $section = $selector.closest('.layout-section');
                AdvancedFields.ensureColumnRows($section, count);
            });
        },

        getNextLayoutIndex: function(){
            var indices = [];
            $('#layout-sections .layout-section').each(function(){
                var idx = parseInt($(this).attr('data-index'), 10);
                if(!isNaN(idx)) indices.push(idx);
            });
            var max = indices.length ? Math.max.apply(null, indices) : -1;
            return max + 1;
        },

        reindexLayoutSections: function(){
            $('#layout-sections .layout-section').each(function(i){
                var $sec = $(this);
                $sec.attr('data-index', i);
                // Update name attributes: nosfirnews_layout_sections[<n>]
                $sec.find('[name]').each(function(){
                    var name = $(this).attr('name');
                    if(!name) return;
                    name = name.replace(/nosfirnews_layout_sections\[\d+\]/, 'nosfirnews_layout_sections[' + i + ']');
                    $(this).attr('name', name);
                });
            });
        },

        ensureColumnRows: function($section, count){
            var index = parseInt($section.attr('data-index'), 10) || 0;
            var $table = $section.find('table.form-table').first();

            // Add missing rows up to count
            for(var i=1; i<=count; i++){
                var sel = '.column-content[data-column="' + i + '"]';
                if($table.find(sel).length === 0){
                    var row = [
                        '<tr class="column-content" data-column="' + i + '">',
                        '<th><label>Column ' + i + ' Content</label></th>',
                        '<td><textarea name="nosfirnews_layout_sections[' + index + '][column_' + i + ']" rows="3" class="large-text"></textarea></td>',
                        '</tr>'
                    ].join('');
                    $table.append(row);
                }
            }

            // Remove extra rows above count
            $table.find('.column-content').each(function(){
                var col = parseInt($(this).attr('data-column'), 10);
                if(col > count){ $(this).remove(); }
            });
        },

        // Gallery manager
        bindGalleryManager: function(){
            var self = this;

            // Sortable gallery grid
            $('#gallery-images').sortable({
                items: '.gallery-image-item',
                placeholder: 'gallery-image-placeholder',
                forcePlaceholderSize: true,
                tolerance: 'pointer',
                update: function(){ AdvancedFields.reindexGallery(); }
            });

            // Add images via media modal (IDs)
            $(document).on('click', '.add-gallery-images', function(e){
                e.preventDefault();
                var title = (window.nosfirnews_advanced && nosfirnews_advanced.select_images) ? nosfirnews_advanced.select_images : 'Select Images';
                var buttonText = (window.nosfirnews_advanced && nosfirnews_advanced.use_images) ? nosfirnews_advanced.use_images : 'Use Images';
                var frame = wp.media({
                    title: title,
                    button: { text: buttonText },
                    library: { type: 'image' },
                    multiple: true
                });
                frame.on('select', function(){
                    var selection = frame.state().get('selection');
                    var next = AdvancedFields.getNextGalleryIndex();
                    selection.each(function(att){
                        var a = att.toJSON();
                        var id = a.id;
                        var thumb = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
                        var item = [
                            '<div class="gallery-image-item" data-index="' + next + '">',
                            ' <div class="image-preview"><img src="' + thumb + '" alt="" /></div>',
                            ' <div class="image-controls">',
                            '   <input type="hidden" name="nosfirnews_gallery_images[' + next + '][id]" value="' + id + '" />',
                            '   <input type="text" name="nosfirnews_gallery_images[' + next + '][caption]" value="" placeholder="Caption" class="regular-text" />',
                            '   <button type="button" class="button remove-gallery-image">' + (typeof nosfirnews_advanced !== 'undefined' ? nosfirnews_advanced.confirm_remove || 'Remove' : 'Remove') + '</button>',
                            ' </div>',
                            '</div>'
                        ].join('');
                        $('#gallery-images').append(item);
                        next++;
                    });
                    AdvancedFields.reindexGallery();
                });
                frame.open();
            });

            // Remove gallery image
            $(document).on('click', '.remove-gallery-image', function(e){
                e.preventDefault();
                var message = (window.nosfirnews_advanced && nosfirnews_advanced.confirm_remove) ? nosfirnews_advanced.confirm_remove : 'Remove this image?';
                if(!confirm(message)) return;
                $(this).closest('.gallery-image-item').remove();
                AdvancedFields.reindexGallery();
            });
        },

        getNextGalleryIndex: function(){
            var indices = [];
            $('#gallery-images .gallery-image-item').each(function(){
                var idx = parseInt($(this).attr('data-index'), 10);
                if(!isNaN(idx)) indices.push(idx);
            });
            var max = indices.length ? Math.max.apply(null, indices) : -1;
            return max + 1;
        },

        reindexGallery: function(){
            $('#gallery-images .gallery-image-item').each(function(i){
                var $item = $(this);
                $item.attr('data-index', i);
                $item.find('[name]').each(function(){
                    var name = $(this).attr('name');
                    if(!name) return;
                    name = name.replace(/nosfirnews_gallery_images\[\d+\]/, 'nosfirnews_gallery_images[' + i + ']');
                    $(this).attr('name', name);
                });
            });
        }
    };

    $(function(){ AdvancedFields.init(); });
})(jQuery);