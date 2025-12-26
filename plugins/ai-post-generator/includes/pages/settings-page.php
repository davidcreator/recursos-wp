<?php
/**
 * Página de Configurações - API e Preferências
 * 
 * Gerencia configurações de provedores de IA, chaves de API e preferências gerais.
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
$image_provider = get_option('aipg_image_provider', 'pollinations');
$image_width = get_option('aipg_image_width', 1920);
$image_height = get_option('aipg_image_height', 1080);
$post_status = get_option('aipg_post_status', 'draft');
$groq_model = get_option('aipg_groq_model', 'llama-3.3-70b-versatile');

// Descrições dos provedores de IA
$ai_providers = array(
    'groq' => array(
        'label' => __('Groq (Llama) - Recomendado', 'ai-post-generator'),
        'description' => __('100% GRATUITO, Ultra-rápido (600+ tokens/seg), Limite: 14.400 req/dia', 'ai-post-generator'),
        'free' => true
    ),
    'huggingface' => array(
        'label' => __('Hugging Face', 'ai-post-generator'),
        'description' => __('100% GRATUITO, Ilimitado, Modelos Open Source', 'ai-post-generator'),
        'free' => true
    ),
    'cohere' => array(
        'label' => __('Cohere', 'ai-post-generator'),
        'description' => __('Plano Grátis: 1000 requisições/mês, Qualidade Alta', 'ai-post-generator'),
        'free' => true
    ),
    'mistral' => array(
        'label' => __('Mistral AI', 'ai-post-generator'),
        'description' => __('5€ créditos grátis, Alta qualidade, Rápido', 'ai-post-generator'),
        'free' => true
    ),
    'openai' => array(
        'label' => __('OpenAI (GPT-4o-mini)', 'ai-post-generator'),
        'description' => __('Melhor qualidade, Pago (~$0.002/post), Sem limite', 'ai-post-generator'),
        'free' => false
    ),
    'anthropic' => array(
        'label' => __('Anthropic (Claude)', 'ai-post-generator'),
        'description' => __('Excelente qualidade, Pago (~$0.015/post), Textos longos', 'ai-post-generator'),
        'free' => false
    )
);

// Provedores de imagem
$image_providers = array(
    'pollinations' => array(
        'label' => __('Pollinations AI - Recomendado', 'ai-post-generator'),
        'description' => __('100% GRATUITO, IA gerativa, Ilimitado', 'ai-post-generator'),
        'needs_key' => false,
        'free' => true
    ),
    'unsplash' => array(
        'label' => __('Unsplash', 'ai-post-generator'),
        'description' => __('Fotos profissionais grátis, 50 req/hora', 'ai-post-generator'),
        'needs_key' => true,
        'free' => true
    ),
    'pexels' => array(
        'label' => __('Pexels', 'ai-post-generator'),
        'description' => __('Biblioteca grande, 200 req/hora', 'ai-post-generator'),
        'needs_key' => true,
        'free' => true
    ),
    'pixabay' => array(
        'label' => __('Pixabay', 'ai-post-generator'),
        'description' => __('Sem limite prático, Ideal para alto volume', 'ai-post-generator'),
        'needs_key' => true,
        'free' => true
    ),
    'dall-e' => array(
        'label' => __('DALL-E 3', 'ai-post-generator'),
        'description' => __('IA gerativa, $0.04/imagem, Alta qualidade', 'ai-post-generator'),
        'needs_key' => true,
        'free' => false
    ),
    'stability' => array(
        'label' => __('Stability AI', 'ai-post-generator'),
        'description' => __('Stable Diffusion, 25 créditos grátis, Qualidade excelente', 'ai-post-generator'),
        'needs_key' => true,
        'free' => true
    )
);

// Status de posts
$post_statuses = array(
    'draft' => __('Rascunho', 'ai-post-generator'),
    'pending' => __('Pendente de Revisão', 'ai-post-generator'),
    'publish' => __('Publicado', 'ai-post-generator')
);

// Modelos Groq
$groq_models = array(
    'llama-3.3-70b-versatile' => __('Llama 3.3 70B (Recomendado)', 'ai-post-generator'),
    'llama-3.1-70b-versatile' => __('Llama 3.1 70B (Anterior)', 'ai-post-generator'),
    'meta-llama/llama-4-scout-17b-16e-instruct' => __('Llama 4 Scout 17B (Experimental)', 'ai-post-generator'),
    'mixtral-8x7b-32768' => __('Mixtral 8x7B (Contexto Longo)', 'ai-post-generator')
);

// Obter chaves de API (sem mostrar completas)
$groq_key = get_option('aipg_groq_key');
$openai_key = get_option('aipg_openai_key');
$anthropic_key = get_option('aipg_anthropic_key');
$huggingface_key = get_option('aipg_huggingface_key');
$cohere_key = get_option('aipg_cohere_key');
$mistral_key = get_option('aipg_mistral_key');
$unsplash_key = get_option('aipg_unsplash_key');
$pexels_key = get_option('aipg_pexels_key');
$pixabay_key = get_option('aipg_pixabay_key');
$stability_key = get_option('aipg_stability_key');
?>

<div class="wrap aipg-wrap">
    <h1>
        <span class="dashicons dashicons-admin-generic" style="vertical-align: middle;"></span>
        <?php echo esc_html__('Configurações - AI Post Generator', 'ai-post-generator'); ?>
    </h1>

    <div class="notice notice-info is-dismissible">
        <p>
            <strong><?php echo esc_html__('💡 Dica:', 'ai-post-generator'); ?></strong>
            <?php echo esc_html__('Comece com Groq (100% grátis) ou use Hugging Face para testar antes de usar APIs pagas.', 'ai-post-generator'); ?>
        </p>
    </div>

    <form method="post" action="options.php" class="aipg-settings-form">
        <?php settings_fields('aipg_settings'); ?>

        <!-- SEÇÃO: Provedores de IA -->
        <h2><?php echo esc_html__('🤖 Provedores de IA - Geração de Conteúdo', 'ai-post-generator'); ?></h2>
        <p class="description">
            <?php echo esc_html__('Escolha qual provedor de IA usar para gerar o conteúdo dos posts.', 'ai-post-generator'); ?>
        </p>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="aipg_api_provider">
                            <?php echo esc_html__('Provedor Padrão *', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="aipg_api_provider" name="aipg_api_provider" class="aipg-provider-select" required>
                            <option value=""><?php echo esc_html__('-- Selecione um provedor --', 'ai-post-generator'); ?></option>
                            <?php foreach ($ai_providers as $key => $provider): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($api_provider, $key); ?>>
                                    <?php echo esc_html($provider['label']); ?>
                                    <?php if ($provider['free']): ?>
                                        <span style="color: #46b450;">✓ <?php echo esc_html__('Grátis', 'ai-post-generator'); ?></span>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="provider-description" style="display: none;"></div>
                        <p class="description">
                            <?php echo esc_html__('Altere o provedor aqui e configure a chave de API abaixo.', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- API Keys para cada provedor -->
        <table class="form-table">
            <tbody>
                <!-- Groq -->
                <tr class="api-key-row" data-provider="groq" style="display: none;">
                    <th scope="row">
                        <label for="aipg_groq_key">
                            <?php echo esc_html__('Chave API Groq', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_groq_key" 
                            name="aipg_groq_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($groq_key); ?>"
                            placeholder="<?php echo esc_attr__('gsk_...', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <?php echo esc_html__('Obtenha em:', 'ai-post-generator'); ?>
                            <a href="https://console.groq.com" target="_blank" rel="noopener">https://console.groq.com</a><br>
                            ✅ <?php echo esc_html__('100% Grátis | Limite: 14.400 req/dia', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- Modelo Groq -->
                <tr class="api-key-row" data-provider="groq" style="display: none;">
                    <th scope="row">
                        <label for="aipg_groq_model">
                            <?php echo esc_html__('Modelo Groq', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="aipg_groq_model" name="aipg_groq_model" class="regular-text">
                            <?php foreach ($groq_models as $model_key => $model_label): ?>
                                <option value="<?php echo esc_attr($model_key); ?>" <?php selected($groq_model, $model_key); ?>>
                                    <?php echo esc_html($model_label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="groq-model-description" class="description"></div>
                    </td>
                </tr>

                <!-- OpenAI -->
                <tr class="api-key-row" data-provider="openai" style="display: none;">
                    <th scope="row">
                        <label for="aipg_openai_key">
                            <?php echo esc_html__('Chave API OpenAI', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_openai_key" 
                            name="aipg_openai_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($openai_key); ?>"
                            placeholder="<?php echo esc_attr__('sk-...', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <?php echo esc_html__('Obtenha em:', 'ai-post-generator'); ?>
                            <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener">https://platform.openai.com/api-keys</a><br>
                            💳 <?php echo esc_html__('Pago | Modelo: GPT-4o-mini (~$0.002/post)', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- Anthropic -->
                <tr class="api-key-row" data-provider="anthropic" style="display: none;">
                    <th scope="row">
                        <label for="aipg_anthropic_key">
                            <?php echo esc_html__('Chave API Anthropic', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_anthropic_key" 
                            name="aipg_anthropic_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($anthropic_key); ?>"
                            placeholder="<?php echo esc_attr__('sk-ant-...', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <?php echo esc_html__('Obtenha em:', 'ai-post-generator'); ?>
                            <a href="https://console.anthropic.com" target="_blank" rel="noopener">https://console.anthropic.com</a><br>
                            💳 <?php echo esc_html__('Pago | Modelo: Claude 3.5 Sonnet (~$0.015/post)', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- Hugging Face -->
                <tr class="api-key-row" data-provider="huggingface" style="display: none;">
                    <th scope="row">
                        <label for="aipg_huggingface_key">
                            <?php echo esc_html__('Token Hugging Face', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_huggingface_key" 
                            name="aipg_huggingface_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($huggingface_key); ?>"
                            placeholder="<?php echo esc_attr__('hf_...', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <?php echo esc_html__('Obtenha em:', 'ai-post-generator'); ?>
                            <a href="https://huggingface.co/settings/tokens" target="_blank" rel="noopener">https://huggingface.co/settings/tokens</a><br>
                            ✅ <?php echo esc_html__('100% Grátis | Sem limite prático', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- Cohere -->
                <tr class="api-key-row" data-provider="cohere" style="display: none;">
                    <th scope="row">
                        <label for="aipg_cohere_key">
                            <?php echo esc_html__('Chave API Cohere', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_cohere_key" 
                            name="aipg_cohere_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($cohere_key); ?>"
                            placeholder="<?php echo esc_attr__('...', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <?php echo esc_html__('Obtenha em:', 'ai-post-generator'); ?>
                            <a href="https://dashboard.cohere.ai" target="_blank" rel="noopener">https://dashboard.cohere.ai</a><br>
                            ✅ <?php echo esc_html__('Grátis: 1000 req/mês | Qualidade: Alta', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- Mistral -->
                <tr class="api-key-row" data-provider="mistral" style="display: none;">
                    <th scope="row">
                        <label for="aipg_mistral_key">
                            <?php echo esc_html__('Chave API Mistral', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_mistral_key" 
                            name="aipg_mistral_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($mistral_key); ?>"
                            placeholder="<?php echo esc_attr__('...', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <?php echo esc_html__('Obtenha em:', 'ai-post-generator'); ?>
                            <a href="https://console.mistral.ai" target="_blank" rel="noopener">https://console.mistral.ai</a><br>
                            ✅ <?php echo esc_html__('5€ créditos grátis | Qualidade: Excelente', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- SEÇÃO: Provedores de Imagem -->
        <h2><?php echo esc_html__('🖼️ Provedores de Imagem', 'ai-post-generator'); ?></h2>
        <p class="description">
            <?php echo esc_html__('Escolha como gerar ou buscar imagens destacadas para seus posts.', 'ai-post-generator'); ?>
        </p>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="aipg_image_provider">
                            <?php echo esc_html__('Provedor Padrão', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="aipg_image_provider" name="aipg_image_provider" class="regular-text">
                            <?php foreach ($image_providers as $key => $provider): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($image_provider, $key); ?>>
                                    <?php echo esc_html($provider['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="image-provider-description" style="display: none;"></div>
                    </td>
                </tr>

                <!-- Dimensões de Imagem -->
                <tr>
                    <th scope="row">
                        <?php echo esc_html__('Dimensões Padrão', 'ai-post-generator'); ?>
                    </th>
                    <td>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label for="aipg_image_width">
                                    <?php echo esc_html__('Largura (px)', 'ai-post-generator'); ?>
                                </label>
                                <input 
                                    type="number" 
                                    id="aipg_image_width" 
                                    name="aipg_image_width" 
                                    value="<?php echo esc_attr($image_width); ?>"
                                    min="100"
                                    max="4000"
                                    class="regular-text"
                                />
                            </div>
                            <div>
                                <label for="aipg_image_height">
                                    <?php echo esc_html__('Altura (px)', 'ai-post-generator'); ?>
                                </label>
                                <input 
                                    type="number" 
                                    id="aipg_image_height" 
                                    name="aipg_image_height" 
                                    value="<?php echo esc_attr($image_height); ?>"
                                    min="100"
                                    max="4000"
                                    class="regular-text"
                                />
                            </div>
                        </div>

                        <!-- Presets -->
                        <div style="margin-top: 15px;">
                            <button type="button" class="button aipg-preset-size" data-width="1920" data-height="1080">
                                <?php echo esc_html__('16:9 (Blog)', 'ai-post-generator'); ?>
                            </button>
                            <button type="button" class="button aipg-preset-size" data-width="1200" data-height="628">
                                <?php echo esc_html__('Facebook', 'ai-post-generator'); ?>
                            </button>
                            <button type="button" class="button aipg-preset-size" data-width="1080" data-height="1080">
                                <?php echo esc_html__('Instagram', 'ai-post-generator'); ?>
                            </button>
                            <button type="button" class="button aipg-preset-size" data-width="1024" data-height="1024">
                                <?php echo esc_html__('Quadrado', 'ai-post-generator'); ?>
                            </button>
                        </div>

                        <!-- Preview -->
                        <div id="aipg-image-preview" style="margin-top: 15px; display: none; background: #f5f5f5; padding: 15px; border-radius: 4px;">
                            <span id="aipg-preview-size"></span><br>
                            <span id="aipg-preview-ratio"></span><br>
                            <span id="aipg-preview-use"></span>
                        </div>
                    </td>
                </tr>

                <!-- Unsplash -->
                <tr class="image-api-key-row" data-provider="unsplash" style="display: none;">
                    <th scope="row">
                        <label for="aipg_unsplash_key">
                            <?php echo esc_html__('Chave API Unsplash', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_unsplash_key" 
                            name="aipg_unsplash_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($unsplash_key); ?>"
                            placeholder="<?php echo esc_attr__('Access Key', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <a href="https://unsplash.com/oauth/applications" target="_blank" rel="noopener">
                                <?php echo esc_html__('Obtenha aqui', 'ai-post-generator'); ?>
                            </a> - 
                            ✅ <?php echo esc_html__('Grátis | 50 req/hora', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- Pexels -->
                <tr class="image-api-key-row" data-provider="pexels" style="display: none;">
                    <th scope="row">
                        <label for="aipg_pexels_key">
                            <?php echo esc_html__('Chave API Pexels', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_pexels_key" 
                            name="aipg_pexels_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($pexels_key); ?>"
                            placeholder="<?php echo esc_attr__('API Key', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <a href="https://www.pexels.com/api/" target="_blank" rel="noopener">
                                <?php echo esc_html__('Obtenha aqui', 'ai-post-generator'); ?>
                            </a> - 
                            ✅ <?php echo esc_html__('Grátis | 200 req/hora', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- Pixabay -->
                <tr class="image-api-key-row" data-provider="pixabay" style="display: none;">
                    <th scope="row">
                        <label for="aipg_pixabay_key">
                            <?php echo esc_html__('Chave API Pixabay', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_pixabay_key" 
                            name="aipg_pixabay_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($pixabay_key); ?>"
                            placeholder="<?php echo esc_attr__('API Key', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <a href="https://pixabay.com/api/" target="_blank" rel="noopener">
                                <?php echo esc_html__('Obtenha aqui', 'ai-post-generator'); ?>
                            </a> - 
                            ✅ <?php echo esc_html__('Grátis | Sem limite prático', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- DALL-E -->
                <tr class="image-api-key-row" data-provider="dall-e" style="display: none;">
                    <th scope="row">
                        <label for="aipg_openai_key_dalle">
                            <?php echo esc_html__('Chave API OpenAI (DALL-E)', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <p class="description">
                            <?php echo esc_html__('Usa a mesma chave OpenAI configurada acima. Custo: $0.04/imagem', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <!-- Stability AI -->
                <tr class="image-api-key-row" data-provider="stability" style="display: none;">
                    <th scope="row">
                        <label for="aipg_stability_key">
                            <?php echo esc_html__('Chave API Stability AI', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <input 
                            type="password" 
                            id="aipg_stability_key" 
                            name="aipg_stability_key" 
                            class="regular-text"
                            value="<?php echo esc_attr($stability_key); ?>"
                            placeholder="<?php echo esc_attr__('sk-...', 'ai-post-generator'); ?>"
                        />
                        <p class="description">
                            <a href="https://platform.stability.ai/account/keys" target="_blank" rel="noopener">
                                <?php echo esc_html__('Obtenha aqui', 'ai-post-generator'); ?>
                            </a> - 
                            ✅ <?php echo esc_html__('25 créditos grátis | Qualidade: Excelente', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- SEÇÃO: Preferências Gerais -->
        <h2><?php echo esc_html__('⚙️ Preferências Gerais', 'ai-post-generator'); ?></h2>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="aipg_post_status">
                            <?php echo esc_html__('Status Padrão de Posts', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="aipg_post_status" name="aipg_post_status" class="regular-text">
                            <?php foreach ($post_statuses as $status_key => $status_label): ?>
                                <option value="<?php echo esc_attr($status_key); ?>" <?php selected($post_status, $status_key); ?>>
                                    <?php echo esc_html($status_label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php echo esc_html__('Posts gerados usarão este status. Recomenda-se Rascunho para revisão.', 'ai-post-generator'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="aipg_default_category">
                            <?php echo esc_html__('Categoria Padrão', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="aipg_default_category" name="aipg_default_category" class="regular-text">
                            <option value="0"><?php echo esc_html__('Nenhuma', 'ai-post-generator'); ?></option>
                            <?php 
                            $categories = get_categories(array('hide_empty' => false));
                            $default_category = get_option('aipg_default_category', 0);
                            foreach ($categories as $cat): 
                            ?>
                                <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($default_category, $cat->term_id); ?>>
                                    <?php echo esc_html($cat->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="aipg_default_author">
                            <?php echo esc_html__('Autor Padrão', 'ai-post-generator'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="aipg_default_author" name="aipg_default_author" class="regular-text">
                            <?php 
                            $default_author = get_option('aipg_default_author', get_current_user_id());
                            $users = get_users(array('who' => 'authors'));
                            foreach ($users as $user): 
                            ?>
                                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($default_author, $user->ID); ?>>
                                    <?php echo esc_html($user->display_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- SEÇÃO: Recursos Padrão -->
        <h2><?php echo esc_html__('📋 Habilitar Recursos por Padrão', 'ai-post-generator'); ?></h2>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php echo esc_html__('Recursos', 'ai-post-generator'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <?php echo esc_html__('Recursos Padrão', 'ai-post-generator'); ?>
                            </legend>
                            <label>
                                <input 
                                    type="checkbox" 
                                    name="aipg_auto_tags" 
                                    value="1" 
                                    <?php checked(get_option('aipg_auto_tags'), '1'); ?>
                                />
                                <?php echo esc_html__('Gerar tags automaticamente', 'ai-post-generator'); ?>
                            </label>
                            <br/>
                            <label>
                                <input 
                                    type="checkbox" 
                                    name="aipg_seo_optimization" 
                                    value="1" 
                                    <?php checked(get_option('aipg_seo_optimization'), '1'); ?>
                                />
                                <?php echo esc_html__('Otimizar para SEO', 'ai-post-generator'); ?>
                            </label>
                            <br/>
                            <label>
                                <input 
                                    type="checkbox" 
                                    name="aipg_auto_featured_image" 
                                    value="1" 
                                    <?php checked(get_option('aipg_auto_featured_image'), '1'); ?>
                                />
                                <?php echo esc_html__('Gerar imagem destacada', 'ai-post-generator'); ?>
                            </label>
                            <br/>
                            <label>
                                <input 
                                    type="checkbox" 
                                    name="aipg_add_internal_links" 
                                    value="1" 
                                    <?php checked(get_option('aipg_add_internal_links'), '1'); ?>
                                />
                                <?php echo esc_html__('Adicionar links internos', 'ai-post-generator'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Informações de Ajuda -->
        <div class="notice notice-info" style="margin-top: 30px;">
            <p>
                <strong><?php echo esc_html__('ℹ️ Informações:', 'ai-post-generator'); ?></strong><br>
                <?php echo esc_html__('• Todas as chaves de API são armazenadas de forma segura no WordPress', 'ai-post-generator'); ?><br>
                <?php echo esc_html__('• Recomenda-se usar Groq para começar (100% grátis)', 'ai-post-generator'); ?><br>
                <?php echo esc_html__('• Teste provedores antes de escolher o padrão', 'ai-post-generator'); ?><br>
                <?php echo esc_html__('• Imagens podem ser salvas automaticamente ou selecionadas manualmente', 'ai-post-generator'); ?>
            </p>
        </div>

        <!-- Botão Salvar -->
        <?php submit_button(__('Salvar Configurações', 'ai-post-generator'), 'primary', 'submit', true); ?>
    </form>
</div>