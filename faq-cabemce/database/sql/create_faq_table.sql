-- =====================================================
-- Script de Criação da Tabela FAQ
-- Database: chatwoot (PostgreSQL)
-- =====================================================

-- Remove a tabela se existir
DROP TABLE IF EXISTS faq CASCADE;

-- Cria a tabela FAQ
CREATE TABLE faq (
    id SERIAL PRIMARY KEY,
    pergunta TEXT NOT NULL,
    resposta TEXT NOT NULL,
    categoria VARCHAR(100),
    ativo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- ÍNDICES PARA OTIMIZAÇÃO DE BUSCA
-- =====================================================

-- Índice para busca em texto completo na pergunta (português)
CREATE INDEX idx_faq_pergunta ON faq USING gin(to_tsvector('portuguese', pergunta));

-- Índice para busca em texto completo na resposta (português)
CREATE INDEX idx_faq_resposta ON faq USING gin(to_tsvector('portuguese', resposta));

-- Índice para filtrar por categoria
CREATE INDEX idx_faq_categoria ON faq(categoria);

-- Índice para filtrar por status ativo
CREATE INDEX idx_faq_ativo ON faq(ativo);

-- Índice composto para consultas filtradas por ativo e categoria
CREATE INDEX idx_faq_ativo_categoria ON faq(ativo, categoria);

-- =====================================================
-- TRIGGER PARA ATUALIZAR UPDATED_AT AUTOMATICAMENTE
-- =====================================================

-- Cria função para atualizar o campo updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Cria trigger que executa antes de UPDATE
CREATE TRIGGER trigger_faq_updated_at
    BEFORE UPDATE ON faq
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- =====================================================
-- COMENTÁRIOS E DOCUMENTAÇÃO
-- =====================================================

COMMENT ON TABLE faq IS 'Tabela de perguntas frequentes (FAQ) para o AI Agent';
COMMENT ON COLUMN faq.id IS 'Identificador único da FAQ';
COMMENT ON COLUMN faq.pergunta IS 'Pergunta do FAQ';
COMMENT ON COLUMN faq.resposta IS 'Resposta do FAQ';
COMMENT ON COLUMN faq.categoria IS 'Categoria do FAQ (Empresa, Horarios, Endereços, Contatos, serviços, loja, creche)';
COMMENT ON COLUMN faq.ativo IS 'Define se o FAQ está ativo/visível (true = sim, false = não)';
COMMENT ON COLUMN faq.created_at IS 'Data e hora de criação do registro';
COMMENT ON COLUMN faq.updated_at IS 'Data e hora da última atualização (atualizado automaticamente via trigger)';

-- =====================================================
-- VERIFICAÇÃO
-- =====================================================

-- Exibe a estrutura da tabela
SELECT 
    column_name,
    data_type,
    character_maximum_length,
    column_default,
    is_nullable
FROM 
    information_schema.columns
WHERE 
    table_name = 'faq'
ORDER BY 
    ordinal_position;
