#!/bin/bash

if [ -z "$1" ]; then
    echo "❌ Uso: ./configure-webhook.sh WEBHOOK_ID"
    echo ""
    echo "Exemplo:"
    echo "  ./configure-webhook.sh e5xrMci56dg9y63o"
    echo ""
    echo "Para obter o WEBHOOK_ID:"
    echo "  1. Acesse http://localhost:5678"
    echo "  2. Abra seu workflow"
    echo "  3. Clique no nó 'Webhook'"
    echo "  4. Copie o ID da URL (última parte após /webhook/)"
    exit 1
fi

WEBHOOK_ID=$1
WEBHOOK_URL="http://auto-zap-n8n-1:5678/webhook/$WEBHOOK_ID"

echo "🔧 Configurando webhook do n8n..."
echo "================================"
echo ""
echo "Webhook ID: $WEBHOOK_ID"
echo "URL completa: $WEBHOOK_URL"
echo ""

# Verificar se já existe configuração
if grep -q "N8N_WEBHOOK_URL" .env 2>/dev/null; then
    echo "⚠️  Configuração existente encontrada"
    echo "   Removendo configuração antiga..."
    sed -i '/N8N_WEBHOOK_URL/d' .env
    sed -i '/N8N_BASE_URL/d' .env
fi

# Adicionar nova configuração
echo "" >> .env
echo "# Configuração N8N" >> .env
echo "N8N_WEBHOOK_URL=$WEBHOOK_URL" >> .env
echo "N8N_BASE_URL=http://auto-zap-n8n-1:5678" >> .env

echo "✅ Configuração adicionada ao .env"
echo ""

# Testar conexão
echo "🧪 Testando conexão..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$WEBHOOK_URL" -H 'Content-Type: application/json' -d '{"pergunta":"teste de configuração"}')

if [ "$HTTP_CODE" == "200" ] || [ "$HTTP_CODE" == "201" ]; then
    echo "✅ Webhook funcionando! (Status: $HTTP_CODE)"
    echo ""
    echo "📋 Próximos passos:"
    echo "   1. Limpar cache: php artisan config:clear"
    echo "   2. Acessar o painel: http://localhost:8080/admin"
    echo "   3. Testar uma pergunta!"
elif [ "$HTTP_CODE" == "404" ]; then
    echo "❌ Webhook não encontrado (Status: 404)"
    echo ""
    echo "Possíveis causas:"
    echo "  - Webhook ID incorreto"
    echo "  - Workflow não está ativo no n8n"
    echo "  - Webhook não está configurado no workflow"
    echo ""
    echo "Verifique:"
    echo "  1. Se o workflow está ATIVO (toggle verde no n8n)"
    echo "  2. Se o nó Webhook está corretamente configurado"
    echo "  3. Se o ID copiado está correto"
else
    echo "⚠️  Status inesperado: $HTTP_CODE"
    echo ""
    echo "Detalhes da resposta:"
    curl -X POST "$WEBHOOK_URL" -H 'Content-Type: application/json' -d '{"pergunta":"teste"}' 2>&1
fi

echo ""
echo "================================"
echo "✅ Configuração concluída!"

