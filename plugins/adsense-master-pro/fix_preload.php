<?php
// Definir o caminho para o arquivo do plugin
$plugin_file = __DIR__ . '/adsense-master-pro.php';

// Verificar se o arquivo existe
if (!file_exists($plugin_file)) {
    die("Arquivo do plugin não encontrado: $plugin_file");
}

// Ler o conteúdo do arquivo
$content = file_get_contents($plugin_file);

// Verificar se o método preload_resources existe
if (strpos($content, 'public function preload_resources()') === false) {
    // Adicionar o método preload_resources após o método insert_auto_ads
    $insert_position = strpos($content, 'public function insert_auto_ads()');
    if ($insert_position !== false) {
        // Encontrar o final do método insert_auto_ads
        $end_of_method = strpos($content, '}', $insert_position);
        $end_of_method = strpos($content, '}', $end_of_method + 1);
        
        // Código do método preload_resources
        $preload_method = '
    
    public function preload_resources() {
        // Pré-carregar recursos do Google AdSense para melhorar o desempenho
        echo \'<link rel="preconnect" href="https://pagead2.googlesyndication.com">\'.\"\n\";
        echo \'<link rel="preconnect" href="https://googleads.g.doubleclick.net">\'.\"\n\";
        echo \'<link rel="preconnect" href="https://www.google-analytics.com">\'.\"\n\";
        
        // Pré-carregar o script do AdSense
        if (!empty($this->settings[\'publisher_id\'])) {
            echo \'<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=\'.esc_attr($this->settings[\'publisher_id\']).\'">\';
        } else {
            echo \'<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">\';
        }
    }';
        
        // Inserir o método no conteúdo
        $content = substr_replace($content, $preload_method, $end_of_method + 1, 0);
        
        // Salvar o arquivo modificado
        if (file_put_contents($plugin_file, $content)) {
            echo "Método preload_resources adicionado com sucesso!\n";
        } else {
            echo "Erro ao salvar o arquivo modificado.\n";
        }
    } else {
        echo "Não foi possível encontrar o método insert_auto_ads para inserir o método preload_resources após ele.\n";
    }
} else {
    // O método já existe, vamos substituí-lo
    $pattern = '/public\s+function\s+preload_resources\s*\(\)\s*\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s';
    $replacement = 'public function preload_resources() {
        // Pré-carregar recursos do Google AdSense para melhorar o desempenho
        echo \'<link rel="preconnect" href="https://pagead2.googlesyndication.com">\'.\"\n\";
        echo \'<link rel="preconnect" href="https://googleads.g.doubleclick.net">\'.\"\n\";
        echo \'<link rel="preconnect" href="https://www.google-analytics.com">\'.\"\n\";
        
        // Pré-carregar o script do AdSense
        if (!empty($this->settings[\'publisher_id\'])) {
            echo \'<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=\'.esc_attr($this->settings[\'publisher_id\']).\'">\';
        } else {
            echo \'<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">\';
        }
    }';
    
    $new_content = preg_replace($pattern, $replacement, $content);
    
    if ($new_content !== $content) {
        if (file_put_contents($plugin_file, $new_content)) {
            echo "Método preload_resources substituído com sucesso!\n";
        } else {
            echo "Erro ao salvar o arquivo modificado.\n";
        }
    } else {
        echo "Não foi possível substituir o método preload_resources.\n";
    }
}

// Limpar o cache do PHP OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache limpo com sucesso.\n";
} else {
    echo "OPcache não está disponível.\n";
}

echo "Processo concluído. Verifique se o erro foi resolvido.";