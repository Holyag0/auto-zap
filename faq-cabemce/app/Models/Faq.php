<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /**
     * Conexão do banco de dados.
     */
    protected $connection = 'chatwoot';

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
     * Scope para buscar apenas perguntas ativas.
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar por categoria.
     */
    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }
}
