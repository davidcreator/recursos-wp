<?php
/**
 * Página Principal - Geração de Posts
 * 
 * Renderiza o formulário principal para geração de posts com IA.
 * CSS e JS são enfileirados em class-ai-post-generator.php
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Obter dados salvos
$api_provider = get_option('aipg_api_provider', 'groq');
$image_provider = get_option('aipg_image_provider', 'pollinations');
$default_category = get_option('aipg_default_category', 0);
$templates = get_option('aipg_templates', array());
$post_status = get_option('aipg_post_status', 'draft');
$groq_model = get_option('aipg_groq_model', 'llama-3.3-70b-versatile');

// Obter categorias
$categories = get_categories(array('hide_empty' => false));

// Modelos Groq disponíveis
$groq_models = array(
    'llama-3.3-70b-versatile' => __('Llama 3.3 70B (Recomendado)', 'ai-post-generator'),
    'llama-3.1-70b-versatile' => __('Llama 3.1 70B (Anterior)', 'ai-post-generator'),
    'meta-llama/llama-4-scout-17b-16e-instruct' => __('Llama 4 Scout 17B (Experimental)', 'ai-post-generator'),
    'mixtral-8x7b-32768' => __('Mixtral 8x7B (Contexto Longo)', 'ai-post-generator')
);

// Validar configuração
$config_valid = false;
$config_message = '';

switch ($api_provider) {
    case 'groq':
        if (empty(get_option('aipg_groq_key'))) {
            $config_message = __('Configure a chave API Groq nas configurações', 'ai-post-generator');
        } else {
            $config_valid = true;
        }
        break;
    case 'openai':
        if (empty(get_option('aipg_openai_key'))) {
            $config_message = __('Configure a chave API OpenAI nas configurações', 'ai-post-generator');
        } else {
            $config_valid = true;
        }
        break;
    case 'anthropic':
        if (empty(get_option('aipg_anthropic_key'))) {
            $config_message = __('Configure a chave API Anthropic nas configurações', 'ai-post-generator');
        } else {
            $config_valid = true;
        }
        break;
    case 'huggingface':
        if (empty(get_option('aipg_huggingface_key'))) {
            $config_message = __('Configure o token Hugging Face nas configurações', 'ai-post-generator');
        } else {
            $config_valid = true;
        }
        break;
    case 'cohere':
        if (empty(get_option('aipg_cohere_key'))) {
            $config_message = __('Configure a chave API Cohere nas configurações', 'ai-post-generator');
        } else {
            $config_valid = true;
        }
        break;
    case 'mistral':
        if (empty(get_option('aipg_mistral_key'))) {
            $config_message = __('Configure a chave API Mistral nas configurações', 'ai-post-generator');
        } else {
            $config_valid = true;
        }
        break;
}
?>

<div class="wrap aipg-wrap">
    <h1>
        <span class="dashicons dashicons-edit-large" style="vertical-align: middle;"></span>
        <?php echo esc_html__('AI Post Generator Pro', 'ai-post-generator'); ?>
    </h1>

    <?php if (!$config_valid): ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php echo esc_html__('⚠️ Aviso:', 'ai-post-generator'); ?></strong>
                <?php echo esc_html($config_message); ?> 
                <a href="<?php echo esc_url(admin_url('admin.php?page=ai-post-generator-settings')); ?>">
                    <?php echo esc_html__('Configurar agora', 'ai-post-generator'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>

    <div class="aipg-container">
        <!-- Seção Principal -->
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px;">
            
            <!-- Formulário -->
            <div class="aipg-form-card">
                <h2><?php echo esc_html__('📝 Gerar Novo Post', 'ai-post-generator'); ?></h2>

                <form id="aipg-generate-form" method="post" action="#">
                    <table class="form-table">
                        <tbody>
                            <!-- Tópico -->
                            <tr>
                                <th scope="row">
                                    <label for="aipg_topic">
                                        <?php echo esc_html__('Tópico/Assunto *', 'ai-post-generator'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="aipg_topic" 
                                        name="topic" 
                                        class="regular-text"
                                        placeholder="<?php echo esc_attr__('Ex: Como otimizar sua presença no Instagram', 'ai-post-generator'); ?>"
                                        required
                                    />
                                    <p class="description">
                                        <?php echo esc_html__('Descreva o tema do post que quer gerar', 'ai-post-generator'); ?>
                                    </p>
                                </td>
                            </tr>

                            <!-- Palavras-chave -->
                            <tr>
                                <th scope="row">
                                    <label for="aipg_keywords">
                                        <?php echo esc_html__('Palavras-chave', 'ai-post-generator'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="aipg_keywords" 
                                        name="keywords" 
                                        class="regular-text"
                                        placeholder="<?php echo esc_attr__('instagram, marketing, redes sociais', 'ai-post-generator'); ?>"
                                    />
                                    <p class="description">
                                        <?php echo esc_html__('Separe por vírgula. Ajuda a direcionar o conteúdo', 'ai-post-generator'); ?>
                                    </p>
                                </td>
                            </tr>

                            <!-- Tom do Post -->
                            <tr>
                                <th scope="row">
                                    <label for="aipg_tone">
                                        <?php echo esc_html__('Tom do Post', 'ai-post-generator'); ?>
                                    </label>
                                </th>
                                <td>
                                    <select id="aipg_tone" name="tone" class="regular-text">
                                        <option value="professional"><?php echo esc_html__('Profissional', 'ai-post-generator'); ?></option>
                                        <option value="casual"><?php echo esc_html__('Casual/Amigável', 'ai-post-generator'); ?></option>
                                        <option value="technical"><?php echo esc_html__('Técnico', 'ai-post-generator'); ?></option>
                                        <option value="educational"><?php echo esc_html__('Educacional', 'ai-post-generator'); ?></option>
                                        <option value="persuasive"><?php echo esc_html__('Persuasivo', 'ai-post-generator'); ?></option>
                                        <option value="funny"><?php echo esc_html__('Humorístico', 'ai-post-generator'); ?></option>
                                    </select>
                                </td>
                            </tr>

                            <!-- Tamanho -->
                            <tr>
                                <th scope="row">
                                    <label for="aipg_length">
                                        <?php echo esc_html__('Tamanho', 'ai-post-generator'); ?>
                                    </label>
                                </th>
                                <td>
                                    <select id="aipg_length" name="length" class="regular-text">
                                        <option value="short"><?php echo esc_html__('Curto (300-500 palavras)', 'ai-post-generator'); ?></option>
                                        <option value="medium" selected><?php echo esc_html__('Médio (500-800 palavras)', 'ai-post-generator'); ?></option>
                                        <option value="long"><?php echo esc_html__('Longo (800-1200 palavras)', 'ai-post-generator'); ?></option>
                                        <option value="verylong"><?php echo esc_html__('Muito Longo (1200-2000 palavras)', 'ai-post-generator'); ?></option>
                                    </select>
                                </td>
                            </tr>

                            <!-- Idioma -->
                            <tr>
                                <th scope="row">
                                    <label for="aipg_language">
                                        <?php echo esc_html__('Idioma', 'ai-post-generator'); ?>
                                    </label>
                                </th>
                                <td>
                                    <select id="aipg_language" name="language" class="regular-text">
                                        <option value="pt-br" selected><?php echo esc_html__('Português Brasileiro', 'ai-post-generator'); ?></option>
                                        <option value="en"><?php echo esc_html__('Inglês', 'ai-post-generator'); ?></option>
                                        <option value="es"><?php echo esc_html__('Espanhol', 'ai-post-generator'); ?></option>
                                    </select>
                                </td>
                            </tr>

                            <!-- Categoria -->
                            <tr>
                                <th scope="row">
                                    <label for="aipg_category">
                                        <?php echo esc_html__('Categoria', 'ai-post-generator'); ?>
                                    </label>
                                </th>
                                <td>
                                    <select id="aipg_category" name="category" class="regular-text">
                                        <option value="0"><?php echo esc_html__('Sem Categoria', 'ai-post-generator'); ?></option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($default_category, $cat->term_id); ?>>
                                                <?php echo esc_html($cat->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <!-- Modelo Groq -->
                            <?php if ($api_provider === 'groq'): ?>
                                <tr>
                                    <th scope="row">
                                        <label for="aipg_groq_model">
                                            <?php echo esc_html__('Modelo Groq', 'ai-post-generator'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select id="aipg_groq_model" name="groq_model" class="regular-text">
                                            <?php foreach ($groq_models as $model_key => $model_label): ?>
                                                <option value="<?php echo esc_attr($model_key); ?>" <?php selected($groq_model, $model_key); ?>>
                                                    <?php echo esc_html($model_label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div id="groq-model-description" class="description"></div>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- Recursos Avançados -->
                            <tr>
                                <th scope="row"><?php echo esc_html__('Recursos Avançados', 'ai-post-generator'); ?></th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <?php echo esc_html__('Recursos Avançados', 'ai-post-generator'); ?>
                                        </legend>
                                        <label>
                                            <input type="checkbox" name="generate_image" id="aipg_generate_image" value="1" />
                                            <?php echo esc_html__('Gerar imagem destacada automaticamente', 'ai-post-generator'); ?>
                                        </label>
                                        <br/>
                                        <label>
                                            <input type="checkbox" name="auto_tags" id="aipg_auto_tags" value="1" />
                                            <?php echo esc_html__('Gerar tags automaticamente', 'ai-post-generator'); ?>
                                        </label>
                                        <br/>
                                        <label>
                                            <input type="checkbox" name="seo_optimization" id="aipg_seo" value="1" />
                                            <?php echo esc_html__('Otimizar para SEO', 'ai-post-generator'); ?>
                                        </label>
                                        <br/>
                                        <label>
                                            <input type="checkbox" name="add_internal_links" id="aipg_links" value="1" />
                                            <?php echo esc_html__('Adicionar links internos', 'ai-post-generator'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>

                            <!-- Agendamento -->
                            <tr>
                                <th scope="row">
                                    <label for="aipg_schedule">
                                        <?php echo esc_html__('Agendamento', 'ai-post-generator'); ?>
                                    </label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="schedule_post" id="aipg_schedule" value="1" />
                                        <?php echo esc_html__('Agendar publicação para data específica', 'ai-post-generator'); ?>
                                    </label>
                                </td>
                            </tr>

                            <!-- Data/Hora do Agendamento -->
                            <tr id="aipg_schedule_options" style="display: none;">
                                <th scope="row">
                                    <label for="aipg_schedule_date">
                                        <?php echo esc_html__('Data e Hora', 'ai-post-generator'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input 
                                        type="datetime-local" 
                                        id="aipg_schedule_date" 
                                        name="schedule_date" 
                                        class="regular-text"
                                    />
                                    <p class="description">
                                        <?php echo esc_html__('O post será gerado e publicado automaticamente nesta data/hora', 'ai-post-generator'); ?>
                                    </p>
                                </td>
                            </tr>

                            <!-- Template -->
                            <?php if (!empty($templates)): ?>
                                <tr>
                                    <th scope="row">
                                        <label for="aipg_template">
                                            <?php echo esc_html__('Template Salvo', 'ai-post-generator'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select id="aipg_template" name="template" class="regular-text">
                                            <option value=""><?php echo esc_html__('-- Selecione um template --', 'ai-post-generator'); ?></option>
                                            <?php foreach ($templates as $tpl_id => $template): ?>
                                                <option value="<?php echo esc_attr($tpl_id); ?>">
                                                    <?php echo esc_html($template['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html__('Carregue configurações salvas anteriormente', 'ai-post-generator'); ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php wp_nonce_field('aipg_generate_post', 'aipg_nonce'); ?>

                    <p class="submit">
                        <button type="submit" class="button button-primary button-large" id="aipg-submit-btn">
                            <?php echo esc_html__('Gerar Post', 'ai-post-generator'); ?>
                        </button>
                        <button type="button" class="button button-secondary" id="aipg-save-template">
                            <?php echo esc_html__('Salvar como Template', 'ai-post-generator'); ?>
                        </button>
                    </p>
                </form>

                <!-- Resultado da Geração -->
                <div id="aipg-result" style="display: none;">
                    <h3><?php echo esc_html__('Resultado:', 'ai-post-generator'); ?></h3>
                    <div id="aipg-result-content"></div>
                </div>
            </div>

            <!-- Sidebar -->
            <aside>
                <!-- Card: Provedor -->
                <div class="aipg-form-card">
                    <h3><?php echo esc_html__('Provedor Atual', 'ai-post-generator'); ?></h3>
                    <p style="font-weight: 600; color: #2271b1; margin: 10px 0;">
                        <?php 
                        $provider_names = array(
                            'groq' => 'Groq (Llama)',
                            'openai' => 'OpenAI (GPT)',
                            'anthropic' => 'Anthropic (Claude)',
                            'huggingface' => 'Hugging Face',
                            'cohere' => 'Cohere',
                            'mistral' => 'Mistral AI'
                        );
                        echo esc_html($provider_names[$api_provider] ?? $api_provider);
                        ?>
                    </p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=ai-post-generator-settings')); ?>" class="button button-small">
                        <?php echo esc_html__('Alterar Provedor', 'ai-post-generator'); ?>
                    </a>
                </div>

                <!-- Card: Dicas -->
                <div class="aipg-form-card">
                    <h3><?php echo esc_html__('💡 Dicas Rápidas', 'ai-post-generator'); ?></h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><?php echo esc_html__('Tópicos específicos = conteúdo melhor', 'ai-post-generator'); ?></li>
                        <li><?php echo esc_html__('Use templates para posts recorrentes', 'ai-post-generator'); ?></li>
                        <li><?php echo esc_html__('SEO + tags + imagem = otimizado', 'ai-post-generator'); ?></li>
                        <li><?php echo esc_html__('Ctrl+Enter para gerar rápido', 'ai-post-generator'); ?></li>
                    </ul>
                </div>

                <!-- Card: Links Úteis -->
                <div class="aipg-form-card">
                    <h3><?php echo esc_html__('Links Úteis', 'ai-post-generator'); ?></h3>
                    <ul style="margin: 0; padding-left: 20px; list-style: none;">
                        <li>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=ai-post-generator-settings')); ?>">
                                ⚙️ <?php echo esc_html__('Configurações', 'ai-post-generator'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=ai-post-generator-images')); ?>">
                                🖼️ <?php echo esc_html__('Gerenciar Imagens', 'ai-post-generator'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(admin_url('edit.php')); ?>">
                                📝 <?php echo esc_html__('Todos os Posts', 'ai-post-generator'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>