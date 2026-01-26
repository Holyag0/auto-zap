#!/bin/bash

echo "🚀 Instalando Sistema de Senhas - CABEMCE"
echo "=========================================="
echo ""

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Instalar dependências do Composer
echo -e "${BLUE}1. Instalando dependências do Composer...${NC}"
docker exec faq_workspace composer require simplesoftwareio/simple-qrcode
echo -e "${GREEN}✅ Dependências instaladas${NC}"
echo ""

# 2. Rodar migrations
echo -e "${BLUE}2. Rodando migrations...${NC}"
docker exec faq_workspace php artisan migrate
echo -e "${GREEN}✅ Migrations executadas${NC}"
echo ""

# 3. Limpar cache
echo -e "${BLUE}3. Limpando cache...${NC}"
docker exec faq_workspace php artisan config:clear
docker exec faq_workspace php artisan cache:clear
docker exec faq_workspace php artisan view:clear
echo -e "${GREEN}✅ Cache limpo${NC}"
echo ""

# 4. Popular dados de exemplo
echo -e "${BLUE}4. Populando dados de exemplo...${NC}"
docker exec faq_workspace php artisan db:seed --class=SenhaSystemSeeder
echo -e "${GREEN}✅ Dados populados${NC}"
echo ""

# 5. Exibir URLs de acesso
echo -e "${GREEN}=========================================="
echo "🎉 Instalação concluída com sucesso!"
echo "==========================================${NC}"
echo ""
echo -e "${YELLOW}📌 URLs de Acesso:${NC}"
echo ""
echo "🔐 Painel Admin:"
echo "   http://localhost:8080/admin"
echo "   Email: admin@cabemce.com"
echo "   Senha: #Cabemce2025#"
echo ""
echo "📊 Painéis Públicos:"
echo "   http://localhost:8080/painel (todos setores)"
echo "   http://localhost:8080/painel/1 (setor específico)"
echo ""
echo "📱 Autoatendimento:"
echo "   http://localhost:8080/senha/1/qrcode"
echo "   http://localhost:8080/senha/1"
echo ""
echo -e "${YELLOW}⚠️  Importante:${NC}"
echo "- Acesse /admin/setors para ver os códigos de acesso gerados"
echo "- Configure os setores na aba 'Configuração de Senhas'"
echo "- Os códigos de acesso foram exibidos no terminal acima"
echo ""
echo -e "${GREEN}📖 Documentação completa: README_SISTEMA_SENHAS.md${NC}"
echo ""
