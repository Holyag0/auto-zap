#!/bin/bash

# Script para iniciar o ngrok com o projeto AutoZap
# Autor: AutoZap Team
# Data: 2025

set -e

echo "🌐 Configurando Ngrok para AutoZap"
echo "===================================="
echo ""

# Verificar se o token do ngrok está configurado
if [ -z "$NGROK_AUTHTOKEN" ]; then
    echo "❌ ERRO: Token do ngrok não configurado!"
    echo ""
    echo "📋 Para obter seu token:"
    echo "   1. Acesse: https://dashboard.ngrok.com/get-started/your-authtoken"
    echo "   2. Faça login ou crie uma conta gratuita"
    echo "   3. Copie o token fornecido"
    echo ""
    echo "💡 Configure o token de uma das formas:"
    echo ""
    echo "   Opção 1 - Variável de ambiente (recomendado):"
    echo "   export NGROK_AUTHTOKEN=seu_token_aqui"
    echo ""
    echo "   Opção 2 - Arquivo .env (na raiz do projeto):"
    echo "   echo 'NGROK_AUTHTOKEN=seu_token_aqui' >> .env"
    echo ""
    echo "   Opção 3 - Passar como parâmetro:"
    echo "   NGROK_AUTHTOKEN=seu_token_aqui ./start-ngrok.sh"
    echo ""
    exit 1
fi

# Verificar se o docker-compose está rodando
if ! docker ps | grep -q faq_workspace; then
    echo "⚠️  O container faq_workspace não está rodando!"
    echo ""
    echo "🚀 Iniciando os serviços necessários..."
    docker compose up -d faq-workspace
    sleep 3
fi

echo "✅ Token configurado: ${NGROK_AUTHTOKEN:0:10}..."
echo ""

# Verificar se o arquivo ngrok.yml existe
if [ ! -f "ngrok.yml" ]; then
    echo "❌ Arquivo ngrok.yml não encontrado!"
    exit 1
fi

echo "🚀 Iniciando ngrok..."
echo ""

# Iniciar o ngrok
docker compose up -d ngrok

echo ""
echo "⏳ Aguardando ngrok iniciar..."
sleep 5

# Obter a URL pública do túnel
echo ""
echo "🔍 Obtendo URL pública..."
sleep 3

# Tentar obter a URL da API do ngrok (aguardar um pouco mais)
echo "⏳ Aguardando túnel ser estabelecido..."
sleep 5

# Verificar se o container está rodando
if ! docker ps | grep -q ngrok_tunnel; then
    echo "❌ Erro: Container ngrok não está rodando!"
    echo ""
    echo "📝 Verifique os logs:"
    echo "   docker logs ngrok_tunnel"
    exit 1
fi

# Tentar obter a URL da API do ngrok (múltiplas tentativas)
MAX_TRIES=5
NGROK_URL=""

for i in $(seq 1 $MAX_TRIES); do
    sleep 2
    NGROK_URL=$(docker exec ngrok_tunnel curl -s http://localhost:4040/api/tunnels 2>/dev/null | grep -o '"public_url":"https://[^"]*"' | head -1 | cut -d'"' -f4)
    
    if [ -n "$NGROK_URL" ]; then
        break
    fi
    
    echo "   Tentativa $i/$MAX_TRIES..."
done

if [ -z "$NGROK_URL" ]; then
    echo "⚠️  Não foi possível obter a URL automaticamente."
    echo ""
    echo "📋 Acesse a interface web do ngrok para ver a URL:"
    echo "   http://localhost:4040"
    echo ""
    echo "📝 Ou verifique os logs:"
    echo "   docker logs ngrok_tunnel"
    echo ""
    echo "💡 Dica: O túnel pode levar alguns segundos para ser estabelecido."
else
    echo ""
    echo "✅ Ngrok configurado com sucesso!"
    echo ""
    echo "🌐 URL pública do sistema FAQ:"
    echo "   $NGROK_URL"
    echo ""
    echo "📋 Interface web do ngrok:"
    echo "   http://localhost:4040"
    echo ""
    echo "🔗 Acesse o painel admin em:"
    echo "   $NGROK_URL/admin"
    echo ""
    echo "👤 Credenciais:"
    echo "   Email: admin@cabemce.com"
    echo "   Senha: #Cabemce2025#"
    echo ""
fi

echo ""
echo "📝 Comandos úteis:"
echo "   Ver logs:        docker logs ngrok_tunnel -f"
echo "   Parar ngrok:     docker compose stop ngrok"
echo "   Reiniciar:       docker compose restart ngrok"
echo ""
