# Sistema de Gerenciamento de FAQ - CABEMCE

Sistema desenvolvido em Laravel com Filament para gerenciar perguntas e respostas do modelo de IA.

## 🚀 Características

- **Framework**: Laravel 11
- **Admin Panel**: Filament 3
- **Banco de Dados**: PostgreSQL (compartilhado com n8n/chatwoot)
- **Interface**: Livewire + Alpine.js
- **Docker**: Configurado com Laravel Sail

## 📦 Estrutura do Projeto

### Bancos de Dados

O sistema utiliza **duas conexões** de banco de dados:

1. **faq_system** (conexão principal): Gerencia usuários e autenticação do sistema
2. **chatwoot** (conexão secundária): Acessa a tabela `faq` compartilhada com o n8n

### Tabela FAQ

Estrutura da tabela no banco `chatwoot`:

```sql
id          - Identificador único
pergunta    - Texto da pergunta
resposta    - Texto da resposta
categoria   - Categoria da FAQ (opcional)
ativo       - Status (ativo/inativo)
created_at  - Data de criação
updated_at  - Data de atualização
```

## 🔐 Credenciais de Acesso

**Painel Administrativo**: http://localhost:8080/admin

- **Email**: admin@cabemce.com
- **Senha**: #Cabemce2025#

## 🐳 Docker

### Serviços Configurados

```yaml
faq-workspace:
  - Porta: 8080
  - Imagem: laravelsail/php83-composer
  - Banco: PostgreSQL (auto-zap-postgres-1)
```

### Comandos Úteis

```bash
# Iniciar o sistema
docker compose up -d faq-workspace

# Parar o sistema
docker compose stop faq-workspace

# Ver logs
docker logs faq_workspace -f

# Acessar o container
docker exec -it faq_workspace bash

# Rodar migrations
docker exec faq_workspace php artisan migrate

# Criar novo usuário admin
docker exec faq_workspace php artisan make:filament-user
```

## 📂 Estrutura de Arquivos

```
faq-cabemce/
├── app/
│   ├── Filament/
│   │   └── Resources/
│   │       └── FaqResource.php       # Resource principal do Filament
│   └── Models/
│       └── Faq.php                   # Model da FAQ
├── docker/                            # Arquivos Docker do Sail
├── config/
│   └── database.php                   # Configurações de conexões
└── README_SISTEMA.md                  # Este arquivo
```

## 🛠️ Funcionalidades do Painel

### Gerenciamento de FAQs

- ✅ Criar novas perguntas e respostas
- ✅ Editar FAQs existentes
- ✅ Ativar/Desativar FAQs
- ✅ Categorizar perguntas
- ✅ Busca e filtros avançados
- ✅ Visualização com paginação

### Categorias Pré-definidas

- Contatos
- Endereços
- Serviços
- Horários
- Informações Gerais

## 🔗 Integração com N8N

O sistema compartilha a tabela `faq` do banco `chatwoot` com o fluxo do n8n, permitindo que:

1. O n8n consulte as FAQs ativas para alimentar o modelo
2. O admin gerencie as perguntas/respostas via interface amigável
3. As atualizações sejam refletidas imediatamente no modelo

## 📝 Próximos Passos

- [ ] Adicionar API REST para integração com n8n
- [ ] Implementar webhook para notificar o n8n de mudanças
- [ ] Adicionar sistema de versionamento de respostas
- [ ] Implementar analytics de perguntas mais acessadas
- [ ] Criar endpoint de teste de perguntas

## 🚀 Migração para Sail

O projeto já está preparado para usar Laravel Sail:

```bash
# A pasta docker/ contém os arquivos do Sail
# O compose.yaml está na raiz do projeto
# Para usar o Sail nativamente, execute:
./vendor/bin/sail up -d
```

## 📞 Suporte

Para dúvidas ou problemas, verifique:
- Logs do container: `docker logs faq_workspace`
- Conexão com banco: Verifique se o PostgreSQL está rodando
- Variáveis de ambiente: Arquivo `.env` do projeto

