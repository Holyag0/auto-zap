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
