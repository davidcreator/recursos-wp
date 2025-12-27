# Guia de Logging - AI Post Generator

## Overview

O sistema de logging do AI Post Generator fornece rastreamento completo de todas as operações do plugin, especialmente útil para debugging de problemas com APIs de IA.

## Estrutura

### AIPG_Logger (Classe Principal)

Singleton responsável por:
- Escrever logs em arquivos diários
- Formatar mensagens com contexto
- Gerenciar arquivo de logs

### AIPG_Logging (Trait)

Trait para adicionar suporte a logging em qualquer classe:

## Níveis de Log (PSR-3)

| Nível | Uso | Exemplo |
|-------|-----|---------|
| **emergency** | Sistema totalmente inoperável | Arquivo de config corrompido |
| **alert** | Ação imediata necessária | Falha crítica de API |
| **critical** | Condições críticas | Erro ao criar post |
| **error** | Erros que impedem operação | Requisição API falhou |
| **warning** | Situações incomuns | Taxa limite de API próxima |
| **notice** | Eventos normais significativos | Post gerado com sucesso |
| **info** | Mensagens informativas | Requisição iniciada |
| **debug** | Informações detalhadas | Detalhes da requisição |

## Locação dos Logs

Um arquivo por dia, cada linha contém:
- Timestamp
- Nível
- Mensagem
- Arquivo e linha do código
- Contexto em JSON

## Exemplos de Uso

### Log Simples

````````

### Com Contexto

````````

### Requisições API

````````

### Geração de Conteúdo

````````

## Limpeza de Logs

Logs com mais de 30 dias são automaticamente deletados:

````````

## Recuperando Logs

````````

## Boas Práticas

✅ **Faça:**
- Incluir contexto relevante em cada log
- Usar o nível apropriado
- Logar requisições de API
- Logar erros com detalhes

❌ **Evite:**
- Logar chaves de API ou tokens
- Logar senhas
- Logar dados sensíveis do usuário
- Logs excessivos em operações normais

## Troubleshooting

### Logs não aparecem

1. Verifique permissões em `/wp-content/uploads/`
2. Verifique se WP_DEBUG está ativado
3. Verifique o arquivo de log diretamente

### Arquivo de log muito grande

Execute limpeza manual:

````````

## Exemplo Completo
```
public function processar_geracao() {
    $this->init_logger();
    
    try {
        $this->log_info("Iniciando processamento");
        
        // Validação
        if (!$this->validar_entrada()) {
            $this->log_warning("Entrada inválida", ['entrada' => $_POST]);
            return new WP_Error('invalid_input', 'Entrada inválida');
        }
        
        // Processamento
        $resultado = $this->chamar_api();
        
        if (is_wp_error($resultado)) {
            $this->log_error("Erro ao chamar API", [
                'codigo' => $resultado->get_error_code(),
                'mensagem' => $resultado->get_error_message(),
            ]);
            return $resultado;
        }
        
        $this->log_info("Sucesso", ['resultado_id' => $resultado['id']]);
        return $resultado;
        
    } catch (Exception $e) {
        $this->log_error("Exceção: " . $e->getMessage(), [
            'arquivo' => $e->getFile(),
            'linha' => $e->getLine(),
        ]);
        return new WP_Error('exception', $e->getMessage());
    }
}
```

