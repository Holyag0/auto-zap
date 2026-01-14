# 🔗 Configuração de Integração com N8N

## 📋 Visão Geral

O sistema FAQ CABEMCE agora pode testar perguntas diretamente no painel administrativo, enviando requisições para o workflow n8n.

## ⚙️ Configuração

### 1. Configurar o Webhook no N8N

No seu workflow n8n, você precisa:

1. **Adicionar um nó "Webhook"** no início do fluxo (se ainda não tiver)
2. **Configurar o webhook para aceitar POST**
3. **Anotar a URL do webhook**

**Exemplo de URL:**
```
http://localhost:5678/webhook/e5xrMci56dg9y63o
```

### 2. Adicionar URL ao .env do Laravel

Edite o arquivo `.env` e adicione:

```bash
N8N_WEBHOOK_URL=http://auto-zap-n8n-1:5678/webhook/SEU_WEBHOOK_ID
N8N_BASE_URL=http://auto-zap-n8n-1:5678
```

**Importante:** 
- Use `auto-zap-n8n-1` (nome do container) ao invés de `localhost`
- Substitua `SEU_WEBHOOK_ID` pelo ID real do seu webhook

### 3. Formato de Dados Enviados

O sistema envia os seguintes dados para o n8n:

```json
{
  "pergunta": "Qual o horário de funcionamento?",
  "context": {
    "faq_id": 30,
    "categoria": "horarios",
    "resposta_esperada": "Funcionamos das 8h às 18h"
  },
  "timestamp": "2026-01-02T15:45:00.000000Z"
}
```

### 4. Formato de Resposta Esperado

O n8n deve retornar um JSON com a resposta:

```json
{
  "resposta": "A CABEMCE funciona de segunda a sexta, das 8h às 18h.",
  "model": "gemini-1.5-pro",
  "confidence": 0.95
}
```

**Campo obrigatório:**
- `resposta`: Texto da resposta do modelo

**Campos opcionais:**
- `model`: Nome do modelo usado
- `confidence`: Confiança da resposta (0-1)
- Qualquer outro dado adicional

## 🎯 Funcionalidades Disponíveis

### 1. Testar FAQ Individual

Na lista de FAQs, cada registro tem um botão **"Testar"** (ícone de play verde).

**Como usar:**
1. Acesse o painel admin: http://localhost:8080/admin
2. Vá em "Perguntas e Respostas"
3. Clique no botão "Testar" na FAQ desejada
4. Aguarde a resposta do modelo
5. Uma notificação aparecerá com a resposta

### 2. Testar Pergunta Livre

Existe uma página dedicada para testes de perguntas livres.

**Como usar:**
1. No menu lateral, clique em "Testar Perguntas"
2. Digite qualquer pergunta no formulário
3. Clique em "Enviar para Modelo"
4. A resposta aparecerá abaixo do formulário

## 🔧 Exemplo de Workflow N8N

```
┌─────────────┐
│   Webhook   │
│   (POST)    │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Postgres   │
│  (Buscar    │
│   FAQs)     │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Google    │
│   Gemini    │
│   (Modelo)  │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Respond    │
│  to Webhook │
└─────────────┘
```

### Configuração do Nó Webhook

**Method:** POST  
**Path:** `/webhook/SEU_ID`  
**Response Mode:** "Last Node"

### Configuração do Nó PostgreSQL

```sql
SELECT id, pergunta, resposta, categoria 
FROM faq 
WHERE ativo = true 
ORDER BY id;
```

### Configuração do Nó Gemini

**Prompt Exemplo:**
```
Você é um assistente da CABEMCE. Use as seguintes FAQs para responder:

{{$json.faqs}}

Pergunta do usuário: {{$json.body.pergunta}}

Responda de forma clara e objetiva.
```

### Configuração do Respond to Webhook

**Response Body:**
```json
{
  "resposta": "={{$json.response}}",
  "model": "gemini-1.5-pro",
  "timestamp": "={{$now}}"
}
```

## 🐛 Troubleshooting

### Erro: "Erro ao conectar com o n8n"

**Possíveis causas:**
1. Container n8n não está rodando
2. URL do webhook incorreta no .env
3. Firewall bloqueando comunicação entre containers

**Solução:**
```bash
# Verificar se n8n está rodando
docker ps | grep n8n

# Testar conectividade
docker exec faq_workspace curl http://auto-zap-n8n-1:5678

# Verificar .env
docker exec faq_workspace cat /var/www/faq-cabemce/.env | grep N8N
```

### Erro: "Timeout"

**Causa:** O modelo está demorando muito para responder

**Solução:** Ajustar timeout no `N8nService.php`:
```php
$response = Http::timeout(60) // Aumentar de 30 para 60 segundos
    ->post($this->webhookUrl, $payload);
```

### Webhook não está recebendo dados

**Verificar:**
1. O workflow está ativo no n8n?
2. O webhook está aguardando requisição?
3. A URL está correta?

```bash
# Testar manualmente
curl -X POST http://localhost:5678/webhook/SEU_ID \
  -H "Content-Type: application/json" \
  -d '{"pergunta": "teste"}'
```

## 📊 Logs

Ver logs do sistema:
```bash
# Logs do Laravel
docker exec faq_workspace tail -f /var/www/faq-cabemce/storage/logs/laravel.log

# Logs do container
docker logs faq_workspace -f
```

## 🔐 Segurança

**Recomendações:**
1. Não exponha o webhook n8n publicamente
2. Use autenticação no webhook (API key)
3. Valide dados de entrada
4. Limite rate de requisições

## 📚 Recursos Adicionais

- [Documentação N8N Webhook](https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-base.webhook/)
- [Laravel HTTP Client](https://laravel.com/docs/11.x/http-client)
- [Filament Actions](https://filamentphp.com/docs/3.x/actions/overview)

