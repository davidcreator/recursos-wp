<?php
// Script para limpar o cache OPcache

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache limpo com sucesso.";
} else {
    echo "OPcache não está disponível.";
}
?>