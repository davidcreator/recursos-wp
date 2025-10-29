(function() {
    'use strict';
    
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, MediaUpload, RichText, ColorPalette } = wp.blockEditor;
    const { PanelBody, Button, RangeControl, SelectControl, ToggleControl, TextControl } = wp.components;
    const { Fragment } = wp.element;
    const { __ } = wp.i18n;
    
    // Hero Section Block
    registerBlockType('nosfirnews/hero-section', {
        title: __('Hero Section', 'nosfirnews'),
        icon: 'format-image',
        category: 'nosfirnews',
        attributes: {
            title: {
                type: 'string',
                default: 'Hero Title'
            },
            subtitle: {
                type: 'string',
                default: 'Hero Subtitle'
            },
            backgroundImage: {
                type: 'string',
                default: ''
            },
            buttonText: {
                type: 'string',
                default: 'Learn More'
            },
            buttonUrl: {
                type: 'string',
                default: '#'
            },
            overlayOpacity: {
                type: 'number',
                default: 0.5
            }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, subtitle, backgroundImage, buttonText, buttonUrl, overlayOpacity } = attributes;
            
            return (
                Fragment({},
                    InspectorControls({},
                        PanelBody({ title: __('Hero Settings', 'nosfirnews') },
                            MediaUpload({
                                onSelect: function(media) {
                                    setAttributes({ backgroundImage: media.url });
                                },
                                type: 'image',
                                value: backgroundImage,
                                render: function(obj) {
                                    return Button({
                                        className: backgroundImage ? 'image-button' : 'button button-large',
                                        onClick: obj.open
                                    }, backgroundImage ? 'Change Image' : __('Select Background Image', 'nosfirnews'));
                                }
                            }),
                            RangeControl({
                                label: __('Overlay Opacity', 'nosfirnews'),
                                value: overlayOpacity,
                                onChange: function(value) {
                                    setAttributes({ overlayOpacity: value });
                                },
                                min: 0,
                                max: 1,
                                step: 0.1
                            }),
                            TextControl({
                                label: __('Button URL', 'nosfirnews'),
                                value: buttonUrl,
                                onChange: function(value) {
                                    setAttributes({ buttonUrl: value });
                                }
                            })
                        )
                    ),
                    
                    wp.element.createElement('div', {
                        className: 'nosfirnews-hero-block-editor',
                        style: {
                            backgroundImage: backgroundImage ? `url(${backgroundImage})` : 'none',
                            minHeight: '400px',
                            position: 'relative',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: 'white',
                            textAlign: 'center'
                        }
                    },
                        wp.element.createElement('div', {
                            style: {
                                position: 'absolute',
                                top: 0,
                                left: 0,
                                right: 0,
                                bottom: 0,
                                backgroundColor: 'rgba(0,0,0,' + overlayOpacity + ')'
                            }
                        }),
                        wp.element.createElement('div', {
                            style: { position: 'relative', zIndex: 1 }
                        },
                            RichText({
                                tagName: 'h1',
                                placeholder: __('Hero Title', 'nosfirnews'),
                                value: title,
                                onChange: function(value) {
                                    setAttributes({ title: value });
                                },
                                style: { fontSize: '2.5em', marginBottom: '0.5em' }
                            }),
                            RichText({
                                tagName: 'p',
                                placeholder: __('Hero Subtitle', 'nosfirnews'),
                                value: subtitle,
                                onChange: function(value) {
                                    setAttributes({ subtitle: value });
                                },
                                style: { fontSize: '1.2em', marginBottom: '1em' }
                            }),
                            RichText({
                                tagName: 'span',
                                placeholder: __('Button Text', 'nosfirnews'),
                                value: buttonText,
                                onChange: function(value) {
                                    setAttributes({ buttonText: value });
                                },
                                style: {
                                    display: 'inline-block',
                                    padding: '10px 20px',
                                    backgroundColor: '#007cba',
                                    color: 'white',
                                    textDecoration: 'none',
                                    borderRadius: '4px'
                                }
                            })
                        )
                    )
                )
            );
        },
        
        save: function() {
            return null; // Rendered by PHP
        }
    });
    
    // Call to Action Block
    registerBlockType('nosfirnews/call-to-action', {
        title: __('Call to Action', 'nosfirnews'),
        icon: 'megaphone',
        category: 'nosfirnews',
        attributes: {
            title: {
                type: 'string',
                default: 'Call to Action'
            },
            description: {
                type: 'string',
                default: 'Description text'
            },
            buttonText: {
                type: 'string',
                default: 'Get Started'
            },
            buttonUrl: {
                type: 'string',
                default: '#'
            },
            backgroundColor: {
                type: 'string',
                default: '#007cba'
            },
            textColor: {
                type: 'string',
                default: '#ffffff'
            }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, description, buttonText, buttonUrl, backgroundColor, textColor } = attributes;
            
            const colors = [
                { name: 'Blue', color: '#007cba' },
                { name: 'Green', color: '#00a32a' },
                { name: 'Red', color: '#d63638' },
                { name: 'Orange', color: '#ff6900' },
                { name: 'Purple', color: '#8f5ee8' },
                { name: 'Black', color: '#000000' },
                { name: 'White', color: '#ffffff' }
            ];
            
            return (
                Fragment({},
                    InspectorControls({},
                        PanelBody({ title: __('CTA Settings', 'nosfirnews') },
                            wp.element.createElement('p', {}, __('Background Color', 'nosfirnews')),
                            ColorPalette({
                                colors: colors,
                                value: backgroundColor,
                                onChange: function(value) {
                                    setAttributes({ backgroundColor: value });
                                }
                            }),
                            wp.element.createElement('p', {}, __('Text Color', 'nosfirnews')),
                            ColorPalette({
                                colors: colors,
                                value: textColor,
                                onChange: function(value) {
                                    setAttributes({ textColor: value });
                                }
                            }),
                            TextControl({
                                label: __('Button URL', 'nosfirnews'),
                                value: buttonUrl,
                                onChange: function(value) {
                                    setAttributes({ buttonUrl: value });
                                }
                            })
                        )
                    ),
                    
                    wp.element.createElement('div', {
                        className: 'nosfirnews-cta-block-editor',
                        style: {
                            backgroundColor: backgroundColor,
                            color: textColor,
                            padding: '40px',
                            textAlign: 'center'
                        }
                    },
                        RichText({
                            tagName: 'h2',
                            placeholder: __('CTA Title', 'nosfirnews'),
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            },
                            style: { marginBottom: '1em' }
                        }),
                        RichText({
                            tagName: 'p',
                            placeholder: __('CTA Description', 'nosfirnews'),
                            value: description,
                            onChange: function(value) {
                                setAttributes({ description: value });
                            },
                            style: { marginBottom: '1.5em' }
                        }),
                        RichText({
                            tagName: 'span',
                            placeholder: __('Button Text', 'nosfirnews'),
                            value: buttonText,
                            onChange: function(value) {
                                setAttributes({ buttonText: value });
                            },
                            style: {
                                display: 'inline-block',
                                padding: '12px 24px',
                                backgroundColor: textColor,
                                color: backgroundColor,
                                borderRadius: '4px',
                                fontWeight: 'bold'
                            }
                        })
                    )
                )
            );
        },
        
        save: function() {
            return null; // Rendered by PHP
        }
    });
    
    // Featured Posts Block
    registerBlockType('nosfirnews/featured-posts', {
        title: __('Featured Posts', 'nosfirnews'),
        icon: 'grid-view',
        category: 'nosfirnews',
        attributes: {
            numberOfPosts: {
                type: 'number',
                default: 3
            },
            category: {
                type: 'string',
                default: ''
            },
            layout: {
                type: 'string',
                default: 'grid'
            },
            showExcerpt: {
                type: 'boolean',
                default: true
            },
            showDate: {
                type: 'boolean',
                default: true
            },
            showAuthor: {
                type: 'boolean',
                default: true
            }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { numberOfPosts, category, layout, showExcerpt, showDate, showAuthor } = attributes;
            
            return (
                Fragment({},
                    InspectorControls({},
                        PanelBody({ title: __('Posts Settings', 'nosfirnews') },
                            RangeControl({
                                label: __('Number of Posts', 'nosfirnews'),
                                value: numberOfPosts,
                                onChange: function(value) {
                                    setAttributes({ numberOfPosts: value });
                                },
                                min: 1,
                                max: 12
                            }),
                            TextControl({
                                label: __('Category Slug', 'nosfirnews'),
                                value: category,
                                onChange: function(value) {
                                    setAttributes({ category: value });
                                },
                                help: __('Leave empty to show posts from all categories', 'nosfirnews')
                            }),
                            SelectControl({
                                label: __('Layout', 'nosfirnews'),
                                value: layout,
                                options: [
                                    { label: __('Grid', 'nosfirnews'), value: 'grid' },
                                    { label: __('List', 'nosfirnews'), value: 'list' },
                                    { label: __('Carousel', 'nosfirnews'), value: 'carousel' }
                                ],
                                onChange: function(value) {
                                    setAttributes({ layout: value });
                                }
                            }),
                            ToggleControl({
                                label: __('Show Excerpt', 'nosfirnews'),
                                checked: showExcerpt,
                                onChange: function(value) {
                                    setAttributes({ showExcerpt: value });
                                }
                            }),
                            ToggleControl({
                                label: __('Show Date', 'nosfirnews'),
                                checked: showDate,
                                onChange: function(value) {
                                    setAttributes({ showDate: value });
                                }
                            }),
                            ToggleControl({
                                label: __('Show Author', 'nosfirnews'),
                                checked: showAuthor,
                                onChange: function(value) {
                                    setAttributes({ showAuthor: value });
                                }
                            })
                        )
                    ),
                    
                    wp.element.createElement('div', {
                        className: 'nosfirnews-featured-posts-editor'
                    },
                        wp.element.createElement('h3', {}, __('Featured Posts Block', 'nosfirnews')),
                        wp.element.createElement('p', {}, 
                            __('Showing', 'nosfirnews') + ' ' + numberOfPosts + ' ' + 
                            __('posts in', 'nosfirnews') + ' ' + layout + ' ' + __('layout', 'nosfirnews')
                        ),
                        category && wp.element.createElement('p', {}, 
                            __('Category:', 'nosfirnews') + ' ' + category
                        )
                    )
                )
            );
        },
        
        save: function() {
            return null; // Rendered by PHP
        }
    });
    
    // Testimonial Block
    registerBlockType('nosfirnews/testimonial', {
        title: __('Testimonial', 'nosfirnews'),
        icon: 'format-quote',
        category: 'nosfirnews',
        attributes: {
            quote: {
                type: 'string',
                default: 'This is a testimonial quote.'
            },
            author: {
                type: 'string',
                default: 'John Doe'
            },
            position: {
                type: 'string',
                default: 'CEO, Company'
            },
            avatar: {
                type: 'string',
                default: ''
            },
            style: {
                type: 'string',
                default: 'default'
            }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { quote, author, position, avatar, style } = attributes;
            
            return (
                Fragment({},
                    InspectorControls({},
                        PanelBody({ title: __('Testimonial Settings', 'nosfirnews') },
                            MediaUpload({
                                onSelect: function(media) {
                                    setAttributes({ avatar: media.url });
                                },
                                type: 'image',
                                value: avatar,
                                render: function(obj) {
                                    return Button({
                                        className: avatar ? 'image-button' : 'button button-large',
                                        onClick: obj.open
                                    }, avatar ? 'Change Avatar' : __('Select Avatar', 'nosfirnews'));
                                }
                            }),
                            SelectControl({
                                label: __('Style', 'nosfirnews'),
                                value: style,
                                options: [
                                    { label: __('Default', 'nosfirnews'), value: 'default' },
                                    { label: __('Boxed', 'nosfirnews'), value: 'boxed' },
                                    { label: __('Minimal', 'nosfirnews'), value: 'minimal' }
                                ],
                                onChange: function(value) {
                                    setAttributes({ style: value });
                                }
                            })
                        )
                    ),
                    
                    wp.element.createElement('div', {
                        className: 'nosfirnews-testimonial-editor',
                        style: {
                            padding: '20px',
                            border: '1px solid #ddd',
                            borderRadius: '4px',
                            textAlign: 'center'
                        }
                    },
                        RichText({
                            tagName: 'blockquote',
                            placeholder: __('Enter testimonial quote...', 'nosfirnews'),
                            value: quote,
                            onChange: function(value) {
                                setAttributes({ quote: value });
                            },
                            style: {
                                fontSize: '1.2em',
                                fontStyle: 'italic',
                                marginBottom: '1em'
                            }
                        }),
                        wp.element.createElement('div', {
                            style: { display: 'flex', alignItems: 'center', justifyContent: 'center' }
                        },
                            avatar && wp.element.createElement('img', {
                                src: avatar,
                                alt: author,
                                style: {
                                    width: '50px',
                                    height: '50px',
                                    borderRadius: '50%',
                                    marginRight: '15px'
                                }
                            }),
                            wp.element.createElement('div', {},
                                RichText({
                                    tagName: 'cite',
                                    placeholder: __('Author Name', 'nosfirnews'),
                                    value: author,
                                    onChange: function(value) {
                                        setAttributes({ author: value });
                                    },
                                    style: { fontWeight: 'bold', display: 'block' }
                                }),
                                RichText({
                                    tagName: 'span',
                                    placeholder: __('Position, Company', 'nosfirnews'),
                                    value: position,
                                    onChange: function(value) {
                                        setAttributes({ position: value });
                                    },
                                    style: { fontSize: '0.9em', color: '#666' }
                                })
                            )
                        )
                    )
                )
            );
        },
        
        save: function() {
            return null; // Rendered by PHP
        }
    });
    
})();