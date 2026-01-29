# 🚀 Guia Rápido - Subir Containers

## ✅ Correções Aplicadas no docker-compose.yaml

### 1. Healthchecks Adicionados
- ✅ `evolution-db`: Healthcheck para PostgreSQL
- ✅ `evolution-redis`: Healthcheck para Redis
- ✅ Todos os serviços agora aguardam dependências ficarem prontas

### 2. Dependências Corrigidas
- ✅ `evolution-api` agora aguarda `evolution-db` e `evolution-redis` ficarem saudáveis
- ✅ `n8n` aguarda `redis` ficar saudável
- ✅ `faq-workspace` aguarda `postgres` ficar saudável

### 3. Rede Configurada
- ✅ Todos os serviços estão na mesma rede (`auto-zap-network`)
- ✅ Comunicação entre containers garantida

### 4. Nomes de Containers
- ✅ `faq-workspace` agora usa `postgres` (nome do serviço) ao invés de nome hardcoded

---

## 📋 Como Subir os Containers

### Opção 1: Subir Todos os Serviços

```bash
# Subir todos os containers
docker compose up -d

# Verificar status
docker compose ps

# Ver logs de todos os serviços
docker compose logs -f
```

### Opção 2: Subir Serviços Específicos

```bash
# Apenas infraestrutura (PostgreSQL, Redis)
docker compose up -d postgres redis

# Chatwoot completo
docker compose up -d postgres redis chatwoot-init rails sidekiq

# Evolution API
docker compose up -d evolution-db evolution-redis evolution-api

# n8n
docker compose up -d redis n8n

# FAQ System
docker compose up -d postgres faq-workspace
```

---

## 🔍 Verificar se Está Funcionando

### 1. Verificar Containers em Execução

```bash
docker compose ps
```

Todos devem estar com status `Up` ou `Up (healthy)`.

### 2. Testar Acessos

```bash
# Chatwoot
curl http://localhost:3000

# n8n
curl http://localhost:5678

# Evolution API
curl http://localhost:8081/manager/health

# FAQ System
curl http://localhost:8080
```

### 3. Verificar Logs

```bash
# Logs de um serviço específico
docker compose logs -f rails
docker compose logs -f evolution-api
docker compose logs -f n8n
docker compose logs -f faq-workspace
```

---

## 🐛 Troubleshooting

### Problema: Container não inicia

**Solução:**
```bash
# Ver logs do container
docker compose logs nome-do-servico

# Reiniciar container
docker compose restart nome-do-servico

# Recriar container
docker compose up -d --force-recreate nome-do-servico
```

### Problema: Container aguardando dependências

**Solução:**
```bash
# Verificar healthcheck das dependências
docker compose ps

# Se dependência não está saudável, verificar logs
docker compose logs -f postgres
docker compose logs -f redis
```

### Problema: Erro de conexão entre containers

**Solução:**
```bash
# Verificar se estão na mesma rede
docker network inspect auto-zap-network

# Verificar se o nome do serviço está correto
# Use o nome do serviço (ex: postgres) não o nome do container
```

### Problema: Porta já em uso

**Solução:**
```bash
# Verificar qual processo está usando a porta
lsof -i :3000  # Para porta 3000
lsof -i :8081  # Para porta 8081

# Parar o processo ou alterar a porta no docker-compose.yaml
```

---

## 📊 Ordem de Inicialização Recomendada

1. **Infraestrutura Base**
   ```bash
   docker compose up -d postgres redis
   ```

2. **Aguardar healthchecks** (30-60 segundos)

3. **Chatwoot**
   ```bash
   docker compose up -d chatwoot-init
   # Aguardar conclusão
   docker compose up -d rails sidekiq
   ```

4. **Evolution API**
   ```bash
   docker compose up -d evolution-db evolution-redis
   # Aguardar healthchecks
   docker compose up -d evolution-api
   ```

5. **n8n**
   ```bash
   docker compose up -d n8n
   ```

6. **FAQ System**
   ```bash
   docker compose up -d faq-workspace
   ```

7. **Ngrok (opcional)**
   ```bash
   export NGROK_AUTHTOKEN=seu_token
   docker compose up -d ngrok
   ```

---

## 🔐 Credenciais Padrão

### Chatwoot
- **URL**: http://localhost:3000
- **Email**: admin@localhost.com
- **Senha**: #Cabemce2025#

### n8n
- **URL**: http://localhost:5678
- **Usuário**: admin
- **Senha**: teste_123

### Evolution API
- **URL**: http://localhost:8081/manager/
- **API Key**: 429683C4C977415CAAFCCE10F7D57E11

### PostgreSQL (Chatwoot)
- **Host**: localhost:5433
- **Database**: chatwoot
- **User**: postgres
- **Password**: postgres_teste_123

### PostgreSQL (Evolution)
- **Host**: evolution-db (dentro do Docker)
- **Database**: evolution
- **User**: evolution_user
- **Password**: evolution_teste_123

### Redis
- **Host**: localhost:6379
- **Password**: redis_teste_123

---

## 🧹 Limpeza

### Parar Todos os Containers

```bash
docker compose down
```

### Parar e Remover Volumes (⚠️ Apaga dados!)

```bash
docker compose down -v
```

### Remover Imagens

```bash
docker compose down --rmi all
```

---

## 📝 Notas Importantes

1. **Primeira Execução**: O `chatwoot-init` pode demorar alguns minutos na primeira vez
2. **Healthchecks**: Alguns serviços podem levar 30-60 segundos para ficarem saudáveis
3. **Portas**: Certifique-se de que as portas não estão em uso por outros serviços
4. **Memória**: O stack completo pode consumir ~2-3GB de RAM
5. **Rede**: Todos os serviços devem estar na mesma rede Docker para comunicação

---

## 🔄 Atualizar Containers

```bash
# Parar containers
docker compose down

# Atualizar imagens
docker compose pull

# Subir novamente
docker compose up -d
```

---

## 📚 Documentação Relacionada

- [Integração Chatwoot + Evolution API](./INTEGRACAO_CHATWOOT_EVOLUTION.md)
- [README Principal](./README.md)
- [Configuração N8N](./faq-cabemce/CONFIGURACAO_N8N.md)
