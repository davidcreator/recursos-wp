<?php
// Limpar o cache do PHP OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache limpo com sucesso.\n";
} else {
    echo "OPcache não está disponível.\n";
}

// Limpar o cache do WordPress
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "Cache do WordPress limpo com sucesso.\n";
}

echo "Todos os caches foram limpos. As alterações devem ser aplicadas agora.";