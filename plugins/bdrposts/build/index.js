(function(){
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor || wp.editor;
    const SSR = wp.serverSideRender || (wp.blockEditor && wp.blockEditor.ServerSideRender) || (wp.editor && wp.editor.ServerSideRender);
    const { PanelBody, SelectControl, ToggleControl, RangeControl, TextControl } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('brdposts/post-block', {
        title: __('BDR Posts', 'brdposts'),
        icon: 'list-view',
        category: 'widgets',
        supports: {
            align: true
        },
        edit: (props) => {
            const { attributes, setAttributes } = props;

            const updateCommaSeparated = (value, key) => {
                const arr = (value || '')
                    .split(',')
                    .map(v => parseInt(v.trim(), 10))
                    .filter(v => !isNaN(v));
                setAttributes({ [key]: arr });
            };

            return (
                wp.element.createElement('div', { className: 'brdposts-editor' },
                    wp.element.createElement(InspectorControls, {},
                        wp.element.createElement(PanelBody, { title: __('Layout', 'brdposts'), initialOpen: true },
                            wp.element.createElement(SelectControl, {
                                label: __('Layout', 'brdposts'),
                                value: attributes.layout || 'grid',
                                options: [
                                    { label: __('Grid', 'brdposts'), value: 'grid' },
                                    { label: __('Masonry', 'brdposts'), value: 'masonry' },
                                    { label: __('Slider', 'brdposts'), value: 'slider' },
                                    { label: __('Ticker', 'brdposts'), value: 'ticker' },
                                ],
                                onChange: (v) => setAttributes({ layout: v })
                            }),
                            wp.element.createElement(SelectControl, {
                                label: __('Sub-layout', 'brdposts'),
                                value: attributes.subLayout || 'title-meta',
                                options: [
                                    { label: __('Title Meta', 'brdposts'), value: 'title-meta' },
                                    { label: __('Left Image', 'brdposts'), value: 'left-image' },
                                    { label: __('Right Image', 'brdposts'), value: 'right-image' },
                                    { label: __('Overlay Content', 'brdposts'), value: 'overlay' },
                                ],
                                onChange: (v) => setAttributes({ subLayout: v })
                            }),
                            wp.element.createElement(RangeControl, {
                                label: __('Columns', 'brdposts'),
                                value: attributes.columns || 3,
                                min: 1,
                                max: 6,
                                onChange: (v) => setAttributes({ columns: v })
                            })
                        ),
                        wp.element.createElement(PanelBody, { title: __('Query', 'brdposts'), initialOpen: true },
                            wp.element.createElement(SelectControl, {
                                label: __('Post Type', 'brdposts'),
                                value: attributes.postType || 'post',
                                options: [
                                    { label: __('Post', 'brdposts'), value: 'post' },
                                    { label: __('Page', 'brdposts'), value: 'page' },
                                ],
                                onChange: (v) => setAttributes({ postType: v })
                            }),
                            wp.element.createElement(TextControl, {
                                label: __('Custom Post Type (slug)', 'brdposts'),
                                help: __('Se preencher, substitui o tipo acima.', 'brdposts'),
                                value: attributes.postTypeCustom || '',
                                onChange: (v) => setAttributes({ postType: v || attributes.postType })
                            }),
                            wp.element.createElement(RangeControl, {
                                label: __('Posts per page', 'brdposts'),
                                value: attributes.postsPerPage || 6,
                                min: 1,
                                max: 50,
                                onChange: (v) => setAttributes({ postsPerPage: v })
                            }),
                            wp.element.createElement(SelectControl, {
                                label: __('Order', 'brdposts'),
                                value: attributes.order || 'DESC',
                                options: [
                                    { label: 'DESC', value: 'DESC' },
                                    { label: 'ASC', value: 'ASC' },
                                ],
                                onChange: (v) => setAttributes({ order: v })
                            }),
                            wp.element.createElement(SelectControl, {
                                label: __('Order By', 'brdposts'),
                                value: attributes.orderBy || 'date',
                                options: [
                                    { label: __('Date', 'brdposts'), value: 'date' },
                                    { label: __('Title', 'brdposts'), value: 'title' },
                                    { label: __('Modified', 'brdposts'), value: 'modified' },
                                    { label: __('Menu Order', 'brdposts'), value: 'menu_order' },
                                    { label: __('Random', 'brdposts'), value: 'rand' },
                                ],
                                onChange: (v) => setAttributes({ orderBy: v })
                            }),
                            wp.element.createElement(RangeControl, {
                                label: __('Offset', 'brdposts'),
                                value: attributes.offset || 0,
                                min: 0,
                                max: 50,
                                onChange: (v) => setAttributes({ offset: v })
                            }),
                            wp.element.createElement(TextControl, {
                                label: __('Categories (IDs separados por vírgula)', 'brdposts'),
                                value: (attributes.categories || []).join(','),
                                onChange: (v) => updateCommaSeparated(v, 'categories')
                            }),
                            wp.element.createElement(TextControl, {
                                label: __('Tags (IDs separados por vírgula)', 'brdposts'),
                                value: (attributes.tags || []).join(','),
                                onChange: (v) => updateCommaSeparated(v, 'tags')
                            }),
                            wp.element.createElement(TextControl, {
                                label: __('Authors (IDs separados por vírgula)', 'brdposts'),
                                value: (attributes.authors || []).join(','),
                                onChange: (v) => updateCommaSeparated(v, 'authors')
                            }),
                            wp.element.createElement(TextControl, {
                                label: __('Include posts (IDs)', 'brdposts'),
                                value: (attributes.includePosts || []).join(','),
                                onChange: (v) => updateCommaSeparated(v, 'includePosts')
                            }),
                            wp.element.createElement(TextControl, {
                                label: __('Exclude posts (IDs)', 'brdposts'),
                                value: (attributes.excludePosts || []).join(','),
                                onChange: (v) => updateCommaSeparated(v, 'excludePosts')
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Exclude current post', 'brdposts'),
                                checked: !!attributes.excludeCurrent,
                                onChange: (v) => setAttributes({ excludeCurrent: v })
                            })
                        ),
                        wp.element.createElement(PanelBody, { title: __('Elements', 'brdposts'), initialOpen: false },
                            wp.element.createElement(ToggleControl, {
                                label: __('Show featured image', 'brdposts'),
                                checked: !!attributes.showImage,
                                onChange: (v) => setAttributes({ showImage: v })
                            }),
                            wp.element.createElement(SelectControl, {
                                label: __('Image size', 'brdposts'),
                                value: attributes.imageSize || 'medium',
                                options: [
                                    { label: 'thumbnail', value: 'thumbnail' },
                                    { label: 'medium', value: 'medium' },
                                    { label: 'large', value: 'large' },
                                    { label: 'full', value: 'full' },
                                ],
                                onChange: (v) => setAttributes({ imageSize: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Link image to post', 'brdposts'),
                                checked: !!attributes.linkImage,
                                onChange: (v) => setAttributes({ linkImage: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Show title', 'brdposts'),
                                checked: !!attributes.showTitle,
                                onChange: (v) => setAttributes({ showTitle: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Make title a link', 'brdposts'),
                                checked: !!attributes.linkTitle,
                                onChange: (v) => setAttributes({ linkTitle: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Show excerpt', 'brdposts'),
                                checked: !!attributes.showExcerpt,
                                onChange: (v) => setAttributes({ showExcerpt: v })
                            }),
                            wp.element.createElement(RangeControl, {
                                label: __('Excerpt words', 'brdposts'),
                                value: attributes.excerptLength || 20,
                                min: 5,
                                max: 100,
                                onChange: (v) => setAttributes({ excerptLength: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Show meta', 'brdposts'),
                                checked: !!attributes.showMeta,
                                onChange: (v) => setAttributes({ showMeta: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Show date', 'brdposts'),
                                checked: !!attributes.showDate,
                                onChange: (v) => setAttributes({ showDate: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Show author', 'brdposts'),
                                checked: !!attributes.showAuthor,
                                onChange: (v) => setAttributes({ showAuthor: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Show categories', 'brdposts'),
                                checked: !!attributes.showCategories,
                                onChange: (v) => setAttributes({ showCategories: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Show reading time', 'brdposts'),
                                checked: !!attributes.showReadingTime,
                                onChange: (v) => setAttributes({ showReadingTime: v })
                            }),
                            wp.element.createElement(ToggleControl, {
                                label: __('Show Read More button', 'brdposts'),
                                checked: !!attributes.showReadMore,
                                onChange: (v) => setAttributes({ showReadMore: v })
                            }),
                            wp.element.createElement(TextControl, {
                                label: __('Read More label', 'brdposts'),
                                value: attributes.readMoreText || 'Ler Mais',
                                onChange: (v) => setAttributes({ readMoreText: v })
                            })
                        ),
                        wp.element.createElement(PanelBody, { title: __('Pagination', 'brdposts'), initialOpen: false },
                            wp.element.createElement(ToggleControl, {
                                label: __('Enable pagination', 'brdposts'),
                                checked: !!attributes.enablePagination,
                                onChange: (v) => setAttributes({ enablePagination: v })
                            })
                        )
                    ),
                    wp.element.createElement('div', { className: 'brdposts-editor-preview' },
                        wp.element.createElement(SSR, {
                            block: 'brdposts/post-block',
                            attributes: attributes
                        })
                    )
                )
            );
        },
        save: () => null
    });
})();