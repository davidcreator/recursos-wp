<?php
/**
 * Script para gerar arquivos .mo a partir de arquivos .po
 * Usado quando msgfmt não está disponível no sistema
 */

function po_to_mo($po_file, $mo_file) {
    if (!file_exists($po_file)) {
        echo "Arquivo .po não encontrado: $po_file\n";
        return false;
    }
    
    $po_content = file_get_contents($po_file);
    $lines = explode("\n", $po_content);
    
    $translations = array();
    $current_msgid = '';
    $current_msgstr = '';
    $in_msgid = false;
    $in_msgstr = false;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || $line[0] === '#') {
            continue;
        }
        
        // Start of msgid
        if (strpos($line, 'msgid ') === 0) {
            // Save previous translation if exists
            if (!empty($current_msgid) && !empty($current_msgstr)) {
                $translations[$current_msgid] = $current_msgstr;
            }
            
            $current_msgid = substr($line, 7, -1); // Remove 'msgid "' and '"'
            $current_msgstr = '';
            $in_msgid = true;
            $in_msgstr = false;
            continue;
        }
        
        // Start of msgstr
        if (strpos($line, 'msgstr ') === 0) {
            $current_msgstr = substr($line, 8, -1); // Remove 'msgstr "' and '"'
            $in_msgid = false;
            $in_msgstr = true;
            continue;
        }
        
        // Continuation of msgid or msgstr
        if ($line[0] === '"' && $line[strlen($line)-1] === '"') {
            $content = substr($line, 1, -1); // Remove quotes
            if ($in_msgid) {
                $current_msgid .= $content;
            } elseif ($in_msgstr) {
                $current_msgstr .= $content;
            }
        }
    }
    
    // Save last translation
    if (!empty($current_msgid) && !empty($current_msgstr)) {
        $translations[$current_msgid] = $current_msgstr;
    }
    
    // Generate .mo file content
    $mo_content = generate_mo_content($translations);
    
    if (file_put_contents($mo_file, $mo_content) !== false) {
        echo "Arquivo .mo gerado com sucesso: $mo_file\n";
        return true;
    } else {
        echo "Erro ao gerar arquivo .mo: $mo_file\n";
        return false;
    }
}

function generate_mo_content($translations) {
    $keys = array_keys($translations);
    $values = array_values($translations);
    
    // MO file header
    $magic = 0x950412de;
    $revision = 0;
    $count = count($translations);
    
    // Calculate offsets
    $key_start = 28;
    $value_start = $key_start + 8 * $count;
    $key_offsets = array();
    $value_offsets = array();
    
    $key_offset = $value_start + 8 * $count;
    $value_offset = $key_offset;
    
    // Calculate key offsets
    foreach ($keys as $key) {
        $key_offsets[] = array('length' => strlen($key), 'offset' => $key_offset);
        $key_offset += strlen($key) + 1;
    }
    
    $value_offset = $key_offset;
    
    // Calculate value offsets
    foreach ($values as $value) {
        $value_offsets[] = array('length' => strlen($value), 'offset' => $value_offset);
        $value_offset += strlen($value) + 1;
    }
    
    // Build MO file
    $mo = '';
    
    // Header
    $mo .= pack('V', $magic);
    $mo .= pack('V', $revision);
    $mo .= pack('V', $count);
    $mo .= pack('V', $key_start);
    $mo .= pack('V', $value_start);
    $mo .= pack('V', 0); // hash table offset
    $mo .= pack('V', 0); // hash table size
    
    // Key table
    foreach ($key_offsets as $offset) {
        $mo .= pack('V', $offset['length']);
        $mo .= pack('V', $offset['offset']);
    }
    
    // Value table
    foreach ($value_offsets as $offset) {
        $mo .= pack('V', $offset['length']);
        $mo .= pack('V', $offset['offset']);
    }
    
    // Keys
    foreach ($keys as $key) {
        $mo .= $key . "\0";
    }
    
    // Values
    foreach ($values as $value) {
        $mo .= $value . "\0";
    }
    
    return $mo;
}

// Gerar arquivos .mo
$languages_dir = __DIR__;

echo "Gerando arquivos .mo...\n";

// Gerar pt_BR.mo
po_to_mo($languages_dir . '/pt_BR.po', $languages_dir . '/pt_BR.mo');

// Gerar en_US.mo
po_to_mo($languages_dir . '/en_US.po', $languages_dir . '/en_US.mo');

echo "Processo concluído!\n";
?>