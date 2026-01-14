#!/bin/bash

echo "🔍 Testando Conexão com N8N"
echo "================================"
echo ""

echo "1️⃣ Testando conexão básica com n8n..."
if curl -s -o /dev/null -w "%{http_code}" http://auto-zap-n8n-1:5678 | grep -q "200"; then
    echo "✅ N8N está acessível"
else
    echo "❌ N8N não está acessível"
    exit 1
fi

echo ""
echo "2️⃣ Testando webhook sem ID (deve retornar 404)..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST http://auto-zap-n8n-1:5678/webhook -H 'Content-Type: application/json' -d '{"pergunta":"teste"}')
echo "Status: $HTTP_CODE"
if [ "$HTTP_CODE" == "404" ]; then
    echo "⚠️  Webhook não configurado (esperado)"
    echo ""
    echo "📋 AÇÃO NECESSÁRIA:"
    echo "   1. Acesse http://localhost:5678"
    echo "   2. Abra seu workflow"
    echo "   3. Clique no nó 'Webhook' ou 'When chat message received'"
    echo "   4. Copie o ID do webhook (ex: e5xrMci56dg9y63o)"
    echo "   5. Execute: ./configure-webhook.sh SEU_WEBHOOK_ID"
fi

echo ""
echo "3️⃣ Verificando configuração atual..."
if grep -q "N8N_WEBHOOK_URL" .env 2>/dev/null; then
    WEBHOOK_URL=$(grep "N8N_WEBHOOK_URL" .env | cut -d'=' -f2-)
    echo "✅ Configurado: $WEBHOOK_URL"
    
    # Testar o webhook configurado
    echo ""
    echo "4️⃣ Testando webhook configurado..."
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$WEBHOOK_URL" -H 'Content-Type: application/json' -d '{"pergunta":"teste de conexão"}')
    echo "Status: $HTTP_CODE"
    
    if [ "$HTTP_CODE" == "200" ]; then
        echo "✅ Webhook funcionando!"
    else
        echo "❌ Webhook retornou erro $HTTP_CODE"
        echo "   Verifique se o workflow está ativo no n8n"
    fi
else
    echo "⚠️  N8N_WEBHOOK_URL não configurado no .env"
fi

echo ""
echo "================================"

