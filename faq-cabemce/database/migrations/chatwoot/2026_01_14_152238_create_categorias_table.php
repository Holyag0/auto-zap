<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Conexão do banco de dados
     */
    protected $connection = 'pgsql_chatwoot';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->text('descricao')->nullable();
            $table->string('cor', 7)->nullable()->comment('Cor em hexadecimal (#FF5733)');
            $table->string('icone', 50)->nullable()->comment('Nome do ícone heroicon');
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            // Índices
            $table->index('ativo');
            $table->index('ordem');
        });

        // Comentários na tabela
        DB::connection($this->connection)->statement("COMMENT ON TABLE categorias IS 'Categorias para organização das FAQs'");
        DB::connection($this->connection)->statement("COMMENT ON COLUMN categorias.nome IS 'Nome da categoria'");
        DB::connection($this->connection)->statement("COMMENT ON COLUMN categorias.slug IS 'Slug da categoria para URLs'");
        DB::connection($this->connection)->statement("COMMENT ON COLUMN categorias.descricao IS 'Descrição detalhada da categoria'");
        DB::connection($this->connection)->statement("COMMENT ON COLUMN categorias.cor IS 'Cor da categoria em hexadecimal'");
        DB::connection($this->connection)->statement("COMMENT ON COLUMN categorias.icone IS 'Ícone da categoria (heroicon)'");
        DB::connection($this->connection)->statement("COMMENT ON COLUMN categorias.ordem IS 'Ordem de exibição da categoria'");
        DB::connection($this->connection)->statement("COMMENT ON COLUMN categorias.ativo IS 'Define se a categoria está ativa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('categorias');
    }
};
