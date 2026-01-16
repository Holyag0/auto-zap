# 🗂️ Estrutura do Sistema FAQ CABEMCE

## 📊 Banco de Dados

### Tabela: `faq`

```sql
CREATE TABLE faq (
    id SERIAL PRIMARY KEY,
    pergunta TEXT NOT NULL,
    resposta TEXT NOT NULL,
    categoria VARCHAR(100),
    ativo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 🎯 Índices Otimizados

| Índice | Tipo | Descrição |
|--------|------|-----------|
| `idx_faq_pergunta` | GIN | Busca em texto completo (português) na pergunta |
| `idx_faq_resposta` | GIN | Busca em texto completo (português) na resposta |
| `idx_faq_categoria` | BTree | Filtro por categoria |
| `idx_faq_ativo` | BTree | Filtro por status ativo |
| `idx_faq_ativo_categoria` | BTree | Filtro composto (ativo + categoria) |

### ⚙️ Trigger Automático

```sql
CREATE TRIGGER trigger_faq_updated_at
    BEFORE UPDATE ON faq
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
```

**Função:** Atualiza automaticamente o campo `updated_at` em cada modificação.

---

## 📁 Arquivos do Sistema

### Estrutura de Diretórios

```
faq-cabemce/
├── app/
│   ├── Models/
│   │   └── Faq.php                    # Model Eloquent
│   ├── Services/
│   │   └── N8nService.php             # Integração com n8n
│   └── Filament/
│       ├── Resources/
│       │   └── FaqResource.php        # CRUD Admin
│       └── Pages/
│           └── TestarPergunta.php     # Página de teste
├── database/
│   ├── seeders/
│   │   ├── FaqSeeder.php              # Seeder de importação
│   │   ├── faq_seed.csv               # Dados CSV
│   │   └── README.md                  # Documentação
│   └── sql/
│       └── create_faq_table.sql       # Script SQL completo
├── config/
│   ├── database.php                   # Configuração de conexões
│   └── services.php                   # Configuração n8n
└── .env                               # Variáveis de ambiente
```

---

## 🔗 Integrações

### 1. Laravel ↔ PostgreSQL

**Conexões configuradas:**

```php
// config/database.php
'pgsql' => [
    'database' => env('DB_DATABASE', 'faq_system'),  // Laravel interno
],

'pgsql_chatwoot' => [
    'database' => 'chatwoot',  // FAQs compartilhadas com n8n
],
```

**Model com conexão específica:**

```php
// app/Models/Faq.php
class Faq extends Model
{
    protected $connection = 'pgsql_chatwoot';
    protected $table = 'faq';
}
```

### 2. Laravel ↔ n8n

**Variáveis de ambiente:**

```bash
N8N_WEBHOOK_URL=http://auto-zap-n8n-1:5678/webhook/7ccef290-0864-4a58-b86f-595fc57766fb
N8N_BASE_URL=http://auto-zap-n8n-1:5678
```

**Serviço de integração:**

```php
// app/Services/N8nService.php
public function testarPergunta(string $pergunta): array
{
    $response = Http::post($this->webhookUrl, [
        'chatInput' => $pergunta,
    ]);
    
    return [
        'success' => $response->successful(),
        'resposta' => $response->json()['resposta'] ?? 'Erro',
    ];
}
```

### 3. n8n ↔ PostgreSQL ↔ AI Agent

**Fluxo do Workflow:**

```
┌─────────────┐
│  Webhook    │ Recebe chatInput
│  Trigger    │
└──────┬──────┘
       │
       v
┌─────────────┐
│  Postgres   │ SELECT * FROM faq WHERE ativo = true
│  Query      │
└──────┬──────┘
       │
       v
┌─────────────┐
│  AI Agent   │ Google Gemini + Contexto FAQs
│  (Gemini)   │
└──────┬──────┘
       │
       v
