# Configuração de Logging - AI Post Generator

## Instalação

### 1. Copiar Arquivos

````````

### 2. Atualizar Arquivo Principal

O arquivo `ai-post-generator.php` já está atualizado com:

````````

## Uso Rápido

### Em Classes com Logging

```
public function fazer_algo() {
    $this->init_logger(); // Uma vez
    $this->log_info("Fazendo algo");
}
```


### Diretamente no Plugin

```
aipg_logger()->error('Erro crítico', ['post_id' => 123]); aipg_logger()->info('Sucesso', ['dados' => 'contexto']);
```


## Visualizar Logs no Admin

1. Vá para **AI Posts** → **Logs**
2. Filtre por nível ou texto
3. Baixe ou limpe os logs

## Locação dos Logs


````````

## Exemplo Completo de Integração

```
public function __construct() {
    $this->init_logger();
}

public function generate($data) {
    $this->log_info("Iniciando geração", ['topic' => $data['topic']]);
    
    try {
        $result = $this->call_api($data);
        $this->log_info("Sucesso", ['content_size' => strlen($result)]);
        return $result;
    } catch (Exception $e) {
        $this->log_error("Erro: " . $e->getMessage(), [
            'topic' => $data['topic'],
            'code' => $e->getCode(),
        ]);
        throw $e;
    }
}
```

## Dicas de Debugging

### Encontrar Erros Recentes

```
grep -i ERROR /wp-content/uploads/aipg-logs/aipg-*.log
```


### Rastrear Requisição Específica

```
grep "api-request|api-response" /wp-content/uploads/aipg-logs/aipg-*.log
```


### Monitor em Tempo Real

```
tail -f /wp-content/uploads/aipg-logs/aipg-latest.log
```


## Troubleshooting

### "Permissão negada em /uploads/"

```
chmod 755 /wp-content/uploads/
chown www-data:www-data /wp-content/uploads/
```


### Logs não aparecem

1. Verifique `WP_DEBUG` está ativado
2. Verifique permissões da pasta `uploads`
3. Procure erros em `debug.log`

## Performance

- Logs são escritos **assincronamente** via `error_log()`
- Limpeza automática **1x por semana** de logs com 30+ dias
- Sem impacto mensurável em performance


Em qualquer lugar
aipg_logger()->info('Mensagem', ['contexto' => 'dados']);

// Em classes
class Minha_Classe {
    use AIPG_Logging;
    public function __construct() {
        $this->init_logger();
    }
    public function fazer() {
        $this->log_info('Fazendo');
    }
}

