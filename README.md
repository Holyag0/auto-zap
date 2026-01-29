# 💬 AutoZap Stack - Chatwoot + n8n + Evolution API

Este projeto configura um ambiente completo com:

- [Chatwoot](https://www.chatwoot.com/) — plataforma de atendimento ao cliente
- [n8n](https://n8n.io/) — automações visuais low-code
- [Evolution API](https://github.com/Atendai/evolution-api) — gateway de WhatsApp
- PostgreSQL + Redis

---

## 📦 Tecnologias Utilizadas

- Docker
- Docker Compose
- PostgreSQL (com pgvector)  localhost:5433
- Redis  	  localhost:6379
- Chatwoot  http://localhost:3000
- n8n       http://localhost:5678
- Evolution API   http://localhost:8081/manager/

---

## 📁 Pré-requisitos

- Docker e Docker Compose instalados
- Git instalado

---

## 🚀 Como subir o projeto

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/auto-zap.git
cd auto-zap
docker-compose up -d
```

### 2. Configurar Ngrok (Opcional - para testes externos)

Para expor o sistema na internet para testes, configure o ngrok:```bash
# 1. Obter token em: https://dashboard.ngrok.com/get-started/your-authtoken
# 2. Configurar token
export NGROK_AUTHTOKEN=seu_token_aqui

# 3. Iniciar ngrok
./start-ngrok.sh
```

📚 **Documentação completa**: Veja [NGROK_SETUP.md](./NGROK_SETUP.md)

---

## 🌐 Expor Sistema com Ngrok

O projeto inclui configuração do ngrok em container Docker para expor os serviços localmente na internet.

**Resposta: Container Docker vs Instalação Local**
- ✅ **Recomendado: Container Docker** - Mais fácil de gerenciar, não polui o sistema, versionado no projeto
- ❌ Instalação local - Requer instalação manual, mais difícil de remover

**Portas dos Serviços:**
- FAQ System (Laravel): `localhost:8080` → Exposto via ngrok
- Chatwoot: `localhost:3000`
- n8n: `localhost:5678`
- Evolution API: `localhost:8081`

---
