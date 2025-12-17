/* AI Post Generator - Gutenberg Plugin */

(function() {
    const { registerPlugin } = wp.plugins;
    const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
    const { PanelBody, Button, TextControl, SelectControl, CheckboxControl } = wp.components;
    const { useState } = wp.element;
    const { useSelect, useDispatch } = wp.data;
    const { __ } = wp.i18n;

    const AIPostGeneratorSidebar = () => {
        const [topic, setTopic] = useState('');
        const [tone, setTone] = useState('professional');
        const [length, setLength] = useState('medium');
        const [generateImage, setGenerateImage] = useState(false);
        const [isGenerating, setIsGenerating] = useState(false);
        const [status, setStatus] = useState('');

        const { editPost } = useDispatch('core/editor');
        const { createNotice } = useDispatch('core/notices');

        const postTitle = useSelect((select) => {
            return select('core/editor').getEditedPostAttribute('title');
        });

        const generateContent = () => {
            const topicToUse = topic || postTitle;

            if (!topicToUse) {
                createNotice('error', __('Por favor, preencha o título ou tópico.', 'ai-post-generator'), {
                    isDismissible: true,
                });
                return;
            }

            setIsGenerating(true);
            setStatus(__('Gerando conteúdo...', 'ai-post-generator'));

            const data = new FormData();
            data.append('action', 'aipg_generate_content_only');
            data.append('nonce', aipgGutenberg.nonce);
            data.append('topic', topicToUse);
            data.append('tone', tone);
            data.append('length', length);
            data.append('language', 'pt-br');

            fetch(aipgGutenberg.ajax_url, {
                method: 'POST',
                body: data,
            })
                .then((response) => response.json())
                .then((response) => {
                    if (response.success) {
                        // Insere conteúdo
                        const blocks = wp.blocks.parse(response.data.content);
                        wp.data.dispatch('core/block-editor').insertBlocks(blocks);

                        // Atualiza título se vazio
                        if (!postTitle) {
                            editPost({ title: response.data.title });
                        }

                        setStatus('');
                        createNotice('success', __('Conteúdo gerado com sucesso!', 'ai-post-generator'), {
                            isDismissible: true,
                        });
                    } else {
                        setStatus('');
                        createNotice('error', response.data.message || __('Erro ao gerar conteúdo', 'ai-post-generator'), {
                            isDismissible: true,
                        });
                    }
                })
                .catch((error) => {
                    setStatus('');
                    createNotice('error', __('Erro de conexão', 'ai-post-generator'), {
                        isDismissible: true,
                    });
                })
                .finally(() => {
                    setIsGenerating(false);
                });
        };

        return (
            <>
                <PluginSidebarMoreMenuItem target="ai-post-generator-sidebar">
                    {__('✨ Gerar com IA', 'ai-post-generator')}
                </PluginSidebarMoreMenuItem>

                <PluginSidebar
                    name="ai-post-generator-sidebar"
                    title={__('✨ Gerar Conteúdo com IA', 'ai-post-generator')}
                >
                    <PanelBody>
                        <p style={{ fontSize: '13px', color: '#646970', marginBottom: '16px' }}>
                            {__('Preencha o título acima e clique em gerar para criar o conteúdo automaticamente.', 'ai-post-generator')}
                        </p>

                        <TextControl
                            label={__('Tópico/Tema', 'ai-post-generator')}
                            value={topic}
                            onChange={setTopic}
                            placeholder={__('Deixe em branco para usar o título', 'ai-post-generator')}
                            help={__('Ou use o título do post automaticamente', 'ai-post-generator')}
                        />

                        <SelectControl
                            label={__('Tamanho', 'ai-post-generator')}
                            value={length}
                            options={[
                                { label: __('Curto (300-500)', 'ai-post-generator'), value: 'short' },
                                { label: __('Médio (500-800)', 'ai-post-generator'), value: 'medium' },
                                { label: __('Longo (800-1200)', 'ai-post-generator'), value: 'long' },
                                { label: __('Muito Longo (1200-2000)', 'ai-post-generator'), value: 'verylong' },
                            ]}
                            onChange={setLength}
                        />

                        <SelectControl
                            label={__('Tom', 'ai-post-generator')}
                            value={tone}
                            options={[
                                { label: __('Profissional', 'ai-post-generator'), value: 'professional' },
                                { label: __('Casual', 'ai-post-generator'), value: 'casual' },
                                { label: __('Técnico', 'ai-post-generator'), value: 'technical' },
                                { label: __('Amigável', 'ai-post-generator'), value: 'friendly' },
                                { label: __('Educacional', 'ai-post-generator'), value: 'educational' },
                            ]}
                            onChange={setTone}
                        />

                        <CheckboxControl
                            label={__('Gerar imagem destacada', 'ai-post-generator')}
                            checked={generateImage}
                            onChange={setGenerateImage}
                        />

                        <Button
                            isPrimary
                            isLarge
                            onClick={generateContent}
                            disabled={isGenerating}
                            style={{ marginTop: '16px', width: '100%' }}
                        >
                            {isGenerating ? __('Gerando...', 'ai-post-generator') : __('✨ Gerar Conteúdo', 'ai-post-generator')}
                        </Button>

                        {status && (
                            <div style={{
                                marginTop: '16px',
                                padding: '10px',
                                background: '#f0f6fc',
                                borderLeft: '4px solid #2271b1',
                                borderRadius: '4px',
                                fontSize: '13px',
                            }}>
                                {status}
                            </div>
                        )}

                        <div style={{
                            marginTop: '16px',
                            padding: '12px',
                            background: '#f9f9f9',
                            borderRadius: '4px',
                            fontSize: '12px',
                            color: '#646970',
                        }}>
                            <strong>{__('Atalho:', 'ai-post-generator')}</strong>
                            <br />
                            Ctrl/Cmd + Shift + G
                        </div>
                    </PanelBody>
                </PluginSidebar>
            </>
        );
    };

    registerPlugin('ai-post-generator', {
        render: AIPostGeneratorSidebar,
        icon: 'edit',
    });
})();