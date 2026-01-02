<?php
/**
 * Classe de Geração de Imagens
 * 
 * Responsável por gerar imagens usando diferentes provedores
 * 
 * @package AI_Post_Generator
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIPG_Image_Generator {
    
    /**
     * Gera imagem destacada
     * 
     * @param string $topic Tópico/descrição da imagem
     * @param int $post_id ID do post (opcional)
     * @return int|WP_Error ID da imagem ou erro
     */
    public function generate($topic, $post_id = 0) {
        $provider = get_option('aipg_image_provider', 'pollinations');
        
        switch ($provider) {
            case 'unsplash':
                return $this->generate_unsplash($topic, $post_id);
            case 'pexels':
                return $this->generate_pexels($topic, $post_id);
            case 'pixabay':
                return $this->generate_pixabay($topic, $post_id);
            case 'pollinations':
                return $this->generate_pollinations($topic, $post_id);
            case 'dall-e':
                return $this->generate_dalle($topic, $post_id);
            case 'stability':
                return $this->generate_stability($topic, $post_id);
            default:
                return new WP_Error('no_provider', __('Provedor de imagem não configurado', 'ai-post-generator'));
        }
    }
    
    /**
     * Gera imagem usando provedor específico (ignora configuração salva)
     */
    private function generate_with_provider($provider, $topic, $post_id = 0) {
        switch ($provider) {
            case 'unsplash':
                return $this->generate_unsplash($topic, $post_id);
            case 'pexels':
                return $this->generate_pexels($topic, $post_id);
            case 'pixabay':
                return $this->generate_pixabay($topic, $post_id);
            case 'pollinations':
                return $this->generate_pollinations($topic, $post_id);
            case 'dall-e':
                return $this->generate_dalle($topic, $post_id);
            case 'stability':
                return $this->generate_stability($topic, $post_id);
            default:
                return new WP_Error('no_provider', __('Provedor de imagem inválido para teste', 'ai-post-generator'));
        }
    }
    
    /**
     * AJAX: Gera imagem
     */
    public function ajax_generate_image() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        if (!current_user_can('upload_files')) {
            wp_send_json_error(array('message' => __('Permissão negada', 'ai-post-generator')));
        }
        
        $topic = sanitize_text_field($_POST['topic']);
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        if (empty($topic)) {
            wp_send_json_error(array('message' => __('Tópico não fornecido', 'ai-post-generator')));
        }
        
        $image_id = $this->generate($topic, $post_id);
        
        if (is_wp_error($image_id)) {
            wp_send_json_error(array('message' => $image_id->get_error_message()));
        }
        
        if (!$image_id) {
            wp_send_json_error(array('message' => __('Falha ao gerar imagem', 'ai-post-generator')));
        }
        
        wp_send_json_success(array(
            'message' => __('Imagem gerada com sucesso!', 'ai-post-generator'),
            'image_id' => $image_id,
            'image_url' => wp_get_attachment_url($image_id),
            'image_thumb' => wp_get_attachment_image_url($image_id, 'thumbnail')
        ));
    }
    
    /**
     * AJAX: Busca imagens recentes
     */
    public function ajax_get_recent_images() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 12;
        
        $args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => $limit,
            'meta_query' => array(
                array(
                    'key' => '_aipg_generated_image',
                    'value' => '1'
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $query = new WP_Query($args);
        $images = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $id = get_the_ID();
                
                $images[] = array(
                    'id' => $id,
                    'title' => get_the_title(),
                    'thumb' => wp_get_attachment_image_url($id, 'medium'),
                    'url' => wp_get_attachment_url($id),
                    'provider' => get_post_meta($id, '_aipg_image_provider', true),
                    'date' => get_the_date('d/m/Y'),
                    'edit_url' => get_edit_post_link($id, 'raw')
                );
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success(array('images' => $images));
    }
    
    /**
     * AJAX: Estatísticas de imagens
     */
    public function ajax_get_image_stats() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        global $wpdb;
        
        $total = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'attachment'
            AND pm.meta_key = '_aipg_generated_image'
            AND pm.meta_value = '1'
        ");
        
        $this_month = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'attachment'
            AND pm.meta_key = '_aipg_generated_image'
            AND pm.meta_value = '1'
            AND MONTH(p.post_date) = %d
            AND YEAR(p.post_date) = %d
        ", date('n'), date('Y')));
        
        $provider_stats = $wpdb->get_results("
            SELECT pm2.meta_value as provider, COUNT(*) as count
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->postmeta} pm2 ON pm.post_id = pm2.post_id
            WHERE pm.meta_key = '_aipg_generated_image'
            AND pm.meta_value = '1'
            AND pm2.meta_key = '_aipg_image_provider'
            GROUP BY pm2.meta_value
            ORDER BY count DESC
            LIMIT 1
        ");
        
        $provider_most_used = !empty($provider_stats) ? ucfirst($provider_stats[0]->provider) : 'N/A';
        
        wp_send_json_success(array(
            'total' => number_format_i18n($total),
            'this_month' => number_format_i18n($this_month),
            'provider_most_used' => $provider_most_used,
            'avg_size' => '1920×1080'
        ));
    }
    
    /**
     * AJAX: Testa provedor
     */
    public function ajax_test_image_provider() {
        check_ajax_referer('aipg_generate_post', 'nonce');
        
        $provider = sanitize_text_field($_POST['provider']);
        
        if (empty($provider)) {
            wp_send_json_error(array('message' => __('Provedor não informado', 'ai-post-generator')));
        }
        
        $result = $this->generate_with_provider($provider, 'mountain landscape', 0);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        if ($result) {
            wp_delete_attachment($result, true);
            
            wp_send_json_success(array(
                'message' => sprintf(
                    __('Provedor %s está funcionando corretamente!', 'ai-post-generator'),
                    ucfirst($provider)
                )
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Falha no teste do provedor.', 'ai-post-generator')
            ));
        }
    }
    
    /**
     * Gera via Unsplash
     */
    private function generate_unsplash($topic, $post_id) {
        $api_key = get_option('aipg_unsplash_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Unsplash não configurada', 'ai-post-generator'));
        }
        
        $width = get_option('aipg_image_width', 1920);
        $height = get_option('aipg_image_height', 1080);
        
        $response = wp_remote_get(
            'https://api.unsplash.com/search/photos?query=' . urlencode($topic) . '&per_page=1&orientation=landscape',
            array(
                'headers' => array('Authorization' => 'Client-ID ' . $api_key),
                'timeout' => 30
            )
        );
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (empty($body['results'])) {
            return new WP_Error('no_results', __('Nenhuma imagem encontrada', 'ai-post-generator'));
        }
        
        $image_url = $body['results'][0]['urls']['raw'] . '&w=' . $width . '&h=' . $height . '&fit=crop';
        $photographer = $body['results'][0]['user']['name'];
        
        return $this->download_and_attach($image_url, $topic, $post_id, array(
            'caption' => sprintf(__('Foto por %s no Unsplash', 'ai-post-generator'), $photographer)
        ));
    }
    
    /**
     * Gera via Pexels
     */
    private function generate_pexels($topic, $post_id) {
        $api_key = get_option('aipg_pexels_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Pexels não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_get(
            'https://api.pexels.com/v1/search?query=' . urlencode($topic) . '&per_page=1',
            array(
                'headers' => array('Authorization' => $api_key),
                'timeout' => 30
            )
        );
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (empty($body['photos'])) {
            return new WP_Error('no_results', __('Nenhuma imagem encontrada', 'ai-post-generator'));
        }
        
        $image_url = $body['photos'][0]['src']['large2x'];
        
        return $this->download_and_attach($image_url, $topic, $post_id, array(
            'caption' => __('Imagem do Pexels', 'ai-post-generator')
        ));
    }
    
    /**
     * Gera via Pixabay
     */
    private function generate_pixabay($topic, $post_id) {
        $api_key = get_option('aipg_pixabay_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Pixabay não configurada', 'ai-post-generator'));
        }
        
        $response = wp_remote_get(
            'https://pixabay.com/api/?key=' . $api_key . '&q=' . urlencode($topic) . '&image_type=photo&per_page=3',
            array('timeout' => 30)
        );
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (empty($body['hits'])) {
            return new WP_Error('no_results', __('Nenhuma imagem encontrada', 'ai-post-generator'));
        }
        
        $image_url = $body['hits'][0]['largeImageURL'];
        
        return $this->download_and_attach($image_url, $topic, $post_id, array(
            'caption' => __('Imagem do Pixabay', 'ai-post-generator')
        ));
    }
    
    /**
     * Gera via Pollinations AI (GRÁTIS)
     */
    private function generate_pollinations($topic, $post_id) {
        $width = get_option('aipg_image_width', 1920);
        $height = get_option('aipg_image_height', 1080);
        
        $enhanced_prompt = $this->enhance_prompt($topic);
        
        $image_url = sprintf(
            'https://image.pollinations.ai/prompt/%s?width=%d&height=%d&nologo=true&enhance=true&_ts=%d',
            urlencode($enhanced_prompt),
            $width,
            $height,
            time()
        );
        
        return $this->download_and_attach($image_url, $topic, $post_id, array(
            'caption' => __('Imagem gerada por IA (Pollinations)', 'ai-post-generator')
        ));
    }
    
    /**
     * Gera via DALL-E 3
     */
    private function generate_dalle($topic, $post_id) {
        $api_key = get_option('aipg_openai_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API OpenAI não configurada', 'ai-post-generator'));
        }
        
        $width = get_option('aipg_image_width', 1024);
        $height = get_option('aipg_image_height', 1024);
        $size = $this->get_dalle_size($width, $height);
        
        $enhanced_prompt = $this->enhance_prompt($topic);
        
        $response = wp_remote_post('https://api.openai.com/v1/images/generations', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'model' => 'dall-e-3',
                'prompt' => $enhanced_prompt,
                'size' => $size,
                'quality' => 'standard',
                'n' => 1
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
        
        if (empty($body['data'][0]['url'])) {
            return new WP_Error('no_image', __('Nenhuma imagem gerada', 'ai-post-generator'));
        }
        
        return $this->download_and_attach($body['data'][0]['url'], $topic, $post_id, array(
            'caption' => __('Imagem gerada por DALL-E 3', 'ai-post-generator')
        ));
    }
    
    /**
     * Gera via Stability AI
     */
    private function generate_stability($topic, $post_id) {
        $api_key = get_option('aipg_stability_key');
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Chave API Stability AI não configurada', 'ai-post-generator'));
        }
        
        $width = max(64, min(2048, round(get_option('aipg_image_width', 1024) / 64) * 64));
        $height = max(64, min(2048, round(get_option('aipg_image_height', 1024) / 64) * 64));
        
        $enhanced_prompt = $this->enhance_prompt($topic);
        
        $response = wp_remote_post('https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ),
            'body' => json_encode(array(
                'text_prompts' => array(array('text' => $enhanced_prompt, 'weight' => 1)),
                'cfg_scale' => 7,
                'height' => $height,
                'width' => $width,
                'samples' => 1,
                'steps' => 30
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
        
        if (empty($body['artifacts'][0]['base64'])) {
            return new WP_Error('no_image', __('Nenhuma imagem gerada', 'ai-post-generator'));
        }
        
        return $this->attach_base64($body['artifacts'][0]['base64'], $topic, $post_id, array(
            'caption' => __('Imagem gerada por Stable Diffusion', 'ai-post-generator')
        ));
    }
    
    /**
     * Download e anexa imagem
     */
    private function download_and_attach($image_url, $topic, $post_id, $meta = array(), $ext = 'jpg') {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $tmp = download_url($image_url);
        
        if (is_wp_error($tmp)) {
            return $tmp;
        }
        
        // Verifica se o arquivo baixado possui conteúdo; se estiver vazio, tenta fallback com stream
        $filesize = @filesize($tmp);
        if ($filesize === false || $filesize <= 0) {
            @unlink($tmp);
            $tmp_stream = wp_tempnam($image_url);
            if (!$tmp_stream) {
                return new WP_Error('temp_file', __('Falha ao criar arquivo temporário', 'ai-post-generator'));
            }
            // Usa URL com cache-buster para serviços que podem demorar a gerar (ex.: Pollinations)
            $dl_url = $image_url;
            $host = parse_url($image_url, PHP_URL_HOST);
            if (is_string($host) && stripos($host, 'pollinations.ai') !== false && strpos($image_url, '_ts=') === false) {
                $dl_url .= (strpos($image_url, '?') !== false ? '&' : '?') . '_ts=' . time();
            }
            $response = wp_remote_get($dl_url, array(
                'timeout' => 90,
                'redirection' => 5,
                'sslverify' => ! (bool) get_option('aipg_disable_ssl_verify', false),
                'stream' => true,
                'filename' => $tmp_stream,
                'headers' => array(
                    'Accept' => 'image/*',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AIPG'
                )
            ));
            if (is_wp_error($response)) {
                @unlink($tmp_stream);
                return $response;
            }
            $code = wp_remote_retrieve_response_code($response);
            if ($code !== 200) {
                @unlink($tmp_stream);
                return new WP_Error('download_failed', sprintf(__('Falha ao baixar imagem (HTTP %d)', 'ai-post-generator'), $code));
            }
            $stream_size = @filesize($tmp_stream);
            if ($stream_size === false || $stream_size <= 0) {
                @unlink($tmp_stream);
                $tmp_buf = wp_tempnam($image_url);
                if (!$tmp_buf) {
                    return new WP_Error('temp_file', __('Falha ao criar arquivo temporário', 'ai-post-generator'));
                }
                $resp2 = wp_remote_get($dl_url, array(
                    'timeout' => 90,
                    'redirection' => 5,
                    'sslverify' => ! (bool) get_option('aipg_disable_ssl_verify', false),
                    'headers' => array(
                        'Accept' => 'image/*',
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AIPG'
                    )
                ));
                if (is_wp_error($resp2)) {
                    @unlink($tmp_buf);
                    return $resp2;
                }
                $code2 = wp_remote_retrieve_response_code($resp2);
                if ($code2 !== 200) {
                    @unlink($tmp_buf);
                    return new WP_Error('download_failed', sprintf(__('Falha ao baixar imagem (HTTP %d)', 'ai-post-generator'), $code2));
                }
                $ctype = wp_remote_retrieve_header($resp2, 'content-type');
                $body = wp_remote_retrieve_body($resp2);
                if (!is_string($body) || strlen($body) < 64 || (is_string($ctype) && strpos($ctype, 'image') === false)) {
                    @unlink($tmp_buf);
                    return new WP_Error('empty_file', __('O arquivo retornado está vazio. Verifique configurações do servidor (upload_max_filesize/post_max_size) e bloqueios de saída.', 'ai-post-generator'));
                }
                if (file_put_contents($tmp_buf, $body) === false) {
                    @unlink($tmp_buf);
                    return new WP_Error('save_failed', __('Falha ao salvar imagem', 'ai-post-generator'));
                }
                $tmp = $tmp_buf;
            } else {
                $tmp = $tmp_stream;
            }
        }
        
        $safe_ext = in_array($ext, array('jpg','jpeg','png','webp')) ? $ext : 'jpg';
        if ($safe_ext === 'jpeg') { $safe_ext = 'jpg'; }
        $sniff = $this->sniff_extension_from_file($tmp);
        if ($sniff) {
            $safe_ext = $sniff;
        }
        $file_array = array(
            'name' => sanitize_file_name($topic) . '-' . time() . '.' . $safe_ext,
            'tmp_name' => $tmp
        );
        
        $id = media_handle_sideload($file_array, $post_id, $topic);
        
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return $id;
        }
        
        if (!empty($meta['caption'])) {
            wp_update_post(array('ID' => $id, 'post_excerpt' => $meta['caption']));
        }
        
        update_post_meta($id, '_aipg_generated_image', '1');
        update_post_meta($id, '_aipg_image_provider', get_option('aipg_image_provider'));
        update_post_meta($id, '_wp_attachment_image_alt', $topic);
        
        return $id;
    }
    
    /**
     * Anexa imagem base64
     */
    private function attach_base64($base64_image, $topic, $post_id, $meta = array()) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload_dir = wp_upload_dir();
        $filename = sanitize_file_name($topic) . '-' . time() . '.png';
        $filepath = $upload_dir['path'] . '/' . $filename;
        
        $image_data = base64_decode($base64_image);
        
        if (file_put_contents($filepath, $image_data) === false) {
            return new WP_Error('save_failed', __('Falha ao salvar imagem', 'ai-post-generator'));
        }
        
        $filetype = wp_check_filetype($filename);
        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => $filetype['type'],
            'post_title' => $topic,
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attach_id = wp_insert_attachment($attachment, $filepath, $post_id);
        
        if (is_wp_error($attach_id)) {
            @unlink($filepath);
            return $attach_id;
        }
        
        $attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        if (!empty($meta['caption'])) {
            wp_update_post(array('ID' => $attach_id, 'post_excerpt' => $meta['caption']));
        }
        
        update_post_meta($attach_id, '_aipg_generated_image', '1');
        update_post_meta($attach_id, '_aipg_image_provider', get_option('aipg_image_provider'));
        
        return $attach_id;
    }
    
    /**
     * Detecta extensão a partir dos primeiros bytes do arquivo
     */
    private function sniff_extension_from_file($filepath) {
        $fp = @fopen($filepath, 'rb');
        if (!$fp) return null;
        $bytes = @fread($fp, 12);
        @fclose($fp);
        if (!is_string($bytes) || strlen($bytes) < 4) return null;
        // JPEG
        if (ord($bytes[0]) === 0xFF && ord($bytes[1]) === 0xD8) return 'jpg';
        // PNG
        if (substr($bytes, 0, 8) === "\x89PNG\r\n\x1a\n") return 'png';
        // WEBP (RIFF....WEBP)
        if (substr($bytes, 0, 4) === "RIFF" && substr($bytes, 8, 4) === "WEBP") return 'webp';
        return null;
    }
    
    /**
     * Melhora o prompt para IA
     */
    private function enhance_prompt($topic) {
        $clean_topic = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $topic);
        return sprintf(
            '%s, high quality, detailed, professional photography, 4k, sharp focus, well lit',
            $clean_topic
        );
    }
    
    /**
     * Retorna tamanho válido para DALL-E
     */
    private function get_dalle_size($width, $height) {
        $ratio = $width / $height;
        
        if ($ratio > 1.5) {
            return '1792x1024';
        } elseif ($ratio < 0.7) {
            return '1024x1792';
        } else {
            return '1024x1024';
        }
    }
}
