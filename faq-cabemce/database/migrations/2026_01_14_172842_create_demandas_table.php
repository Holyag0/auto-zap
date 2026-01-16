<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();
            $table->string('protocolo', 20)->unique();
            $table->foreignId('setor_id')->constrained('setors')->onDelete('restrict');
            $table->foreignId('tipo_demanda_id')->constrained('tipo_demandas')->onDelete('restrict');
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('descricao');
            $table->enum('situacao', ['em_analise', 'em_andamento', 'concluida', 'cancelada'])->default('em_analise');
            $table->json('arquivos')->nullable()->comment('Array de caminhos dos arquivos anexados');
            $table->timestamp('data_conclusao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index('protocolo');
            $table->index('situacao');
            $table->index(['setor_id', 'situacao']);
            $table->index(['responsavel_id', 'situacao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandas');
    }
};
