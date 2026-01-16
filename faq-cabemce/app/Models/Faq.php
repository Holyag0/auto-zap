<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    /**
     * Conexão do banco de dados.
     */
    protected $connection = 'pgsql_chatwoot';

    /**
     * Nome da tabela no banco de dados.
     */
    protected $table = 'faq';

    /**
     * Os atributos que podem ser atribuídos em massa.
     */
    protected $fillable = [
        'pergunta',
        'resposta',
        'categoria',
        'categoria_id',
        'ativo',
    ];

    /**
     * Os atributos que devem ser convertidos.
     */
    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com Categoria
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Scope para buscar apenas perguntas ativas.
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar por categoria (legado - string).
     */
    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para filtrar por categoria_id.
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }
}

