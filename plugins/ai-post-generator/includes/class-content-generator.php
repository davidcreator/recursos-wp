<?php
/**
 * Classe de Geração de Conteúdo com IA
 * 
 * Responsável por gerar texto usando diferentes provedores de IA
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIPG_Content_Generator {
    
    /**
     * Mapeamento de tamanhos
     */
    private $length_words = array(
        'short' => '300-500',
        'medium' => '500-800',
        'long' => '800-1200',
        'verylong' => '1200-2000'
    );
    
    /**
     * Mapeamento de idiomas
     */
    private $language_map = array(
        'pt-br' => 'português brasileiro',
        'en' => 'inglês',
        'es' => 'espanhol'
    );
    
    /**
     * Gera conteúdo baseado nos dados fornecidos
     * 
     * @param array $data Dados de configuração
     * @return array|WP_Error Resultado ou erro
     */
    public function generate($data) {
        $provider = get_option('aipg_api_provider', 'groq');
        $prompt = $this->build_prompt($data);
        
        switch ($provider) {
            case 'groq':
                return $this->generate_with_groq($prompt);
            case 'huggingface':
                return $this->generate_with_huggingface($prompt);
            case 'cohere':
                return $this->generate_with_cohere($prompt);
            case 'mistral':
                return $this->generate_with_mistral($prompt);
            case 'openai':
                return $this->generate_with_openai($prompt);
            case 'anthropic':
                return $this->generate_with_anthropic($prompt);
            default:
                return new WP_Error('no_provider', __('Provedor não configurado', 'ai-post-generator'));
        }
    }
    
    /**
     * Constrói o prompt para a IA
     */
    private function build_prompt($data) {
        $prompt = sprintf(
            "Crie um post de blog em %s sobre '%s'.\n\n",
            $this->language_map[$data['language']],
            $data['topic']
        );
        
        if (!empty($data['keywords'])) {
            $prompt .= "Palavras-chave: " . $data['keywords'] . "\n";
        }
        
        $prompt .= sprintf(
            "Tom: %s\nTamanho: %s palavras\n\n",
            $data['tone'],
            $this->length_words[$data['length']]
        );
        
        if ($data['seo_optimization']) {
            $prompt .= "Inclua:\n- Título SEO otimizado (máx 60 caracteres)\n- Meta description (máx 160 caracteres)\n\n";
        }
        
        if ($data['auto_tags']) {
            $prompt .= "Sugira 5-8 tags relevantes.\n\n";
        }
        
        $prompt .= "Retorne em formato JSON:\n{\n  \"title\": \"título do post\",\n  \"content\": \"conteúdo HTML\",\n  \"tags\": [\"tag1\", \"tag2\"],\n  \"seo\": {\"title\": \"título seo\", \"description\": \"meta description\"}\n}";
        
        return $prompt;
    }
    
    /**
     * Gera com Groq (Llama)
     */
    private function generate_with_groq($prompt) {
        $api_key = get_option('aipg_groq_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Groq não configurada', 'ai-post-generator'));
        }
        
        $model = get_option('aipg_groq_model', 'llama-3.3-70b-versatile');
        
        $model_configs = array(
            'llama-3.3-70b-versatile' => array('max_tokens' => 8000, 'temperature' => 0.7),
            'llama-3.1-70b-versatile' => array('max_tokens' => 8000, 'temperature' => 0.7),
            'meta-llama/llama-4-scout-17b-16e-instruct' => array('max_tokens' => 4096, 'temperature' => 0.6),
            'mixtral-8x7b-32768' => array('max_tokens' => 16000, 'temperature' => 0.7),
        );
        
        $config = isset($model_configs[$model]) ? $model_configs[$model] : $model_configs['llama-3.3-70b-versatile'];
        
        $response = wp_remote_post('https://api.groq.com/openai/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'system', 'content' => 'Você é um especialista em criar conteúdo de blog. Sempre retorne JSON válido.'),
                    array('role' => 'user', 'content' => $prompt)
                ),
                'temperature' => $config['temperature'],
                'max_tokens' => $config['max_tokens'],
                'response_format' => array('type' => 'json_object')
            )),
            'timeout' => 90
        ));
        
        return $this->parse_response($response);
    }
    
    /**
     * Gera com OpenAI (GPT)
     */
    private function generate_with_openai($prompt) {
        $api_key = get_option('aipg_openai_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API OpenAI não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'gpt-4o-mini',
                'messages' => array(
                    array('role' => 'system', 'content' => 'Você é um especialista em criar conteúdo de blog otimizado. Sempre retorne JSON válido.'),
                    array('role' => 'user', 'content' => $prompt)
                ),
                'temperature' => 0.7,
                'response_format' => array('type' => 'json_object')
            )),
            'timeout' => 90
        ));
        
        return $this->parse_response($response);
    }
    
    /**
     * Gera com Anthropic (Claude)
     */
    private function generate_with_anthropic($prompt) {
        $api_key = get_option('aipg_anthropic_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Anthropic não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
            'headers' => array(
                'x-api-key' => $api_key,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 4096,
                'messages' => array(
                    array('role' => 'user', 'content' => $prompt)
                )
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message']);
        }
        
        $content_text = $body['content'][0]['text'];
        $content = json_decode($content_text, true);
        
        return $content;
    }
    
    /**
     * Gera com Hugging Face
     */
    private function generate_with_huggingface($prompt) {
        $api_key = get_option('aipg_huggingface_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Token Hugging Face não configurado', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api-inference.huggingface.co/models/mistralai/Mixtral-8x7B-Instruct-v0.1', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'inputs' => $prompt,
                'parameters' => array(
                    'max_new_tokens' => 2000,
                    'temperature' => 0.7,
                    'return_full_text' => false
                )
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']);
        }
        
        $generated_text = $body[0]['generated_text'];
        preg_match('/\{.*\}/s', $generated_text, $matches);
        
        if (empty($matches)) {
            return array(
                'title' => 'Post Gerado',
                'content' => $generated_text,
                'tags' => array(),
                'seo' => array('title' => '', 'description' => '')
            );
        }
        
        $content = json_decode($matches[0], true);
        return $content;
    }
    
    /**
     * Gera com Cohere
     */
    private function generate_with_cohere($prompt) {
        $api_key = get_option('aipg_cohere_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Cohere não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api.cohere.ai/v1/generate', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'command-r-plus',
                'prompt' => $prompt,
                'max_tokens' => 2000,
                'temperature' => 0.7
            )),
            'timeout' => 90
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['message'])) {
            return new WP_Error('api_error', $body['message']);
        }
        
        $generated_text = $body['generations'][0]['text'];
        preg_match('/\{.*\}/s', $generated_text, $matches);
        
        if (empty($matches)) {
            return array(
                'title' => 'Post Gerado',
                'content' => $generated_text,
                'tags' => array(),
                'seo' => array('title' => '', 'description' => '')
            );
        }
        
        $content = json_decode($matches[0], true);
        return $content;
    }
    
    /**
     * Gera com Mistral AI
     */
    private function generate_with_mistral($prompt) {
        $api_key = get_option('aipg_mistral_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Mistral não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_post('https://api.mistral.ai/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'mistral-small-latest',
                'messages' => array(
                    array('role' => 'system', 'content' => 'Você é um especialista em criar conteúdo. Retorne JSON válido.'),
                    array('role' => 'user', 'content' => $prompt)
                ),
                'temperature' => 0.7,
                'response_format' => array('type' => 'json_object')
            )),
            'timeout' => 90
        ));
        
        return $this->parse_response($response);
    }
    
    /**
     * Parse genérico de resposta (para APIs compatíveis com OpenAI)
     */
    private function parse_response($response) {
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message'] ?? 'Erro desconhecido');
        }
        
        if (empty($body['choices'][0]['message']['content'])) {
            return new WP_Error('no_content', __('Nenhum conteúdo gerado', 'ai-post-generator'));
        }
        
        $content = json_decode($body['choices'][0]['message']['content'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_json', __('Resposta inválida da IA', 'ai-post-generator'));
        }
        
        return $content;
    }
}