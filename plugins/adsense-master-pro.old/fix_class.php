<?php
// Script para corrigir o problema com o método preload_resources

// Definir o caminho para o arquivo do plugin
$plugin_file = __DIR__ . '/adsense-master-pro.php';

// Verificar se o arquivo existe
if (!file_exists($plugin_file)) {
    die("Arquivo do plugin não encontrado: $plugin_file");
}

// Ler o conteúdo do arquivo
$content = file_get_contents($plugin_file);

// Verificar se o método preload_resources está sendo registrado
$hook_pattern = '/add_action\s*\(\s*[\'"]wp_head[\'"]\s*,\s*array\s*\(\s*\$this\s*,\s*[\'"]preload_resources[\'"]\s*\)\s*,\s*\d+\s*\)\s*;/';

// Remover o hook existente
$content = preg_replace($hook_pattern, '// Hook removido temporariamente', $content);

// Adicionar um método estático simples para preload_resources
$method_pattern = '/public\s+function\s+preload_resources\s*\(\)\s*\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s';
$new_method = 'public function preload_resources() {
        // Pré-carregar recursos do Google AdSense para melhorar o desempenho
        echo \'<link rel="preconnect" href="https://pagead2.googlesyndication.com">\'."\n";
        echo \'<link rel="preconnect" href="https://googleads.g.doubleclick.net">\'."\n";
        echo \'<link rel="preconnect" href="https://tpc.googlesyndication.com">\'."\n";
        echo \'<link rel="preconnect" href="https://www.google-analytics.com">\'."\n";
        
        // Pré-carregar o script do AdSense
        if (!empty($this->settings[\'publisher_id\'])) {
            echo \'<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=\'.esc_attr($this->settings[\'publisher_id\']).\'">\'." \n";
        } else {
            echo \'<link rel="preload" as="script" href="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js">\'." \n";
        }
    }';

// Substituir o método existente ou adicionar o novo método
if (preg_match($method_pattern, $content)) {
    $content = preg_replace($method_pattern, $new_method, $content);
    echo "Método preload_resources substituído com sucesso!\n";
} else {
    // Adicionar o método após o construtor
    $constructor_pattern = '/public\s+function\s+__construct\s*\([^)]*\)\s*\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s';
    if (preg_match($constructor_pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        $end_of_constructor = $matches[0][1] + strlen($matches[0][0]);
        $content = substr_replace($content, "\n\n    " . $new_method, $end_of_constructor, 0);
        echo "Método preload_resources adicionado após o construtor!\n";
    } else {
        echo "Não foi possível encontrar o construtor para adicionar o método preload_resources.\n";
    }
}

// Adicionar o hook novamente após a definição da classe
$class_end_pattern = '/}\s*\/\/\s*End\s+of\s+class/i';
if (preg_match($class_end_pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
    $init_code = '
    
    // Inicializar hooks
    public function init_hooks() {
        // Adicionar preload_resources ao wp_head
        add_action(\'wp_head\', array($this, \'preload_resources\'), 1);
    }
';
    $content = substr_replace($content, $init_code, $matches[0][1], 0);
    
    // Adicionar chamada para init_hooks no construtor
    $constructor_end_pattern = '/public\s+function\s+__construct\s*\([^)]*\)\s*\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s';
    if (preg_match($constructor_end_pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        $constructor_content = $matches[0][0];
        $last_brace_pos = strrpos($constructor_content, '}');
        $new_constructor = substr($constructor_content, 0, $last_brace_pos) . "\n        \$this->init_hooks();\n    }";
        $content = str_replace($constructor_content, $new_constructor, $content);
        echo "Chamada para init_hooks adicionada ao construtor!\n";
    }
}

// Salvar o arquivo modificado
if (file_put_contents($plugin_file, $content)) {
    echo "Arquivo do plugin atualizado com sucesso!\n";
} else {
    echo "Erro ao salvar o arquivo modificado.\n";
}

// Limpar o cache do PHP OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache limpo com sucesso.\n";
} else {
    echo "OPcache não está disponível.\n";
}

echo "Processo concluído. Verifique se o erro foi resolvido.";
?>