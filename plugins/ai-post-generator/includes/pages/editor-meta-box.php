<?php
/**
 * Meta Box do Editor - Geração de Conteúdo Inline
 * 
 * Renderiza o painel lateral no editor de posts para gerar conteúdo com IA.
 * Permite gerar conteúdo diretamente no editor sem ir para a página de admin.
 * CSS e JS são enfileirados em class-ai-post-generator.php
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Obter valores salvos
$api_provider = get_option('aipg_api_provider', 'groq');
$groq_model = get_option('aipg_groq_model', 'llama-3.3-70b-versatile');
$templates = isset($templates) ? $templates : get_option('aipg_templates', array());

// Obter título do post atual
$post_title = isset($post->post_title) ? $post->post_title : '';

// Modelos Groq disponíveis
$groq_models = array(
    'llama-3.3-70b-versatile' => __('Llama 3.3 70B (Rápido)', 'ai-post-generator'),
    'llama-3.1-70b-versatile' => __('Llama 3.1 70B', 'ai-post-generator'),
    'meta-llama/llama-4-scout-17b-16e-instruct' => __('Scout 17B (Muito Rápido)', 'ai-post-generator'),
    'mixtral-8x7b-32768' => __('Mixtral 8x7B (Longo)', 'ai-post-generator')
);

// Validar configuração
$config_valid = !empty(get_option('aipg_groq_key')) || 
                !empty(get_option('aipg_openai_key')) ||
                !empty(get_option('aipg_anthropic_key')) ||
                !empty(get_option('aipg_huggingface_key')) ||
                !empty(get_option('aipg_cohere_key')) ||
                !empty(get_option('aipg_mistral_key'));
?>

<div id="aipg-editor-metabox" class="aipg-metabox">
    
    <!-- Verificação de Configuração -->
    <?php if (!$config_valid): ?>
        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 12px; margin-bottom: 15px;">
            <p style="margin: 0; font-size: 13px; color: #856404;">
                <strong><?php echo esc_html__('⚠️ Atenção:', 'ai-post-generator'); ?></strong><br>
                <?php echo esc_html__('Nenhuma API configurada. Configure em:', 'ai-post-generator'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=ai-post-generator-settings')); ?>" style="color: #0073aa;">
                    <?php echo esc_html__('AI Posts → Configurações', 'ai-post-generator'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>

    <!-- Formulário Principal -->
    <form id="aipg-editor-form" method="post" action="#">
        <!-- Tópico -->
        <div style="margin-bottom: 15px;">
            <label for="aipg-editor-topic" style="display: block; font-weight: 600; margin-bottom: 5px;">
                <?php echo esc_html__('📌 Tópico/Assunto', 'ai-post-generator'); ?>
            </label>
            <input 
                type="text" 
                id="aipg-editor-topic" 
                name="topic" 
                class="widefat"
                placeholder="<?php echo esc_attr__('Deixe em branco para usar o título', 'ai-post-generator'); ?>"
                value="<?php echo esc_attr($post_title); ?>"
                style="padding: 8px; border-radius: 4px;"
            />
            <small style="color: #666; display: block; margin-top: 3px;">
                <?php echo esc_html__('Descreva o tema do conteúdo', 'ai-post-generator'); ?>
            </small>
        </div>

        <!-- Tom do Post -->
        <div style="margin-bottom: 15px;">
            <label for="aipg-editor-tone" style="display: block; font-weight: 600; margin-bottom: 5px;">
                <?php echo esc_html__('💬 Tom', 'ai-post-generator'); ?>
            </label>
            <select id="aipg-editor-tone" name="tone" class="widefat" style="padding: 8px; border-radius: 4px;">
                <option value="professional"><?php echo esc_html__('Profissional', 'ai-post-generator'); ?></option>
                <option value="casual"><?php echo esc_html__('Casual', 'ai-post-generator'); ?></option>
                <option value="technical"><?php echo esc_html__('Técnico', 'ai-post-generator'); ?></option>
                <option value="educational"><?php echo esc_html__('Educacional', 'ai-post-generator'); ?></option>
                <option value="persuasive"><?php echo esc_html__('Persuasivo', 'ai-post-generator'); ?></option>
                <option value="funny"><?php echo esc_html__('Humorístico', 'ai-post-generator'); ?></option>
            </select>
        </div>

        <!-- Tamanho -->
        <div style="margin-bottom: 15px;">
            <label for="aipg-editor-length" style="display: block; font-weight: 600; margin-bottom: 5px;">
                <?php echo esc_html__('📏 Tamanho', 'ai-post-generator'); ?>
            </label>
            <select id="aipg-editor-length" name="length" class="widefat" style="padding: 8px; border-radius: 4px;">
                <option value="short"><?php echo esc_html__('Curto (300-500)', 'ai-post-generator'); ?></option>
                <option value="medium" selected><?php echo esc_html__('Médio (500-800)', 'ai-post-generator'); ?></option>
                <option value="long"><?php echo esc_html__('Longo (800-1200)', 'ai-post-generator'); ?></option>
                <option value="verylong"><?php echo esc_html__('Muito Longo (1200+)', 'ai-post-generator'); ?></option>
            </select>
        </div>

        <!-- Idioma -->
        <div style="margin-bottom: 15px;">
            <label for="aipg-editor-language" style="display: block; font-weight: 600; margin-bottom: 5px;">
                <?php echo esc_html__('🌍 Idioma', 'ai-post-generator'); ?>
            </label>
            <select id="aipg-editor-language" name="language" class="widefat" style="padding: 8px; border-radius: 4px;">
                <option value="pt-br" selected><?php echo esc_html__('Português', 'ai-post-generator'); ?></option>
                <option value="en"><?php echo esc_html__('Inglês', 'ai-post-generator'); ?></option>
                <option value="es"><?php echo esc_html__('Espanhol', 'ai-post-generator'); ?></option>
            </select>
        </div>

        <!-- Modelo Groq (se selecionado) -->
        <?php if ($api_provider === 'groq'): ?>
            <div style="margin-bottom: 15px;">
                <label for="aipg-editor-groq-model" style="display: block; font-weight: 600; margin-bottom: 5px;">
                    <?php echo esc_html__('🤖 Modelo', 'ai-post-generator'); ?>
                </label>
                <select id="aipg-editor-groq-model" name="groq_model" class="widefat" style="padding: 8px; border-radius: 4px;">
                    <?php foreach ($groq_models as $model_key => $model_label): ?>
                        <option value="<?php echo esc_attr($model_key); ?>" <?php selected($groq_model, $model_key); ?>>
                            <?php echo esc_html($model_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #666; display: block; margin-top: 3px;">
                    <?php echo esc_html__('Modelos mais rápidos geram em segundos', 'ai-post-generator'); ?>
                </small>
            </div>
        <?php endif; ?>

        <!-- Templates -->
        <?php if (!empty($templates)): ?>
            <div style="margin-bottom: 15px;">
                <label for="aipg-editor-template" style="display: block; font-weight: 600; margin-bottom: 5px;">
                    <?php echo esc_html__('📋 Template Salvo', 'ai-post-generator'); ?>
                </label>
                <select id="aipg-editor-template" name="template" class="widefat" style="padding: 8px; border-radius: 4px;">
                    <option value=""><?php echo esc_html__('-- Sem template --', 'ai-post-generator'); ?></option>
                    <?php foreach ($templates as $tpl_id => $template): ?>
                        <option value="<?php echo esc_attr($tpl_id); ?>">
                            <?php echo esc_html($template['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #666; display: block; margin-top: 3px;">
                    <?php echo esc_html__('Carrega configurações salvas', 'ai-post-generator'); ?>
                </small>
            </div>
        <?php endif; ?>

        <!-- Recursos Avançados -->
        <div style="margin-bottom: 15px; padding: 12px; background: #f5f5f5; border-radius: 4px;">
            <label style="font-weight: 600; display: block; margin-bottom: 8px;">
                <?php echo esc_html__('⚙️ Recursos', 'ai-post-generator'); ?>
            </label>

            <div style="margin-bottom: 8px;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; margin: 0;">
                    <input 
                        type="checkbox" 
                        id="aipg-editor-tags" 
                        name="auto_tags" 
                        value="1"
                        style="margin-right: 8px;"
                    />
                    <?php echo esc_html__('Gerar tags', 'ai-post-generator'); ?>
                </label>
            </div>

            <div style="margin-bottom: 8px;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; margin: 0;">
                    <input 
                        type="checkbox" 
                        id="aipg-editor-seo" 
                        name="seo_optimization" 
                        value="1"
                        style="margin-right: 8px;"
                    />
                    <?php echo esc_html__('SEO otimizado', 'ai-post-generator'); ?>
                </label>
            </div>

            <div style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; margin: 0;">
                    <input 
                        type="checkbox" 
                        id="aipg-editor-image" 
                        name="generate_image" 
                        value="1"
                        style="margin-right: 8px;"
                    />
                    <?php echo esc_html__('Imagem destacada', 'ai-post-generator'); ?>
                </label>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div style="display: grid; grid-template-columns: 1fr auto; gap: 8px; margin-bottom: 15px;">
            <button 
                type="button" 
                id="aipg-editor-generate-btn" 
                class="button button-primary"
                style="width: 100%; padding: 10px; font-weight: 600;"
                <?php disabled(!$config_valid); ?>
            >
                <span class="dashicons dashicons-editor-spellcheck" style="vertical-align: middle; margin-right: 5px;"></span>
                <?php echo esc_html__('Gerar', 'ai-post-generator'); ?>
            </button>

            <button 
                type="button" 
                id="aipg-editor-help-btn" 
                class="button button-secondary"
                style="padding: 10px 12px;"
                title="<?php echo esc_attr__('Atalhos e dicas', 'ai-post-generator'); ?>"
            >
                <span class="dashicons dashicons-editor-help"></span>
            </button>
        </div>

        <!-- Status -->
        <div id="aipg-editor-status" style="display: none; padding: 12px; background: #e7f3ff; border-left: 4px solid #2271b1; border-radius: 4px; margin-bottom: 15px;">
            <small style="display: block; color: #0073aa; font-weight: 500;">
                <span class="aipg-loader" style="display: inline-block; width: 12px; height: 12px; border: 2px solid #2271b1; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; margin-right: 5px;"></span>
                <span id="aipg-editor-status-text"><?php echo esc_html__('Gerando conteúdo...', 'ai-post-generator'); ?></span>
            </small>
        </div>

        <!-- Info de Atalhos -->
        <div id="aipg-editor-shortcuts" style="display: none; padding: 12px; background: #f0f0f0; border-radius: 4px; margin-bottom: 15px; font-size: 12px;">
            <p style="margin: 0 0 8px 0; font-weight: 600;">
                ⌨️ <?php echo esc_html__('Atalhos:', 'ai-post-generator'); ?>
            </p>
            <ul style="margin: 0; padding-left: 20px; color: #555;">
                <li><?php echo esc_html__('Ctrl+Shift+G = Gerar conteúdo', 'ai-post-generator'); ?></li>
                <li><?php echo esc_html__('Ctrl+Enter = Submeter (se foco no form)', 'ai-post-generator'); ?></li>
            </ul>
        </div>

        <?php wp_nonce_field('aipg_generate_post', 'aipg_editor_nonce', false); ?>
    </form>

    <!-- Dicas Rápidas -->
    <div style="padding: 12px; background: #fffbea; border-left: 4px solid #ffb81c; border-radius: 4px; margin-top: 15px;">
        <p style="margin: 0; font-size: 12px; color: #666;">
            <strong>💡 Dica:</strong> 
            <?php echo esc_html__('Deixe o tópico em branco para usar o título do post. Escreva conteúdo específico para melhor resultado.', 'ai-post-generator'); ?>
        </p>
    </div>
</div>

<style>
    #aipg-editor-metabox {
        font-size: 13px;
        line-height: 1.5;
    }

    #aipg-editor-metabox .widefat {
        max-width: 100%;
        border: 1px solid #ddd;
        background: #fff;
        box-sizing: border-box;
    }

    #aipg-editor-metabox .widefat:focus {
        border-color: #2271b1;
        box-shadow: 0 0 0 2px rgba(34, 113, 177, 0.1);
        outline: none;
    }

    #aipg-editor-metabox .button {
        border-radius: 4px;
        border: 1px solid #ccc;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    #aipg-editor-metabox .button:hover {
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    #aipg-editor-metabox .button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 600px) {
        #aipg-editor-metabox {
            font-size: 12px;
        }

        #aipg-editor-metabox .widefat {
            padding: 6px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('aipg-editor-form');
    const generateBtn = document.getElementById('aipg-editor-generate-btn');
    const helpBtn = document.getElementById('aipg-editor-help-btn');
    const statusDiv = document.getElementById('aipg-editor-status');
    const statusText = document.getElementById('aipg-editor-status-text');
    const shortcutsDiv = document.getElementById('aipg-editor-shortcuts');
    const templateSelect = document.getElementById('aipg-editor-template');

    // Toggle de ajuda
    helpBtn?.addEventListener('click', function(e) {
        e.preventDefault();
        shortcutsDiv.style.display = shortcutsDiv.style.display === 'none' ? 'block' : 'none';
    });

    // Carrega template
    templateSelect?.addEventListener('change', function() {
        if (this.value) {
            fetch(aipgEditor.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=aipg_get_template&nonce=' + document.querySelector('[name="aipg_editor_nonce"]').value + '&template_id=' + this.value
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('aipg-editor-tone').value = data.data.tone || 'professional';
                    document.getElementById('aipg-editor-length').value = data.data.length || 'medium';
                    document.getElementById('aipg-editor-language').value = data.data.language || 'pt-br';
                }
            });
        }
    });

    // Geração de conteúdo
    generateBtn?.addEventListener('click', function(e) {
        e.preventDefault();

        const topic = document.getElementById('aipg-editor-topic').value;
        if (!topic) {
            alert(aipgEditor.strings.fill_topic);
            return;
        }

        generateBtn.disabled = true;
        statusDiv.style.display = 'block';

        const formData = new FormData();
        formData.append('action', 'aipg_generate_content_only');
        formData.append('nonce', document.querySelector('[name="aipg_editor_nonce"]').value);
        formData.append('topic', topic);
        formData.append('tone', document.getElementById('aipg-editor-tone').value);
        formData.append('length', document.getElementById('aipg-editor-length').value);
        formData.append('language', document.getElementById('aipg-editor-language').value);
        formData.append('auto_tags', document.getElementById('aipg-editor-tags')?.checked ? 1 : 0);
        formData.append('seo_optimization', document.getElementById('aipg-editor-seo')?.checked ? 1 : 0);
        formData.append('generate_image', document.getElementById('aipg-editor-image')?.checked ? 1 : 0);

        fetch(aipgEditor.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                statusText.textContent = aipgEditor.strings.success;
                statusDiv.style.backgroundColor = '#e7ffe7';
                statusDiv.style.borderLeftColor = '#46b450';

                if (data.data.content && window.wp && window.wp.editor) {
                    const blocks = wp.blocks.parse(data.data.content);
                    wp.data.dispatch('core/block-editor').insertBlocks(blocks);
                }

                setTimeout(() => {
                    statusDiv.style.display = 'none';
                }, 3000);
            } else {
                statusText.textContent = data.data.message || aipgEditor.strings.error;
                statusDiv.style.backgroundColor = '#ffe7e7';
                statusDiv.style.borderLeftColor = '#dc3232';
            }
        })
        .catch(err => {
            statusText.textContent = aipgEditor.strings.error + ': ' + err.message;
            statusDiv.style.backgroundColor = '#ffe7e7';
            statusDiv.style.borderLeftColor = '#dc3232';
        })
        .finally(() => {
            generateBtn.disabled = false;
        });
    });

    // Atalho: Ctrl+Shift+G
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'G') {
            e.preventDefault();
            generateBtn?.click();
        }
    });
});
</script>