# 📚 Seeders - FAQ System

## 📝 Descrição

Este diretório contém os seeders para popular o banco de dados com dados iniciais do sistema FAQ CABEMCE.

## 📂 Arquivos

### `faq_seed.csv`
Arquivo CSV com todas as perguntas e respostas do FAQ da CABEMCE.

**Formato:**
```csv
pergunta,resposta,categoria,ativo
"Pergunta aqui","Resposta aqui","Categoria","sim"
```

**Categorias disponíveis:**
- `Empresa` - Informações sobre a CABEMCE
- `Horarios` - Horários de funcionamento
- `Endereços` - Localização
- `Contatos` - Formas de contato
- `serviços` - Serviços oferecidos
- `loja` - Informações da loja
- `creche` - Creche Escola Tiradentes

### `FaqSeeder.php`
Seeder responsável por importar os dados do CSV para a tabela `faq`.

## 🚀 Como usar

### 1. Executar o seeder de FAQs:
```bash
docker exec faq_workspace bash -c "cd /var/www/faq-cabemce && php artisan db:seed --class=FaqSeeder"
```

### 2. Executar todos os seeders:
```bash
docker exec faq_workspace bash -c "cd /var/www/faq-cabemce && php artisan db:seed"
```

### 3. Resetar o banco e executar seeders:
```bash
docker exec faq_workspace bash -c "cd /var/www/faq-cabemce && php artisan migrate:fresh --seed"
```

## ✏️ Como adicionar novas FAQs

### Opção 1: Via CSV (recomendado para múltiplas FAQs)
1. Edite o arquivo `faq_seed.csv`
2. Adicione novas linhas no formato:
   ```csv
   "Sua pergunta?","Sua resposta","categoria","sim"
   ```
3. Execute o seeder novamente:
   ```bash
   docker exec faq_workspace bash -c "cd /var/www/faq-cabemce && php artisan db:seed --class=FaqSeeder"
   ```

### Opção 2: Via Painel Admin (recomendado para FAQs individuais)
1. Acesse http://localhost:8080/admin
2. Vá em "Perguntas e Respostas"
3. Clique em "Nova Faq"
4. Preencha os campos e salve

## 📊 Estatísticas atuais

Total de FAQs importadas: **24**

| Categoria   | Quantidade |
|-------------|------------|
| serviços    | 13         |
| loja        | 4          |
| creche      | 3          |
| Empresa     | 1          |
| Horarios    | 1          |
| Endereços   | 1          |
| Contatos    | 1          |

## 🔍 Verificar dados importados

```bash
# Via PostgreSQL
docker exec auto-zap-postgres-1 psql -U postgres -d chatwoot -c "SELECT categoria, COUNT(*) FROM faq GROUP BY categoria;"

# Via Laravel Tinker
docker exec faq_workspace bash -c "cd /var/www/faq-cabemce && php artisan tinker --execute='echo App\Models\Faq::count();'"
```

## ⚠️ Observações

- O seeder **limpa a tabela** antes de importar (truncate)
- Todas as FAQs são importadas como **ativas** por padrão
- O campo `ativo` aceita: `sim`, `Sim`, `SIM` (convertidos para `true`)
- Certifique-se de que o CSV está em UTF-8
- Use aspas duplas para campos que contenham vírgulas ou quebras de linha

## 🔗 Integração com n8n

As FAQs são automaticamente utilizadas pelo workflow n8n para responder perguntas dos usuários através do AI Agent (Google Gemini).

**Fluxo:**
1. Usuário faz pergunta → Laravel
2. Laravel → Webhook n8n
3. n8n → Consulta PostgreSQL (FAQs ativas)
4. n8n → AI Agent (Gemini) com contexto das FAQs
5. AI Agent → Resposta inteligente
6. Resposta → Laravel → Usuário
