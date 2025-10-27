(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor || wp.editor;
    const { ServerSideRender } = wp.serverSideRender ? wp.serverSideRender : (wp.editor ? wp.editor.ServerSideRender : null);
    const { PanelBody, SelectControl, ToggleControl, RangeControl, TextControl, Spinner } = wp.components;
    const { __ } = wp.i18n;
    const { useState, useEffect } = wp.element;
    const apiFetch = wp.apiFetch;

    registerBlockType('bdrposts/post-block', {
        title: __('BDR Posts', 'bdrposts'),
        icon: 'grid-view',
        category: 'widgets',
        keywords: [__('posts', 'bdrposts'), __('grid', 'bdrposts'), __('blog', 'bdrposts')],
        supports: {
            align: ['wide', 'full'],
            html: false
        },
        attributes: {
            layout: { type: 'string', default: 'grid' },
            subLayout: { type: 'string', default: 'title-meta' },
            postType: { type: 'string', default: 'post' },
            postsPerPage: { type: 'number', default: 6 },
            columns: { type: 'number', default: 3 },
            order: { type: 'string', default: 'DESC' },
            orderBy: { type: 'string', default: 'date' },
            categories: { type: 'array', default: [] },
            tags: { type: 'array', default: [] },
            authors: { type: 'array', default: [] },
            offset: { type: 'number', default: 0 },
            includePosts: { type: 'array', default: [] },
            excludePosts: { type: 'array', default: [] },
            excludeCurrent: { type: 'boolean', default: false },
            showImage: { type: 'boolean', default: true },
            imageSize: { type: 'string', default: 'medium' },
            linkImage: { type: 'boolean', default: true },
            showTitle: { type: 'boolean', default: true },
            linkTitle: { type: 'boolean', default: true },
            showExcerpt: { type: 'boolean', default: true },
            excerptLength: { type: 'number', default: 20 },
            showMeta: { type: 'boolean', default: true },
            showDate: { type: 'boolean', default: true },
            showAuthor: { type: 'boolean', default: true },
            showCategories: { type: 'boolean', default: true },
            showTags: { type: 'boolean', default: false },
            linkAuthor: { type: 'boolean', default: true },
            taxonomy: { type: 'string', default: '' },
            taxonomyTerms: { type: 'array', default: [] },
            showReadMore: { type: 'boolean', default: true },
            readMoreText: { type: 'string', default: 'Ler Mais' },
            enablePagination: { type: 'boolean', default: false },
            showReadingTime: { type: 'boolean', default: false }
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const [postTypes, setPostTypes] = useState([]);
            const [taxonomies, setTaxonomies] = useState([]);

            useEffect(() => {
                // Carrega post types
                apiFetch({ path: '/bdrposts/v1/post-types' })
                    .then(data => setPostTypes(data))
                    .catch(() => setPostTypes([
                        { label: 'Post', value: 'post' },
                        { label: 'Page', value: 'page' }
                    ]));
            }, []);

            useEffect(() => {
                // Carrega taxonomias quando post type muda
                if (attributes.postType) {
                    apiFetch({ path: `/bdrposts/v1/taxonomies/${attributes.postType}` })
                        .then(data => setTaxonomies(data))
                        .catch(() => setTaxonomies([]));
                }
            }, [attributes.postType]);

            const parseCommaSeparated = (value) => {
                if (!value) return [];
                return value.split(',').map(v => parseInt(v.trim(), 10)).filter(v => !isNaN(v));
            };

            const arrayToString = (arr) => {
                if (!arr || !Array.isArray(arr)) return '';
                return arr.join(',');
            };

            return wp.element.createElement('div', { className: 'bdrposts-editor-wrapper' },
                wp.element.createElement(InspectorControls, {},
                    // Layout Panel
                    wp.element.createElement(PanelBody, { 
                        title: __('Layout', 'bdrposts'), 
                        initialOpen: true 
                    },
                        wp.element.createElement(SelectControl, {
                            label: __('Layout Principal', 'bdrposts'),
                            value: attributes.layout,
                            options: [
                                { label: __('Grid', 'bdrposts'), value: 'grid' },
                                { label: __('Masonry', 'bdrposts'), value: 'masonry' },
                                { label: __('Slider', 'bdrposts'), value: 'slider' },
                                { label: __('Ticker', 'bdrposts'), value: 'ticker' }
                            ],
                            onChange: (v) => setAttributes({ layout: v })
                        }),
                        attributes.layout !== 'ticker' && wp.element.createElement(SelectControl, {
                            label: __('Sub-layout', 'bdrposts'),
                            value: attributes.subLayout,
                            options: [
                                { label: __('Title + Meta', 'bdrposts'), value: 'title-meta' },
                                { label: __('Meta + Title', 'bdrposts'), value: 'meta-title' },
                                { label: __('Imagem à Esquerda', 'bdrposts'), value: 'left-image' },
                                { label: __('Imagem à Direita', 'bdrposts'), value: 'right-image' },
                                { label: __('Overlay', 'bdrposts'), value: 'overlay' }
                            ],
                            onChange: (v) => setAttributes({ subLayout: v })
                        }),
                        attributes.layout !== 'ticker' && attributes.layout !== 'slider' && wp.element.createElement(RangeControl, {
                            label: __('Colunas', 'bdrposts'),
                            value: attributes.columns,
                            min: 1,
                            max: 6,
                            onChange: (v) => setAttributes({ columns: v })
                        })
                    ),

                    // Query Panel
                    wp.element.createElement(PanelBody, { 
                        title: __('Consulta', 'bdrposts'), 
                        initialOpen: true 
                    },
                        wp.element.createElement(SelectControl, {
                            label: __('Tipo de Post', 'bdrposts'),
                            value: attributes.postType,
                            options: postTypes.length > 0 ? postTypes : [
                                { label: 'Post', value: 'post' },
                                { label: 'Page', value: 'page' }
                            ],
                            onChange: (v) => setAttributes({ postType: v })
                        }),
                        wp.element.createElement(RangeControl, {
                            label: __('Posts por Página', 'bdrposts'),
                            value: attributes.postsPerPage,
                            min: 1,
                            max: 50,
                            onChange: (v) => setAttributes({ postsPerPage: v })
                        }),
                        wp.element.createElement(SelectControl, {
                            label: __('Ordenar Por', 'bdrposts'),
                            value: attributes.orderBy,
                            options: [
                                { label: __('Data', 'bdrposts'), value: 'date' },
                                { label: __('Título', 'bdrposts'), value: 'title' },
                                { label: __('Modificado', 'bdrposts'), value: 'modified' },
                                { label: __('Ordem do Menu', 'bdrposts'), value: 'menu_order' },
                                { label: __('Aleatório', 'bdrposts'), value: 'rand' }
                            ],
                            onChange: (v) => setAttributes({ orderBy: v })
                        }),
                        wp.element.createElement(SelectControl, {
                            label: __('Ordem', 'bdrposts'),
                            value: attributes.order,
                            options: [
                                { label: 'DESC', value: 'DESC' },
                                { label: 'ASC', value: 'ASC' }
                            ],
                            onChange: (v) => setAttributes({ order: v })
                        }),
                        wp.element.createElement(RangeControl, {
                            label: __('Offset', 'bdrposts'),
                            help: __('Pular os primeiros N posts', 'bdrposts'),
                            value: attributes.offset,
                            min: 0,
                            max: 50,
                            onChange: (v) => setAttributes({ offset: v })
                        })
                    ),

                    // Filtros Panel
                    wp.element.createElement(PanelBody, { 
                        title: __('Filtros', 'bdrposts'), 
                        initialOpen: false 
                    },
                        wp.element.createElement(TextControl, {
                            label: __('IDs de Categorias', 'bdrposts'),
                            help: __('Separados por vírgula (ex: 1,2,3)', 'bdrposts'),
                            value: arrayToString(attributes.categories),
                            onChange: (v) => setAttributes({ categories: parseCommaSeparated(v) })
                        }),
                        wp.element.createElement(TextControl, {
                            label: __('IDs de Tags', 'bdrposts'),
                            help: __('Separados por vírgula', 'bdrposts'),
                            value: arrayToString(attributes.tags),
                            onChange: (v) => setAttributes({ tags: parseCommaSeparated(v) })
                        }),
                        wp.element.createElement(TextControl, {
                            label: __('IDs de Autores', 'bdrposts'),
                            help: __('Separados por vírgula', 'bdrposts'),
                            value: arrayToString(attributes.authors),
                            onChange: (v) => setAttributes({ authors: parseCommaSeparated(v) })
                        }),
                        wp.element.createElement(TextControl, {
                            label: __('Incluir Posts (IDs)', 'bdrposts'),
                            help: __('Separados por vírgula', 'bdrposts'),
                            value: arrayToString(attributes.includePosts),
                            onChange: (v) => setAttributes({ includePosts: parseCommaSeparated(v) })
                        }),
                        wp.element.createElement(TextControl, {
                            label: __('Excluir Posts (IDs)', 'bdrposts'),
                            help: __('Separados por vírgula', 'bdrposts'),
                            value: arrayToString(attributes.excludePosts),
                            onChange: (v) => setAttributes({ excludePosts: parseCommaSeparated(v) })
                        }),
                        wp.element.createElement(ToggleControl, {
                            label: __('Excluir Post Atual', 'bdrposts'),
                            checked: attributes.excludeCurrent,
                            onChange: (v) => setAttributes({ excludeCurrent: v })
                        }),
                        taxonomies.length > 0 && wp.element.createElement(SelectControl, {
                            label: __('Taxonomia Customizada', 'bdrposts'),
                            value: attributes.taxonomy,
                            options: [
                                { label: __('Nenhuma', 'bdrposts'), value: '' },
                                ...taxonomies
                            ],
                            onChange: (v) => setAttributes({ taxonomy: v })
                        })
                    ),

                    // Elementos Panel
                    wp.element.createElement(PanelBody, { 
                        title: __('Elementos Visíveis', 'bdrposts'), 
                        initialOpen: false 
                    },
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Imagem Destacada', 'bdrposts'),
                            checked: attributes.showImage,
                            onChange: (v) => setAttributes({ showImage: v })
                        }),
                        attributes.showImage && wp.element.createElement(SelectControl, {
                            label: __('Tamanho da Imagem', 'bdrposts'),
                            value: attributes.imageSize,
                            options: [
                                { label: 'Thumbnail', value: 'thumbnail' },
                                { label: 'Medium', value: 'medium' },
                                { label: 'Large', value: 'large' },
                                { label: 'Full', value: 'full' }
                            ],
                            onChange: (v) => setAttributes({ imageSize: v })
                        }),
                        attributes.showImage && wp.element.createElement(ToggleControl, {
                            label: __('Link na Imagem', 'bdrposts'),
                            checked: attributes.linkImage,
                            onChange: (v) => setAttributes({ linkImage: v })
                        }),
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Título', 'bdrposts'),
                            checked: attributes.showTitle,
                            onChange: (v) => setAttributes({ showTitle: v })
                        }),
                        attributes.showTitle && wp.element.createElement(ToggleControl, {
                            label: __('Link no Título', 'bdrposts'),
                            checked: attributes.linkTitle,
                            onChange: (v) => setAttributes({ linkTitle: v })
                        }),
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Resumo', 'bdrposts'),
                            checked: attributes.showExcerpt,
                            onChange: (v) => setAttributes({ showExcerpt: v })
                        }),
                        attributes.showExcerpt && wp.element.createElement(RangeControl, {
                            label: __('Palavras no Resumo', 'bdrposts'),
                            value: attributes.excerptLength,
                            min: 5,
                            max: 100,
                            onChange: (v) => setAttributes({ excerptLength: v })
                        }),
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Botão Ler Mais', 'bdrposts'),
                            checked: attributes.showReadMore,
                            onChange: (v) => setAttributes({ showReadMore: v })
                        }),
                        attributes.showReadMore && wp.element.createElement(TextControl, {
                            label: __('Texto do Botão', 'bdrposts'),
                            value: attributes.readMoreText,
                            onChange: (v) => setAttributes({ readMoreText: v })
                        })
                    ),

                    // Meta Panel
                    wp.element.createElement(PanelBody, { 
                        title: __('Meta Informações', 'bdrposts'), 
                        initialOpen: false 
                    },
                        wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Meta', 'bdrposts'),
                            checked: attributes.showMeta,
                            onChange: (v) => setAttributes({ showMeta: v })
                        }),
                        attributes.showMeta && wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Data', 'bdrposts'),
                            checked: attributes.showDate,
                            onChange: (v) => setAttributes({ showDate: v })
                        }),
                        attributes.showMeta && wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Autor', 'bdrposts'),
                            checked: attributes.showAuthor,
                            onChange: (v) => setAttributes({ showAuthor: v })
                        }),
                        attributes.showMeta && attributes.showAuthor && wp.element.createElement(ToggleControl, {
                            label: __('Link no Autor', 'bdrposts'),
                            checked: attributes.linkAuthor,
                            onChange: (v) => setAttributes({ linkAuthor: v })
                        }),
                        attributes.showMeta && wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Categorias', 'bdrposts'),
                            checked: attributes.showCategories,
                            onChange: (v) => setAttributes({ showCategories: v })
                        }),
                        attributes.showMeta && wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Tags', 'bdrposts'),
                            checked: attributes.showTags,
                            onChange: (v) => setAttributes({ showTags: v })
                        }),
                        attributes.showMeta && wp.element.createElement(ToggleControl, {
                            label: __('Mostrar Tempo de Leitura', 'bdrposts'),
                            checked: attributes.showReadingTime,
                            onChange: (v) => setAttributes({ showReadingTime: v })
                        })
                    ),

                    // Paginação Panel
                    wp.element.createElement(PanelBody, { 
                        title: __('Paginação', 'bdrposts'), 
                        initialOpen: false 
                    },
                        wp.element.createElement(ToggleControl, {
                            label: __('Ativar Paginação', 'bdrposts'),
                            checked: attributes.enablePagination,
                            onChange: (v) => setAttributes({ enablePagination: v })
                        })
                    )
                ),

                // Preview
                wp.element.createElement('div', { className: 'bdrposts-editor-preview' },
                    wp.element.createElement('div', { className: 'bdrposts-editor-header' },
                        wp.element.createElement('h3', {}, __('📋 BDR Posts', 'bdrposts')),
                        wp.element.createElement('p', {}, __('Visualização do bloco', 'bdrposts'))
                    ),
                    ServerSideRender 
                        ? wp.element.createElement(ServerSideRender, {
                            block: 'bdrposts/post-block',
                            attributes: attributes,
                            EmptyResponsePlaceholder: () => wp.element.createElement('p', {}, __('Nenhum post encontrado.', 'bdrposts')),
                            LoadingResponsePlaceholder: () => wp.element.createElement('div', { className: 'bdrposts-loading' }, 
                                wp.element.createElement(Spinner, {}),
                                wp.element.createElement('p', {}, __('Carregando...', 'bdrposts'))
                            )
                        })
                        : wp.element.createElement('p', {}, __('Preview não disponível. O bloco será renderizado no frontend.', 'bdrposts')),
                    wp.element.createElement('p', { className: 'bdrposts-editor-note' },
                        __('Use os controles à direita para personalizar a exibição', 'bdrposts')
                    )
                )
            );
        },
        save: () => null
    });
})();