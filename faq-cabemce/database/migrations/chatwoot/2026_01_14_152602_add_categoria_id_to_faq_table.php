<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // 1. Adiciona a coluna categoria_id
        Schema::connection($this->connection)->table('faq', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id')->nullable()->after('resposta');
            $table->index('categoria_id');
        });

        // 2. Popular categoria_id com base na coluna categoria (string)
        $this->populateCategoriaId();

        // 3. Adiciona foreign key
        Schema::connection($this->connection)->table('faq', function (Blueprint $table) {
            $table->foreign('categoria_id')
                ->references('id')
                ->on('categorias')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Popula categoria_id com base na categoria (string)
     */
    protected function populateCategoriaId(): void
    {
        // Busca todas as categorias
        $categorias = DB::connection($this->connection)
            ->table('categorias')
            ->select('id', 'nome')
            ->get();

        // Atualiza cada FAQ com sua categoria_id correspondente
        foreach ($categorias as $categoria) {
            DB::connection($this->connection)
                ->table('faq')
                ->where('categoria', $categoria->nome)
                ->update(['categoria_id' => $categoria->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->table('faq', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
    }
};