┌─────────────┐
│  Respond    │ Retorna JSON: {"resposta": "...", "success": true}
│  Webhook    │
└─────────────┘
```

---

## 📋 Categorias de FAQ

| Categoria | Descrição | Total |
|-----------|-----------|-------|
| **Empresa** | Informações sobre a CABEMCE | 1 |
| **Horarios** | Horários de funcionamento | 1 |
| **Endereços** | Localização física | 1 |
| **Contatos** | Meios de contato | 1 |
| **serviços** | Serviços oferecidos | 13 |
| **loja** | Informações da loja | 4 |
| **creche** | Creche Escola Tiradentes | 3 |

**Total:** 24 FAQs

---

## 🚀 Comandos Úteis

### Recriar a tabela

```bash
cat faq-cabemce/database/sql/create_faq_table.sql | docker exec -i auto-zap-postgres-1 psql -U postgres -d chatwoot
```

### Popular com dados

```bash
docker exec faq_workspace bash -c "cd /var/www/faq-cabemce && php artisan db:seed --class=FaqSeeder"
```

### Verificar dados

```bash
# Total de FAQs
docker exec auto-zap-postgres-1 psql -U postgres -d chatwoot -c "SELECT COUNT(*) FROM faq WHERE ativo = true;"

# Por categoria
docker exec auto-zap-postgres-1 psql -U postgres -d chatwoot -c "SELECT categoria, COUNT(*) FROM faq GROUP BY categoria ORDER BY COUNT(*) DESC;"
```

### Testar AI Agent

```bash
docker exec faq_workspace bash -c "curl -X POST 'http://auto-zap-n8n-1:5678/webhook/7ccef290-0864-4a58-b86f-595fc57766fb' -H 'Content-Type: application/json' -d '{\"chatInput\":\"qual o horário da cabemce?\"}'"
```

---

## 🎨 Interface Admin

### Acesso

- **URL:** http://localhost:8080/admin
- **Usuário:** admin@cabemce.com
- **Senha:** #Cabemce2025#

### Funcionalidades

1. **Listar FAQs** - Tabela com busca, filtros e paginação
2. **Criar FAQ** - Formulário para nova pergunta/resposta
3. **Editar FAQ** - Modificar FAQs existentes
4. **Deletar FAQ** - Remover FAQs (soft delete opcional)
5. **Testar FAQ** - Botão para testar FAQ específica no AI Agent
6. **Testar Perguntas** - Página para testar perguntas livres

### Filtros disponíveis

- Por categoria (dropdown)
- Por status ativo (sim/não/todos)
- Busca em texto (pergunta ou resposta)

---

## 🔍 Busca Inteligente

### Recursos do PostgreSQL

O sistema utiliza **Full Text Search** com dicionário português:

```sql
-- Buscar FAQs por palavra-chave
SELECT * FROM faq 
WHERE to_tsvector('portuguese', pergunta || ' ' || resposta) 
      @@ plainto_tsquery('portuguese', 'horário funcionamento')
AND ativo = true;
```

### Performance

- **Índices GIN:** Busca rápida em grandes volumes de texto
- **Cache Laravel:** Reduz queries repetidas
- **Eager Loading:** Otimiza relacionamentos

---

## 📈 Monitoramento

### Queries úteis

```sql
-- FAQs mais recentes
SELECT id, pergunta, categoria, created_at 
FROM faq 
ORDER BY created_at DESC 
LIMIT 10;

-- FAQs inativas
SELECT COUNT(*) FROM faq WHERE ativo = false;

-- Tamanho médio das respostas
SELECT AVG(LENGTH(resposta)) as tamanho_medio FROM faq;
```

---

## 🛠️ Manutenção

### Backup da tabela

```bash
docker exec auto-zap-postgres-1 pg_dump -U postgres -d chatwoot -t faq > backup_faq_$(date +%Y%m%d).sql
```

### Restore da tabela

```bash
cat backup_faq_20260114.sql | docker exec -i auto-zap-postgres-1 psql -U postgres -d chatwoot
```

### Limpar cache do Laravel

```bash
docker exec faq_workspace bash -c "cd /var/www/faq-cabemce && php artisan cache:clear && php artisan config:clear"
```

---

## 📝 Próximos Passos (Sugestões)

- [ ] Implementar versionamento de FAQs
- [ ] Adicionar tags para melhor categorização
- [ ] Criar relatório de FAQs mais consultadas
- [ ] Implementar importação via Excel
- [ ] Adicionar auditoria de alterações
- [ ] Criar API REST para consultas externas
- [ ] Implementar sistema de aprovação de FAQs

---

**Última atualização:** 14/01/2026  
**Versão:** 1.0.0
